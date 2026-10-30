/**
 * LifeCare Diagnostic Center - Advanced PWA & Offline POS Suite
 * Features:
 * 1. Service Worker & PWA Install Prompt
 * 2. App Shortcuts & Lifecycle
 * 3. Network Status Live Indicator (Online / Offline)
 * 4. Web Audio POS Beep & Sync Chime Synthesizer (Zero-dependency)
 * 5. Web Push & Desktop Notifications
 * 6. IndexedDB Engine for Offline POS & Pending Invoices
 * 7. Smart Offline Diagnostic Catalog Preloading (Tests, Doctors, Referrers)
 * 8. Dynamic Offline Cash Receipt Modal & Printing
 * 9. Offline Vault & Sync Manager (History & Re-print)
 * 10. Webcam / Device Camera Barcode & QR Scanner
 * 11. 1-Click Kiosk / Fullscreen POS Mode
 * 12. Automatic & Manual Background Sync Engine
 */
"use strict";

(function (window, document, $) {
    var DB_NAME = 'lifecare_pwa_db';
    var DB_VERSION = 2;
    var STORE_INVOICES = 'pending_invoices';
    var STORE_CATALOG = 'catalog_cache';
    var dbInstance = null;
    var deferredInstallPrompt = null;
    var isSyncing = false;
    var scannerMediaStream = null;
    var scannerInterval = null;

    // ============================================================
    // 1. Web Audio Synthesizer (Zero external sound files needed)
    // ============================================================
    var SoundFX = {
        ctx: null,
        init: function () {
            if (!this.ctx && (window.AudioContext || window.webkitAudioContext)) {
                var AudioCtx = window.AudioContext || window.webkitAudioContext;
                this.ctx = new AudioCtx();
            }
        },
        playTone: function (freq, type, duration, delay) {
            try {
                this.init();
                if (!this.ctx) return;
                if (this.ctx.state === 'suspended') {
                    this.ctx.resume();
                }
                var osc = this.ctx.createOscillator();
                var gain = this.ctx.createGain();
                var startTime = this.ctx.currentTime + (delay || 0);
                osc.type = type || 'sine';
                osc.frequency.setValueAtTime(freq, startTime);
                gain.gain.setValueAtTime(0.12, startTime);
                gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
                osc.connect(gain);
                gain.connect(this.ctx.destination);
                osc.start(startTime);
                osc.stop(startTime + duration);
            } catch (e) {
                // Audio autoplay restrictions or unsupported
            }
        },
        // POS Scanner Beep
        posBeep: function () {
            this.playTone(880, 'square', 0.08, 0);
        },
        // Pleasant Success Chime (C5, E5, G5)
        successChime: function () {
            this.playTone(523.25, 'sine', 0.15, 0);
            this.playTone(659.25, 'sine', 0.18, 0.08);
            this.playTone(783.99, 'sine', 0.25, 0.16);
        },
        // Offline Alert Tone
        offlineAlert: function () {
            this.playTone(440, 'triangle', 0.12, 0);
            this.playTone(349.23, 'triangle', 0.18, 0.12);
        }
    };

    // ============================================================
    // 2. Desktop Notifications API
    // ============================================================
    var DesktopNotify = {
        requestPermission: function () {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        },
        send: function (title, body, tag) {
            if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    new Notification(title, {
                        body: body,
                        icon: $('base').attr('href') ? $('base').attr('href') + 'uploads/pwa-icon-192.png' : 'uploads/pwa-icon-192.png',
                        tag: tag || 'lifecare-notification'
                    });
                } catch (e) {
                    // Fallback to serviceWorker registration notification if available
                    if (navigator.serviceWorker && navigator.serviceWorker.ready) {
                        navigator.serviceWorker.ready.then(function (reg) {
                            reg.showNotification(title, {
                                body: body,
                                icon: 'uploads/pwa-icon-192.png'
                            });
                        });
                    }
                }
            }
        }
    };

    // ============================================================
    // 3. IndexedDB Manager (Invoices & Catalog)
    // ============================================================
    var OfflineDB = {
        open: function () {
            return new Promise(function (resolve, reject) {
                if (dbInstance) {
                    return resolve(dbInstance);
                }
                if (!window.indexedDB) {
                    return reject(new Error('IndexedDB is not supported.'));
                }
                var request = window.indexedDB.open(DB_NAME, DB_VERSION);
                request.onupgradeneeded = function (event) {
                    var db = event.target.result;
                    if (!db.objectStoreNames.contains(STORE_INVOICES)) {
                        var invStore = db.createObjectStore(STORE_INVOICES, { keyPath: 'uuid' });
                        invStore.createIndex('status', 'status', { unique: false });
                        invStore.createIndex('created_at', 'created_at', { unique: false });
                    }
                    if (!db.objectStoreNames.contains(STORE_CATALOG)) {
                        db.createObjectStore(STORE_CATALOG, { keyPath: 'key' });
                    }
                };
                request.onsuccess = function (event) {
                    dbInstance = event.target.result;
                    resolve(dbInstance);
                };
                request.onerror = function (event) {
                    console.error('[PWA DB] Open failed:', event.target.error);
                    reject(event.target.error);
                };
            });
        },

        saveInvoice: function (invoiceData) {
            return OfflineDB.open().then(function (db) {
                return new Promise(function (resolve, reject) {
                    var tx = db.transaction([STORE_INVOICES], 'readwrite');
                    var store = tx.objectStore(STORE_INVOICES);
                    var req = store.put(invoiceData);
                    req.onsuccess = function () { resolve(invoiceData); };
                    req.onerror = function (e) { reject(e.target.error); };
                });
            });
        },

        getAllInvoices: function () {
            return OfflineDB.open().then(function (db) {
                return new Promise(function (resolve, reject) {
                    var tx = db.transaction([STORE_INVOICES], 'readonly');
                    var store = tx.objectStore(STORE_INVOICES);
                    var req = store.getAll();
                    req.onsuccess = function (e) { resolve(e.target.result || []); };
                    req.onerror = function (e) { reject(e.target.error); };
                });
            });
        },

        getInvoice: function (uuid) {
            return OfflineDB.open().then(function (db) {
                return new Promise(function (resolve, reject) {
                    var tx = db.transaction([STORE_INVOICES], 'readonly');
                    var store = tx.objectStore(STORE_INVOICES);
                    var req = store.get(uuid);
                    req.onsuccess = function (e) { resolve(e.target.result); };
                    req.onerror = function (e) { reject(e.target.error); };
                });
            });
        },

        markSynced: function (uuid, serverInvoiceId) {
            return OfflineDB.open().then(function (db) {
                return new Promise(function (resolve, reject) {
                    var tx = db.transaction([STORE_INVOICES], 'readwrite');
                    var store = tx.objectStore(STORE_INVOICES);
                    var getReq = store.get(uuid);
                    getReq.onsuccess = function (e) {
                        var record = e.target.result;
                        if (record) {
                            record.status = 'synced';
                            record.server_invoice_id = serverInvoiceId;
                            record.synced_at = new Date().toISOString();
                            var updateReq = store.put(record);
                            updateReq.onsuccess = function () { resolve(record); };
                            updateReq.onerror = function (err) { reject(err.target.error); };
                        } else {
                            resolve(null);
                        }
                    };
                    getReq.onerror = function (e) { reject(e.target.error); };
                });
            });
        },

        deleteInvoice: function (uuid) {
            return OfflineDB.open().then(function (db) {
                return new Promise(function (resolve, reject) {
                    var tx = db.transaction([STORE_INVOICES], 'readwrite');
                    var store = tx.objectStore(STORE_INVOICES);
                    var req = store.delete(uuid);
                    req.onsuccess = function () { resolve(); };
                    req.onerror = function (e) { reject(e.target.error); };
                });
            });
        },

        clearSyncedInvoices: function () {
            return OfflineDB.getAllInvoices().then(function (all) {
                var synced = all.filter(function (i) { return i.status === 'synced'; });
                return Promise.all(synced.map(function (i) { return OfflineDB.deleteInvoice(i.uuid); }));
            });
        },

        // Catalog Preloading Storage
        saveCatalog: function (key, data) {
            return OfflineDB.open().then(function (db) {
                return new Promise(function (resolve, reject) {
                    var tx = db.transaction([STORE_CATALOG], 'readwrite');
                    var store = tx.objectStore(STORE_CATALOG);
                    var req = store.put({ key: key, data: data, updated_at: Date.now() });
                    req.onsuccess = function () { resolve(); };
                    req.onerror = function (e) { reject(e.target.error); };
                });
            });
        },

        getCatalog: function (key) {
            return OfflineDB.open().then(function (db) {
                return new Promise(function (resolve, reject) {
                    var tx = db.transaction([STORE_CATALOG], 'readonly');
                    var store = tx.objectStore(STORE_CATALOG);
                    var req = store.get(key);
                    req.onsuccess = function (e) {
                        resolve(e.target.result ? e.target.result.data : null);
                    };
                    req.onerror = function (e) { reject(e.target.error); };
                });
            });
        }
    };

    // Expose OfflineDB and SoundFX to global scope for module interoperability
    window.OfflineDB = OfflineDB;
    window.SoundFX = SoundFX;

    // ============================================================
    // 4. Smart Offline Diagnostic Catalog Preloader
    // ============================================================
    function preloadDiagnosticCatalog() {
        if ($('select.multi-select option').length > 0) {
            var tests = [];
            $('select.multi-select option').each(function () {
                var $opt = $(this);
                tests.push({
                    id: $opt.val(),
                    idd: $opt.data('idd') || $opt.val(),
                    name: $opt.text(),
                    price: parseFloat($opt.data('id')) || 0,
                    d_comm: parseFloat($opt.data('d_commission')) || 0,
                    r_comm: parseFloat($opt.data('r_commission')) || 0
                });
            });
            OfflineDB.saveCatalog('diagnostic_tests', tests);
        }

        if ($('#add_doctor option').length > 1) {
            var doctors = [];
            $('#add_doctor option').each(function () {
                var val = $(this).val();
                if (val && val !== 'add_new') {
                    doctors.push({ id: val, name: $(this).text() });
                }
            });
            OfflineDB.saveCatalog('doctors_list', doctors);
        }

        if ($('#add_referrer option').length > 1) {
            var referrers = [];
            $('#add_referrer option').each(function () {
                var val = $(this).val();
                if (val && val !== 'add_new') {
                    referrers.push({ id: val, name: $(this).text() });
                }
            });
            OfflineDB.saveCatalog('referrers_list', referrers);
        }

        // Preload Lab Report Templates if on report pages
        if ($('#template1 option').length > 1) {
            var labTemplates = [];
            $('#template1 option').each(function () {
                var val = $(this).val();
                if (val) {
                    labTemplates.push({ id: val, name: $(this).text() });
                }
            });
            OfflineDB.saveCatalog('lab_templates_list', labTemplates);
        }
    }

    // ============================================================
    // 5. Service Worker Registration & PWA Install Engine
    // ============================================================
    function initServiceWorker() {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                var swPath = $('base').attr('href') ? $('base').attr('href') + 'sw.js' : 'sw.js';
                navigator.serviceWorker.register(swPath, { scope: './' })
                    .then(function (reg) {
                        console.log('[PWA SW] Registered with scope:', reg.scope);

                        navigator.serviceWorker.addEventListener('message', function (event) {
                            if (event.data && event.data.type === 'TRIGGER_BACKGROUND_SYNC') {
                                syncOfflineInvoices();
                            }
                        });
                    })
                    .catch(function (err) {
                        console.warn('[PWA SW] Registration failed:', err);
                    });
            });
        }
    }

    function initInstallPrompt() {
        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredInstallPrompt = e;
            $('#pwa_install_btn').removeClass('d-none');
        });

        $(document).on('click', '#pwa_install_btn', function () {
            if (!deferredInstallPrompt) return;
            deferredInstallPrompt.prompt();
            deferredInstallPrompt.userChoice.then(function (choiceResult) {
                if (choiceResult.outcome === 'accepted') {
                    $('#pwa_install_btn').addClass('d-none');
                }
                deferredInstallPrompt = null;
            });
        });

        window.addEventListener('appinstalled', function () {
            $('#pwa_install_btn').addClass('d-none');
            DesktopNotify.requestPermission();
            SoundFX.successChime();
            if (typeof toastr !== 'undefined') {
                toastr.success('LifeCare App installed successfully on your device!', 'App Installed');
            }
        });
    }

    // ============================================================
    // 6. Network Status & Navbar Indicator
    // ============================================================
    function updateNetworkStatusUI() {
        var isOnline = navigator.onLine;
        var $pill = $('#pwa_network_pill');
        var $text = $pill.find('.pwa-status-text');

        if (isOnline) {
            $pill.removeClass('offline').addClass('online').attr('title', 'System is Online & Connected');
            $text.text('Online');
        } else {
            $pill.removeClass('online').addClass('offline').attr('title', 'System is in Offline Mode');
            $text.text('Offline Mode');
        }
    }

    function updatePendingCountBadge() {
        OfflineDB.getAllInvoices().then(function (all) {
            var pending = all.filter(function (i) { return i.status === 'pending'; });
            var count = pending.length;
            var $badge = $('#pwa_sync_badge');
            var $countSpan = $('#pwa_pending_count');

            $countSpan.text(count);
            if (count > 0) {
                $badge.show();
            } else {
                $badge.hide();
            }
        }).catch(function (err) {
            console.warn('[PWA] Could not update badge count:', err);
        });
    }

    function initNetworkListeners() {
        window.addEventListener('online', function () {
            updateNetworkStatusUI();
            SoundFX.posBeep();
            if (typeof toastr !== 'undefined') {
                toastr.success('Internet connection restored! Checking for offline bills to sync...', 'Online');
            }
            setTimeout(syncOfflineInvoices, 1500);
        });

        window.addEventListener('offline', function () {
            updateNetworkStatusUI();
            SoundFX.offlineAlert();
            if (typeof toastr !== 'undefined') {
                toastr.warning('You are currently offline. Invoices will be saved locally and auto-synced once back online.', 'Offline Mode');
            }
        });

        // Click badge opens the Offline Vault modal directly
        $(document).on('click', '#pwa_sync_badge', function (e) {
            e.preventDefault();
            openOfflineVaultModal();
        });

        $(document).on('click', '#pwa_vault_btn', function (e) {
            e.preventDefault();
            openOfflineVaultModal();
        });
    }

    // ============================================================
    // 7. Offline POS Invoice Interception & Local Storing
    // ============================================================
    function capturePOSInvoiceData(formElem) {
        var $form = $(formElem);
        var formDataArray = $form.serializeArray();

        var now = new Date();
        var dateFormatted = now.toLocaleDateString('en-GB') + ' ' + now.toLocaleTimeString('en-US', { hour12: true });
        var tokenRand = Math.floor(Math.random() * 8999 + 1000);
        var offlineUuid = 'OFF-' + now.getFullYear() + String(now.getMonth() + 1).padStart(2, '0') + String(now.getDate()).padStart(2, '0') + '-' + tokenRand;

        var patientName = $('#p_name').val() || $('#pos_select option:selected').text().split('(')[0].trim() || 'Walk-in Patient';
        var patientPhone = $('#p_phone').val() || '';
        var patientAge = $('#p_age').val() || '';
        var patientGender = $('#p_gender').val() || '';
        var doctorName = $('#add_doctor option:selected').text().trim() || 'Self';
        var referrerName = $('#add_referrer option:selected').text().trim() || '';

        // Clean Dr. prefix if Doctor / Ref is Self or UPAZILA HEALTH COMPLEX
        if (doctorName.toLowerCase().indexOf('self') !== -1 || doctorName.toLowerCase().indexOf('upazila') !== -1) {
            doctorName = doctorName.replace(/^Dr\.\s*/i, '');
        }

        var items = [];
        $('select.multi-select option:selected').each(function () {
            var id1 = $(this).data('idd') || $(this).val();
            var name = $(this).text().split('*')[0].trim();
            var price = parseFloat($(this).data('id')) || 0;
            var qtyInput = $('#idinput-' + id1);
            var qty = qtyInput.length ? (parseFloat(qtyInput.val()) || 1) : 1;
            items.push({
                id: id1,
                name: name,
                price: price,
                qty: qty,
                total: price * qty
            });
        });

        var subTotal = parseFloat($('#subtotal').val()) || parseFloat($('.pos-subtotal-val').text().replace(/[^\d.]/g, '')) || 0;
        var discountVal = parseFloat($('#dis_id').val()) || 0;
        var grossTotal = parseFloat($('#gross_total').val()) || parseFloat($('.pos-gross-val').text().replace(/[^\d.]/g, '')) || 0;
        var paidVal = parseFloat($('#amount_received').val()) || 0;
        var dueVal = Math.max(0, grossTotal - paidVal);

        return {
            uuid: offlineUuid,
            status: 'pending',
            created_at: now.toISOString(),
            display_time: dateFormatted,
            patient_name: patientName,
            patient_phone: patientPhone,
            patient_age: patientAge,
            patient_gender: patientGender,
            doctor_name: doctorName,
            referrer_name: referrerName,
            items: items,
            sub_total: subTotal,
            discount: discountVal,
            gross_total: grossTotal,
            amount_received: paidVal,
            due_amount: dueVal,
            deposit_type: $('#selecttype').val() || 'Cash',
            formData: formDataArray
        };
    }

    function showOfflineReceiptModal(invoice) {
        var existingModal = $('#offlineReceiptModal');
        if (existingModal.length) {
            existingModal.remove();
        }

        var itemRowsHtml = '';
        invoice.items.forEach(function (item, idx) {
            itemRowsHtml += '<tr>' +
                '<td class="text-center">' + (idx + 1) + '</td>' +
                '<td class="font-weight-bold text-left">' + item.name + '</td>' +
                '<td class="text-center">' + item.qty + '</td>' +
                '<td class="text-right">' + item.price.toFixed(2) + '</td>' +
                '<td class="text-right">' + item.total.toFixed(2) + '</td>' +
            '</tr>';
        });

        var dueHtml = '';
        if (invoice.due_amount > 0) {
            dueHtml = '<tr class="text-danger font-weight-bold">' +
                '<td colspan="4" class="text-right">Due Amount:</td>' +
                '<td class="text-right">' + invoice.due_amount.toFixed(2) + ' TK</td>' +
            '</tr>';
        }

        var modalHtml = '<div class="modal fade" id="offlineReceiptModal" tabindex="-1" role="dialog" aria-hidden="true">' +
            '<div class="modal-dialog modal-dialog-centered modal-lg" role="document">' +
                '<div class="modal-content">' +
                    '<div class="modal-header bg-warning text-dark py-2">' +
                        '<h5 class="modal-title font-weight-bold">' +
                            '<i class="fas fa-wifi text-danger mr-2"></i> Offline Receipt Generated (Pending Server Sync)' +
                        '</h5>' +
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                    '</div>' +
                    '<div class="modal-body p-4" id="offline_print_area">' +
                        '<!-- Receipt Header -->' +
                        '<div class="text-center pb-3 border-bottom mb-3">' +
                            '<h3 class="font-weight-bold mb-1" style="color:#007bff;">LIFECARE DIAGNOSTIC CENTER</h3>' +
                            '<p class="text-muted mb-1 small">Hospital Management System & Diagnostic Laboratory</p>' +
                            '<div class="d-inline-block px-3 py-1 rounded bg-warning text-dark font-weight-bold small mb-2">' +
                                '<i class="fas fa-bolt mr-1"></i> OFFLINE BILL TOKEN: ' + invoice.uuid +
                            '</div>' +
                            '<div class="small text-secondary">Date & Time: ' + invoice.display_time + '</div>' +
                        '</div>' +

                        '<!-- Patient & Doctor Info Grid -->' +
                        '<div class="row mb-3 small">' +
                            '<div class="col-6">' +
                                '<div><strong>Patient:</strong> ' + invoice.patient_name + '</div>' +
                                '<div><strong>Phone:</strong> ' + (invoice.patient_phone || 'N/A') + '</div>' +
                                '<div><strong>Age / Sex:</strong> ' + (invoice.patient_age ? invoice.patient_age + ' Y' : 'N/A') + ' / ' + (invoice.patient_gender || 'N/A') + '</div>' +
                            '</div>' +
                            '<div class="col-6 text-right">' +
                                '<div><strong>Doctor:</strong> ' + invoice.doctor_name + '</div>' +
                                '<div><strong>Payment Method:</strong> ' + invoice.deposit_type + '</div>' +
                            '</div>' +
                        '</div>' +

                        '<!-- Tests Table -->' +
                        '<div class="table-responsive mb-3">' +
                            '<table class="table table-sm table-bordered mb-0 small">' +
                                '<thead class="thead-light text-center">' +
                                    '<tr>' +
                                        '<th style="width: 40px;">#</th>' +
                                        '<th>Test / Investigation</th>' +
                                        '<th style="width: 60px;">Qty</th>' +
                                        '<th style="width: 100px;" class="text-right">Price</th>' +
                                        '<th style="width: 110px;" class="text-right">Total</th>' +
                                    '</tr>' +
                                '</thead>' +
                                '<tbody>' +
                                    itemRowsHtml +
                                '</tbody>' +
                                '<tfoot>' +
                                    '<tr>' +
                                        '<td colspan="4" class="text-right font-weight-bold">Subtotal:</td>' +
                                        '<td class="text-right font-weight-bold">' + invoice.sub_total.toFixed(2) + ' TK</td>' +
                                    '</tr>' +
                                    (invoice.discount > 0 ? '<tr><td colspan="4" class="text-right text-success">Discount:</td><td class="text-right text-success">-' + invoice.discount.toFixed(2) + ' TK</td></tr>' : '') +
                                    '<tr class="table-primary font-weight-bold" style="font-size: 1.05em;">' +
                                        '<td colspan="4" class="text-right">Net Payable:</td>' +
                                        '<td class="text-right">' + invoice.gross_total.toFixed(2) + ' TK</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td colspan="4" class="text-right font-weight-bold text-success">Paid Amount:</td>' +
                                        '<td class="text-right font-weight-bold text-success">' + invoice.amount_received.toFixed(2) + ' TK</td>' +
                                    '</tr>' +
                                    dueHtml +
                                '</tfoot>' +
                            '</table>' +
                        '</div>' +

                        '<!-- Notice -->' +
                        '<div class="alert alert-light border small text-center text-muted mb-0 py-2">' +
                            '<i class="fas fa-info-circle mr-1 text-info"></i> ' +
                            'Offline billing complete. This bill is safely preserved and will auto-sync to central server once internet connects.' +
                        '</div>' +
                    '</div>' +
                    '<div class="modal-footer d-flex justify-content-between">' +
                        '<button type="button" class="btn btn-secondary" data-dismiss="modal">' +
                            '<i class="fas fa-times mr-1"></i> Close & New Bill' +
                        '</button>' +
                        '<div>' +
                            '<button type="button" class="btn btn-primary" id="btn_print_offline_receipt">' +
                                '<i class="fas fa-print mr-1"></i> Print Offline Receipt' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';

        $('body').append(modalHtml);
        $('#offlineReceiptModal').modal({ backdrop: 'static', keyboard: false });

        $('#btn_print_offline_receipt').on('click', function () {
            var printContents = document.getElementById('offline_print_area').innerHTML;
            var printWin = window.open('', '', 'width=750,height=600');
            printWin.document.write('<html><head><title>Offline Receipt - ' + invoice.uuid + '</title>');
            printWin.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">');
            printWin.document.write('<style>body{padding:20px;font-family:sans-serif;} @media print {.no-print{display:none;}}</style>');
            printWin.document.write('</head><body>');
            printWin.document.write(printContents);
            printWin.document.write('</body></html>');
            printWin.document.close();
            setTimeout(function () {
                printWin.focus();
                printWin.print();
                printWin.close();
            }, 300);
        });

        $('#offlineReceiptModal').on('hidden.bs.modal', function () {
            resetPOSForm();
        });
    }

    function resetPOSForm() {
        if ($('#editPaymentForm').length) {
            if ($('select.multi-select').length && typeof $('select.multi-select').multiSelect === 'function') {
                $('select.multi-select').multiSelect('deselect_all');
            }
            $('#p_name').val('');
            $('#p_phone').val('');
            $('#p_age').val('');
            $('#p_gender').val('');
            $('#p_address').val('');
            $('#dis_id').val('');
            $('#dis_id_percent').val('');
            $('#amount_received').val('');
            $('#remarks').val('');
            if ($('#pos_select').length && $('#pos_select').hasClass('select2-hidden-accessible')) {
                $('#pos_select').val(null).trigger('change');
            }
            if (typeof updatePOSState === 'function') {
                updatePOSState();
            }
            try {
                localStorage.removeItem('lifecare_pos_cart');
            } catch (e) {}
        }
    }

    function initPOSInterception() {
        $(document).on('submit', '#editPaymentForm', function (e) {
            if (!navigator.onLine) {
                e.preventDefault();
                processOfflineFormSubmit(this);
                return false;
            }
        });

        $(document).on('click', '#submit2, #submit, #submit-btn2', function (e) {
            if (!navigator.onLine) {
                e.preventDefault();
                processOfflineFormSubmit($('#editPaymentForm')[0]);
                return false;
            }
        });
    }

    function processOfflineFormSubmit(formElem) {
        var patientVal = $('#pos_select').val();
        var pName = $('#p_name').val();

        if (!patientVal && !pName) {
            SoundFX.offlineAlert();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Patient Required',
                    text: 'Please select an existing patient or fill in patient name and phone number.'
                });
            } else {
                alert('Please select a patient or enter patient name.');
            }
            return false;
        }

        var selectedTests = $('select.multi-select option:selected').length;
        if (selectedTests === 0) {
            SoundFX.offlineAlert();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Tests Selected',
                    text: 'Please select at least one diagnostic test/investigation.'
                });
            } else {
                alert('Please select at least one test item.');
            }
            return false;
        }

        var invoiceData = capturePOSInvoiceData(formElem);
        OfflineDB.saveInvoice(invoiceData).then(function () {
            SoundFX.posBeep();
            updatePendingCountBadge();
            showOfflineReceiptModal(invoiceData);
            if (typeof toastr !== 'undefined') {
                toastr.success('Offline Invoice ' + invoiceData.uuid + ' saved in local storage!', 'Offline POS');
            }
        }).catch(function (err) {
            console.error('[PWA] Error saving offline invoice:', err);
            alert('Failed to save offline invoice to local database. Please try again.');
        });
    }

    // ============================================================
    // 7B. Offline Due Collection Interception & Deposit Manager
    // ============================================================
    function captureDueDepositData(formElem) {
        var $form = $(formElem);
        var formDataArray = $form.serializeArray();

        var now = new Date();
        var dateFormatted = now.toLocaleDateString('en-GB') + ' ' + now.toLocaleTimeString('en-US', { hour12: true });
        var tokenRand = Math.floor(Math.random() * 8999 + 1000);
        var offlineUuid = 'OFF-DEP-' + now.getFullYear() + String(now.getMonth() + 1).padStart(2, '0') + String(now.getDate()).padStart(2, '0') + '-' + tokenRand;

        var invoiceNo = $('#invoice_no').val() || $('#payment_id').val() || '';
        var patientId = $('#patient_id').val() || '';
        var patientName = $('#name').val() || 'Patient';
        var originalDue = parseFloat($('#due_amount').data('original-due')) || parseFloat($('#due_amount').val()) || 0;
        var depositVal = parseFloat($('#deposited_amount').val()) || 0;
        var discountVal = parseFloat($('#additional_discount').val()) || 0;
        var remainingDue = Math.max(0, originalDue - discountVal - depositVal);
        var depositType = $('#selecttype').val() || 'Cash';

        return {
            uuid: offlineUuid,
            type: 'due_deposit',
            status: 'pending',
            created_at: now.toISOString(),
            display_time: dateFormatted,
            invoice_no: invoiceNo,
            payment_id: invoiceNo,
            patient_id: patientId,
            patient_name: patientName,
            original_due: originalDue,
            deposited_amount: depositVal,
            additional_discount: discountVal,
            remaining_due: remainingDue,
            deposit_type: depositType,
            formData: formDataArray
        };
    }

    function showOfflineDueReceiptModal(deposit) {
        var existingModal = $('#offlineDueReceiptModal');
        if (existingModal.length) {
            existingModal.remove();
        }

        var modalHtml = '<div class="modal fade" id="offlineDueReceiptModal" tabindex="-1" role="dialog" aria-hidden="true">' +
            '<div class="modal-dialog modal-dialog-centered modal-lg" role="document">' +
                '<div class="modal-content shadow-lg">' +
                    '<div class="modal-header bg-success text-white py-2">' +
                        '<h5 class="modal-title font-weight-bold">' +
                            '<i class="fas fa-hand-holding-usd text-warning mr-2"></i> Offline Due Collection Receipt (Pending Server Sync)' +
                        '</h5>' +
                        '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                    '</div>' +
                    '<div class="modal-body p-4" id="offline_due_print_area">' +
                        '<!-- Receipt Header -->' +
                        '<div class="text-center pb-3 border-bottom mb-3">' +
                            '<h3 class="font-weight-bold mb-1" style="color:#007bff;">LIFECARE DIAGNOSTIC CENTER</h3>' +
                            '<p class="text-muted mb-1 small">Hospital Management System & Diagnostic Laboratory</p>' +
                            '<h5 class="font-weight-bold text-dark my-2">OFFLINE MONEY RECEIPT (DUE COLLECTION)</h5>' +
                            '<div class="d-inline-block px-3 py-1 rounded bg-warning text-dark font-weight-bold small mb-2">' +
                                '<i class="fas fa-bolt mr-1"></i> OFFLINE DEPOSIT TOKEN: ' + deposit.uuid +
                            '</div>' +
                            '<div class="small text-secondary">Date & Time: ' + deposit.display_time + '</div>' +
                        '</div>' +

                        '<!-- Patient & Invoice Info Grid -->' +
                        '<div class="row mb-3 small">' +
                            '<div class="col-6">' +
                                '<div><strong>Patient Name:</strong> ' + deposit.patient_name + '</div>' +
                                '<div><strong>Central Invoice #:</strong> #' + deposit.invoice_no + '</div>' +
                            '</div>' +
                            '<div class="col-6 text-right">' +
                                '<div><strong>Payment Method:</strong> ' + deposit.deposit_type + '</div>' +
                                '<div><strong>Status:</strong> <span class="badge badge-warning">Offline Collected (Queued)</span></div>' +
                            '</div>' +
                        '</div>' +

                        '<!-- Deposit Breakdown Table -->' +
                        '<div class="table-responsive mb-3">' +
                            '<table class="table table-sm table-bordered mb-0 small">' +
                                '<thead class="thead-light text-center">' +
                                    '<tr>' +
                                        '<th>Description</th>' +
                                        '<th style="width: 160px;" class="text-right">Amount (TK)</th>' +
                                    '</tr>' +
                                '</thead>' +
                                '<tbody>' +
                                    '<tr>' +
                                        '<td>Previous Due Amount</td>' +
                                        '<td class="text-right font-weight-bold">' + deposit.original_due.toFixed(2) + ' TK</td>' +
                                    '</tr>' +
                                    (deposit.additional_discount > 0 ? ('<tr><td class="text-success font-weight-bold">Additional Discount Applied</td><td class="text-right text-success font-weight-bold">-' + deposit.additional_discount.toFixed(2) + ' TK</td></tr>') : '') +
                                    '<tr class="table-success font-weight-bold" style="font-size: 1.05em;">' +
                                        '<td>Amount Deposited / Collected Now</td>' +
                                        '<td class="text-right text-success font-weight-bold">' + deposit.deposited_amount.toFixed(2) + ' TK</td>' +
                                    '</tr>' +
                                    '<tr class="table-light font-weight-bold">' +
                                        '<td class="text-danger">Remaining Balance Due</td>' +
                                        '<td class="text-right text-danger font-weight-bold">' + deposit.remaining_due.toFixed(2) + ' TK</td>' +
                                    '</tr>' +
                                '</tbody>' +
                            '</table>' +
                        '</div>' +

                        '<!-- Notice -->' +
                        '<div class="alert alert-light border small text-center text-muted mb-0 py-2">' +
                            '<i class="fas fa-info-circle mr-1 text-info"></i> ' +
                            'Offline due deposit collected successfully. This receipt is legally valid and will auto-sync to the central hospital database once internet is restored.' +
                        '</div>' +
                    '</div>' +
                    '<div class="modal-footer d-flex justify-content-between">' +
                        '<button type="button" class="btn btn-secondary" data-dismiss="modal">' +
                            '<i class="fas fa-times mr-1"></i> Close' +
                        '</button>' +
                        '<div>' +
                            '<button type="button" class="btn btn-primary" id="btn_print_offline_due_receipt">' +
                                '<i class="fas fa-print mr-1"></i> Print Money Receipt' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';

        $('body').append(modalHtml);
        $('#offlineDueReceiptModal').modal({ backdrop: 'static', keyboard: false });

        $('#btn_print_offline_due_receipt').on('click', function () {
            var printContents = document.getElementById('offline_due_print_area').innerHTML;
            var printWin = window.open('', '', 'width=750,height=600');
            printWin.document.write('<html><head><title>Due Receipt - ' + deposit.uuid + '</title>');
            printWin.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">');
            printWin.document.write('<style>body{padding:20px;font-family:sans-serif;} @media print {.no-print{display:none;}}</style>');
            printWin.document.write('</head><body>');
            printWin.document.write(printContents);
            printWin.document.write('</body></html>');
            printWin.document.close();
            setTimeout(function () {
                printWin.focus();
                printWin.print();
                printWin.close();
            }, 300);
        });

        $('#offlineDueReceiptModal').on('hidden.bs.modal', function () {
            if ($('#deposit-form').length) {
                $('#deposit-form').trigger('reset');
            }
        });
    }

    function processOfflineDepositSubmit(formElem) {
        var invoiceNo = $('#invoice_no').val() || $('#payment_id').val();
        if (!invoiceNo) {
            SoundFX.offlineAlert();
            alert('Invoice number is required for due deposit.');
            return false;
        }

        var depositVal = parseFloat($('#deposited_amount').val()) || 0;
        var discountVal = parseFloat($('#additional_discount').val()) || 0;
        if (depositVal <= 0 && discountVal <= 0) {
            SoundFX.offlineAlert();
            alert('Please enter a Deposit Amount or Discount Amount.');
            return false;
        }

        var originalDue = parseFloat($('#due_amount').data('original-due')) || parseFloat($('#due_amount').val()) || 0;
        if ((depositVal + discountVal) > (originalDue + 0.05)) {
            if (!confirm('Total Deposit (৳' + depositVal.toFixed(2) + ') and Additional Discount (৳' + discountVal.toFixed(2) + ') is greater than Due Amount (৳' + originalDue.toFixed(2) + '). Do you want to proceed?')) {
                return false;
            }
        }

        var depositData = captureDueDepositData(formElem);

        OfflineDB.saveInvoice(depositData).then(function () {
            SoundFX.posBeep();
            updatePendingCountBadge();

            // Update local cached due invoices so the due reflects immediately on screen
            OfflineDB.getCatalog('due_invoices_cache').then(function (cached) {
                if (cached && Array.isArray(cached)) {
                    var updated = cached.map(function (c) {
                        if (String(c.id) === String(depositData.invoice_no)) {
                            c.due = depositData.remaining_due;
                            c.paid_amount = (parseFloat(c.paid_amount) || 0) + depositData.deposited_amount;
                            c.discount = (parseFloat(c.discount) || 0) + depositData.additional_discount;
                        }
                        return c;
                    });
                    OfflineDB.saveCatalog('due_invoices_cache', updated);
                }
            }).catch(function () {});

            $('#myModal').modal('hide');
            showOfflineDueReceiptModal(depositData);

            if (typeof toastr !== 'undefined') {
                toastr.success('Offline Due Deposit ' + depositData.uuid + ' saved in local vault!', 'Due Collection');
            }
        }).catch(function (err) {
            console.error('[PWA] Error saving offline deposit:', err);
            alert('Failed to save offline deposit to local database. Please try again.');
        });
    }

    function initDueCollectionInterception() {
        $(document).on('submit', '#deposit-form', function (e) {
            if (!navigator.onLine) {
                e.preventDefault();
                processOfflineDepositSubmit(this);
                return false;
            }
        });

        $(document).on('click', '#deposit-form button[type="submit"]', function (e) {
            if (!navigator.onLine) {
                e.preventDefault();
                processOfflineDepositSubmit($('#deposit-form')[0]);
                return false;
            }
        });
    }

    // ============================================================
    // 8. Offline Vault & Sync Manager Modal
    // ============================================================
    function openOfflineVaultModal() {
        var existing = $('#offlineVaultModal');
        if (existing.length) existing.remove();

        OfflineDB.getAllInvoices().then(function (allInvoices) {
            var pending = allInvoices.filter(function (i) { return i.status === 'pending'; });
            var synced = allInvoices.filter(function (i) { return i.status === 'synced'; });

            var pendingRows = '';
            if (pending.length === 0) {
                pendingRows = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-check-circle text-success mr-1"></i> No pending offline transactions. All invoices & deposits are synced!</td></tr>';
            } else {
                pending.forEach(function (inv, idx) {
                    var isDeposit = inv.type === 'due_deposit';
                    var typeBadge = isDeposit ? '<span class="badge badge-info ml-1">Due Deposit</span>' : '<span class="badge badge-primary ml-1">POS Bill</span>';
                    var detailCol = isDeposit ? 
                        ('Invoice #' + inv.payment_id + '<br><small class="text-muted">Prev Due: ' + (inv.original_due || 0).toFixed(2) + '</small>') : 
                        ((inv.items ? inv.items.length : 0) + ' test(s)');
                    var amountCol = isDeposit ? 
                        ('<strong class="text-success">Paid: ' + (inv.deposited_amount || 0).toFixed(2) + ' TK</strong><br><small class="text-danger">Remaining: ' + (inv.remaining_due || 0).toFixed(2) + '</small>') : 
                        ('<strong>' + (inv.gross_total || 0).toFixed(2) + ' TK</strong><br><small class="text-success">Paid: ' + (inv.amount_received || 0).toFixed(2) + '</small>');

                    pendingRows += '<tr>' +
                        '<td>' + (idx + 1) + '</td>' +
                        '<td><span class="badge badge-warning">' + inv.uuid + '</span>' + typeBadge + '</td>' +
                        '<td>' + inv.display_time + '</td>' +
                        '<td><strong>' + inv.patient_name + '</strong><br><small class="text-muted">' + (inv.patient_phone || '') + '</small></td>' +
                        '<td>' + detailCol + '</td>' +
                        '<td>' + amountCol + '</td>' +
                        '<td class="text-right">' +
                            '<button type="button" class="btn btn-xs btn-primary btn-reprint-offline mr-1" data-uuid="' + inv.uuid + '" title="Re-print Receipt">' +
                                '<i class="fas fa-print"></i>' +
                            '</button>' +
                            '<button type="button" class="btn btn-xs btn-success btn-sync-single mr-1" data-uuid="' + inv.uuid + '" title="Sync Now">' +
                                '<i class="fas fa-sync-alt"></i>' +
                            '</button>' +
                            '<button type="button" class="btn btn-xs btn-danger btn-delete-offline" data-uuid="' + inv.uuid + '" title="Delete">' +
                                '<i class="fas fa-trash"></i>' +
                            '</button>' +
                        '</td>' +
                    '</tr>';
                });
            }

            var syncedRows = '';
            if (synced.length === 0) {
                syncedRows = '<tr><td colspan="6" class="text-center py-4 text-muted">No previously synced offline records found.</td></tr>';
            } else {
                synced.forEach(function (inv, idx) {
                    var isDeposit = inv.type === 'due_deposit';
                    var typeBadge = isDeposit ? '<span class="badge badge-info ml-1">Due Deposit</span>' : '<span class="badge badge-primary ml-1">POS Bill</span>';
                    var targetId = inv.server_invoice_id || inv.payment_id;
                    var amountText = isDeposit ? 
                        ('Paid: ' + (inv.deposited_amount || 0).toFixed(2) + ' TK') : 
                        ((inv.gross_total || 0).toFixed(2) + ' TK');

                    syncedRows += '<tr>' +
                        '<td>' + (idx + 1) + '</td>' +
                        '<td><span class="badge badge-light border">' + inv.uuid + '</span>' + typeBadge + '</td>' +
                        '<td><a href="finance/invoice?id=' + targetId + '" target="_blank" class="badge badge-success font-weight-bold">Invoice #' + targetId + ' <i class="fas fa-external-link-alt ml-1"></i></a></td>' +
                        '<td>' + inv.patient_name + '</td>' +
                        '<td>' + amountText + '</td>' +
                        '<td class="text-right">' +
                            '<button type="button" class="btn btn-xs btn-secondary btn-reprint-offline mr-1" data-uuid="' + inv.uuid + '" title="View/Print Offline Token">' +
                                '<i class="fas fa-print"></i>' +
                            '</button>' +
                            '<button type="button" class="btn btn-xs btn-outline-danger btn-delete-offline" data-uuid="' + inv.uuid + '" title="Delete from history">' +
                                '<i class="fas fa-times"></i>' +
                            '</button>' +
                        '</td>' +
                    '</tr>';
                });
            }

            var modalHtml = '<div class="modal fade" id="offlineVaultModal" tabindex="-1" role="dialog" aria-hidden="true">' +
                '<div class="modal-dialog modal-dialog-centered modal-xl" role="document">' +
                    '<div class="modal-content shadow-lg">' +
                        '<div class="modal-header bg-dark text-white py-2">' +
                            '<h5 class="modal-title font-weight-bold">' +
                                '<i class="fas fa-archive text-warning mr-2"></i> LifeCare Offline Vault & Sync Manager' +
                            '</h5>' +
                            '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                        '</div>' +
                        '<div class="modal-body p-4">' +
                            '<!-- Summary Stat Cards -->' +
                            '<div class="row mb-3">' +
                                '<div class="col-4">' +
                                    '<div class="vault-stat-card border-warning">' +
                                        '<div class="vault-stat-number text-warning">' + pending.length + '</div>' +
                                        '<div class="text-muted small font-weight-bold">Pending Sync</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="col-4">' +
                                    '<div class="vault-stat-card border-success">' +
                                        '<div class="vault-stat-number text-success">' + synced.length + '</div>' +
                                        '<div class="text-muted small font-weight-bold">Synced to Server</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="col-4">' +
                                    '<div class="vault-stat-card border-primary">' +
                                        '<div class="vault-stat-number text-primary">' + allInvoices.length + '</div>' +
                                        '<div class="text-muted small font-weight-bold">Total Offline Bills</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +

                            '<!-- Tabs -->' +
                            '<ul class="nav nav-pills mb-3" id="vaultTabs" role="tablist">' +
                                '<li class="nav-item">' +
                                    '<a class="nav-link active font-weight-bold" id="pending-tab" data-toggle="pill" href="#pending-content" role="tab">' +
                                        '<i class="fas fa-clock mr-1 text-warning"></i> Pending Bills (' + pending.length + ')' +
                                    '</a>' +
                                '</li>' +
                                '<li class="nav-item">' +
                                    '<a class="nav-link font-weight-bold" id="synced-tab" data-toggle="pill" href="#synced-content" role="tab">' +
                                        '<i class="fas fa-check-double mr-1 text-success"></i> Synced History (' + synced.length + ')' +
                                    '</a>' +
                                '</li>' +
                            '</ul>' +

                            '<div class="tab-content" id="vaultTabContent">' +
                                '<!-- Tab 1: Pending -->' +
                                '<div class="tab-pane fade show active" id="pending-content" role="tabpanel">' +
                                    '<div class="d-flex justify-content-between align-items-center mb-2">' +
                                        '<div class="small text-muted font-weight-bold">These invoices are preserved in local storage and will sync automatically once online.</div>' +
                                        '<div>' +
                                            '<button type="button" class="btn btn-sm btn-success" id="btn_vault_sync_all" ' + (pending.length === 0 ? 'disabled' : '') + '>' +
                                                '<i class="fas fa-cloud-upload-alt mr-1"></i> Sync All Pending Bills' +
                                            '</button>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="table-responsive">' +
                                        '<table class="table table-sm table-hover table-bordered small">' +
                                            '<thead class="thead-light">' +
                                                '<tr>' +
                                                    '<th>#</th>' +
                                                    '<th>Token UUID</th>' +
                                                    '<th>Generated Time</th>' +
                                                    '<th>Patient</th>' +
                                                    '<th>Tests</th>' +
                                                    '<th>Amount</th>' +
                                                    '<th class="text-right">Actions</th>' +
                                                '</tr>' +
                                            '</thead>' +
                                            '<tbody>' + pendingRows + '</tbody>' +
                                        '</table>' +
                                    '</div>' +
                                '</div>' +

                                '<!-- Tab 2: Synced History -->' +
                                '<div class="tab-pane fade" id="synced-content" role="tabpanel">' +
                                    '<div class="d-flex justify-content-between align-items-center mb-2">' +
                                        '<div class="small text-muted">Offline bills that have been uploaded to the central server.</div>' +
                                        (synced.length > 0 ? '<button type="button" class="btn btn-xs btn-outline-danger" id="btn_clear_synced_history"><i class="fas fa-broom mr-1"></i> Clear Synced History</button>' : '') +
                                    '</div>' +
                                    '<div class="table-responsive">' +
                                        '<table class="table table-sm table-hover table-bordered small">' +
                                            '<thead class="thead-light">' +
                                                '<tr>' +
                                                    '<th>#</th>' +
                                                    '<th>Token UUID</th>' +
                                                    '<th>Central Server Invoice</th>' +
                                                    '<th>Patient</th>' +
                                                    '<th>Amount</th>' +
                                                    '<th class="text-right">Actions</th>' +
                                                '</tr>' +
                                            '</thead>' +
                                            '<tbody>' + syncedRows + '</tbody>' +
                                        '</table>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="modal-footer py-2">' +
                            '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

            $('body').append(modalHtml);
            $('#offlineVaultModal').modal('show');

            // Wire action buttons
            $('.btn-reprint-offline').on('click', function () {
                var uuid = $(this).data('uuid');
                OfflineDB.getInvoice(uuid).then(function (inv) {
                    if (inv) {
                        $('#offlineVaultModal').modal('hide');
                        if (inv.type === 'due_deposit') {
                            showOfflineDueReceiptModal(inv);
                        } else {
                            showOfflineReceiptModal(inv);
                        }
                    }
                });
            });

            $('.btn-sync-single').on('click', function () {
                var uuid = $(this).data('uuid');
                $(this).html('<i class="fas fa-spinner fa-spin"></i>');
                syncSingleOfflineInvoice(uuid, function () {
                    $('#offlineVaultModal').modal('hide');
                    openOfflineVaultModal();
                });
            });

            $('.btn-delete-offline').on('click', function () {
                var uuid = $(this).data('uuid');
                if (confirm('Are you sure you want to remove offline token ' + uuid + '?')) {
                    OfflineDB.deleteInvoice(uuid).then(function () {
                        updatePendingCountBadge();
                        $('#offlineVaultModal').modal('hide');
                        openOfflineVaultModal();
                    });
                }
            });

            $('#btn_vault_sync_all').on('click', function () {
                $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> Syncing...');
                syncOfflineInvoices(true, function () {
                    $('#offlineVaultModal').modal('hide');
                    openOfflineVaultModal();
                });
            });

            $('#btn_clear_synced_history').on('click', function () {
                if (confirm('Clear all synced offline records from this browser?')) {
                    OfflineDB.clearSyncedInvoices().then(function () {
                        $('#offlineVaultModal').modal('hide');
                        openOfflineVaultModal();
                    });
                }
            });
        });
    }

    // ============================================================
    // 9. Camera Barcode / QR Scanner Engine
    // ============================================================
    function openCameraScannerModal() {
        var existing = $('#cameraScannerModal');
        if (existing.length) existing.remove();

        var modalHtml = '<div class="modal fade" id="cameraScannerModal" tabindex="-1" role="dialog" aria-hidden="true">' +
            '<div class="modal-dialog modal-dialog-centered" role="document">' +
                '<div class="modal-content bg-dark text-white">' +
                    '<div class="modal-header border-secondary py-2">' +
                        '<h5 class="modal-title font-weight-bold text-light">' +
                            '<i class="fas fa-qrcode text-primary mr-2"></i> Camera Barcode / QR Scanner' +
                        '</h5>' +
                        '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                    '</div>' +
                    '<div class="modal-body p-3 text-center">' +
                        '<div class="scanner-viewport-wrap mb-3">' +
                            '<video id="scanner_video_feed" class="scanner-video" autoplay playsinline muted></video>' +
                            '<div class="scanner-overlay-box">' +
                                '<div class="scanner-laser"></div>' +
                            '</div>' +
                        '</div>' +
                        '<p class="small text-muted mb-2"><i class="fas fa-info-circle mr-1"></i> Point your camera at a patient bill barcode, token, or QR code</p>' +
                        '<div class="input-group input-group-sm mb-1">' +
                            '<input type="text" id="manual_scan_input" class="form-control" placeholder="Or enter token / invoice # manually...">' +
                            '<div class="input-group-append">' +
                                '<button class="btn btn-primary" type="button" id="btn_manual_scan_go">Lookup</button>' +
                            '</div>' +
                        '</div>' +
                        '<div id="scanner_status_msg" class="small text-warning mt-2" style="display:none;"></div>' +
                    '</div>' +
                    '<div class="modal-footer border-secondary py-2">' +
                        '<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close Scanner</button>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';

        $('body').append(modalHtml);
        $('#cameraScannerModal').modal('show');

        var videoElem = document.getElementById('scanner_video_feed');

        // Start Webcam Stream
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
            }).then(function (stream) {
                scannerMediaStream = stream;
                videoElem.srcObject = stream;
                videoElem.play();
                startBarcodeDetection(videoElem);
            }).catch(function (err) {
                console.warn('[Scanner] Camera access error:', err);
                $('#scanner_status_msg').text('Camera access denied or unavailable. You can enter barcode number manually above.').show();
            });
        } else {
            $('#scanner_status_msg').text('Webcam API not supported in this browser. Use manual entry above.').show();
        }

        function handleDetectedCode(codeStr) {
            if (!codeStr) return;
            SoundFX.posBeep();
            stopCameraStream();
            $('#cameraScannerModal').modal('hide');

            var trimmed = codeStr.trim();
            // Case 1: Offline Tokens (Due Deposit or POS Bill)
            if (trimmed.startsWith('OFF-DEP-')) {
                OfflineDB.getInvoice(trimmed).then(function (inv) {
                    if (inv) {
                        showOfflineDueReceiptModal(inv);
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.info('Due Deposit Token ' + trimmed + ' not found in local vault.', 'Lookup');
                        }
                    }
                });
            } else if (trimmed.startsWith('OFF-')) {
                OfflineDB.getInvoice(trimmed).then(function (inv) {
                    if (inv) {
                        showOfflineReceiptModal(inv);
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.info('Token ' + trimmed + ' not found in local vault. Checking server...', 'Lookup');
                        }
                    }
                });
            } else if (/^\d+$/.test(trimmed)) {
                // Case 2: Numeric Server Invoice ID -> open invoice
                window.location.href = $('base').attr('href') ? $('base').attr('href') + 'finance/invoice?id=' + trimmed : 'finance/invoice?id=' + trimmed;
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.info('Scanned Code: ' + trimmed, 'Barcode Scanned');
                }
            }
        }

        function startBarcodeDetection(vElem) {
            // Check for native BarcodeDetector API
            if ('BarcodeDetector' in window) {
                var detector = new BarcodeDetector({ formats: ['code_128', 'qr_code', 'ean_13', 'ean_8', 'code_39'] });
                scannerInterval = setInterval(function () {
                    if (vElem.readyState === vElem.HAVE_ENOUGH_DATA) {
                        detector.detect(vElem).then(function (barcodes) {
                            if (barcodes.length > 0) {
                                clearInterval(scannerInterval);
                                handleDetectedCode(barcodes[0].rawValue);
                            }
                        }).catch(function () {});
                    }
                }, 250);
            }
        }

        function stopCameraStream() {
            if (scannerInterval) {
                clearInterval(scannerInterval);
                scannerInterval = null;
            }
            if (scannerMediaStream) {
                scannerMediaStream.getTracks().forEach(function (track) { track.stop(); });
                scannerMediaStream = null;
            }
        }

        $('#cameraScannerModal').on('hidden.bs.modal', function () {
            stopCameraStream();
        });

        $('#btn_manual_scan_go').on('click', function () {
            var val = $('#manual_scan_input').val();
            if (val) handleDetectedCode(val);
        });

        $('#manual_scan_input').on('keypress', function (e) {
            if (e.which === 13) {
                var val = $(this).val();
                if (val) handleDetectedCode(val);
            }
        });
    }

    function initCameraScanner() {
        $(document).on('click', '#pwa_camera_scanner_btn', function (e) {
            e.preventDefault();
            openCameraScannerModal();
        });
    }

    // ============================================================
    // 10. 1-Click Kiosk / Fullscreen POS Mode
    // ============================================================
    function initFullscreenKiosk() {
        $(document).on('click', '#pwa_fullscreen_btn', function (e) {
            e.preventDefault();
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(function (err) {
                    console.warn('[PWA Fullscreen] Error attempting to enable full-screen mode:', err);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        });

        document.addEventListener('fullscreenchange', function () {
            var $btn = $('#pwa_fullscreen_btn');
            if (document.fullscreenElement) {
                $btn.html('<i class="fas fa-compress"></i>').attr('title', 'Exit Fullscreen Kiosk Mode');
            } else {
                $btn.html('<i class="fas fa-expand"></i>').attr('title', 'Toggle Fullscreen POS Mode');
            }
        });
    }

    // ============================================================
    // 11. Background Sync Engine (Single & Batch)
    // ============================================================
    function syncSingleOfflineInvoice(uuid, callback) {
        if (!navigator.onLine) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('Cannot sync while offline.', 'Offline');
            }
            if (callback) callback(false);
            return;
        }

        OfflineDB.getInvoice(uuid).then(function (inv) {
            if (!inv) {
                if (callback) callback(false);
                return;
            }

            var formData = new FormData();
            formData.append('is_offline_sync', '1');
            formData.append('offline_uuid', inv.uuid);

            var postUrl = '';

            if (inv.type === 'due_deposit') {
                formData.append('id', '');
                formData.append('patient', inv.patient_id || '');
                formData.append('payment_id', inv.payment_id || inv.invoice_no);
                formData.append('deposited_amount', inv.deposited_amount);
                formData.append('additional_discount', inv.additional_discount);
                formData.append('deposit_type', inv.deposit_type || 'Cash');
                formData.append('redirect', 'due');

                if (Array.isArray(inv.formData)) {
                    inv.formData.forEach(function (field) {
                        if (!formData.has(field.name)) {
                            formData.append(field.name, field.value);
                        }
                    });
                }

                postUrl = $('base').attr('href') ? $('base').attr('href') + 'finance/deposit' : 'finance/deposit';
            } else {
                if (Array.isArray(inv.formData)) {
                    inv.formData.forEach(function (field) {
                        formData.append(field.name, field.value);
                    });
                }
                if (!inv.formData.some(function (f) { return f.name === 'patient'; })) {
                    formData.append('patient', 'add_new');
                }

                postUrl = $('base').attr('href') ? $('base').attr('href') + 'finance/addPayment' : 'finance/addPayment';
            }

            fetch(postUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (res) { return res.json(); })
            .then(function (res) {
                if (res && res.status === 'success') {
                    SoundFX.successChime();
                    var refId = res.payment_id || res.invoice_id || inv.payment_id;
                    OfflineDB.markSynced(inv.uuid, refId).then(function () {
                        updatePendingCountBadge();
                        if (typeof toastr !== 'undefined') {
                            var msg = (inv.type === 'due_deposit') ? 
                                ('Due deposit for Invoice #' + refId + ' synced to central server!') : 
                                ('Token ' + inv.uuid + ' synced -> Central Invoice #' + refId);
                            toastr.success(msg, 'Synced');
                        }
                        if (callback) callback(true);
                    });
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error((res && res.message) ? res.message : 'Sync failed', 'Error');
                    }
                    if (callback) callback(false);
                }
            })
            .catch(function (err) {
                console.error('[PWA Sync] Error:', err);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Network error during sync', 'Sync Error');
                }
                if (callback) callback(false);
            });
        });
    }

    function syncOfflineInvoices(manualTrigger, callback) {
        if (!navigator.onLine) {
            if (manualTrigger && typeof toastr !== 'undefined') {
                toastr.warning('Cannot sync while offline. Please check internet connection.', 'Offline');
            }
            if (callback) callback(false);
            return;
        }

        if (isSyncing) return;
        isSyncing = true;

        OfflineDB.getAllInvoices().then(function (all) {
            var pendingList = all.filter(function (i) { return i.status === 'pending'; });

            if (pendingList.length === 0) {
                isSyncing = false;
                updatePendingCountBadge();
                if (manualTrigger && typeof toastr !== 'undefined') {
                    toastr.info('All transactions are already up to date. No pending syncs.', 'All Synced');
                }
                if (callback) callback(true);
                return;
            }

            var $badge = $('#pwa_sync_badge');
            $badge.html('<i class="fas fa-spinner fa-spin mr-1"></i> Syncing...');

            if (typeof toastr !== 'undefined') {
                toastr.info('Syncing ' + pendingList.length + ' offline transaction(s) with central server...', 'Background Sync');
            }

            var syncIndex = 0;
            var successCount = 0;

            function processNext() {
                if (syncIndex >= pendingList.length) {
                    isSyncing = false;
                    updatePendingCountBadge();

                    if (successCount > 0) {
                        SoundFX.successChime();
                        DesktopNotify.send(
                            'LifeCare Sync Complete',
                            successCount + ' offline transaction(s) successfully synced with central server.',
                            'pwa-sync-complete'
                        );

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sync Completed',
                                text: successCount + ' offline transaction(s) successfully synced with central server!'
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success(successCount + ' transaction(s) synced with server!', 'Sync Complete');
                        }
                    }
                    if (callback) callback(true);
                    return;
                }

                var inv = pendingList[syncIndex];
                syncIndex++;

                var formData = new FormData();
                formData.append('is_offline_sync', '1');
                formData.append('offline_uuid', inv.uuid);

                var postUrl = '';

                if (inv.type === 'due_deposit') {
                    formData.append('id', '');
                    formData.append('patient', inv.patient_id || '');
                    formData.append('payment_id', inv.payment_id || inv.invoice_no);
                    formData.append('deposited_amount', inv.deposited_amount);
                    formData.append('additional_discount', inv.additional_discount);
                    formData.append('deposit_type', inv.deposit_type || 'Cash');
                    formData.append('redirect', 'due');

                    if (Array.isArray(inv.formData)) {
                        inv.formData.forEach(function (field) {
                            if (!formData.has(field.name)) {
                                formData.append(field.name, field.value);
                            }
                        });
                    }

                    postUrl = $('base').attr('href') ? $('base').attr('href') + 'finance/deposit' : 'finance/deposit';
                } else {
                    if (Array.isArray(inv.formData)) {
                        inv.formData.forEach(function (field) {
                            formData.append(field.name, field.value);
                        });
                    }
                    if (!inv.formData.some(function (f) { return f.name === 'patient'; })) {
                        formData.append('patient', 'add_new');
                    }

                    postUrl = $('base').attr('href') ? $('base').attr('href') + 'finance/addPayment' : 'finance/addPayment';
                }

                fetch(postUrl, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (response) { return response.json(); })
                .then(function (res) {
                    if (res && res.status === 'success') {
                        successCount++;
                        var refId = res.payment_id || res.invoice_id || inv.payment_id;
                        OfflineDB.markSynced(inv.uuid, refId).then(function () {
                            processNext();
                        });
                    } else {
                        console.warn('[PWA Sync] Server returned non-success:', res);
                        processNext();
                    }
                })
                .catch(function (err) {
                    console.error('[PWA Sync] Network error syncing transaction ' + inv.uuid + ':', err);
                    processNext();
                });
            }

            processNext();
        }).catch(function (err) {
            console.error('[PWA] Error reading pending list for sync:', err);
            isSyncing = false;
            if (callback) callback(false);
        });
    }

    // ============================================================
    // 13. Lab Report Offline Auto-Draft Engine
    // ============================================================
    function initLabReportOfflineDraft() {
        var $labIdInput = $('form#editLabForm input[name="id"]');
        if ($labIdInput.length === 0) {
            return;
        }

        var labId = $labIdInput.val() || 'new';
        var draftKey = 'lifecare_lab_draft_' + labId;

        // 1. Check for unsaved offline draft on load
        try {
            var savedDraft = localStorage.getItem(draftKey);
            if (savedDraft) {
                var draftObj = JSON.parse(savedDraft);
                setTimeout(function () {
                    var currentContent = '';
                    if (typeof tinymce !== 'undefined' && tinymce.get('editor')) {
                        currentContent = tinymce.get('editor').getContent();
                    } else if ($('#editor').length > 0) {
                        currentContent = $('#editor').val();
                    }

                    if ((!currentContent || currentContent.trim() === '') && draftObj.content) {
                        if (typeof tinymce !== 'undefined' && tinymce.get('editor')) {
                            tinymce.get('editor').setContent(draftObj.content);
                        } else if ($('#editor').length > 0) {
                            $('#editor').val(draftObj.content);
                        }

                        if (typeof SoundFX !== 'undefined' && SoundFX.successChime) {
                            SoundFX.successChime();
                        }

                        if ($('#draft_restore_notice').length === 0) {
                            var noticeHtml = '<div id="draft_restore_notice" class="alert alert-warning alert-dismissible fade show shadow-sm mb-3" role="alert">' +
                                '<i class="fas fa-history mr-2"></i><strong>Offline Draft Restored:</strong> Your previously unsaved report draft from ' + (draftObj.time || '') + ' was automatically recovered.' +
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                '</div>';
                            $('form#editLabForm').prepend(noticeHtml);
                        }
                    }
                }, 1200);
            }
        } catch (e) {}

        // 2. Periodic Auto-save (Every 10 seconds)
        setInterval(function () {
            try {
                var content = '';
                if (typeof tinymce !== 'undefined' && tinymce.get('editor')) {
                    content = tinymce.get('editor').getContent();
                } else if ($('#editor').length > 0) {
                    content = $('#editor').val();
                }

                if (content && content.trim() !== '') {
                    localStorage.setItem(draftKey, JSON.stringify({
                        content: content,
                        time: new Date().toLocaleTimeString()
                    }));
                }
            } catch (e) {}
        }, 10000);

        // 3. Clear draft when form is submitted
        $('form#editLabForm').on('submit', function () {
            try {
                localStorage.removeItem(draftKey);
            } catch (e) {}
        });
    }

    // ============================================================
    // 14. Global Initialization
    // ============================================================
    $(document).ready(function () {
        initServiceWorker();
        initInstallPrompt();
        initNetworkListeners();
        initPOSInterception();
        initDueCollectionInterception();
        initCameraScanner();
        initFullscreenKiosk();
        initLabReportOfflineDraft();
        updateNetworkStatusUI();
        updatePendingCountBadge();

        // Preload tests, doctors and templates catalog on page load
        setTimeout(preloadDiagnosticCatalog, 2000);

        // Auto sync check on startup
        setTimeout(function () {
            if (navigator.onLine) {
                syncOfflineInvoices();
            }
        }, 3000);
    });

})(window, document, jQuery);
