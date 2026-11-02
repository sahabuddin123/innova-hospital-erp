<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Cancelled Diagnostic Investigations & Withdrawn Orders Audit</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-ban text-danger"></i> <?php echo lang('cancelled_test_report'); ?></h3>
                <p>Audit trail of cancelled investigation items, documented cancellation rates, and revenue impact for <?php echo htmlspecialchars($period['label']); ?>.</p>
            </div>
            <form method="GET" action="lab/cancelledTestReport" class="diag-filter-form">
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
                <div class="kpi-label">Total Tests Ordered</div>
                <div class="kpi-value"><?php echo number_format($summary['total_tests_ordered']); ?></div>
                <div class="kpi-sub">Total denominator volume</div>
            </div>
            <div class="diag-kpi-card kpi-accent-rose">
                <div class="kpi-label">Total Cancelled Tests</div>
                <div class="kpi-value"><?php echo number_format($summary['total_cancelled']); ?></div>
                <div class="kpi-sub">Withdrawn investigations</div>
            </div>
            <div class="diag-kpi-card kpi-accent-amber">
                <div class="kpi-label">Cancellation Rate</div>
                <div class="kpi-value"><?php echo $summary['cancellation_rate']; ?>%</div>
                <div class="kpi-sub">Cancelled / Total Ordered</div>
            </div>
            <div class="diag-kpi-card kpi-accent-indigo">
                <div class="kpi-label">Cancelled Billed Value</div>
                <div class="kpi-value">৳<?php echo number_format($summary['cancelled_amount'], 2); ?></div>
                <div class="kpi-sub">Original test catalog price</div>
            </div>
        </div>

        <!-- CANCELLED TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-ban text-danger mr-1"></i> Cancelled Investigation Records (<?php echo count($cancelled_tests); ?> Tests)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="cancelledTable">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Invoice #</th>
                            <th>Order Date</th>
                            <th>Patient Name</th>
                            <th>Test Name</th>
                            <th>Category Group</th>
                            <th>Department</th>
                            <th>Original Amount</th>
                            <th>Cancellation Reason</th>
                            <th>Recorded By</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cancelled_tests)): ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle text-success mr-1"></i> Excellent! Zero cancelled tests recorded for this period.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cancelled_tests as $c): ?>
                                <tr>
                                    <td><strong>#<?php echo $c->id; ?></strong></td>
                                    <td><a href="finance/invoice?id=<?php echo $c->invoice_id; ?>" target="_blank">#<?php echo $c->invoice_id; ?></a></td>
                                    <td><?php echo date('d-m-Y', $c->date); ?></td>
                                    <td><strong><?php echo htmlspecialchars($c->patient_name); ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars($c->test_name); ?></strong></td>
                                    <td><span class="badge badge-light border"><?php echo htmlspecialchars($c->category_group); ?></span></td>
                                    <td><?php echo htmlspecialchars($c->department_name); ?></td>
                                    <td>৳<?php echo number_format($c->test_price, 2); ?></td>
                                    <td><?php echo htmlspecialchars($c->cancellation_reason); ?></td>
                                    <td><?php echo !empty($c->cashier_name) ? htmlspecialchars($c->cashier_name) : 'Staff'; ?></td>
                                    <td><span class="badge-diag badge-diag-cancelled">Cancelled</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
    $('#cancelledTable').DataTable({
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
