<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Doctor-wise Diagnostic Investigation & Referral Volume Report</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-user-md text-primary"></i> <?php echo lang('doctor_diagnostic_performance'); ?></h3>
                <p>Operational referral volume analysis, completed tests, and specialty distribution. (Commission auditing remains in Phase 4).</p>
            </div>
            <form method="GET" action="lab/doctorDiagnosticPerformance" class="diag-filter-form">
                <select name="period_type" class="form-control form-control-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="today" <?php echo ($period['type'] == 'today') ? 'selected' : ''; ?>>Today</option>
                    <option value="this_week" <?php echo ($period['type'] == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                    <option value="this_month" <?php echo ($period['type'] == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                    <option value="last_month" <?php echo ($period['type'] == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="this_year" <?php echo ($period['type'] == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                </select>
                <select name="doctor_id" class="form-control form-control-sm" style="width: 160px;">
                    <option value="">All Doctors</option>
                    <?php foreach ($doctors as $doc): ?>
                        <option value="<?php echo $doc->id; ?>" <?php echo ($filters['doctor_id'] == $doc->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($doc->name); ?>
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
                <div class="kpi-label">Active Doctors</div>
                <div class="kpi-value"><?php echo number_format($summary['doctors_count']); ?></div>
                <div class="kpi-sub">Referring physicians</div>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Referred Patients</div>
                <div class="kpi-value"><?php echo number_format($summary['total_patients']); ?></div>
                <div class="kpi-sub">Unique patients advised</div>
            </div>
            <div class="diag-kpi-card kpi-accent-cyan">
                <div class="kpi-label">Total Tests Ordered</div>
                <div class="kpi-value"><?php echo number_format($summary['total_ordered']); ?></div>
                <div class="kpi-sub">Total referred investigations</div>
            </div>
        </div>

        <!-- DOCTORS TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-user-md text-primary mr-1"></i> Referring Doctor Operational Performance (<?php echo count($doctors); ?> Doctors)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="doctorTable">
                    <thead>
                        <tr>
                            <th>Doctor ID</th>
                            <th>Doctor Name</th>
                            <th>Department / Specialty</th>
                            <th>Referred Patients</th>
                            <th>Associated Invoices</th>
                            <th>Tests Ordered</th>
                            <th>Completed Tests</th>
                            <th>Delivered Reports</th>
                            <th>Cancelled Tests</th>
                            <th>Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doctors as $d): ?>
                            <tr>
                                <td><strong>#<?php echo $d->doctor_id; ?></strong></td>
                                <td><strong><?php echo htmlspecialchars($d->doctor_name); ?></strong></td>
                                <td><span class="badge badge-light border"><?php echo htmlspecialchars($d->department_name); ?></span></td>
                                <td><?php echo number_format($d->referred_patients); ?></td>
                                <td><?php echo number_format($d->associated_invoices); ?></td>
                                <td><strong><?php echo number_format($d->total_tests_ordered); ?></strong></td>
                                <td><span class="badge-diag badge-diag-done"><?php echo number_format($d->completed_tests); ?></span></td>
                                <td><span class="badge-diag badge-diag-delivered"><?php echo number_format($d->delivered_reports); ?></span></td>
                                <td><?php echo ($d->cancelled_tests > 0) ? '<span class="badge-diag badge-diag-cancelled">' . $d->cancelled_tests . '</span>' : '0'; ?></td>
                                <td><strong><?php echo $d->completion_rate; ?>%</strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total Sum:</th>
                            <th><?php echo number_format($summary['total_patients']); ?></th>
                            <th></th>
                            <th><?php echo number_format($summary['total_ordered']); ?></th>
                            <th colspan="4"></th>
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
    $('#doctorTable').DataTable({
        responsive: true,
        dom: "<'row no-print'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row no-print'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
        paging: false,
        order: [[5, 'desc']]
    });
});
</script>
