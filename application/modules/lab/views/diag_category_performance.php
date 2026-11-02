<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Category-wise Diagnostic Performance Report</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-tags text-primary"></i> <?php echo lang('category_performance_report'); ?></h3>
                <p>Diagnostic category volume, throughput, and billing analysis for <?php echo htmlspecialchars($period['label']); ?>.</p>
            </div>
            <form method="GET" action="lab/categoryPerformanceReport" class="diag-filter-form">
                <select name="period_type" class="form-control form-control-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="today" <?php echo ($period['type'] == 'today') ? 'selected' : ''; ?>>Today</option>
                    <option value="this_week" <?php echo ($period['type'] == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                    <option value="this_month" <?php echo ($period['type'] == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                    <option value="last_month" <?php echo ($period['type'] == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="this_year" <?php echo ($period['type'] == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Apply</button>
                <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'diag_category_performance']); ?>
            </form>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Active Categories</div>
                <div class="kpi-value"><?php echo number_format($summary['categories_count']); ?></div>
                <div class="kpi-sub">Diagnostic groups</div>
            </div>
            <div class="diag-kpi-card kpi-accent-cyan">
                <div class="kpi-label">Total Tests Ordered</div>
                <div class="kpi-value"><?php echo number_format($summary['total_ordered']); ?></div>
                <div class="kpi-sub">Total investigation volume</div>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Total Category Billing</div>
                <div class="kpi-value">৳<?php echo number_format($summary['total_billing'], 2); ?></div>
                <div class="kpi-sub">Catalog pricing volume</div>
            </div>
        </div>

        <!-- CATEGORIES TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-tags text-primary mr-1"></i> Diagnostic Categories (<?php echo count($categories); ?> Groups)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="categoryTable">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Total Ordered</th>
                            <th>Volume Share %</th>
                            <th>Completed</th>
                            <th>Pending Tests</th>
                            <th>Delivered</th>
                            <th>Pending Delivery</th>
                            <th>Cancelled</th>
                            <th>Completion Rate</th>
                            <th>Category Billing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($cat->category_name); ?></strong></td>
                                <td><strong><?php echo number_format($cat->total_ordered); ?></strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2 font-weight-bold"><?php echo $cat->volume_share_pct; ?>%</span>
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-info" style="width: <?php echo $cat->volume_share_pct; ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge-diag badge-diag-done"><?php echo number_format($cat->total_completed); ?></span></td>
                                <td><?php echo number_format($cat->pending_tests); ?></td>
                                <td><span class="badge-diag badge-diag-delivered"><?php echo number_format($cat->total_delivered); ?></span></td>
                                <td><?php echo number_format($cat->pending_delivery); ?></td>
                                <td><?php echo ($cat->total_cancelled > 0) ? '<span class="badge-diag badge-diag-cancelled">' . $cat->total_cancelled . '</span>' : '0'; ?></td>
                                <td><strong><?php echo $cat->completion_rate; ?>%</strong></td>
                                <td>৳<?php echo number_format($cat->category_billing, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total Sum:</th>
                            <th><?php echo number_format($summary['total_ordered']); ?></th>
                            <th>100.0%</th>
                            <th colspan="6"></th>
                            <th>৳<?php echo number_format($summary['total_billing'], 2); ?></th>
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
    $('#categoryTable').DataTable({
        responsive: true,
        dom: "<'row no-print'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row no-print'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
        paging: false,
        order: [[1, 'desc']]
    });
});
</script>
