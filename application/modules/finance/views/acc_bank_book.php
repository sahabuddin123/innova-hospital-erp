<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-university text-info mr-2"></i><?php echo lang('bank_book'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Bank Accounts & Digital Payment Gateways Ledger</small>
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
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'bank_book']); ?>

            <!-- Filter Box -->
            <div class="acc-filter-box">
                <form method="get" action="finance/bankBook" class="row align-items-center">
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">From Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">To Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-info w-100 mr-2 mt-4">Load Bank Book</button>
                        <a href="finance/bankBook" class="btn btn-sm btn-outline-secondary mt-4">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Notice -->
            <div class="acc-alert-notice">
                <i class="fas fa-university mr-1 text-info"></i>
                <strong>Bank & Gateway Segregation Audit:</strong>
                Includes verified Card, Bank Transfer, bKash, and Nagad digital merchant collections alongside bank-disbursed expense vouchers. Physical counter cash is reported separately in the Cash Book.
            </div>

            <!-- KPI Cards -->
            <div class="acc-kpi-grid">
                <div class="acc-kpi-card info">
                    <div class="acc-kpi-label">Opening Bank Balance</div>
                    <div class="acc-kpi-val text-primary">৳ <?php echo number_format($report['opening_balance'], 2); ?></div>
                    <div class="acc-kpi-sub">Book Balance on <?php echo date('d M Y', strtotime($start_date)); ?></div>
                </div>
                <div class="acc-kpi-card success">
                    <div class="acc-kpi-label">Total Bank Deposits</div>
                    <div class="acc-kpi-val text-success">৳ <?php echo number_format($report['total_deposits'], 2); ?></div>
                    <div class="acc-kpi-sub">Card / Gateway Collections</div>
                </div>
                <div class="acc-kpi-card danger">
                    <div class="acc-kpi-label">Total Withdrawals</div>
                    <div class="acc-kpi-val text-danger">৳ <?php echo number_format($report['total_withdrawals'], 2); ?></div>
                    <div class="acc-kpi-sub">Cheque / Transfer Disbursements</div>
                </div>
                <div class="acc-kpi-card primary">
                    <div class="acc-kpi-label">Closing Bank Balance</div>
                    <div class="acc-kpi-val font-weight-bold">৳ <?php echo number_format($report['closing_balance'], 2); ?></div>
                    <div class="acc-kpi-sub">Book Balance on <?php echo date('d M Y', strtotime($end_date)); ?></div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="acc-card">
                <div class="acc-card-header">
                    <h5 class="acc-card-title"><i class="fas fa-list text-muted"></i> Chronological Bank & Gateway Activity</h5>
                    <span class="badge badge-info"><?php echo count($report['transactions']); ?> Records Found</span>
                </div>
                <div class="acc-card-body p-0">
                    <div class="table-responsive">
                        <table class="table acc-table mb-0 datatable-export">
                            <thead>
                                <tr>
                                    <th style="width: 110px;">Date</th>
                                    <th style="width: 140px;">Bank / Gateway</th>
                                    <th style="width: 110px;">Ref #</th>
                                    <th>Description / Transaction Narration</th>
                                    <th class="text-right" style="width: 140px;">Deposit (৳)</th>
                                    <th class="text-right" style="width: 140px;">Withdrawal (৳)</th>
                                    <th class="text-right" style="width: 160px;">Running Balance (৳)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-light font-weight-bold">
                                    <td><?php echo date('d-m-Y', strtotime($start_date)); ?></td>
                                    <td>ALL BANKS</td>
                                    <td>OPEN-BAL</td>
                                    <td><i class="fas fa-history mr-1"></i> Opening Bank Book Balance Brought Forward</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right acc-bal font-weight-bold">৳ <?php echo number_format($report['opening_balance'], 2); ?></td>
                                </tr>

                                <?php if (!empty($report['transactions'])): ?>
                                    <?php foreach ($report['transactions'] as $tx): ?>
                                        <tr>
                                            <td><?php echo date('d-m-Y', intval($tx['tx_date'])); ?></td>
                                            <td><span class="badge badge-light border"><?php echo htmlspecialchars($tx['bank_name'] ?: 'Digital Gateway'); ?></span></td>
                                            <td><span class="font-weight-bold text-dark"><?php echo htmlspecialchars($tx['reference']); ?></span></td>
                                            <td><?php echo htmlspecialchars($tx['description']); ?></td>
                                            <td class="acc-amount acc-cr font-weight-bold">
                                                <?php echo $tx['deposit'] > 0 ? '৳ ' . number_format($tx['deposit'], 2) : '-'; ?>
                                            </td>
                                            <td class="acc-amount text-danger font-weight-bold">
                                                <?php echo $tx['withdrawal'] > 0 ? '৳ ' . number_format($tx['withdrawal'], 2) : '-'; ?>
                                            </td>
                                            <td class="acc-amount acc-bal font-weight-bold">
                                                ৳ <?php echo number_format($tx['running_balance'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No bank or digital gateway movements recorded during this period.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold border-top" style="font-size: 14px;">
                                    <td colspan="4" class="text-right text-uppercase">Period Total Bank Flows:</td>
                                    <td class="acc-amount acc-cr text-right">৳ <?php echo number_format($report['total_deposits'], 2); ?></td>
                                    <td class="acc-amount text-danger text-right">৳ <?php echo number_format($report['total_withdrawals'], 2); ?></td>
                                    <td class="acc-amount text-primary text-right font-weight-bold">৳ <?php echo number_format($report['closing_balance'], 2); ?></td>
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
        ordering: false,
        pageLength: 50,
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
        buttons: [
            { extend: 'copy', className: 'btn btn-xs btn-outline-secondary' },
            { extend: 'excel', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Bank_Book' },
            { extend: 'csv', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Bank_Book' },
            { extend: 'pdf', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Bank_Book' },
            { extend: 'print', className: 'btn btn-xs btn-outline-secondary' }
        ]
    });
});
</script>
