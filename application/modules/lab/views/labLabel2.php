<?php
/**
 * Life Care Diagnostic Center - Smart Specimen Barcode & Radiology Envelope Label
 * Tailored for Xprinter XP-350BM (50mm x 30mm) Thermal Barcode Label Printer
 * Supports zero-dialog Silent Printing via Chrome Kiosk Mode / iframe
 */

// Format Patient Age
$patient_age_compact = '';
$patient_age_full = '';
if (!empty($patient->age)) {
    $age_parts = explode('-', $patient->age);
    if (count($age_parts) === 3) {
        $y = (int)$age_parts[0];
        $m = (int)$age_parts[1];
        $d = (int)$age_parts[2];
        if ($y > 0) {
            $patient_age_compact = $y . 'Y' . ($m > 0 ? " {$m}M" : '');
        } elseif ($m > 0) {
            $patient_age_compact = $m . 'M' . ($d > 0 ? " {$d}D" : '');
        } else {
            $patient_age_compact = $d . 'D';
        }
        $patient_age_full = "{$y} Y {$m} M {$d} D";
    } else {
        $patient_age_compact = $patient->age . (is_numeric($patient->age) ? 'Y' : '');
        $patient_age_full = $patient->age;
    }
}

// Format Gender
$gender_display = !empty($patient->sex) ? ucfirst($patient->sex) : 'N/A';
$gender_short = !empty($patient->sex) ? strtoupper(substr($patient->sex, 0, 1)) : '-';

// Doctor Name
$ref_doctor_name = 'SELF';
if (!empty($doctor) && !empty($doctor->name)) {
    $ref_doctor_name = $doctor->name;
} elseif (!empty($lab->doctor_name)) {
    $ref_doctor_name = $lab->doctor_name;
}

// Test names string
if (empty($test_names) && !empty($labels)) {
    $test_names = array();
    foreach ($labels as $l) {
        $pcat = $this->db->get_where('payment_category', array('id' => $l->category_id))->row();
        if ($pcat) {
            $test_names[] = $pcat->category;
        }
    }
}
$test_names_str = !empty($test_names) ? implode(', ', $test_names) : 'Laboratory Test';

// Barcode numbers
$barcode_inv = '000000000' . $lab->invoice_id;
$barcode_pid = '000000000' . $patient->id;
$barcode_lab = str_pad($lab->id, 10, '0', STR_PAD_LEFT);

// Specimen badge
if (empty($specimen_type)) {
    $specimen_type = !empty($is_imaging) ? 'IMAGING' : 'BLOOD';
}

// Active Layout Mode: 'tube' for Specimen Barcode, 'envelope' for USG/X-Ray
$active_mode = !empty($default_mode) ? $default_mode : (!empty($is_imaging) ? 'envelope' : 'tube');

// Check URL overrides
if (isset($_GET['format'])) {
    $active_mode = ($_GET['format'] === 'envelope') ? 'envelope' : 'tube';
}
$is_silent = !empty($silent) || (isset($_GET['silent']) && $_GET['silent'] == '1');
$auto_rotate = isset($_GET['rotate']) && $_GET['rotate'] == '90';
?>

<?php if ($is_silent): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Barcode Label #<?php echo $lab->invoice_id; ?> - Life Care Diagnostic</title>
    <style>
        @page {
            size: <?php echo ($active_mode === 'envelope') ? '100mm 60mm' : '50mm 30mm'; ?>;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            color: #000 !important;
            width: <?php echo ($active_mode === 'envelope') ? '100mm' : '50mm'; ?> !important;
            height: <?php echo ($active_mode === 'envelope') ? '60mm' : '30mm'; ?> !important;
            overflow: hidden !important;
            font-family: Arial, Helvetica, sans-serif;
        }
        .thermal-label-wrap {
            width: <?php echo ($active_mode === 'envelope') ? '100mm' : '50mm'; ?>;
            height: <?php echo ($active_mode === 'envelope') ? '60mm' : '30mm'; ?>;
            padding: 1.5mm 2mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            page-break-after: always;
        }
        .thermal-label-wrap:last-child {
            page-break-after: avoid;
        }
    </style>
</head>
<body onload="window.print();">
    <div id="silentPrintContainer">
        <!-- Render Tube Label -->
        <?php if ($active_mode === 'tube'): ?>
            <div class="thermal-label-wrap" style="width: 50mm; height: 30mm; padding: 1.5mm 2mm; font-size: 7pt; line-height: 1.1;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 0.8px solid #000; padding-bottom: 1px;">
                    <span style="font-size: 7pt; font-weight: 800; letter-spacing: 0.3px;">LIFE CARE DIAGNOSTIC</span>
                    <span style="font-size: 6.5pt; font-weight: 700;"><?php echo date('d/m/y', $lab->date); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 1px;">
                    <span style="font-size: 8.5pt; font-weight: 900; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 32mm;">
                        <?php echo strtoupper($patient->name); ?>
                    </span>
                    <span style="font-size: 7.5pt; font-weight: 800;">
                        PID: <?php echo $patient->id; ?>
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 6.5pt; font-weight: 700;">
                    <span><?php echo $patient_age_compact; ?> / <?php echo $gender_short; ?></span>
                    <span>Inv: #<?php echo $lab->invoice_id; ?></span>
                    <span style="border: 0.8px solid #000; padding: 0.5px 2px; border-radius: 2px; font-size: 5.5pt; font-weight: 800;">
                        <?php echo $specimen_type; ?>
                    </span>
                </div>
                <div style="text-align: center; margin: 1px 0;">
                    <img alt="Barcode" src="<?php echo site_url('lab/barcode'); ?>?text=<?php echo $barcode_inv; ?>&size=24&sizefactor=1&print=false" style="height: 7.5mm; max-width: 44mm; display: block; margin: 0 auto; image-rendering: pixelated;" />
                    <span style="font-family: monospace; font-size: 6pt; font-weight: 800; letter-spacing: 1px;">*<?php echo $barcode_inv; ?>*</span>
                </div>
                <div style="font-size: 6pt; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; border-top: 0.8px solid #000; padding-top: 1px;">
                    <span>T:</span> <?php echo $test_names_str; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Render Envelope Label -->
            <div class="thermal-label-wrap" style="width: 100mm; height: 60mm; padding: 3mm; font-size: 9pt;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #000; padding-bottom: 2px;">
                    <span style="font-weight: 900; font-size: 11pt;">LIFE CARE DIAGNOSTIC CENTER</span>
                    <span style="font-weight: 700;">Date: <?php echo date('d/m/Y', $lab->date); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 3px;">
                    <span><strong>PID:</strong> <?php echo $patient->id; ?></span>
                    <span><strong>Invoice ID:</strong> <?php echo $lab->invoice_id; ?></span>
                </div>
                <div><strong>Patient:</strong> <?php echo strtoupper($patient->name); ?></div>
                <div style="display: flex; justify-content: space-between;">
                    <span><strong>Age:</strong> <?php echo $patient_age_full; ?></span>
                    <span><strong>Gender:</strong> <?php echo $gender_display; ?></span>
                </div>
                <div><strong>Ref By:</strong> <?php echo $ref_doctor_name; ?></div>
                <div><strong>Test:</strong> <?php echo $test_names_str; ?></div>
                <div style="display: flex; justify-content: space-between; margin-top: 4px;">
                    <div>Film: [ _______ ]</div>
                    <div>Paper Print: [ _______ ]</div>
                </div>
                <div style="text-align: center; margin-top: 4px;">
                    <img alt="Barcode" src="<?php echo site_url('lab/barcode'); ?>?text=<?php echo $barcode_inv; ?>&size=28&sizefactor=1&print=false" style="height: 10mm; display: block; margin: 0 auto;" />
                    <span style="font-family: monospace; font-size: 7.5pt; font-weight: 700;">*<?php echo $barcode_inv; ?>*</span>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script>
        window.onafterprint = function() {
            try { window.close(); } catch(e) {}
        };
    </script>
</body>
</html>
<?php exit; endif; ?>

<!-- ========================================================================= -->
<!-- NORMAL INTERACTIVE DASHBOARD VIEW (Label Preview & Print Center)          -->
<!-- ========================================================================= -->

<link href="common/extranal/css/lab/lab.css" rel="stylesheet">

<style>
/* Clean Screen Styling */
.label-control-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.mode-tab-btn {
    font-weight: 700;
    font-size: 0.95rem;
    padding: 10px 18px;
    border-radius: 6px;
    border: 2px solid transparent;
    transition: all 0.2s ease;
}
.mode-tab-btn.active {
    background-color: #16a34a !important;
    color: #ffffff !important;
    border-color: #15803d !important;
    box-shadow: 0 2px 4px rgba(22, 163, 74, 0.3);
}
.mode-tab-btn:not(.active) {
    background-color: #f1f5f9;
    color: #475569;
    border-color: #cbd5e1;
}
.mode-tab-btn:hover:not(.active) {
    background-color: #e2e8f0;
    color: #0f172a;
}

/* 50mm x 30mm Thermal Label Preview Container (Exact XP-350BM proportions) */
.label-preview-wrapper {
    background: #f8fafc;
    border: 2px dashed #94a3b8;
    border-radius: 8px;
    padding: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 220px;
    overflow-x: auto;
}
.thermal-sticker-screen {
    width: 50mm;
    height: 30mm;
    background: #ffffff;
    color: #000000;
    box-sizing: border-box;
    padding: 1.5mm 2mm;
    border: 1px solid #000000;
    box-shadow: 0 6px 12px rgba(0,0,0,0.12);
    font-family: Arial, Helvetica, sans-serif;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
    margin: 5px;
    flex-shrink: 0;
    transition: transform 0.2s;
}
.thermal-sticker-screen.rotated-90 {
    transform: rotate(90deg);
    margin: 25px;
}

/* Envelope Label Preview */
.envelope-label-screen {
    max-width: 5.5in;
    width: 100%;
    background: #ffffff;
    border: 1.5px solid #000000;
    padding: 15px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.12);
    font-family: Arial, Helvetica, sans-serif;
    color: #000000;
}

/* ========================================================================= */
/* STRICT PRINT STYLES FOR XPRINTER XP-350BM                                 */
/* ========================================================================= */
@media print {
    /* Set page dimensions based on active mode */
    body.print-mode-tube {
        width: 50mm !important;
        height: 30mm !important;
    }
    @page {
        margin: 0mm !important;
    }
    body.print-mode-tube @page {
        size: 50mm 30mm !important;
    }
    body.print-mode-envelope @page {
        size: 100mm 60mm !important;
    }

    /* Hide all dashboard chrome and screen controls */
    .no-print,
    .main-header,
    .main-sidebar,
    .content-header,
    .main-footer,
    .card-header,
    .label-control-bar,
    .mode-tabs-container,
    .alert-info-banner,
    .modal,
    aside {
        display: none !important;
    }

    html, body, .content-wrapper, .content, .container-fluid, .card, .card-body {
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        width: auto !important;
        min-height: auto !important;
    }

    #printableArea {
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Tube Sticker Print Rules */
    .thermal-sticker-screen {
        width: 50mm !important;
        height: 30mm !important;
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 1.5mm 2mm !important;
        page-break-after: always !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        overflow: hidden !important;
    }
    .thermal-sticker-screen:last-child {
        page-break-after: avoid !important;
    }

    /* Envelope Print Rules */
    .envelope-label-screen {
        width: 100mm !important;
        max-width: 100mm !important;
        border: 1px solid #000 !important;
        box-shadow: none !important;
        padding: 3mm !important;
        margin: 0 !important;
        page-break-after: avoid !important;
    }
}
</style>

<div class="content-wrapper bg-light">
    <!-- Header -->
    <section class="content-header no-print">
        <div class="container-fluid">
            <div class="row my-2 pl-1 align-items-center">
                <div class="col-sm-6">
                    <h1 class="font-weight-bold">
                        <i class="fas fa-barcode text-success mr-2"></i>
                        <?php echo lang('label_print'); ?> (বারকোড ও লেবেল প্রিন্ট)
                    </h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="lab/testStatus" class="btn btn-outline-secondary btn-sm mr-2">
                        <i class="fa fa-arrow-left"></i> <?php echo lang('back_to_lab_tests'); ?>
                    </a>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#xpGuideModal">
                        <i class="fas fa-question-circle mr-1"></i> Xprinter XP-350BM Setup Guide
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Info Notification Banner -->
            <div class="alert alert-light border shadow-sm mb-3 no-print">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <span class="badge badge-primary mr-2" style="font-size: 0.9rem;">Invoice #<?php echo $lab->invoice_id; ?></span>
                        <span class="badge badge-secondary mr-2" style="font-size: 0.9rem;">Lab ID #<?php echo $lab->id; ?></span>
                        <strong class="text-dark ml-1"><?php echo strtoupper($patient->name); ?></strong>
                        <span class="text-muted ml-2">(PID: <?php echo $patient->id; ?> | <?php echo $gender_display; ?>, <?php echo $patient_age_compact; ?>)</span>
                    </div>
                    <div>
                        <span class="badge badge-info px-2 py-1">
                            <i class="fas fa-microscope mr-1"></i>
                            Specimen: <strong><?php echo $specimen_type; ?></strong>
                        </span>
                        <span class="badge badge-<?php echo $is_imaging ? 'warning' : 'success'; ?> px-2 py-1 ml-1">
                            <i class="fas fa-tag mr-1"></i>
                            Type: <strong><?php echo $is_imaging ? 'Radiology / Imaging' : 'Laboratory / Pathology'; ?></strong>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Controls & Preview -->
                <div class="col-lg-9 col-md-12">
                    <div class="card label-control-card">
                        <!-- Mode Tabs -->
                        <div class="card-header bg-white border-bottom py-3 no-print">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="btn-group mode-tabs-container mb-2 mb-md-0" role="group">
                                    <button type="button" class="btn mode-tab-btn <?php echo ($active_mode === 'tube') ? 'active' : ''; ?>" id="tabTubeBtn" onclick="switchMode('tube')">
                                        <i class="fas fa-vial mr-2"></i> 🧪 Specimen Barcode Label <span class="badge badge-light ml-1">XP-350BM (50×30mm)</span>
                                    </button>
                                    <button type="button" class="btn mode-tab-btn <?php echo ($active_mode === 'envelope') ? 'active' : ''; ?>" id="tabEnvelopeBtn" onclick="switchMode('envelope')">
                                        <i class="fas fa-envelope-open mr-2"></i> 📁 Radiology Film Envelope <span class="badge badge-light ml-1">USG & X-Ray</span>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-pill badge-success px-3 py-2" id="activeModeBadge">
                                        Active: <?php echo ($active_mode === 'tube') ? 'Tube Barcode (50×30mm)' : 'Film Envelope Label'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body: Live Preview & Action Toolbar -->
                        <div class="card-body p-4">
                            <!-- Action Control Toolbar -->
                            <div class="row align-items-center bg-light p-3 rounded mb-4 border no-print">
                                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                                    <button class="btn btn-success btn-lg btn-block font-weight-bold shadow-sm" onclick="triggerPrint()">
                                        <i class="fa fa-print mr-2"></i> Print Label
                                    </button>
                                </div>

                                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                                    <label class="small text-muted font-weight-bold mb-1 d-block">Number of Copies (কপি):</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-outline-secondary" type="button" onclick="changeCopies(-1)">-</button>
                                        </div>
                                        <input type="number" id="copiesInput" class="form-control text-center font-weight-bold" value="1" min="1" max="10" onchange="renderStickers()">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="changeCopies(1)">+</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                                    <label class="small text-muted font-weight-bold mb-1 d-block">Barcode Target:</label>
                                    <select id="barcodeTargetSelect" class="form-control form-control-sm" onchange="updateBarcodeType()">
                                        <option value="inv" selected>Invoice ID (#<?php echo $lab->invoice_id; ?>)</option>
                                        <option value="pid">Patient ID (#<?php echo $patient->id; ?>)</option>
                                        <option value="lab">Lab ID (#<?php echo $lab->id; ?>)</option>
                                    </select>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <label class="small text-muted font-weight-bold mb-1 d-block">Silent Print Auto:</label>
                                    <div class="custom-control custom-switch mt-1">
                                        <input type="checkbox" class="custom-control-input" id="autoSilentSwitch" onchange="toggleAutoSilent(this.checked)">
                                        <label class="custom-control-label small font-weight-bold" for="autoSilentSwitch">
                                            Auto Print on Open
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Preview Area -->
                            <div class="text-center mb-2 no-print">
                                <span class="badge badge-light border text-muted px-2 py-1">
                                    <i class="fa fa-eye mr-1"></i> Live Print Preview (প্রিন্ট প্রিভিউ)
                                </span>
                            </div>

                            <div class="label-preview-wrapper" id="previewContainer">
                                <!-- Printable Area (both screen preview & print target) -->
                                <div id="printableArea">
                                    <!-- Dynamic Container populated by JavaScript -->
                                    <div id="stickersMount"></div>
                                </div>
                            </div>

                            <!-- Additional Options Bar -->
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 text-muted small no-print">
                                <div>
                                    <i class="fas fa-info-circle mr-1 text-primary"></i>
                                    <span>রক্ত ও ইউরিন টেস্টের জন্য স্বয়ংক্রিয়ভাবে ৫০×৩০মিমি বারকোড স্টিকার নির্বাচন করা হয়েছে।</span>
                                </div>
                                <div>
                                    <button class="btn btn-link btn-sm text-secondary p-0" onclick="toggleRotation()">
                                        <i class="fas fa-redo mr-1"></i> <span id="rotateLabel">Rotate 90°</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Settings, Instructions & Hardware Notes -->
                <div class="col-lg-3 col-md-12 no-print">
                    <!-- Xprinter Hardware Profile -->
                    <div class="card shadow-sm border mb-3">
                        <div class="card-header bg-dark text-white py-2">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fas fa-print mr-2 text-warning"></i> Xprinter XP-350BM
                            </h6>
                        </div>
                        <div class="card-body p-3 font-size-sm">
                            <p class="mb-2"><strong>Device:</strong> Thermal Barcode Printer</p>
                            <p class="mb-2"><strong>DPI:</strong> 203 DPI (8 dots/mm)</p>
                            <p class="mb-2"><strong>Standard Roll:</strong> 50mm × 30mm</p>
                            <p class="mb-2"><strong>Specimen:</strong> Vacutainer / Tube / Cup</p>
                            <p class="mb-0"><strong>Status:</strong> <span class="text-success font-weight-bold">Ready</span></p>
                        </div>
                    </div>

                    <!-- Silent Print Quick Tips -->
                    <div class="card shadow-sm border mb-3">
                        <div class="card-header bg-success text-white py-2">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fas fa-bolt mr-2 text-warning"></i> Silent Print কীভাবে কাজ করে?
                            </h6>
                        </div>
                        <div class="card-body p-3 small">
                            <p class="mb-2">
                                <strong>১. Kiosk Printing:</strong> গুগল ক্রোম শর্টকাটে <code>--kiosk-printing</code> ফ্ল্যাগ যোগ করলে প্রিন্ট ডায়ালগ ছাড়াই সরাসরি প্রিন্ট হবে।
                            </p>
                            <p class="mb-2">
                                <strong>২. মার্জিন:</strong> প্রিন্ট পেপারে কোনো ব্রাউজার হেডার/ফুটার আসবে না।
                            </p>
                            <p class="mb-0">
                                <strong>৩. Auto Print:</strong> উপরের সুইচটি অন রাখলে পেজ লোড হলেই স্বয়ংক্রিয়ভাবে লেবেল প্রিন্টারে চলে যাবে।
                            </p>
                        </div>
                    </div>

                    <!-- Single Test Silent Iframe Trigger -->
                    <div class="card shadow-sm border">
                        <div class="card-body p-3 text-center">
                            <button class="btn btn-outline-success btn-sm btn-block mb-2 font-weight-bold" onclick="testSilentIframe()">
                                <i class="fas fa-bolt mr-1"></i> টেস্ট সাইলেন্ট প্রিন্ট
                            </button>
                            <small class="text-muted d-block">পেজ রিফ্রেশ ছাড়া ব্যাকগ্রাউন্ডে প্রিন্ট পাঠানোর পরীক্ষা</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ========================================================================= -->
<!-- XPRINTER XP-350BM & CHROME KIOSK PRINTING SETUP MODAL                     -->
<!-- ========================================================================= -->
<div class="modal fade" id="xpGuideModal" tabindex="-1" role="dialog" aria-labelledby="xpGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" id="xpGuideModalLabel">
                    <i class="fas fa-cogs mr-2 text-warning"></i> Xprinter XP-350BM সাইলেন্ট প্রিন্ট সেটআপ গাইড
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>এক ক্লিকে কোনো প্রিন্ট ডায়ালগ ছাড়া স্বয়ংক্রিয়ভাবে লেবেল প্রিন্ট করার নিয়ম:</strong>
                </div>

                <h6 class="font-weight-bold text-primary mt-3">ধাপ ১: উইন্ডোজ প্রিন্টার সেটিংস (XP-350BM Driver)</h6>
                <ol class="small">
                    <li>উইন্ডোজের <strong>Settings &gt; Devices &gt; Printers &amp; Scanners</strong> এ যান।</li>
                    <li><strong>Xprinter XP-350BM</strong> প্রিন্টারে ক্লিক করে <strong>Manage &gt; Printing Preferences</strong> সিলেক্ট করুন।</li>
                    <li><strong>Page Setup</strong> ট্যাবে নতুন পেপার সাইজ তৈরি করুন: <strong>Width: 50.0 mm, Height: 30.0 mm</strong>।</li>
                    <li><strong>Advanced Setup</strong> এ সেন্সর <strong>Black Mark / Die-cut (Gap)</strong> নির্বাচন করুন।</li>
                    <li>প্রিন্টারটিকে উইন্ডোজের <strong>Default Printer</strong> হিসেবে সেট করুন।</li>
                </ol>

                <h6 class="font-weight-bold text-primary mt-4">ধাপ ২: গুগল ক্রোমে Zero-Dialog Silent Print চালু করা</h6>
                <ol class="small">
                    <li>ডেস্কটপে থাকা <strong>Google Chrome</strong> শর্টকাটের ওপর রাইট ক্লিক করে <strong>Properties</strong> এ যান।</li>
                    <li><strong>Shortcut</strong> ট্যাবের <strong>Target</strong> বক্সের শেষে একটি স্পেস দিয়ে নিচের লেখাটি যোগ করুন:
                        <div class="bg-dark text-white p-2 rounded my-2 font-family-monospace user-select-all">
                            --kiosk-printing
                        </div>
                    </li>
                    <li>উদাহরণস্বরূপ Target হবে:<br>
                        <code>"C:\Program Files\Google\Chrome\Application\chrome.exe" --kiosk-printing</code>
                    </li>
                    <li><strong>Apply</strong> এবং <strong>OK</strong> দিন। এবার ক্রোম ব্রাউজার বন্ধ করে উক্ত শর্টকাট দিয়ে চালু করুন।</li>
                    <li><strong>ফলাফল:</strong> এখন থেকে যে কোনো বারকোড বা লেবেল প্রিন্ট বাটনে ক্লিক করলেই এক সেকেন্ডে সরাসরি Xprinter থেকে স্টিকার বের হয়ে আসবে!</li>
                </ol>

                <div class="bg-light p-3 rounded border mt-3 small">
                    <strong>💡 টিপস:</strong> আপনি যদি একাধিক টেস্টের জন্য ২ বা ৩টি টিউবে রক্ত নিয়ে থাকেন (যেমন: ১টি EDTA ও ১টি সিরাম টিউব), তাহলে উপরের <strong>Copies</strong> সংখ্যা ২ বা ৩ করে প্রিন্ট দিলে একই সাথে স্বয়ংক্রিয়ভাবে দুটি স্টিকার প্রিন্ট হয়ে যাবে।
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">বুঝেছি, ধন্যবাদ</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT LOGIC (Interactive Tabs, Multi-Copy, Barcode Swap, Auto-Print)-->
<!-- ========================================================================= -->
<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var labData = {
        invoiceId: "<?php echo $lab->invoice_id; ?>",
        labId: "<?php echo $lab->id; ?>",
        patientId: "<?php echo $patient->id; ?>",
        patientName: "<?php echo addslashes(strtoupper($patient->name)); ?>",
        patientAgeCompact: "<?php echo addslashes($patient_age_compact); ?>",
        patientAgeFull: "<?php echo addslashes($patient_age_full); ?>",
        genderShort: "<?php echo addslashes($gender_short); ?>",
        genderDisplay: "<?php echo addslashes($gender_display); ?>",
        specimenType: "<?php echo addslashes($specimen_type); ?>",
        testNames: "<?php echo addslashes($test_names_str); ?>",
        refDoctor: "<?php echo addslashes($ref_doctor_name); ?>",
        labDate: "<?php echo date('d/m/y', $lab->date); ?>",
        labDateFull: "<?php echo date('d/m/Y', $lab->date); ?>",
        barcodeUrlBase: "<?php echo site_url('lab/barcode'); ?>",
        barcodeInv: "<?php echo $barcode_inv; ?>",
        barcodePid: "<?php echo $barcode_pid; ?>",
        barcodeLab: "<?php echo $barcode_lab; ?>",
        defaultMode: "<?php echo $active_mode; ?>",
        isImaging: <?php echo $is_imaging ? 'true' : 'false'; ?>
    };

    var currentMode = labData.defaultMode;
    var currentBarcodeVal = labData.barcodeInv;
    var isRotated = <?php echo $auto_rotate ? 'true' : 'false'; ?>;

    // Render Stickers on DOM Ready
    $(document).ready(function () {
        // Load Auto Silent Print preference from localStorage
        var savedAutoSilent = localStorage.getItem('lifecare_auto_silent_print');
        if (savedAutoSilent === '1') {
            $('#autoSilentSwitch').prop('checked', true);
        }

        // Initial render
        renderStickers();

        // Check for Auto Print Trigger
        var urlParams = new URLSearchParams(window.location.search);
        var autoPrintParam = urlParams.get('autoprint');
        if (autoPrintParam === '1' || savedAutoSilent === '1') {
            setTimeout(function () {
                triggerPrint();
            }, 450);
        }
    });

    // Switch between Specimen Tube mode and Radiology Envelope mode
    function switchMode(mode) {
        currentMode = mode;
        if (mode === 'tube') {
            $('#tabTubeBtn').addClass('active');
            $('#tabEnvelopeBtn').removeClass('active');
            $('#activeModeBadge').text('Active: Tube Barcode (50×30mm)');
            $('body').removeClass('print-mode-envelope').addClass('print-mode-tube');
        } else {
            $('#tabEnvelopeBtn').addClass('active');
            $('#tabTubeBtn').removeClass('active');
            $('#activeModeBadge').text('Active: Film Envelope Label');
            $('body').removeClass('print-mode-tube').addClass('print-mode-envelope');
        }
        renderStickers();
    }

    // Change Copies Counter
    function changeCopies(delta) {
        var val = parseInt($('#copiesInput').val(), 10) || 1;
        val += delta;
        if (val < 1) val = 1;
        if (val > 10) val = 10;
        $('#copiesInput').val(val);
        renderStickers();
    }

    // Toggle 90 Degree Rotation
    function toggleRotation() {
        isRotated = !isRotated;
        $('.thermal-sticker-screen').toggleClass('rotated-90', isRotated);
        $('#rotateLabel').text(isRotated ? 'Original (0°)' : 'Rotate 90°');
    }

    // Update Barcode Type
    function updateBarcodeType() {
        var sel = $('#barcodeTargetSelect').val();
        if (sel === 'pid') {
            currentBarcodeVal = labData.barcodePid;
        } else if (sel === 'lab') {
            currentBarcodeVal = labData.barcodeLab;
        } else {
            currentBarcodeVal = labData.barcodeInv;
        }
        renderStickers();
    }

    // Toggle Auto Silent Print Preference
    function toggleAutoSilent(checked) {
        if (checked) {
            localStorage.setItem('lifecare_auto_silent_print', '1');
        } else {
            localStorage.removeItem('lifecare_auto_silent_print');
        }
    }

    // Render HTML stickers inside mount
    function renderStickers() {
        var copies = parseInt($('#copiesInput').val(), 10) || 1;
        var html = '';

        if (currentMode === 'tube') {
            // Render N copies of 50mm x 30mm Tube Barcode Label
            for (var i = 0; i < copies; i++) {
                html += `
                <div class="thermal-sticker-screen ${isRotated ? 'rotated-90' : ''}">
                    <!-- Row 1: Hospital Name & Date -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 0.8px solid #000; padding-bottom: 1px;">
                        <span style="font-size: 7pt; font-weight: 800; letter-spacing: 0.2px;">LIFE CARE DIAGNOSTIC</span>
                        <span style="font-size: 6.5pt; font-weight: 700;">${labData.labDate}</span>
                    </div>

                    <!-- Row 2: Patient Name & PID -->
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 1px;">
                        <span style="font-size: 8.5pt; font-weight: 900; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 32mm;">
                            ${labData.patientName}
                        </span>
                        <span style="font-size: 7.5pt; font-weight: 800;">
                            PID: ${labData.patientId}
                        </span>
                    </div>

                    <!-- Row 3: Age/Gender, Inv#, Specimen Type -->
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 6.5pt; font-weight: 700;">
                        <span>${labData.patientAgeCompact} / ${labData.genderShort}</span>
                        <span>Inv: #${labData.invoiceId}</span>
                        <span style="border: 0.8px solid #000; padding: 0.5px 3px; border-radius: 2px; font-size: 5.5pt; font-weight: 800; text-transform: uppercase;">
                            ${labData.specimenType}
                        </span>
                    </div>

                    <!-- Row 4: Barcode + Human Readable -->
                    <div style="text-align: center; margin: 1px 0;">
                        <img alt="Barcode" src="${labData.barcodeUrlBase}?text=${currentBarcodeVal}&size=24&sizefactor=1&print=false" style="height: 7.5mm; max-width: 44mm; display: block; margin: 0 auto; image-rendering: pixelated;" />
                        <span style="font-family: monospace; font-size: 6pt; font-weight: 800; letter-spacing: 1px;">*${currentBarcodeVal}*</span>
                    </div>

                    <!-- Row 5: Test Name(s) -->
                    <div style="font-size: 6pt; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; border-top: 0.8px solid #000; padding-top: 1px;">
                        <span>T:</span> ${labData.testNames}
                    </div>
                </div>`;
            }
        } else {
            // Render Envelope Label (USG / X-Ray)
            html += `
            <div class="envelope-label-screen">
                <div style="display: flex; justify-content: space-between; border-bottom: 1.5px solid #000; padding-bottom: 4px; margin-bottom: 8px;">
                    <div>
                        <h4 style="margin: 0; font-weight: 900; font-size: 13pt;">LIFE CARE DIAGNOSTIC CENTER</h4>
                        <small style="color: #475569;">Radiology &amp; Imaging Department</small>
                    </div>
                    <div style="text-align: right; font-weight: 700; font-size: 9pt;">
                        Date: ${labData.labDateFull}
                    </div>
                </div>

                <div style="display: flex; margin-bottom: 8px;">
                    <div style="flex: 1;">
                        <table style="width: 100%; font-size: 9pt; line-height: 1.6;">
                            <tr>
                                <td style="width: 50%;"><strong>PID:</strong> ${labData.patientId}</td>
                                <td style="width: 50%;"><strong>Invoice ID:</strong> ${labData.invoiceId}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Patient:</strong> ${labData.patientName}</td>
                            </tr>
                            <tr>
                                <td><strong>Age:</strong> ${labData.patientAgeFull}</td>
                                <td><strong>Gender:</strong> ${labData.genderDisplay}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Ref By:</strong> ${labData.refDoctor}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Test:</strong> ${labData.testNames}</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 8px;">
                                    <strong>Film:</strong> <br>
                                    <input type="text" style="width: 85%; border: 1px solid #000; padding: 2px 4px;">
                                </td>
                                <td style="padding-top: 8px;">
                                    <strong>Paper Print:</strong> <br>
                                    <input type="text" style="width: 85%; border: 1px solid #000; padding: 2px 4px;">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="width: 32mm; text-align: center; display: flex; align-items: center; justify-content: center;">
                        <img alt="Vertical Barcode" src="${labData.barcodeUrlBase}?text=${currentBarcodeVal}&print=true&orientation=vertical" style="max-height: 48mm;" />
                    </div>
                </div>

                <div style="text-align: center; border-top: 1px dashed #000; padding-top: 6px; margin-top: 4px;">
                    <img alt="Barcode" src="${labData.barcodeUrlBase}?text=${currentBarcodeVal}&print=true" style="height: 10mm; display: block; margin: 0 auto;" />
                </div>
            </div>`;
        }

        $('#stickersMount').html(html);
    }

    // Trigger Print
    function triggerPrint() {
        if (currentMode === 'tube') {
            $('body').removeClass('print-mode-envelope').addClass('print-mode-tube');
        } else {
            $('body').removeClass('print-mode-tube').addClass('print-mode-envelope');
        }
        window.print();
    }

    // Test Silent Iframe Print
    function testSilentIframe() {
        if (typeof window.silentPrintBarcode === 'function') {
            window.silentPrintBarcode(labData.labId);
        } else {
            alert('Iframe trigger sent for Lab #' + labData.labId);
        }
    }
</script>