<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$currency = !empty($settings->currency) ? $settings->currency : '৳';
$period_label = !empty($summary['period_label']) ? $summary['period_label'] : ucfirst(str_replace('_', ' ', $period_type));
$prev_label = !empty($summary['prev_label']) ? $summary['prev_label'] : 'vs previous period';
$trends = !empty($summary['trends']) ? $summary['trends'] : [];
$date_from_val = !empty($summary['date_from']) ? $summary['date_from'] : date('Y-m-01');
$date_to_val = !empty($summary['date_to']) ? $summary['date_to'] : date('Y-m-d');
?>

<!-- Financial Dashboard Styles & Chart.js -->
<link rel="stylesheet" href="application/modules/dashboard/views/css/financial_dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<div class="content-wrapper">
    <div class="fin-dashboard-wrapper">

        <!-- Top Header & Controls -->
        <div class="fin-top-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div class="fin-header-title">
                    <h2>
                        <i class="fas fa-chart-line text-primary"></i> 
                        Centralized Financial Dashboard
                    </h2>
                    <p>
                        <i class="far fa-hospital mr-1"></i> Lifecare Diagnostic Center &bull; 
                        <span id="current_period_label" class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span>
                    </p>
                </div>
                <div class="fin-actions-group">
                    <button type="button" class="fin-action-btn btn-refresh" id="btn_refresh_dashboard" title="Refresh Dashboard">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button type="button" class="fin-action-btn" onclick="window.print()" title="Print Summary">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="finance/exportFinancialReportPdf?period_type=<?php echo urlencode($period_type); ?>" class="fin-action-btn" target="_blank" title="Export PDF">
                        <i class="fas fa-file-pdf text-danger"></i> PDF
                    </a>
                    <a href="finance/exportFinancialReportExcel?period_type=<?php echo urlencode($period_type); ?>" class="fin-action-btn" target="_blank" title="Export Excel">
                        <i class="fas fa-file-excel text-success"></i> Excel
                    </a>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="fin-filter-container">
                <!-- Period Pills -->
                <div class="fin-period-pills" id="period_pills">
                    <button type="button" class="fin-period-btn <?php echo ($period_type === 'today') ? 'active' : ''; ?>" data-period="today">Today</button>
                    <button type="button" class="fin-period-btn <?php echo ($period_type === 'yesterday') ? 'active' : ''; ?>" data-period="yesterday">Yesterday</button>
                    <button type="button" class="fin-period-btn <?php echo ($period_type === 'this_week') ? 'active' : ''; ?>" data-period="this_week">This Week</button>
                    <button type="button" class="fin-period-btn <?php echo ($period_type === 'this_month') ? 'active' : ''; ?>" data-period="this_month">This Month</button>
                    <button type="button" class="fin-period-btn <?php echo ($period_type === 'last_month') ? 'active' : ''; ?>" data-period="last_month">Last Month</button>
                    <button type="button" class="fin-period-btn <?php echo ($period_type === 'this_year') ? 'active' : ''; ?>" data-period="this_year">This Year</button>
                    <button type="button" class="fin-period-btn <?php echo ($period_type === 'custom') ? 'active' : ''; ?>" data-period="custom">Custom Range</button>
                </div>

                <!-- Custom Date Range Controls -->
                <div class="fin-custom-range-wrap" id="custom_range_container" style="<?php echo ($period_type === 'custom') ? '' : 'display:none;'; ?>">
                    <span class="text-muted font-weight-bold" style="font-size: 12px;">From:</span>
                    <input type="date" id="date_from_input" class="fin-date-picker" value="<?php echo html_escape($date_from_val); ?>">
                    <span class="text-muted font-weight-bold" style="font-size: 12px;">To:</span>
                    <input type="date" id="date_to_input" class="fin-date-picker" value="<?php echo html_escape($date_to_val); ?>">
                    <button type="button" class="fin-btn-apply" id="btn_apply_custom">Apply</button>
                </div>
            </div>
        </div>

        <!-- 9 Core KPI Cards -->
        <div class="fin-kpi-grid">
            <!-- 1. Gross Billing -->
            <div class="fin-kpi-card kpi-blue">
                <div class="fin-kpi-top">
                    <div class="fin-kpi-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    <?php $t = isset($trends['gross_billing']) ? $trends['gross_billing'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                    <span class="fin-trend-badge trend-<?php echo $t['dir']; ?>" id="trend_gross_billing">
                        <?php echo ($t['dir'] === 'up') ? '&uarr;' : (($t['dir'] === 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                    </span>
                </div>
                <div class="fin-kpi-label">Gross Billing</div>
                <div class="fin-kpi-value" id="kpi_gross_billing"><?php echo $currency; ?> <?php echo number_format($summary['gross_billing'], 2); ?></div>
                <div class="fin-kpi-meta">
                    <span>Total Test Invoices</span>
                    <span class="fin-kpi-pill" id="kpi_total_invoices"><?php echo intval($summary['total_invoices']); ?> Invoices</span>
                </div>
            </div>

            <!-- 2. Total Discount -->
            <div class="fin-kpi-card kpi-rose">
                <div class="fin-kpi-top">
                    <div class="fin-kpi-icon"><i class="fas fa-tags"></i></div>
                    <?php $t = isset($trends['total_discount']) ? $trends['total_discount'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                    <span class="fin-trend-badge trend-<?php echo $t['dir']; ?>" id="trend_total_discount">
                        <?php echo ($t['dir'] === 'up') ? '&uarr;' : (($t['dir'] === 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                    </span>
                </div>
                <div class="fin-kpi-label">Total Discount</div>
                <div class="fin-kpi-value" id="kpi_total_discount"><?php echo $currency; ?> <?php echo number_format($summary['total_discount'], 2); ?></div>
                <div class="fin-kpi-meta">
                    <span>Patient & Referrer Deductions</span>
                    <span class="fin-kpi-pill">Granted</span>
                </div>
            </div>

            <!-- 3. Net Payable -->
            <div class="fin-kpi-card kpi-cyan">
                <div class="fin-kpi-top">
                    <div class="fin-kpi-icon"><i class="fas fa-receipt"></i></div>
                    <?php $t = isset($trends['net_billing']) ? $trends['net_billing'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                    <span class="fin-trend-badge trend-<?php echo $t['dir']; ?>" id="trend_net_billing">
                        <?php echo ($t['dir'] === 'up') ? '&uarr;' : (($t['dir'] === 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                    </span>
                </div>
                <div class="fin-kpi-label">Net Payable</div>
                <div class="fin-kpi-value" id="kpi_net_billing"><?php echo $currency; ?> <?php echo number_format($summary['net_billing'], 2); ?></div>
                <div class="fin-kpi-meta">
                    <span>Gross - Discount</span>
                    <span class="fin-kpi-pill">Net Demand</span>
                </div>
            </div>

            <!-- 4. Total Collection -->
            <div class="fin-kpi-card kpi-green">
                <div class="fin-kpi-top">
                    <div class="fin-kpi-icon"><i class="fas fa-hand-holding-usd"></i></div>
                    <?php $t = isset($trends['total_collection']) ? $trends['total_collection'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                    <span class="fin-trend-badge trend-<?php echo $t['dir']; ?>" id="trend_total_collection">
                        <?php echo ($t['dir'] === 'up') ? '&uarr;' : (($t['dir'] === 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                    </span>
                </div>
                <div class="fin-kpi-label">Total Collection</div>
                <div class="fin-kpi-value" id="kpi_total_collection"><?php echo $currency; ?> <?php echo number_format($summary['total_collection'], 2); ?></div>
                <div class="fin-kpi-meta">
                    <span>Actual Received Deposits</span>
                    <span class="fin-kpi-pill text-success font-weight-bold">Cleared Cash</span>
                </div>
            </div>

            <!-- 5. Outstanding Due -->
            <div class="fin-kpi-card kpi-amber">
                <div class="fin-kpi-top">
                    <div class="fin-kpi-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <?php $t = isset($trends['total_due']) ? $trends['total_due'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                    <span class="fin-trend-badge trend-<?php echo $t['dir']; ?>" id="trend_total_due">
                        <?php echo ($t['dir'] === 'up') ? '&uarr;' : (($t['dir'] === 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                    </span>
                </div>
                <div class="fin-kpi-label">Outstanding Due</div>
                <div class="fin-kpi-value" id="kpi_total_due"><?php echo $currency; ?> <?php echo number_format($summary['total_due'], 2); ?></div>
                <div class="fin-kpi-meta">
                    <span>Uncollected Receivables</span>
                    <span class="fin-kpi-pill text-warning font-weight-bold">Unpaid</span>
                </div>
            </div>

            <!-- 6. Total Expenses -->
            <div class="fin-kpi-card kpi-crimson">
                <div class="fin-kpi-top">
                    <div class="fin-kpi-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <?php $t = isset($trends['total_expenses']) ? $trends['total_expenses'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                    <span class="fin-trend-badge trend-<?php echo $t['dir']; ?>" id="trend_total_expenses">
                        <?php echo ($t['dir'] === 'up') ? '&uarr;' : (($t['dir'] === 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                    </span>
                </div>
                <div class="fin-kpi-label">Total Expenses</div>
                <div class="fin-kpi-value" id="kpi_total_expenses"><?php echo $currency; ?> <?php echo number_format($summary['total_expenses'], 2); ?></div>
                <div class="fin-kpi-meta">
                    <span>Hospital Operating Outflow</span>
                    <span class="fin-kpi-pill">Expenses</span>
                </div>
            </div>

            <!-- 7. Doctor Commission -->
            <div class="fin-kpi-card kpi-purple">
                <div class="fin-kpi-top">
                    <div class="fin-kpi-icon"><i class="fas fa-user-md"></i></div>
                    <?php $t = isset($trends['doctor_commission']) ? $trends['doctor_commission'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                    <span class="fin-trend-badge trend-<?php echo $t['dir']; ?>" id="trend_doctor_commission">
                        <?php echo ($t['dir'] === 'up') ? '&uarr;' : (($t['dir'] === 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                    </span>
                </div>
                <div class="fin-kpi-label">Doctor Commission</div>
                <div class="fin-kpi-value" id="kpi_doctor_commission"><?php echo $currency; ?> <?php echo number_format($summary['doctor_commission'], 2); ?></div>
                <div class="fin-kpi-meta">
                    <span id="kpi_doctor_paid_text">Paid: <?php echo $currency; ?> <?php echo number_format($summary['paid_doctor_comm'], 2); ?></span>
                    <span class="fin-kpi-pill" id="kpi_doctor_pending_text">Pending: <?php echo $currency; ?> <?php echo number_format($summary['pending_doctor_comm'], 2); ?></span>
                </div>
            </div>

            <!-- 8. Referral Commission -->
            <div class="fin-kpi-card kpi-indigo">
                <div class="fin-kpi-top">
                    <div class="fin-kpi-icon"><i class="fas fa-users-cog"></i></div>
                    <?php $t = isset($trends['referral_commission']) ? $trends['referral_commission'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                    <span class="fin-trend-badge trend-<?php echo $t['dir']; ?>" id="trend_referral_commission">
                        <?php echo ($t['dir'] === 'up') ? '&uarr;' : (($t['dir'] === 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                    </span>
                </div>
                <div class="fin-kpi-label">Referral Commission</div>
                <div class="fin-kpi-value" id="kpi_referral_commission"><?php echo $currency; ?> <?php echo number_format($summary['referral_commission'], 2); ?></div>
                <div class="fin-kpi-meta">
                    <span id="kpi_ref_earned_text">Earned: <?php echo $currency; ?> <?php echo number_format($summary['earned_ref_comm'], 2); ?></span>
                    <span class="fin-kpi-pill" id="kpi_ref_pending_text">Pending: <?php echo $currency; ?> <?php echo number_format($summary['pending_ref_comm'], 2); ?></span>
                </div>
            </div>

            <!-- 9. Net Operating Profit -->
            <div class="fin-kpi-card kpi-emerald">
                <div class="fin-kpi-top">
                    <div class="fin-kpi-icon"><i class="fas fa-chart-pie"></i></div>
                    <span class="fin-trend-badge trend-up" id="kpi_net_margin_badge">
                        Margin: <?php echo number_format($summary['net_margin'], 2); ?>%
                    </span>
                </div>
                <div class="fin-kpi-label">Net Operating Profit</div>
                <div class="fin-kpi-value text-success" id="kpi_net_profit"><?php echo $currency; ?> <?php echo number_format($summary['net_profit'], 2); ?></div>
                <div class="fin-kpi-meta">
                    <span>Net Revenue - Expenses - Comm</span>
                    <span class="fin-kpi-pill text-success font-weight-bold">Operating Margin</span>
                </div>
            </div>
        </div>

        <!-- Charts & Analytics Row 1 -->
        <div class="fin-chart-grid">
            <!-- Main Trend Chart -->
            <div class="fin-chart-card">
                <div class="fin-chart-header">
                    <h3><i class="fas fa-chart-area text-primary"></i> Financial Flow &amp; Collection Trend</h3>
                    <div class="fin-chart-toggle">
                        <button type="button" class="fin-toggle-btn active" id="btn_toggle_collections" data-mode="collection">Collections Only</button>
                        <button type="button" class="fin-toggle-btn" id="btn_toggle_comparison" data-mode="comparison">Billing vs Collection vs Expense</button>
                    </div>
                </div>
                <div class="fin-chart-body">
                    <canvas id="mainTrendChart"></canvas>
                </div>
            </div>

            <!-- Payment Method Distribution -->
            <div class="fin-chart-card">
                <div class="fin-chart-header">
                    <h3><i class="fas fa-wallet text-success"></i> Payment Methods</h3>
                </div>
                <div class="fin-donut-wrapper">
                    <div style="height: 190px; width: 100%;">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                    <div class="fin-method-list" id="payment_method_list">
                        <?php 
                        $method_colors = [
                            'Cash' => '#10b981',
                            'Card' => '#3b82f6',
                            'Bkash' => '#e11d48',
                            'Nagad' => '#f97316',
                            'Rocket' => '#8b5cf6',
                            'Bank' => '#06b6d4',
                            'Others' => '#64748b'
                        ];
                        foreach ($methods as $m_name => $m_data): 
                            $c = isset($method_colors[$m_name]) ? $method_colors[$m_name] : '#64748b';
                        ?>
                        <div class="fin-method-item">
                            <span class="fin-method-badge">
                                <span class="fin-method-dot" style="background-color: <?php echo $c; ?>;"></span>
                                <?php echo html_escape($m_name); ?>
                            </span>
                            <span class="fin-method-val">
                                <?php echo $currency; ?> <?php echo number_format($m_data['amount'], 2); ?>
                                <small class="text-muted font-weight-normal">(<?php echo $m_data['percentage']; ?>%)</small>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts & Analytics Row 2 -->
        <div class="fin-chart-grid" style="grid-template-columns: 1fr 1fr;">
            <!-- Expense Category Distribution -->
            <div class="fin-chart-card">
                <div class="fin-chart-header">
                    <h3><i class="fas fa-receipt text-danger"></i> Expense Categories Breakdown</h3>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div style="height: 220px; width: 100%;">
                            <canvas id="expenseCategoryChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="fin-method-list" id="expense_category_list" style="max-height: 220px; overflow-y: auto;">
                            <?php 
                            $tot_exp = floatval($summary['total_expenses']);
                            if (!empty($expense_breakdown)):
                                foreach ($expense_breakdown as $c_name => $c_amt):
                                    $pct = ($tot_exp > 0) ? round(($c_amt / $tot_exp) * 100, 1) : 0;
                            ?>
                            <div class="fin-method-item">
                                <span class="fin-method-badge">
                                    <span class="fin-method-dot" style="background-color: #ef4444;"></span>
                                    <?php echo html_escape($c_name); ?>
                                </span>
                                <span class="fin-method-val">
                                    <?php echo $currency; ?> <?php echo number_format($c_amt, 2); ?>
                                    <small class="text-muted font-weight-normal">(<?php echo $pct; ?>%)</small>
                                </span>
                            </div>
                            <?php 
                                endforeach;
                            else: 
                            ?>
                            <div class="text-muted text-center py-4">No expenses recorded for this period</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Income vs Expense Bar Chart -->
            <div class="fin-chart-card">
                <div class="fin-chart-header">
                    <h3><i class="fas fa-balance-scale text-primary"></i> Income vs Expense Summary</h3>
                </div>
                <div class="fin-chart-body" style="height: 220px;">
                    <canvas id="incomeVsExpenseChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Business Performance & Intelligence Tables Section -->
        <div class="fin-tables-card">
            <!-- Tab Navigation -->
            <div class="fin-nav-tabs">
                <button type="button" class="fin-tab-link active" data-tab="tab_diagnostic_tests">
                    <i class="fas fa-microscope text-primary"></i> Top Performing Diagnostic Tests
                </button>
                <button type="button" class="fin-tab-link" data-tab="tab_doctor_commissions">
                    <i class="fas fa-user-md text-purple"></i> Doctor Commission Summary
                </button>
                <button type="button" class="fin-tab-link" data-tab="tab_referral_commissions">
                    <i class="fas fa-users-cog text-indigo"></i> Referral Commission Summary
                </button>
            </div>

            <!-- Tab 1: Diagnostic Tests -->
            <div class="fin-tab-pane active" id="tab_diagnostic_tests">
                <div class="table-responsive">
                    <table class="fin-table" id="table_diagnostic_tests">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Diagnostic Test / Service</th>
                                <th class="text-center">Frequency (Qty)</th>
                                <th class="text-right">Gross Billing</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">Net Billing</th>
                                <th class="text-right">Ref. Commission</th>
                                <th class="text-right">Hospital Net Share</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_diagnostic_tests">
                            <?php 
                            if (!empty($service_perf)):
                                $rank = 1;
                                foreach ($service_perf as $test):
                                    $rank_class = ($rank <= 3) ? 'fin-rank-' . $rank : '';
                            ?>
                            <tr>
                                <td><span class="fin-rank-badge <?php echo $rank_class; ?>"><?php echo $rank++; ?></span></td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($test['name']); ?></td>
                                <td class="text-center font-weight-bold"><?php echo intval($test['qty']); ?></td>
                                <td class="text-right"><?php echo $currency; ?> <?php echo number_format($test['gross'], 2); ?></td>
                                <td class="text-right text-danger">- <?php echo $currency; ?> <?php echo number_format($test['discount'], 2); ?></td>
                                <td class="text-right font-weight-bold text-dark"><?php echo $currency; ?> <?php echo number_format($test['net'], 2); ?></td>
                                <td class="text-right text-purple">- <?php echo $currency; ?> <?php echo number_format($test['ref_comm'], 2); ?></td>
                                <td class="text-right font-weight-bold text-success"><?php echo $currency; ?> <?php echo number_format($test['hospital_net'], 2); ?></td>
                            </tr>
                            <?php 
                                endforeach;
                            else: 
                            ?>
                            <tr><td colspan="8" class="text-center py-4 text-muted">No diagnostic services recorded for this period</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Doctor Commissions -->
            <div class="fin-tab-pane" id="tab_doctor_commissions">
                <div class="table-responsive">
                    <table class="fin-table" id="table_doctor_commissions">
                        <thead>
                            <tr>
                                <th>Doctor Name</th>
                                <th class="text-center">Total Invoices</th>
                                <th class="text-right">Total Billing</th>
                                <th class="text-right">Commission Generated</th>
                                <th class="text-right">Paid Commission</th>
                                <th class="text-right">Pending Commission</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_doctor_commissions">
                            <?php 
                            if (!empty($doctor_breakdown)):
                                foreach ($doctor_breakdown as $doc):
                                    $is_clear = ($doc['pending_commission'] <= 0 && $doc['commission'] > 0);
                            ?>
                            <tr>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($doc['name']); ?></td>
                                <td class="text-center"><?php echo intval($doc['invoices']); ?></td>
                                <td class="text-right"><?php echo $currency; ?> <?php echo number_format($doc['billing'], 2); ?></td>
                                <td class="text-right font-weight-bold text-purple"><?php echo $currency; ?> <?php echo number_format($doc['commission'], 2); ?></td>
                                <td class="text-right text-success"><?php echo $currency; ?> <?php echo number_format($doc['paid_commission'], 2); ?></td>
                                <td class="text-right text-warning font-weight-bold"><?php echo $currency; ?> <?php echo number_format($doc['pending_commission'], 2); ?></td>
                                <td class="text-center">
                                    <?php if ($doc['commission'] <= 0): ?>
                                        <span class="badge-status badge-eligible">None</span>
                                    <?php elseif ($is_clear): ?>
                                        <span class="badge-status badge-paid">Fully Cleared</span>
                                    <?php else: ?>
                                        <span class="badge-status badge-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                endforeach;
                            else: 
                            ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">No doctor commission records for this period</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Referral Commissions -->
            <div class="fin-tab-pane" id="tab_referral_commissions">
                <div class="table-responsive">
                    <table class="fin-table" id="table_referral_commissions">
                        <thead>
                            <tr>
                                <th>Referrer Name</th>
                                <th class="text-center">Patients Referred</th>
                                <th class="text-center">Invoices</th>
                                <th class="text-right">Referred Billing</th>
                                <th class="text-right">Commission Earned</th>
                                <th class="text-right">Pending Commission</th>
                                <th class="text-right">Available Wallet Balance</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_referral_commissions">
                            <?php 
                            if (!empty($referrer_breakdown)):
                                foreach ($referrer_breakdown as $ref):
                            ?>
                            <tr>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($ref['name']); ?></td>
                                <td class="text-center"><?php echo intval($ref['patient_count']); ?></td>
                                <td class="text-center"><?php echo intval($ref['invoice_count']); ?></td>
                                <td class="text-right"><?php echo $currency; ?> <?php echo number_format($ref['billing'], 2); ?></td>
                                <td class="text-right text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($ref['earned'], 2); ?></td>
                                <td class="text-right text-warning font-weight-bold"><?php echo $currency; ?> <?php echo number_format($ref['pending'], 2); ?></td>
                                <td class="text-right text-primary font-weight-bold"><?php echo $currency; ?> <?php echo number_format($ref['wallet_balance'], 2); ?></td>
                            </tr>
                            <?php 
                                endforeach;
                            else: 
                            ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">No referral commission records for this period</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Embedded Server State & Dynamic Chart Setup Script -->
<script>
window.financialDashboardData = {
    currency: <?php echo json_encode($currency); ?>,
    periodType: <?php echo json_encode($period_type); ?>,
    chartData: <?php echo json_encode($chart_data); ?>,
    summary: <?php echo json_encode($summary); ?>,
    methods: <?php echo json_encode($methods); ?>,
    expenseBreakdown: <?php echo json_encode($expense_breakdown); ?>
};
</script>

<script src="<?php echo base_url('application/modules/dashboard/views/js/financial_dashboard.js'); ?>"></script>
