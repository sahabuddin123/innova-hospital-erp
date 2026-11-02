<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
$selected_utility_cat = !empty($selected_utility_cat) ? $selected_utility_cat : 'all';
?>
<link href="common/extranal/css/finance/expense_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_expense_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Utility Expense & Bills Report &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-bolt text-warning"></i> Utility Expense & Bills Report</h2>
                    <p>Electricity (DESCO/DPDC), Water (WASA), Gas, Internet, and Telephone utility bills &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'utility_bill']); ?>
                </div>
            </div>

            <form method="GET" action="finance/utilityBillReport" class="m-0">
                <input type="hidden" name="period_type" id="period_type_input" value="<?php echo html_escape($period['period_type']); ?>">
                <div class="rep-controls-row">
                    <!-- Period Pills -->
                    <div class="rep-period-pills">
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'today') ? 'active' : ''; ?>" data-period="today">Today</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_month') ? 'active' : ''; ?>" data-period="this_month">This Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'last_month') ? 'active' : ''; ?>" data-period="last_month">Last Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_year') ? 'active' : ''; ?>" data-period="this_year">This Year</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'custom') ? 'active' : ''; ?>" data-period="custom">Custom</button>
                    </div>

                    <!-- Filter Inputs -->
                    <div class="rep-filter-group">
                        <div class="rep-input-wrap">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">FROM:</span>
                            <input type="date" name="date_from" id="date_from" class="rep-date-input" value="<?php echo html_escape($period['date_from']); ?>">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">TO:</span>
                            <input type="date" name="date_to" id="date_to" class="rep-date-input" value="<?php echo html_escape($period['date_to']); ?>">
                        </div>

                        <!-- Utility Type Filter -->
                        <select name="utility_cat" class="rep-select">
                            <option value="all">-- All Utilities --</option>
                            <option value="Electricity" <?php echo ($selected_utility_cat === 'Electricity') ? 'selected' : ''; ?>>Electricity (DESCO/DPDC)</option>
                            <option value="Internet" <?php echo ($selected_utility_cat === 'Internet') ? 'selected' : ''; ?>>Internet & WiFi</option>
                            <option value="Water & Gas" <?php echo ($selected_utility_cat === 'Water & Gas') ? 'selected' : ''; ?>>Water & Gas</option>
                            <option value="Other Utilities" <?php echo ($selected_utility_cat === 'Other Utilities') ? 'selected' : ''; ?>>Other Utilities</option>
                        </select>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Apply</button>
                        <a href="finance/utilityBillReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-money-bill-wave text-danger"></i> Total Utility Spend</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['total_utility'], 2); ?></span>
                <span class="rep-kpi-sub">Across <?php echo number_format($summary['total_bills']); ?> utility bills</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-bolt text-warning"></i> Electricity (Power)</span>
                <span class="rep-kpi-val text-warning"><?php echo $currency . number_format($summary['electricity'], 2); ?></span>
                <span class="rep-kpi-sub">DESCO / DPDC / Grid Power</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-wifi text-primary"></i> Internet & Network</span>
                <span class="rep-kpi-val text-primary"><?php echo $currency . number_format($summary['internet'], 2); ?></span>
                <span class="rep-kpi-sub">Broadband ISP & IT utilities</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-tint text-info"></i> Water & Gas</span>
                <span class="rep-kpi-val text-info"><?php echo $currency . number_format($summary['water_gas'], 2); ?></span>
                <span class="rep-kpi-sub">WASA & Gas line bills</span>
            </div>
        </div>

        <!-- Utility Bills Table Card -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="utilityBillsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bill / Voucher Date</th>
                            <th>Voucher #</th>
                            <th>Utility Category</th>
                            <th>Provider / Payee</th>
                            <th>Description / Bill Details</th>
                            <th class="text-center">Method</th>
                            <th class="text-right">Amount (<?php echo trim($currency); ?>)</th>
                            <th class="text-center">Status</th>
                            <th>Paid By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            foreach ($records as $r): 
                                $badge_class = 'badge-secondary';
                                if ($r->utility_type === 'Electricity') $badge_class = 'badge-warning text-dark';
                                elseif ($r->utility_type === 'Internet') $badge_class = 'badge-primary';
                                elseif ($r->utility_type === 'Water & Gas') $badge_class = 'badge-info';
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><?php echo date('d/m/Y', $r->date); ?></td>
                                <td class="font-weight-bold">
                                    <a href="finance/expenseInvoice?id=<?php echo $r->id; ?>" target="_blank" class="text-primary">
                                        EXP-<?php echo $r->id; ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge <?php echo $badge_class; ?> px-2 py-1 font-weight-bold">
                                        <?php echo html_escape($r->utility_type); ?>
                                    </span>
                                </td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($r->pay_to ?: 'Utility Service Provider'); ?></td>
                                <td><small class="text-muted"><?php echo html_escape($r->note ?: '-'); ?></small></td>
                                <td class="text-center"><span class="badge badge-light border"><?php echo html_escape($r->payment_method); ?></span></td>
                                <td class="text-right text-danger font-weight-bold" style="font-size: 14px;">
                                    <?php echo number_format($r->amount, 2); ?>
                                </td>
                                <td class="text-center"><span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Paid</span></td>
                                <td><small class="text-muted"><?php echo html_escape($r->creator_name); ?></small></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="fas fa-bolt text-muted fa-3x mb-2 d-block"></i>
                                    <strong>No utility bill payments recorded for this period.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="7" class="text-right font-weight-bold">Total Utility Expenses:</td>
                            <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_utility'], 2); ?></td>
                            <td colspan="2"></td>
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
                        <strong>Facility Manager</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Accounts Head</strong>
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

    if ($.fn.DataTable && $('#utilityBillsTable tbody tr td').length > 1) {
        $('#utilityBillsTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 25,
            order: [[1, 'desc']]
        });
    }
});
</script>

