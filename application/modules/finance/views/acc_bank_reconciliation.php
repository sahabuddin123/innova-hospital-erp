<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-check-double text-info mr-2"></i><?php echo lang('bank_reconciliation'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Bank Statement Audit & Book Balance Reconciliation</small>
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
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'bank_reconciliation']); ?>

            <!-- Filter Box -->
            <div class="acc-filter-box">
                <form method="get" action="finance/bankReconciliation" class="row align-items-center">
                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold">Statement Reconcile Date</label>
                        <input type="date" name="as_of_date" class="form-control form-control-sm" value="<?php echo $as_of_date; ?>">
                    </div>
                    <div class="col-md-3 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-info w-100 mt-4">Load Reconciliation</button>
                    </div>
                </form>
            </div>

            <!-- KPI Cards -->
            <div class="acc-kpi-grid">
                <div class="acc-kpi-card info">
                    <div class="acc-kpi-label">General Ledger Book Balance</div>
                    <div class="acc-kpi-val text-primary">৳ <?php echo number_format($report['book_balance'], 2); ?></div>
                    <div class="acc-kpi-sub">Hospital Ledger as of <?php echo date('d M Y', strtotime($as_of_date)); ?></div>
                </div>
                <div class="acc-kpi-card success">
                    <div class="acc-kpi-label">Bank Statement Balance</div>
                    <div class="acc-kpi-val text-success">৳ <?php echo number_format($report['statement_balance'], 2); ?></div>
                    <div class="acc-kpi-sub">Verified from Physical Statement</div>
                </div>
                <div class="acc-kpi-card <?php echo $report['is_reconciled'] ? 'success' : 'danger'; ?>">
                    <div class="acc-kpi-label">Reconciliation Difference</div>
                    <div class="acc-kpi-val <?php echo $report['is_reconciled'] ? 'text-success' : 'text-danger'; ?>">
                        ৳ <?php echo number_format($report['reconciliation_difference'], 2); ?>
                    </div>
                    <div class="acc-kpi-sub">
                        <?php echo $report['is_reconciled'] ? '<i class="fas fa-check-circle text-success"></i> Fully Reconciled' : '<i class="fas fa-times-circle text-danger"></i> Unreconciled Variance'; ?>
                    </div>
                </div>
                <div class="acc-kpi-card primary">
                    <div class="acc-kpi-label">Matched Transactions</div>
                    <div class="acc-kpi-val"><?php echo $report['matched_count']; ?></div>
                    <div class="acc-kpi-sub">Deposits & Withdrawals Audited</div>
                </div>
            </div>

            <!-- Reconciliation Form Card -->
            <div class="acc-card">
                <div class="acc-card-header bg-white">
                    <h5 class="acc-card-title"><i class="fas fa-file-contract text-muted"></i> Bank Statement Reconciliation Sheet</h5>
                </div>
                <div class="acc-card-body">
                    <form id="reconciliationForm" method="post" action="finance/saveBankReconciliationAction">
                        <input type="hidden" name="statement_date" value="<?php echo $as_of_date; ?>">
                        <input type="hidden" name="book_balance" value="<?php echo $report['book_balance']; ?>">

                        <div class="form-row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold small">Current Book Balance (from ERP)</label>
                                <input type="text" class="form-control form-control-sm font-weight-bold" value="৳ <?php echo number_format($report['book_balance'], 2); ?>" readonly>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold small">Bank Statement Balance (from Bank) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="statement_balance" id="inputStatementBal" class="form-control form-control-sm font-weight-bold" value="<?php echo $report['statement_balance']; ?>" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold small">Reconciliation Difference</label>
                                <input type="text" id="displayDiff" class="form-control form-control-sm font-weight-bold <?php echo $report['is_reconciled'] ? 'text-success' : 'text-danger'; ?>" value="৳ <?php echo number_format($report['reconciliation_difference'], 2); ?>" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold small">Reconciliation Audit Notes / Reference</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="3" placeholder="Enter cheque clearing notes, in-transit deposits, or bank fees..."><?php echo htmlspecialchars($report['last_reconciliation']['notes'] ?? ''); ?></textarea>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-sm btn-info shadow-sm">
                                <i class="fas fa-save mr-1"></i> Save Reconciliation Record
                            </button>
                        </div>
                    </form>
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
    var bookBal = <?php echo floatval($report['book_balance']); ?>;

    $('#inputStatementBal').on('input', function() {
        var stmtBal = parseFloat($(this).val()) || 0;
        var diff = Math.abs(bookBal - stmtBal);
        $('#displayDiff').val('৳ ' + diff.toFixed(2));
        if (diff < 0.009) {
            $('#displayDiff').removeClass('text-danger').addClass('text-success');
        } else {
            $('#displayDiff').removeClass('text-success').addClass('text-danger');
        }
    });

    $('#reconciliationForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp) {
                if (resp.status) {
                    alert('Bank reconciliation saved successfully.');
                    location.reload();
                } else {
                    alert(resp.message || 'Error saving reconciliation.');
                }
            },
            error: function() {
                alert('Server error occurred.');
            }
        });
    });
});
</script>
