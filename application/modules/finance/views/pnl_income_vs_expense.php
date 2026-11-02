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
                        <i class="fas fa-balance-scale text-primary"></i> Income vs Expense Report
                        <?php if ($is_accrual) { ?>
                            <span class="badge badge-accrual">Accrual Basis</span>
                        <?php } else { ?>
                            <span class="badge badge-cash">Cash / Collection Basis</span>
                        <?php } ?>
                    </h2>
                    <p>Comprehensive operating revenue inflows versus operational expenditure outflows &bull; <strong><?php echo $period['period_label']; ?></strong></p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div class="basis-toggle-group d-print-none">
                        <a href="finance/incomeVsExpenseReport?period_type=<?php echo $period_type; ?>&accounting_basis=cash" class="basis-toggle-btn <?php echo (!$is_accrual) ? 'active-cash' : ''; ?>">
                            <i class="fas fa-coins"></i> Cash Basis
                        </a>
                        <a href="finance/incomeVsExpenseReport?period_type=<?php echo $period_type; ?>&accounting_basis=accrual" class="basis-toggle-btn <?php echo ($is_accrual) ? 'active-accrual' : ''; ?>">
                            <i class="fas fa-balance-scale"></i> Accrual Basis
                        </a>
                    </div>
                    <div class="rep-actions d-print-none">
                        <button type="button" onclick="window.print()" class="rep-action-btn"><i class="fas fa-print"></i> Print</button>
                    </div>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--pnl-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/incomeVsExpenseReport?period_type=today&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/incomeVsExpenseReport?period_type=yesterday&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/incomeVsExpenseReport?period_type=this_week&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/incomeVsExpenseReport?period_type=this_month&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/incomeVsExpenseReport?period_type=last_month&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/incomeVsExpenseReport?period_type=this_year&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_year') ? 'active' : ''; ?>">This Year</a>
            </div>

            <!-- Custom Filter Form -->
            <form action="finance/incomeVsExpenseReport" method="get" class="row g-3 align-items-end mt-2 d-print-none">
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
                    <a href="finance/incomeVsExpenseReport?accounting_basis=<?php echo $accounting_basis; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>

        <!-- 3 Primary Statement Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-4 h-100 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="small text-uppercase font-weight-bold text-success mb-1">
                        <i class="fas fa-arrow-down mr-1"></i> Net Operating Inflows
                    </div>
                    <div class="h2 font-weight-bold text-success mb-2"><?php echo $currency . ' ' . number_format($summary['net_collections'], 2); ?></div>
                    <div class="small text-muted">
                        Gross Collections: <strong><?php echo $currency . ' ' . number_format($summary['patient_collections'], 2); ?></strong><br>
                        Less Refunds: <strong><?php echo $currency . ' ' . number_format($summary['refunds'], 2); ?></strong>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-4 h-100 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="small text-uppercase font-weight-bold text-danger mb-1">
                        <i class="fas fa-arrow-up mr-1"></i> Total Operating Outflows
                    </div>
                    <div class="h2 font-weight-bold text-danger mb-2"><?php echo $currency . ' ' . number_format($summary['total_cash_outflows'], 2); ?></div>
                    <div class="small text-muted">
                        Expenses: <strong><?php echo $currency . ' ' . number_format($summary['operating_expenses_paid'], 2); ?></strong> | 
                        Doc Comm: <strong><?php echo $currency . ' ' . number_format($summary['doctor_comm_paid'], 2); ?></strong><br>
                        Ref Withdrawn: <strong><?php echo $currency . ' ' . number_format($summary['referral_comm_withdrawn'], 2); ?></strong>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-4 h-100 text-white" style="border-radius: var(--pnl-radius); background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <div class="small text-uppercase font-weight-bold text-warning mb-1">
                        <i class="fas fa-award mr-1"></i> Net Operating Surplus
                    </div>
                    <div class="h2 font-weight-bold mb-2" style="color: <?php echo ($summary['net_cash_operating_surplus'] >= 0) ? '#34d399' : '#f87171'; ?>;">
                        <?php echo $currency . ' ' . number_format($summary['net_cash_operating_surplus'], 2); ?>
                    </div>
                    <div class="small text-light" style="opacity: 0.9;">
                        Operating Ratio: <strong><?php echo ($summary['net_collections'] > 0) ? number_format(($summary['net_cash_operating_surplus'] / $summary['net_collections']) * 100, 2) : 0; ?>%</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Chart Card -->
        <div class="chart-card mb-4">
            <h4><i class="fas fa-chart-line text-primary mr-1"></i> Inflows vs Outflows Performance Trend</h4>
            <div style="position: relative; height: 320px;">
                <canvas id="chart-income-vs-expense"></canvas>
            </div>
        </div>

        <!-- Detailed Expense Disbursements Table -->
        <div class="rep-table-card mt-4">
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--pnl-gray-800);">
                <i class="fas fa-receipt text-primary mr-1"></i> Operating Expense Disbursements Ledger
            </h4>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle rep-table" id="inc-exp-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Classification</th>
                            <th>Payee / Description</th>
                            <th>Method</th>
                            <th class="text-right">Disbursed (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        foreach ($expenses as $ex) { ?>
                            <tr>
                                <td class="text-center font-weight-bold">#<?php echo $ex->id; ?></td>
                                <td><?php echo date('d/m/Y', $ex->date); ?></td>
                                <td><strong><?php echo html_escape($ex->category_name); ?></strong></td>
                                <td><span class="badge badge-light border"><?php echo html_escape($ex->classification); ?></span></td>
                                <td><?php echo html_escape($ex->pay_to . ' ' . $ex->note); ?></td>
                                <td><span class="badge badge-info"><?php echo html_escape($ex->payment_method); ?></span></td>
                                <td class="text-right font-weight-bold text-danger"><?php echo $currency . ' ' . number_format($ex->amount, 2); ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    var ctx = document.getElementById('chart-income-vs-expense').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($charts['months']); ?>,
            datasets: [
                {
                    label: 'Net Collections',
                    data: <?php echo json_encode($charts['trend_collections']); ?>,
                    backgroundColor: '#059669',
                    borderRadius: 4
                },
                {
                    label: 'Total Expenses',
                    data: <?php echo json_encode($charts['trend_expenses']); ?>,
                    backgroundColor: '#dc2626',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '৳' + value.toLocaleString(); }
                    }
                }
            }
        }
    });

    $('#inc-exp-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Income vs Expense - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Income vs Expense - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Income vs Expense - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Income vs Expense - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        pageLength: 25
    });
});
</script>
