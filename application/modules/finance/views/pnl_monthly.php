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
                        <i class="fas fa-calendar-alt text-primary"></i> Monthly Profit &amp; Loss Report
                        <?php if ($is_accrual) { ?>
                            <span class="badge badge-accrual">Accrual Basis</span>
                        <?php } else { ?>
                            <span class="badge badge-cash">Cash / Collection Basis</span>
                        <?php } ?>
                    </h2>
                    <p>Calendar day matrix, Month-over-Month (MoM) comparison, and Year-to-Date (YTD) position &bull; <strong><?php echo $target_month_label; ?></strong></p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div class="basis-toggle-group d-print-none">
                        <a href="finance/monthlyProfitLoss?month=<?php echo $target_month; ?>&accounting_basis=cash" class="basis-toggle-btn <?php echo (!$is_accrual) ? 'active-cash' : ''; ?>">
                            <i class="fas fa-coins"></i> Cash Basis
                        </a>
                        <a href="finance/monthlyProfitLoss?month=<?php echo $target_month; ?>&accounting_basis=accrual" class="basis-toggle-btn <?php echo ($is_accrual) ? 'active-accrual' : ''; ?>">
                            <i class="fas fa-balance-scale"></i> Accrual Basis
                        </a>
                    </div>
                    <div class="rep-actions d-print-none">
                        <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'pnl_monthly']); ?>
                    </div>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--pnl-gray-200);">

            <!-- Month Selector Form -->
            <form action="finance/monthlyProfitLoss" method="get" class="row g-3 align-items-end mt-2 d-print-none">
                <input type="hidden" name="accounting_basis" value="<?php echo $accounting_basis; ?>">
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">Select Month</label>
                    <input type="month" name="month" class="form-control form-control-sm" value="<?php echo html_escape($target_month); ?>">
                </div>
                <div class="col-md-4 d-flex" style="gap: 8px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-search"></i> Generate Report</button>
                    <a href="finance/monthlyProfitLoss?month=<?php echo date('Y-m'); ?>&accounting_basis=<?php echo $accounting_basis; ?>" class="btn btn-sm btn-outline-secondary">This Month</a>
                    <a href="finance/monthlyProfitLoss?month=<?php echo date('Y-m', strtotime('first day of last month')); ?>&accounting_basis=<?php echo $accounting_basis; ?>" class="btn btn-sm btn-outline-secondary">Last Month</a>
                </div>
            </form>
        </div>

        <!-- Comparative Intelligence Cards (MoM and YTD) -->
        <div class="row g-3 mb-4">
            <!-- MoM Card -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 p-3 h-100" style="border-radius: var(--pnl-radius); background: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-uppercase font-weight-bold text-muted"><i class="fas fa-exchange-alt mr-1"></i> Month-over-Month (MoM) Comparison</span>
                        <span class="badge <?php echo ($prev_month['growth_pct'] >= 0) ? 'badge-success' : 'badge-danger'; ?> px-2 py-1">
                            <?php echo ($prev_month['growth_pct'] >= 0) ? '+' : ''; ?><?php echo number_format($prev_month['growth_pct'], 2); ?>%
                        </span>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="small text-muted"><?php echo $target_month_label; ?> Surplus</div>
                            <div class="h4 font-weight-bold mb-0 text-dark"><?php echo $currency . ' ' . number_format($summary['cash_surplus'], 2); ?></div>
                        </div>
                        <div class="col-6 border-left">
                            <div class="small text-muted"><?php echo $prev_month['label']; ?> Surplus</div>
                            <div class="h4 font-weight-bold mb-0 text-muted"><?php echo $currency . ' ' . number_format($prev_month['summary']['cash_surplus'], 2); ?></div>
                        </div>
                    </div>
                    <div class="small text-muted mt-2">
                        Surplus Variance: <strong><?php echo ($prev_month['variance_surplus'] >= 0) ? '+' : ''; ?><?php echo $currency . ' ' . number_format($prev_month['variance_surplus'], 2); ?></strong>
                    </div>
                </div>
            </div>

            <!-- YTD Summary Card -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 p-3 h-100" style="border-radius: var(--pnl-radius); background: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-uppercase font-weight-bold text-muted"><i class="fas fa-layer-group mr-1"></i> Year-to-Date (YTD) Cumulative Position</span>
                        <span class="badge badge-info px-2 py-1">Jan &minus; <?php echo date('M Y', strtotime($target_month . '-01')); ?></span>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="small text-muted">Cumulative Net Collections</div>
                            <div class="h4 font-weight-bold mb-0 text-success"><?php echo $currency . ' ' . number_format($ytd_summary['net_collections'], 2); ?></div>
                        </div>
                        <div class="col-6 border-left">
                            <div class="small text-muted">Cumulative Cash Surplus</div>
                            <div class="h4 font-weight-bold mb-0 text-primary"><?php echo $currency . ' ' . number_format($ytd_summary['cash_surplus'], 2); ?></div>
                        </div>
                    </div>
                    <div class="small text-muted mt-2">
                        Total Outflows YTD: <strong><?php echo $currency . ' ' . number_format($ytd_summary['total_expenses'], 2); ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Breakdown Table Card -->
        <div class="rep-table-card mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h4 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--pnl-gray-800);">
                    <i class="fas fa-table text-primary mr-1"></i> Day-by-Day Monthly Performance Ledger
                </h4>
                <div class="d-print-none small text-muted">
                    Showing daily breakdown for <strong><?php echo $target_month_label; ?></strong>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle rep-table" id="monthly-pnl-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Date / Day</th>
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
                        <?php foreach ($daily_rows as $row) { ?>
                            <tr>
                                <td><strong><?php echo $row['day_label']; ?></strong></td>
                                <td class="text-center"><?php echo $row['invoices']; ?></td>
                                <td class="text-right"><?php echo $currency . ' ' . number_format($row['gross_billing'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($row['discount'], 2); ?></td>
                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($row['net_billing'], 2); ?></td>
                                <td class="text-right text-success"><?php echo $currency . ' ' . number_format($row['patient_collections'], 2); ?></td>
                                <td class="text-right text-warning"><?php echo $currency . ' ' . number_format($row['refunds'], 2); ?></td>
                                <td class="text-right font-weight-bold text-success"><?php echo $currency . ' ' . number_format($row['net_collections'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($row['operating_expenses'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($row['doctor_comm'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($row['referral_comm'], 2); ?></td>
                                <td class="text-right font-weight-bold <?php echo ($row['operating_surplus'] >= 0) ? 'text-success' : 'text-danger'; ?>" style="background-color: #f8fafc;">
                                    <?php echo $currency . ' ' . number_format($row['operating_surplus'], 2); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f1f5f9; font-weight: 700;">
                            <td>MONTH SUMMARY:</td>
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
    $('#monthly-pnl-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Monthly P&L Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Monthly P&L Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Monthly P&L Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Monthly P&L Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        pageLength: 35
    });
});
</script>
