<?php
$patient = !empty($lab->patient) ? $this->db->get_where('patient', array('id' => $lab->patient))->row() : null;
$invoice_details = !empty($lab->invoice_id) ? $this->db->get_where('payment', array('id' => $lab->invoice_id))->row() : null;

// Billing ID & Patient ID
$billing_id = !empty($lab->invoice_id) ? $lab->invoice_id : (!empty($invoice_details->id) ? $invoice_details->id : '-');
$patient_id = !empty($patient->patient_id) ? $patient->patient_id : (!empty($patient->id) ? $patient->id : (!empty($lab->patient) ? $lab->patient : '-'));
$id_no = $billing_id;

// Patient Name
$patient_name = !empty($patient->name) ? strtoupper($patient->name) : (!empty($lab->patient_name) ? strtoupper($lab->patient_name) : '-');

// Ref. By / Doctor
$ref_by = 'SELF';
$doctor_obj = null;
if (!empty($lab->doctor)) {
    $doctor_obj = $this->db->get_where('doctor', array('id' => $lab->doctor))->row();
} elseif (!empty($invoice_details->doctor)) {
    $doctor_obj = $this->db->get_where('doctor', array('id' => $invoice_details->doctor))->row();
}
if (!empty($doctor_obj->name)) {
    $ref_by = $doctor_obj->name;
} elseif (!empty($lab->doctor_name)) {
    $ref_by = $lab->doctor_name;
} elseif (!empty($invoice_details->doctor_name)) {
    $ref_by = $invoice_details->doctor_name;
}

// Specimen Detection
$specimen = 'Blood';
$cat_name = !empty($lab->category_name) ? strtolower($lab->category_name) : '';
if (strpos($cat_name, 'urine') !== false) {
    $specimen = 'Urine';
} elseif (strpos($cat_name, 'stool') !== false) {
    $specimen = 'Stool';
} elseif (strpos($cat_name, 'semen') !== false) {
    $specimen = 'Semen';
} elseif (strpos($cat_name, 'sputum') !== false) {
    $specimen = 'Sputum';
} elseif (strpos($cat_name, 'swab') !== false) {
    $specimen = 'Swab';
} elseif (strpos($cat_name, 'x-ray') !== false || strpos($cat_name, 'usg') !== false || strpos($cat_name, 'ecg') !== false || strpos($cat_name, 'echo') !== false) {
    $specimen = 'N/A';
}

// Date
$report_date = !empty($lab->date) ? (is_numeric($lab->date) ? date('d/m/Y', $lab->date) : date('d/m/Y', strtotime($lab->date))) : date('d/m/Y');

// Age
$patient_age = '-';
if (!empty($patient->age)) {
    $age_parts = explode('-', $patient->age);
    $patient_age = trim($age_parts[0]) . ' Y';
} elseif (!empty($patient->birthdate)) {
    $diff = date_diff(date_create($patient->birthdate), date_create(date("Y-m-d")));
    $patient_age = $diff->format('%y Y');
}

// Sex
$patient_sex = !empty($patient->sex) ? ucfirst($patient->sex) : (!empty($patient->gender) ? ucfirst($patient->gender) : '-');

// Hospital Logo
$logo_path = 'uploads/lifecare_logo_circle.png';
if (!file_exists($logo_path) && !empty($settings->logo) && file_exists($settings->logo)) {
    $logo_path = $settings->logo;
}
?>

<div class="content-wrapper">
    <!-- Top Action Bar (Screen Only) -->
    <div class="report-action-bar no-print py-3 px-4 bg-white border-bottom shadow-sm d-flex justify-content-between align-items-center">
        <div>
            <a href="lab" class="btn btn-outline-secondary btn-sm mr-2 font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> <?php echo lang('back_to_lab_module'); ?>
            </a>
            <span class="badge badge-light border text-dark font-weight-bold p-2">
                Report ID: #<?php echo $lab->id; ?> | Invoice: #<?php echo !empty($lab->invoice_id) ? $lab->invoice_id : 'N/A'; ?>
            </span>
        </div>
        <div>
            <?php if ($this->ion_auth->in_group(array('admin', 'Laboratorist'))) { ?>
                <a href="lab?id=<?php echo $lab->id; ?>" class="btn btn-outline-info btn-sm mr-2 font-weight-bold">
                    <i class="fas fa-edit mr-1"></i> Edit Report
                </a>
            <?php } ?>
            <?php if (empty($redirect) || $redirect != 'download1') { ?>
                <a href="<?php echo site_url('lab/testPdf?id=' . $lab->id); ?>" class="btn btn-outline-success btn-sm mr-2 font-weight-bold">
                    <i class="fas fa-file-pdf mr-1"></i> Download PDF
                </a>
            <?php } ?>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm font-weight-bold" onclick="window.print()">
                <i class="fas fa-print mr-1"></i> Print Report (A4)
            </button>
        </div>
    </div>

    <!-- Main Report Canvas Container -->
    <section class="content py-4">
        <div class="container-fluid d-flex justify-content-center">
            
            <!-- A4 Clinical Laboratory Report Sheet -->
            <div class="lab-report-sheet" id="printable-report">
                
                <!-- Background LCDC Emblem Watermark -->
                <div class="report-watermark" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 260 260" width="340" height="340">
                        <circle cx="130" cy="130" r="120" fill="none" stroke="#b91c1c" stroke-width="2.5"/>
                        <circle cx="130" cy="130" r="112" fill="none" stroke="#b91c1c" stroke-width="1.2"/>
                        <circle cx="130" cy="130" r="82" fill="none" stroke="#b91c1c" stroke-width="1.2"/>
                        
                        <path id="lcdcWatermarkTop" d="M 30,130 A 100,100 0 0,1 230,130" fill="none"/>
                        <text font-family="Arial, Helvetica, sans-serif" font-size="11" font-weight="bold" fill="#b91c1c" letter-spacing="2">
                            <textPath href="#lcdcWatermarkTop" startOffset="50%" text-anchor="middle">LIFE CARE DIAGNOSTIC CENTER</textPath>
                        </text>

                        <path id="lcdcWatermarkBottom" d="M 230,130 A 100,100 0 0,1 30,130" fill="none"/>
                        <text font-family="Arial, Helvetica, sans-serif" font-size="10.5" font-weight="bold" fill="#b91c1c" letter-spacing="2">
                            <textPath href="#lcdcWatermarkBottom" startOffset="50%" text-anchor="middle">★ KHASERHAT ★ NOAKHALI ★</textPath>
                        </text>

                        <rect x="122" y="96" width="16" height="68" fill="#b91c1c" rx="2"/>
                        <rect x="96" y="122" width="68" height="16" fill="#b91c1c" rx="2"/>
                        <text x="130" y="90" font-family="Arial, Helvetica, sans-serif" font-size="16" font-weight="900" fill="#b91c1c" text-anchor="middle">L C D C</text>
                    </svg>
                </div>

                <!-- 1. HEADER SECTION -->
                <header class="report-header">
                    <!-- Top-Left Green Ribbon Accent -->
                    <div class="header-green-ribbon"></div>

                    <!-- Header Grid -->
                    <div class="header-grid">
                        <!-- Left: Logo & Hospital Names -->
                        <div class="header-left">
                            <div class="brand-row">
                                <div class="logo-box">
                                    <img src="<?php echo $logo_path; ?>" alt="Life Care Logo" class="center-logo">
                                </div>
                                <div class="brand-texts">
                                    <div class="brand-tagline">বিশ্বাস ও আস্থার সাথে, সব সময় আপনার পাশে</div>
                                    <h1 class="brand-bangla">লাইফ কেয়ার ডায়াগনস্টিক সেন্টার</h1>
                                    <h2 class="brand-english">LIFE CARE DIAGNOSTIC CENTER</h2>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Address, Phone, Email -->
                        <div class="header-right">
                            <div class="contact-line">
                                <i class="fas fa-map-marker-alt text-danger contact-icon"></i>
                                <span>খাসেরহাট রাস্তার মাথা, চরবাটা, সুবর্ণচর, নোয়াখালী</span>
                            </div>
                            <div class="contact-line">
                                <i class="fas fa-phone-alt text-primary contact-icon"></i>
                                <span>01829 96 25 55, 01727 69 69 77</span>
                            </div>
                            <div class="contact-line">
                                <i class="fas fa-envelope text-info contact-icon"></i>
                                <span>lifecare5boy@gmail.com</span>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- 2. PATIENT INFORMATION BOX -->
                <div class="patient-info-box">
                    <table class="patient-info-tbl">
                        <tr>
                            <td class="lbl">Billing ID</td>
                            <td class="sep">:</td>
                            <td class="val"><strong><?php echo html_escape($billing_id); ?></strong></td>
                            
                            <td class="lbl">Date</td>
                            <td class="sep">:</td>
                            <td class="val"><?php echo html_escape($report_date); ?></td>
                        </tr>
                        <tr>
                            <td class="lbl">Patient's Name</td>
                            <td class="sep">:</td>
                            <td class="val"><strong><?php echo html_escape($patient_name); ?></strong></td>

                            <td class="lbl">Age</td>
                            <td class="sep">:</td>
                            <td class="val"><?php echo html_escape($patient_age); ?></td>
                        </tr>
                        <tr>
                            <td class="lbl">Ref. By</td>
                            <td class="sep">:</td>
                            <td class="val"><strong><?php echo html_escape($ref_by); ?></strong></td>

                            <td class="lbl">Sex</td>
                            <td class="sep">:</td>
                            <td class="val"><?php echo html_escape($patient_sex); ?></td>
                        </tr>
                        <tr>
                            <td class="lbl">Specimen</td>
                            <td class="sep">:</td>
                            <td class="val"><strong><?php echo html_escape($specimen); ?></strong></td>

                            <td class="lbl">Patient ID</td>
                            <td class="sep">:</td>
                            <td class="val"><strong><?php echo html_escape($patient_id); ?></strong></td>
                        </tr>
                    </table>
                </div>

                <!-- 3. REPORT BODY / CLINICAL TEST CONTENT -->
                <main class="report-content-body">
                    <?php echo $lab->report; ?>
                </main>

                <!-- 4. SIGNATURE SECTION -->
                <footer class="report-signatures-section">
                    <!-- Left: Doctor Signature Block -->
                    <div class="sig-block sig-left">
                        <div class="sig-title font-weight-bold">DR.MD.ABU SUFIAN</div>
                        <div class="sig-sub">MBBS.CMU (ULTRA).</div>
                        <div class="sig-role">MEDICAL OFFICER</div>
                        <div class="sig-hosp">LIFE CARE DIAGNOSTIC CENTER</div>
                    </div>

                    <!-- Right: Medical Technologist Signature Block -->
                    <div class="sig-block sig-right">
                        <div class="sig-title font-weight-bold">RASEL AHAMMED</div>
                        <div class="sig-sub">Medical Technologist (Lab)</div>
                        <div class="sig-role">Diploma In LabratoryMedicine(OMI)</div>
                        <div class="sig-hosp">Life Care Diagnostic Center</div>
                    </div>
                </footer>

                <!-- 5. BOTTOM SOLID GREEN BAR -->
                <div class="report-bottom-green-bar"></div>

            </div>
        </div>
    </section>
</div>

<!-- STYLING: Pixel-Perfect A4 Lab Report Sheet -->
<style>
/* Base Styles */
.lab-report-sheet {
    width: 210mm;
    min-height: 297mm;
    background: #ffffff;
    margin: 0 auto;
    padding: 12mm 15mm 10mm 15mm;
    box-shadow: 0 4px 25px rgba(0,0,0,0.12);
    position: relative;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
    color: #111;
    display: flex;
    flex-direction: column;
}

/* Watermark (Lower Right Quadrant matching physical report) */
.report-watermark {
    position: absolute;
    bottom: 22%;
    right: 14%;
    opacity: 0.08;
    pointer-events: none;
    z-index: 0;
}

/* Header */
.report-header {
    position: relative;
    z-index: 1;
    margin-bottom: 6px;
}

.header-green-ribbon {
    position: absolute;
    top: -12mm;
    left: -15mm;
    width: 28mm;
    height: 28mm;
    background: #16a34a;
    clip-path: polygon(0 0, 100% 0, 0 100%);
    z-index: 2;
}

.header-grid {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1.5px solid #222;
    padding-bottom: 8px;
}

.header-left .brand-row {
    display: flex;
    align-items: center;
}

.logo-box {
    margin-right: 12px;
}

.center-logo {
    width: 62px;
    height: 62px;
    object-fit: contain;
    border-radius: 50%;
}

.brand-tagline {
    font-size: 11px;
    color: #1e3a8a;
    font-style: italic;
    font-weight: 600;
    margin-bottom: 1px;
}

.brand-bangla {
    font-size: 21px;
    font-weight: 800;
    color: #c5221f;
    margin: 0;
    line-height: 1.15;
    letter-spacing: 0.2px;
}

.brand-english {
    font-size: 14.5px;
    font-weight: 900;
    color: #0b2545;
    margin: 1px 0 0 0;
    letter-spacing: 0.8px;
}

.header-right {
    font-size: 10.5px;
    color: #222;
    line-height: 1.5;
    text-align: left;
    max-width: 250px;
}

.contact-line {
    display: flex;
    align-items: center;
    margin-bottom: 2px;
}

.contact-icon {
    width: 14px;
    margin-right: 6px;
    font-size: 11px;
}

/* Patient Box */
.patient-info-box {
    position: relative;
    z-index: 1;
    border: 1.5px solid #222;
    border-radius: 4px;
    padding: 6px 10px;
    margin: 8px 0 10px 0;
    background: #ffffff;
}

.patient-info-tbl {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
    color: #111;
}

.patient-info-tbl td {
    padding: 2px 2px;
    vertical-align: middle;
}

.patient-info-tbl .lbl {
    font-weight: 700;
    width: 90px;
    color: #222;
}

.patient-info-tbl .sep {
    width: 12px;
    text-align: center;
    font-weight: 700;
}

.patient-info-tbl .val {
    width: 38%;
}

/* Report Content Body */
.report-content-body {
    position: relative;
    z-index: 1;
    flex: 1;
    margin: 4px 0 12px 0;
}

/* Signatures */
.report-signatures-section {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: auto;
    padding-top: 15px;
    font-size: 11px;
    line-height: 1.35;
}

.sig-block {
    width: 44%;
}

.sig-left {
    text-align: left;
}

.sig-right {
    text-align: right;
}

.sig-title {
    font-size: 12px;
    color: #000;
    letter-spacing: 0.3px;
}

.sig-sub, .sig-role, .sig-hosp {
    color: #222;
    font-size: 10.5px;
}

/* Bottom Green Bar */
.report-bottom-green-bar {
    position: relative;
    z-index: 1;
    height: 10px;
    background: #16a34a;
    margin-top: 10px;
    border-radius: 0 0 2px 2px;
}

/* Print Overrides */
@media print {
    body {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .no-print,
    .main-header,
    .main-sidebar,
    .main-footer,
    .control-sidebar,
    .report-action-bar,
    .breadcrumb,
    nav {
        display: none !important;
    }

    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        border: none !important;
    }

    .content {
        padding: 0 !important;
        margin: 0 !important;
    }

    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
        display: block !important;
    }

    .lab-report-sheet {
        width: 100% !important;
        max-width: 100% !important;
        min-height: auto !important;
        margin: 0 !important;
        padding: 6mm 10mm 6mm 10mm !important;
        box-shadow: none !important;
        border: none !important;
        box-sizing: border-box !important;
    }

    .header-green-ribbon {
        top: -6mm !important;
        left: -10mm !important;
    }

    @page {
        size: A4 portrait;
        margin: 6mm;
    }
}
</style>