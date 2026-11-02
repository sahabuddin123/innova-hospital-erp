<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Test-wise Diagnostic Performance & Order Analysis</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-vial text-primary"></i> <?php echo lang('test_performance_report'); ?></h3>
                <p>Individual investigation volumes, completion rates, turnaround times, and billings for <?php echo htmlspecialchars($period['label']); ?>.</p>
            </div>
            <form method="GET" action="lab/testPerformanceReport" class="diag-filter-form">
                <select name="period_type" class="form-control form-control-sm" style="width: 130px;" onchange="this.form.submit()">
                    <option value="today" <?php echo ($period['type'] == 'today') ? 'selected' : ''; ?>>Today</option>
                    <option value="this_week" <?php echo ($period['type'] == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                    <option value="this_month" <?php echo ($period['type'] == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                    <option value="last_month" <?php echo ($period['type'] == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="this_year" <?php echo ($period['type'] == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                </select>
                <select name="preset" class="form-control form-control-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="all" <?php echo ($preset == 'all') ? 'selected' : ''; ?>>All Tests</option>
                    <option value="top10" <?php echo ($preset == 'top10') ? 'selected' : ''; ?>>Top 10 Ordered</option>
                    <option value="top20" <?php echo ($preset == 'top20') ? 'selected' : ''; ?>>Top 20 Ordered</option>
                    <option value="least" <?php echo ($preset == 'least') ? 'selected' : ''; ?>>Least Ordered</option>
                </select>
                <select name="department_id" class="form-control form-control-sm" style="width: 140px;">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dep): ?>
                        <option value="<?php echo $dep->id; ?>" <?php echo ($filters['department_id'] == $dep->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dep->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Apply</button>
                <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'diag_test_performance']); ?>
            </form>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Test Catalog Count</div>
                <div class="kpi-value"><?php echo number_format($summary['total_tests_catalog']); ?></div>
                <div class="kpi-sub">Tests ordered in period</div>
            </div>
            <div class="diag-kpi-card kpi-accent-cyan">
                <div class="kpi-label">Total Ordered</div>
                <div class="kpi-value"><?php echo number_format($summary['total_ordered']); ?></div>
                <div class="kpi-sub">Total test volume</div>
            </div>
            <div class="diag-kpi-card kpi-accent-purple">
                <div class="kpi-label">Total Completed</div>
                <div class="kpi-value"><?php echo number_format($summary['total_completed']); ?></div>
                <div class="kpi-sub">Pending: <?php echo number_format($summary['total_pending']); ?></div>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Reports Delivered</div>
                <div class="kpi-value"><?php echo number_format($summary['total_delivered']); ?></div>
                <div class="kpi-sub">Handed to patient</div>
            </div>
            <div class="diag-kpi-card kpi-accent-indigo">
                <div class="kpi-label">Gross Billing (Info)</div>
                <div class="kpi-value">৳<?php echo number_format($summary['total_gross_billing'], 2); ?></div>
                <div class="kpi-sub">Catalog pricing volume</div>
            </div>
        </div>

        <!-- TEST TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-vial text-primary mr-1"></i> Investigation Test Catalog (<?php echo count($records); ?> Tests)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="testTable">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Test Name</th>
                            <th>Category Group</th>
                            <th>Unit Price</th>
                            <th>Target SLA</th>
                            <th>Total Ordered</th>
                            <th>Completed</th>
                            <th>Pending</th>
                            <th>Delivered</th>
                            <th>Cancelled</th>
                            <th>Completion %</th>
                            <th>Avg TAT</th>
                            <th>Gross Billing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><strong>#<?php echo $r->test_id; ?></strong></td>
                                <td><strong><?php echo htmlspecialchars($r->test_name); ?></strong></td>
                                <td><span class="badge badge-light border"><?php echo htmlspecialchars($r->category_name); ?></span></td>
                                <td>৳<?php echo number_format($r->unit_price, 2); ?></td>
                                <td><?php echo ($r->sla_days > 0) ? ($r->sla_days . ' Days') : 'Routine'; ?></td>
                                <td><strong><?php echo number_format($r->total_ordered); ?></strong></td>
                                <td><span class="badge-diag badge-diag-done"><?php echo number_format($r->total_completed); ?></span></td>
                                <td><?php echo number_format($r->total_pending); ?></td>
                                <td><span class="badge-diag badge-diag-delivered"><?php echo number_format($r->total_delivered); ?></span></td>
                                <td><?php echo ($r->total_cancelled > 0) ? '<span class="badge-diag badge-diag-cancelled">' . $r->total_cancelled . '</span>' : '0'; ?></td>
                                <td><strong><?php echo $r->completion_rate; ?>%</strong></td>
                                <td><?php echo ($r->avg_tat_hours !== null) ? ($r->avg_tat_hours . 'h') : '<span class="text-muted">-</span>'; ?></td>
                                <td>৳<?php echo number_format($r->gross_billing, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">Total Sum:</th>
                            <th><?php echo number_format($summary['total_ordered']); ?></th>
                            <th><?php echo number_format($summary['total_completed']); ?></th>
                            <th><?php echo number_format($summary['total_pending']); ?></th>
                            <th><?php echo number_format($summary['total_delivered']); ?></th>
                            <th><?php echo number_format($summary['total_cancelled']); ?></th>
                            <th colspan="2"></th>
                            <th>৳<?php echo number_format($summary['total_gross_billing'], 2); ?></th>
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
    $('#testTable').DataTable({
        responsive: true,
        dom: "<'row no-print'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row no-print'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
        pageLength: 25,
        order: [[5, 'desc']]
    });
});
</script>
