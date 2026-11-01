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
            <p class="text-muted mb-0">Bank Book & Direct Wire Transfers Ledger &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-university text-primary"></i> Bank Book (Bank Deposits & Transfers)</h2>
                    <p>Chronological bank account collections from patients vs bank vendor disbursements &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print Bank Book</button>
                </div>
            </div>

            <form method="GET" action="finance/bankBookReport" class="m-0">
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
                        <a href="finance/bankBookReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card success">
                <span class="rep-kpi-label"><i class="fas fa-arrow-down text-success"></i> Bank Deposits (Inflow)</span>
                <span class="rep-kpi-val text-success"><?php echo $currency . number_format($summary['total_deposits'], 2); ?></span>
                <span class="rep-kpi-sub">Direct patient wire deposits</span>
            </div>
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-arrow-up text-danger"></i> Bank Withdrawals (Outflow)</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['total_withdrawals'], 2); ?></span>
                <span class="rep-kpi-sub">Cheque / wire expense payments</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-university text-primary"></i> Net Bank Position</span>
                <span class="rep-kpi-val <?php echo ($summary['net_bank_flow'] >= 0) ? 'text-primary' : 'text-danger'; ?>">
                    <?php echo $currency . number_format($summary['net_bank_flow'], 2); ?>
                </span>
                <span class="rep-kpi-sub">Deposits minus Withdrawals</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-exchange-alt text-info"></i> Bank Transactions</span>
                <span class="rep-kpi-val"><?php echo number_format($summary['tx_count']); ?></span>
                <span class="rep-kpi-sub">Banking transactions logged</span>
            </div>
        </div>

        <!-- Disclosure Alert -->
        <div class="alert alert-light border mb-4 p-3 d-print-none" style="border-radius: 8px; font-size: 13px;">
            <i class="fas fa-university text-primary mr-1"></i>
            <strong>Bank Ledger Standard:</strong> This Bank Book reconciles electronic bank deposits and cheque payouts recorded across patient deposits and expense vouchers. MFS (bKash/Nagad) and Cash transactions are completely excluded.
        </div>

        <!-- Bank Ledger Table -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="bankBookTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date & Time</th>
                            <th>Reference #</th>
                            <th>Particulars / Source</th>
                            <th class="text-center">Transaction Type</th>
                            <th class="text-right text-success">Deposit (<?php echo trim($currency); ?>)</th>
                            <th class="text-right text-danger">Withdrawal (<?php echo trim($currency); ?>)</th>
                            <th class="text-right font-weight-bold">Running Position (<?php echo trim($currency); ?>)</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            foreach ($records as $r): 
                                $is_dep = ($r['type'] === 'Deposit');
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><?php echo date('d/m/Y h:i A', $r['date']); ?></td>
                                <td class="font-weight-bold">
                                    <span class="badge badge-light border"><?php echo html_escape($r['id']); ?></span>
                                </td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($r['description']); ?></td>
                                <td class="text-center">
                                    <?php if ($is_dep): ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-arrow-down"></i> Deposit</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-arrow-up"></i> Withdrawal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right font-weight-bold text-success">
                                    <?php echo ($r['deposit'] > 0) ? number_format($r['deposit'], 2) : '-'; ?>
                                </td>
                                <td class="text-right font-weight-bold text-danger">
                                    <?php echo ($r['withdrawal'] > 0) ? number_format($r['withdrawal'], 2) : '-'; ?>
                                </td>
                                <td class="text-right font-weight-bold <?php echo ($r['running_balance'] >= 0) ? 'text-primary' : 'text-danger'; ?>" style="font-size: 14px;">
                                    <?php echo number_format($r['running_balance'], 2); ?>
                                </td>
                                <td><small class="text-muted"><?php echo html_escape($r['recorded_by']); ?></small></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-university text-muted fa-3x mb-2 d-block"></i>
                                    <strong>No bank transactions recorded in the selected period.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="5" class="text-right font-weight-bold">Period Totals:</td>
                            <td class="text-right font-weight-bold text-success"><?php echo number_format($summary['total_deposits'], 2); ?></td>
                            <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_withdrawals'], 2); ?></td>
                            <td class="text-right font-weight-bold text-primary"><?php echo number_format($summary['net_bank_flow'], 2); ?></td>
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
                        <strong>Bank Reconciliation Clerk</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Finance Controller</strong>
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

    if ($.fn.DataTable && $('#bankBookTable tbody tr td').length > 1) {
        $('#bankBookTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 25,
            order: [[1, 'asc']]
        });
    }
});
</script>
