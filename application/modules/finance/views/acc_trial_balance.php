<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-balance-scale-left text-info mr-2"></i><?php echo lang('trial_balance'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Verified Double-Entry Trial Balance</small>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary shadow-sm" onclick="window.print();">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <div class="container-fluid">
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'trial_balance']); ?>

            <!-- Filter Box -->
            <div class="acc-filter-box">
                <form method="get" action="finance/trialBalance" class="row align-items-center">
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">As-of Date</label>
                        <input type="date" name="as_of_date" class="form-control form-control-sm" value="<?php echo $as_of_date; ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">Fiscal Period Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-info w-100 mr-2 mt-4">Compute Trial Balance</button>
                        <a href="finance/trialBalance" class="btn btn-sm btn-outline-secondary mt-4">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Mandatory Audit Alert Notice -->
            <?php if ($report['unposted_invoices'] > 0): ?>
                <div class="acc-alert-notice">
                    <i class="fas fa-exclamation-triangle mr-1 text-warning"></i>
                    <strong>Accounting Foundation Audit Notice:</strong>
                    This Trial Balance reflects strictly <em>posted double-entry journal vouchers</em> from the General Ledger. There are currently <strong><?php echo $report['unposted_invoices']; ?> operational billing transactions</strong> in the patient billing sub-ledger that have not been posted to the General Ledger. In accordance with Prompt Rules #3, #7, and #21, no artificial balancing entries are fabricated.
                </div>
            <?php endif; ?>

            <!-- KPI Balance Cards -->
            <div class="acc-kpi-grid">
                <div class="acc-kpi-card info">
                    <div class="acc-kpi-label">Total Closing Debit</div>
                    <div class="acc-kpi-val text-primary">৳ <?php echo number_format($report['total_close_dr'], 2); ?></div>
                    <div class="acc-kpi-sub">Sum of All Debit Balances</div>
                </div>
                <div class="acc-kpi-card success">
                    <div class="acc-kpi-label">Total Closing Credit</div>
                    <div class="acc-kpi-val text-success">৳ <?php echo number_format($report['total_close_cr'], 2); ?></div>
                    <div class="acc-kpi-sub">Sum of All Credit Balances</div>
                </div>
                <div class="acc-kpi-card <?php echo $report['is_balanced'] ? 'success' : 'danger'; ?>">
                    <div class="acc-kpi-label">Debit - Credit Difference</div>
                    <div class="acc-kpi-val <?php echo $report['is_balanced'] ? 'text-success' : 'text-danger'; ?>">
                        ৳ <?php echo number_format($report['closing_difference'], 2); ?>
                    </div>
                    <div class="acc-kpi-sub">
                        <?php echo $report['is_balanced'] ? '<i class="fas fa-check-circle text-success"></i> Perfectly Balanced' : '<i class="fas fa-times-circle text-danger"></i> Out of Balance'; ?>
                    </div>
                </div>
                <div class="acc-kpi-card primary">
                    <div class="acc-kpi-label">Accounts Evaluated</div>
                    <div class="acc-kpi-val"><?php echo count($report['rows']); ?></div>
                    <div class="acc-kpi-sub">Active Chart of Accounts</div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="acc-card">
                <div class="acc-card-header">
                    <h5 class="acc-card-title"><i class="fas fa-table text-muted"></i> Trial Balance Worksheet as of <?php echo date('d F Y', strtotime($as_of_date)); ?></h5>
                    <span class="badge <?php echo $report['is_balanced'] ? 'badge-success' : 'badge-danger'; ?> p-2">
                        <?php echo $report['is_balanced'] ? 'VERIFIED EQUALITY: TOTAL DR = TOTAL CR' : 'VARIANCE DETECTED'; ?>
                    </span>
                </div>
                <div class="acc-card-body p-0">
                    <div class="table-responsive">
                        <table class="table acc-table mb-0 datatable-export">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 100px;">Code</th>
                                    <th rowspan="2">Account Title</th>
                                    <th rowspan="2" style="width: 90px;">Type</th>
                                    <th colspan="2" class="text-center bg-light border-bottom">Opening Balance</th>
                                    <th colspan="2" class="text-center bg-light border-bottom">Period Activity</th>
                                    <th colspan="2" class="text-center bg-light border-bottom">Closing Balance</th>
                                </tr>
                                <tr>
                                    <th class="text-right" style="width: 110px;">Debit (Dr) ৳</th>
                                    <th class="text-right" style="width: 110px;">Credit (Cr) ৳</th>
                                    <th class="text-right" style="width: 110px;">Debit (Dr) ৳</th>
                                    <th class="text-right" style="width: 110px;">Credit (Cr) ৳</th>
                                    <th class="text-right" style="width: 120px;">Debit (Dr) ৳</th>
                                    <th class="text-right" style="width: 120px;">Credit (Cr) ৳</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report['rows'] as $r): ?>
                                    <tr>
                                        <td><span class="font-weight-bold text-dark"><?php echo $r['account_code']; ?></span></td>
                                        <td>
                                            <div class="font-weight-bold"><?php echo htmlspecialchars($r['account_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($r['group_name']); ?></small>
                                        </td>
                                        <td><span class="badge badge-light border"><?php echo ucfirst($r['account_type']); ?></span></td>
                                        <td class="acc-amount acc-dr"><?php echo $r['opening_dr'] > 0 ? number_format($r['opening_dr'], 2) : '-'; ?></td>
                                        <td class="acc-amount acc-cr"><?php echo $r['opening_cr'] > 0 ? number_format($r['opening_cr'], 2) : '-'; ?></td>
                                        <td class="acc-amount acc-dr"><?php echo $r['period_dr'] > 0 ? number_format($r['period_dr'], 2) : '-'; ?></td>
                                        <td class="acc-amount acc-cr"><?php echo $r['period_cr'] > 0 ? number_format($r['period_cr'], 2) : '-'; ?></td>
                                        <td class="acc-amount acc-dr font-weight-bold"><?php echo $r['closing_dr'] > 0 ? number_format($r['closing_dr'], 2) : '-'; ?></td>
                                        <td class="acc-amount acc-cr font-weight-bold"><?php echo $r['closing_cr'] > 0 ? number_format($r['closing_cr'], 2) : '-'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold border-top" style="font-size: 14px;">
                                    <td colspan="3" class="text-right text-uppercase">Total Trial Balance:</td>
                                    <td class="acc-amount acc-dr text-right">৳ <?php echo number_format($report['total_open_dr'], 2); ?></td>
                                    <td class="acc-amount acc-cr text-right">৳ <?php echo number_format($report['total_open_cr'], 2); ?></td>
                                    <td class="acc-amount acc-dr text-right">৳ <?php echo number_format($report['total_period_dr'], 2); ?></td>
                                    <td class="acc-amount acc-cr text-right">৳ <?php echo number_format($report['total_period_cr'], 2); ?></td>
                                    <td class="acc-amount acc-dr text-right text-primary">৳ <?php echo number_format($report['total_close_dr'], 2); ?></td>
                                    <td class="acc-amount acc-cr text-right text-success">৳ <?php echo number_format($report['total_close_cr'], 2); ?></td>
                                </tr>
                                <tr class="<?php echo $report['is_balanced'] ? 'table-success' : 'table-danger'; ?> font-weight-bold">
                                    <td colspan="7" class="text-right text-uppercase">Trial Balance Status:</td>
                                    <td colspan="2" class="text-center">
                                        <?php if ($report['is_balanced']): ?>
                                            <i class="fas fa-check-circle text-success mr-1"></i> BALANCED (0.00 Variance)
                                        <?php else: ?>
                                            <i class="fas fa-times-circle text-danger mr-1"></i> OUT OF BALANCE (৳ <?php echo number_format($report['closing_difference'], 2); ?> Variance)
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Signatures for print -->
            <div class="acc-signatures-block">
                <div class="acc-sig-grid">
                    <div class="acc-sig-line">Prepared By<br><small>Senior Accountant</small></div>
                    <div class="acc-sig-line">Checked & Verified By<br><small>Head of Accounts & Finance</small></div>
                    <div class="acc-sig-line">Approved By<br><small>Managing Director / CFO</small></div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('.datatable-export')) {
        $('.datatable-export').DataTable().destroy();
    }
    $('.datatable-export').DataTable({
        responsive: true,
        pageLength: 50,
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
        buttons: [
            { extend: 'copy', className: 'btn btn-xs btn-outline-secondary' },
            { extend: 'excel', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Trial_Balance' },
            { extend: 'csv', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Trial_Balance' },
            { extend: 'pdf', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Trial_Balance' },
            { extend: 'print', className: 'btn btn-xs btn-outline-secondary' }
        ]
    });
});
</script>
