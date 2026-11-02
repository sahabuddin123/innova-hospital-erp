<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳';
$is_accrual = ($accounting_basis === 'accrual');
?>
<link rel="stylesheet" href="common/extranal/css/finance/pnl_reports.css">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <?php $this->load->view('finance/inc_pnl_reports_nav'); ?>

        <!-- Header Card -->
        <div class="rep-header-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 16px;">
                <div class="rep-title">
                    <h2>
                        <i class="fas fa-chart-pie text-primary"></i> 
                        Profit &amp; Loss Executive Dashboard
                        <?php if ($is_accrual) { ?>
                            <span class="badge badge-accrual"><i class="fas fa-file-invoice"></i> Accrual (Billing) Basis</span>
                        <?php } else { ?>
                            <span class="badge badge-cash"><i class="fas fa-money-bill-wave"></i> Collection (Cash) Basis</span>
                        <?php } ?>
                    </h2>
                    <p>Real-time operating revenues, expenses, commissions, and net operating performance &bull; <strong><?php echo $period['period_label']; ?></strong></p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <!-- Accounting Basis Switcher -->
                    <div class="basis-toggle-group d-print-none">
                        <a href="finance/profitLossDashboard?period_type=<?php echo $period_type; ?>&accounting_basis=cash<?php echo !empty($date_from) ? '&date_from=' . $date_from . '&date_to=' . $date_to : ''; ?>" 
                           class="basis-toggle-btn <?php echo (!$is_accrual) ? 'active-cash' : ''; ?>" title="Cash / Collection Basis: Measures actual money received & disbursed">
                            <i class="fas fa-coins"></i> Cash Basis
                        </a>
                        <a href="finance/profitLossDashboard?period_type=<?php echo $period_type; ?>&accounting_basis=accrual<?php echo !empty($date_from) ? '&date_from=' . $date_from . '&date_to=' . $date_to : ''; ?>" 
                           class="basis-toggle-btn <?php echo ($is_accrual) ? 'active-accrual' : ''; ?>" title="Accrual / Billing Basis: Measures recognized revenue & expenses">
                            <i class="fas fa-balance-scale"></i> Accrual Basis
                        </a>
                    </div>
                    <div class="rep-actions d-print-none">
                        <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'pnl_dashboard']); ?>
                        <a href="finance/profitLossReconciliation" class="rep-action-btn" style="border-color: #dc2626; color: #dc2626;"><i class="fas fa-check-double"></i> Audit Checks</a>
                    </div>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--pnl-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/profitLossDashboard?period_type=today&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/profitLossDashboard?period_type=yesterday&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/profitLossDashboard?period_type=this_week&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/profitLossDashboard?period_type=this_month&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/profitLossDashboard?period_type=last_month&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/profitLossDashboard?period_type=this_year&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_year') ? 'active' : ''; ?>">This Year</a>
            </div>

            <!-- Custom Filter Form -->
            <form action="finance/profitLossDashboard" method="get" class="row g-3 align-items-end mt-2 d-print-none">
                <input type="hidden" name="period_type" value="custom">
                <input type="hidden" name="accounting_basis" value="<?php echo $accounting_basis; ?>">
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo html_escape($period['date_from']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo html_escape($period['date_to']); ?>">
                </div>
                <div class="col-md-4 d-flex" style="gap: 8px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-filter"></i> Apply Filter</button>
                    <a href="finance/profitLossDashboard?accounting_basis=<?php echo $accounting_basis; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>

        <!-- Callout Banner Explaining Basis -->
        <div class="alert <?php echo ($is_accrual) ? 'alert-primary' : 'alert-success'; ?> shadow-sm border-0 d-flex align-items-center mb-4" role="alert" style="border-radius: var(--pnl-radius);">
            <div class="mr-3 font-weight-bold" style="font-size: 20px;">
                <i class="fas <?php echo ($is_accrual) ? 'fa-info-circle text-primary' : 'fa-check-circle text-success'; ?>"></i>
            </div>
            <div>
                <?php if ($is_accrual) { ?>
                    <strong>Accrual-Based Operating Performance:</strong> Measures revenue and recognized obligations on invoices generated between <strong><?php echo $period['period_label']; ?></strong>. Revenue is recognized at billing date, regardless of when collections occur.
                <?php } else { ?>
                    <strong>Collection-Based Operating Performance (Recommended):</strong> Measures actual cash flow during <strong><?php echo $period['period_label']; ?></strong>. Revenue represents cash received from patients; expenses and commissions represent actual completed payouts only.
                <?php } ?>
            </div>
        </div>

        <!-- 9 Primary KPI Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-card">
                <div class="rep-card-icon bg-blue"><i class="fas fa-file-invoice"></i></div>
                <div class="rep-card-label">Gross Billing</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></div>
                <div class="rep-card-sub text-muted">Total invoice list prices</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-purple"><i class="fas fa-tags"></i></div>
                <div class="rep-card-label">Total Discounts</div>
                <div class="rep-card-val text-danger"><?php echo $currency . ' ' . number_format($summary['total_discount'], 2); ?></div>
                <div class="rep-card-sub text-muted">Granted concession</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-teal"><i class="fas fa-receipt"></i></div>
                <div class="rep-card-label">Net Billing</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></div>
                <div class="rep-card-sub text-muted">Gross minus discounts</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-green"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="rep-card-label">Patient Collections</div>
                <div class="rep-card-val text-success"><?php echo $currency . ' ' . number_format($summary['patient_collections'], 2); ?></div>
                <div class="rep-card-sub text-success">Actual cash received</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-orange"><i class="fas fa-undo"></i></div>
                <div class="rep-card-label">Actual Refunds</div>
                <div class="rep-card-val text-warning"><?php echo $currency . ' ' . number_format($summary['refunds'], 2); ?></div>
                <div class="rep-card-sub text-muted">Returned deposits</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-emerald"><i class="fas fa-coins"></i></div>
                <div class="rep-card-label">Net Collections</div>
                <div class="rep-card-val text-success"><?php echo $currency . ' ' . number_format($summary['net_collections'], 2); ?></div>
                <div class="rep-card-sub text-success">Collections minus refunds</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-red"><i class="fas fa-wallet"></i></div>
                <div class="rep-card-label">Operating Expenses Paid</div>
                <div class="rep-card-val text-danger"><?php echo $currency . ' ' . number_format($summary['operating_expenses_paid'], 2); ?></div>
                <div class="rep-card-sub text-muted">General operating costs</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-indigo"><i class="fas fa-user-md"></i></div>
                <div class="rep-card-label">Doctor Commission Paid</div>
                <div class="rep-card-val text-danger"><?php echo $currency . ' ' . number_format($summary['doctor_comm_paid'], 2); ?></div>
                <div class="rep-card-sub text-muted">Actual physician payouts</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-cyan"><i class="fas fa-user-friends"></i></div>
                <div class="rep-card-label">Referral Withdrawn</div>
                <div class="rep-card-val text-danger"><?php echo $currency . ' ' . number_format($summary['referral_comm_withdrawn'], 2); ?></div>
                <div class="rep-card-sub text-muted">Completed cash payouts</div>
            </div>
        </div>

        <!-- Executive Profit Highlight Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: var(--pnl-radius); background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <span class="badge badge-warning text-dark font-weight-bold px-3 py-1 mb-2">
                            <i class="fas fa-award mr-1"></i> <?php echo html_escape($summary['active_kpi']['result_label']); ?>
                        </span>
                        <h2 class="display-4 font-weight-bold mb-1" style="color: <?php echo ($summary['active_kpi']['result_value'] >= 0) ? '#34d399' : '#f87171'; ?>;">
                            <?php echo $currency . ' ' . number_format($summary['active_kpi']['result_value'], 2); ?>
                        </h2>
                        <p class="text-light mb-0 small" style="opacity: 0.9;">
                            Calculated as: <strong><?php echo $summary['active_kpi']['revenue_label']; ?> (<?php echo $currency . ' ' . number_format($summary['active_kpi']['revenue_value'], 2); ?>)</strong>
                            &minus; <strong><?php echo $summary['active_kpi']['expenses_label']; ?> (<?php echo $currency . ' ' . number_format($summary['active_kpi']['expenses_value'], 2); ?>)</strong>
                        </p>
                    </div>
                    <div class="col-md-5 text-md-right mt-3 mt-md-0">
                        <div class="d-inline-block text-center p-3 rounded" style="background: rgba(255, 255, 255, 0.1); min-width: 160px;">
                            <div class="small text-uppercase tracking-wider text-light" style="opacity: 0.8;">Operating Margin</div>
                            <div class="h2 font-weight-bold mb-0 text-white"><?php echo number_format($summary['active_kpi']['margin'], 2); ?>%</div>
                            <div class="small text-success mt-1"><i class="fas fa-chart-line"></i> Performance Ratio</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual Analytics Grid (Charts) -->
        <div class="row g-4 mb-4">
            <!-- 1. Monthly Collections vs Expenses Trend -->
            <div class="col-lg-8">
                <div class="chart-card">
                    <h4><i class="fas fa-chart-area text-primary"></i> 12-Month Financial Performance Trend (Collections vs Outflows)</h4>
                    <div style="position: relative; height: 320px;">
                        <canvas id="chart-collections-expenses"></canvas>
                    </div>
                </div>
            </div>

            <!-- 2. Expense Category Distribution -->
            <div class="col-lg-4">
                <div class="chart-card">
                    <h4><i class="fas fa-chart-pie text-info"></i> Expense Category Distribution</h4>
                    <div style="position: relative; height: 320px;">
                        <canvas id="chart-expense-categories"></canvas>
                    </div>
                </div>
            </div>

            <!-- 3. Department-wise Revenue Breakdown -->
            <div class="col-lg-6">
                <div class="chart-card">
                    <h4><i class="fas fa-sitemap text-purple"></i> Department Revenue Distribution</h4>
                    <div style="position: relative; height: 280px;">
                        <canvas id="chart-department-revenue"></canvas>
                    </div>
                </div>
            </div>

            <!-- 4. Top Performing Tests -->
            <div class="col-lg-6">
                <div class="chart-card">
                    <h4><i class="fas fa-vial text-teal"></i> Top Diagnostic Tests by Revenue</h4>
                    <div style="position: relative; height: 280px;">
                        <canvas id="chart-top-tests"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Sub-Report Access Cards -->
        <div class="row g-3 d-print-none mb-4">
            <div class="col-md-3">
                <a href="finance/dailyProfitLoss" class="card shadow-sm border-0 text-decoration-none h-100 p-3 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="d-flex align-items-center">
                        <div class="rep-card-icon bg-blue mr-3 mb-0"><i class="fas fa-calendar-day"></i></div>
                        <div>
                            <div class="font-weight-bold text-dark">Daily P&amp;L</div>
                            <div class="small text-muted">Hourly drill-down</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="finance/monthlyProfitLoss" class="card shadow-sm border-0 text-decoration-none h-100 p-3 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="d-flex align-items-center">
                        <div class="rep-card-icon bg-green mr-3 mb-0"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <div class="font-weight-bold text-dark">Monthly P&amp;L</div>
                            <div class="small text-muted">MoM variance &amp; YTD</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="finance/departmentProfitability" class="card shadow-sm border-0 text-decoration-none h-100 p-3 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="d-flex align-items-center">
                        <div class="rep-card-icon bg-purple mr-3 mb-0"><i class="fas fa-sitemap"></i></div>
                        <div>
                            <div class="font-weight-bold text-dark">Department P&amp;L</div>
                            <div class="small text-muted">Contribution margins</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="finance/profitLossReconciliation" class="card shadow-sm border-0 text-decoration-none h-100 p-3 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="d-flex align-items-center">
                        <div class="rep-card-icon bg-red mr-3 mb-0"><i class="fas fa-balance-scale"></i></div>
                        <div>
                            <div class="font-weight-bold text-dark">Reconciliation</div>
                            <div class="small text-muted">Automated integrity audit</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Print Signatures -->
        <div class="d-none d-print-block rep-print-signatures">
            <div class="signature-box">Prepared By (Accounts Officer)</div>
            <div class="signature-box">Checked By (Audit Head)</div>
            <div class="signature-box">Approved By (Managing Director)</div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    // 1. Collections vs Expenses Trend Chart
    var ctxTrend = document.getElementById('chart-collections-expenses').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($charts['months']); ?>,
            datasets: [
                {
                    label: 'Net Collections',
                    data: <?php echo json_encode($charts['trend_collections']); ?>,
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Total Expenses',
                    data: <?php echo json_encode($charts['trend_expenses']); ?>,
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Operating Surplus',
                    data: <?php echo json_encode($charts['trend_surplus']); ?>,
                    borderColor: '#0284c7',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '৳' + value.toLocaleString(); }
                    }
                }
            }
        }
    });

    // 2. Expense Category Distribution Donut
    var ctxExp = document.getElementById('chart-expense-categories').getContext('2d');
    new Chart(ctxExp, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(!empty($charts['expense_categories']) ? $charts['expense_categories'] : ['No Expenses in Period']); ?>,
            datasets: [{
                data: <?php echo json_encode(!empty($charts['expense_category_amounts']) ? $charts['expense_category_amounts'] : [1]); ?>,
                backgroundColor: ['#0284c7', '#059669', '#d97706', '#7c3aed', '#dc2626', '#0d9488', '#4f46e5', '#64748b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 3. Department Revenue Chart
    var ctxDept = document.getElementById('chart-department-revenue').getContext('2d');
    new Chart(ctxDept, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(!empty($charts['dept_labels']) ? $charts['dept_labels'] : ['Pathology']); ?>,
            datasets: [{
                data: <?php echo json_encode(!empty($charts['dept_data']) ? $charts['dept_data'] : [1]); ?>,
                backgroundColor: ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 4. Top Tests Chart
    var ctxTests = document.getElementById('chart-top-tests').getContext('2d');
    new Chart(ctxTests, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(!empty($charts['test_labels']) ? $charts['test_labels'] : ['General Diagnostic Test']); ?>,
            datasets: [{
                label: 'Revenue',
                data: <?php echo json_encode(!empty($charts['test_data']) ? $charts['test_data'] : [0]); ?>,
                backgroundColor: '#0d9488',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '৳' + value.toLocaleString(); }
                    }
                }
            }
        }
    });
});
</script>
