<script type="text/javascript" src="common/js/google-loader.js"></script>
<div class="content-wrapper bg-light home-dashboard-tight">

    <section class="content-header">
        <div class="container-fluid mt-2 pt-1">
            <div class="row my-1 pl-1">
                <div class="col-sm-6 pl-2 pl-sm-3">
                    <div class="welcome-text-container">
                        <?php
                        $user_id = $this->ion_auth->user()->row()->id;
                        $user_image = $this->db->get_where('users', array('id' => $user_id))->row()->img_url;
                        $default_image = 'uploads/default.png';
                        ?>
                        <div class="d-flex align-items-center">
                            <div class="user-image-container mr-2 mr-sm-3">
                                <img src="<?php echo $user_image ? base_url() . $user_image : base_url() . $default_image; ?>"
                                    alt="User Image"
                                    class="img-circle elevation-2"
                                    style="width: 48px; height: 48px; object-fit: cover;">
                            </div>
                            <div>
                                <h1 class="h4 font-weight-bold welcome-text title-spacing mb-0">
                                    <?php echo lang('welcome') ?>, 
                                    <?php
                                    $username = $this->ion_auth->user()->row()->username;
                                    if (!empty($username)) {
                                        echo $username;
                                    }
                                    ?>!
                                </h1> 
                                <p class="welcome-text mt-1 mb-0 small text-muted">Welcome to the dashboard of <?php echo $settings->title ?? ''; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 address-text">

                    <?php if ($this->ion_auth->in_group('admin')): ?>
                        <div class="d-flex justify-content-end">
                            <a href="finance/addPaymentView" class="btn btn-primary btn-sm mr-1 mr-sm-2 px-3 py-1">
                                <i class="fa fa-plus mr-1"></i> <?php echo lang('new_invoice'); ?>
                            </a>
                            <a href="patient/addNewView" class="btn btn-success btn-sm mr-1 mr-sm-2 px-3 py-1">
                                <i class="fa fa-plus mr-1"></i> <?php echo lang('add_patient'); ?>
                            </a>
                        </div>
                    <?php endif; ?>


                </div>
            </div>
        </div>
    </section>




    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="<?php if ($this->ion_auth->in_group(array('admin'))) {
                                echo 'col-md-6 col-lg-6 col-12';
                            } else {
                                echo 'col-12';
                            } ?>">
                    <div class="">
                        <div class="modal fade" role="dialog" id="cmodal">
                            <div class="modal-dialog modal-xl header_modal" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-file-medical mr-2"></i>
                                            <?php echo lang('medical_history'); ?>
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div id='medical_history' class="row">
                                            <div class="col-md-12">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                <i class="fas fa-times mr-1"></i>Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <?php if ($this->ion_auth->in_group(array('Doctor'))) { ?>
                            <?php if (in_array('appointment', $this->modules)) { ?>
                                <div class="row state-overview col-md-5 state_overview_design">
                                    <header class="card-header">
                                        <i class="fa fa-user"></i> <?php echo lang('todays_appointments'); ?>
                                    </header>
                                    <div class="card-body">
                                        <div class="adv-table editable-table ">
                                            <div class="space15"></div>
                                            <table class="table table-striped table-hover table-bordered" id="editable-samplee">
                                                <thead>
                                                    <tr>
                                                        <th> <?php echo lang('patient_id'); ?></th>
                                                        <th> <?php echo lang('name'); ?></th>
                                                        <th> <?php echo lang('date-time'); ?></th>
                                                        <th> <?php echo lang('status'); ?></th>
                                                        <th> <?php echo lang('options'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($appointments as $appointment) {
                                                        if ($appointment->date == strtotime(date('Y-m-d'))) {
                                                    ?>
                                                            <tr class="">
                                                                <td> <?php echo $this->db->get_where('patient', array('id' => $appointment->patient))->row()->id; ?></td>
                                                                <td> <?php echo $this->db->get_where('patient', array('id' => $appointment->patient))->row()->name; ?></td>

                                                                <td class="center"> <strong> <?php echo $appointment->s_time; ?> </strong></td>
                                                                <td>
                                                                    <?php echo $appointment->status; ?>
                                                                </td>
                                                                <td>
                                                                    <a class="btn detailsbutton" title="<?php lang('history') ?>" href="patient/medicalHistory?id=<?php echo $appointment->patient ?>"><i class="fa fa-stethoscope"></i> <?php echo lang('history'); ?></a>
                                                                </td>
                                                            </tr>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                        <?php } ?>
                        <?php if (!$this->ion_auth->in_group('superadmin')) { ?>
                            <?php if (!$this->ion_auth->in_group('Doctor')) { ?>


                                <style>
                                    .card-custom {
                                        border: none;
                                        border-radius: 0.5rem;
                                        transition: transform 0.2s ease-in-out;
                                    }

                                    .card-custom:hover {
                                        /* transform: scale(1.05); */
                                    }

                                    .card-body-custom {
                                        /* padding: 0 rem; */
                                    }

                                    .icon-custom {
                                        font-size: 3rem;
                                        color: rgba(0, 0, 0, 0.1);
                                        font-size: 2rem;
                                    }

                                    .inner-custom {
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                    }

                                    .text-custom {
                                        font-size: 1.25rem;
                                        font-weight: bold;
                                    }

                                    .stat-number {
                                        font-size: 2rem;
                                        font-weight: bold;
                                    }

                                    .stat-text {
                                        font-size: 1rem;
                                    }



                                    .analytics {
                                        background: #f8f9fa !important;
                                    }
                                </style>

                                <div class="container-fluid pr-0" style="margin-top: 0;">
                                    <div class="row g-2 mt-1 mb-1">
                                        <style>
                                            .stat-card {
                                                background-color: hsl(00, 0%, 100%);
                                                border-radius: 5px;
                                                transition: all 0.3s ease;
                                                overflow: hidden;
                                            }



                                            .stat-card a {
                                                text-decoration: none;
                                            }

                                            .stat-card-body {
                                                padding: 0.65rem 0.75rem;
                                                position: relative;
                                            }

                                            .stat-number {
                                                font-size: clamp(1rem, 2vw, 2.75rem);
                                                font-weight: 700;
                                                color: #555;
                                                margin-bottom: 0.35rem;
                                                word-break: break-word;
                                            }

                                            .stat-text {
                                                color: #555;
                                                word-wrap: break-word;
                                            }

                                            @media screen and (min-width: 1600px) and (max-width: 1920px) {
                                                .stat-number {
                                                    font-size: clamp(1.5rem, 2.5vw, 2.5rem);
                                                }

                                                .stat-text {
                                                    font-size: clamp(0.8rem, 1vw, 1.1rem);
                                                }
                                            }

                                            @media screen and (min-width: 1300px) and (max-width: 1599px) {
                                                .stat-number {
                                                    font-size: clamp(1.5rem, 4vw, 2.5rem);
                                                }

                                                .stat-text {
                                                    font-size: 13px;
                                                }

                                                .card-custom {
                                                    font-size: 1rem;
                                                }
                                            }

                                            @media screen and (min-width: 1200px) and (max-width: 1299px) {
                                                .stat-number {
                                                    font-size: clamp(1.5rem, 4vw, 2.5rem);
                                                }

                                                .stat-text {
                                                    font-size: 10px;
                                                }

                                                .card-custom {
                                                    font-size: 1rem;
                                                }
                                            }


                                            @media screen and (min-width: 768px) and (max-width: 1199px) {
                                                .stat-number {
                                                    font-size: clamp(1.5rem, 4vw, 2.5rem);
                                                }

                                                .stat-text {
                                                    font-size: 8px;
                                                }
                                            }




                                            @media screen and (max-width: 767px) {
                                                .stat-number {
                                                    font-size: 3rem;
                                                }

                                                .stat-text {
                                                    font-size: 1rem;
                                                }
                                            }

                                            .stat-icon {
                                                font-size: clamp(2rem, 4vw, 3.5rem);
                                                color: rgba(255, 255, 255, 0.2);
                                                position: absolute;
                                                bottom: 1rem;
                                                right: 1rem;
                                                opacity: 0.2;



                                            }

                                            @media (max-width: 768px) {
                                                .stat-card-body {
                                                    padding: 0.75rem;
                                                }

                                                .stat-icon {
                                                    font-size: 2rem;
                                                    bottom: 0.5rem;
                                                    right: 0.5rem;
                                                }

                                                .welcome-text {
                                                    font-size: 1.2rem !important;
                                                }

                                                .address-text {
                                                    text-align: center;
                                                    display: flex;
                                                    justify-content: center;
                                                    align-items: center;
                                                    margin-top: 1rem;

                                                }

                                                .logs-row {
                                                    display: none;
                                                }



                                            }
                                        </style>



                                        <?php if (in_array('finance', $this->modules)) { ?>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="card stat-card h-100 shadow-sm custom-rounded text-center">
                                                    <a href="finance/payment">
                                                        <div class="card-body stat-card-body">

                                                            <p class="badge badge-primary text-sm mb-0 bg-transparent"><?php echo lang('total'); ?> <?php echo lang('bill'); ?></p> <br>
                                                            <span class="sub-text fw-bold"><?php echo lang('this_month'); ?> </span>
                                                            <h2 class="dashboard-title text-primary"><?php echo $settings->currency ?? ''; ?><?php echo format_number_short($this_month['payment'] ?? 0); ?></h2>
                                                            <div class="percentage-change text-xs">
                                                                <?php
                                                                $percentage_change_bill = isset($percentage_change_bill) ? $percentage_change_bill : 0;
                                                                if ($percentage_change_bill > 0): ?>
                                                                    <span class="text-success"><i class="fas fa-arrow-up"></i> <?php echo $percentage_change_bill; ?>%</span>
                                                                <?php elseif ($percentage_change_bill < 0): ?>
                                                                    <span class="text-danger"><i class="fas fa-arrow-down"></i> <?php echo $percentage_change_bill; ?>%</span>
                                                                <?php else: ?>
                                                                    <span><?php echo $percentage_change_bill; ?>%</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>



                                        <?php if (in_array('finance', $this->modules)) { ?>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="card stat-card h-100 shadow-sm custom-rounded text-center">
                                                    <a href="finance/payment">
                                                        <div class="card-body stat-card-body">
                                                            <p class="badge badge-primary text-sm mb-0 bg-transparent"><?php echo lang('total'); ?> <?php echo lang('deposit'); ?></p><br>
                                                            <span class="sub-text fw-bold"><?php echo lang('this_month'); ?> </span>
                                                            <h2 class="dashboard-title text-success"><?php echo $settings->currency ?? ''; ?><?php echo format_number_short($this_month['deposit'] ?? 0, 2, '.', ','); ?></h2>
                                                            <div class="percentage-change text-xs">
                                                                <?php
                                                                $percentage_change_deposit = isset($percentage_change_deposit) ? $percentage_change_deposit : 0;
                                                                if ($percentage_change_deposit > 0): ?>
                                                                    <span class="text-success"><i class="fas fa-arrow-up"></i> <?php echo $percentage_change_deposit; ?>%</span>
                                                                <?php elseif ($percentage_change_deposit < 0): ?>
                                                                    <span class="text-danger"><i class="fas fa-arrow-down"></i> <?php echo $percentage_change_deposit; ?>%</span>
                                                                <?php else: ?>
                                                                    <span><?php echo $percentage_change_deposit; ?>%</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if (in_array('finance', $this->modules)) { ?>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="card stat-card h-100 shadow-sm custom-rounded text-center">
                                                    <a href="finance/dueCollection">
                                                        <div class="card-body stat-card-body">
                                                            <p class="badge badge-primary text-sm mb-0 bg-transparent"><?php echo lang('pending'); ?></p><br>
                                                            <span class="sub-text fw-bold"><?php echo lang('this_month'); ?> </span>
                                                            <h2 class="dashboard-title text-orange"><?php echo $settings->currency ?? ''; ?><?php echo format_number_short($this_month['due'] ?? 0, 2, '.', ','); ?></h2>
                                                            <div class="percentage-change text-xs">

                                                                <?php
                                                                if ($percentage_change_due > 0): ?>
                                                                    <span class="text-success"><i class="fas fa-arrow-up"></i> <?php echo $percentage_change_due; ?>%</span>
                                                                <?php elseif ($percentage_change_due < 0): ?>
                                                                    <span class="text-danger"><i class="fas fa-arrow-down"></i> <?php echo $percentage_change_due; ?>%</span>
                                                                <?php else: ?>
                                                                    <span><?php echo $percentage_change_due; ?>%</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if (in_array('finance', $this->modules)) { ?>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="card stat-card h-100 shadow-sm custom-rounded text-center">
                                                    <a href="finance/expense">
                                                        <div class="card-body stat-card-body">
                                                            <p class="badge badge-primary text-sm mb-0 bg-transparent"><?php echo lang('total'); ?> <?php echo lang('expense'); ?></p><br>
                                                            <span class="sub-text fw-bold"><?php echo lang('this_month'); ?> </span>
                                                            <h2 class="dashboard-title text-info"><?php echo $settings->currency ?? ''; ?><?php echo format_number_short($this_month['expense'] ?? 0, 2, '.', ','); ?></h2>
                                                            <div class="percentage-change text-xs">

                                                                <?php
                                                                $percentage_change_expense = isset($percentage_change_expense) ? $percentage_change_expense : 0;
                                                                if ($percentage_change_expense > 0): ?>
                                                                    <span class="text-success"><i class="fas fa-arrow-up"></i> <?php echo $percentage_change_expense; ?>%</span>
                                                                <?php elseif ($percentage_change_expense < 0): ?>
                                                                    <span class="text-danger"><i class="fas fa-arrow-down"></i> <?php echo $percentage_change_expense; ?>%</span>
                                                                <?php else: ?>
                                                                    <span><?php echo $percentage_change_expense; ?>%</span>
                                                                <?php endif; ?>

                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                                        <?php if (in_array('finance', $this->modules)) { ?>
                                            <div class="row g-2">
                                                <div class="col-lg-12 col-sm-12">
                                                    <div class="card card-custom shadow-sm custom-rounded mx-0 m-1">
                                                        <div class="card-body">
                                                            <h5 class="border-bottom pb-2 py-1 mb-2 text-sm fw-bold"><?php echo lang('sales_vs_expenses') ?> <span class="text-xs badge badge-secondary ml-2 px-2 py-1"><?php echo lang('this_year') ?></span></h5>
                                                            <div id="sales_expense_chart" style="width: 100%; height: 220px;"></div>
                                                            <script type="text/javascript">
                                                                google.charts.load('current', {
                                                                    packages: ['corechart']
                                                                });
                                                                google.charts.setOnLoadCallback(drawSalesExpenseChart);

                                                                function drawSalesExpenseChart() {
                                                                    var data = google.visualization.arrayToDataTable([
                                                                        ['Month', '<?php echo lang('sales'); ?>', '<?php echo lang('expense'); ?>'],
                                                                        ['Jan', <?php echo $this_year['payment_per_month']['january'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['january'] ?? 0; ?>],
                                                                        ['Feb', <?php echo $this_year['payment_per_month']['february'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['february'] ?? 0; ?>],
                                                                        ['Mar', <?php echo $this_year['payment_per_month']['march'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['march'] ?? 0; ?>],
                                                                        ['Apr', <?php echo $this_year['payment_per_month']['april'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['april'] ?? 0; ?>],
                                                                        ['May', <?php echo $this_year['payment_per_month']['may'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['may'] ?? 0; ?>],
                                                                        ['June', <?php echo $this_year['payment_per_month']['june'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['june'] ?? 0; ?>],
                                                                        ['July', <?php echo $this_year['payment_per_month']['july'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['july'] ?? 0; ?>],
                                                                        ['Aug', <?php echo $this_year['payment_per_month']['august'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['august'] ?? 0; ?>],
                                                                        ['Sep', <?php echo $this_year['payment_per_month']['september'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['september'] ?? 0; ?>],
                                                                        ['Oct', <?php echo $this_year['payment_per_month']['october'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['october'] ?? 0; ?>],
                                                                        ['Nov', <?php echo $this_year['payment_per_month']['november'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['november'] ?? 0; ?>],
                                                                        ['Dec', <?php echo $this_year['payment_per_month']['december'] ?? 0; ?>, <?php echo $this_year['expense_per_month']['december'] ?? 0; ?>]
                                                                    ]);

                                                                    var options = {
                                                                        hAxis: {
                                                                            margin: 0,
                                                                            padding: 0
                                                                        },
                                                                        vAxis: {
                                                                            margin: 0,
                                                                            padding: 0
                                                                        },
                                                                        chartArea: { // Reduce chartArea margins
                                                                            left: 44,
                                                                            right: 44,
                                                                            top: 36,
                                                                            bottom: 14,
                                                                            width: '100%',
                                                                            height: '80%' // Use more space for the chart itself
                                                                        },
                                                                        seriesType: 'area',
                                                                        series: {
                                                                            0: {
                                                                                type: 'area'
                                                                            },
                                                                            1: {
                                                                                type: 'area'
                                                                            }
                                                                        },
                                                                        colors: ['#FAD380', '#FD6B01'],


                                                                        backgroundColor: 'transparent',
                                                                        titleTextStyle: {
                                                                            color: $('body').hasClass('dark-mode') ? '#FFFFFF' : '#333333'
                                                                        },
                                                                        legendTextStyle: {
                                                                            color: $('body').hasClass('dark-mode') ? '#FFFFFF' : '#333333'
                                                                        },
                                                                        vAxis: {
                                                                            textStyle: {
                                                                                color: $('body').hasClass('dark-mode') ? '#FFFFFF' : '#333333'
                                                                            }
                                                                        },
                                                                        hAxis: {
                                                                            textStyle: {
                                                                                color: $('body').hasClass('dark-mode') ? '#FFFFFF' : '#333333'
                                                                            }
                                                                        }




                                                                    };

                                                                    var chart = new google.visualization.ComboChart(document.getElementById('sales_expense_chart'));
                                                                    chart.draw(data, options);
                                                                }
                                                            </script>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>

                                    <?php if ($this->ion_auth->in_group(array('Nurse'))) { ?>
                                        <?php if (in_array('notice', $this->modules)) { ?>
                                            <div class="row g-2 mt-2">
                                                <div class="col-md-7 col-sm-12">
                                                    <div class="card card-custom shadow-sm custom-rounded m-1">
                                                        <div class="card-header badge-secondary text-white">
                                                            <h5 class="mb-0"><?php echo lang('notice'); ?></h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-bordered table-hover" id="editable-sample">
                                                                <thead>
                                                                    <tr>
                                                                        <th><?php echo lang('title'); ?></th>
                                                                        <th><?php echo lang('description'); ?></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($notices as $notice) { ?>
                                                                        <tr>
                                                                            <td><?php echo $notice->title; ?></td>
                                                                            <td><?php echo $notice->description; ?></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                            <div class="d-flex justify-content-between">
                                                                <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                                                                    <a class="btn btn-success btn-sm" href="notice/addNewView"><?php echo lang('add'); ?> <?php echo lang('notice'); ?></a>
                                                                <?php } ?>
                                                                <a class="btn btn-success btn-sm my-3" href="notice"><?php echo lang('all'); ?> <?php echo lang('notice'); ?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>


                                    <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                                        <div class="row logs-row">
                                            <div class="col-md-12">
                                                <div class="card card-custom shadow-sm custom-rounded m-1">
                                                    <div class="card-header py-2">
                                                        <h5 class="mb-0 font-weight-bold text-sm"><?php echo lang('latest_logs'); ?></h5>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <div class="list-group">
                                                            <?php
                                                            $this->load->model('logs/logs_model');
                                                            $latest_logs = $this->logs_model->getLogsByLimit(5, 0, 'id', 'desc');
                                                            foreach ($latest_logs as $log) { ?>
                                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <h6 class="mb-1 fw-bold"><?php echo $log->name; ?></h6>
                                                                        <small class="text-muted"><?php echo $log->role; ?></small>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($log->date_time)); ?></small>
                                                                        <div><?php echo $log->email; ?></div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer badge-white">
                                                        <div class="d-flex justify-content-end">
                                                            <a class="btn btn-info btn-sm" href="logs"><?php echo lang('all'); ?> <?php echo lang('logs'); ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-2 logs-row">
                                            <div class="col-md-12">
                                                <div class="card card-custom shadow-sm custom-rounded m-1">
                                                    <div class="card-header py-2">
                                                        <h5 class="mb-0 font-weight-bold text-sm"><?php echo lang('latest_transaction_logs'); ?></h5>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover mb-0">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th class="border-top-0"><?php echo lang('time'); ?></th>
                                                                        <th class="border-top-0 text-right"><?php echo lang('invoice'); ?></th>
                                                                        <th class="border-top-0 text-right"><?php echo lang('patient'); ?></th>
                                                                        <th class="border-top-0 text-right"><?php echo lang('amount'); ?></th>
                                                                        <th class="border-top-0 text-right"><?php echo lang('action'); ?></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $this->load->model('logs/logs_model');
                                                                    $latest_transaction_logs = $this->logs_model->getTransactionLogsByLimit(5, 0, 'id', 'desc');
                                                                    foreach ($latest_transaction_logs as $log) {
                                                                        $action = '';
                                                                        switch ($log->action) {
                                                                            case 'Added':
                                                                                $action = '<span class="badge badge-success">' . lang('added') . '</span>';
                                                                                break;
                                                                            case 'Added/Deposited':
                                                                                $action = '<span class="badge badge-success">' . lang('added') . ' ' . lang('deposited') . '</span>';
                                                                                break;
                                                                            case 'Updated':
                                                                                $action = '<span class="badge badge-success">' . lang('updated') . '</span>';
                                                                                break;
                                                                            case 'deleted_deposit':
                                                                                $action = '<span class="badge badge-danger">' . lang('deleted') . ' ' . 'Deposit' . '</span>';
                                                                                break;
                                                                            case 'deleted':
                                                                                $action = '<span class="badge badge-danger">' . lang('deleted') . '</span>';
                                                                                break;
                                                                            default:
                                                                                $action = '<span class="badge badge-info">' . lang('updated') . ' ' . lang('deposited') . '</span>';
                                                                        }
                                                                    ?>
                                                                        <tr>
                                                                            <td class="border-0">
                                                                                <strong><?php echo date('M d, Y', strtotime($log->date_time)); ?></strong> <br>
                                                                                <span class="text-muted text-sm"><?php echo date('H:i', strtotime($log->date_time)); ?></span>
                                                                            </td>
                                                                            <td class="text-right border-0"><a target="_blank" <?php if ($log->action != 'deleted') {
                                                                                                                                    echo 'href="' . site_url('finance/invoice?id=' . $log->invoice_id) . '"';
                                                                                                                                } ?>><?php echo $log->invoice_id; ?></a></td>
                                                                            <td class="text-right border-0"><?php echo $log->patientname; ?></td>
                                                                            <td class="text-right border-0"><?php echo number_format($log->amount, 2); ?></td>
                                                                            <td class="text-right border-0"><?php echo $action; ?></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer badge-white">
                                                        <div class="d-flex justify-content-end">
                                                            <a class="btn btn-success btn-sm" href="logs/transactionLogs"><?php echo lang('all'); ?> <?php echo lang('transaction_logs'); ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>






                                    <div class="row g-2">
                                        <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                                            <?php if (in_array('appointment', $this->modules)) { ?>
                                                <div class="col-md-12">
                                                    <div class="card card-custom shadow-sm custom-rounded m-1">
                                                        <div class="card-header badge-transparent text-left pt-4 pb-3">
                                                            <h5 class="fw-bold text-sm"><?php echo lang('appointment'); ?> <?php echo lang('calendar'); ?></h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div id="calendar"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>

                                </div>





                            <?php } ?>

                        <?php } else { ?>
                            <?php if (in_array('home', $this->super_modules)) { ?>

                                <div class="row state-overview col-md-12 state_overview_design">

                                    <div class="col-lg-3 col-6">
                                        <!-- small box -->
                                        <a href="hospital" class="small-box-footer">
                                            <div class="small-box badge-info">
                                                <div class="inner">
                                                    <h3>
                                                        <?php
                                                        $count = 0;
                                                        $hospitalList = $this->db->get('hospital')->result();
                                                        foreach ($hospitalList as $hospitalList) {
                                                            $count = $count + 1;
                                                        }

                                                        echo $count;
                                                        ?>
                                                    </h3>

                                                    <p class="text-lg"><?php echo lang('total'); ?> <?php echo lang('hospitals'); ?></p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-hospital"></i>
                                                </div>
                                                <?php if ($this->ion_auth->in_group('superadmin')) { ?>
                                                    <!-- <a href="hospital" class="small-box-footer"><?php echo lang("more_infoo"); ?> <i class="fas fa-arrow-circle-right"></i></a> -->
                                                <?php } ?>
                                            </div>
                                        </a>
                                    </div>
                                    </a>

                                    <div class="col-lg-3 col-6">
                                        <!-- small box -->
                                        <a href=" hospital/active" class="small-box-footer">
                                            <div class="small-box badge-success">
                                                <div class="inner">
                                                    <h3>
                                                        <?php
                                                        $count = 0;
                                                        $hospitalList = $this->db->get('hospital')->result();
                                                        foreach ($hospitalList as $hospitalList) {
                                                            $this->db->where('id', $hospitalList->ion_user_id);
                                                            $status = $this->db->get('users')->row();
                                                            if ($status->active == "1") {
                                                                $count = $count + 1;
                                                            }
                                                        }

                                                        echo $count;
                                                        ?>
                                                    </h3>

                                                    <p class="text-lg"><?php echo lang('active'); ?> <?php echo lang('hospitals'); ?></p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-check"></i>
                                                </div>
                                                <?php if ($this->ion_auth->in_group('superadmin')) { ?>
                                                    <!-- <a href=" hospital/active" class="small-box-footer"><?php echo lang("more_infoo"); ?> <i class="fas fa-arrow-circle-right"></i></a> -->
                                                <?php } ?>
                                            </div>
                                        </a>
                                    </div>
                                    </a>

                                    <div class="col-lg-3 col-6">
                                        <!-- small box -->
                                        <a href="hospital/disable" class="small-box-footer">
                                            <div class="small-box badge-secondary">
                                                <div class="inner">
                                                    <h3>
                                                        <?php
                                                        $count = 0;
                                                        $hospitalList = $this->db->get('hospital')->result();
                                                        foreach ($hospitalList as $hospitalList) {
                                                            $this->db->where('id', $hospitalList->ion_user_id);
                                                            $status = $this->db->get('users')->row();
                                                            if ($status->active == "0") {
                                                                $count = $count + 1;
                                                            }
                                                        }

                                                        echo $count;
                                                        ?>
                                                    </h3>

                                                    <p class="text-lg"><?php echo lang('inactive'); ?> <?php echo lang('hospitals'); ?></p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-pause"></i>
                                                </div>
                                                <?php if ($this->ion_auth->in_group('superadmin')) { ?>
                                                    <!-- <a href="hospital/disable" class="small-box-footer"><?php echo lang("more_infoo"); ?> <i class="fas fa-arrow-circle-right"></i></a> -->
                                                <?php } ?>
                                            </div>
                                        </a>
                                    </div>
                                    </a>

                                    <div class="col-lg-3 col-6">
                                        <!-- small box -->
                                        <a href="systems/expiredHospitals" class="small-box-footer">
                                            <div class="small-box badge-danger">
                                                <div class="inner">
                                                    <h3>
                                                        <?php
                                                        $count = 0;
                                                        $hospitalRequestList = $this->db->get('hospital_payment')->result();

                                                        foreach ($hospitalRequestList as $hospitalRequestList) {

                                                            if ($hospitalRequestList->next_due_date_stamp < time()) {
                                                                $hospital_details = $this->db->get_where('hospital', array('id' => $hospitalRequestList->hospital_user_id))->row();
                                                                if (!empty($hospital_details)) {
                                                                    $count = $count + 1;
                                                                }
                                                            }
                                                        }


                                                        echo $count;

                                                        ?>
                                                    </h3>

                                                    <p class="text-lg"><?php echo lang('licence_expired'); ?></p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-ban"></i>
                                                </div>
                                                <?php if ($this->ion_auth->in_group('superadmin')) { ?>
                                                    <!-- <a href="systems/expiredHospitals" class="small-box-footer"><?php echo lang("more_infoo"); ?> <i class="fas fa-arrow-circle-right"></i></a> -->
                                                <?php } ?>
                                            </div>
                                        </a>
                                    </div>
                                    </a>


                                </div>
                                <div class="row">
                                    <div class="col-lg-8 col-sm-12">
                                        <div id="chart_div_superadmin" class="card"></div>

                                    </div>
                                    <div class="col-lg-4 col-sm-6">

                                        <div id="piechart_3d_superadmin" class="card"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <section class="card">
                                            <header class="card-header">
                                                <?php echo date('D d F, Y'); ?>
                                            </header>
                                            <div class="card-body">

                                                <div class="home_section">
                                                    <?php echo lang('monthly'); ?> <?php echo lang('subscription'); ?>: <?php echo $settings->currency; ?> <?php echo format_number_short($this_day['payment'], 2, '.', ','); ?>
                                                    <hr>
                                                </div>
                                                <div class="home_section">
                                                    <?php echo lang('yearly'); ?> <?php echo lang('subscription'); ?>: <?php echo $settings->currency; ?> <?php echo format_number_short($this_day['payment_yearly'], 2, '.', ','); ?>
                                                    <hr>
                                                </div>
                                                <div class="home_section">
                                                    <?php echo lang('total'); ?> <?php echo lang('income'); ?> : <?php echo $settings->currency; ?> <?php echo format_number_short($this_day['payment'] + $this_day['payment_yearly'], 2, '.', ','); ?>
                                                    <hr>
                                                </div>



                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-md-4">
                                        <section class="card">
                                            <header class="card-header">
                                                <?php echo date('F, Y'); ?>
                                            </header>
                                            <div class="card-body">

                                                <div class="home_section">
                                                    <?php echo lang('monthly'); ?> <?php echo lang('subscription'); ?>: <?php echo $settings->currency; ?> <?php echo format_number_short($this_monthly['payment'], 2, '.', ','); ?>
                                                    <hr>
                                                </div>
                                                <div class="home_section">
                                                    <?php echo lang('yearly'); ?> <?php echo lang('subscription'); ?> : <?php echo $settings->currency; ?> <?php echo format_number_short($this_year['payment'], 2, '.', ','); ?>
                                                    <hr>
                                                </div>
                                                <div class="home_section">
                                                    <?php echo lang('total'); ?> <?php echo lang('income'); ?> : <?php echo $settings->currency; ?> <?php echo format_number_short($this_year['payment'] + $this_monthly['payment'], 2, '.', ','); ?>
                                                    <hr>
                                                </div>



                                            </div>
                                        </section>

                                    </div>
                                    <div class="col-md-4">
                                        <section class="card">
                                            <header class="card-header">
                                                <?php echo date('Y'); ?>
                                            </header>
                                            <div class="card-body">

                                                <div class="home_section">
                                                    <?php echo lang('monthly'); ?> <?php echo lang('subscription'); ?> : <?php echo $settings->currency; ?> <?php echo format_number_short($this_month_payment['payment'], 2, '.', ','); ?>
                                                    <hr>
                                                </div>
                                                <div class="home_section">
                                                    <?php echo lang('yearly'); ?> <?php echo lang('subscription'); ?> : <?php echo $settings->currency; ?> <?php echo format_number_short($this_year_payment['payment'], 2, '.', ','); ?>
                                                    <hr>
                                                </div>
                                                <div class="home_section">
                                                    <?php echo lang('total'); ?> <?php echo lang('income'); ?> : <?php echo $settings->currency; ?> <?php echo format_number_short($this_year_payment['payment'] + $this_month_payment['payment'], 2, '.', ','); ?>
                                                    <hr>
                                                </div>


                                            </div>
                                        </section>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <!-- /.card -->
                </div>

                <?php if ($this->ion_auth->in_group(array('admin'))) { ?>
                    <div class="col-md-3 col-lg-3 col-12">

                        <div class="card shadow-sm custom-rounded m-1 mb-2">
                            <div class="p-3">
                                <h5 class="border-bottom pb-2 pt-1 mb-2 text-sm fw-bold">
                                    <?php echo lang('top_services') ?>
                                    <span class="text-xs badge badge-secondary ml-2 px-2 py-1">
                                        <?php echo lang('last_30_days') ?>
                                    </span>
                                </h5>

                                <div id="topServicesChart" style="height: 160px;"></div>
                                <script type="text/javascript">
                                    google.charts.load('current', {
                                        'packages': ['corechart']
                                    });
                                    google.charts.setOnLoadCallback(drawChartTopServices);

                                    function drawChartTopServices() {
                                        var data = google.visualization.arrayToDataTable([
                                            ['Service', 'Revenue'],
                                            <?php
                                            // Check if $topRevenueGeneratingServices is an array and has values
                                            if (is_array($topRevenueGeneratingServices) && !empty($topRevenueGeneratingServices)) {
                                                $topServices = array_slice($topRevenueGeneratingServices, 0, 4, true);
                                                foreach ($topServices as $key => $value) {
                                                    // Check if the category name exists and value is set
                                                    $categoryName = $this->home_model->getPaymentCategoryName($key);
                                                    if (!is_null($categoryName) && isset($value)) {
                                                        echo "['" . htmlspecialchars($categoryName) . "', " . floatval($value) . "],";
                                                    }
                                                }
                                                // Calculate 'Others' only if there are more than 4 items
                                                if (count($topRevenueGeneratingServices) > 4) {
                                                    $othersRevenue = array_sum(array_slice($topRevenueGeneratingServices, 4));
                                                    echo "['Others', " . floatval($othersRevenue) . "]";
                                                }
                                            } else {
                                                // Default message or empty chart data in case of null/empty array
                                                echo "['No data available', 0]";
                                            }
                                            ?>
                                        ]);



                                        var options = {
                                            title: "<?php echo lang('top_services'); ?>",
                                            pieHole: 0.3,
                                            colors: ['#ff9999', '#66b3ff', '#99ff99', '#ffcc99', '#c2c2f0', '#ffb3e6'],
                                            legend: {
                                                position: 'bottom'
                                            },


                                            backgroundColor: 'transparent',
                                            titleTextStyle: {
                                                color: $('body').hasClass('dark-mode') ? '#FFFFFF' : '#333333'
                                            },
                                            legendTextStyle: {
                                                color: $('body').hasClass('dark-mode') ? '#FFFFFF' : '#333333'
                                            },

                                            vAxis: {
                                                textStyle: {
                                                    color: $('body').hasClass('dark-mode') ? '#FFFFFF' : '#333333'
                                                }
                                            },
                                            hAxis: {
                                                textStyle: {
                                                    color: $('body').hasClass('dark-mode') ? '#FFFFFF' : '#333333'
                                                }
                                            }



                                        };

                                        var chart = new google.visualization.PieChart(document.getElementById('topServicesChart'));
                                        chart.draw(data, options);
                                    }
                                </script>
                            </div>

                        </div>








                        <!-- Referral Management & Top Referrers -->
                        <div class="card shadow-sm custom-rounded m-1 mb-2">
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 pt-1 mb-2">
                                    <h5 class="text-sm fw-bold mb-0">
                                        <i class="fas fa-handshake text-primary mr-1"></i> <?php echo lang('referral_management'); ?>
                                    </h5>
                                    <a href="referral" class="text-xs text-primary font-weight-bold">View All <i class="fas fa-arrow-right"></i></a>
                                </div>
                                <div class="d-flex justify-content-between mb-2 p-2 bg-light rounded text-xs">
                                    <div>
                                        <span class="text-muted d-block">Referral Doctors</span>
                                        <strong class="text-primary" style="font-size: 1.05rem;"><?php echo $totalReferrers ?? 0; ?></strong>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-muted d-block"><?php echo lang('this_month'); ?> Commission</span>
                                        <strong class="text-success" style="font-size: 1.05rem;"><?php echo $settings->currency ?? ''; ?><?php echo number_format($referralCommissionThisMonth ?? 0, 2); ?></strong>
                                    </div>
                                </div>
                                <p class="text-xs text-muted mb-1 font-weight-bold">Top Referring Doctors</p>
                                <ul class="list-group list-group-flush">
                                    <?php if (!empty($topReferrers)) { ?>
                                        <?php foreach ($topReferrers as $ref) { ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-1 py-1 text-xs">
                                                <span class="text-truncate mr-2 font-weight-500" style="max-width: 140px;"><?php echo html_escape($ref->name); ?></span>
                                                <span>
                                                    <span class="badge badge-info badge-pill mr-1"><?php echo $ref->total_cases; ?> tests</span>
                                                    <strong class="text-dark"><?php echo $settings->currency ?? ''; ?><?php echo number_format($ref->total_comm, 0); ?></strong>
                                                </span>
                                            </li>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <li class="list-group-item px-1 py-2 text-xs text-muted">No referral activity recorded yet</li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Inventory & Human Resources Quick Overview -->
                        <div class="card shadow-sm custom-rounded m-1 mb-2">
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 pt-1 mb-2">
                                    <h5 class="text-sm fw-bold mb-0">
                                        <i class="fas fa-boxes text-info mr-1"></i> <?php echo lang('inventory'); ?> &amp; <?php echo lang('human_resources'); ?>
                                    </h5>
                                </div>
                                <div class="row g-2 text-center mt-1">
                                    <div class="col-6 mb-2">
                                        <a href="inventory/items" class="text-decoration-none">
                                            <div class="p-2 border rounded bg-light">
                                                <i class="fas fa-boxes text-info fa-lg mb-1"></i>
                                                <div class="text-xs text-muted">Inventory Items</div>
                                                <strong class="text-dark" style="font-size: 1.1rem;"><?php echo $totalInventoryItems ?? 0; ?></strong>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <a href="nurse" class="text-decoration-none">
                                            <div class="p-2 border rounded bg-light">
                                                <i class="fas fa-user-tie text-success fa-lg mb-1"></i>
                                                <div class="text-xs text-muted"><?php echo lang('staff_on_duty'); ?></div>
                                                <strong class="text-dark" style="font-size: 1.1rem;"><?php echo ($nursesOnDuty + $laboratoristsOnDuty + $receptionistsOnDuty + $accountantsOnDuty); ?></strong>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top text-xs">
                                    <span class="text-muted">Total Staff Members</span>
                                    <span class="badge badge-primary px-2 py-1"><?php echo $totalStaff ?? 0; ?> Staff</span>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm custom-rounded m-1 mb-2">
                            <div class="p-3">
                                <h5 class="border-bottom pb-3 pt-2 text-sm fw-bold"><?php echo lang('hospital') ?> <?php echo lang('analytics') ?></h5>
                                <ul class="list-group list-group-flush">
                                    <?php
                                    // Define hospital ID
                                    $hospital_id = $this->session->userdata('hospital_id');

                                    if (isset($hospital_id)) {
                                        $this->db->reset_query();
                                        $this->db->where('hospital_id', $hospital_id);
                                        $totalPatients = $this->db->count_all_results('patient');

                                        $this->db->reset_query();
                                        $this->db->where('hospital_id', $hospital_id);
                                        $totalDoctors = $this->db->count_all_results('doctor');

                                        $this->db->reset_query();
                                        $this->db->where('hospital_id', $hospital_id);
                                        $totalAppointments = $this->db->count_all_results('appointment');
                                    } else {
                                        $totalPatients = $totalDoctors = $totalAppointments = 0;
                                    }
                                    ?>

                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <a href="patient" class="text-dark text-decoration-none"><span><i class="fas fa-user-injured text-primary mr-1"></i> <?php echo lang('total') ?> <?php echo lang('patients') ?></span></a>
                                        <span class="badge badge-primary badge-pill"><?php echo $totalPatients; ?></span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <a href="doctor" class="text-dark text-decoration-none"><span><i class="fas fa-user-md text-info mr-1"></i> <?php echo lang('total') ?> <?php echo lang('doctors') ?></span></a>
                                        <span class="badge badge-primary badge-pill"><?php echo $totalDoctors; ?></span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <a href="lab/lab" class="text-dark text-decoration-none"><span><i class="fas fa-flask text-success mr-1"></i> <?php echo lang('total') ?> <?php echo lang('lab_reports-h') ?></span></a>
                                        <span class="badge badge-success badge-pill"><?php echo $totalLabReports ?? 0; ?></span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <a href="finance/payment" class="text-dark text-decoration-none"><span><i class="fas fa-file-invoice-dollar text-warning mr-1"></i> <?php echo lang('total') ?> <?php echo lang('invoices') ?></span></a>
                                        <span class="badge badge-warning badge-pill"><?php echo $totalInvoices ?? 0; ?></span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <a href="referral" class="text-dark text-decoration-none"><span><i class="fas fa-handshake text-secondary mr-1"></i> Referral Doctors</span></a>
                                        <span class="badge badge-info badge-pill"><?php echo $totalReferrers ?? 0; ?></span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <a href="nurse" class="text-dark text-decoration-none"><span><i class="fas fa-users text-secondary mr-1"></i> Total Staff Members</span></a>
                                        <span class="badge badge-secondary badge-pill"><?php echo $totalStaff ?? 0; ?></span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <a href="inventory/items" class="text-dark text-decoration-none"><span><i class="fas fa-boxes text-info mr-1"></i> <?php echo lang('inventory') ?> Items</span></a>
                                        <span class="badge badge-dark badge-pill"><?php echo $totalInventoryItems ?? 0; ?></span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <a href="appointment" class="text-dark text-decoration-none"><span><i class="fas fa-calendar-alt text-danger mr-1"></i> <?php echo lang('total') ?> <?php echo lang('appointments') ?></span></a>
                                        <span class="badge badge-danger badge-pill"><?php echo $totalAppointments; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>


                        <div class="card shadow-sm custom-rounded m-1 mb-2">
                            <div class="p-3">
                                <h5 class="border-bottom pb-3 pt-2 text-sm fw-bold"><?php echo lang('staff_on_duty') ?></h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <span> <?php echo lang('doctors_on_duty') ?></span>
                                        <span class="badge badge-primary badge-pill"><?php echo $doctorsOnDuty; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <span><?php echo lang('laboratorists_on_duty') ?></span>
                                        <span class="badge badge-primary badge-pill"><?php echo $laboratoristsOnDuty; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <span><?php echo lang('nurses_on_duty') ?></span>
                                        <span class="badge badge-primary badge-pill"><?php echo $nursesOnDuty; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <span><?php echo lang('receptionists_on_duty') ?></span>
                                        <span class="badge badge-primary badge-pill"><?php echo $receptionistsOnDuty; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center pl-0">
                                        <span><?php echo lang('accountants_on_duty') ?></span>
                                        <span class="badge badge-primary badge-pill"><?php echo $accountantsOnDuty; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>



                    </div>



                    <div class="col-md-3 col-lg-3 col-12">



                        <!-- Diagnostic Lab Reports Status (Replaces Bed Occupancy) -->
                        <div class="card shadow-sm custom-rounded m-1 mb-2">
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 pt-1 mb-2">
                                    <h5 class="text-sm fw-bold mb-0">
                                        <i class="fas fa-flask text-info mr-1"></i> Diagnostic Lab Reports
                                    </h5>
                                    <a href="lab/lab" class="text-xs text-primary font-weight-bold">View Tests <i class="fas fa-arrow-right"></i></a>
                                </div>
                                <div id="labStatusChart" style="min-height: 160px;"></div>
                                <script type="text/javascript">
                                    google.charts.load('current', {
                                        'packages': ['corechart']
                                    });
                                    google.charts.setOnLoadCallback(drawChartLabStatus);

                                    function drawChartLabStatus() {
                                        var data = google.visualization.arrayToDataTable([
                                            ['Status', 'Count'],
                                            ['Pending / In Progress', <?php echo (int)($labPendingCount ?? 0); ?>],
                                            ['Completed', <?php echo (int)($labDoneCount ?? 0); ?>],
                                            ['Delivered', <?php echo (int)($labDeliveredCount ?? 0); ?>]
                                        ]);

                                        var options = {
                                            pieHole: 0.45,
                                            colors: ['#ffc107', '#28a745', '#17a2b8'],
                                            legend: {
                                                position: 'bottom',
                                                textStyle: {
                                                    fontSize: 11,
                                                    color: $('body').hasClass('dark-mode') ? '#FFFFFF' : '#333333'
                                                }
                                            },
                                            chartArea: {
                                                left: 10,
                                                right: 10,
                                                top: 10,
                                                bottom: 30,
                                                width: '100%',
                                                height: '85%'
                                            },
                                            backgroundColor: 'transparent'
                                        };

                                        var chart = new google.visualization.PieChart(document.getElementById('labStatusChart'));
                                        chart.draw(data, options);
                                    }
                                </script>
                                <div class="d-flex justify-content-around text-center pt-2 border-top text-xs">
                                    <div>
                                        <span class="text-warning font-weight-bold d-block" style="font-size: 1rem;"><?php echo (int)($labPendingCount ?? 0); ?></span>
                                        <span class="text-muted">Pending</span>
                                    </div>
                                    <div>
                                        <span class="text-success font-weight-bold d-block" style="font-size: 1rem;"><?php echo (int)($labDoneCount ?? 0); ?></span>
                                        <span class="text-muted">Completed</span>
                                    </div>
                                    <div>
                                        <span class="text-info font-weight-bold d-block" style="font-size: 1rem;"><?php echo (int)($labDeliveredCount ?? 0); ?></span>
                                        <span class="text-muted">Delivered</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnostic & Financial Monthly Snapshot (Replaces Top Treatments) -->
                        <div class="card shadow-sm custom-rounded m-1 mb-2">
                            <div class="p-3">
                                <h5 class="border-bottom pb-2 py-1 mb-2 text-sm fw-bold">
                                    <i class="fas fa-chart-pie text-primary mr-1"></i> Diagnostic Performance <span class="text-xs badge badge-secondary ml-2 px-2 py-1"><?php echo lang('this_month') ?></span>
                                </h5>
                                <div class="row g-2 text-center mt-2">
                                    <div class="col-6 mb-2">
                                        <a href="finance/payment" class="text-decoration-none">
                                            <div class="p-2 border rounded bg-light">
                                                <div class="text-xs text-muted mb-1"><?php echo lang('invoices'); ?></div>
                                                <strong class="text-primary" style="font-size: 1.15rem;"><?php echo $invoicesThisMonth ?? 0; ?></strong>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <a href="lab/lab" class="text-decoration-none">
                                            <div class="p-2 border rounded bg-light">
                                                <div class="text-xs text-muted mb-1">Lab Tests</div>
                                                <strong class="text-success" style="font-size: 1.15rem;"><?php echo $labTestsThisMonth ?? 0; ?></strong>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <a href="referral" class="text-decoration-none">
                                            <div class="p-2 border rounded bg-light">
                                                <div class="text-xs text-muted mb-1">Doctor Commission</div>
                                                <strong class="text-info" style="font-size: 1rem;"><?php echo $settings->currency ?? ''; ?><?php echo number_format($referralCommissionThisMonth ?? 0, 0); ?></strong>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <a href="finance/payment" class="text-decoration-none">
                                            <div class="p-2 border rounded bg-light">
                                                <div class="text-xs text-muted mb-1"><?php echo lang('total'); ?> <?php echo lang('bill'); ?></div>
                                                <strong class="text-dark" style="font-size: 1rem;"><?php echo $settings->currency ?? ''; ?><?php echo format_number_short($this_month['payment'] ?? 0); ?></strong>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card shadow-sm custom-rounded m-1 mb-2 bg-transparent">
                            <div class="">
                                <h5 class="my-4 fw-bold text-center text-sm"><?php echo lang('today'); ?> - <?php echo lang('overview'); ?></h5>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <a href="finance/payment" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 ml-3 border rounded">
                                                <i class="fas fa-file-invoice bg-success text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('invoices'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $invoicesToday ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="lab/lab" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 mr-3 border rounded">
                                                <i class="fas fa-flask bg-info text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted">Lab Tests</h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $labTestsToday ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <a href="patient" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 ml-3 border rounded">
                                                <i class="fas fa-user-check bg-info text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('registered'); ?> </h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $registeredToday ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="finance/payment" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 mr-3 border rounded">
                                                <i class="fas fa-dollar-sign bg-primary text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('income'); ?> </h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $settings->currency; ?><?php echo format_number_short($this_day['payment'] ?? 0, 2, '.', ','); ?></p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <a href="finance/expense" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 ml-3 border rounded">
                                                <i class="fas fa-money-bill-wave bg-warning text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('expense'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $settings->currency; ?><?php echo format_number_short($this_day['expense'] ?? 0, 2, '.', ','); ?></p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="appointment" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 mr-3 border rounded">
                                                <i class="fas fa-calendar-check bg-secondary text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('appointments'); ?> </h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $this_day['appointment'] ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card shadow-sm custom-rounded m-1 mb-2 bg-transparent">
                            <div class="">
                                <h5 class="my-4 fw-bold text-center text-sm"><?php echo lang('this_month'); ?> - <?php echo lang('overview'); ?></h5>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <a href="finance/payment" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 ml-3 border rounded">
                                                <i class="fas fa-file-invoice bg-success text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"> <?php echo lang('invoices'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $invoicesThisMonth ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="lab/lab" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 mr-3 border rounded">
                                                <i class="fas fa-flask bg-info text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"> Lab Tests</h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $labTestsThisMonth ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <a href="patient" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 ml-3 border rounded">
                                                <i class="fas fa-user-check bg-info text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"> <?php echo lang('registered'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $registeredThisMonth ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="finance/payment" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 mr-3 border rounded">
                                                <i class="fas fa-dollar-sign bg-primary text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('income'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $settings->currency; ?><?php echo format_number_short($this_month['payment'] ?? 0, 2, '.', ','); ?></p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <a href="finance/expense" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 ml-3 border rounded">
                                                <i class="fas fa-money-bill-wave bg-warning text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('expense'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $settings->currency; ?><?php echo format_number_short($this_month['expense'] ?? 0, 2, '.', ','); ?></p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="appointment" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 mr-3 border rounded">
                                                <i class="fas fa-calendar-check bg-secondary text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('appointment'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $this_month['appointment'] ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card shadow-sm custom-rounded m-1 bg-transparent">
                            <div class="">
                                <h5 class="my-4 fw-bold text-center text-sm"><?php echo lang('this_year'); ?> - <?php echo lang('overview'); ?></h5>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <a href="finance/payment" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 ml-3 border rounded">
                                                <i class="fas fa-file-invoice bg-success text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('invoices'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $invoicesThisYear ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="lab/lab" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 mr-3 border rounded">
                                                <i class="fas fa-flask bg-info text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted">Lab Tests</h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $labTestsThisYear ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <a href="patient" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 ml-3 border rounded">
                                                <i class="fas fa-user-check bg-info text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('registered'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $registeredThisYear ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="finance/payment" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 mr-3 border rounded">
                                                <i class="fas fa-dollar-sign bg-primary text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('income'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $settings->currency; ?><?php echo format_number_short($this_year['payment'] ?? 0, 2, '.', ','); ?></p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <a href="finance/expense" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 ml-3 border rounded">
                                                <i class="fas fa-money-bill-wave bg-warning text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('expense'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $settings->currency; ?><?php echo format_number_short($this_year['expense'] ?? 0, 2, '.', ','); ?></p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="appointment" class="text-decoration-none">
                                            <div class="stat-card bg-light p-3 mr-3 border rounded">
                                                <i class="fas fa-calendar-check bg-secondary text-white fa-lg p-2 rounded-circle"></i>
                                                <h6 class="fw-bold mt-2 text-dark title-spacing text-sm text-muted"><?php echo lang('appointment'); ?></h6>
                                                <p class="fw-bold text-dark mb-0" style="font-size: 1.1rem;"><?php echo $this_year['appointment'] ?? 0; ?></p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>






                <?php } ?>


                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- /.content -->
</div>















</section>

<?php
if (!$this->ion_auth->in_group(array('superadmin'))) {
    if (!empty($this_month['payment'])) {
        $payment_this = $this_month['payment'];
    } else {
        $payment_this = 0;
    }
    if (!empty($this_month['expense'])) {
        $expense_this = $this_month['expense'];
    } else {
        $expense_this = 0;
    }
    if (!empty($this_month['appointment_treated'])) {
        $appointment_treated = $this_month['appointment_treated'];
    } else {
        $appointment_treated = 0;
    }


    if (!empty($this_month['appointment_cancelled'])) {
        $appointment_cancelled = $this_month['appointment_cancelled'];
    } else {
        $appointment_cancelled = 0;
    }
    $superadmin_login = 'no';
} else {
    if (!empty($this_month['payment'])) {
        $superadmin_month_payment = $this_month['payment'];
    } else {
        $superadmin_month_payment = '0';
    }
    if (!empty($this_yearly['payment'])) {
        $superadmin_year_payment = $this_yearly['payment'];
    } else {
        $superadmin_year_payment = '0';
    }
    $superadmin_login = 'yes';
}
?>


<script type="text/javascript">
    var per_month_income_expense = "<?php echo lang('per_month_income_expense') ?>";
    var income = "<?php echo lang('income') ?>";
    var expense = "<?php echo lang('expense') ?>";
</script>
<script type="text/javascript">
    var currency = "<?php echo $settings->currency ?>";
</script>
<script type="text/javascript">
    var months_lang = "<?php echo lang('months') ?>";
</script>
<script type="text/javascript">
    var superadmin_login = "<?php echo $superadmin_login; ?>";
</script>
<?php if (!$this->ion_auth->in_group(array('superadmin'))) { ?>
    <script type="text/javascript">
        var payment_this = <?php echo $payment_this ?>;
    </script>
    <script type="text/javascript">
        var expense_this = <?php echo $expense_this ?>;
    </script>
    <script type="text/javascript">
        var appointment_treated = <?php echo $appointment_treated ?>; 
    </script>
    <script type="text/javascript">
        var appointment_cancelled = <?php echo $appointment_cancelled ?>;
    </script>
    <script type="text/javascript">
        var this_year_expenses = <?php echo json_encode($this_year['expense_per_month']); ?>;
    </script>
<?php } else { ?>
    <script type="text/javascript">
        var superadmin_month_payment = <?php echo $superadmin_month_payment ?>;
    </script>
    <script type="text/javascript">
        var superadmin_year_payment = <?php echo $superadmin_year_payment ?>;
    </script>
<?php } ?>

<script type="text/javascript"> 
    var this_year = <?php echo json_encode($this_year['payment_per_month']); ?>;
    var monthly_subscription_lang = '<?php echo lang('monthly'); ?> <?php echo lang('subscription'); ?>';
    var yearly_subscription_lang = '<?php echo lang('yearly'); ?> <?php echo lang('subscription'); ?>';
    var income_lang = '<?php echo lang('income'); ?>';
    var expense_lang = '<?php echo lang('expense'); ?>';
    var treated_lang = '<?php echo lang('treated'); ?>';
    var cancelled_lang = '<?php echo lang('cancelled'); ?>';
    var jan = '<?php echo lang('jan'); ?>';
    var feb = '<?php echo lang('feb'); ?>';
    var mar = '<?php echo lang('mar'); ?>';
    var apr = '<?php echo lang('apr'); ?>';
    var may = '<?php echo lang('may'); ?>';
    var june = '<?php echo lang('june'); ?>';
    var july = '<?php echo lang('july'); ?>';
    var aug = '<?php echo lang('aug'); ?>';
    var sep = '<?php echo lang('sep'); ?>';
    var oct = '<?php echo lang('oct'); ?>';
    var nov = '<?php echo lang('nov'); ?>';
    var dec = '<?php echo lang('dec'); ?>';

    var January = '<?php echo lang('January'); ?>';
    var February = '<?php echo lang('February'); ?>';
    var March = '<?php echo lang('March'); ?>';
    var April = '<?php echo lang('April'); ?>';
    var May = '<?php echo lang('May'); ?>';
    var June = '<?php echo lang('June'); ?>';
    var July = '<?php echo lang('July'); ?>';
    var August = '<?php echo lang('August'); ?>';
    var September = '<?php echo lang('September'); ?>';
    var October = '<?php echo lang('October'); ?>';
    var November = '<?php echo lang('November'); ?>';
    var December = '<?php echo lang('December'); ?>';
</script>

<script src="common/extranal/js/home.js"></script>
<script src="<?php echo base_url(); ?>application/assets/js/enhanced-sidebar.js"></script>
<script src="<?php echo base_url(); ?>application/assets/js/enhanced-components.js"></script>

<?php
function format_number_short($n)
{
    if ($n >= 1000000000) {
        return number_format($n / 1000000000, 2) . 'B';
    } elseif ($n >= 1000000) {
        return number_format($n / 1000000, 2) . 'M';
    } elseif ($n >= 1000) {
        return number_format($n / 1000, 2) . 'K';
    }
    return number_format($n, 2);
}
?>

<!-- Timezone modal is now included globally in dashboard.php -->