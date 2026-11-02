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
                        <i class="fas fa-calendar-check text-primary"></i> Yearly Profit &amp; Loss Report
                        <?php if ($is_accrual) { ?>
                            <span class="badge badge-accrual">Accrual Basis</span>
                        <?php } else { ?>
                            <span class="badge badge-cash">Cash / Collection Basis</span>
                        <?php } ?>
                    </h2>
                    <p>Annual 12-month performance matrix and Year-over-Year (YoY) comparison &bull; <strong>Year <?php echo $target_year; ?></strong></p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div class="basis-toggle-group d-print-none">
                        <a href="finance/yearlyProfitLoss?year=<?php echo $target_year; ?>&accounting_basis=cash" class="basis-toggle-btn <?php echo (!$is_accrual) ? 'active-cash' : ''; ?>">
                            <i class="fas fa-coins"></i> Cash Basis
                        </a>
                        <a href="finance/yearlyProfitLoss?year=<?php echo $target_year; ?>&accounting_basis=accrual" class="basis-toggle-btn <?php echo ($is_accrual) ? 'active-accrual' : ''; ?>">
                            <i class="fas fa-balance-scale"></i> Accrual Basis
                        </a>
                    </div>
                    <div class="rep-actions d-print-none">
                        <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'pnl_yearly']); ?>
                    </div>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--pnl-gray-200);">

            <!-- Year Selector Form -->
            <form action="finance/yearlyProfitLoss" method="get" class="row g-3 align-items-end mt-2 d-print-none">
                <input type="hidden" name="accounting_basis" value="<?php echo $accounting_basis; ?>">
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">Select Financial Year</label>
                    <select name="year" class="form-control form-control-sm">
                        <?php 
                        $cur_y = intval(date('Y'));
                        for ($y = $cur_y; $y >= $cur_y - 5; $y--) { ?>
                            <option value="<?php echo $y; ?>" <?php echo ($target_year == $y) ? 'selected' : ''; ?>>Financial Year <?php echo $y; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex" style="gap: 8px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-search"></i> Load Financial Year</button>
                    <a href="finance/yearlyProfitLoss?year=<?php echo date('Y'); ?>&accounting_basis=<?php echo $accounting_basis; ?>" class="btn btn-sm btn-outline-secondary">Current Year</a>
                </div>
            </form>
        </div>

        <!-- YoY Comparison Card -->
        <div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: var(--pnl-radius); background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small text-uppercase font-weight-bold text-muted"><i class="fas fa-chart-line mr-1"></i> Year-over-Year (YoY) Performance Comparison</span>
                <span class="badge <?php echo ($prev_year['growth_pct'] >= 0) ? 'badge-success' : 'badge-danger'; ?> px-2 py-1">
                    <?php echo ($prev_year['growth_pct'] >= 0) ? '+' : ''; ?><?php echo number_format($prev_year['growth_pct'], 2); ?>% Annual Growth
                </span>
            </div>
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="small text-muted">Year <?php echo $target_year; ?> Cash Surplus</div>
                    <div class="h3 font-weight-bold mb-0 text-dark"><?php echo $currency . ' ' . number_format($summary['cash_surplus'], 2); ?></div>
                </div>
                <div class="col-md-4 border-left">
                    <div class="small text-muted">Year <?php echo $prev_year['year']; ?> Cash Surplus</div>
                    <div class="h3 font-weight-bold mb-0 text-muted"><?php echo $currency . ' ' . number_format($prev_year['summary']['cash_surplus'], 2); ?></div>
                </div>
                <div class="col-md-4 border-left">
                    <div class="small text-muted">Surplus Variance</div>
                    <div class="h3 font-weight-bold mb-0 <?php echo ($prev_year['variance_surplus'] >= 0) ? 'text-success' : 'text-danger'; ?>">
                        <?php echo ($prev_year['variance_surplus'] >= 0) ? '+' : ''; ?><?php echo $currency . ' ' . number_format($prev_year['variance_surplus'], 2); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 12-Month Performance Table Card -->
        <div class="rep-table-card mt-4">
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--pnl-gray-800);">
                <i class="fas fa-calendar mr-1 text-primary"></i> 12-Month Performance Breakdown (Year <?php echo $target_year; ?>)
            </h4>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle rep-table" id="yearly-pnl-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-center">Invoices</th>
                            <th class="text-right">Gross Billing</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Net Billing</th>
                            <th class="text-right">Collections</th>
                            <th class="text-right">Refunds</th>
                            <th class="text-right">Net Collections</th>
                            <th class="text-right">Operating Exp</th>
                            <th class="text-right">Doctor Comm</th>
                            <th class="text-right">Ref Comm</th>
                            <th class="text-right">Operating Surplus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($month_rows as $mr) { ?>
                            <tr>
                                <td><strong><?php echo $mr['month_name']; ?></strong></td>
                                <td class="text-center"><?php echo $mr['invoices']; ?></td>
                                <td class="text-right"><?php echo $currency . ' ' . number_format($mr['gross_billing'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($mr['discount'], 2); ?></td>
                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($mr['net_billing'], 2); ?></td>
                                <td class="text-right text-success"><?php echo $currency . ' ' . number_format($mr['patient_collections'], 2); ?></td>
                                <td class="text-right text-warning"><?php echo $currency . ' ' . number_format($mr['refunds'], 2); ?></td>
                                <td class="text-right font-weight-bold text-success"><?php echo $currency . ' ' . number_format($mr['net_collections'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($mr['operating_expenses'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($mr['doctor_comm'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($mr['referral_comm'], 2); ?></td>
                                <td class="text-right font-weight-bold <?php echo ($mr['operating_surplus'] >= 0) ? 'text-success' : 'text-danger'; ?>" style="background-color: #f8fafc;">
                                    <?php echo $currency . ' ' . number_format($mr['operating_surplus'], 2); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f1f5f9; font-weight: 700;">
                            <td>ANNUAL TOTAL:</td>
                            <td class="text-center">-</td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['discount'], 2); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></td>
                            <td class="text-right text-success"><?php echo $currency . ' ' . number_format($summary['patient_collections'], 2); ?></td>
                            <td class="text-right text-warning"><?php echo $currency . ' ' . number_format($summary['refunds'], 2); ?></td>
                            <td class="text-right text-success"><?php echo $currency . ' ' . number_format($summary['net_collections'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['operating_expenses'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['doctor_comm_paid'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['referral_comm_withdrawn'], 2); ?></td>
                            <td class="text-right text-primary"><?php echo $currency . ' ' . number_format($summary['cash_surplus'], 2); ?></td>
                        </tr>
                    </tfoot>
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
    $('#yearly-pnl-table').DataTable({
        responsive: true,
        paging: false,
        dom: "<'row'<'col-sm-6'B><'col-sm-6'f>>" +
             "<'row'<'col-sm-12'tr>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Yearly P&L Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Yearly P&L Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Yearly P&L Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Yearly P&L Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ]
    });
});
</script>
