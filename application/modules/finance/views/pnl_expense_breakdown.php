<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳';
$is_accrual = ($accounting_basis === 'accrual');
?>
<link rel="stylesheet" href="common/extranal/css/finance/pnl_reports.css">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <?php $this->load->view('finance/inc_pnl_reports_nav'); ?>

        <!-- Header Card -->
        <div class="rep-header-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 16px;">
                <div class="rep-title">
                    <h2>
                        <i class="fas fa-receipt text-primary"></i> Operating Expense Breakdown Analysis
                        <?php if ($is_accrual) { ?>
                            <span class="badge badge-accrual">Recognized Expense Basis</span>
                        <?php } else { ?>
                            <span class="badge badge-cash">Cash Disbursement Basis</span>
                        <?php } ?>
                    </h2>
                    <p>Categorical, payment channel, and vendor disbursement analysis preventing duplicate commission deductions &bull; <strong><?php echo $period['period_label']; ?></strong></p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div class="basis-toggle-group d-print-none">
                        <a href="finance/expenseProfitLossBreakdown?period_type=<?php echo $period_type; ?>&accounting_basis=cash" class="basis-toggle-btn <?php echo (!$is_accrual) ? 'active-cash' : ''; ?>">
                            <i class="fas fa-coins"></i> Cash Basis
                        </a>
                        <a href="finance/expenseProfitLossBreakdown?period_type=<?php echo $period_type; ?>&accounting_basis=accrual" class="basis-toggle-btn <?php echo ($is_accrual) ? 'active-accrual' : ''; ?>">
                            <i class="fas fa-balance-scale"></i> Accrual Basis
                        </a>
                    </div>
                    <div class="rep-actions d-print-none">
                        <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'pnl_expense_breakdown']); ?>
                    </div>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--pnl-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/expenseProfitLossBreakdown?period_type=today&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/expenseProfitLossBreakdown?period_type=yesterday&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/expenseProfitLossBreakdown?period_type=this_week&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/expenseProfitLossBreakdown?period_type=this_month&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/expenseProfitLossBreakdown?period_type=last_month&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/expenseProfitLossBreakdown?period_type=this_year&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_year') ? 'active' : ''; ?>">This Year</a>
            </div>

            <!-- Custom Filter Form -->
            <form action="finance/expenseProfitLossBreakdown" method="get" class="row g-3 align-items-end mt-2 d-print-none">
                <input type="hidden" name="period_type" value="custom">
                <input type="hidden" name="accounting_basis" value="<?php echo $accounting_basis; ?>">
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo html_escape($period['date_from']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo html_escape($period['date_to']); ?>">
                </div>
                <div class="col-md-4 d-flex" style="gap: 8px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-filter"></i> Apply Filter</button>
                    <a href="finance/expenseProfitLossBreakdown?accounting_basis=<?php echo $accounting_basis; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>

        <!-- 4 Summary KPI Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-card">
                <div class="rep-card-icon bg-purple"><i class="fas fa-file-invoice"></i></div>
                <div class="rep-card-label">Total Vouchers</div>
                <div class="rep-card-val"><?php echo number_format($summary['total_vouchers']); ?></div>
                <div class="rep-card-sub text-muted">Completed disbursement records</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-blue"><i class="fas fa-cogs"></i></div>
                <div class="rep-card-label">General Operating Expenses</div>
                <div class="rep-card-val text-primary"><?php echo $currency . ' ' . number_format($summary['total_general_operating'], 2); ?></div>
                <div class="rep-card-sub text-primary">Overhead, utilities &amp; supplies</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-orange"><i class="fas fa-user-md"></i></div>
                <div class="rep-card-label">Doctor Commission Payouts</div>
                <div class="rep-card-val text-warning"><?php echo $currency . ' ' . number_format($summary['total_doctor_commission_disbursements'], 2); ?></div>
                <div class="rep-card-sub text-muted">Physician fees from expenses</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-red"><i class="fas fa-wallet"></i></div>
                <div class="rep-card-label">Total Outflows Recorded</div>
                <div class="rep-card-val text-danger"><?php echo $currency . ' ' . number_format($summary['total_all_expenses'], 2); ?></div>
                <div class="rep-card-sub text-danger">Combined disbursements</div>
            </div>
        </div>

        <!-- Important Anti-Double-Counting Notice -->
        <div class="alert alert-warning shadow-sm border-0 d-flex align-items-center mb-4" role="alert" style="border-radius: var(--pnl-radius);">
            <div class="mr-3 font-weight-bold" style="font-size: 20px;">
                <i class="fas fa-shield-alt text-warning"></i>
            </div>
            <div>
                <strong>Accounting Anti-Double-Counting Rule:</strong> When calculating Net Operating Surplus in the executive P&amp;L, doctor commission payouts (৳<?php echo number_format($summary['total_doctor_commission_disbursements'], 2); ?>) and referral payouts (৳<?php echo number_format($summary['total_referral_commission_disbursements'], 2); ?>) are segregated into their dedicated commission lines and excluded from general operating costs to ensure zero duplication!
            </div>
        </div>

        <!-- Breakdown Tables (2 Columns: Categories and Payment Methods) -->
        <div class="row g-4 mb-4">
            <!-- Category Breakdown -->
            <div class="col-lg-7">
                <div class="rep-table-card h-100">
                    <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 14px; color: var(--pnl-gray-800);">
                        <i class="fas fa-folder-open text-primary mr-1"></i> Expense Category Breakdown
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm rep-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Classification</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-right">Amount (৳)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($by_category as $cat_r) { ?>
                                    <tr>
                                        <td><strong><?php echo html_escape($cat_r['category']); ?></strong></td>
                                        <td><span class="badge badge-light border"><?php echo html_escape($cat_r['type']); ?></span></td>
                                        <td class="text-center"><?php echo $cat_r['count']; ?></td>
                                        <td class="text-right font-weight-bold text-danger"><?php echo $currency . ' ' . number_format($cat_r['amount'], 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment Method Breakdown -->
            <div class="col-lg-5">
                <div class="rep-table-card h-100">
                    <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 14px; color: var(--pnl-gray-800);">
                        <i class="fas fa-credit-card text-success mr-1"></i> Payment Channel Distribution
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm rep-table">
                            <thead>
                                <tr>
                                    <th>Payment Method</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-right">Amount (৳)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($by_method as $m_r) { ?>
                                    <tr>
                                        <td><strong><?php echo html_escape($m_r['method']); ?></strong></td>
                                        <td class="text-center"><?php echo $m_r['count']; ?></td>
                                        <td class="text-right font-weight-bold text-danger"><?php echo $currency . ' ' . number_format($m_r['amount'], 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Vouchers List -->
        <div class="rep-table-card mt-4">
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--pnl-gray-800);">
                <i class="fas fa-list-alt text-primary mr-1"></i> Detailed Expense Vouchers Ledger
            </h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle rep-table" id="expense-breakdown-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">Voucher #</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Classification</th>
                            <th>Payee / Vendor</th>
                            <th>Note / Purpose</th>
                            <th>Method</th>
                            <th>Recorded By</th>
                            <th class="text-right">Amount (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $e) { ?>
                            <tr>
                                <td class="text-center font-weight-bold">#<?php echo $e->id; ?></td>
                                <td><?php echo date('d/m/Y', $e->date); ?></td>
                                <td><strong><?php echo html_escape($e->category_name); ?></strong></td>
                                <td><span class="badge badge-light border"><?php echo html_escape($e->classification); ?></span></td>
                                <td><?php echo !empty($e->pay_to) ? html_escape($e->pay_to) : '<span class="text-muted">N/A</span>'; ?></td>
                                <td><?php echo html_escape($e->note); ?></td>
                                <td><span class="badge badge-info"><?php echo html_escape($e->payment_method); ?></span></td>
                                <td><?php echo html_escape($e->recorded_by); ?></td>
                                <td class="text-right font-weight-bold text-danger"><?php echo $currency . ' ' . number_format($e->amount, 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Print Signatures -->
            <div class="d-none d-print-block rep-print-signatures">
                <div class="signature-box">Prepared By (Accounts Officer)</div>
                <div class="signature-box">Checked By (Audit Head)</div>
                <div class="signature-box">Approved By (Managing Director)</div>
            </div>
        </div>

    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    $('#expense-breakdown-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Expense Breakdown - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Expense Breakdown - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Expense Breakdown - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Expense Breakdown - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[1, "desc"]],
        pageLength: 25
    });
});
</script>
