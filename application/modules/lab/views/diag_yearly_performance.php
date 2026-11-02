<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Annual Diagnostic Performance Statement</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Year: <?php echo $selected_year; ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-calendar text-primary"></i> <?php echo lang('yearly_diagnostic_performance'); ?></h3>
                <p>12-month annual laboratory aggregation and Year-over-Year (YoY) comparison for Fiscal Year <?php echo $selected_year; ?>.</p>
            </div>
            <form method="GET" action="lab/yearlyDiagnosticPerformance" class="diag-filter-form">
                <select name="year" class="form-control form-control-sm" style="width: 110px;" onchange="this.form.submit()">
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo ($selected_year == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Apply</button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            </form>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Total Tests Ordered</div>
                <div class="kpi-value"><?php echo number_format($summary['total_tests_ordered']); ?></div>
                <div class="kpi-sub">Annual volume</div>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Samples Collected</div>
                <div class="kpi-value"><?php echo number_format($summary['samples_collected']); ?></div>
                <div class="kpi-sub">Total accessions</div>
            </div>
            <div class="diag-kpi-card kpi-accent-purple">
                <div class="kpi-label">Tests Completed</div>
                <div class="kpi-value"><?php echo number_format($summary['tests_completed']); ?></div>
                <div class="kpi-sub">Completion: <?php echo $summary['completion_rate']; ?>%</div>
            </div>
            <div class="diag-kpi-card kpi-accent-cyan">
                <div class="kpi-label">Reports Delivered</div>
                <div class="kpi-value"><?php echo number_format($summary['reports_delivered']); ?></div>
                <div class="kpi-sub">Delivery Rate: <?php echo $summary['delivery_rate']; ?>%</div>
            </div>
            <div class="diag-kpi-card kpi-accent-amber">
                <div class="kpi-label">Year-over-Year (YoY)</div>
                <div class="kpi-value" style="color: <?php echo ($yoy_comparison['variance_tests'] >= 0) ? '#10b981' : '#f43f5e'; ?>;">
                    <?php echo ($yoy_comparison['variance_tests'] >= 0) ? '+' : ''; ?><?php echo number_format($yoy_comparison['variance_tests']); ?>
                </div>
                <div class="kpi-sub">Growth: <?php echo ($yoy_comparison['growth_pct'] >= 0) ? '+' : ''; ?><?php echo $yoy_comparison['growth_pct']; ?>% vs prev year</div>
            </div>
        </div>

        <!-- 12-MONTH MATRIX TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-calendar-alt text-primary mr-1"></i> 12-Month Performance Matrix (<?php echo $selected_year; ?>)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="yearlyTable">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Patients</th>
                            <th>Invoices</th>
                            <th>Tests Ordered</th>
                            <th>Samples Collected</th>
                            <th>Tests Completed</th>
                            <th>Reports Verified</th>
                            <th>Reports Delivered</th>
                            <th>Pending</th>
                            <th>Cancelled</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($month_rows as $row): ?>
                            <tr>
                                <td><strong><?php echo $row['month_name']; ?></strong></td>
                                <td><?php echo number_format($row['patients']); ?></td>
                                <td><?php echo number_format($row['invoices']); ?></td>
                                <td><strong><?php echo number_format($row['tests_ordered']); ?></strong></td>
                                <td><?php echo number_format($row['samples_collected']); ?></td>
                                <td><span class="badge-diag badge-diag-done"><?php echo number_format($row['tests_completed']); ?></span></td>
                                <td><?php echo number_format($row['reports_verified']); ?></td>
                                <td><span class="badge-diag badge-diag-delivered"><?php echo number_format($row['reports_delivered']); ?></span></td>
                                <td><?php echo number_format($row['reports_pending']); ?></td>
                                <td><?php echo ($row['cancelled'] > 0) ? '<span class="badge-diag badge-diag-cancelled">' . $row['cancelled'] . '</span>' : '0'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total Annual Sum:</th>
                            <th><?php echo number_format($summary['total_tests_ordered']); ?></th>
                            <th><?php echo number_format($summary['samples_collected']); ?></th>
                            <th><?php echo number_format($summary['tests_completed']); ?></th>
                            <th><?php echo number_format($summary['reports_verified']); ?></th>
                            <th><?php echo number_format($summary['reports_delivered']); ?></th>
                            <th><?php echo number_format($summary['reports_pending']); ?></th>
                            <th><?php echo number_format($summary['cancelled_tests']); ?></th>
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
    $('#yearlyTable').DataTable({
        responsive: true,
        dom: "<'row no-print'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row no-print'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
        paging: false,
        ordering: false
    });
});
</script>
