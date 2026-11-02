<!--main content start-->

<link href="common/extranal/css/finance/financial_report.css" rel="stylesheet">

<style>
    .card .badge {
        font-size: 120%;
        margin-bottom: 5px;
        width: 100%;
        text-align: left !important;
        /* background: #fff !important; */
        /* color: #333; */
        padding: 16px;
    }
</style>

<div class="content-wrapper bg-light">
    <!-- Content Header (Page header) -->
    <section class="content-header py-4 mb-5 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-money-bill-wave text-primary mr-3"></i>
                        <?php echo lang('financial_report'); ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('financial_report'); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- <div class="card-header">
                            <h3 class="card-title">All the department names and related informations</h3>
                            <div class="float-right">
                                <a data-toggle="modal" href="#myModal">
                                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'financial']); ?>
                                </a>
                            </div>
                        </div> -->
                        <!-- /.card-header -->


                        <div class="card-body">
                            <div class="">
                                <div class="mb-4">
                                    <section>
                                        <form role="form" class="f_report" action="finance/financialReport" method="post" enctype="multipart/form-data">
                                            <div class="form-group row">

                                                <div class="col-md-6">
                                                    <div class="input-group input-large" data-date="13/07/2013" data-date-format="" style="z-index: 9999;">
                                                        <input type="text" class="form-control form-control-lg text-md dpd1" name="date_from" value="<?php
                                                                                                                                                        if (!empty($from)) {
                                                                                                                                                            echo $from;
                                                                                                                                                        }
                                                                                                                                                        ?>" placeholder="<?php echo lang('date_from'); ?>" readonly="">
                                                        <span class="input-group-addon"></span>
                                                        <input type="text" class="form-control form-control-lg text-md dpd2" name="date_to" value="<?php
                                                                                                                                                    if (!empty($to)) {
                                                                                                                                                        echo $to;
                                                                                                                                                    }
                                                                                                                                                    ?>" placeholder="<?php echo lang('date_to'); ?>" readonly="">
                                                    </div>
                                                    <div class="row"></div>
                                                    <span class="help-block"></span>
                                                </div>
                                                <div class="col-md-6 no-print">
                                                    <button type="submit" name="submit" class="btn btn-lg btn-info range_submit py-3 px-5"><?php echo lang('submit'); ?></button>
                                                </div>
                                            </div>
                                        </form>
                                    </section>
                                </div>
                            </div>

                            <?php
                            if (!empty($payments)) {
                                $paid_number = 0;
                                foreach ($payments as $payment) {
                                    $paid_number = $paid_number + 1;
                                }
                            }
                            ?>
                            <div class="row">
                                <div class="col-lg-7">
                                    <section class="card">
                                        <header class="card-header">
                                            <h4> <?php echo lang('income'); ?> </h4>
                                        </header>
                                        <table class="table table-striped table-advance table-hover">
                                            <thead>
                                                <tr>
                                                    <th> <?php echo lang('category'); ?></th>
                                                    <th> <?php echo lang('quantity'); ?></th>
                                                    <th class="hidden-phone"> <?php echo lang('amount'); ?></th>

                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                                $category_id_for_report = array();
                                                foreach ($payment_categories as $cat_name) {
                                                    foreach ($payments as $payment) {
                                                        $categories_in_payment = explode(',', $payment->category_name);
                                                        foreach ($categories_in_payment as $key => $category_in_payment) {
                                                            $category_id = explode('*', $category_in_payment);
                                                            if ($category_id[0] == $cat_name->id) {
                                                                $category_id_for_report[] = $category_id[0];
                                                            }
                                                        }
                                                    }
                                                }
                                                $category_id_for_reports = array_unique($category_id_for_report);
                                                ?>

                                                <?php
                                                foreach ($payment_categories as $category) {

                                                    $category_quantity = array();
                                                    if (in_array($category->id, $category_id_for_reports)) {
                                                ?>
                                                        <tr class="">
                                                            <td><?php echo $category->category ?></td>
                                                            <td>


                                                                <?php
                                                                foreach ($payments as $paymentt) {
                                                                    $category_names_and_amountss = $paymentt->category_name;
                                                                    $category_names_and_amountss = explode(',', $category_names_and_amountss);
                                                                    foreach ($category_names_and_amountss as $category_name_and_amountt) {
                                                                        $category_namee = explode('*', $category_name_and_amountt);
                                                                        if (($category->id == $category_namee[0])) {
                                                                            $category_quantity[] = $category_namee[3];
                                                                        }
                                                                    }
                                                                }
                                                                if (!empty($category_quantity)) {
                                                                    echo array_sum($category_quantity);
                                                                } else {
                                                                    echo '0';
                                                                }
                                                                ?>




                                                            </td>
                                                            <td><?php echo $settings->currency; ?> <?php
                                                                                                    foreach ($payments as $payment) {
                                                                                                        $category_names_and_amounts = $payment->category_name;
                                                                                                        $category_names_and_amounts = explode(',', $category_names_and_amounts);
                                                                                                        foreach ($category_names_and_amounts as $category_name_and_amount) {
                                                                                                            $category_name = explode('*', $category_name_and_amount);
                                                                                                            if (($category->id == $category_name[0])) {
                                                                                                                $amount_per_category[] = $category_name[1] * $category_name[3];
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                    if (!empty($amount_per_category)) {
                                                                                                        echo array_sum($amount_per_category);
                                                                                                        $total_payment_by_category[] = array_sum($amount_per_category);
                                                                                                    } else {
                                                                                                        echo '0';
                                                                                                    }

                                                                                                    $amount_per_category = NULL;
                                                                                                    ?>
                                                            </td>
                                                        </tr>
                                                <?php
                                                    }
                                                }
                                                ?>









                                                <tr class="">
                                                    <td><?php echo lang('appointment') ?></td>
                                                    <td>
                                                        <?php
                                                        foreach ($payments as $paymentt) {
                                                            if (($paymentt->payment_from == 'Consultant Fee')) {
                                                                $category_quantity[] = 1;
                                                            }
                                                        }
                                                        if (!empty($category_quantity)) {
                                                            echo array_sum($category_quantity);
                                                        } else {
                                                            echo '0';
                                                        }
                                                        ?>
                                                    </td>

                                                    <td>
                                                        <?php echo $settings->currency; ?>
                                                        <?php
                                                        foreach ($payments as $payment) {
                                                            if (($payment->category_name == 'Consultant Fee')) {
                                                                $amount_per_category[] = $payment->amount;
                                                            }
                                                        }
                                                        if (!empty($amount_per_category)) {
                                                            echo array_sum($amount_per_category);
                                                            $total_payment_by_category_for_appointment = array_sum($amount_per_category);
                                                        } else {
                                                            echo '0';
                                                        }

                                                        $amount_per_category = NULL;
                                                        ?>

                                                    </td>
                                                </tr>

















                                            </tbody>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <h3><?php echo lang('sub_total'); ?> </h3>
                                                    </td>
                                                    <td></td>
                                                    <td>
                                                        <?php echo $settings->currency; ?>
                                                        <?php

                                                        echo $total_sub_total = (isset($total_payment_by_category) ? array_sum($total_payment_by_category) : 0) + (isset($total_payment_by_category_for_appointment) ? $total_payment_by_category_for_appointment : 0);

                                                        ?>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <h5><?php echo lang('total'); ?> <?php echo lang('vat'); ?></h5>
                                                    </td>
                                                    <td></td>
                                                    <td>
                                                        <?php echo $settings->currency; ?>
                                                        <?php
                                                        if (!empty($payments)) {
                                                            foreach ($payments as $payment) {
                                                                $vat[] = $payment->vat;
                                                            }
                                                            if ($paid_number > 0) {
                                                                echo array_sum($vat);
                                                            } else {
                                                                echo '0';
                                                            }
                                                        } else {
                                                            echo '0';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <h5><?php echo lang('total'); ?> <?php echo lang('discount'); ?></h5>
                                                    </td>
                                                    <td></td>
                                                    <td>
                                                        <?php echo $settings->currency; ?>
                                                        <?php
                                                        if (!empty($payments)) {
                                                            foreach ($payments as $payment) {
                                                                $discount[] = $payment->flat_discount;
                                                            }
                                                            if ($paid_number > 0) {
                                                                echo array_sum($discount);
                                                            } else {
                                                                echo '0';
                                                            }
                                                        } else {
                                                            echo '0';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <h5> <?php echo lang('gross_income'); ?></h5>
                                                    </td>
                                                    <td></td>
                                                    <td>
                                                        <?php echo $settings->currency; ?>
                                                        <?php
                                                        if (!empty($payments)) {
                                                            if ($paid_number > 0) {
                                                                $gross = $total_sub_total - array_sum($discount) + array_sum($vat);
                                                                echo $gross;
                                                            } else {
                                                                echo '0';
                                                            }
                                                        } else {
                                                            echo '0';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h5><?php echo lang('total'); ?> <?php echo lang('hospital_amount'); ?></h5>
                                                    </td>
                                                    <td></td>
                                                    <td>
                                                        <?php echo $settings->currency; ?>
                                                        <?php
                                                        if (!empty($payments)) {
                                                            foreach ($payments as $payment) {
                                                                $hospital_amount[] = $payment->hospital_amount;
                                                            }
                                                            if ($paid_number > 0) {
                                                                $hospital_amount = array_sum($hospital_amount);
                                                                echo $hospital_amount;
                                                            } else {
                                                                echo '0';
                                                            }
                                                        } else {
                                                            echo '0';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h5><?php echo lang('total'); ?> <?php echo lang('doctors_amount'); ?></h5>
                                                    </td>
                                                    <td></td>
                                                    <td>
                                                        <?php echo $settings->currency; ?>
                                                        <?php
                                                        if (!empty($payments)) {
                                                            foreach ($payments as $payment) {
                                                                $doctor_amount[] = $payment->doctor_amount;
                                                            }
                                                            if ($paid_number > 0) {
                                                                $gross_doctor_amount = array_sum($doctor_amount);
                                                                echo $gross_doctor_amount;
                                                            } else {
                                                                echo '0';
                                                            }
                                                        } else {
                                                            echo '0';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </section>




                                    <section></section>


                                    <section class="card">
                                        <header class="card-header">
                                            <h4> <?php echo lang('expense'); ?> </h4>
                                        </header>
                                        <table class="table table-striped table-advance table-hover">
                                            <thead>
                                                <tr>
                                                    <th> <?php echo lang('category'); ?></th>
                                                    <th class="hidden-phone"> <?php echo lang('amount'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($expense_categories as $category) { ?>
                                                    <tr class="">
                                                        <td><?php echo $category->category ?></td>
                                                        <td>
                                                            <?php echo $settings->currency; ?>
                                                            <?php
                                                            foreach ($expenses as $expense) {
                                                                $category_name = $expense->category;


                                                                if (($category->category == $category_name)) {
                                                                    $amount_per_category[] = $expense->amount;
                                                                }
                                                            }
                                                            if (!empty($amount_per_category)) {
                                                                $total_expense_by_category[] = array_sum($amount_per_category);
                                                                echo array_sum($amount_per_category);
                                                            } else {
                                                                echo '0';
                                                            }

                                                            $amount_per_category = NULL;
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>

                                            </tbody>
                                        </table>
                                    </section>
                                </div>

                                <div class="col-lg-5">
                                    <section class="card">
                                        <header class="card-header">
                                            <h4><?php echo lang('summary'); ?></h4>
                                        </header>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-primary"><i class="fas fa-money-bill-wave"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?php echo lang('sub_total'); ?></span>
                                                            <span class="info-box-number"><?php echo $settings->currency; ?> <?php echo !empty($total_sub_total) ? number_format($total_sub_total, 2) : '0.00'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-warning"><i class="fas fa-percent"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?php echo lang('gross'); ?> <?php echo lang('vat'); ?></span>
                                                            <span class="info-box-number"><?php echo $settings->currency; ?> <?php echo !empty($vat) ? number_format(array_sum($vat), 2) : '0.00'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-success"><i class="fas fa-tags"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?php echo lang('discount'); ?></span>
                                                            <span class="info-box-number"><?php echo $settings->currency; ?> <?php echo !empty($discount) ? number_format(array_sum($discount), 2) : '0.00'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-info"><i class="fas fa-file-invoice-dollar"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?php echo lang('gross_bill'); ?></span>
                                                            <span class="info-box-number"><?php echo $settings->currency; ?> <?php echo !empty($gross) ? number_format($gross, 2) : '0.00'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-secondary"><i class="fas fa-hospital"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?php echo lang('gross_hospital_amount'); ?></span>
                                                            <span class="info-box-number"><?php echo $settings->currency; ?> <?php echo !empty($payments) && $paid_number > 0 ? number_format($hospital_amount, 2) : '0.00'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-secondary"><i class="fas fa-user-md"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?php echo lang('gross_doctors_commission'); ?></span>
                                                            <span class="info-box-number"><?php echo $settings->currency; ?> <?php echo !empty($gross_doctor_amount) || !empty($gross_doctor_amount_ot) ? number_format($gross_doctor_amount + $gross_doctor_amount_ot, 2) : '0.00'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-info"><i class="fas fa-hand-holding-usd"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?php echo lang('gross_deposit'); ?></span>
                                                            <span class="info-box-number"><?php echo $settings->currency; ?> <?php echo !empty($deposits) ? number_format(array_sum(array_column($deposits, 'deposited_amount')), 2) : '0.00'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?php echo lang('gross_due'); ?></span>
                                                            <span class="info-box-number">
                                                                <?php
                                                                $total_bill = !empty($gross) ? $gross : 0;
                                                                $total_deposit = !empty($deposits) ? array_sum(array_column($deposits, 'deposited_amount')) : 0;
                                                                $due = $total_bill - $total_deposit;
                                                                echo $settings->currency . ' ' . number_format($due, 2);
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-box">
                                                        <span class="info-box-icon bg-danger"><i class="fas fa-money-bill-wave"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text"><?php echo lang('gross_expense'); ?></span>
                                                            <span class="info-box-number"><?php echo $settings->currency; ?> <?php echo !empty($total_expense_by_category) ? number_format(array_sum($total_expense_by_category), 2) : '0.00'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- /.content -->
</div>


<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = "<?php echo $this->language; ?>";
</script>

<script>
    var dateFormat = "<?php echo $date_format; ?>";

    // Initialize datepicker with dynamic format
    // $('.dpd1, .dpd2').datepicker({
    //     format: dateFormat
    // });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.dpd1, .dpd2').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayBtn: true,
            showMeridian: true,
            language: langdate,
        });
    });
</script>






<!--main content end-->
<!--footer start-->