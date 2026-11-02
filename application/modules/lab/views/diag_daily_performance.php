<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Daily Diagnostic Performance & Investigation Log</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Date: <?php echo date('d F Y', strtotime($selected_date)); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-calendar-day text-primary"></i> <?php echo lang('daily_diagnostic_performance'); ?></h3>
                <p>24-hour time slots, patient classification, and itemized investigation test log for <?php echo date('d M Y', strtotime($selected_date)); ?>.</p>
            </div>
            <form method="GET" action="lab/dailyDiagnosticPerformance" class="diag-filter-form">
                <input type="date" name="date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($selected_date); ?>">
                <select name="department_id" class="form-control form-control-sm" style="width: 140px;">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dep): ?>
                        <option value="<?php echo $dep->id; ?>" <?php echo ($filters['department_id'] == $dep->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dep->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="status" class="form-control form-control-sm" style="width: 120px;">
                    <option value="">All Statuses</option>
                    <option value="completed" <?php echo ($filters['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="pending" <?php echo ($filters['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="delivered" <?php echo ($filters['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo ($filters['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filter</button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            </form>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Total Patients</div>
                <div class="kpi-value"><?php echo number_format($summary['total_patients']); ?></div>
                <div class="kpi-sub">New: <?php echo $summary['new_patients']; ?> | Ret: <?php echo $summary['returning_patients']; ?></div>
            </div>
            <div class="diag-kpi-card kpi-accent-indigo">
                <div class="kpi-label">Diagnostic Invoices</div>
                <div class="kpi-value"><?php echo number_format($summary['total_invoices']); ?></div>
                <div class="kpi-sub">Invoices billed today</div>
            </div>
            <div class="diag-kpi-card kpi-accent-cyan">
                <div class="kpi-label">Tests Ordered</div>
                <div class="kpi-value"><?php echo number_format($summary['tests_ordered']); ?></div>
                <div class="kpi-sub">Active test entries</div>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Samples Collected</div>
                <div class="kpi-value"><?php echo number_format($summary['samples_collected']); ?></div>
                <div class="kpi-sub">Pending: <?php echo $summary['samples_pending']; ?></div>
            </div>
            <div class="diag-kpi-card kpi-accent-purple">
                <div class="kpi-label">Tests Completed</div>
                <div class="kpi-value"><?php echo number_format($summary['tests_completed']); ?></div>
                <div class="kpi-sub">Verified: <?php echo $summary['reports_verified']; ?></div>
            </div>
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Reports Delivered</div>
                <div class="kpi-value"><?php echo number_format($summary['reports_delivered']); ?></div>
                <div class="kpi-sub">Pending: <?php echo $summary['reports_pending']; ?></div>
            </div>
        </div>

        <!-- 24-HOUR TIME SLOTS TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-clock text-info mr-1"></i> 24-Hour Time Slot Activity</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag">
                    <thead>
                        <tr>
                            <th>Hour Slot</th>
                            <th>Tests Ordered</th>
                            <th>Samples Collected</th>
                            <th>Tests Completed</th>
                            <th>Reports Delivered</th>
                            <th>Cancelled</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $has_activity = false;
                        foreach ($hourly_slots as $slot): 
                            if ($slot['tests_ordered'] > 0 || $slot['samples_collected'] > 0 || $slot['tests_completed'] > 0 || $slot['reports_delivered'] > 0):
                                $has_activity = true;
                        ?>
                            <tr>
                                <td><strong><?php echo $slot['hour_slot']; ?></strong></td>
                                <td><?php echo number_format($slot['tests_ordered']); ?></td>
                                <td><?php echo number_format($slot['samples_collected']); ?></td>
                                <td><?php echo number_format($slot['tests_completed']); ?></td>
                                <td><?php echo number_format($slot['reports_delivered']); ?></td>
                                <td><?php echo number_format($slot['cancelled']); ?></td>
                            </tr>
                        <?php 
                            endif;
                        endforeach; 
                        if (!$has_activity):
                        ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No diagnostic activity recorded for this date.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ITEMIZED TEST DRILLDOWN TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-list text-primary mr-1"></i> Investigation Test Records (<?php echo count($tests); ?> Tests)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="dailyTestsTable">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Inv #</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Department</th>
                            <th>Test Category</th>
                            <th>Test Name</th>
                            <th>Sample Status</th>
                            <th>Execution</th>
                            <th>Delivery</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tests as $t): ?>
                            <tr>
                                <td><strong>#<?php echo $t->id; ?></strong></td>
                                <td><a href="finance/invoice?id=<?php echo $t->invoice_id; ?>" target="_blank">#<?php echo $t->invoice_id; ?></a></td>
                                <td><?php echo date('h:i A', $t->date); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($t->patient_name); ?></strong><br>
                                    <small class="text-muted">ID: <?php echo $t->patient; ?> | Ph: <?php echo htmlspecialchars($t->patient_phone); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($t->doctor_name_resolved); ?></td>
                                <td><?php echo htmlspecialchars($t->department_name); ?></td>
                                <td><span class="badge badge-light border"><?php echo htmlspecialchars($t->category_name_resolved); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($t->test_name); ?></strong></td>
                                <td>
                                    <?php 
                                    if ($t->status == 'sample_taken' || $t->test_status == 'done') {
                                        echo '<span class="badge-diag badge-diag-collected">Collected</span>';
                                    } elseif ($t->test_status == 'cancelled' || $t->status == 'cancelled') {
                                        echo '<span class="badge-diag badge-diag-cancelled">Cancelled</span>';
                                    } else {
                                        echo '<span class="badge-diag badge-diag-pending">Pending</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($t->test_status == 'done') {
                                        echo '<span class="badge-diag badge-diag-done">Done</span>';
                                        if ($t->test_status_date) echo '<br><small class="text-muted">' . date('h:i A', $t->test_status_date) . '</small>';
                                    } elseif ($t->test_status == 'cancelled') {
                                        echo '<span class="badge-diag badge-diag-cancelled">Cancelled</span>';
                                    } else {
                                        echo '<span class="badge-diag badge-diag-waiting">In Progress</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($t->delivery_status == 'delivered') {
                                        echo '<span class="badge-diag badge-diag-delivered">Delivered</span>';
                                        if ($t->delivery_status_date) echo '<br><small class="text-muted">' . date('h:i A', $t->delivery_status_date) . '</small>';
                                    } else {
                                        echo '<span class="badge-diag badge-diag-pending">Pending</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="diag-print-signatures">
            <div class="diag-signature-box">Prepared By (Medical Technologist)</div>
            <div class="diag-signature-box">Verified By (Lab In-Charge)</div>
            <div class="diag-signature-box">Approved By (Head of Diagnostics)</div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    $('#dailyTestsTable').DataTable({
        responsive: true,
        dom: "<'row no-print'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row no-print'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
        pageLength: 25,
        order: [[0, 'desc']]
    });
});
</script>
