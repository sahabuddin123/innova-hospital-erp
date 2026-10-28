<div class="content-wrapper bg-gradient-light" style="min-height: 2726.9px;">
    <section class="content-header py-4 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="display-4 font-weight-black mb-0">
                        <i class="fas fa-calendar-alt text-primary mr-3"></i>
                        Doctor Holidays & Leave Management
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="home">Home</a></li>
                            <li class="breadcrumb-item"><a href="doctor">Doctor</a></li>
                            <li class="breadcrumb-item active">Holidays & Leave</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <a data-toggle="modal" href="#myModal" class="btn btn-primary btn-sm px-4 py-3 shadow-sm font-weight-bold">
                        <i class="fa fa-plus mr-1"></i> Add Doctor Holiday / Leave
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content py-5">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-lg border-0">
                        <div class="card-body bg-light p-4">
                            <table class="table table-hover table-bordered datatables" id="editable-sample" width="100%">
                                <thead>
                                    <tr class="bg-light">
                                        <th style="width: 40px;">#</th>
                                        <th>Doctor</th>
                                        <th>Date / Period</th>
                                        <th>Leave Type</th>
                                        <th>Hours / Time</th>
                                        <th>Reason</th>
                                        <?php if ($this->ion_auth->in_group(array('admin', 'Doctor'))) { ?>
                                            <th style="width: 150px;">Options</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($holidays as $holiday) {
                                        $i = $i + 1;
                                        $doc = $this->doctor_model->getDoctorById($holiday->doctor);
                                        $doc_name = !empty($doc) ? (!empty($doc->name_en) ? $doc->name_en : $doc->name) : 'Unknown Doctor';
                                        
                                        $start_str = is_numeric($holiday->date) ? date('d-m-Y', (int)$holiday->date) : date('d-m-Y', strtotime($holiday->date));
                                        $end_str = (!empty($holiday->end_date) && $holiday->end_date != $holiday->date) ? (is_numeric($holiday->end_date) ? date('d-m-Y', (int)$holiday->end_date) : date('d-m-Y', strtotime($holiday->end_date))) : '';
                                        $date_display = $start_str . (!empty($end_str) ? ' to ' . $end_str : '');

                                        $type = !empty($holiday->leave_type) ? $holiday->leave_type : 'full_day';
                                        if ($type == 'full_day') {
                                            $type_badge = '<span class="badge badge-primary px-2 py-1">Full-day Holiday</span>';
                                        } elseif ($type == 'partial_day') {
                                            $type_badge = '<span class="badge badge-info px-2 py-1">Partial-day Leave</span>';
                                        } elseif ($type == 'emergency') {
                                            $type_badge = '<span class="badge badge-danger px-2 py-1">Emergency Unavailability</span>';
                                        } else {
                                            $type_badge = '<span class="badge badge-secondary px-2 py-1">' . html_escape($type) . '</span>';
                                        }

                                        $time_display = ($type == 'partial_day' && !empty($holiday->start_time) && !empty($holiday->end_time)) ? $holiday->start_time . ' - ' . $holiday->end_time : '<span class="text-muted">All Day</span>';
                                        $reason_display = !empty($holiday->reason) ? html_escape($holiday->reason) : '<span class="text-muted small">Not specified</span>';
                                    ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td class="font-weight-bold text-dark"><?php echo html_escape($doc_name); ?></td>
                                            <td><?php echo $date_display; ?></td>
                                            <td><?php echo $type_badge; ?></td>
                                            <td><?php echo $time_display; ?></td>
                                            <td><?php echo $reason_display; ?></td>
                                            <?php if ($this->ion_auth->in_group(array('admin', 'Doctor'))) { ?>
                                                <td>
                                                    <button type="button" class="btn btn-info btn-sm editbutton mr-1" data-toggle="modal" data-id="<?php echo $holiday->id; ?>">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </button>
                                                    <a class="btn btn-danger btn-sm delete_button" href="schedule/deleteHoliday?id=<?php echo $holiday->id; ?>&doctor=<?php echo $holiday->doctor; ?>&redirect=schedule/allHolidays" onclick="return confirm('Are you sure you want to delete this holiday / leave?');">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Holiday Modal-->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-light">
            <div class="modal-header bg-gradient-primary">
                <h2 class="modal-title text-white font-weight-800">Add Doctor Holiday / Leave</h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form role="form" action="schedule/addHoliday" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Select Doctor <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg shadow-sm" id="doctorchoose" name="doctor" style="width: 100%;" required>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Start Date <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" name="date" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">End Date (Leave blank for single day)</label>
                                <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" name="end_date" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Leave Type</label>
                                <select class="form-control form-control-lg shadow-sm" name="leave_type">
                                    <option value="full_day">Full-day Holiday / Off-Day</option>
                                    <option value="partial_day">Partial-day Leave</option>
                                    <option value="emergency">Emergency Unavailability</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Start Time</label>
                                <input type="text" class="form-control form-control-lg shadow-sm timepicker-default" name="start_time" placeholder="e.g. 10:00 AM">
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">End Time</label>
                                <input type="text" class="form-control form-control-lg shadow-sm timepicker-default" name="end_time" placeholder="e.g. 01:00 PM">
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Reason / Notes</label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="reason" placeholder="e.g. Annual Leave, Conference, Medical Leave">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" value=''>
                    <input type="hidden" name="redirect" value='schedule/allHolidays'>

                    <button type="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3 mt-3 font-weight-bold">
                        <i class="fas fa-check-circle mr-2"></i> Save Doctor Holiday / Leave
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Holiday Modal-->
<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-light">
            <div class="modal-header bg-gradient-primary">
                <h2 class="modal-title text-white font-weight-800">Edit Doctor Holiday / Leave</h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form role="form" id="editHolidayForm" action="schedule/addHoliday" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Select Doctor <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg shadow-sm" id="doctorchoose1" name="doctor" style="width: 100%;" required>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Start Date <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" name="date" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">End Date (Leave blank for single day)</label>
                                <input type="text" class="form-control form-control-lg shadow-sm default-date-picker" name="end_date" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Leave Type</label>
                                <select class="form-control form-control-lg shadow-sm" name="leave_type">
                                    <option value="full_day">Full-day Holiday / Off-Day</option>
                                    <option value="partial_day">Partial-day Leave</option>
                                    <option value="emergency">Emergency Unavailability</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Start Time</label>
                                <input type="text" class="form-control form-control-lg shadow-sm timepicker-default" name="start_time" placeholder="e.g. 10:00 AM">
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">End Time</label>
                                <input type="text" class="form-control form-control-lg shadow-sm timepicker-default" name="end_time" placeholder="e.g. 01:00 PM">
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="text-uppercase font-weight-bold text-muted">Reason / Notes</label>
                                <input type="text" class="form-control form-control-lg shadow-sm" name="reason" placeholder="e.g. Annual Leave, Conference, Medical Leave">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" value=''>
                    <input type="hidden" name="redirect" value='schedule/allHolidays'>

                    <button type="submit" class="btn btn-primary btn-lg btn-block shadow-lg py-3 mt-3 font-weight-bold">
                        <i class="fas fa-check-circle mr-2"></i> Update Doctor Holiday / Leave
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
    var language = "<?php echo $this->language; ?>";
</script>
<script src="common/extranal/js/schedule/all_holidays.js"></script>