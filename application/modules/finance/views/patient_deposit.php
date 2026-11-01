<!--sidebar end-->
<!--main content start-->

<link href="common/extranal/css/finance/patient_deposit.css" rel="stylesheet">

<div class="content-wrapper bg-light">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row my-2 pl-1">
                <div class="col-sm-6">
                    <h1 class="font-weight-bold"><i class="fas fa-history mr-2"></i><?php echo lang('payment_history'); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home"><?php echo lang('home') ?></a></li>
                        <li class="breadcrumb-item active"> <?php echo lang('payment_history'); ?> </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"> <?php echo lang('payment_history'); ?></h3>

                        <div class="float-right mr-1 no-print">
                            <a data-toggle="modal" href="#myModal">
                                <button id="" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus-circle"></i> <?php echo lang('deposit'); ?>
                                </button>
                            </a>
                        </div>

                        <div class="float-right mr-1 no-print">
                            <a data-toggle="modal" href="#myModal5">
                                <button id="" class="btn btn-warning btn-sm">
                                    <i class="fa fa-plus-circle"></i> <?php echo lang('invoice'); ?>
                                </button>
                            </a>
                        </div>

                        <div class="float-right mr-1 no-print">
                            <a href="finance/addPaymentByPatientView?id=<?php echo $patient->id; ?>&type=gen">
                                <button id="" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus-circle"></i> <?php echo lang('add_new'); ?>
                                    <?php echo lang('invoice'); ?>
                                </button>
                            </a>
                        </div>
                    </div>
                    <!-- /.card-header -->

                    <div class="col-md-12 no-print mt-4 ml-2">
                        <form role="form" class="f_report" action="finance/patientPaymentHistory" method="post" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control dpd1" name="date_from" value="<?php if (!empty($date_from)) echo date('m/d/Y', $date_from); ?>" placeholder="<?php echo lang('date_from'); ?>" readonly>

                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo lang(''); ?></span>
                                        </div>

                                        <input type="text" class="form-control dpd2" name="date_to" value="<?php if (!empty($date_to)) echo date('m/d/Y', $date_to); ?>" placeholder="<?php echo lang('date_to'); ?>" readonly>

                                        <input type="hidden" class="form-control dpd2" name="patient" value="<?php echo $patient->id; ?>">
                                    </div>
                                    <span class="help-block"></span>
                                </div>

                                <div class="col-md-6">
                                    <button type="submit" name="submit" class="btn btn-secondary"><?php echo lang('submit'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        <header class="card-header col-md-12 row">
                            <?php echo lang('all_bills'); ?> & <?php echo lang('deposits'); ?>
                        </header>
                        <div class="space15"></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered" id="editable-samples">
                                <thead>
                                    <tr>
                                        <th class=""><?php echo lang('date'); ?></th>
                                        <th class=""><?php echo lang('invoice'); ?> #</th>
                                        <th class=""><?php echo lang('bill_amount'); ?></th>
                                        <th class=""><?php echo lang('deposit'); ?></th>
                                        <th class=""><?php echo lang('deposit_type'); ?></th>
                                        <th class=""><?php echo lang('from'); ?></th>
                                        <th class="no-print"><?php echo lang('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $dates = array();
                                    $datess = array();
                                    foreach ($payments as $payment) {
                                        $dates[] = $payment->date;
                                    }
                                    foreach ($deposits as $deposit) {
                                        $datess[] = $deposit->date;
                                    }
                                    $dat = array_merge($dates, $datess);
                                    $dattt = array_unique($dat);
                                    asort($dattt);

                                    $total_pur = array();
                                    $total_p = array();
                                    ?>

                                    <?php
                                    foreach ($dattt as $key => $value) {
                                        foreach ($payments as $payment) {
                                            if ($payment->date == $value) {
                                    ?>
                                                <tr class="">
                                                    <td><?php echo date('d-m-y', $payment->date); ?></td>
                                                    <td> <?php echo $payment->id; ?></td>
                                                    <td><?php echo $settings->currency; ?> <?php echo $payment->gross_total; ?></td>
                                                    <td><?php
                                                        if (!empty($payment->amount_received)) {
                                                            echo $settings->currency;
                                                        }
                                                        ?> <?php echo $payment->amount_received; ?>
                                                    </td>

                                                    <td> <?php echo $payment->deposit_type; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($payment->payment_from == 'appointment') {
                                                            echo '<span class="label label-primary">' . lang('appointment') . '</span>';
                                                        } elseif ($payment->payment_from == 'admitted_patient_bed_medicine') {
                                                            echo '<span class="label label-warning">' . lang('ipd_medicine') . '</span>';
                                                        } elseif ($payment->payment_from == 'case') {
                                                            echo '<span class="label label-primary">' . lang('case') . '</span>';
                                                        } elseif ($payment->payment_from == 'admitted_patient_bed_service') {
                                                            echo '<span class="label label-success">' . lang('ipd_service') . '</span>';
                                                        } elseif ($payment->payment_from == 'admitted_patient_bed_diagnostic') {
                                                            echo '<span class="label label-info">' . lang('ipd_diagnostic') . '</span>';
                                                        } elseif ($payment->payment_from == 'payment' || empty($payment->payment_from)) {
                                                            echo '<span class="label label-primary">' . lang('opd') . '</span>';
                                                        } elseif ($payment->payment_from == 'pre_service') {
                                                            echo lang('pre_surgery') . ' ' . lang('service');
                                                        } elseif ($payment->payment_from == 'post_service') {
                                                            echo lang('post_surgery') . ' ' . lang('service');
                                                        } elseif ($payment->payment_from == 'surgery') {
                                                            echo lang('surgery');
                                                        } elseif ($payment->payment_from == 'pre_surgery_medical_analysis') {
                                                            echo lang('pre_surgery') . ' ' . lang('medical_analysis');
                                                        } elseif ($payment->payment_from == 'post_surgery_medical_analysis') {
                                                            echo lang('post_surgery') . ' ' . lang('medical_analysis');
                                                        } elseif ($payment->payment_from == 'pre_surgery_medicine') {
                                                            echo lang('pre_surgery') . ' ' . lang('medicine');
                                                        } elseif ($payment->payment_from == 'post_surgery_medicine') {
                                                            echo lang('post_surgery') . ' ' . lang('medicine');
                                                        }
                                                        ?>
                                                    </td>

                                                    <td class="no-print d-flex gap-1">
                                                        <?php if ($this->ion_auth->in_group(array('admin', 'Accountant'))) { ?>
                                                            <?php if ($payment->payment_from == 'payment' && empty($payment->payment_from)) {
                                                                $lab_pending = array();
                                                                $lab_reports_previous = $this->lab_model->getLabByInvoice($payment->id);

                                                                if (!empty($lab_reports_previous)) {
                                                                    foreach ($lab_reports_previous as $lab) {
                                                                        if ($lab->test_status == 'not_done' || empty($lab->test_status)) {
                                                                            $lab_pending[] = 'no';
                                                                        }
                                                                    }
                                                                }
                                                                if (count($lab_reports_previous) == count($lab_pending) || empty($lab_reports_previous)) {
                                                            ?>
                                                                    <a type="button" class="btn-sm btn-info edit_pay" title="<?php echo lang('edit'); ?>" href="finance/editPayment?id=<?php echo $payment->id; ?>"><i class="fa fa-edit"> </i></a>
                                                            <?php }
                                                            } ?>
                                                        <?php } ?>
                                                        <a class="btn-sm btn-secondary" title="<?php echo lang('invoice'); ?>" href="finance/invoice?id=<?php echo $payment->id; ?>"><i class="fa fa-file-invoice"></i> </a>
                                                        <?php if ($this->ion_auth->in_group(array('admin', 'Accountant'))) { ?>
                                                            <?php if ($payment->payment_from == 'payment' && empty($payment->payment_from)) {
                                                                $lab_pending = array();
                                                                $lab_reports_previous = $this->lab_model->getLabByInvoice($payment->id);

                                                                if (!empty($lab_reports_previous)) {
                                                                    foreach ($lab_reports_previous as $lab) {
                                                                        if ($lab->test_status == 'not_done' || empty($lab->test_status)) {
                                                                            $lab_pending[] = 'no';
                                                                        }
                                                                    }
                                                                }
                                                                if (count($lab_reports_previous) == count($lab_pending) || empty($lab_reports_previous)) {
                                                            ?>
                                                                    <a type="button" class="btn-sm btn-danger delete_button" title="<?php echo lang('delete'); ?>" style="width: 25%;" href="finance/delete?id=<?php echo $payment->id; ?>" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i> </a>
                                                            <?php }
                                                            } ?>
                                                        <?php } ?>
                                                        </button>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>

                                        <?php
                                        foreach ($deposits as $deposit) {
                                            if ($deposit->date == $value) {
                                                if (!empty($deposit->deposited_amount) && empty($deposit->amount_received_id)) {
                                        ?>
                                                    <tr class="">
                                                        <td><?php echo date('d-m-y', $deposit->date); ?></td>
                                                        <td><?php echo $deposit->payment_id; ?></td>
                                                        <td></td>
                                                        <td><?php echo $settings->currency; ?> <?php echo $deposit->deposited_amount; ?></td>
                                                        <td> <?php echo $deposit->deposit_type; ?></td>
                                                        <td></td>
                                                        <td class="no-print d-flex gap-1">
                                                            <?php if ($this->ion_auth->in_group(array('admin', 'Accountant'))) { ?>
                                                                <a type="button" class="btn-sm btn-info editbutton edit_pay" title="<?php echo lang('edit'); ?>" data-toggle="modal" data-id="<?php echo $deposit->id; ?>"><i class="fa fa-edit"></i></a>
                                                            <?php } ?>
                                                            <?php if ($this->ion_auth->in_group(array('admin', 'Accountant'))) { ?>
                                                                <a type="button" class="btn-sm btn-danger" href="finance/deleteDeposit?id=<?php echo $deposit->id; ?>&patient=<?php echo $patient->id; ?>" onclick="return confirm('Are you sure you want to delete this item?');">
                                                                    <?php echo lang(''); ?> <i class="fa fa-trash"></i>
                                                                </a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                        <?php
                                                }
                                            }
                                        }
                                        ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-user-circle mr-2"></i><?php echo lang('patient'); ?> <?php echo lang('details'); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="font-weight-bold text-primary mb-3"><?php echo $patient->name; ?></h2>
                            <p class="text-muted mb-0"><i class="fas fa-map-marker-alt mr-2"></i><?php echo $patient->address; ?></p>
                        </div>

                        <div class="list-group list-group-flush mb-4">
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-envelope text-primary mr-3"></i>
                                    <span><?php echo $patient->email; ?></span>
                                </div>
                            </div>
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-phone text-primary mr-3"></i>
                                    <span><?php echo $patient->phone; ?></span>
                                </div>
                            </div>
                        </div>

                        <?php
                        $total_bill = array();
                        foreach ($payments as $payment) {
                            $total_bill[] = $payment->gross_total;
                        }
                        $total_bill = !empty($total_bill) ? array_sum($total_bill) : 0;
                        ?>

                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fa fa-money-bill text-success mr-2"></i>
                                        <span class="text-muted"><?php echo lang('total_bill_amount'); ?></span>
                                    </div>
                                    <div class="h5 mb-0"><?php echo $settings->currency; ?> <?php echo $total_bill; ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fa fa-piggy-bank text-info mr-2"></i>
                                        <span class="text-muted"><?php echo lang('total_deposit_amount'); ?></span>
                                    </div>
                                    <div class="h5 mb-0">
                                        <?php echo $settings->currency; ?>
                                        <?php
                                        $total_deposit = array();
                                        foreach ($deposits as $deposit) {
                                            $total_deposit[] = $deposit->deposited_amount;
                                        }
                                        echo array_sum($total_deposit);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fa fa-exclamation-circle text-danger mr-2"></i>
                                        <span class="text-muted"><?php echo lang('due_amount'); ?></span>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold">
                                        <?php echo $settings->currency; ?>
                                        <?php echo $total_bill - array_sum($total_deposit); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>

    <!-- /.content -->
</div>









<!--main content end-->
<!--footer start-->





<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"> <?php echo lang('add_deposit'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form role="form" action="finance/deposit" id="deposit-form" class="clearfix" method="post" enctype="multipart/form-data">
                    <div class="form-group payment_class">
                        <label for="exampleInputEmail1"> <?php echo lang('invoice'); ?></label>
                        <select class="form-control form-control-lg m-bot15 js-example-basic-single" id="payment_id1" name="payment_id" value='' required>
                            <option value="">Select .....</option>
                            <?php foreach ($payments as $payment) {
                                if ($payment->payment_from == 'payment' || $payment->payment_from == 'admitted_patient_bed_medicine'  || $payment->payment_from == 'admitted_patient_bed_service' || $payment->payment_from == 'admitted_patient_bed_diagnostic'   || $payment->payment_from == null) {
                            ?>
                                    <option value="<?php echo $payment->id; ?>" <?php

                                                                                ?>><?php echo $payment->id; ?>
                                    </option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1"> <?php echo lang('due'); ?> <?php echo lang('amount'); ?></label>
                        <input type="text" class="form-control form-control-lg" id="due_amount" name="due" value='' placeholder="" readonly>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1"> <?php echo lang('deposit_amount'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="deposited_amount" id="deposited_amount" value='' placeholder="" required="">
                    </div>



                    <div class="form-group">
                        <div class="">
                            <label for="exampleInputEmail1"> <?php echo lang('deposit_type'); ?></label>
                        </div>
                        <div class="">
                            <select class="form-control form-control-lg m-bot15 js-example-basic-single selecttype" id="selecttype" name="deposit_type" value=''>
                                <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                                    <option value="Cash"> <?php echo lang('cash'); ?> </option>
                                    <!-- <option value="Insurance"> <?php echo lang('insurance'); ?> </option> -->
                                    <option value="Card">Card</option>
                                    <option value="Bkash">Bkash</option>
                                    <option value="Nagad">Nagad</option>
                                    <option value="Bank">Bank</option>
                                    <option value="Others">Others</option>
                                <?php } ?>

                            </select>
                        </div>
                        <div class="hidden insurance_div">

                            <div class="form-group" style="margin-top:10px;">
                                <label for="exampleInputEmail1"> <?php echo lang('insurance_company'); ?>
                                    <?php echo lang('name'); ?></label>

                                <div class="company_div">
                                    <select class="form-control form-control-lg m-bot15 js-example-basic-single" name="insurance_company" id="insurance_company" value=''>
                                        <option value="">Select Company</option>
                                        <?php foreach ($insurance_companys as $insurance_company) { ?>
                                            <option value="<?php echo $insurance_company->id; ?>" <?php


                                                                                                    ?>>
                                                <?php echo $insurance_company->name; ?> </option>
                                        <?php } ?>
                                    </select>
                                </div>

                            </div>
                            <div class="">
                                <div class="payment_label" style="margin-top:10px;">
                                    <label for="exampleInputEmail1"> <?php echo lang('insurance_details'); ?>
                                    </label>
                                </div>
                                <div class="">
                                    <textarea class="form-control form-control-lg" name="insurance_details" rows="2" cols="20"></textarea>
                                </div>

                            </div>
                        </div>
                        <?php
                        $payment_gateway = $settings->payment_gateway;
                        ?>



                        <div class="cardPayment">

                            <hr>
                            <div class="col-md-12 payment pad_bot">
                                <label for="exampleInputEmail1"> <?php echo lang('accepted'); ?>
                                    <?php echo lang('cards'); ?></label>
                                <div class="payment pad_bot">
                                    <img src="uploads/card.png" width="100%">
                                </div>
                            </div>
                            <?php
                            if ($payment_gateway == 'PayPal') {
                            ?>

                                <div class="col-md-12 payment pad_bot">
                                    <label for="exampleInputEmail1"> <?php echo lang('card'); ?>
                                        <?php echo lang('type'); ?></label>
                                    <select class="form-control form-control-lg m-bot15" name="card_type" value=''>

                                        <option value="Mastercard"> <?php echo lang('mastercard'); ?> </option>
                                        <option value="Visa"> <?php echo lang('visa'); ?> </option>
                                        <option value="American Express"> <?php echo lang('american_express'); ?> </option>
                                    </select>
                                </div>
                            <?php } ?>
                            <?php if ($payment_gateway == '2Checkout' || $payment_gateway == 'PayPal') {
                            ?>
                                <div class="col-md-12 payment pad_bot">
                                    <label for="exampleInputEmail1"> <?php echo lang('cardholder'); ?>
                                        <?php echo lang('name'); ?></label>
                                    <input type="text" id="cardholder" class="form-control pay_in" name="cardholder" value='' placeholder="">
                                </div>
                            <?php } ?>
                            <?php if ($payment_gateway != 'Pay U Money' && $payment_gateway != 'Paystack' && $payment_gateway != 'SSLCOMMERZ' && $payment_gateway != 'Paytm') { ?>
                                <div class="col-md-12 payment pad_bot">
                                    <label for="exampleInputEmail1"> <?php echo lang('card'); ?>
                                        <?php echo lang('number'); ?></label>
                                    <input type="text" class="form-control pay_in" id="card" name="card_number" value='' placeholder="">
                                </div>



                                <div class="col-md-8 payment pad_bot">
                                    <label for="exampleInputEmail1"> <?php echo lang('expire'); ?>
                                        <?php echo lang('date'); ?></label>
                                    <input type="text" class="form-control pay_in" id="expire" data-date="" data-date-format="MM YY" placeholder="Expiry (MM/YY)" name="expire_date" maxlength="7" aria-describedby="basic-addon1" value='' placeholder="">
                                </div>
                                <div class="col-md-4 payment pad_bot">
                                    <label for="exampleInputEmail1"> <?php echo lang('cvv'); ?> </label>
                                    <input type="text" class="form-control pay_in" id="cvv" maxlength="3" name="cvv" value='' placeholder="">
                                </div>

                        </div>

                    <?php
                            }
                    ?>

                    </div>



                    <input type="hidden" name="id" value=''>
                    <input type="hidden" name="patient" value='<?php echo $patient->id; ?>'>
                    <div class="form-group cashsubmit payment">
                        <button type="submit" name="submit2" id="submit1" class="btn btn-info btn-block float-right">
                            <?php echo lang('submit'); ?></button>
                    </div>
                    <div class="form-group cardsubmit d-none">
                        <?php $twocheckout = $this->db->get_where('paymentgateway', array('name =' => '2Checkout'))->row(); ?>
                        <button type="submit" name="pay_now" id="submit-btn" class="btn btn-info btn-block float-right" <?php if ($settings->payment_gateway == 'Stripe') {
                                                                                                                        ?>onClick="stripePay(event);" <?php }
                                                                                                                                                        ?><?php if ($settings->payment_gateway == '2Checkout' && $twocheckout->status == 'live') {
                                                                                                                                                            ?>onClick="twoCheckoutPay(event);" <?php }
                                                                                                                                                                                                ?>> <?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
</div>
<!-- Add Patient Modal-->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold"> <?php echo lang('edit_deposit'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form role="form" id="editDepositform" action="finance/deposit" class="clearfix" method="post" enctype="multipart/form-data">
                    <div class=payment_label">
                        <label for="exampleInputEmail1"> <?php echo lang('invoice'); ?></label>
                        <select class="form-control form-control-lg m-bot15 js-example-basic-single" id="payment_id2" name="payment_id" value=''>
                            <option value="">Select .....</option>
                            <?php foreach ($payments as $payment) {
                                if ($payment->payment_from == 'payment' || $payment->payment_from == 'admitted_patient_bed_medicine'  || $payment->payment_from == 'admitted_patient_bed_service' || $payment->payment_from == 'admitted_patient_bed_diagnostic') {
                            ?>
                                    <option value="<?php echo $payment->id; ?>" <?php
                                                                                if (!empty($deposit->payment_id)) {
                                                                                    if ($deposit->payment_id == $payment->id) {
                                                                                        echo 'selected';
                                                                                    }
                                                                                }
                                                                                ?>><?php echo $payment->id; ?>
                                    </option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1"> <?php echo lang('due'); ?> <?php echo lang('amount'); ?></label>
                        <input type="text" class="form-control form-control-lg" id="due_amount1" name="due" value='' placeholder="" readonly>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1"> <?php echo lang('deposit_amount'); ?></label>
                        <input type="text" class="form-control form-control-lg" name="deposited_amount" id="deposited_amount1" value='' placeholder="">
                    </div>


                    <div class="form-group">
                        <div class="">
                            <label for="exampleInputEmail1"> <?php echo lang('deposit_type'); ?></label>
                        </div>
                        <div class="">
                            <select class="form-control form-control-lg m-bot15 js-example-basic-single selecttype1" id="selecttype1" name="deposit_type" value=''>
                                <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) { ?>
                                    <option value="Cash"> <?php echo lang('cash'); ?> </option>
                                    <option value="Insurance"> <?php echo lang('insurance'); ?> </option>
                                    <option value="Card">Card</option>
                                    <option value="Bkash">Bkash</option>
                                    <option value="Nagad">Nagad</option>
                                    <option value="Bank">Bank</option>
                                    <option value="Others">Others</option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="hidden insurance_div1">

                            <div class="form-group" style="margin-top:10px;">
                                <label for="exampleInputEmail1"> <?php echo lang('insurance_company'); ?>
                                    <?php echo lang('name'); ?></label>

                                <div class="company_div1">
                                    <select class="form-control form-control-lg m-bot15 js-example-basic-single" name="insurance_company" id="insurance_company1" value=''>
                                        <option value="">Select Company</option>
                                        <?php foreach ($insurance_companys as $insurance_company) { ?>
                                            <option value="<?php echo $insurance_company->id; ?>" <?php
                                                                                                    if (!empty($setval)) {
                                                                                                        if ($insurance_company->id == set_value('insurance_company')) {
                                                                                                            echo 'selected';
                                                                                                        }
                                                                                                    }
                                                                                                    if (!empty($payment->insurance_company)) {
                                                                                                        if ($insurance_company->id == $payment->insurance_company) {
                                                                                                            echo 'selected';
                                                                                                        }
                                                                                                    }
                                                                                                    ?>>
                                                <?php echo $insurance_company->name; ?> </option>
                                        <?php } ?>
                                    </select>
                                </div>

                            </div>
                            <div class="">
                                <div class="payment_label" style="margin-top:10px;">
                                    <label for="exampleInputEmail1"> <?php echo lang('insurance_details'); ?>
                                    </label>
                                </div>
                                <div class="">
                                    <textarea class="form-control form-control-lg" name="insurance_details" rows="2" cols="20"></textarea>
                                </div>

                            </div>
                        </div>
                        <?php
                        $payment_gateway = $settings->payment_gateway;
                        ?>



                        <div class="card1">

                            <hr>
                            <div class="col-md-12 payment pad_bot">
                                <label for="exampleInputEmail1"> <?php echo lang('accepted'); ?>
                                    <?php echo lang('cards'); ?></label>
                                <div class="payment pad_bot">
                                    <img src="uploads/card.png" width="100%">
                                </div>
                            </div>

                            <?php
                            if ($payment_gateway == 'PayPal') {
                            ?>
                                <div class="col-md-12 payment pad_bot">
                                    <label for="exampleInputEmail1"> <?php echo lang('card'); ?>
                                        <?php echo lang('type'); ?></label>
                                    <select class="form-control form-control-lg m-bot15" name="card_type" value=''>

                                        <option value="Mastercard"> <?php echo lang('mastercard'); ?> </option>
                                        <option value="Visa"> <?php echo lang('visa'); ?> </option>
                                        <option value="American Express"> <?php echo lang('american_express'); ?> </option>
                                    </select>
                                </div>
                            <?php } ?>
                            <?php if ($payment_gateway == '2Checkout' || $payment_gateway == 'PayPal') {
                            ?>
                                <div class="col-md-12 payment pad_bot">
                                    <label for="exampleInputEmail1"> <?php echo lang('cardholder'); ?>
                                        <?php echo lang('name'); ?></label>
                                    <input type="text" id="cardholder" class="form-control pay_in" name="cardholder" value='' placeholder="">
                                </div>
                            <?php } ?>
                            <?php if ($payment_gateway != 'Pay U Money' && $payment_gateway != 'Paystack' && $payment_gateway != 'SSLCOMMERZ' && $payment_gateway != 'Paytm') { ?>
                                <div class="col-md-12 payment pad_bot">
                                    <label for="exampleInputEmail1"> <?php echo lang('card'); ?>
                                        <?php echo lang('number'); ?></label>
                                    <input type="text" class="form-control pay_in" id="card1" name="card_number" value='<?php
                                                                                                                        if (!empty($payment->p_email)) {
                                                                                                                            echo $payment->p_email;
                                                                                                                        }
                                                                                                                        ?>' placeholder="">
                                </div>



                                <div class="col-md-8 payment pad_bot">
                                    <label for="exampleInputEmail1"> <?php echo lang('expire'); ?>
                                        <?php echo lang('date'); ?></label>
                                    <input type="text" class="form-control pay_in" data-date="" id="expire1" data-date-format="MM YY" placeholder="Expiry (MM/YY)" name="expire_date" maxlength="7" aria-describedby="basic-addon1" value='<?php
                                                                                                                                                                                                                                            if (!empty($payment->p_phone)) {
                                                                                                                                                                                                                                                echo $payment->p_phone;
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                            ?>' placeholder="">
                                </div>
                                <div class="col-md-4 payment pad_bot">
                                    <label for="exampleInputEmail1"> <?php echo lang('cvv'); ?> </label>
                                    <input type="text" class="form-control pay_in" id="cvv1" maxlength="3" name="cvv_number" value='<?php
                                                                                                                                    if (!empty($payment->p_age)) {
                                                                                                                                        echo $payment->p_age;
                                                                                                                                    }
                                                                                                                                    ?>' placeholder="">
                                </div>

                        </div>

                    <?php
                            }
                    ?>

                    </div>



                    <input type="hidden" name="id" value=''>
                    <input type="hidden" name="patient" value='<?php echo $patient->id; ?>'>
                    <div class="form-group cashsubmit1 payment">
                        <button type="submit" name="submit2" id="submit1" class="btn btn-info btn-block float-right">
                            <?php echo lang('submit'); ?></button>
                    </div>
                    <div class="form-group cardsubmit1 d-none">
                        <button type="submit" name="pay_now" id="submit-btn1" class="btn btn-info btn-block float-right" <?php if ($settings->payment_gateway == 'Stripe') {
                                                                                                                            ?>onClick="stripePay1(event);" <?php }
                                                                                                                                                            ?>>
                            <?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
</div>
<!-- Add Patient Modal-->












<div class="modal fade" id="myModal5" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title font-weight-bold"> <?php echo lang('invoice'); ?></h4>
                <button type="button" class="close no-print" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body clearfix">
                <div class="card-primary">

                    <div class="" id="invoice">
                        <div class="row invoice-list">
                            <div class="text-center corporate-id top_title">
                                <!-- <img alt="" src="<?php echo $this->settings_model->getSettings()->logo; ?>" width="200" height="100"> -->
                                <h3>
                                    <?php echo $settings->title ?>
                                </h3>
                                <h4>
                                    <?php echo $settings->address ?>
                                </h4>
                                <h4>
                                    Tel: <?php echo $settings->phone ?>
                                </h4>
                            </div>
                            <div class="col-lg-4 col-sm-4 information">
                                <h4><?php echo lang('payment_to'); ?>:</h4>
                                <p>
                                    <?php echo $settings->title; ?> <br>
                                    <?php echo $settings->address; ?><br>
                                    Tel: <?php echo $settings->phone; ?>
                                </p>
                            </div>
                            <?php if (!empty($payment->patient)) { ?>
                                <div class="col-lg-4 col-sm-4 information">
                                    <h4><?php echo lang('bill_to'); ?>:</h4>
                                    <p>
                                        <?php
                                        if (!empty($patient->name)) {
                                            echo $patient->name . ' <br>';
                                        }
                                        if (!empty($patient->address)) {
                                            echo $patient->address . ' <br>';
                                        }
                                        if (!empty($patient->phone)) {
                                            echo $patient->phone . ' <br>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            <?php } ?>
                            <div class="col-lg-4 col-sm-4 information">
                                <h4><?php echo lang('invoice_info'); ?></h4>
                                <ul class="unstyled">
                                    <li>Date : <?php echo date('m/d/Y'); ?></li>
                                </ul>
                            </div>
                            <br>
                        </div>
                        <table class="table table-striped table-hover table-bordered" id="editable-samples">
                            <thead>
                                <tr>
                                    <th class=""><?php echo lang('date'); ?></th>
                                    <th class=""><?php echo lang('invoice'); ?> #</th>
                                    <th class=""><?php echo lang('bill_amount'); ?></th>
                                    <th class=""><?php echo lang('deposit'); ?></th>
                                </tr>
                            </thead>
                            <tbody>


                                <?php
                                $dates = array();
                                $datess = array();
                                foreach ($payments as $payment) {
                                    $dates[] = $payment->date;
                                }
                                foreach ($deposits as $deposit) {
                                    $datess[] = $deposit->date;
                                }
                                $dat = array_merge($dates, $datess);
                                $dattt = array_unique($dat);
                                asort($dattt);

                                $total_pur = array();

                                $total_p = array();
                                ?>

                                <?php
                                foreach ($dattt as $key => $value) {
                                    foreach ($payments as $payment) {
                                        if ($payment->date == $value) {
                                ?>
                                            <tr class="">
                                                <td><?php echo date('d/m/y', $payment->date); ?></td>
                                                <td> <?php echo $payment->id; ?></td>
                                                <td><?php echo $settings->currency; ?> <?php echo $payment->gross_total; ?></td>
                                                <td><?php
                                                    if (!empty($payment->amount_received)) {
                                                        echo $settings->currency;
                                                    }
                                                    ?> <?php echo $payment->amount_received; ?>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                    <?php
                                    foreach ($deposits as $deposit) {
                                        if ($deposit->date == $value) {
                                            if (!empty($deposit->deposited_amount) && empty($deposit->amount_received_id)) {
                                    ?>

                                                <tr class="">
                                                    <td><?php echo date('d-m-y', $deposit->date); ?></td>
                                                    <td><?php echo $deposit->payment_id; ?></td>
                                                    <td></td>
                                                    <td><?php echo $settings->currency; ?> <?php echo $deposit->deposited_amount; ?>
                                                    </td>
                                                </tr>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-lg-8 invoice-block float-right total_section">
                                <ul class="unstyled amounts">
                                    <li><strong><?php echo lang('grand_total'); ?> :
                                        </strong><?php echo $settings->currency; ?>
                                        <?php echo $total_payable_bill = $total_bill; ?></li>
                                    <li><strong><?php echo lang('amount_received'); ?> :
                                        </strong><?php echo $settings->currency; ?>
                                        <?php echo array_sum($total_deposit); ?></li>
                                    <li><strong><?php echo lang('amount_to_be_paid'); ?> :
                                        </strong><?php echo $settings->currency; ?>
                                        <?php echo $total_payable_bill - array_sum($total_deposit); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="card col-md-3 no-print">
                            <a class="btn btn-sm btn-secondary invoice_button" onclick="javascript:window.print();"><i class="fa fa-print"></i> <?php echo lang('print'); ?> </a>
                        </div>

                        <div class="col-md-3 text-center invoice-btn clearfix down no-print">
                            <a class="btn btn-info btn-sm detailsbutton float-left download" id="download"><i class="fa fa-download"></i> <?php echo lang('download'); ?> </a>
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
</div>

<?php if (!empty($gateway->publish)) {
    $gateway_stripe = $gateway->publish;
} else {
    $gateway_stripe = '';
} ?>



<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>
<script type="text/javascript">
    var publish = "<?php echo $gateway_stripe; ?>";
</script>
<script src="common/js/moment.min.js"></script>

<script type="text/javascript">
    var payment_gateway = "<?php echo $settings->payment_gateway; ?>";
</script>

<script src="common/extranal/js/finance/patient_deposit.js"></script>