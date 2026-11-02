<link href="common/extranal/css/finance/user_activity_report.css" rel="stylesheet">

<div class="content-wrapper bg-light">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row my-2 pl-1">
                <div class="col-sm-6">
                    <h1 class="font-weight-bold"><i class="fas fa-chart-line mr-2"></i><?php echo lang('activities_by'); ?> <strong class="activities_by"><?php echo $user->name; ?></strong>
                        ( <?php
                            if (!empty($date_from)) {
                                echo lang('from') . ' ' . date('m/d/Y', $date_from) . ' ';
                            }

                            if (!empty($date_to)) {
                                echo lang('to') . ' ' . date('m/d/Y', $date_to);
                            }

                            if (!empty($day)) {
                                echo lang(strtolower($day));
                            }
                            ?> )
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home"><?php echo lang('home') ?></a></li>
                        <li class="breadcrumb-item active"><?php echo lang('department') ?></li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-8">
                    <div class="card">
                        <div class="card-header">
                            <!-- <h3 class="card-title">All the department names and related informations</h3> -->

                            <div class="row">
                                <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                                    <section class="col-md-4 no-print">
                                        <div class="float-right">
                                            <a href="finance/allUserActivityReport?user=<?php echo $user->ion_user_id; ?>">
                                                <div class="btn-group">
                                                    <button id="" class="btn btn-sm <?php
                                                                                    if (!empty($day)) {
                                                                                        if ($day == 'Today') {
                                                                                            echo 'btn-success';
                                                                                        }
                                                                                    }
                                                                                    ?>">
                                                        <i class="fa fa-search"></i> <?php echo lang('today'); ?>
                                                    </button>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="float-right">
                                            <a href="finance/allUserActivityReport?user=<?php echo $user->ion_user_id; ?>&yesterday='all'">
                                                <div class="btn-group">
                                                    <button id="" class="btn btn-sm <?php
                                                                                    if (!empty($day)) {
                                                                                        if ($day == 'Yesterday') {
                                                                                            echo 'btn-success';
                                                                                        }
                                                                                    }
                                                                                    ?>">
                                                        <i class="fa fa-search"></i> <?php echo lang('yesterday'); ?>
                                                    </button>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="float-right">
                                            <a href="finance/allUserActivityReport?user=<?php echo $user->ion_user_id; ?>&all='all'">
                                                <div class="btn-group">
                                                    <button id="" class="btn btn-sm <?php
                                                                                    if (!empty($day)) {
                                                                                        if ($day == 'All') {
                                                                                            echo 'btn-success';
                                                                                        }
                                                                                    }
                                                                                    ?>">
                                                        <i class="fa fa-search"></i> <?php echo lang('all'); ?>
                                                    </button>
                                                </div>
                                            </a>
                                        </div>
                                    </section>

                                    <section class="col-md-1 float-right">
                                        <div class="">
                                            <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'user_activity']); ?>
                                        </div>
                                    </section>

                                <?php } ?>

                                <?php if ($this->ion_auth->in_group(array('Accountant', 'Receptionist'))) { ?>
                                    <section class="col-md-5 no-print row float-left">
                                        <div class="float-right">
                                            <a href="finance/UserActivityReport?user=<?php echo $user->ion_user_id; ?>">
                                                <div class="btn-group">
                                                    <button id="" class="btn btn-sm <?php
                                                                                    if (!empty($day)) {
                                                                                        if ($day == 'Today') {
                                                                                            echo 'btn-success';
                                                                                        }
                                                                                    }
                                                                                    ?>">
                                                        <i class="fa fa-search"></i> <?php echo lang('today'); ?>
                                                    </button>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="float-right">
                                            <a href="finance/UserActivityReport?user=<?php echo $user->ion_user_id; ?>&yesterday='all'">
                                                <div class="btn-group">
                                                    <button id="" class="btn btn-sm <?php
                                                                                    if (!empty($day)) {
                                                                                        if ($day == 'Yesterday') {
                                                                                            echo 'btn-success';
                                                                                        }
                                                                                    }
                                                                                    ?>">
                                                        <i class="fa fa-search"></i> Yesterday
                                                    </button>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="float-right">
                                            <a href="finance/UserActivityReport?user=<?php echo $user->ion_user_id; ?>&all='all'">
                                                <div class="btn-group">
                                                    <button id="" class="btn btn-sm <?php
                                                                                    if (!empty($day)) {
                                                                                        if ($day == 'All') {
                                                                                            echo 'btn-success';
                                                                                        }
                                                                                    }
                                                                                    ?>">
                                                        <i class="fa fa-search"></i> <?php echo lang('all'); ?>
                                                    </button>
                                                </div>
                                            </a>
                                        </div>
                                    </section>

                                    <section class="col-md-1 float-right">
                                        <div class="">
                                            <button class="btn btn-sm btn-success' no-print float-right" onclick="javascript:window.print();"><?php echo lang('print'); ?></button>
                                        </div>
                                    </section>


                                <?php } ?>
                            </div>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if ($this->ion_auth->in_group(array('Accountant', 'Receptionist'))) { ?>


                                        <section class="col-md-12 no-print">
                                            <form role="form" class="f_report" action="finance/userActivityReportDateWise" method="post" enctype="multipart/form-data">
                                                <!--     <label class="control-label col-md-3">Date Range</label> -->

                                                <div class="form-row">
                                                    <div class="col-md-6">
                                                        <div class="input-group input-large" data-date="13/07/2013" data-date-format="mm/dd/yyyy">
                                                            <input type="text" class="form-control dpd1" name="date_from" value="<?php
                                                                                                                                    if (!empty($date_from)) {
                                                                                                                                        echo date('m/d/Y', $date_from);
                                                                                                                                    }
                                                                                                                                    ?>" placeholder="<?php echo lang('date_from'); ?>" readonly="">
                                                            <span class="input-group-addon"><?php echo lang('to'); ?></span>
                                                            <input type="text" class="form-control dpd2" name="date_to" value="<?php
                                                                                                                                if (!empty($date_to)) {
                                                                                                                                    echo date('m/d/Y', $date_to);
                                                                                                                                }
                                                                                                                                ?>" placeholder="<?php echo lang('date_to'); ?>" readonly="">
                                                            <input type="hidden" class="form-control dpd2" name="user" value="<?php echo $user->ion_user_id; ?>">
                                                        </div>
                                                        <div class="row"></div>
                                                        <span class="help-block"></span>
                                                    </div>
                                                    <div class="col-md-6 no-print">
                                                        <button type="submit" name="submit" class="btn btn-info range_submit"><?php echo lang('submit'); ?></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </section>

                                    <?php } ?>




                                    <?php if ($this->ion_auth->in_group(array('admin'))) { ?>

                                        <section class="col-md-12 no-print mb-3">
                                            <form role="form" class="f_report" action="finance/allUserActivityReportDateWise" method="post" enctype="multipart/form-data">
                                                <div class="form-row">
                                                    <!--     <label class="control-label col-md-3">Date Range</label> -->
                                                    <div class="col-md-6">
                                                        <div class="input-group input-large" data-date="13/07/2013" data-date-format="mm/dd/yyyy">
                                                            <input type="text" class="form-control dpd1" name="date_from" value="<?php
                                                                                                                                    if (!empty($date_from)) {
                                                                                                                                        echo date('m/d/Y', $date_from);
                                                                                                                                    }
                                                                                                                                    ?>" placeholder="<?php echo lang('date_from'); ?>" readonly="">
                                                            <span class="input-group-addon"><?php echo lang(''); ?></span>
                                                            <input type="text" class="form-control dpd2" name="date_to" value="<?php
                                                                                                                                if (!empty($date_to)) {
                                                                                                                                    echo date('m/d/Y', $date_to);
                                                                                                                                }
                                                                                                                                ?>" placeholder="<?php echo lang('date_to'); ?>" readonly="">
                                                            <input type="hidden" class="form-control dpd2" name="user" value="<?php echo $user->ion_user_id; ?>">
                                                        </div>
                                                        <div class="row"></div>
                                                        <span class="help-block"></span>
                                                    </div>
                                                    <div class="col-md-6 no-print">
                                                        <button type="submit" name="submit" class="btn btn-info range_submit"><?php echo lang('submit'); ?></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </section>

                                    <?php } ?>



                                </div>
                                <div class="col-md-12 row">
                                    <div class="adv-table editable-table ">
                                        <header class="panel-heading">
                                            <h4> <?php echo lang('all_bills'); ?> </h4>
                                        </header>
                                        <div class="space15"></div>
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

                                                $total_payment = array();

                                                $total_deposit = array();
                                                ?>

                                                <?php
                                                foreach ($dattt as $key => $value) {
                                                    foreach ($payments as $payment) {
                                                        if ($payment->date == $value) {
                                                            $total_payment[] = $payment->gross_total;
                                                ?>
                                                            <tr class="">
                                                                <td><?php echo date('d/m/y', $payment->date); ?></td>
                                                                <td> <?php echo $payment->id; ?></td>
                                                                <td><?php echo $settings->currency; ?> <?php echo $payment->gross_total; ?></td>
                                                                <td><?php
                                                                    if (!empty($payment->amount_received)) {
                                                                        echo $settings->currency;
                                                                    }
                                                                    ?> <?php echo $payment->amount_received; ?></td>

                                                            </tr>

                                                    <?php
                                                        }
                                                    }
                                                    ?>

                                                    <?php
                                                    foreach ($deposits as $deposit) {
                                                        if ($deposit->date == $value) {
                                                            $total_deposit[] = $deposit->deposited_amount;
                                                            if (!empty($deposit->deposited_amount) && empty($deposit->amount_received_id)) {
                                                    ?>

                                                                <tr class="">
                                                                    <td><?php echo date('d-m-y', $deposit->date); ?></td>
                                                                    <td><?php echo $deposit->payment_id; ?></td>
                                                                    <td></td>
                                                                    <td><?php echo $settings->currency; ?> <?php echo $deposit->deposited_amount; ?></td>

                                                                </tr>
                                                    <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                <?php } ?>

                                                <?php
                                                if (!empty($total_payment)) {
                                                    $total_p = array_sum($total_payment);
                                                } else {
                                                    $total_p = 0;
                                                }

                                                if (!empty($total_deposit)) {
                                                    $total_d = array_sum($total_deposit);
                                                } else {
                                                    $total_d = 0;
                                                }
                                                ?>

                                                <tr class="total">
                                                    <td></td>
                                                    <td> <strong> <?php echo lang('total'); ?> </strong></td>
                                                    <td> <strong> <?php echo $settings->currency; ?> <?php echo $total_p; ?> </strong></td>
                                                    <td> <strong> <?php echo $settings->currency; ?> <?php echo $total_d; ?> </strong></td>

                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Summary</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">


                                <div class="col-md-12">


                                    <?php
                                    $total_bill = array();
                                    foreach ($payments as $payment) {
                                        $total_bill[] = $payment->gross_total;
                                    }
                                    if (!empty($total_bill)) {
                                        $total_bill = array_sum($total_bill);
                                    } else {
                                        $total_bill = 0;
                                    }
                                    ?>

                                    <?php
                                    $total_bill_ot = array();
                                    if (!empty($total_bill_ot)) {
                                        $total_bill_ot = array_sum($total_bill_ot);
                                    } else {
                                        $total_bill_ot = 0;
                                    }
                                    ?>




                                    <section class="panel">
                                        <div class="weather-bg">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-xs-4">
                                                        <i class="fa fa-money"></i>
                                                        <?php echo lang('total_bill_amount'); ?>

                                                    </div>
                                                    <div class="col-xs-8">
                                                        <div class="degree">
                                                            <?php echo $settings->currency; ?>
                                                            <?php echo $total_payable_bill = $total_bill + $total_bill_ot; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <section class="panel">
                                        <div class="weather-bg">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-xs-4">
                                                        <i class="fa fa-money"></i>
                                                        <?php echo lang('total_deposit_amount'); ?>
                                                    </div>
                                                    <div class="col-xs-8">
                                                        <div class="degree">
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
                                        </div>
                                    </section>

                                    <section class="panel red due_div">
                                        <div class="weather-bg">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-xs-4">
                                                        <i class="fa fa-money"></i>
                                                        <?php echo lang('due_amount'); ?>
                                                    </div>
                                                    <div class="col-xs-8">
                                                        <div class="degree">
                                                            <?php echo $settings->currency; ?>
                                                            <?php
                                                            echo $total_payable_bill - array_sum($total_deposit);
                                                            ?>
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



<!--main content end-->
<!--footer start-->

<script src="common/js/codearistos.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.dpd1, .dpd2').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            showMeridian: true
        });
    });
</script>