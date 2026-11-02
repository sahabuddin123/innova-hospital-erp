<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-history text-info mr-2"></i><?php echo lang('accounting_audit_trail'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Immutable Double-Entry Accounting Audit Log</small>
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
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'audit_trail']); ?>

            <!-- Filter Box -->
            <div class="acc-filter-box">
                <form method="get" action="finance/accountingAuditTrail" class="row align-items-center">
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">Filter Action</label>
                        <select name="action" class="form-control form-control-sm">
                            <option value="">-- All Actions --</option>
                            <option value="POST_JOURNAL" <?php echo ($filters['action'] ?? '') === 'POST_JOURNAL' ? 'selected' : ''; ?>>POST_JOURNAL</option>
                            <option value="REVERSE_JOURNAL" <?php echo ($filters['action'] ?? '') === 'REVERSE_JOURNAL' ? 'selected' : ''; ?>>REVERSE_JOURNAL</option>
                            <option value="CREATE_ACCOUNT" <?php echo ($filters['action'] ?? '') === 'CREATE_ACCOUNT' ? 'selected' : ''; ?>>CREATE_ACCOUNT</option>
                            <option value="UPDATE_ACCOUNT" <?php echo ($filters['action'] ?? '') === 'UPDATE_ACCOUNT' ? 'selected' : ''; ?>>UPDATE_ACCOUNT</option>
                            <option value="SAVE_OPENING_BALANCES" <?php echo ($filters['action'] ?? '') === 'SAVE_OPENING_BALANCES' ? 'selected' : ''; ?>>SAVE_OPENING_BALANCES</option>
                            <option value="CLOSE_PERIOD" <?php echo ($filters['action'] ?? '') === 'CLOSE_PERIOD' ? 'selected' : ''; ?>>CLOSE_PERIOD</option>
                            <option value="REOPEN_PERIOD" <?php echo ($filters['action'] ?? '') === 'REOPEN_PERIOD' ? 'selected' : ''; ?>>REOPEN_PERIOD</option>
                            <option value="BANK_RECONCILIATION" <?php echo ($filters['action'] ?? '') === 'BANK_RECONCILIATION' ? 'selected' : ''; ?>>BANK_RECONCILIATION</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">Filter Module</label>
                        <select name="module" class="form-control form-control-sm">
                            <option value="">-- All Modules --</option>
                            <option value="JournalEntry" <?php echo ($filters['module'] ?? '') === 'JournalEntry' ? 'selected' : ''; ?>>JournalEntry</option>
                            <option value="ChartOfAccounts" <?php echo ($filters['module'] ?? '') === 'ChartOfAccounts' ? 'selected' : ''; ?>>ChartOfAccounts</option>
                            <option value="OpeningBalances" <?php echo ($filters['module'] ?? '') === 'OpeningBalances' ? 'selected' : ''; ?>>OpeningBalances</option>
                            <option value="PeriodClosing" <?php echo ($filters['module'] ?? '') === 'PeriodClosing' ? 'selected' : ''; ?>>PeriodClosing</option>
                            <option value="BankReconciliation" <?php echo ($filters['module'] ?? '') === 'BankReconciliation' ? 'selected' : ''; ?>>BankReconciliation</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-info w-100 mr-2 mt-4">Filter Log</button>
                        <a href="finance/accountingAuditTrail" class="btn btn-sm btn-outline-secondary mt-4">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="acc-card">
                <div class="acc-card-header">
                    <h5 class="acc-card-title"><i class="fas fa-list text-muted"></i> Chronological Accounting Audit Trail Log</h5>
                    <span class="badge badge-info"><?php echo count($logs); ?> Events Recorded</span>
                </div>
                <div class="acc-card-body p-0">
                    <div class="table-responsive">
                        <table class="table acc-table mb-0 datatable-export">
                            <thead>
                                <tr>
                                    <th style="width: 140px;">Timestamp</th>
                                    <th style="width: 110px;">User</th>
                                    <th style="width: 130px;">Action</th>
                                    <th style="width: 120px;">Module</th>
                                    <th style="width: 130px;">Reference</th>
                                    <th>Audit Details / Payload / Reason</th>
                                    <th style="width: 100px;">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logs)): ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?php echo date('d-m-Y H:i:s', $log['date_time']); ?></td>
                                            <td>
                                                <span class="font-weight-bold text-dark"><?php echo htmlspecialchars($log['username'] ?: 'User #' . $log['user_id']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?php echo htmlspecialchars($log['action']); ?></span>
                                            </td>
                                            <td><span class="badge badge-light border"><?php echo htmlspecialchars($log['module']); ?></span></td>
                                            <td><span class="font-weight-bold text-dark"><?php echo htmlspecialchars($log['journal_reference'] ?: '-'); ?></span></td>
                                            <td>
                                                <?php if (!empty($log['reason'])): ?>
                                                    <div class="text-danger font-weight-bold mb-1">Reason: <?php echo htmlspecialchars($log['reason']); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($log['new_value'])): ?>
                                                    <pre class="bg-light p-1 mb-0 border rounded text-muted" style="font-size: 11px; max-height: 80px; overflow-y: auto;"><?php echo htmlspecialchars($log['new_value']); ?></pre>
                                                <?php endif; ?>
                                            </td>
                                            <td><small class="text-muted"><?php echo htmlspecialchars($log['ip_address'] ?: '-'); ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No audit events recorded matching criteria.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
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
        pageLength: 25,
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
        buttons: [
            { extend: 'copy', className: 'btn btn-xs btn-outline-secondary' },
            { extend: 'excel', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Accounting_Audit_Trail' },
            { extend: 'csv', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Accounting_Audit_Trail' },
            { extend: 'pdf', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Accounting_Audit_Trail' },
            { extend: 'print', className: 'btn btn-xs btn-outline-secondary' }
        ]
    });
});
</script>
