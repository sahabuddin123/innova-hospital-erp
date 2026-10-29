/**
 * LifeCare Diagnostic Center - PWA & Offline POS Sync Engine
 * Handles Service Worker, PWA installation, Network Status,
 * IndexedDB storage for offline diagnostic billing, and automatic background sync.
 */
"use strict";

(function (window, document, $) {
    var DB_NAME = 'lifecare_pwa_db';
    var DB_VERSION = 1;
    var STORE_NAME = 'pending_invoices';
    var dbInstance = null;
    var deferredInstallPrompt = null;
    var isSyncing = false;

    // ============================================================
    // 1. IndexedDB Helper Module
    // ============================================================
    var OfflineDB = {
        open: function () {
            return new Promise(function (resolve, reject) {
                if (dbInstance) {
                    return resolve(dbInstance);
                }
                if (!window.indexedDB) {
                    return reject(new Error('IndexedDB not supported in this browser.'));
                }
                var request = window.indexedDB.open(DB_NAME, DB_VERSION);
                request.onupgradeneeded = function (event) {
                    var db = event.target.result;
                    if (!db.objectStoreNames.contains(STORE_NAME)) {
                        var store = db.createObjectStore(STORE_NAME, { keyPath: 'uuid' });
                        store.createIndex('status', 'status', { unique: false });
                        store.createIndex('created_at', 'created_at', { unique: false });
                    }
                };
                request.onsuccess = function (event) {
                    dbInstance = event.target.result;
                    resolve(dbInstance);
                };
                request.onerror = function (event) {
                    console.error('[PWA DB] Error opening database:', event.target.error);
                    reject(event.target.error);
                };
            });
        },

        saveInvoice: function (invoiceData) {
            return OfflineDB.open().then(function (db) {
                return new Promise(function (resolve, reject) {
                    var tx = db.transaction([STORE_NAME], 'readwrite');
                    var store = tx.objectStore(STORE_NAME);
                    var req = store.put(invoiceData);
                    req.onsuccess = function () { resolve(invoiceData); };
                    req.onerror = function (e) { reject(e.target.error); };
                });
            });
        },

        getAllPending: function () {
            return OfflineDB.open().then(function (db) {
                return new Promise(function (resolve, reject) {
                    var tx = db.transaction([STORE_NAME], 'readonly');
                    var store = tx.objectStore(STORE_NAME);
                    var req = store.getAll();
                    req.onsuccess = function (e) {
                        var all = e.target.result || [];
                        var pending = all.filter(function (item) {
                            return item.status === 'pending';
                        });
                        resolve(pending);
                    };
                    req.onerror = function (e) { reject(e.target.error); };
                });
            });
        },

        markSynced: function (uuid, serverInvoiceId) {
            return OfflineDB.open().then(function (db) {
                return new Promise(function (resolve, reject) {
                    var tx = db.transaction([STORE_NAME], 'readwrite');
                    var store = tx.objectStore(STORE_NAME);
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
                    var tx = db.transaction([STORE_NAME], 'readwrite');
                    var store = tx.objectStore(STORE_NAME);
                    var req = store.delete(uuid);
                    req.onsuccess = function () { resolve(); };
                    req.onerror = function (e) { reject(e.target.error); };
                });
            });
        }
    };

    // ============================================================
    // 2. Service Worker Registration & PWA Install Engine
    // ============================================================
    function initServiceWorker() {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                var swPath = $('base').attr('href') ? $('base').attr('href') + 'sw.js' : 'sw.js';
                navigator.serviceWorker.register(swPath, { scope: './' })
                    .then(function (reg) {
                        console.log('[PWA SW] Registered successfully with scope:', reg.scope);

                        // Listen for background sync triggers sent by SW
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
            // Prevent Chrome from showing the mini-infobar automatically
            e.preventDefault();
            deferredInstallPrompt = e;
            $('#pwa_install_btn').removeClass('d-none');
        });

        $(document).on('click', '#pwa_install_btn', function () {
            if (!deferredInstallPrompt) return;
            deferredInstallPrompt.prompt();
            deferredInstallPrompt.userChoice.then(function (choiceResult) {
                if (choiceResult.outcome === 'accepted') {
                    console.log('[PWA] User accepted the install prompt');
                    $('#pwa_install_btn').addClass('d-none');
                }
                deferredInstallPrompt = null;
            });
        });

        window.addEventListener('appinstalled', function () {
            console.log('[PWA] LifeCare App installed successfully');
            $('#pwa_install_btn').addClass('d-none');
            if (typeof toastr !== 'undefined') {
                toastr.success('LifeCare App installed successfully on your device!', 'App Installed');
            }
        });
    }

    // ============================================================
    // 3. Network Status & Navbar Indicator
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
        OfflineDB.getAllPending().then(function (pendingList) {
            var count = pendingList.length;
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
            if (typeof toastr !== 'undefined') {
                toastr.success('Internet connection restored! Checking for offline bills to sync...', 'Online');
            }
            setTimeout(syncOfflineInvoices, 1500);
        });

        window.addEventListener('offline', function () {
            updateNetworkStatusUI();
            if (typeof toastr !== 'undefined') {
                toastr.warning('You are currently offline. Invoices will be saved locally and auto-synced once back online.', 'Offline Mode');
            }
        });

        // Click badge to manually trigger sync
        $(document).on('click', '#pwa_sync_badge', function (e) {
            e.preventDefault();
            syncOfflineInvoices(true);
        });
    }

    // ============================================================
    // 4. Offline POS Invoice Interception & Local Storing
    // ============================================================
    function capturePOSInvoiceData(formElem) {
        var $form = $(formElem);
        var formDataArray = $form.serializeArray();

        // Generate offline UUID & Token
        var now = new Date();
        var dateFormatted = now.toLocaleDateString('en-GB') + ' ' + now.toLocaleTimeString('en-US', { hour12: true });
        var tokenRand = Math.floor(Math.random() * 8999 + 1000);
        var offlineUuid = 'OFF-' + now.getFullYear() + (now.getMonth() + 1) + now.getDate() + '-' + tokenRand;

        // Gather human-friendly summary for receipt display
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

        // Selected Tests
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

        // Build item rows HTML
        var itemRowsHtml = '';
        invoice.items.forEach(function (item, idx) {
            itemRowsHtml += '<tr>' +
                '<td>' + (idx + 1) + '</td>' +
                '<td class="font-weight-bold text-left">' + item.name + '</td>' +
                '<td class="text-center">' + item.qty + '</td>' +
                '<td class="text-right">' + item.price.toFixed(2) + '</td>' +
                '<td class="text-right">' + item.total.toFixed(2) + '</td>' +
            '</tr>';
        });

        // Determine if due should be displayed (if full paid or 0 due, hide due section)
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
                            '<i class="fas fa-wifi text-danger mr-2"></i> Offline Receipt Generated (Pending Sync)' +
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
                                (invoice.referrer_name ? '<div><strong>Ref:</strong> ' + invoice.referrer_name + '</div>' : '') +
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

                        '<!-- Notice & QR/Barcode Placeholder -->' +
                        '<div class="alert alert-light border small text-center text-muted mb-0 py-2">' +
                            '<i class="fas fa-info-circle mr-1 text-info"></i> ' +
                            'Offline billing complete. This bill is preserved in local storage and will sync automatically once internet connects.' +
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

        // When modal closes, reset the POS page cleanly for the next patient
        $('#offlineReceiptModal').on('hidden.bs.modal', function () {
            resetPOSForm();
        });
    }

    function resetPOSForm() {
        if ($('#editPaymentForm').length) {
            // Deselect all selected tests in multi-select
            if ($('select.multi-select').length && typeof $('select.multi-select').multiSelect === 'function') {
                $('select.multi-select').multiSelect('deselect_all');
            }
            // Clear input fields
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
            // Trigger calculation to refresh totals
            if (typeof updatePOSState === 'function') {
                updatePOSState();
            }
            // Clear local storage cart
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

        // Catch save buttons when offline
        $(document).on('click', '#submit2, #submit, #submit-btn2', function (e) {
            if (!navigator.onLine) {
                e.preventDefault();
                processOfflineFormSubmit($('#editPaymentForm')[0]);
                return false;
            }
        });
    }

    function processOfflineFormSubmit(formElem) {
        var $form = $(formElem);

        // Validation 1: Patient Selection or Inline Name
        var patientVal = $('#pos_select').val();
        var pName = $('#p_name').val();
        var pPhone = $('#p_phone').val();

        if (!patientVal && !pName) {
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

        // Validation 2: At least 1 test selected
        var selectedTests = $('select.multi-select option:selected').length;
        if (selectedTests === 0) {
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

        // Capture data & save in IndexedDB
        var invoiceData = capturePOSInvoiceData(formElem);
        OfflineDB.saveInvoice(invoiceData).then(function () {
            updatePendingCountBadge();
            showOfflineReceiptModal(invoiceData);
            if (typeof toastr !== 'undefined') {
                toastr.success('Offline Invoice ' + invoiceData.uuid + ' saved locally!', 'Offline POS');
            }
        }).catch(function (err) {
            console.error('[PWA] Error saving offline invoice:', err);
            alert('Failed to save offline invoice to local database. Please try again.');
        });
    }

    // ============================================================
    // 5. Automatic & Manual Background Sync Engine
    // ============================================================
    function syncOfflineInvoices(manualTrigger) {
        if (!navigator.onLine) {
            if (manualTrigger && typeof toastr !== 'undefined') {
                toastr.warning('Cannot sync while offline. Please check your internet connection.', 'Still Offline');
            }
            return;
        }

        if (isSyncing) return;
        isSyncing = true;

        OfflineDB.getAllPending().then(function (pendingList) {
            if (pendingList.length === 0) {
                isSyncing = false;
                updatePendingCountBadge();
                if (manualTrigger && typeof toastr !== 'undefined') {
                    toastr.info('All invoices are already up to date. No pending syncs.', 'All Synced');
                }
                return;
            }

            var $badge = $('#pwa_sync_badge');
            $badge.html('<i class="fas fa-spinner fa-spin mr-1"></i> Syncing...');

            if (typeof toastr !== 'undefined') {
                toastr.info('Syncing ' + pendingList.length + ' offline bill(s) with central server...', 'Background Sync');
            }

            var syncIndex = 0;
            var successCount = 0;

            function processNext() {
                if (syncIndex >= pendingList.length) {
                    isSyncing = false;
                    updatePendingCountBadge();

                    if (successCount > 0) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sync Completed',
                                text: successCount + ' offline bill(s) successfully synced with the central server!'
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success(successCount + ' bills synced with server!', 'Sync Complete');
                        }
                    }
                    return;
                }

                var inv = pendingList[syncIndex];
                syncIndex++;

                // Build FormData payload
                var formData = new FormData();
                formData.append('is_offline_sync', '1');
                formData.append('offline_uuid', inv.uuid);

                if (Array.isArray(inv.formData)) {
                    inv.formData.forEach(function (field) {
                        formData.append(field.name, field.value);
                    });
                }

                // If patient was newly entered offline, pass new patient flag
                if (!inv.formData.some(function (f) { return f.name === 'patient'; })) {
                    formData.append('patient', 'add_new');
                }

                var postUrl = $('base').attr('href') ? $('base').attr('href') + 'finance/addPayment' : 'finance/addPayment';

                fetch(postUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function (response) {
                    return response.json();
                })
                .then(function (res) {
                    if (res && res.status === 'success') {
                        successCount++;
                        OfflineDB.markSynced(inv.uuid, res.invoice_id).then(function () {
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Token ' + inv.uuid + ' synced -> Central Invoice #' + res.invoice_id, 'Synced');
                            }
                            processNext();
                        });
                    } else {
                        console.warn('[PWA Sync] Server returned non-success:', res);
                        processNext();
                    }
                })
                .catch(function (err) {
                    console.error('[PWA Sync] Error syncing invoice ' + inv.uuid + ':', err);
                    processNext();
                });
            }

            processNext();
        }).catch(function (err) {
            console.error('[PWA] Error reading pending list for sync:', err);
            isSyncing = false;
        });
    }

    // ============================================================
    // 6. Global Initialization
    // ============================================================
    $(document).ready(function () {
        initServiceWorker();
        initInstallPrompt();
        initNetworkListeners();
        initPOSInterception();
        updateNetworkStatusUI();
        updatePendingCountBadge();

        // Check for sync 3 seconds after page load if online
        setTimeout(function () {
            if (navigator.onLine) {
                syncOfflineInvoices();
            }
        }, 3000);
    });

})(window, document, jQuery);
