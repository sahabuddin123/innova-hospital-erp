<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
$selected_category = !empty($selected_category) ? $selected_category : '';
$selected_method = !empty($selected_method) ? $selected_method : '';
$selected_user = !empty($selected_user) ? $selected_user : '';
?>
<link href="common/extranal/css/finance/expense_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_expense_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Daily Expense Report &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-calendar-day text-danger"></i> Daily Expense Report</h2>
                    <p>Itemized hospital and diagnostic operational disbursements &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <a href="finance/addExpenseView" class="rep-action-btn text-primary font-weight-bold"><i class="fas fa-plus-circle"></i> Add Expense</a>
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/dailyExpenseReport" class="m-0">
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

                        <!-- Payment Method Filter -->
                        <select name="payment_method" class="rep-select">
                            <option value="">-- All Payment Methods --</option>
                            <option value="Cash" <?php echo ($selected_method === 'Cash') ? 'selected' : ''; ?>>Cash</option>
                            <option value="Bank" <?php echo ($selected_method === 'Bank') ? 'selected' : ''; ?>>Bank Transfer</option>
                            <option value="bKash" <?php echo ($selected_method === 'bKash') ? 'selected' : ''; ?>>bKash</option>
                            <option value="Nagad" <?php echo ($selected_method === 'Nagad') ? 'selected' : ''; ?>>Nagad</option>
                            <option value="Card" <?php echo ($selected_method === 'Card') ? 'selected' : ''; ?>>Card</option>
                        </select>

                        <!-- User Filter -->
                        <select name="user_id" class="rep-select">
                            <option value="">-- All Users --</option>
                            <?php if (!empty($users)): foreach ($users as $u): ?>
                                <option value="<?php echo $u->id; ?>" <?php echo ($selected_user == $u->id) ? 'selected' : ''; ?>>
                                    <?php echo html_escape(!empty($u->first_name) ? $u->first_name . ' ' . $u->last_name : $u->username); ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Filter</button>
                        <a href="finance/dailyExpenseReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-money-bill-wave text-danger"></i> Total Expenses</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['total_expense'], 2); ?></span>
                <span class="rep-kpi-sub">Across <?php echo number_format($summary['total_transactions']); ?> expense vouchers</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-coins text-warning"></i> Cash Expenses</span>
                <span class="rep-kpi-val text-warning"><?php echo $currency . number_format($summary['cash_expense'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo ($summary['total_expense'] > 0) ? round(($summary['cash_expense'] / $summary['total_expense']) * 100, 1) : 0; ?>% of total expense</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-university text-primary"></i> Bank Expenses</span>
                <span class="rep-kpi-val text-primary"><?php echo $currency . number_format($summary['bank_expense'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo ($summary['total_expense'] > 0) ? round(($summary['bank_expense'] / $summary['total_expense']) * 100, 1) : 0; ?>% via bank transfers</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-mobile-alt text-info"></i> Digital / Mobile Payments</span>
                <span class="rep-kpi-val text-info"><?php echo $currency . number_format($summary['online_expense'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo ($summary['total_expense'] > 0) ? round(($summary['online_expense'] / $summary['total_expense']) * 100, 1) : 0; ?>% via bKash, Nagad, Card</span>
            </div>
        </div>

        <!-- Expense Records Table Card -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="dailyExpenseTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Voucher #</th>
                            <th>Expense Date</th>
                            <th>Category</th>
                            <th>Payee / Vendor</th>
                            <th>Payer / Staff</th>
                            <th>Description / Note</th>
                            <th class="text-center">Payment Method</th>
                            <th class="text-right">Amount (<?php echo trim($currency); ?>)</th>
                            <th>Recorded By</th>
                            <th class="text-center d-print-none">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            foreach ($records as $r): 
                                $cat_name = !empty($r->category) ? $r->category : 'General Expense';
                                $payee = !empty($r->pay_to) ? $r->pay_to : '-';
                                $payer = !empty($r->payer_name) ? $r->payer_name : '-';
                                $desc = !empty($r->note) ? $r->note : '-';
                                $method = !empty($r->payment_method) ? $r->payment_method : 'Cash';

                                $badge_class = 'badge-secondary';
                                if ($method === 'Cash') $badge_class = 'badge-success';
                                elseif ($method === 'bKash') $badge_class = 'badge-danger';
                                elseif ($method === 'Nagad') $badge_class = 'badge-warning text-dark';
                                elseif ($method === 'Bank') $badge_class = 'badge-primary';
                                elseif ($method === 'Card') $badge_class = 'badge-info';
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td class="font-weight-bold">
                                    <a href="finance/expenseInvoice?id=<?php echo $r->id; ?>" target="_blank" class="text-primary font-weight-bold">
                                        EXP-<?php echo $r->id; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($r->date) && is_numeric($r->date)) {
                                        echo date('d/m/Y', $r->date);
                                    } elseif (!empty($r->bill_date)) {
                                        echo date('d/m/Y', strtotime($r->bill_date));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td><span class="badge badge-light border font-weight-bold text-dark"><?php echo html_escape($cat_name); ?></span></td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($payee); ?></td>
                                <td><small class="text-muted"><?php echo html_escape($payer); ?></small></td>
                                <td><small class="text-muted"><?php echo html_escape($desc); ?></small></td>
                                <td class="text-center">
                                    <span class="badge <?php echo $badge_class; ?> px-2 py-1"><?php echo html_escape($method); ?></span>
                                </td>
                                <td class="text-right text-danger font-weight-bold" style="font-size: 14.5px;">
                                    <?php echo number_format($r->amount, 2); ?>
                                </td>
                                <td><small class="text-muted"><i class="fas fa-user-circle"></i> <?php echo html_escape($r->creator_name); ?></small></td>
                                <td class="text-center d-print-none">
                                    <a href="finance/expenseInvoice?id=<?php echo $r->id; ?>" target="_blank" class="btn btn-xs btn-outline-primary" title="View Expense Voucher">
                                        <i class="fas fa-file-invoice"></i> Voucher
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-5">
                                    <i class="fas fa-receipt text-muted fa-3x mb-2 d-block"></i>
                                    <strong>No expense records found for the selected period.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="8" class="text-right font-weight-bold">Grand Total (<?php echo count($records); ?> vouchers):</td>
                            <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_expense'], 2); ?></td>
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
                        <strong>Cashier / Purchaser</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Accounts Officer</strong>
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

    if ($.fn.DataTable && $('#dailyExpenseTable tbody tr td').length > 1) {
        $('#dailyExpenseTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[2, 'desc'], [1, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search category, payee, voucher..."
            }
        });
    }
});
</script>

