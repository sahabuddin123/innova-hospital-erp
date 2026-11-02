<!-- Main content start -->
<link href="common/extranal/css/finance/financial_performance_report.css" rel="stylesheet">

<?php
$currency = !empty($settings->currency) ? $settings->currency : '৳';
$period_label = !empty($summary['period_label']) ? $summary['period_label'] : ucfirst($period_type);
$prev_label = !empty($summary['prev_label']) ? $summary['prev_label'] : 'vs previous period';
$trends = !empty($summary['trends']) ? $summary['trends'] : [];
?>

<div class="content-wrapper bg-light">
    <!-- Content Header -->
    <section class="content-header py-3 mb-3 bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="font-weight-bold mb-0 text-dark" style="font-size: 24px;">
                        <i class="fas fa-chart-line text-primary mr-2"></i>
                        <?php echo lang('financial_performance_report'); ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0 mb-0 mt-1" style="font-size: 13px;">
                            <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                            <li class="breadcrumb-item"><a href="finance"><?php echo lang('finance'); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo lang('financial_performance_report'); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-right">
                    <span class="badge badge-light border px-3 py-2 text-muted" style="font-size: 13px;">
                        <i class="far fa-hospital mr-1"></i> Lifecare Diagnostic Center &bull; 
                        <i class="far fa-calendar-alt ml-1 mr-1"></i> <?php echo html_escape($period_label); ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Control & Filter Bar -->
            <div class="fin-header-card">
                <form id="financial_report_form" method="GET" action="finance/financialPerformanceReport" class="m-0">
                    <input type="hidden" name="period_type" id="period_type_input" value="<?php echo html_escape($period_type); ?>">

                    <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 16px;">
                        <!-- Left: Title & Subtitle -->
                        <div class="fin-title-group">
                            <h2>
                                <span>Financial Performance &amp; Profit/Loss Report</span>
                            </h2>
                            <p>Complete financial summary of Lifecare Diagnostic Center &bull; <?php echo html_escape($period_label); ?></p>
                        </div>

                        <!-- Right: Period Selector, Date Input, and Action Buttons -->
                        <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                            <!-- Period Switcher Buttons -->
                            <div class="fin-btn-group">
                                <button type="button" class="fin-btn-period <?php echo ($period_type == 'today' || $period_type == 'daily') ? 'active' : ''; ?>" data-period="today">Today</button>
                                <button type="button" class="fin-btn-period <?php echo ($period_type == 'yesterday') ? 'active' : ''; ?>" data-period="yesterday">Yesterday</button>
                                <button type="button" class="fin-btn-period <?php echo ($period_type == 'this_week' || $period_type == 'weekly') ? 'active' : ''; ?>" data-period="this_week">This Week</button>
                                <button type="button" class="fin-btn-period <?php echo ($period_type == 'this_month' || $period_type == 'monthly') ? 'active' : ''; ?>" data-period="this_month">This Month</button>
                                <button type="button" class="fin-btn-period <?php echo ($period_type == 'last_month') ? 'active' : ''; ?>" data-period="last_month">Last Month</button>
                                <button type="button" class="fin-btn-period <?php echo ($period_type == 'this_year' || $period_type == 'yearly') ? 'active' : ''; ?>" data-period="this_year">This Year</button>
                            </div>

                            <!-- Date Picker -->
                            <div class="fin-date-input-wrap">
                                <i class="far fa-calendar-alt"></i>
                                <input type="text" name="date" id="date_input" class="fin-date-input" value="<?php echo html_escape($selected_date); ?>" placeholder="Select Date / Period">
                            </div>

                            <!-- Actions -->
                            <button type="submit" class="fin-btn-action fin-btn-primary">
                                <i class="fas fa-sync-alt"></i> Generate Report
                            </button>
                            <button type="button" id="btn_print_report" class="fin-btn-action">
                                <i class="fas fa-print"></i> Print
                            </button>
                            <button type="button" id="btn_pdf_report" class="fin-btn-action">
                                <i class="fas fa-file-pdf text-danger"></i> PDF
                            </button>
                            <button type="button" id="btn_excel_report" class="fin-btn-action">
                                <i class="fas fa-file-excel text-success"></i> Excel
                            </button>
                            <button type="button" id="btn_csv_report" class="fin-btn-action">
                                <i class="fas fa-file-csv text-info"></i> CSV
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Top 12 Summary Cards -->
            <div class="fin-cards-grid">
                <!-- 1. Total Invoices -->
                <div class="fin-stat-card fin-theme-blue">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-file-invoice"></i></div>
                        <div>
                            <?php $t = isset($trends['total_invoices']) ? $trends['total_invoices'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo $t['dir']; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Total Invoices</div>
                        <div class="fin-card-val"><?php echo number_format($summary['total_invoices']); ?></div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>

                <!-- 2. Gross Billing -->
                <div class="fin-stat-card fin-theme-orange">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-money-bill-wave"></i></div>
                        <div>
                            <?php $t = isset($trends['gross_billing']) ? $trends['gross_billing'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo $t['dir']; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Gross Billing</div>
                        <div class="fin-card-val"><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>

                <!-- 3. Total Discount -->
                <div class="fin-stat-card fin-theme-red">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-tags"></i></div>
                        <div>
                            <?php $t = isset($trends['total_discount']) ? $trends['total_discount'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo $t['dir']; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Total Discount</div>
                        <div class="fin-card-val"><?php echo $currency . ' ' . number_format($summary['total_discount'], 2); ?></div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>

                <!-- 4. Net Billing -->
                <div class="fin-stat-card fin-theme-green">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-chart-line"></i></div>
                        <div>
                            <?php $t = isset($trends['net_billing']) ? $trends['net_billing'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo $t['dir']; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Net Billing</div>
                        <div class="fin-card-val"><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>

                <!-- 5. Total Collection -->
                <div class="fin-stat-card fin-theme-green">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-wallet"></i></div>
                        <div>
                            <?php $t = isset($trends['total_collection']) ? $trends['total_collection'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo $t['dir']; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Total Collection</div>
                        <div class="fin-card-val"><?php echo $currency . ' ' . number_format($summary['total_collection'], 2); ?></div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>

                <!-- 6. Total Due -->
                <div class="fin-stat-card fin-theme-red">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-receipt"></i></div>
                        <div>
                            <?php $t = isset($trends['total_due']) ? $trends['total_due'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo ($t['dir'] == 'up') ? 'down' : 'up'; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Total Due</div>
                        <div class="fin-card-val"><?php echo $currency . ' ' . number_format($summary['total_due'], 2); ?></div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>

                <!-- 7. Doctor Commission -->
                <div class="fin-stat-card fin-theme-blue">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-user-md"></i></div>
                        <div>
                            <?php $t = isset($trends['doctor_commission']) ? $trends['doctor_commission'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo $t['dir']; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Doctor Commission</div>
                        <div class="fin-card-val"><?php echo $currency . ' ' . number_format($summary['doctor_commission'], 2); ?></div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>

                <!-- 8. Referral Commission -->
                <div class="fin-stat-card fin-theme-purple">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-users"></i></div>
                        <div>
                            <?php $t = isset($trends['referral_commission']) ? $trends['referral_commission'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo $t['dir']; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Referral Commission</div>
                        <div class="fin-card-val"><?php echo $currency . ' ' . number_format($summary['referral_commission'], 2); ?></div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>

                <!-- 9. Total Expenses -->
                <div class="fin-stat-card fin-theme-red">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-money-check-alt"></i></div>
                        <div>
                            <?php $t = isset($trends['total_expenses']) ? $trends['total_expenses'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo ($t['dir'] == 'up') ? 'down' : 'up'; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Total Expenses</div>
                        <div class="fin-card-val"><?php echo $currency . ' ' . number_format($summary['total_expenses'], 2); ?></div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>

                <!-- 10. Gross Profit -->
                <div class="fin-stat-card fin-theme-teal">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-chart-bar"></i></div>
                        <div>
                            <?php $t = isset($trends['gross_profit']) ? $trends['gross_profit'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo $t['dir']; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Gross Profit</div>
                        <div class="fin-card-val"><?php echo $currency . ' ' . number_format($summary['gross_profit'], 2); ?></div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>

                <!-- 11. Net Profit -->
                <div class="fin-stat-card fin-theme-green">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <?php $t = isset($trends['net_profit']) ? $trends['net_profit'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo $t['dir']; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Net Profit</div>
                        <div class="fin-card-val <?php echo ($summary['net_profit'] < 0) ? 'text-danger' : 'text-success'; ?>">
                            <?php echo $currency . ' ' . number_format($summary['net_profit'], 2); ?>
                        </div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>

                <!-- 12. Net Margin -->
                <div class="fin-stat-card fin-theme-orange">
                    <div class="fin-card-top">
                        <div class="fin-card-icon-wrap"><i class="fas fa-percentage"></i></div>
                        <div>
                            <?php $t = isset($trends['net_margin']) ? $trends['net_margin'] : ['dir' => 'neutral', 'text' => '0%']; ?>
                            <span class="fin-card-trend fin-trend-<?php echo $t['dir']; ?>">
                                <?php echo ($t['dir'] == 'up') ? '&uarr;' : (($t['dir'] == 'down') ? '&darr;' : '&bull;'); ?> <?php echo $t['text']; ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="fin-card-label">Net Margin</div>
                        <div class="fin-card-val"><?php echo number_format($summary['net_margin'], 2); ?>%</div>
                        <div class="fin-card-subtext"><?php echo html_escape($prev_label); ?></div>
                    </div>
                </div>
            </div>

            <!-- Charts Row: Trend Line & Payment Method Donut -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="fin-section-card">
                        <div class="fin-section-header">
                            <h3 class="fin-section-title">
                                <i class="fas fa-chart-area text-primary"></i> Billing, Collection &amp; Due Trend
                            </h3>
                            <span class="badge badge-light border text-muted"><?php echo html_escape($period_label); ?></span>
                        </div>
                        <div class="fin-section-body">
                            <div class="fin-chart-wrap" style="height: 300px;">
                                <canvas id="financialTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="fin-section-card">
                        <div class="fin-section-header">
                            <h3 class="fin-section-title">
                                <i class="fas fa-chart-pie text-info"></i> Payment Method Wise Collection
                            </h3>
                            <span class="badge badge-light border text-muted"><?php echo count($methods); ?> Methods</span>
                        </div>
                        <div class="fin-section-body">
                            <div class="fin-donut-wrap" style="height: 200px;">
                                <canvas id="paymentMethodChart"></canvas>
                                <div class="fin-donut-center-text">
                                    <span class="fin-donut-center-val"><?php echo $currency . ' ' . number_format($summary['total_collection']); ?></span>
                                    <span class="fin-donut-center-lbl">Total Collection</span>
                                </div>
                            </div>

                            <div class="mt-3">
                                <table class="fin-table table-sm">
                                    <tbody>
                                        <?php foreach ($methods as $m_name => $m_data): ?>
                                            <tr>
                                                <td class="font-weight-600">
                                                    <span class="badge badge-dot mr-1" style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#2563eb;"></span>
                                                    <?php echo html_escape($m_data['name']); ?>
                                                </td>
                                                <td class="text-right text-muted"><?php echo number_format($m_data['percentage'], 1); ?>%</td>
                                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($m_data['amount'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sections Row: 1. Invoice & Billing Summary & 2. Collection & Due Summary -->
            <div class="row">
                <!-- 1. INVOICE & BILLING SUMMARY -->
                <div class="col-lg-6">
                    <div class="fin-section-card">
                        <div class="fin-section-header">
                            <h3 class="fin-section-title">
                                <i class="fas fa-file-invoice-dollar text-primary"></i> 1. Invoice &amp; Billing Summary
                            </h3>
                        </div>
                        <div class="fin-section-body">
                            <ul class="fin-kv-list">
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label">Total Invoices</span>
                                    <span class="fin-kv-value"><?php echo number_format($summary['total_invoices']); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label">Gross Billing</span>
                                    <span class="fin-kv-value"><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label">Total Discount (-)</span>
                                    <span class="fin-kv-value text-danger">- <?php echo $currency . ' ' . number_format($summary['total_discount'], 2); ?></span>
                                </li>
                                <li class="fin-kv-item fin-kv-highlight">
                                    <span class="fin-kv-label font-weight-bold text-dark">Net Billing</span>
                                    <span class="fin-kv-value text-primary"><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label">Cancelled Invoices</span>
                                    <span class="fin-kv-value text-muted"><?php echo number_format($summary['cancelled_invoices']); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label">Refunded Amount</span>
                                    <span class="fin-kv-value text-danger"><?php echo $currency . ' ' . number_format($summary['refunded_amount'], 2); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 2. COLLECTION & DUE SUMMARY -->
                <div class="col-lg-6">
                    <div class="fin-section-card">
                        <div class="fin-section-header">
                            <h3 class="fin-section-title">
                                <i class="fas fa-hand-holding-usd text-success"></i> 2. Collection &amp; Due Summary
                            </h3>
                        </div>
                        <div class="fin-section-body">
                            <ul class="fin-kv-list">
                                <li class="fin-kv-item fin-kv-highlight">
                                    <span class="fin-kv-label font-weight-bold text-dark">Total Collection</span>
                                    <span class="fin-kv-value text-success"><?php echo $currency . ' ' . number_format($summary['total_collection'], 2); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label">Cash Collection</span>
                                    <span class="fin-kv-value"><?php echo $currency . ' ' . number_format($summary['cash_collection'], 2); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label">bKash Collection</span>
                                    <span class="fin-kv-value"><?php echo $currency . ' ' . number_format($summary['bkash_collection'], 2); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label">Nagad Collection</span>
                                    <span class="fin-kv-value"><?php echo $currency . ' ' . number_format($summary['nagad_collection'], 2); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label">Bank Collection</span>
                                    <span class="fin-kv-value"><?php echo $currency . ' ' . number_format($summary['bank_collection'], 2); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label">Card Collection</span>
                                    <span class="fin-kv-value"><?php echo $currency . ' ' . number_format($summary['card_collection'], 2); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label">Other Collection</span>
                                    <span class="fin-kv-value"><?php echo $currency . ' ' . number_format($summary['other_collection'], 2); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label font-weight-bold text-danger">Total Due</span>
                                    <span class="fin-kv-value text-danger font-weight-bold"><?php echo $currency . ' ' . number_format($summary['total_due'], 2); ?></span>
                                </li>
                                <li class="fin-kv-item">
                                    <span class="fin-kv-label text-muted">Previous Due Collected</span>
                                    <span class="fin-kv-value text-muted"><?php echo $currency . ' ' . number_format($summary['prev_due_collected'], 2); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sections Row: 3. Doctor Commission & 4. Referral Commission -->
            <div class="row">
                <!-- 3. DOCTOR COMMISSION -->
                <div class="col-lg-6">
                    <div class="fin-section-card">
                        <div class="fin-section-header">
                            <h3 class="fin-section-title">
                                <i class="fas fa-user-md text-primary"></i> 3. Doctor Commission
                            </h3>
                            <span class="badge badge-primary px-3 py-1 font-weight-bold" style="font-size: 13px;">
                                Total: <?php echo $currency . ' ' . number_format($summary['doctor_commission'], 2); ?>
                            </span>
                        </div>
                        <div class="fin-section-body">
                            <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded" style="font-size: 13px;">
                                <div><span class="text-muted">Paid Commission:</span> <strong><?php echo $currency . ' ' . number_format($summary['paid_doctor_comm'], 2); ?></strong></div>
                                <div><span class="text-muted">Pending Commission:</span> <strong class="text-warning"><?php echo $currency . ' ' . number_format($summary['pending_doctor_comm'], 2); ?></strong></div>
                            </div>

                            <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                                <table class="fin-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Doctor</th>
                                            <th class="text-center">Invoices</th>
                                            <th class="text-right">Billing</th>
                                            <th class="text-right">Commission</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($doctor_breakdown)): ?>
                                            <?php $i = 1; foreach ($doctor_breakdown as $doc): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td class="font-weight-600"><?php echo html_escape($doc['name']); ?></td>
                                                    <td class="text-center"><?php echo is_array($doc['invoices']) ? count($doc['invoices']) : $doc['invoices']; ?></td>
                                                    <td class="text-right"><?php echo $currency . ' ' . number_format($doc['billing'], 2); ?></td>
                                                    <td class="text-right font-weight-bold text-primary"><?php echo $currency . ' ' . number_format($doc['commission'], 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No doctor commission records for this period.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. REFERRAL COMMISSION -->
                <div class="col-lg-6">
                    <div class="fin-section-card">
                        <div class="fin-section-header">
                            <h3 class="fin-section-title">
                                <i class="fas fa-users-cog text-purple"></i> 4. Referral Commission
                            </h3>
                            <span class="badge badge-purple px-3 py-1 font-weight-bold" style="background:#9333ea; color:#fff; font-size: 13px;">
                                Total: <?php echo $currency . ' ' . number_format($summary['referral_commission'], 2); ?>
                            </span>
                        </div>
                        <div class="fin-section-body">
                            <div class="row mb-3 p-2 bg-light rounded text-center" style="font-size: 12.5px;">
                                <div class="col-3 p-1"><span class="text-muted d-block">Earned</span><strong><?php echo $currency . ' ' . number_format($summary['earned_ref_comm'], 2); ?></strong></div>
                                <div class="col-3 p-1"><span class="text-muted d-block">Pending</span><strong class="text-warning"><?php echo $currency . ' ' . number_format($summary['pending_ref_comm'], 2); ?></strong></div>
                                <div class="col-3 p-1"><span class="text-muted d-block">Withdrawn</span><strong><?php echo $currency . ' ' . number_format($summary['withdrawn_ref_comm'], 2); ?></strong></div>
                                <div class="col-3 p-1"><span class="text-muted d-block">Available Wallet</span><strong class="text-success"><?php echo $currency . ' ' . number_format($summary['available_wallet_balance'], 2); ?></strong></div>
                            </div>

                            <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                                <table class="fin-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Referrer</th>
                                            <th class="text-center">Patients</th>
                                            <th class="text-center">Invoices</th>
                                            <th class="text-right">Commission</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($referrer_breakdown)): ?>
                                            <?php $i = 1; foreach ($referrer_breakdown as $ref): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td class="font-weight-600"><?php echo html_escape($ref['name']); ?></td>
                                                    <td class="text-center"><?php echo is_array($ref['patients']) ? count($ref['patients']) : $ref['patients']; ?></td>
                                                    <td class="text-center"><?php echo is_array($ref['invoices']) ? count($ref['invoices']) : $ref['invoices']; ?></td>
                                                    <td class="text-right font-weight-bold text-purple" style="color: #9333ea;"><?php echo $currency . ' ' . number_format($ref['commission'], 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No referral commission records for this period.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sections Row: 5. Service Performance & 6. Hospital Expenses -->
            <div class="row">
                <!-- 5. SERVICE / TEST PERFORMANCE -->
                <div class="col-lg-6">
                    <div class="fin-section-card">
                        <div class="fin-section-header">
                            <h3 class="fin-section-title">
                                <i class="fas fa-vials text-success"></i> 5. Service / Test Performance
                            </h3>
                            <div>
                                <?php if (count($service_perf) > 5): ?>
                                    <button type="button" id="toggle_services_btn" class="btn btn-xs btn-outline-primary">Show All (<?php echo count($service_perf); ?>)</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="fin-section-body">
                            <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                                <table class="fin-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Service / Test</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-right">Gross Amount</th>
                                            <th class="text-right">Discount</th>
                                            <th class="text-right">Net Amount</th>
                                            <th class="text-right" style="color: #9333ea;">Ref Comm</th>
                                            <th class="text-right" style="color: #16a34a;">Hospital Net</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($service_perf)): ?>
                                            <?php $i = 1; foreach ($service_perf as $idx => $s): ?>
                                                <tr class="<?php echo ($idx >= 5) ? 'fin-service-extra-row' : ''; ?>" style="<?php echo ($idx >= 5) ? 'display:none;' : ''; ?>">
                                                    <td><?php echo $i++; ?></td>
                                                    <td class="font-weight-600"><?php echo html_escape($s['name']); ?></td>
                                                    <td class="text-center font-weight-bold"><?php echo $s['qty']; ?></td>
                                                    <td class="text-right"><?php echo $currency . ' ' . number_format($s['gross'], 2); ?></td>
                                                    <td class="text-right text-danger"><?php echo ($s['discount'] > 0) ? '- ' . $currency . ' ' . number_format($s['discount'], 2) : '-'; ?></td>
                                                    <td class="text-right font-weight-bold text-primary"><?php echo $currency . ' ' . number_format($s['net'], 2); ?></td>
                                                    <td class="text-right font-weight-600" style="color: #9333ea;"><?php echo $currency . ' ' . number_format($s['ref_comm'], 2); ?></td>
                                                    <td class="text-right font-weight-bold text-success"><?php echo $currency . ' ' . number_format($s['hospital_net'], 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-3">No service / test records for this period.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. HOSPITAL EXPENSES -->
                <div class="col-lg-6">
                    <div class="fin-section-card">
                        <div class="fin-section-header">
                            <h3 class="fin-section-title">
                                <i class="fas fa-file-invoice text-danger"></i> 6. Hospital Expenses
                            </h3>
                            <span class="badge badge-danger px-3 py-1 font-weight-bold" style="font-size: 13px;">
                                Total: <?php echo $currency . ' ' . number_format($summary['total_expenses'], 2); ?>
                            </span>
                        </div>
                        <div class="fin-section-body">
                            <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                                <table class="fin-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Expense Category</th>
                                            <th class="text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($expense_breakdown)): ?>
                                            <?php $i = 1; foreach ($expense_breakdown as $cat_name => $cat_amt): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td class="font-weight-600"><?php echo html_escape($cat_name); ?></td>
                                                    <td class="text-right font-weight-bold text-danger"><?php echo $currency . ' ' . number_format($cat_amt, 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">No hospital expenses recorded for this period.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="font-weight-bold">TOTAL EXPENSES</td>
                                            <td class="text-right font-weight-bold text-danger"><?php echo $currency . ' ' . number_format($summary['total_expenses'], 2); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 7: Profit Calculation -->
            <div class="fin-section-card">
                <div class="fin-section-header">
                    <h3 class="fin-section-title">
                        <i class="fas fa-calculator text-success"></i> 7. Profit Calculation
                    </h3>
                    <span class="badge badge-pill badge-light border text-muted">Net Margin: <?php echo number_format($summary['net_margin'], 2); ?>%</span>
                </div>
                <div class="fin-section-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                            <div class="fin-profit-step plus">
                                <span><strong>Gross Billing:</strong> Total valid invoices</span>
                                <strong><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></strong>
                            </div>
                            <div class="fin-profit-step minus">
                                <span><strong>(-) Total Discount:</strong> Invoice discounts granted</span>
                                <strong>- <?php echo $currency . ' ' . number_format($summary['total_discount'], 2); ?></strong>
                            </div>
                            <div class="fin-profit-step" style="background:#e0f2fe; color:#0369a1;">
                                <span><strong>(=) Net Billing:</strong> Hospital receivable revenue</span>
                                <strong><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></strong>
                            </div>
                            <div class="fin-profit-step minus">
                                <span><strong>(-) Doctor Commission:</strong> Internal hospital cost</span>
                                <strong>- <?php echo $currency . ' ' . number_format($summary['doctor_commission'], 2); ?></strong>
                            </div>
                            <div class="fin-profit-step minus">
                                <span><strong>(-) Referral Commission:</strong> Internal hospital cost</span>
                                <strong>- <?php echo $currency . ' ' . number_format($summary['referral_commission'], 2); ?></strong>
                            </div>
                            <div class="fin-profit-step minus">
                                <span><strong>(-) Hospital Expenses:</strong> Operational &amp; overhead expenses</span>
                                <strong>- <?php echo $currency . ' ' . number_format($summary['total_expenses'], 2); ?></strong>
                            </div>
                            <div class="fin-profit-step total">
                                <span><i class="fas fa-coins mr-1"></i> (=) NET PROFIT</span>
                                <span><?php echo $currency . ' ' . number_format($summary['net_profit'], 2); ?></span>
                            </div>
                        </div>
                        <div class="col-lg-5 text-center mt-3 mt-lg-0">
                            <div class="p-4 bg-light rounded-lg border">
                                <div class="text-muted text-uppercase font-weight-bold" style="font-size: 12px; letter-spacing: 1px;">Net Margin</div>
                                <div class="display-4 font-weight-bold text-success my-2" style="font-size: 42px;">
                                    <?php echo number_format($summary['net_margin'], 2); ?>%
                                </div>
                                <p class="text-muted mb-0" style="font-size: 13px;">
                                    (Net Profit / Net Billing) &times; 100<br>
                                    <strong><?php echo $currency . ' ' . number_format($summary['net_profit'], 2); ?></strong> / <strong><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 8: Period Breakdown Table -->
            <div class="fin-section-card">
                <div class="fin-section-header">
                    <h3 class="fin-section-title">
                        <i class="fas fa-table text-dark"></i> 8. Period Breakdown Table (<?php echo ucfirst($period_type); ?> Breakdown)
                    </h3>
                    <span class="badge badge-light border text-muted">Summary &bull; Reconciled 100% with Top Cards</span>
                </div>
                <div class="fin-section-body p-0">
                    <div class="table-responsive">
                        <table class="fin-table table-bordered table-hover m-0">
                            <thead>
                                <tr>
                                    <th><?php echo ($period_type == 'daily') ? 'Hour' : (($period_type == 'yearly') ? 'Month' : 'Date'); ?></th>
                                    <th class="text-center">Invoices</th>
                                    <th class="text-right">Gross Billing</th>
                                    <th class="text-right">Discount</th>
                                    <th class="text-right">Net Billing</th>
                                    <th class="text-right">Collection</th>
                                    <th class="text-right">Due</th>
                                    <th class="text-right">Doctor Comm</th>
                                    <th class="text-right">Referral Comm</th>
                                    <th class="text-right">Expense</th>
                                    <th class="text-right">Net Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($breakdown)): ?>
                                    <?php foreach ($breakdown as $row): ?>
                                        <tr>
                                            <td class="font-weight-600"><?php echo html_escape($row['label']); ?></td>
                                            <td class="text-center"><?php echo $row['invoices']; ?></td>
                                            <td class="text-right"><?php echo $currency . ' ' . number_format($row['gross_billing'], 2); ?></td>
                                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($row['discount'], 2); ?></td>
                                            <td class="text-right font-weight-600 text-primary"><?php echo $currency . ' ' . number_format($row['net_billing'], 2); ?></td>
                                            <td class="text-right font-weight-600 text-success"><?php echo $currency . ' ' . number_format($row['collection'], 2); ?></td>
                                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($row['due'], 2); ?></td>
                                            <td class="text-right"><?php echo $currency . ' ' . number_format($row['doctor_comm'], 2); ?></td>
                                            <td class="text-right"><?php echo $currency . ' ' . number_format($row['referral_comm'], 2); ?></td>
                                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($row['expense'], 2); ?></td>
                                            <td class="text-right font-weight-bold <?php echo ($row['net_profit'] < 0) ? 'text-danger' : 'text-success'; ?>">
                                                <?php echo $currency . ' ' . number_format($row['net_profit'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-3">No breakdown data available for this period.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr style="background: #f1f5f9; font-weight: bold; border-top: 2px solid #cbd5e1;">
                                    <td>TOTAL</td>
                                    <td class="text-center"><?php echo number_format($summary['total_invoices']); ?></td>
                                    <td class="text-right"><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></td>
                                    <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['total_discount'], 2); ?></td>
                                    <td class="text-right text-primary"><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></td>
                                    <td class="text-right text-success"><?php echo $currency . ' ' . number_format($summary['total_collection'], 2); ?></td>
                                    <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['total_due'], 2); ?></td>
                                    <td class="text-right"><?php echo $currency . ' ' . number_format($summary['doctor_commission'], 2); ?></td>
                                    <td class="text-right"><?php echo $currency . ' ' . number_format($summary['referral_commission'], 2); ?></td>
                                    <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['total_expenses'], 2); ?></td>
                                    <td class="text-right <?php echo ($summary['net_profit'] < 0) ? 'text-danger' : 'text-success'; ?>">
                                        <?php echo $currency . ' ' . number_format($summary['net_profit'], 2); ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Chart data payload -->
<script type="text/javascript">
    window.finChartData = <?php echo json_encode($chart_data); ?>;
    window.finMethodData = <?php echo json_encode($methods); ?>;
</script>

<script src="common/extranal/js/finance/financial_performance_report.js"></script>
<!-- Main content end -->
