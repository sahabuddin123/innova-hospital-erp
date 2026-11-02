<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
?>
<link href="common/extranal/css/finance/expense_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_expense_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Petty Cash & Minor Disbursements Report &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-coins text-warning"></i> Petty Cash Disbursements Report</h2>
                    <p>Audit trail of minor office expenses, staff entertainment, snacks, and daily stationery vouchers &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'petty_cash']); ?>
                </div>
            </div>

            <form method="GET" action="finance/pettyCashReport" class="m-0">
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

                    <!-- Filter Inputs -->
                    <div class="rep-filter-group">
                        <div class="rep-input-wrap">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">FROM:</span>
                            <input type="date" name="date_from" id="date_from" class="rep-date-input" value="<?php echo html_escape($period['date_from']); ?>">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">TO:</span>
                            <input type="date" name="date_to" id="date_to" class="rep-date-input" value="<?php echo html_escape($period['date_to']); ?>">
                        </div>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Apply</button>
                        <a href="finance/pettyCashReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-coins text-warning"></i> Total Petty Cash Outlay</span>
                <span class="rep-kpi-val text-warning"><?php echo $currency . number_format($summary['total_petty_cash'], 2); ?></span>
                <span class="rep-kpi-sub">Across <?php echo number_format($summary['voucher_count']); ?> petty vouchers</span>
            </div>
            <div class="rep-kpi-card success">
                <span class="rep-kpi-label"><i class="fas fa-money-bill-wave text-success"></i> Cash In-Hand Outflow</span>
                <span class="rep-kpi-val text-success"><?php echo $currency . number_format($summary['cash_disbursed'], 2); ?></span>
                <span class="rep-kpi-sub">Direct drawer cash payouts</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-calculator text-info"></i> Average Voucher Size</span>
                <span class="rep-kpi-val text-info"><?php echo $currency . number_format($summary['average_voucher'], 2); ?></span>
                <span class="rep-kpi-sub">Per minor expense transaction</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-receipt text-primary"></i> Vouchers Issued</span>
                <span class="rep-kpi-val"><?php echo number_format($summary['voucher_count']); ?></span>
                <span class="rep-kpi-sub">Receipts verified</span>
            </div>
        </div>

        <!-- Disclosure Alert -->
        <div class="alert alert-light border mb-4 p-3 d-print-none" style="border-radius: 8px; font-size: 13px;">
            <i class="fas fa-info-circle text-info mr-1"></i>
            <strong>Accounting Clarification:</strong> Lifecare Diagnostic Center operates on a direct voucher reimbursement model where petty expenditures are recorded as approved expense vouchers rather than holding an imprest petty float.
        </div>

        <!-- Petty Cash Table Card -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="pettyCashTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Voucher Date</th>
                            <th>Voucher #</th>
                            <th>Particulars / Description</th>
                            <th>Category</th>
                            <th>Payee / Recipient</th>
                            <th>Purchaser / Staff</th>
                            <th class="text-right">Amount (<?php echo trim($currency); ?>)</th>
                            <th>Recorded By</th>
                            <th class="text-center d-print-none">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            foreach ($records as $r): 
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><?php echo date('d/m/Y', $r->date); ?></td>
                                <td class="font-weight-bold">
                                    <a href="finance/expenseInvoice?id=<?php echo $r->id; ?>" target="_blank" class="text-primary">
                                        EXP-<?php echo $r->id; ?>
                                    </a>
                                </td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($r->note ?: '-'); ?></td>
                                <td><span class="badge badge-light border"><?php echo html_escape($r->category ?: 'General'); ?></span></td>
                                <td><?php echo html_escape($r->pay_to ?: '-'); ?></td>
                                <td><small class="text-muted"><?php echo html_escape($r->payer_name ?: '-'); ?></small></td>
                                <td class="text-right font-weight-bold text-danger" style="font-size: 14px;">
                                    <?php echo number_format($r->amount, 2); ?>
                                </td>
                                <td><small class="text-muted"><?php echo html_escape($r->creator_name); ?></small></td>
                                <td class="text-center d-print-none">
                                    <a href="finance/expenseInvoice?id=<?php echo $r->id; ?>" target="_blank" class="btn btn-xs btn-outline-primary" title="View voucher">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="fas fa-coins text-muted fa-3x mb-2 d-block"></i>
                                    <strong>No petty cash expense vouchers recorded in this period.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="7" class="text-right font-weight-bold">Grand Total (<?php echo count($records); ?> vouchers):</td>
                            <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_petty_cash'], 2); ?></td>
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
                        <strong>Cashier / Drawer Clerk</strong>
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

    if ($.fn.DataTable && $('#pettyCashTable tbody tr td').length > 1) {
        $('#pettyCashTable').DataTable({
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

