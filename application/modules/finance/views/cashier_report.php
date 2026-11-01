<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
$selected_user = !empty($user_id) ? $user_id : '';
?>
<link href="common/extranal/css/finance/collection_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Cashier-Wise Collection & Shift Settlement Report &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-user-check text-success"></i> Cashier-Wise Collection Report</h2>
                    <p>Collection breakdown by operator/cashier across payment channels for shift handover &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/cashierReport" class="m-0">
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

                    <!-- Date & Cashier Filter Group -->
                    <div class="rep-filter-group">
                        <div class="rep-input-wrap">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">FROM:</span>
                            <input type="date" name="date_from" id="date_from" class="rep-date-input" value="<?php echo html_escape($period['date_from']); ?>">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">TO:</span>
                            <input type="date" name="date_to" id="date_to" class="rep-date-input" value="<?php echo html_escape($period['date_to']); ?>">
                        </div>

                        <!-- Specific Cashier Filter -->
                        <select name="user_id" class="rep-select">
                            <option value="">-- All Cashiers --</option>
                            <?php if (!empty($cashiers)): foreach ($cashiers as $c): ?>
                                <option value="<?php echo $c->id; ?>" <?php echo ($selected_user == $c->id) ? 'selected' : ''; ?>>
                                    <?php echo html_escape($c->username); ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Filter</button>
                        <a href="finance/cashierReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card success">
                <span class="rep-kpi-label"><i class="fas fa-wallet text-success"></i> Total Handled Collection</span>
                <span class="rep-kpi-val text-success"><?php echo $currency . number_format($summary['total_collection'], 2); ?></span>
                <span class="rep-kpi-sub">Across <?php echo number_format($summary['total_transactions']); ?> total receipts</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-money-bill-wave text-warning"></i> Cash in Hand</span>
                <span class="rep-kpi-val text-warning"><?php echo $currency . number_format($summary['cash_collection'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo ($summary['total_collection'] > 0) ? round(($summary['cash_collection'] / $summary['total_collection']) * 100, 1) : 0; ?>% physical currency</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-mobile-alt text-info"></i> Digital / Mobile Collection</span>
                <span class="rep-kpi-val"><?php echo $currency . number_format($summary['online_collection'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo ($summary['total_collection'] > 0) ? round(($summary['online_collection'] / $summary['total_collection']) * 100, 1) : 0; ?>% bKash, Nagad, Card</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-users text-primary"></i> Active Cashiers</span>
                <span class="rep-kpi-val"><?php echo number_format($summary['active_cashiers']); ?></span>
                <span class="rep-kpi-sub">Staff logged collection</span>
            </div>
        </div>

        <!-- Table Card -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="cashierReportTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cashier Name</th>
                            <th>User ID / Account</th>
                            <th class="text-center">Receipts / Tx Count</th>
                            <th class="text-right">Cash (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Digital / Online (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Bank (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Others (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Total Collection (<?php echo trim($currency); ?>)</th>
                            <th class="text-center">Share %</th>
                            <th class="text-center d-print-none">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            $grand = $summary['total_collection'];
                            foreach ($records as $r): 
                                $c_name = !empty($r->first_name) ? trim($r->first_name . ' ' . $r->last_name) : $r->cashier_name;
                                $share = ($grand > 0) ? round(($r->total_coll / $grand) * 100, 1) : 0.0;
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td class="font-weight-bold text-dark">
                                    <i class="fas fa-user-circle text-primary mr-1"></i> <?php echo html_escape($c_name); ?>
                                </td>
                                <td><span class="badge badge-light border">@<?php echo html_escape($r->cashier_name); ?></span></td>
                                <td class="text-center font-weight-bold"><?php echo number_format($r->tx_count); ?></td>
                                <td class="text-right font-weight-bold text-dark"><?php echo number_format($r->cash_coll, 2); ?></td>
                                <td class="text-right text-info font-weight-bold"><?php echo number_format($r->online_coll, 2); ?></td>
                                <td class="text-right"><?php echo number_format($r->bank_coll, 2); ?></td>
                                <td class="text-right text-muted"><?php echo number_format($r->other_coll, 2); ?></td>
                                <td class="text-right text-success font-weight-bold" style="font-size: 15px;">
                                    <?php echo number_format($r->total_coll, 2); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary px-2 py-1"><?php echo $share; ?>%</span>
                                </td>
                                <td class="text-center d-print-none">
                                    <a href="finance/dailyCollectionReport?cashier_id=<?php echo $r->cashier_id; ?>&period_type=<?php echo $period['period_type']; ?>" class="btn btn-xs btn-outline-primary" title="View Detailed Receipts">
                                        <i class="fas fa-list"></i> Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-5">
                                    <i class="fas fa-users text-muted fa-3x mb-2 d-block"></i>
                                    <strong>No cashier collection records found for the selected period.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="3" class="text-right font-weight-bold">Grand Total (<?php echo count($records); ?> cashiers):</td>
                            <td class="text-center font-weight-bold"><?php echo number_format($summary['total_transactions']); ?></td>
                            <td class="text-right font-weight-bold"><?php echo number_format($summary['cash_collection'], 2); ?></td>
                            <td class="text-right font-weight-bold text-info"><?php echo number_format($summary['online_collection'], 2); ?></td>
                            <td class="text-right font-weight-bold"><?php echo number_format($summary['bank_collection'], 2); ?></td>
                            <td class="text-right font-weight-bold text-muted">-</td>
                            <td class="text-right font-weight-bold text-success"><?php echo number_format($summary['total_collection'], 2); ?></td>
                            <td class="text-center font-weight-bold">100%</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Print Signatures -->
        <div class="d-none d-print-block mt-5 pt-4">
            <div class="row text-center">
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Cashier Handover Signature</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Head of Accounts</strong>
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

    if ($.fn.DataTable && $('#cashierReportTable tbody tr td').length > 1) {
        $('#cashierReportTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[8, 'desc']], // Highest total collection first
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search cashier name, username..."
            }
        });
    }
});
</script>
