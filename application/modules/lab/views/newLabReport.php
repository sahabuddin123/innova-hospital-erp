<?php
$patient = $this->db->get_where('patient', array('id' => $lab->patient))->row();
$invoice_details = "";
$invoice_details = $this->db->get_where('payment', array('id' => $lab->invoice_id))->row();
?>

<div class="content-wrapper">
    <section class="content py-2">
        <div class="container-fluid">
            <div class="document-wrapper">
                <!-- Document Header -->
                <div class="document-header mb-2">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="document-title mb-1">LABORATORY REPORT #<?php echo $lab->id; ?></h3>
                            <p class="document-subtitle mb-0"><?php echo $patient->name; ?> | ID: <?php echo $patient->id; ?> | Invoice: <?php echo $invoice_details->id; ?></p>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="document-actions">
                                <button type="button" class="btn btn-outline-primary btn-sm print-btn" onclick="printDocument()">
                                    <i class="fas fa-print mr-1"></i>
                                    Print Report
                                </button>
                                <?php if (empty($redirect) || $redirect != 'download1') { ?>
                                    <a href="<?php echo site_url('lab/testPdf?id=' . $lab->id); ?>" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-download mr-1"></i>
                                        Download PDF
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Content -->
                <div class="document-content">
                    <!-- Hospital Information -->
                    <div class="document-section mb-2">
                        <h5 class="section-title">HOSPITAL INFORMATION</h5>
                        <div class="section-content">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="info-row">
                                        <span class="info-label">Hospital:</span>
                                        <span class="info-value"><?php echo $settings->title; ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Address:</span>
                                        <span class="info-value"><?php echo $settings->address; ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Phone:</span>
                                        <span class="info-value"><?php echo $settings->phone; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <img src="<?php echo site_url($this->settings_model->getSettings()->logo); ?>" alt="Hospital Logo" class="hospital-logo" style="max-height: 60px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Information -->
                    <div class="document-section mb-2">
                        <h5 class="section-title">PATIENT INFORMATION</h5>
                        <div class="section-content">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label">Name:</span>
                                        <span class="info-value"><?php echo $patient->name; ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Patient ID:</span>
                                        <span class="info-value"><?php echo $patient->id; ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Phone:</span>
                                        <span class="info-value"><?php echo $patient->phone; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label">Age:</span>
                                        <span class="info-value">
                                            <?php
                                            $age = explode('-', $patient->age);
                                            if (count($age) == 3) {
                                                echo $age[0] . " Y " . $age[1] . " M " . $age[2] . " D";
                                            } else {
                                                echo $patient->age;
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Gender:</span>
                                        <span class="info-value"><?php echo $patient->sex; ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Visit ID:</span>
                                        <span class="info-value"><?php echo $lab->invoice_id; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Information -->
                    <div class="document-section mb-2">
                        <h5 class="section-title">REFERRING PHYSICIAN</h5>
                        <div class="section-content">
                            <?php
                            $doctor_details = "";
                            if ($invoice_details) {
                                if ($invoice_details->doctor) {
                                    $doctor_details = $this->db->get_where('doctor', array('id' => $invoice_details->doctor))->row();
                                }
                            }
                            ?>
                            <div class="info-row">
                                <span class="info-label">Doctor:</span>
                                <span class="info-value">
                                    <?php if ($doctor_details) { ?>
                                        <?php echo $doctor_details->name; ?>
                                    <?php } else { ?>
                                        Not specified
                                    <?php } ?>
                                </span>
                            </div>
                            <?php if ($doctor_details && $doctor_details->profile) { ?>
                                <div class="info-row">
                                    <span class="info-label">Specialization:</span>
                                    <span class="info-value"><?php echo $doctor_details->profile; ?></span>
                                </div>
                            <?php } ?>
                            <div class="info-row">
                                <span class="info-label">Visit Date:</span>
                                <span class="info-value">
                                    <?php
                                    if ($invoice_details) {
                                        echo date('M d, Y h:i A', $invoice_details->date);
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Lab Report Content -->
                    <div class="document-section mb-2">
                        <h5 class="section-title">LABORATORY RESULTS</h5>
                        <div class="section-content">
                            <div class="report-content">
                                <?php echo $lab->report; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Report Footer -->
                    <div class="document-section mb-2">
                        <h5 class="section-title">REPORT DETAILS</h5>
                        <div class="section-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-row">
                                        <span class="info-label">Done By:</span>
                                        <span class="info-value"><?php echo $lab->done_by; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-row">
                                        <span class="info-label">Test Date:</span>
                                        <span class="info-value">
                                            <?php echo $lab->test_status_date != null ? date('M d, Y h:i A', $lab->test_status_date) : "Not completed"; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-row">
                                        <span class="info-label">Last Updated:</span>
                                        <span class="info-value">
                                            <?php
                                            if ($lab->updated_on) {
                                                echo date('M d, Y h:i A', $lab->updated_on);
                                            } else {
                                                echo "Not updated";
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Document Wrapper */
.document-wrapper {
    margin: 0 2rem;
    padding: 1.5rem;
    background: #fff;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Document Header */
.document-header {
    border-bottom: 2px solid #000;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.document-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #000;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.document-subtitle {
    font-size: 0.9rem;
    color: #666;
    margin: 0;
}

/* Section Styling */
.document-section {
    border-bottom: 1px solid #ddd;
    padding-bottom: 0.75rem;
    margin-bottom: 0.75rem;
}

.section-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #000;
    text-transform: uppercase;
    border-bottom: 1px solid #ccc;
    padding-bottom: 0.25rem;
    margin-bottom: 0.5rem;
}

/* Information Display */
.info-row {
    display: flex;
    margin-bottom: 0.25rem;
    font-size: 0.85rem;
    line-height: 1.3;
}

.info-label {
    font-weight: 600;
    color: #333;
    min-width: 120px;
    margin-right: 0.5rem;
    flex-shrink: 0;
}

.info-value {
    color: #000;
    flex: 1;
    word-wrap: break-word;
}

/* Report Content */
.report-content {
    font-size: 0.9rem;
    line-height: 1.4;
    color: #000;
}

/* Print Button */
.print-btn {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
}

/* Hospital Logo */
.hospital-logo {
    max-width: 100%;
    height: auto;
}

/* Responsive Design */
@media (max-width: 768px) {
    .document-wrapper {
        margin: 0 1rem;
        padding: 1rem;
    }
    
    .document-title {
        font-size: 1.2rem;
    }
    
    .info-label {
        min-width: 100px;
    }
}

@media (max-width: 576px) {
    .document-wrapper {
        margin: 0 0.5rem;
        padding: 0.75rem;
    }
    
    .document-title {
        font-size: 1rem;
    }
    
    .info-row {
        font-size: 0.8rem;
    }
}

/* Print Styles */
@media print {
    .document-wrapper {
        margin: 0;
        padding: 0;
        border: none;
        box-shadow: none;
    }
    
    .document-actions {
        display: none;
    }
    
    .document-header {
        border-bottom: 2px solid #000;
    }
    
    .document-section {
        page-break-inside: avoid;
    }
    
    .section-title {
        border-bottom: 1px solid #000;
    }
}
</style>

<script>
function printDocument() {
    // Hide non-printable elements
    const elementsToHide = document.querySelectorAll('.document-actions, .no-print');
    elementsToHide.forEach(el => el.style.display = 'none');
    
    // Print the document
    window.print();
    
    // Restore elements after printing
    elementsToHide.forEach(el => el.style.display = '');
}
</script>





<!--main content end-->
<!--footer start-->



<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var select_doctor = "<?php echo lang('select_doctor'); ?>";
</script>
<script type="text/javascript">
    var select_email = "<?php echo lang('select_email'); ?>";
</script>
<script src="common/extranal/js/lab/lab.js"></script>

<script>
    $(document).ready(function() {
        var prevRowHeight = 0;
        $("p, tr, img").each(function() {
            console.log(prevRowHeight);
            var maxHeight = 750;
            var eachRowHeight = $(this).height();
            if ((prevRowHeight + eachRowHeight) > maxHeight) {
                prevRowHeight = 0;
                $(this).before('<div class="page_breaker"></div>');
                console.log("add page break before");
            }
            prevRowHeight = prevRowHeight + $(this).height();
        });

    });
</script>