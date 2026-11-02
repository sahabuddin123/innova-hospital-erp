<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Department-wise Diagnostic Performance Report</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-hospital text-primary"></i> <?php echo lang('department_performance_report'); ?></h3>
                <p>Clinical departmental diagnostic load, patient volumes, and turnaround times for <?php echo htmlspecialchars($period['label']); ?>.</p>
            </div>
            <form method="GET" action="lab/departmentPerformanceReport" class="diag-filter-form">
                <select name="period_type" class="form-control form-control-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="today" <?php echo ($period['type'] == 'today') ? 'selected' : ''; ?>>Today</option>
                    <option value="this_week" <?php echo ($period['type'] == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                    <option value="this_month" <?php echo ($period['type'] == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                    <option value="last_month" <?php echo ($period['type'] == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="this_year" <?php echo ($period['type'] == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                </select>
                <select name="department_id" class="form-control form-control-sm" style="width: 160px;" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dep): ?>
                        <option value="<?php echo $dep->id; ?>" <?php echo ($filters['department_id'] == $dep->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dep->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Apply</button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            </form>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Active Departments</div>
                <div class="kpi-value"><?php echo number_format($summary['departments_count']); ?></div>
                <div class="kpi-sub">Clinical divisions</div>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Total Patients Served</div>
                <div class="kpi-value"><?php echo number_format($summary['total_patients']); ?></div>
                <div class="kpi-sub">Patients with orders</div>
            </div>
            <div class="diag-kpi-card kpi-accent-cyan">
                <div class="kpi-label">Total Tests Ordered</div>
                <div class="kpi-value"><?php echo number_format($summary['total_ordered']); ?></div>
                <div class="kpi-sub">All departmental tests</div>
            </div>
        </div>

        <!-- DEPARTMENTS TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-hospital text-primary mr-1"></i> Departmental Investigation Load (<?php echo count($departments); ?> Departments)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="deptTable">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Referring Doctors</th>
                            <th>Patients Served</th>
                            <th>Tests Ordered</th>
                            <th>Samples Collected</th>
                            <th>Completed</th>
                            <th>Verified</th>
                            <th>Delivered</th>
                            <th>Pending</th>
                            <th>Cancelled</th>
                            <th>Completion %</th>
                            <th>Avg TAT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $d): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($d->department_name); ?></strong></td>
                                <td><?php echo number_format($d->doctor_count); ?> Doctors</td>
                                <td><?php echo number_format($d->total_patients_served); ?></td>
                                <td><strong><?php echo number_format($d->total_tests_ordered); ?></strong></td>
                                <td><?php echo number_format($d->samples_collected); ?></td>
                                <td><span class="badge-diag badge-diag-done"><?php echo number_format($d->tests_completed); ?></span></td>
                                <td><?php echo number_format($d->reports_verified); ?></td>
                                <td><span class="badge-diag badge-diag-delivered"><?php echo number_format($d->reports_delivered); ?></span></td>
                                <td><?php echo number_format($d->pending_tests); ?></td>
                                <td><?php echo ($d->cancelled_tests > 0) ? '<span class="badge-diag badge-diag-cancelled">' . $d->cancelled_tests . '</span>' : '0'; ?></td>
                                <td><strong><?php echo $d->completion_rate; ?>%</strong></td>
                                <td><?php echo ($d->avg_tat_hours !== null) ? ($d->avg_tat_hours . 'h') : '<span class="text-muted">-</span>'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total Departmental Sum:</th>
                            <th><?php echo number_format($summary['total_ordered']); ?></th>
                            <th colspan="8"></th>
                        </tr>
                    </tfoot>
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
    $('#deptTable').DataTable({
        responsive: true,
        dom: "<'row no-print'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row no-print'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
        paging: false,
        order: [[3, 'desc']]
    });
});
</script>
