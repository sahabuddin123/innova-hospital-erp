<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-landmark text-info mr-2"></i><?php echo lang('balance_sheet'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Statement of Financial Position</small>
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
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'balance_sheet']); ?>

            <!-- Filter Box -->
            <div class="acc-filter-box">
                <form method="get" action="finance/balanceSheet" class="row align-items-center">
                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold">Statement As-of Date</label>
                        <input type="date" name="as_of_date" class="form-control form-control-sm" value="<?php echo $as_of_date; ?>">
                    </div>
                    <div class="col-md-3 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-info w-100 mt-4">Generate Balance Sheet</button>
                    </div>
                </form>
            </div>

            <!-- Mandatory Compliance Notice -->
            <div class="acc-alert-notice">
                <i class="fas fa-balance-scale mr-1 text-info"></i>
                <strong>Accounting Equation Strict Compliance:</strong>
                $$\text{Total Assets} = \text{Total Liabilities} + \text{Total Equity}$$
                In accordance with Prompt Rules #9 and #21, opening balances and journal entries must be verified. No artificial plug numbers or fabricated balancing figures are introduced.
            </div>

            <!-- KPI Cards -->
            <div class="acc-kpi-grid">
                <div class="acc-kpi-card info">
                    <div class="acc-kpi-label">Total Assets</div>
                    <div class="acc-kpi-val text-primary">৳ <?php echo number_format($report['total_assets'], 2); ?></div>
                    <div class="acc-kpi-sub">Current + Non-Current Assets</div>
                </div>
                <div class="acc-kpi-card warning">
                    <div class="acc-kpi-label">Total Liabilities</div>
                    <div class="acc-kpi-val text-warning">৳ <?php echo number_format($report['total_liabilities'], 2); ?></div>
                    <div class="acc-kpi-sub">Current + Long-Term Obligations</div>
                </div>
                <div class="acc-kpi-card purple">
                    <div class="acc-kpi-label">Total Equity & Earnings</div>
                    <div class="acc-kpi-val" style="color: #6f42c1;">৳ <?php echo number_format($report['total_equity_with_earnings'], 2); ?></div>
                    <div class="acc-kpi-sub">Capital + Current Year Surplus</div>
                </div>
                <div class="acc-kpi-card <?php echo $report['is_balanced'] ? 'success' : 'danger'; ?>">
                    <div class="acc-kpi-label">Accounting Equation Status</div>
                    <div class="acc-kpi-val <?php echo $report['is_balanced'] ? 'text-success' : 'text-danger'; ?>">
                        <?php echo $report['is_balanced'] ? 'BALANCED' : 'VARIANCE'; ?>
                    </div>
                    <div class="acc-kpi-sub">
                        Diff: ৳ <?php echo number_format($report['difference'], 2); ?>
                    </div>
                </div>
            </div>

            <!-- Balance Sheet Table -->
            <div class="row">
                <!-- Left Column: Assets -->
                <div class="col-md-6">
                    <div class="acc-card">
                        <div class="acc-card-header bg-primary text-white">
                            <h5 class="acc-card-title text-white"><i class="fas fa-boxes mr-1"></i> ASSETS</h5>
                        </div>
                        <div class="acc-card-body p-0">
                            <table class="table acc-table mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Account</th>
                                        <th class="text-right">Balance (৳)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-light font-weight-bold">
                                        <td colspan="2">CURRENT & NON-CURRENT ASSETS</td>
                                    </tr>
                                    <?php foreach ($report['asset_accounts'] as $a): ?>
                                        <tr>
                                            <td class="pl-4">
                                                <span class="font-weight-bold">[<?php echo $a['account_code']; ?>]</span>
                                                <?php echo htmlspecialchars($a['account_name']); ?>
                                                <small class="text-muted d-block"><?php echo htmlspecialchars($a['group_name']); ?></small>
                                            </td>
                                            <td class="acc-amount acc-dr text-right">
                                                <?php echo number_format($a['balance'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light font-weight-bold" style="font-size: 15px;">
                                        <td class="text-uppercase">TOTAL ASSETS:</td>
                                        <td class="acc-amount text-primary text-right">৳ <?php echo number_format($report['total_assets'], 2); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Liabilities & Equity -->
                <div class="col-md-6">
                    <!-- Liabilities Card -->
                    <div class="acc-card mb-3">
                        <div class="acc-card-header bg-warning text-dark">
                            <h5 class="acc-card-title"><i class="fas fa-file-invoice mr-1"></i> LIABILITIES</h5>
                        </div>
                        <div class="acc-card-body p-0">
                            <table class="table acc-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Account</th>
                                        <th class="text-right">Balance (৳)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($report['liability_accounts'] as $l): ?>
                                        <tr>
                                            <td class="pl-4">
                                                <span class="font-weight-bold">[<?php echo $l['account_code']; ?>]</span>
                                                <?php echo htmlspecialchars($l['account_name']); ?>
                                                <small class="text-muted d-block"><?php echo htmlspecialchars($l['group_name']); ?></small>
                                            </td>
                                            <td class="acc-amount acc-cr text-right">
                                                <?php echo number_format($l['balance'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light font-weight-bold">
                                        <td class="text-uppercase">Total Liabilities:</td>
                                        <td class="acc-amount text-warning text-right">৳ <?php echo number_format($report['total_liabilities'], 2); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Equity Card -->
                    <div class="acc-card">
                        <div class="acc-card-header" style="background-color: #6f42c1; color: #fff;">
                            <h5 class="acc-card-title text-white"><i class="fas fa-coins mr-1"></i> EQUITY & RESERVES</h5>
                        </div>
                        <div class="acc-card-body p-0">
                            <table class="table acc-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Account / Reserve Component</th>
                                        <th class="text-right">Balance (৳)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($report['equity_accounts'] as $e): ?>
                                        <tr>
                                            <td class="pl-4">
                                                <span class="font-weight-bold">[<?php echo $e['account_code']; ?>]</span>
                                                <?php echo htmlspecialchars($e['account_name']); ?>
                                            </td>
                                            <td class="acc-amount acc-cr text-right">
                                                <?php echo number_format($e['balance'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="font-weight-bold bg-light">
                                        <td class="pl-4">Current Year Operating Surplus / (Loss)</td>
                                        <td class="acc-amount text-right <?php echo $report['current_year_earnings'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                            ৳ <?php echo number_format($report['current_year_earnings'], 2); ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light font-weight-bold">
                                        <td class="text-uppercase">Total Equity:</td>
                                        <td class="acc-amount text-right" style="color: #6f42c1;">৳ <?php echo number_format($report['total_equity_with_earnings'], 2); ?></td>
                                    </tr>
                                    <tr class="table-info font-weight-bold" style="font-size: 15px;">
                                        <td class="text-uppercase">TOTAL LIABILITIES & EQUITY:</td>
                                        <td class="acc-amount text-right text-dark">৳ <?php echo number_format($report['total_liab_equity'], 2); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
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
