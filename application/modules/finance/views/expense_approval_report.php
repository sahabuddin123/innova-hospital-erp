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
            <p class="text-muted mb-0">Expense Voucher Approval & Authorization Audit &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-check-double text-success"></i> Expense Approval & Authorization Audit</h2>
                    <p>Internal controls audit of expense submissions, approvals, and authorization status &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/expenseApprovalReport" class="m-0">
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
                        <a href="finance/expenseApprovalReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card success">
                <span class="rep-kpi-label"><i class="fas fa-check-circle text-success"></i> Approved & Disbursed</span>
                <span class="rep-kpi-val text-success"><?php echo $currency . number_format($summary['total_approved_amount'], 2); ?></span>
                <span class="rep-kpi-sub">Fully authorized vouchers</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-clipboard-check text-primary"></i> Vouchers Audited</span>
                <span class="rep-kpi-val"><?php echo number_format($summary['total_vouchers']); ?></span>
                <span class="rep-kpi-sub">Total expense entries</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-clock text-warning"></i> Pending Approval</span>
                <span class="rep-kpi-val text-warning"><?php echo $currency . number_format($summary['pending_approval_amount'], 2); ?></span>
                <span class="rep-kpi-sub">Zero unapproved in active ledger</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-shield-alt text-info"></i> Approval Control</span>
                <span class="rep-kpi-val" style="font-size:16px; padding-top:6px;">Role-Based (Admin)</span>
                <span class="rep-kpi-sub">Restricted entry & edit permissions</span>
            </div>
        </div>

        <!-- Approval Table Card -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="expenseApprovalTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date & Time</th>
                            <th>Voucher #</th>
                            <th>Category</th>
                            <th>Description / Note</th>
                            <th>Payee / Vendor</th>
                            <th class="text-right">Amount (<?php echo trim($currency); ?>)</th>
                            <th class="text-center">Approval Status</th>
                            <th>Submitted By</th>
                            <th>Authorized By</th>
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
                                <td><?php echo date('d/m/Y h:i A', $r->date); ?></td>
                                <td class="font-weight-bold">
                                    <a href="finance/expenseInvoice?id=<?php echo $r->id; ?>" target="_blank" class="text-primary font-weight-bold">
                                        EXP-<?php echo $r->id; ?>
                                    </a>
                                </td>
                                <td><span class="badge badge-light border font-weight-bold"><?php echo html_escape($r->category ?: 'General'); ?></span></td>
                                <td><small class="text-muted"><?php echo html_escape($r->note ?: '-'); ?></small></td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($r->pay_to ?: '-'); ?></td>
                                <td class="text-right font-weight-bold text-danger" style="font-size: 14px;">
                                    <?php echo number_format($r->amount, 2); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double"></i> <?php echo $r->approval_status; ?></span>
                                </td>
                                <td><small class="text-muted"><i class="fas fa-user-edit"></i> <?php echo html_escape($r->creator_name); ?></small></td>
                                <td><small class="text-muted"><i class="fas fa-user-shield text-success"></i> <?php echo html_escape($r->approved_by); ?></small></td>
                                <td class="text-center d-print-none">
                                    <a href="finance/expenseInvoice?id=<?php echo $r->id; ?>" target="_blank" class="btn btn-xs btn-outline-primary" title="View voucher">
                                        <i class="fas fa-file-invoice"></i> Voucher
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-5">
                                    <i class="fas fa-clipboard-check text-muted fa-3x mb-2 d-block"></i>
                                    <strong>No expense vouchers found for the selected period.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="6" class="text-right font-weight-bold">Grand Total (<?php echo count($records); ?> vouchers):</td>
                            <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_approved_amount'], 2); ?></td>
                            <td colspan="4"></td>
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

    if ($.fn.DataTable && $('#expenseApprovalTable tbody tr td').length > 1) {
        $('#expenseApprovalTable').DataTable({
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
