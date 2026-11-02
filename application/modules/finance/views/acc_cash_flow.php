<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-money-bill-wave text-info mr-2"></i><?php echo lang('cash_flow_statement'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Statement of Cash Flows (Operating, Investing & Financing)</small>
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
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'cash_flow']); ?>

            <!-- Filter Box -->
            <div class="acc-filter-box">
                <form method="get" action="finance/cashFlowStatement" class="row align-items-center">
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">From Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">To Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-info w-100 mr-2 mt-4">Compute Cash Flows</button>
                        <a href="finance/cashFlowStatement" class="btn btn-sm btn-outline-secondary mt-4">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Compliance Notice -->
            <div class="acc-alert-notice">
                <i class="fas fa-coins mr-1 text-info"></i>
                <strong>Cash Flow Mathematical Verification:</strong>
                $$\text{Opening Cash \& Cash Equivalents} + \text{Net Change in Cash} = \text{Closing Cash \& Equivalents}$$
                Evaluates physical cash, bank accounts, and verified digital collection gateways. In accordance with Prompt Rule #10, referral wallet credits are liabilities and not counted as cash outflows until formal withdrawal.
            </div>

            <!-- KPI Cards -->
            <div class="acc-kpi-grid">
                <div class="acc-kpi-card info">
                    <div class="acc-kpi-label">Opening Cash & Equivalents</div>
                    <div class="acc-kpi-val text-primary">৳ <?php echo number_format($report['opening_cash'], 2); ?></div>
                    <div class="acc-kpi-sub">As of <?php echo date('d M Y', strtotime($start_date)); ?></div>
                </div>
                <div class="acc-kpi-card <?php echo $report['net_operating_cf'] >= 0 ? 'success' : 'danger'; ?>">
                    <div class="acc-kpi-label">Net Operating Cash Flow</div>
                    <div class="acc-kpi-val <?php echo $report['net_operating_cf'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                        ৳ <?php echo number_format($report['net_operating_cf'], 2); ?>
                    </div>
                    <div class="acc-kpi-sub">Collections Less Payments</div>
                </div>
                <div class="acc-kpi-card purple">
                    <div class="acc-kpi-label">Net Change in Cash</div>
                    <div class="acc-kpi-val" style="color: #6f42c1;">৳ <?php echo number_format($report['net_change'], 2); ?></div>
                    <div class="acc-kpi-sub">Net Period Variance</div>
                </div>
                <div class="acc-kpi-card success">
                    <div class="acc-kpi-label">Closing Cash & Equivalents</div>
                    <div class="acc-kpi-val text-success">৳ <?php echo number_format($report['closing_cash'], 2); ?></div>
                    <div class="acc-kpi-sub">As of <?php echo date('d M Y', strtotime($end_date)); ?></div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="acc-card">
                <div class="acc-card-header">
                    <h5 class="acc-card-title"><i class="fas fa-stream text-muted"></i> Cash Flow Activity Breakdown for <?php echo date('d M Y', strtotime($start_date)); ?> to <?php echo date('d M Y', strtotime($end_date)); ?></h5>
                </div>
                <div class="acc-card-body p-0">
                    <table class="table acc-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Cash Flow Activity Category</th>
                                <th class="text-right" style="width: 220px;">Amount (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- 1. Operating Activities -->
                            <tr class="table-primary font-weight-bold">
                                <td colspan="2"><i class="fas fa-stethoscope mr-1"></i> 1. CASH FLOWS FROM OPERATING ACTIVITIES</td>
                            </tr>
                            <tr>
                                <td class="pl-4">Cash Received from Diagnostic Patients & Clients</td>
                                <td class="acc-amount text-right text-success">৳ <?php echo number_format($report['operating_inflows'], 2); ?></td>
                            </tr>
                            <tr>
                                <td class="pl-4">Cash Paid for Laboratory Consumables, Operating Expenses & Overheads</td>
                                <td class="acc-amount text-right text-danger">(৳ <?php echo number_format($report['operating_outflows'], 2); ?>)</td>
                            </tr>
                            <tr class="bg-light font-weight-bold">
                                <td class="pl-4 text-uppercase">Net Cash Generated by Operating Activities:</td>
                                <td class="acc-amount text-right <?php echo $report['net_operating_cf'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    ৳ <?php echo number_format($report['net_operating_cf'], 2); ?>
                                </td>
                            </tr>

                            <!-- 2. Investing Activities -->
                            <tr class="table-warning font-weight-bold">
                                <td colspan="2"><i class="fas fa-tools mr-1"></i> 2. CASH FLOWS FROM INVESTING ACTIVITIES</td>
                            </tr>
                            <tr>
                                <td class="pl-4">Purchase of Diagnostic Equipment & Machines</td>
                                <td class="acc-amount text-right">৳ 0.00</td>
                            </tr>
                            <tr>
                                <td class="pl-4">Purchase of Furniture & Fixtures</td>
                                <td class="acc-amount text-right">৳ 0.00</td>
                            </tr>
                            <tr class="bg-light font-weight-bold">
                                <td class="pl-4 text-uppercase">Net Cash Used in Investing Activities:</td>
                                <td class="acc-amount text-right">৳ <?php echo number_format($report['net_investing_cf'], 2); ?></td>
                            </tr>

                            <!-- 3. Financing Activities -->
                            <tr class="table-info font-weight-bold">
                                <td colspan="2"><i class="fas fa-hand-holding-usd mr-1"></i> 3. CASH FLOWS FROM FINANCING ACTIVITIES</td>
                            </tr>
                            <tr>
                                <td class="pl-4">Capital Contribution from Owners / Shareholders</td>
                                <td class="acc-amount text-right">৳ 0.00</td>
                            </tr>
                            <tr>
                                <td class="pl-4">Borrowings / Loan Repayments</td>
                                <td class="acc-amount text-right">৳ 0.00</td>
                            </tr>
                            <tr class="bg-light font-weight-bold">
                                <td class="pl-4 text-uppercase">Net Cash from Financing Activities:</td>
                                <td class="acc-amount text-right">৳ <?php echo number_format($report['net_financing_cf'], 2); ?></td>
                            </tr>

                            <!-- Summary -->
                            <tr class="font-weight-bold" style="font-size: 14px;">
                                <td class="text-uppercase">Net Increase / (Decrease) in Cash & Cash Equivalents:</td>
                                <td class="acc-amount text-right text-primary">৳ <?php echo number_format($report['net_change'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Cash & Cash Equivalents at Beginning of the Period:</td>
                                <td class="acc-amount text-right">৳ <?php echo number_format($report['opening_cash'], 2); ?></td>
                            </tr>
                            <tr class="table-success font-weight-bold" style="font-size: 16px;">
                                <td class="text-uppercase">CASH & CASH EQUIVALENTS AT END OF THE PERIOD:</td>
                                <td class="acc-amount text-right text-success">৳ <?php echo number_format($report['closing_cash'], 2); ?></td>
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
