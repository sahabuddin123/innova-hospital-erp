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
                        <i class="fas fa-calendar-day text-primary"></i> Daily Profit &amp; Loss Report
                        <?php if ($is_accrual) { ?>
                            <span class="badge badge-accrual">Accrual Basis</span>
                        <?php } else { ?>
                            <span class="badge badge-cash">Cash / Collection Basis</span>
                        <?php } ?>
                    </h2>
                    <p>Hourly operating performance and granular transaction drill-down &bull; <strong><?php echo date('l, d F Y', strtotime($target_date)); ?></strong></p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div class="basis-toggle-group d-print-none">
                        <a href="finance/dailyProfitLoss?date=<?php echo $target_date; ?>&accounting_basis=cash" class="basis-toggle-btn <?php echo (!$is_accrual) ? 'active-cash' : ''; ?>">
                            <i class="fas fa-coins"></i> Cash Basis
                        </a>
                        <a href="finance/dailyProfitLoss?date=<?php echo $target_date; ?>&accounting_basis=accrual" class="basis-toggle-btn <?php echo ($is_accrual) ? 'active-accrual' : ''; ?>">
                            <i class="fas fa-balance-scale"></i> Accrual Basis
                        </a>
                    </div>
                    <div class="rep-actions d-print-none">
                        <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'pnl_daily']); ?>
                    </div>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--pnl-gray-200);">

            <!-- Date Selector Form -->
            <form action="finance/dailyProfitLoss" method="get" class="row g-3 align-items-end mt-2 d-print-none">
                <input type="hidden" name="accounting_basis" value="<?php echo $accounting_basis; ?>">
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">Select Date</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="<?php echo html_escape($target_date); ?>">
                </div>
                <div class="col-md-4 d-flex" style="gap: 8px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-search"></i> Load Day</button>
                    <a href="finance/dailyProfitLoss?date=<?php echo date('Y-m-d'); ?>&accounting_basis=<?php echo $accounting_basis; ?>" class="btn btn-sm btn-outline-secondary">Today</a>
                    <a href="finance/dailyProfitLoss?date=<?php echo date('Y-m-d', strtotime('-1 day')); ?>&accounting_basis=<?php echo $accounting_basis; ?>" class="btn btn-sm btn-outline-secondary">Yesterday</a>
                </div>
            </form>
        </div>

        <!-- 8 KPI Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-card">
                <div class="rep-card-icon bg-blue"><i class="fas fa-file-invoice"></i></div>
                <div class="rep-card-label">Daily Billing (Gross)</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></div>
                <div class="rep-card-sub text-muted"><?php echo number_format($summary['total_invoices']); ?> invoices generated</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-purple"><i class="fas fa-tags"></i></div>
                <div class="rep-card-label">Daily Discounts</div>
                <div class="rep-card-val text-danger"><?php echo $currency . ' ' . number_format($summary['discount'], 2); ?></div>
                <div class="rep-card-sub text-muted">Granted on invoices</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-teal"><i class="fas fa-receipt"></i></div>
                <div class="rep-card-label">Net Billing</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></div>
                <div class="rep-card-sub text-muted">Gross minus discounts</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-green"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="rep-card-label">Patient Collections</div>
                <div class="rep-card-val text-success"><?php echo $currency . ' ' . number_format($summary['patient_collections'], 2); ?></div>
                <div class="rep-card-sub text-success">Actual cash received</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-orange"><i class="fas fa-undo"></i></div>
                <div class="rep-card-label">Daily Refunds</div>
                <div class="rep-card-val text-warning"><?php echo $currency . ' ' . number_format($summary['refunds'], 2); ?></div>
                <div class="rep-card-sub text-muted">Returned deposits</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-emerald"><i class="fas fa-coins"></i></div>
                <div class="rep-card-label">Net Collections</div>
                <div class="rep-card-val text-success"><?php echo $currency . ' ' . number_format($summary['net_collections'], 2); ?></div>
                <div class="rep-card-sub text-success">Cash inflow for day</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-red"><i class="fas fa-wallet"></i></div>
                <div class="rep-card-label">Operating Expenses</div>
                <div class="rep-card-val text-danger"><?php echo $currency . ' ' . number_format($summary['operating_expenses_paid'], 2); ?></div>
                <div class="rep-card-sub text-muted">Disbursed costs</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-indigo"><i class="fas fa-award"></i></div>
                <div class="rep-card-label">Operating Surplus</div>
                <div class="rep-card-val text-primary"><?php echo $currency . ' ' . number_format($summary['cash_surplus'], 2); ?></div>
                <div class="rep-card-sub text-primary">Collections &minus; Outflows</div>
            </div>
        </div>

        <!-- Hourly Performance Matrix Table -->
        <div class="rep-table-card mt-4">
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--pnl-gray-800);">
                <i class="fas fa-clock text-primary mr-1"></i> Hourly Operating Performance Breakdown
            </h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle rep-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 70px;">Hour</th>
                            <th class="text-center">Invoices</th>
                            <th class="text-right">Gross Billing</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Net Billing</th>
                            <th class="text-right">Collections</th>
                            <th class="text-right">Refunds</th>
                            <th class="text-right">Net Collections</th>
                            <th class="text-right">Expenses</th>
                            <th class="text-right">Operating Surplus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $has_activity = false;
                        foreach ($hourly_slots as $slot) { 
                            if ($slot['invoices'] == 0 && $slot['patient_collections'] == 0 && $slot['operating_expenses'] == 0) {
                                continue;
                            }
                            $has_activity = true;
                        ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?php echo $slot['hour']; ?></td>
                                <td class="text-center"><?php echo $slot['invoices']; ?></td>
                                <td class="text-right"><?php echo $currency . ' ' . number_format($slot['gross_billing'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($slot['discount'], 2); ?></td>
                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($slot['net_billing'], 2); ?></td>
                                <td class="text-right text-success"><?php echo $currency . ' ' . number_format($slot['patient_collections'], 2); ?></td>
                                <td class="text-right text-warning"><?php echo $currency . ' ' . number_format($slot['refunds'], 2); ?></td>
                                <td class="text-right font-weight-bold text-success"><?php echo $currency . ' ' . number_format($slot['net_collections'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($slot['operating_expenses'], 2); ?></td>
                                <td class="text-right font-weight-bold <?php echo ($slot['cash_surplus'] >= 0) ? 'text-success' : 'text-danger'; ?>" style="background-color: #f8fafc;">
                                    <?php echo $currency . ' ' . number_format($slot['cash_surplus'], 2); ?>
                                </td>
                            </tr>
                        <?php } 
                        if (!$has_activity) { ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle mr-1"></i> No transactions recorded for this date.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f1f5f9; font-weight: 700;">
                            <td class="text-center">TOTAL:</td>
                            <td class="text-center"><?php echo number_format($summary['total_invoices']); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['discount'], 2); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></td>
                            <td class="text-right text-success"><?php echo $currency . ' ' . number_format($summary['patient_collections'], 2); ?></td>
                            <td class="text-right text-warning"><?php echo $currency . ' ' . number_format($summary['refunds'], 2); ?></td>
                            <td class="text-right text-success"><?php echo $currency . ' ' . number_format($summary['net_collections'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['operating_expenses_paid'], 2); ?></td>
                            <td class="text-right text-primary"><?php echo $currency . ' ' . number_format($summary['cash_surplus'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Transaction Drill-Down Nav Tabs -->
        <div class="rep-table-card mt-4 d-print-none">
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--pnl-gray-800);">
                <i class="fas fa-list-alt text-primary mr-1"></i> Daily Transaction Drill-Down
            </h4>

            <ul class="nav nav-tabs" id="dailyTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="inv-tab" data-toggle="tab" href="#tab-invoices" role="tab">
                        <i class="fas fa-file-invoice mr-1"></i> Invoices (<?php echo count($invoices); ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="dep-tab" data-toggle="tab" href="#tab-deposits" role="tab">
                        <i class="fas fa-hand-holding-usd mr-1"></i> Cash Receipts (<?php echo count($deposits); ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="exp-tab" data-toggle="tab" href="#tab-expenses" role="tab">
                        <i class="fas fa-wallet mr-1"></i> Expense Vouchers (<?php echo count($expenses); ?>)
                    </a>
                </li>
            </ul>

            <div class="tab-content pt-3" id="dailyTabsContent">
                <!-- Invoices Tab -->
                <div class="tab-pane fade show active" id="tab-invoices" role="tabpanel">
                    <table class="table table-bordered table-sm rep-table" id="daily-inv-table">
                        <thead>
                            <tr>
                                <th>Inv #</th>
                                <th>Time</th>
                                <th>Patient Name</th>
                                <th>Doctor</th>
                                <th>Referrer</th>
                                <th class="text-right">Gross</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $inv) { ?>
                                <tr>
                                    <td><strong>#<?php echo $inv->id; ?></strong></td>
                                    <td><?php echo date('H:i', $inv->date); ?></td>
                                    <td><?php echo html_escape($inv->patient_name); ?></td>
                                    <td><?php echo html_escape($inv->doctor_name); ?></td>
                                    <td><?php echo html_escape($inv->referrer_name); ?></td>
                                    <td class="text-right"><?php echo $currency . ' ' . number_format($inv->amount, 2); ?></td>
                                    <td class="text-right text-danger"><?php echo $currency . ' ' . number_format(!empty($inv->flat_discount) ? $inv->flat_discount : $inv->discount, 2); ?></td>
                                    <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($inv->gross_total, 2); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Deposits Tab -->
                <div class="tab-pane fade" id="tab-deposits" role="tabpanel">
                    <table class="table table-bordered table-sm rep-table" id="daily-dep-table">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Time</th>
                                <th>Inv #</th>
                                <th>Method</th>
                                <th>Cashier</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deposits as $dp) { ?>
                                <tr>
                                    <td>#<?php echo $dp->id; ?></td>
                                    <td><?php echo date('H:i', $dp->date); ?></td>
                                    <td>#<?php echo $dp->payment_id; ?></td>
                                    <td><span class="badge badge-light border"><?php echo html_escape($dp->deposit_type); ?></span></td>
                                    <td><?php echo html_escape($dp->cashier_name); ?></td>
                                    <td class="text-right font-weight-bold text-success"><?php echo $currency . ' ' . number_format($dp->deposited_amount, 2); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Expenses Tab -->
                <div class="tab-pane fade" id="tab-expenses" role="tabpanel">
                    <table class="table table-bordered table-sm rep-table" id="daily-exp-table">
                        <thead>
                            <tr>
                                <th>Voucher #</th>
                                <th>Time</th>
                                <th>Category</th>
                                <th>Payee / Note</th>
                                <th>Recorded By</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expenses as $ex) { ?>
                                <tr>
                                    <td>#<?php echo $ex->id; ?></td>
                                    <td><?php echo date('H:i', $ex->date); ?></td>
                                    <td><strong><?php echo html_escape($ex->category_name); ?></strong></td>
                                    <td><?php echo html_escape($ex->pay_to . ' - ' . $ex->note); ?></td>
                                    <td><?php echo html_escape($ex->recorded_by); ?></td>
                                    <td class="text-right font-weight-bold text-danger"><?php echo $currency . ' ' . number_format($ex->amount, 2); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Print Signatures -->
        <div class="d-none d-print-block rep-print-signatures">
            <div class="signature-box">Prepared By (Accounts Officer)</div>
            <div class="signature-box">Checked By (Audit Head)</div>
            <div class="signature-box">Approved By (Managing Director)</div>
        </div>

    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    $('#daily-inv-table, #daily-dep-table, #daily-exp-table').DataTable({
        responsive: true,
        pageLength: 10
    });
});
</script>
