<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
$selected_category = !empty($selected_category) ? $selected_category : '';
?>
<link href="common/extranal/css/finance/expense_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_expense_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">General Expense Ledger &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-book text-danger"></i> General Expense Ledger</h2>
                    <p>Chronological audit ledger of all recorded expense disbursements &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'expense_ledger']); ?>
                </div>
            </div>

            <form method="GET" action="finance/expenseLedgerReport" class="m-0">
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

                        <!-- Category Filter -->
                        <select name="category" class="rep-select">
                            <option value="">-- All Categories --</option>
                            <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                                <option value="<?php echo html_escape($cat->category); ?>" <?php echo ($selected_category === $cat->category) ? 'selected' : ''; ?>>
                                    <?php echo html_escape($cat->category); ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Apply</button>
                        <a href="finance/expenseLedgerReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-receipt text-danger"></i> Cumulative Debit</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['total_debit'], 2); ?></span>
                <span class="rep-kpi-sub">Total disbursements in period</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-list-ol text-primary"></i> Transactions Logged</span>
                <span class="rep-kpi-val"><?php echo number_format($summary['total_transactions']); ?></span>
                <span class="rep-kpi-sub">Chronological entries</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-balance-scale text-info"></i> Accounting Policy</span>
                <span class="rep-kpi-val" style="font-size:16px; padding-top:6px;">Cash Accounting</span>
                <span class="rep-kpi-sub">Debited upon voucher disbursement</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-tag text-warning"></i> Selected Scope</span>
                <span class="rep-kpi-val" style="font-size:16px; padding-top:6px;"><?php echo !empty($selected_category) ? html_escape($selected_category) : 'All Expenses'; ?></span>
                <span class="rep-kpi-sub">Filter category status</span>
            </div>
        </div>

        <!-- Ledger Table Card -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="expenseLedgerTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date & Time</th>
                            <th>Voucher #</th>
                            <th>Category</th>
                            <th>Payee / Vendor</th>
                            <th>Description / Particulars</th>
                            <th class="text-center">Method</th>
                            <th class="text-right">Debit Amount (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Running Total (<?php echo trim($currency); ?>)</th>
                            <th>Created By</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            foreach ($records as $r): 
                                $payee = !empty($r->pay_to) ? $r->pay_to : '-';
                                $desc = !empty($r->note) ? $r->note : '-';
                                $method = !empty($r->payment_method) ? $r->payment_method : 'Cash';
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><?php echo date('d/m/Y h:i A', $r->date); ?></td>
                                <td class="font-weight-bold">
                                    <a href="finance/expenseInvoice?id=<?php echo $r->id; ?>" target="_blank" class="text-primary">
                                        EXP-<?php echo $r->id; ?>
                                    </a>
                                </td>
                                <td><span class="badge badge-light border font-weight-bold text-dark"><?php echo html_escape($r->category ?: 'General'); ?></span></td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($payee); ?></td>
                                <td><small class="text-muted"><?php echo html_escape($desc); ?></small></td>
                                <td class="text-center">
                                    <span class="badge badge-secondary px-2 py-1"><?php echo html_escape($method); ?></span>
                                </td>
                                <td class="text-right text-danger font-weight-bold" style="font-size: 14px;">
                                    <?php echo number_format($r->amount, 2); ?>
                                </td>
                                <td class="text-right font-weight-bold text-dark">
                                    <?php echo number_format($r->running_balance, 2); ?>
                                </td>
                                <td><small class="text-muted"><?php echo html_escape($r->creator_name); ?></small></td>
                                <td class="text-center">
                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> <?php echo $r->status; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-5">
                                    <i class="fas fa-book-open text-muted fa-3x mb-2 d-block"></i>
                                    <strong>No expense ledger records found for the selected period.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="7" class="text-right font-weight-bold">Total Debits (<?php echo count($records); ?> transactions):</td>
                            <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_debit'], 2); ?></td>
                            <td class="text-right font-weight-bold text-dark"><?php echo number_format($summary['total_debit'], 2); ?></td>
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
                        <strong>Ledger Clerk</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Chief Accountant</strong>
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

    if ($.fn.DataTable && $('#expenseLedgerTable tbody tr td').length > 1) {
        $('#expenseLedgerTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[1, 'asc']], // Chronological by date ascending
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search ledger..."
            }
        });
    }
});
</script>

