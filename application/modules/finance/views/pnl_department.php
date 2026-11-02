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
                        <i class="fas fa-sitemap text-primary"></i> Department-Wise Profitability Report
                        <?php if ($is_accrual) { ?>
                            <span class="badge badge-accrual">Accrual Basis</span>
                        <?php } else { ?>
                            <span class="badge badge-cash">Cash Basis</span>
                        <?php } ?>
                    </h2>
                    <p>Departmental revenues, diagnostic volume, direct commissions, and net contribution margins &bull; <strong><?php echo $period['period_label']; ?></strong></p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div class="basis-toggle-group d-print-none">
                        <a href="finance/departmentProfitability?period_type=<?php echo $period_type; ?>&accounting_basis=cash" class="basis-toggle-btn <?php echo (!$is_accrual) ? 'active-cash' : ''; ?>">
                            <i class="fas fa-coins"></i> Cash Basis
                        </a>
                        <a href="finance/departmentProfitability?period_type=<?php echo $period_type; ?>&accounting_basis=accrual" class="basis-toggle-btn <?php echo ($is_accrual) ? 'active-accrual' : ''; ?>">
                            <i class="fas fa-balance-scale"></i> Accrual Basis
                        </a>
                    </div>
                    <div class="rep-actions d-print-none">
                        <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'pnl_department']); ?>
                    </div>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--pnl-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/departmentProfitability?period_type=today&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/departmentProfitability?period_type=yesterday&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/departmentProfitability?period_type=this_week&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/departmentProfitability?period_type=this_month&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/departmentProfitability?period_type=last_month&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/departmentProfitability?period_type=this_year&accounting_basis=<?php echo $accounting_basis; ?>" class="rep-pill <?php echo ($period_type === 'this_year') ? 'active' : ''; ?>">This Year</a>
            </div>

            <!-- Custom Filter Form -->
            <form action="finance/departmentProfitability" method="get" class="row g-3 align-items-end mt-2 d-print-none">
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
                    <a href="finance/departmentProfitability?accounting_basis=<?php echo $accounting_basis; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>

        <!-- KPI Grid -->
        <div class="rep-kpi-grid">
            <div class="rep-card">
                <div class="rep-card-icon bg-blue"><i class="fas fa-sitemap"></i></div>
                <div class="rep-card-label">Active Departments</div>
                <div class="rep-card-val"><?php echo number_format($summary['total_departments']); ?></div>
                <div class="rep-card-sub text-muted">Diagnostic service units</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-purple"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="rep-card-label">Gross Billing</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></div>
                <div class="rep-card-sub text-muted">Total list price volume</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-teal"><i class="fas fa-receipt"></i></div>
                <div class="rep-card-label">Net Billing</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></div>
                <div class="rep-card-sub text-muted">After discounts granted</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-orange"><i class="fas fa-user-md"></i></div>
                <div class="rep-card-label">Doctor Commission</div>
                <div class="rep-card-val text-danger"><?php echo $currency . ' ' . number_format($summary['doctor_commission'], 2); ?></div>
                <div class="rep-card-sub text-muted">Physician test shares</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-red"><i class="fas fa-user-friends"></i></div>
                <div class="rep-card-label">Referral Commission</div>
                <div class="rep-card-val text-danger"><?php echo $currency . ' ' . number_format($summary['referral_commission'], 2); ?></div>
                <div class="rep-card-sub text-muted">Referral promoter shares</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-green"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="rep-card-label">Total Contribution</div>
                <div class="rep-card-val text-success"><?php echo $currency . ' ' . number_format($summary['contribution_amount'], 2); ?></div>
                <div class="rep-card-sub text-success">Net revenue &minus; direct commissions</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-indigo"><i class="fas fa-percent"></i></div>
                <div class="rep-card-label">Overall Margin %</div>
                <div class="rep-card-val text-primary"><?php echo number_format($summary['overall_margin_pct'], 2); ?>%</div>
                <div class="rep-card-sub text-primary">Departmental profitability</div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="rep-table-card mt-4">
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--pnl-gray-800);">
                <i class="fas fa-list text-primary mr-1"></i> Department Performance &amp; Contribution Margin Analysis
            </h4>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle rep-table" id="dept-pnl-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th>Department Name</th>
                            <th class="text-center">Tests Conducted</th>
                            <th class="text-right">Gross Billing</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Net Billing</th>
                            <th class="text-right">Doctor Comm</th>
                            <th class="text-right">Referral Comm</th>
                            <th class="text-right">Contribution (৳)</th>
                            <th class="text-center">Margin %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        foreach ($departments as $dept) { 
                            $badge_class = ($dept['contribution_margin_pct'] >= 50) ? 'badge-success' : (($dept['contribution_margin_pct'] >= 30) ? 'badge-info' : 'badge-warning');
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td><strong><?php echo html_escape($dept['department']); ?></strong></td>
                                <td class="text-center"><?php echo number_format($dept['test_count']); ?></td>
                                <td class="text-right"><?php echo $currency . ' ' . number_format($dept['gross_billing'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($dept['discount'], 2); ?></td>
                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($dept['net_billing'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($dept['doctor_commission'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($dept['referral_commission'], 2); ?></td>
                                <td class="text-right font-weight-bold text-success" style="background-color: #f0fdf4;">
                                    <?php echo $currency . ' ' . number_format($dept['contribution_amount'], 2); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?php echo $badge_class; ?> px-2 py-1">
                                        <?php echo number_format($dept['contribution_margin_pct'], 1); ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f1f5f9; font-weight: 700;">
                            <td colspan="2" class="text-right">PORTFOLIO TOTAL:</td>
                            <td class="text-center">-</td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['discount'], 2); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['doctor_commission'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['referral_commission'], 2); ?></td>
                            <td class="text-right text-success"><?php echo $currency . ' ' . number_format($summary['contribution_amount'], 2); ?></td>
                            <td class="text-center"><span class="badge badge-dark"><?php echo number_format($summary['overall_margin_pct'], 1); ?>%</span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Unallocated Expenses Transparency Note -->
            <div class="alert alert-light border mt-3 mb-0 small text-muted">
                <i class="fas fa-info-circle text-info mr-1"></i>
                <strong>Accounting Transparency Note:</strong> Shared operating overheads of 
                <strong><?php echo $currency . ' ' . number_format($summary['unallocated_expenses'], 2); ?></strong> 
                (rent, utilities, administration, marketing) are kept unallocated to maintain exact contribution margin purity per department.
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
    $('#dept-pnl-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Department Profitability - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Department Profitability - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Department Profitability - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Department Profitability - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[8, "desc"]], // Highest contribution first
        pageLength: 25
    });
});
</script>
