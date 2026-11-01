<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
?>
<link href="common/extranal/css/finance/expense_reports.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_expense_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Expense Category Analysis Report &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-chart-pie text-warning"></i> Expense Category Analysis Report</h2>
                    <p>Departmental and operational cost breakdown across hospital expense heads &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/expenseCategoryReport" class="m-0">
                <input type="hidden" name="period_type" id="period_type_input" value="<?php echo html_escape($period['period_type']); ?>">
                <div class="rep-controls-row">
                    <!-- Period Pills -->
                    <div class="rep-period-pills">
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'today') ? 'active' : ''; ?>" data-period="today">Today</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'yesterday') ? 'active' : ''; ?>" data-period="yesterday">Yesterday</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_week') ? 'active' : ''; ?>" data-period="this_week">This Week</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_month') ? 'active' : ''; ?>" data-period="this_month">This Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'last_month') ? 'active' : ''; ?>" data-period="last_month">Last Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_year') ? 'active' : ''; ?>" data-period="this_year">This Year</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'custom') ? 'active' : ''; ?>" data-period="custom">Custom</button>
                    </div>

                    <!-- Date Filter Group -->
                    <div class="rep-filter-group">
                        <div class="rep-input-wrap">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">FROM:</span>
                            <input type="date" name="date_from" id="date_from" class="rep-date-input" value="<?php echo html_escape($period['date_from']); ?>">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">TO:</span>
                            <input type="date" name="date_to" id="date_to" class="rep-date-input" value="<?php echo html_escape($period['date_to']); ?>">
                        </div>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Apply</button>
                        <a href="finance/expenseCategoryReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-money-bill-wave text-danger"></i> Total Categorized Spend</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['total_expense'], 2); ?></span>
                <span class="rep-kpi-sub">Across <?php echo number_format($summary['total_transactions']); ?> total vouchers</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-layer-group text-primary"></i> Active Expense Heads</span>
                <span class="rep-kpi-val"><?php echo number_format($summary['total_categories']); ?></span>
                <span class="rep-kpi-sub">Expense categories utilized</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-crown text-warning"></i> Highest Cost Category</span>
                <span class="rep-kpi-val text-warning" style="font-size: 17px; padding-top: 5px;">
                    <?php echo html_escape($summary['top_category']); ?>
                </span>
                <span class="rep-kpi-sub">Top operational expenditure</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-chart-line text-info"></i> Average Voucher Cost</span>
                <span class="rep-kpi-val text-info">
                    <?php echo ($summary['total_transactions'] > 0) ? $currency . number_format($summary['total_expense'] / $summary['total_transactions'], 2) : $currency . '0.00'; ?>
                </span>
                <span class="rep-kpi-sub">Per expense transaction</span>
            </div>
        </div>

        <!-- Chart and Table Layout -->
        <div class="row">
            <!-- Left Chart -->
            <div class="col-lg-5 mb-4 d-print-none">
                <div class="rep-table-card h-100">
                    <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-chart-donut text-primary mr-2"></i> Category Share Distribution</h5>
                    <div style="position: relative; height: 320px;">
                        <canvas id="categoryDonutChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Right / Full Table -->
            <div class="col-lg-7 mb-4">
                <div class="rep-table-card h-100">
                    <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-list-alt text-success mr-2"></i> Category Performance Ledger</h5>
                    <div class="table-responsive">
                        <table class="table table-hover rep-table" id="categoryReportTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Expense Category</th>
                                    <th class="text-center">Vouchers Count</th>
                                    <th class="text-right">Total Amount (<?php echo trim($currency); ?>)</th>
                                    <th class="text-center">Share %</th>
                                    <th class="text-right">Avg Voucher</th>
                                    <th class="text-center d-print-none">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($records)): 
                                    $sl = 1;
                                    $chart_labels = [];
                                    $chart_values = [];
                                    foreach ($records as $r): 
                                        $chart_labels[] = $r->category_name;
                                        $chart_values[] = floatval($r->total_amount);
                                ?>
                                    <tr>
                                        <td><?php echo $sl++; ?></td>
                                        <td class="font-weight-bold text-dark">
                                            <i class="fas fa-folder-open text-warning mr-1"></i> <?php echo html_escape($r->category_name); ?>
                                        </td>
                                        <td class="text-center font-weight-bold"><?php echo number_format($r->tx_count); ?></td>
                                        <td class="text-right font-weight-bold text-danger" style="font-size: 14.5px;">
                                            <?php echo number_format($r->total_amount, 2); ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary px-2 py-1"><?php echo $r->share_pct; ?>%</span>
                                        </td>
                                        <td class="text-right text-muted"><?php echo number_format($r->avg_cost, 2); ?></td>
                                        <td class="text-center d-print-none">
                                            <a href="finance/dailyExpenseReport?category=<?php echo urlencode($r->category_name); ?>&period_type=<?php echo $period['period_type']; ?>" class="btn btn-xs btn-outline-primary" title="View vouchers">
                                                <i class="fas fa-list"></i> Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">No category expenditures found for this period.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if (!empty($records)): ?>
                            <tfoot>
                                <tr class="rep-total-row">
                                    <td colspan="2" class="font-weight-bold">Total:</td>
                                    <td class="text-center font-weight-bold"><?php echo number_format($summary['total_transactions']); ?></td>
                                    <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_expense'], 2); ?></td>
                                    <td class="text-center font-weight-bold">100%</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Signatures -->
        <div class="d-none d-print-block mt-5 pt-4">
            <div class="row text-center">
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Accounts Officer</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Audit Head</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Managing Director</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    $('.rep-period-btn').on('click', function() {
        var period = $(this).data('period');
        $('#period_type_input').val(period);
        $(this).closest('form').submit();
    });

    <?php if (!empty($chart_labels)): ?>
    var ctx = document.getElementById('categoryDonutChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($chart_values); ?>,
                    backgroundColor: [
                        '#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6',
                        '#06b6d4', '#ec4899', '#6366f1', '#14b8a6', '#f97316'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>
