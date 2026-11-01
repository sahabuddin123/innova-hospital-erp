<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';

$collection_pct = ($summary['net_payable'] > 0) ? round(($summary['current_invoice_collection'] / $summary['net_payable']) * 100, 1) : 0.0;
$due_pct = ($summary['net_payable'] > 0) ? round(($summary['outstanding_due'] / $summary['net_payable']) * 100, 1) : 0.0;
?>
<link href="common/extranal/css/finance/collection_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Collection Summary & Financial Reconciliation Statement &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-balance-scale text-primary"></i> Collection Summary & Reconciliation Report</h2>
                    <p>Executive reconciliation of billing, actual realized collections, dues, advance deposits, and refunds &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print Statement</button>
                </div>
            </div>

            <form method="GET" action="finance/collectionSummaryReport" class="m-0">
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

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Generate</button>
                        <a href="finance/collectionSummaryReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card success">
                <span class="rep-kpi-label"><i class="fas fa-check-circle text-success"></i> Net Realized Cash Inflow</span>
                <span class="rep-kpi-val text-success"><?php echo $currency . number_format($summary['net_cash_received'], 2); ?></span>
                <span class="rep-kpi-sub">Total Collections minus Patient Refunds</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-file-invoice-dollar text-primary"></i> Net Invoiced Payable</span>
                <span class="rep-kpi-val"><?php echo $currency . number_format($summary['net_payable'], 2); ?></span>
                <span class="rep-kpi-sub">Gross Billed minus Total Discounts</span>
            </div>
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-exclamation-circle text-danger"></i> Period Outstanding Due</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['outstanding_due'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo $due_pct; ?>% uncollected from current billing</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-hand-holding-usd text-info"></i> Prior Due Collections</span>
                <span class="rep-kpi-val text-info"><?php echo $currency . number_format($summary['prev_due_collection'], 2); ?></span>
                <span class="rep-kpi-sub">Recovery of historical receivables</span>
            </div>
        </div>

        <!-- Master Reconciliation Statements -->
        <div class="row">
            <!-- Left Column: Financial Position Statement -->
            <div class="col-lg-7 mb-4">
                <div class="rep-table-card h-100">
                    <h5 class="font-weight-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="fas fa-list-alt text-primary mr-2"></i> Executive Billing & Collection Statement
                    </h5>

                    <table class="table table-bordered mb-0" style="font-size: 13.5px;">
                        <tbody>
                            <!-- Section 1: Invoicing -->
                            <tr class="table-light">
                                <th colspan="2" class="text-uppercase font-weight-bold text-primary">
                                    1. Diagnostic Billing & Invoicing Activity
                                </th>
                            </tr>
                            <tr>
                                <td>Total Invoices Generated</td>
                                <td class="text-right font-weight-bold"><?php echo number_format($summary['total_invoices']); ?> Invoices</td>
                            </tr>
                            <tr>
                                <td>Gross Test Catalog Billing</td>
                                <td class="text-right font-weight-bold"><?php echo $currency . number_format($summary['gross_billing'], 2); ?></td>
                            </tr>
                            <tr>
                                <td class="text-danger">(-) Total Discounts & Concessions Granted</td>
                                <td class="text-right text-danger font-weight-bold">- <?php echo $currency . number_format($summary['total_discount'], 2); ?></td>
                            </tr>
                            <tr style="background:#f8fafc;">
                                <th class="font-weight-bold text-dark">(=) Net Invoiced Revenue</th>
                                <th class="text-right font-weight-bold text-primary" style="font-size: 15px;">
                                    <?php echo $currency . number_format($summary['net_payable'], 2); ?>
                                </th>
                            </tr>

                            <!-- Section 2: Realized Cash Flow -->
                            <tr class="table-light">
                                <th colspan="2" class="text-uppercase font-weight-bold text-success pt-3">
                                    2. Realized Cash & Digital Inflow (Receipts)
                                </th>
                            </tr>
                            <tr>
                                <td>Collections on Current Period Invoices</td>
                                <td class="text-right font-weight-bold text-dark"><?php echo $currency . number_format($summary['current_invoice_collection'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Collections on Prior Period Dues (Arrears Recovery)</td>
                                <td class="text-right font-weight-bold text-info">+ <?php echo $currency . number_format($summary['prev_due_collection'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Advance Deposits Received (Unallocated)</td>
                                <td class="text-right font-weight-bold text-muted">+ <?php echo $currency . number_format($summary['advance_collection'], 2); ?></td>
                            </tr>
                            <tr style="background:#f0fdf4;">
                                <th class="font-weight-bold text-dark">Gross Total Inflow Collected</th>
                                <th class="text-right font-weight-bold text-success">
                                    <?php echo $currency . number_format($summary['total_collection'], 2); ?>
                                </th>
                            </tr>
                            <tr>
                                <td class="text-danger">(-) Patient Refunds & Cancelled Invoices Paid Out</td>
                                <td class="text-right text-danger font-weight-bold">- <?php echo $currency . number_format($summary['total_refunds'], 2); ?></td>
                            </tr>
                            <tr style="background:#ecfdf5; border-top: 2px solid #10b981;">
                                <th class="font-weight-bold text-success" style="font-size: 15px;">
                                    (=) Net Realized Cash Received (Net Inflow)
                                </th>
                                <th class="text-right font-weight-bold text-success" style="font-size: 16px;">
                                    <?php echo $currency . number_format($summary['net_cash_received'], 2); ?>
                                </th>
                            </tr>

                            <!-- Section 3: Receivables Status -->
                            <tr class="table-light">
                                <th colspan="2" class="text-uppercase font-weight-bold text-danger pt-3">
                                    3. Receivables & Collection Efficiency
                                </th>
                            </tr>
                            <tr>
                                <td>New Outstanding Due Generated (Current Billing)</td>
                                <td class="text-right font-weight-bold text-danger"><?php echo $currency . number_format($summary['outstanding_due'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Collection Efficiency (Current Period Receipts / Net Billed)</td>
                                <td class="text-right font-weight-bold text-dark"><?php echo $collection_pct; ?>%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Payment Channels Breakdown -->
            <div class="col-lg-5 mb-4">
                <div class="rep-table-card h-100">
                    <h5 class="font-weight-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="fas fa-wallet text-success mr-2"></i> Payment Channel Distribution
                    </h5>

                    <p class="text-muted small">Actual collections distributed across physical cash and digital payment gateways during <?php echo html_escape($period_label); ?>.</p>

                    <table class="table table-hover mb-4">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th>Channel</th>
                                <th class="text-right">Amount (<?php echo trim($currency); ?>)</th>
                                <th class="text-center">Share %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $tot = $summary['total_collection'];
                            if (!empty($summary['methods'])): 
                                foreach ($summary['methods'] as $m_name => $m_amt): 
                                    $pct = ($tot > 0) ? round(($m_amt / $tot) * 100, 1) : 0.0;
                                    $badge = 'badge-secondary';
                                    if (strcasecmp($m_name, 'Cash') === 0) $badge = 'badge-success';
                                    elseif (strcasecmp($m_name, 'bKash') === 0) $badge = 'badge-danger';
                                    elseif (strcasecmp($m_name, 'Nagad') === 0) $badge = 'badge-warning text-dark';
                                    elseif (strcasecmp($m_name, 'Card') === 0) $badge = 'badge-info';
                                    elseif (strcasecmp($m_name, 'Bank') === 0) $badge = 'badge-primary';
                            ?>
                                <tr>
                                    <td>
                                        <span class="badge <?php echo $badge; ?> px-2 py-1 mr-1"><?php echo html_escape($m_name); ?></span>
                                    </td>
                                    <td class="text-right font-weight-bold"><?php echo number_format($m_amt, 2); ?></td>
                                    <td class="text-center">
                                        <span class="font-weight-bold"><?php echo $pct; ?>%</span>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No collections recorded in this period.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="rep-total-row">
                                <td class="font-weight-bold">Total Inflow:</td>
                                <td class="text-right font-weight-bold text-success"><?php echo number_format($tot, 2); ?></td>
                                <td class="text-center font-weight-bold">100%</td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Collection Efficiency Progress Bar -->
                    <div class="p-3 bg-light rounded border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold text-dark" style="font-size: 12.5px;">Current Billing Collection Rate:</span>
                            <span class="badge badge-success font-weight-bold"><?php echo $collection_pct; ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo min(100, $collection_pct); ?>%;" aria-valuenow="<?php echo $collection_pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted mt-2" style="font-size: 11px;">
                            <span>Collected: <?php echo $currency . number_format($summary['current_invoice_collection'], 2); ?></span>
                            <span>Due: <?php echo $currency . number_format($summary['outstanding_due'], 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Signatures -->
        <div class="d-none d-print-block mt-5 pt-4">
            <div class="row text-center">
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Prepared By (Accounts)</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Audited By</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Managing Director / CEO</strong>
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
});
</script>
