<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Diagnostic Turnaround Time (TAT) & SLA Benchmark Report</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-stopwatch text-primary"></i> <?php echo lang('turnaround_time_report'); ?></h3>
                <p>Reliable timestamp analysis: Average, Minimum, and Maximum order-to-completion elapsed time compared against configured SLA targets.</p>
            </div>
            <form method="GET" action="lab/turnaroundTimeReport" class="diag-filter-form">
                <select name="period_type" class="form-control form-control-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="today" <?php echo ($period['type'] == 'today') ? 'selected' : ''; ?>>Today</option>
                    <option value="this_week" <?php echo ($period['type'] == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                    <option value="this_month" <?php echo ($period['type'] == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                    <option value="last_month" <?php echo ($period['type'] == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="this_year" <?php echo ($period['type'] == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                </select>
                <select name="department_id" class="form-control form-control-sm" style="width: 150px;">
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
                <div class="kpi-label">Completed Tests Analyzed</div>
                <div class="kpi-value"><?php echo number_format($summary['total_completed_analyzed']); ?></div>
                <div class="kpi-sub">With reliable timestamps</div>
            </div>
            <div class="diag-kpi-card kpi-accent-indigo">
                <div class="kpi-label">Overall Average TAT</div>
                <div class="kpi-value"><?php echo $summary['overall_average_tat_hours']; ?>h</div>
                <div class="kpi-sub">Mean order-to-completion</div>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Within SLA Target</div>
                <div class="kpi-value"><?php echo number_format($summary['within_sla_count']); ?></div>
                <div class="kpi-sub">On-time completions</div>
            </div>
            <div class="diag-kpi-card kpi-accent-amber">
                <div class="kpi-label">Delayed Completions</div>
                <div class="kpi-value"><?php echo number_format($summary['delayed_count']); ?></div>
                <div class="kpi-sub">Exceeded target SLA</div>
            </div>
            <div class="diag-kpi-card kpi-accent-purple">
                <div class="kpi-label">On-Time Performance Rate</div>
                <div class="kpi-value"><?php echo $summary['overall_on_time_rate']; ?>%</div>
                <div class="kpi-sub">SLA compliance rate</div>
            </div>
        </div>

        <!-- TAT AUDIT TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-stopwatch text-primary mr-1"></i> Investigation Turnaround Time Statistics (<?php echo count($records); ?> Tests)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="tatTable">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Test Name</th>
                            <th>Category Group</th>
                            <th>Department</th>
                            <th>Configured SLA Target</th>
                            <th>Completed Count</th>
                            <th>Average TAT</th>
                            <th>Min TAT</th>
                            <th>Max TAT</th>
                            <th>SLA Compliance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><strong>#<?php echo $r->test_id; ?></strong></td>
                                <td><strong><?php echo htmlspecialchars($r->test_name); ?></strong></td>
                                <td><span class="badge badge-light border"><?php echo htmlspecialchars($r->category_group); ?></span></td>
                                <td><?php echo htmlspecialchars($r->department_name); ?></td>
                                <td><?php echo $r->sla_target_label; ?></td>
                                <td><strong><?php echo number_format($r->completed_count); ?></strong></td>
                                <td><strong><?php echo $r->avg_tat_hours; ?> Hours</strong></td>
                                <td><?php echo $r->min_tat_hours; ?>h</td>
                                <td><?php echo $r->max_tat_hours; ?>h</td>
                                <td>
                                    <?php 
                                    if ($r->sla_status == 'Within SLA') {
                                        echo '<span class="badge-diag badge-diag-done"><i class="fas fa-check mr-1"></i> Within SLA</span>';
                                    } else {
                                        echo '<span class="badge-diag badge-diag-cancelled"><i class="fas fa-exclamation-triangle mr-1"></i> Delayed</span>';
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
    $('#tatTable').DataTable({
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
