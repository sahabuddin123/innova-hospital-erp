<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-sitemap text-info mr-2"></i><?php echo lang('chart_of_accounts'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Healthcare Standard Chart of Accounts</small>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#addAccountModal">
                        <i class="fas fa-plus-circle mr-1"></i> Add Account
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary ml-2 shadow-sm" onclick="window.print();">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <div class="container-fluid">
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'chart_of_accounts']); ?>

            <!-- KPI Cards -->
            <div class="acc-kpi-grid">
                <?php
                $total_accs = count($accounts);
                $asset_cnt = 0; $liab_cnt = 0; $eq_cnt = 0; $inc_cnt = 0; $exp_cnt = 0;
                foreach ($accounts as $a) {
                    if ($a['account_type'] === 'asset') $asset_cnt++;
                    elseif ($a['account_type'] === 'liability') $liab_cnt++;
                    elseif ($a['account_type'] === 'equity') $eq_cnt++;
                    elseif ($a['account_type'] === 'income') $inc_cnt++;
                    elseif ($a['account_type'] === 'expense') $exp_cnt++;
                }
                ?>
                <div class="acc-kpi-card primary">
                    <div class="acc-kpi-label">Total Accounts</div>
                    <div class="acc-kpi-val"><?php echo $total_accs; ?></div>
                    <div class="acc-kpi-sub">Across 5 Master Classes</div>
                </div>
                <div class="acc-kpi-card info">
                    <div class="acc-kpi-label">Assets (1000s)</div>
                    <div class="acc-kpi-val"><?php echo $asset_cnt; ?></div>
                    <div class="acc-kpi-sub">Cash, Bank, AR, Inventory, Equipment</div>
                </div>
                <div class="acc-kpi-card warning">
                    <div class="acc-kpi-label">Liabilities (2000s)</div>
                    <div class="acc-kpi-val"><?php echo $liab_cnt; ?></div>
                    <div class="acc-kpi-sub">Payables, Doctor & Referral Accruals</div>
                </div>
                <div class="acc-kpi-card purple">
                    <div class="acc-kpi-label">Equity (3000s)</div>
                    <div class="acc-kpi-val"><?php echo $eq_cnt; ?></div>
                    <div class="acc-kpi-sub">Capital & Retained Earnings</div>
                </div>
                <div class="acc-kpi-card success">
                    <div class="acc-kpi-label">Income (4000s)</div>
                    <div class="acc-kpi-val"><?php echo $inc_cnt; ?></div>
                    <div class="acc-kpi-sub">Diagnostic Pathology, Radiology, Cardiology</div>
                </div>
                <div class="acc-kpi-card danger">
                    <div class="acc-kpi-label">Expenses (5000s)</div>
                    <div class="acc-kpi-val"><?php echo $exp_cnt; ?></div>
                    <div class="acc-kpi-sub">Reagents, Salaries, Rent, Utilities, Fees</div>
                </div>
            </div>

            <!-- Filter Box -->
            <div class="acc-filter-box">
                <form method="get" action="finance/chartOfAccounts" class="row align-items-center">
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold">Account Type</label>
                        <select name="type" class="form-control form-control-sm">
                            <option value="">-- All Master Types --</option>
                            <option value="asset" <?php echo ($filters['account_type'] ?? '') === 'asset' ? 'selected' : ''; ?>>Assets</option>
                            <option value="liability" <?php echo ($filters['account_type'] ?? '') === 'liability' ? 'selected' : ''; ?>>Liabilities</option>
                            <option value="equity" <?php echo ($filters['account_type'] ?? '') === 'equity' ? 'selected' : ''; ?>>Equity</option>
                            <option value="income" <?php echo ($filters['account_type'] ?? '') === 'income' ? 'selected' : ''; ?>>Income / Revenue</option>
                            <option value="expense" <?php echo ($filters['account_type'] ?? '') === 'expense' ? 'selected' : ''; ?>>Expenses</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">Account Group</label>
                        <select name="group_id" class="form-control form-control-sm">
                            <option value="">-- All Groups --</option>
                            <?php foreach ($groups as $g): ?>
                                <option value="<?php echo $g['id']; ?>" <?php echo ($filters['group_id'] ?? '') == $g['id'] ? 'selected' : ''; ?>>
                                    [<?php echo $g['group_code']; ?>] <?php echo $g['name']; ?> (<?php echo ucfirst($g['category']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold">Search Code or Name</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="e.g. 1010 or Cash" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-primary w-100 mr-1 mt-4">Filter</button>
                        <a href="finance/chartOfAccounts" class="btn btn-sm btn-outline-secondary mt-4">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="acc-card">
                <div class="acc-card-header">
                    <h5 class="acc-card-title"><i class="fas fa-list text-muted"></i> Chart of Accounts Master Register</h5>
                    <span class="badge badge-info"><?php echo count($accounts); ?> Accounts Listed</span>
                </div>
                <div class="acc-card-body p-0">
                    <div class="table-responsive">
                        <table class="table acc-table mb-0 datatable-export">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Code</th>
                                    <th>Account Name</th>
                                    <th>Type</th>
                                    <th>Account Group</th>
                                    <th class="text-right">Opening Dr (৳)</th>
                                    <th class="text-right">Opening Cr (৳)</th>
                                    <th class="text-right">Current Balance (৳)</th>
                                    <th class="text-center" style="width: 90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($accounts)): ?>
                                    <?php foreach ($accounts as $a): ?>
                                        <tr>
                                            <td><span class="font-weight-bold text-dark"><?php echo $a['account_code']; ?></span></td>
                                            <td>
                                                <div class="font-weight-bold"><?php echo htmlspecialchars($a['account_name']); ?></div>
                                                <?php if (!empty($a['description'])): ?>
                                                    <small class="text-muted"><?php echo htmlspecialchars($a['description']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-light border">
                                                    <?php echo strtoupper($a['account_type']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($a['group_name'] ?? '-'); ?></td>
                                            <td class="acc-amount acc-dr"><?php echo number_format($a['opening_debit'], 2); ?></td>
                                            <td class="acc-amount acc-cr"><?php echo number_format($a['opening_credit'], 2); ?></td>
                                            <td class="acc-amount acc-bal">
                                                ৳ <?php echo number_format($a['current_balance'], 2); ?>
                                                <small class="text-muted font-weight-normal"><?php echo in_array($a['account_type'], ['asset', 'expense']) ? 'Dr' : 'Cr'; ?></small>
                                            </td>
                                            <td class="text-center">
                                                <a href="finance/generalLedger?account_id=<?php echo $a['id']; ?>" class="btn btn-xs btn-outline-info" title="View General Ledger">
                                                    <i class="fas fa-book-open"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No accounts matching filter criteria.</td>
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

<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="addAccountForm" method="post" action="finance/addAccount">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i> Create Chart of Account</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold small">Account Code <span class="text-danger">*</span></label>
                        <input type="text" name="account_code" class="form-control form-control-sm" placeholder="e.g. 1040" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold small">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" class="form-control form-control-sm" placeholder="e.g. Petty Cash - Lab Counter" required>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">Account Type <span class="text-danger">*</span></label>
                            <select name="account_type" class="form-control form-control-sm" required>
                                <option value="asset">Asset (1000s)</option>
                                <option value="liability">Liability (2000s)</option>
                                <option value="equity">Equity (3000s)</option>
                                <option value="income">Income (4000s)</option>
                                <option value="expense">Expense (5000s)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">Account Group <span class="text-danger">*</span></label>
                            <select name="group_id" class="form-control form-control-sm" required>
                                <?php foreach ($groups as $g): ?>
                                    <option value="<?php echo $g['id']; ?>"><?php echo $g['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">Opening Debit (৳)</label>
                            <input type="number" step="0.01" name="opening_debit" class="form-control form-control-sm" value="0.00">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small">Opening Credit (৳)</label>
                            <input type="number" step="0.01" name="opening_credit" class="form-control form-control-sm" value="0.00">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold small">Description / Notes</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Operational purpose of this account..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-info">Save Account</button>
                </div>
            </form>
        </div>
    </div>
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
            { extend: 'excel', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Chart_of_Accounts' },
            { extend: 'csv', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Chart_of_Accounts' },
            { extend: 'pdf', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Chart_of_Accounts' },
            { extend: 'print', className: 'btn btn-xs btn-outline-secondary' }
        ]
    });

    $('#addAccountForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp) {
                if (resp.status) {
                    location.reload();
                } else {
                    alert(resp.message || 'Error creating account.');
                }
            },
            error: function() {
                alert('Server error occurred.');
            }
        });
    });
});
</script>
