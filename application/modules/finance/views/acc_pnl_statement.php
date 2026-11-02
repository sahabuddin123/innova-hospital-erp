<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-file-invoice-dollar text-info mr-2"></i><?php echo lang('pnl_statement'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Accrual-Based Double-Entry Income Statement</small>
                </div>
                <div>
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'acc_pnl_statement']); ?>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <div class="container-fluid">
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'pnl_statement']); ?>

            <!-- Filter Box -->
            <div class="acc-filter-box">
                <form method="get" action="finance/pnlStatement" class="row align-items-center">
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">From Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">To Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-info w-100 mr-2 mt-4">Generate P&L Statement</button>
                        <a href="finance/pnlStatement" class="btn btn-sm btn-outline-secondary mt-4">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Audit Notice -->
            <div class="acc-alert-notice">
                <i class="fas fa-info-circle mr-1 text-info"></i>
                <strong>Accrual Accounting Compliance Notice:</strong>
                This statement evaluates posted revenue (4000 series) and expense (5000 series) accounts. In accordance with Prompt Rule #8, this report does NOT reuse cash-basis receipts as accrual revenue, and avoids duplicate expense recognition.
            </div>

            <!-- KPI Cards -->
            <div class="acc-kpi-grid">
                <div class="acc-kpi-card info">
                    <div class="acc-kpi-label">Gross Revenue</div>
                    <div class="acc-kpi-val text-primary">৳ <?php echo number_format($report['gross_revenue'], 2); ?></div>
                    <div class="acc-kpi-sub">Total Diagnostic Services</div>
                </div>
                <div class="acc-kpi-card warning">
                    <div class="acc-kpi-label">Discounts & Refunds</div>
                    <div class="acc-kpi-val text-danger">৳ <?php echo number_format($report['discounts_refunds'], 2); ?></div>
                    <div class="acc-kpi-sub">Contra-Revenue Allowed</div>
                </div>
                <div class="acc-kpi-card primary">
                    <div class="acc-kpi-label">Net Operating Revenue</div>
                    <div class="acc-kpi-val">৳ <?php echo number_format($report['net_revenue'], 2); ?></div>
                    <div class="acc-kpi-sub">Gross Less Deductions</div>
                </div>
                <div class="acc-kpi-card danger">
                    <div class="acc-kpi-label">Total Expenses</div>
                    <div class="acc-kpi-val text-danger">৳ <?php echo number_format($report['total_expenses'], 2); ?></div>
                    <div class="acc-kpi-sub">Direct Lab & Overheads</div>
                </div>
                <div class="acc-kpi-card <?php echo $report['net_profit'] >= 0 ? 'success' : 'danger'; ?>">
                    <div class="acc-kpi-label">Net Operating Profit / (Loss)</div>
                    <div class="acc-kpi-val <?php echo $report['net_profit'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                        ৳ <?php echo number_format($report['net_profit'], 2); ?>
                    </div>
                    <div class="acc-kpi-sub">
                        <?php echo $report['net_profit'] >= 0 ? 'Operating Surplus' : 'Operating Deficit'; ?>
                    </div>
                </div>
            </div>

            <!-- P&L Table Card -->
            <div class="acc-card">
                <div class="acc-card-header">
                    <h5 class="acc-card-title"><i class="fas fa-file-alt text-muted"></i> Statement of Comprehensive Income for <?php echo date('d M Y', strtotime($start_date)); ?> to <?php echo date('d M Y', strtotime($end_date)); ?></h5>
                </div>
                <div class="acc-card-body p-0">
                    <table class="table acc-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Account Code & Title</th>
                                <th class="text-right" style="width: 200px;">Subtotal (৳)</th>
                                <th class="text-right" style="width: 200px;">Total (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- SECTION 1: REVENUE -->
                            <tr class="table-primary font-weight-bold">
                                <td colspan="3"><i class="fas fa-chart-line mr-1"></i> 1. REVENUE FROM DIAGNOSTIC SERVICES</td>
                            </tr>
                            <?php foreach ($report['income_accounts'] as $inc): ?>
                                <?php if (!in_array($inc['account_code'], ['4100', '4110'])): ?>
                                    <tr>
                                        <td class="pl-4">
                                            <span class="font-weight-bold">[<?php echo $inc['account_code']; ?>]</span>
                                            <?php echo htmlspecialchars($inc['account_name']); ?>
                                        </td>
                                        <td class="acc-amount text-right"><?php echo number_format($inc['net_amount'], 2); ?></td>
                                        <td></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <tr class="font-weight-bold">
                                <td class="pl-4 text-uppercase">Gross Diagnostic Revenue:</td>
                                <td></td>
                                <td class="acc-amount text-right text-primary">৳ <?php echo number_format($report['gross_revenue'], 2); ?></td>
                            </tr>

                            <!-- Less Contra-Revenue -->
                            <?php foreach ($report['income_accounts'] as $inc): ?>
                                <?php if (in_array($inc['account_code'], ['4100', '4110'])): ?>
                                    <tr class="text-danger">
                                        <td class="pl-4">
                                            Less: <span class="font-weight-bold">[<?php echo $inc['account_code']; ?>]</span>
                                            <?php echo htmlspecialchars($inc['account_name']); ?>
                                        </td>
                                        <td class="acc-amount text-right text-danger">(<?php echo number_format(abs($inc['net_amount']), 2); ?>)</td>
                                        <td></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <tr class="bg-light font-weight-bold border-top border-bottom">
                                <td class="text-uppercase">Net Diagnostic Operating Revenue:</td>
                                <td></td>
                                <td class="acc-amount text-right font-weight-bold" style="font-size: 15px;">৳ <?php echo number_format($report['net_revenue'], 2); ?></td>
                            </tr>

                            <!-- SECTION 2: OPERATING EXPENSES -->
                            <tr class="table-danger font-weight-bold">
                                <td colspan="3"><i class="fas fa-receipt mr-1"></i> 2. OPERATING & LABORATORY EXPENSES</td>
                            </tr>
                            <?php foreach ($report['expense_accounts'] as $exp): ?>
                                <tr>
                                    <td class="pl-4">
                                        <span class="font-weight-bold">[<?php echo $exp['account_code']; ?>]</span>
                                        <?php echo htmlspecialchars($exp['account_name']); ?>
                                    </td>
                                    <td class="acc-amount text-right"><?php echo number_format($exp['net_amount'], 2); ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-light font-weight-bold border-top border-bottom">
                                <td class="text-uppercase">Total Operating Expenses:</td>
                                <td></td>
                                <td class="acc-amount text-right text-danger font-weight-bold" style="font-size: 15px;">৳ <?php echo number_format($report['total_expenses'], 2); ?></td>
                            </tr>

                            <!-- SECTION 3: NET PROFIT -->
                            <tr class="<?php echo $report['net_profit'] >= 0 ? 'table-success' : 'table-danger'; ?> font-weight-bold" style="font-size: 16px;">
                                <td class="text-uppercase">NET PROFIT / (LOSS) FOR THE PERIOD:</td>
                                <td></td>
                                <td class="acc-amount text-right font-weight-bold <?php echo $report['net_profit'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    ৳ <?php echo number_format($report['net_profit'], 2); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
