<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
?>
<link href="common/extranal/css/finance/expense_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_expense_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Monthly Expense Report &bull; Year <?php echo html_escape($year); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-calendar-alt text-danger"></i> Monthly Expense Report</h2>
                    <p>Annual month-by-month expense trend, payment channels, and vendor analysis &bull; <span class="font-weight-bold text-dark">Year <?php echo html_escape($year); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/monthlyExpenseReport" class="m-0">
                <div class="rep-controls-row">
                    <div class="rep-filter-group">
                        <span class="text-muted font-weight-bold" style="font-size: 12px;">SELECT YEAR:</span>
                        <select name="year" class="rep-select" onchange="this.form.submit()">
                            <?php for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?php echo $y; ?>" <?php echo ($year == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>

                        <span class="text-muted font-weight-bold" style="font-size: 12px;">FOCUS MONTH:</span>
                        <select name="month" class="rep-select" onchange="this.form.submit()">
                            <option value="">-- All Months Overview --</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo sprintf('%02d', $m); ?>" <?php echo ($month == $m) ? 'selected' : ''; ?>>
                                    <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-sync-alt"></i> Update</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-chart-line text-danger"></i> Annual Total Expenses</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['total_expense'], 2); ?></span>
                <span class="rep-kpi-sub">Across full year <?php echo html_escape($year); ?></span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-calculator text-primary"></i> Monthly Average</span>
                <span class="rep-kpi-val"><?php echo $currency . number_format($summary['average_monthly_expense'], 2); ?></span>
                <span class="rep-kpi-sub">Average monthly burn rate</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-file-invoice text-info"></i> Total Vouchers</span>
                <span class="rep-kpi-val"><?php echo number_format($summary['total_transactions']); ?></span>
                <span class="rep-kpi-sub">Disbursement transactions logged</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-calendar-check text-warning"></i> Selected Month Burn</span>
                <span class="rep-kpi-val text-warning">
                    <?php echo !empty($selected_month) ? $currency . number_format($selected_month['total_amount'], 2) : '-'; ?>
                </span>
                <span class="rep-kpi-sub"><?php echo !empty($selected_month) ? $selected_month['month_name'] : 'Select a month'; ?></span>
            </div>
        </div>

        <!-- Monthly Trends Table -->
        <div class="rep-table-card">
            <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-table mr-2 text-primary"></i> Month-Wise Expense Breakdown (<?php echo $year; ?>)</h5>
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="monthlyExpenseTable">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-center">Vouchers Count</th>
                            <th class="text-right">Cash Expenses (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Bank Expenses (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Digital / Online (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Total Expenses (<?php echo trim($currency); ?>)</th>
                            <th class="text-center">MoM Growth</th>
                            <th class="text-center d-print-none">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthly_totals as $m_row): 
                            $is_current = !empty($month) && intval($month) === $m_row['month_num'];
                            $diff_pct = $m_row['diff_pct'];
                        ?>
                            <tr style="<?php echo $is_current ? 'background: #eff6ff; font-weight: bold;' : ''; ?>">
                                <td class="font-weight-bold text-dark">
                                    <i class="far fa-calendar-alt text-primary mr-1"></i> <?php echo $m_row['month_name']; ?>
                                    <?php if ($is_current): ?>
                                        <span class="badge badge-primary ml-1">Focused</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?php echo number_format($m_row['tx_count']); ?></td>
                                <td class="text-right text-muted"><?php echo number_format($m_row['cash_amount'], 2); ?></td>
                                <td class="text-right text-muted"><?php echo number_format($m_row['bank_amount'], 2); ?></td>
                                <td class="text-right text-muted"><?php echo number_format($m_row['online_amount'], 2); ?></td>
                                <td class="text-right font-weight-bold text-danger" style="font-size: 14px;">
                                    <?php echo number_format($m_row['total_amount'], 2); ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($m_row['month_num'] == 1): ?>
                                        <span class="text-muted">-</span>
                                    <?php elseif ($diff_pct > 0): ?>
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-arrow-up"></i> +<?php echo $diff_pct; ?>%</span>
                                    <?php elseif ($diff_pct < 0): ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-arrow-down"></i> <?php echo $diff_pct; ?>%</span>
                                    <?php else: ?>
                                        <span class="badge badge-light border">0.0%</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center d-print-none">
                                    <a href="finance/monthlyExpenseReport?year=<?php echo $year; ?>&month=<?php echo sprintf('%02d', $m_row['month_num']); ?>" class="btn btn-xs btn-outline-primary" title="Analyze this month">
                                        <i class="fas fa-search"></i> Inspect
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td class="font-weight-bold">Grand Total (Year <?php echo $year; ?>):</td>
                            <td class="text-center font-weight-bold"><?php echo number_format($summary['total_transactions']); ?></td>
                            <td colspan="3"></td>
                            <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_expense'], 2); ?></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?php if (!empty($selected_month)): ?>
        <!-- Focused Month Detail Row -->
        <div class="row">
            <!-- Left: Category Breakdown -->
            <div class="col-lg-6 mb-4">
                <div class="rep-table-card h-100">
                    <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-chart-pie text-danger mr-2"></i> Category Breakdown (<?php echo $selected_month['month_name']; ?>)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th>Category</th>
                                    <th class="text-center">Vouchers</th>
                                    <th class="text-right">Total (<?php echo trim($currency); ?>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($category_breakdown)): foreach ($category_breakdown as $c_row): ?>
                                    <tr>
                                        <td class="font-weight-bold text-dark"><?php echo html_escape($c_row->category ?: 'General'); ?></td>
                                        <td class="text-center"><?php echo $c_row->tx_count; ?></td>
                                        <td class="text-right font-weight-bold text-danger"><?php echo number_format($c_row->total, 2); ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">No expenses recorded for this month.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right: Vendor Breakdown -->
            <div class="col-lg-6 mb-4">
                <div class="rep-table-card h-100">
                    <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-truck text-primary mr-2"></i> Vendor / Payee Breakdown (<?php echo $selected_month['month_name']; ?>)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th>Payee / Vendor</th>
                                    <th class="text-center">Vouchers</th>
                                    <th class="text-right">Total (<?php echo trim($currency); ?>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($vendor_breakdown)): foreach ($vendor_breakdown as $v_row): ?>
                                    <tr>
                                        <td class="font-weight-bold text-dark"><?php echo html_escape($v_row->vendor ?: 'General Payee'); ?></td>
                                        <td class="text-center"><?php echo $v_row->tx_count; ?></td>
                                        <td class="text-right font-weight-bold text-primary"><?php echo number_format($v_row->total, 2); ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="3" class="text-center text-muted py-3">No vendor expenses recorded for this month.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

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

