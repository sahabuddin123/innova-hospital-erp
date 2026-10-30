<?php if ($redirect == 'download') { ?>
    <!DOCTYPE html>
    <html lang="en" <?php if ($this->db->get('settings')->row()->language == 'arabic') { ?> dir="rtl" <?php } ?>>
        <link href="common/css/bootstrap.min.css" rel="stylesheet">
        <link href="common/css/bootstrap-reset.css" rel="stylesheet">
        <link href="common/assets/fontawesome5pro/css/all.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="common/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />
        <style>
            @import url('https://fonts.googleapis.com/css?family=Ubuntu&display=swap');
        </style>
        <link href="common/assets/DataTables/datatables.css" rel="stylesheet" />
        <link href="common/extranal/css/finance/downloadInvoice.css" rel="stylesheet" />
    <?php } ?>

    <link href="common/extranal/css/finance/invoice-all.css" rel="stylesheet" />
    <?php if ($redirect != 'download') { ?>
        <link href="common/extranal/css/finance/invoice_logical.css" rel="stylesheet" />
        <section id="main-content">
            <section class="wrapper site-min-height">
            <?php } ?>
            <!-- invoice start-->
            <?php if ($redirect != 'download') { ?>
                <section class="col-md-8">
                <?php } else { ?>
                    <section class="col-md-12">
                    <?php } ?>
                    <div class="document-wrapper">
                        <!-- Document Header -->
                        <div class="document-header mb-2">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="document-title mb-1">INVOICE #<?php echo $payment->id; ?></h3>
                                    <p class="document-subtitle mb-0"><?php echo lang('payment') ?> <?php echo lang('invoice') ?> | <?php echo date('M d, Y H:i', $payment->date); ?></p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="document-actions">
                                        <button type="button" class="btn btn-outline-primary btn-sm print-btn" onclick="printDocument()">
                                            <i class="fas fa-print mr-1"></i>
                                            Print Invoice
                                        </button>
                                        <?php if ($redirect != 'download') { ?>
                                            <a href="finance/download?id=<?php echo $payment->id; ?>" class="btn btn-outline-success btn-sm">
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
                                            <img src="<?php echo $this->settings_model->getSettings()->logo; ?>" alt="Hospital Logo" class="hospital-logo" style="max-height: 60px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Patient Information -->
                            <div class="document-section mb-2">
                                <h5 class="section-title">PATIENT INFORMATION</h5>
                                <div class="section-content">
                                    <?php $patient_info = $this->db->get_where('patient', array('id' => $payment->patient))->row(); ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-row">
                                                <span class="info-label">Name:</span>
                                                <span class="info-value">
                                                    <?php
                                                    if (!empty($patient_info)) {
                                                        echo $patient_info->name;
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Patient ID:</span>
                                                <span class="info-value">
                                                    <?php
                                                    if (!empty($patient_info)) {
                                                        echo $patient_info->id;
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Phone:</span>
                                                <span class="info-value">
                                                    <?php
                                                    if (!empty($patient_info)) {
                                                        echo $patient_info->phone;
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-row">
                                                <span class="info-label">Address:</span>
                                                <span class="info-value">
                                                    <?php
                                                    if (!empty($patient_info)) {
                                                        echo $patient_info->address;
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Age:</span>
                                                <span class="info-value">
                                                    <?php
                                                    if (!empty($patient_info)) {
                                                        $age = explode('-', $patient_info->age);
                                                        echo $age[0] . ' ' . lang('years');
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Invoice Date:</span>
                                                <span class="info-value">
                                                    <?php
                                                    if (!empty($payment->date)) {
                                                        echo date('M d, Y H:i', $payment->date);
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Doctor Information -->
                            <div class="document-section mb-2">
                                <h5 class="section-title">REFERRING PHYSICIAN</h5>
                                <div class="section-content">
                                    <div class="info-row">
                                        <span class="info-label">Doctor:</span>
                                        <span class="info-value">
                                            <?php
                                            if (!empty($payment->doctor)) {
                                                $doc_details = $this->doctor_model->getDoctorById($payment->doctor);
                                                if (!empty($doc_details)) {
                                                    echo $doc_details->name;
                                                } else {
                                                    echo $payment->doctor_name;
                                                }
                                            } else {
                                                echo "Not specified";
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Services/Items Table -->
                            <div class="document-section mb-2">
                                <h5 class="section-title">SERVICES & CHARGES</h5>
                                <div class="section-content">
                                    <div class="table-responsive">
                                        <table class="table table-bordered invoice-table">
                                            <thead>
                                                <tr>
                                                    <?php if ($payment->payment_from == 'admitted_patient_bed_service') { ?>
                                                        <th>#</th>
                                                        <th><?php echo lang('service'); ?> <?php echo lang('name'); ?></th>
                                                        <th><?php echo lang('unit'); ?> <?php echo lang('price') ?></th>
                                                        <th><?php echo lang('quantity'); ?></th>
                                                        <th><?php echo lang('amount'); ?></th>
                                                    <?php } elseif ($payment->payment_from == 'admitted_patient_bed_medicine' ) { ?>
                                                        <th>#</th>
                                                        <th><?php echo lang('medicine'); ?> <?php echo lang('name'); ?></th>
                                                        <th><?php echo lang('unit'); ?> <?php echo lang('price') ?></th>
                                                        <th><?php echo lang('quantity'); ?></th>
                                                        <th><?php echo lang('amount'); ?></th>
                                                    <?php } elseif ($payment->payment_from == 'payment' ) { ?>
                                                        <th>#</th>
                                                        <th><?php echo lang('description'); ?></th>
                                                        <th><?php echo lang('service_point'); ?></th>
                                                        <th><?php echo lang('unit_price'); ?></th>
                                                        <th><?php echo lang('qty'); ?></th>
                                                        <th><?php echo lang('amount'); ?></th>
                                                    <?php } else { ?>
                                                        <th>#</th>
                                                        <th><?php echo lang('code'); ?></th>
                                                        <th><?php echo lang('description'); ?></th>
                                                        <th><?php echo lang('unit_price'); ?></th>
                                                        <th><?php echo lang('qty'); ?></th>
                                                        <th><?php echo lang('amount'); ?></th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($payment->payment_from == 'admitted_patient_bed_medicine') {
                                                    if (!empty($payment->category_name)) {
                                                        $category = explode('#', $payment->category_name);
                                                        $i = 0;
                                                        foreach ($category as $cat) {
                                                            $i = $i + 1;
                                                            $cat_new = array();
                                                            $cat_new = explode('*', $cat);
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $i; ?> </td>
                                                                <td><?php echo $cat_new[1]; ?> </td>
                                                                <td class="text-right"><?php echo $settings->currency; ?> <?php echo $cat_new[2]; ?> </td>
                                                                <td class="text-center"> <?php echo $cat_new[3]; ?> </td>
                                                                <td class="text-right"><?php echo $settings->currency; ?> <?php echo $cat_new[4]; ?> </td>
                                                            </tr> 
                                                            <?php
                                                        }
                                                    }
                                                } elseif ($payment->payment_from == 'admitted_patient_bed_service') {
                                                    if (!empty($payment->category_name)) {
                                                        $category = explode('#', $payment->category_name);
                                                        $i = 0;
                                                        foreach ($category as $cat) {
                                                            $i = $i + 1;
                                                            $cat_new = array();
                                                            $cat_new = explode('*', $cat);
                                                            $service = $this->db->get_where('pservice', array('id' => $cat_new[0]))->row();
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $i; ?> </td>
                                                                <td>  <?php echo $service->name; ?> </td>
                                                                <td class="text-right"><?php echo $settings->currency; ?> <?php echo $cat_new[1]; ?> </td>
                                                                <td class="text-center"> <?php echo '1'; ?> </td>
                                                                <td class="text-right"><?php echo $settings->currency; ?> <?php echo $cat_new[1]; ?> </td>
                                                            </tr> 
                                                            <?php
                                                        }
                                                    }
                                                } else {
                                                    if (!empty($payment->category_name)) {
                                                        $category_name = $payment->category_name;
                                                        $category_name1 = explode(',', $category_name);
                                                        $i = 0;
                                                        foreach ($category_name1 as $category_name2) {
                                                            $i = $i + 1;
                                                            $category_name3 = explode('*', $category_name2);
                                                            if ($category_name3[3] > 0) {
                                                                ?>  
                                                                <tr>
                                                                    <td><?php echo $i; ?> </td>
                                                                    <td><?php echo $this->finance_model->getPaymentcategoryById($category_name3[0])->category; ?> </td>
                                                                    <td><?php echo $this->finance_model->getPaymentcategoryById($category_name3[0])->description; ?> </td>
                                                                    <td class="text-right"><?php echo $settings->currency; ?> <?php echo $category_name3[1]; ?> </td>
                                                                    <td class="text-center"> <?php echo $category_name3[3]; ?> </td>
                                                                    <td class="text-right"><?php echo $settings->currency; ?> <?php echo $category_name3[1] * $category_name3[3]; ?> </td>
                                                                </tr> 
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Summary -->
                            <div class="document-section mb-2">
                                <h5 class="section-title">PAYMENT SUMMARY</h5>
                                <div class="section-content">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="info-row">
                                                <span class="info-label">Sub Total:</span>
                                                <span class="info-value"><?php echo $settings->currency; ?> <?php echo $payment->amount; ?></span>
                                            </div>
                                            <?php if (!empty($payment->discount)) { ?>
                                                <div class="info-row">
                                                    <span class="info-label">Discount (<?php echo $payment->percent_discount; ?>%):</span>
                                                    <span class="info-value">
                                                        <?php
                                                        $discount = explode('*', $payment->discount);
                                                        if (!empty($discount[1])) {
                                                            echo $settings->currency . ' ' . $discount[1];
                                                        } else {
                                                            echo $discount[0];
                                                        }
                                                        ?>
                                                    </span>
                                                </div>
                                            <?php } ?>
                                            <?php if (!empty($payment->vat)) { ?>
                                                <div class="info-row">
                                                    <span class="info-label">VAT (<?php echo $payment->vat_amount_percent; ?>%):</span>
                                                    <span class="info-value"><?php echo $settings->currency; ?> <?php echo $payment->vat; ?></span>
                                                </div>
                                            <?php } ?>
                                            <div class="info-row">
                                                <span class="info-label"><strong>Grand Total:</strong></span>
                                                <span class="info-value"><strong><?php echo $settings->currency; ?> <?php echo $payment->gross_total; ?></strong></span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Amount Received:</span>
                                                <span class="info-value"><?php echo $settings->currency; ?> <?php echo $this->finance_model->getDepositAmountByPaymentId($payment->id); ?></span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Amount Due:</span>
                                                <span class="info-value"><?php echo $settings->currency; ?> <?php echo $payment->gross_total - $this->finance_model->getDepositAmountByPaymentId($payment->id); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="payment-status">
                                                <?php if($payment->gross_total - $this->finance_model->getDepositAmountByPaymentId($payment->id) == 0) { ?>
                                                    <div class="status-paid">
                                                        <strong><?php echo lang("paid"); ?></strong>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="status-due">
                                                        <strong><?php echo lang("due_have"); ?></strong>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Internal Accounting / Hospital Financials (Admin & Accountant only) -->
                            <?php if ($this->ion_auth->in_group(array('admin', 'Accountant'))) { 
                                $gross_amt = floatval($payment->amount);
                                $disc_amt = floatval(!empty($payment->flat_discount) ? $payment->flat_discount : (!empty($payment->discount) ? $payment->discount : 0));
                                $net_comm_base = max(0.00, $gross_amt - $disc_amt);
                            ?>
                            <div class="document-section mb-2" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 12px;">
                                <h5 class="section-title text-primary"><i class="fas fa-calculator mr-1"></i> <?php echo lang('internal_commission_summary'); ?> <span class="badge badge-secondary" style="font-size: 0.65rem;"><?php echo lang('hospital_accounting'); ?></span></h5>
                                <div class="section-content">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-row">
                                                <span class="info-label">Gross Amount:</span>
                                                <span class="info-value font-weight-bold"><?php echo $settings->currency; ?> <?php echo number_format($gross_amt, 2); ?></span>
                                            </div>
                                            <div class="info-row text-danger">
                                                <span class="info-label">Discount (-):</span>
                                                <span class="info-value font-weight-bold">-<?php echo $settings->currency; ?> <?php echo number_format($disc_amt, 2); ?></span>
                                            </div>
                                            <div class="info-row font-weight-bold text-dark border-top pt-1">
                                                <span class="info-label">Final Net Amount:</span>
                                                <span class="info-value"><?php echo $settings->currency; ?> <?php echo number_format($net_comm_base, 2); ?></span>
                                            </div>
                                            <div class="info-row text-primary font-weight-bold bg-white px-2 py-1 rounded border mt-1">
                                                <span class="info-label"><i class="fas fa-bullseye mr-1"></i> Commission Base:</span>
                                                <span class="info-value"><?php echo $settings->currency; ?> <?php echo number_format($net_comm_base, 2); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                                $d_pct = ($net_comm_base > 0 && !empty($payment->doctor_amount)) ? ' ' . round(($payment->doctor_amount / $net_comm_base) * 100, 1) . '%' : ' 0%';
                                                $r_pct = ($net_comm_base > 0 && !empty($payment->referral_amount)) ? ' ' . round(($payment->referral_amount / $net_comm_base) * 100, 1) . '%' : ' 0%';
                                            ?>
                                            <div class="info-row text-info">
                                                <span class="info-label"><?php echo lang('doctor_commission'); ?><?php echo $d_pct; ?> (-):</span>
                                                <span class="info-value font-weight-bold"><?php echo $settings->currency; ?> <?php echo number_format(!empty($payment->doctor_amount) ? $payment->doctor_amount : 0, 2); ?></span>
                                            </div>
                                            <div class="info-row text-warning">
                                                <span class="info-label"><?php echo lang('referral_commission'); ?><?php echo $r_pct; ?> (-):</span>
                                                <span class="info-value font-weight-bold"><?php echo $settings->currency; ?> <?php echo number_format(!empty($payment->referral_amount) ? $payment->referral_amount : 0, 2); ?></span>
                                            </div>
                                            <div class="info-row text-success font-weight-bold border-top pt-1">
                                                <span class="info-label"><?php echo lang('net_hospital_revenue'); ?>:</span>
                                                <span class="info-value"><?php echo $settings->currency; ?> <?php echo number_format(!empty($payment->hospital_amount) ? $payment->hospital_amount : ($payment->gross_total - (floatval($payment->doctor_amount ?? 0) + floatval($payment->referral_amount ?? 0))), 2); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2 font-italic">* Commissions are internal expenses calculated from Final Net Amount after discount.</small>
                                </div>
                            </div>
                            <?php } ?>

                            <!-- Payment History -->
                            <?php 
                            $deposited_amount_payment = $this->finance_model->getDepositByInvoiceId($payment->id); 
                            if(count($deposited_amount_payment) > 1) {
                            ?>
                            <div class="document-section mb-2">
                                <h5 class="section-title">PAYMENT HISTORY</h5>
                                <div class="section-content">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th><?php echo lang('date');?></th>
                                                    <th><?php echo lang('amount');?></th>
                                                    <th><?php echo lang('payment_method');?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($deposited_amount_payment as $deposited){ ?>
                                                <tr>
                                                    <td><?php echo date('d-m-y',$deposited->date); ?></td>
                                                    <td><?php echo $settings->currency .' '. $deposited->deposited_amount; ?></td>
                                                    <td>
                                                        <?php if($deposited->deposit_type=='Cash'){
                                                            echo 'Cash';
                                                        }else{
                                                            echo $deposited->gateway;
                                                        } ?>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <!-- Footer -->
                            <div class="document-section mb-2">
                                <h5 class="section-title">INVOICE DETAILS</h5>
                                <div class="section-content">
                                    <div class="info-row">
                                        <span class="info-label">Generated By:</span>
                                        <span class="info-value"><?php echo $this->ion_auth->user($payment->user)->row()->username; ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Generated On:</span>
                                        <span class="info-value"><?php echo date('M d, Y H:i', $payment->date); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <?php if ($redirect != 'download') { ?>
                    <section class="col-md-4">
                        <div class="col-md-5 no-print option_button">
                            <?php if ($payment->payment_from == 'payment' || empty($payment->payment_from)) { ?>
                                <a href="finance/payment" class="btn btn-info btn-sm info float-left"><i class="fa fa-arrow-circle-left"></i>  <?php echo lang('back_to_payment_modules'); ?> </a>
                            <?php } ?>
                            <div class="text-center col-md-12 row">
                                <a class="btn btn-info btn-sm invoice_button float-left" onclick="javascript:window.print();"><i class="fa fa-print"></i> <?php echo lang('print'); ?> </a>
                                <?php if ($this->ion_auth->in_group(array('admin', 'Accountant'))) { ?>
                                    <?php if ($payment->payment_from == 'payment' || empty($payment->payment_from)) {
                                        $lab_pending=array();
                                        $lab_reports_previous=$this->lab_model->getLabByInvoice($payment->id);
                                        if(!empty($lab_reports_previous)){
                                            foreach($lab_reports_previous as $lab){
                                                if($lab->test_status == 'not_done' || empty($lab->test_status)){
                                                    $lab_pending[]='no';
                                                }
                                            }
                                        }
                                        if(count($lab_reports_previous)==count($lab_pending) || empty($lab_reports_previous)){
                                        ?>
                                    <a href="finance/editPayment?id=<?php echo $payment->id; ?>" class="btn btn-info btn-sm editbutton float-left"><i class="fa fa-edit"></i> <?php echo lang('edit'); ?> <?php echo lang('invoice'); ?> </a>
                                    <?php } } ?>
                                    <a href="finance/download?id=<?php echo $payment->id; ?>" class="btn btn-info btn-sm detailsbutton float-left download"><i class="fa fa-download"></i> <?php echo lang('download'); ?>  </a>
                                <?php } ?>
                            </div>
                           
                            <div class="no-print">
                                <?php if ($payment->payment_from == 'payment' || empty($payment->payment_from)) { ?>
                                    <a href="finance/addPaymentView" class="float-left">
                                        <div class="btn-group">
                                            <button id="" class="btn btn-info green btn-sm">
                                                <i class="fa fa-plus-circle"></i> <?php echo lang('add_another_payment'); ?>
                                            </button>
                                        </div>
                                    </a>
                                <?php } ?>
                                <?php if ($this->ion_auth->in_group(array('admin', 'Accountant'))) { ?>
                                    <a href="finance/sendInvoice?id=<?php echo $payment->id; ?>" class="btn  btn-sm float-left send"> <i class="fa fa-paper-plane"></i> <?php echo lang('send_invoice'); ?>  </a>
                                <?php } ?>
                            </div>

                            <div class="panel_button">
                                <div class="text-center invoice-btn no-print float-left ">
                                    <a href="finance/previousInvoice?id=<?php echo $payment->id ?>"class="btn btn-info btn-sm green previousone1"><i class="glyphicon glyphicon-chevron-left"></i> </a>
                                    <a href="finance/nextInvoice?id=<?php echo $payment->id ?>"class="btn btn-info btn-sm green nextone1 "><i class="glyphicon glyphicon-chevron-right"></i> </a>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php } ?>

                <link rel="stylesheet" href="common/extranal/css/finance/invoiceAll.css"/>
                <script src="common/js/codearistos.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>
            </section>
            <?php if ($redirect == 'download') { ?>
                </html>
            <?php } ?>
            <!-- invoice end-->
            <?php if ($redirect != 'download') { ?>
            </section>
        </section>
        <link href="common/extranal/css/finance/print.css" rel="stylesheet" />
        <script src="common/js/codearistos.min.js"></script>
        <script src="common/js/bootstrap.min.js"></script>
        <script  type="text/javascript" src="common/assets/DataTables/datatables.min.js"></script>
    <?php } ?>
    <script src="common/extranal/js/finance/invoice.js"></script>

    <?php if ($redirectlink == 'print') { ?>
        <script src="common/extranal/js/finance/printInvoice.js"></script>
    <?php } ?>

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

/* Invoice Table */
.invoice-table {
    font-size: 0.85rem;
    margin-bottom: 0;
}

.invoice-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #000;
    border: 1px solid #ddd;
    padding: 0.5rem;
}

.invoice-table td {
    border: 1px solid #ddd;
    padding: 0.5rem;
    vertical-align: middle;
}

/* Payment Status */
.payment-status {
    border: 2px solid #000;
    border-radius: 5px;
    padding: 1rem;
    text-align: center;
}

.status-paid {
    color: #28a745;
    font-size: 1.1rem;
}

.status-due {
    color: #dc3545;
    font-size: 1.1rem;
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