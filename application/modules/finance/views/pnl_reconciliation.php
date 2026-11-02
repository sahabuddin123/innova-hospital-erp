<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳';
?>
<link rel="stylesheet" href="common/extranal/css/finance/pnl_reports.css">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <?php $this->load->view('finance/inc_pnl_reports_nav'); ?>

        <!-- Header Card -->
        <div class="rep-header-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 16px;">
                <div class="rep-title">
                    <h2><i class="fas fa-check-double text-danger"></i> Profit &amp; Loss Financial Reconciliation Audit</h2>
                    <p>Automated mathematical and categorical verification engine validating financial accounting integrity &bull; <strong><?php echo $period['period_label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <button type="button" onclick="window.print()" class="rep-action-btn"><i class="fas fa-print"></i> Print Audit</button>
                    <a href="finance/profitLossDashboard" class="rep-action-btn"><i class="fas fa-chart-pie"></i> Back to Dashboard</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--pnl-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/profitLossReconciliation?period_type=today" class="rep-pill <?php echo ($period_type === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/profitLossReconciliation?period_type=yesterday" class="rep-pill <?php echo ($period_type === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/profitLossReconciliation?period_type=this_week" class="rep-pill <?php echo ($period_type === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/profitLossReconciliation?period_type=this_month" class="rep-pill <?php echo ($period_type === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/profitLossReconciliation?period_type=last_month" class="rep-pill <?php echo ($period_type === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/profitLossReconciliation?period_type=this_year" class="rep-pill <?php echo ($period_type === 'this_year') ? 'active' : ''; ?>">This Year</a>
            </div>

            <!-- Custom Filter Form -->
            <form action="finance/profitLossReconciliation" method="get" class="row g-3 align-items-end mt-2 d-print-none">
                <input type="hidden" name="period_type" value="custom">
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo html_escape($period['date_from']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted font-weight-bold">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo html_escape($period['date_to']); ?>">
                </div>
                <div class="col-md-4 d-flex" style="gap: 8px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-filter"></i> Run Reconciliation Audit</button>
                    <a href="finance/profitLossReconciliation" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>

        <!-- Audit Score Overview Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-3 h-100 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="small text-muted text-uppercase font-weight-bold">Audit Rules Verified</div>
                    <div class="h2 font-weight-bold mb-0 text-success">
                        <?php echo $summary['passed_rules']; ?> / <?php echo $summary['total_rules']; ?>
                    </div>
                    <div class="small text-success mt-2"><i class="fas fa-shield-alt mr-1"></i> 100% Rules Evaluated</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-3 h-100 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="small text-muted text-uppercase font-weight-bold">Active Discrepancies</div>
                    <div class="h2 font-weight-bold mb-0 <?php echo ($summary['failed_rules'] == 0) ? 'text-success' : 'text-danger'; ?>">
                        <?php echo $summary['failed_rules']; ?>
                    </div>
                    <div class="small text-muted mt-2">
                        <?php if ($summary['failed_rules'] == 0) { ?>
                            <span class="text-success"><i class="fas fa-check-circle mr-1"></i> No accounting anomalies detected</span>
                        <?php } else { ?>
                            <span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Action items detected</span>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-3 h-100 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="small text-muted text-uppercase font-weight-bold">Net Cash Inflows Audited</div>
                    <div class="h2 font-weight-bold mb-0 text-primary"><?php echo $currency . ' ' . number_format($summary['net_collections'], 2); ?></div>
                    <div class="small text-muted mt-2">Gross Billing: <strong><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></strong></div>
                </div>
            </div>
        </div>

        <!-- Reconciliation Checklist Table Card -->
        <div class="rep-table-card mt-4">
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--pnl-gray-800);">
                <i class="fas fa-tasks text-primary mr-1"></i> 11-Point Financial Accounting Reconciliation Matrix
            </h4>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle rep-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th>Integrity Audit Rule</th>
                            <th class="text-center" style="width: 120px;">Audit Status</th>
                            <th class="text-right">Expected</th>
                            <th class="text-right">Actual Recorded</th>
                            <th class="text-right">Variance</th>
                            <th>Audit Evidence &amp; Explanation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        foreach ($checks as $chk) { 
                            $is_pass = ($chk['status'] === 'VERIFIED');
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td><strong><?php echo html_escape($chk['rule']); ?></strong></td>
                                <td class="text-center">
                                    <?php if ($is_pass) { ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> VERIFIED</span>
                                    <?php } else { ?>
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i> DISCREPANCY</span>
                                    <?php } ?>
                                </td>
                                <td class="text-right font-weight-bold"><?php echo html_escape($chk['expected']); ?></td>
                                <td class="text-right font-weight-bold"><?php echo html_escape($chk['actual']); ?></td>
                                <td class="text-right <?php echo ($chk['variance'] === '৳0.00' || $chk['variance'] === 'None') ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo html_escape($chk['variance']); ?>
                                </td>
                                <td class="small text-muted"><?php echo html_escape($chk['note']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Principle Callout -->
            <div class="alert alert-light border mt-4 mb-0 small text-muted">
                <i class="fas fa-info-circle text-primary mr-1"></i>
                <strong>Audit Compliance Guarantee:</strong> In accordance with Lifecare Diagnostic Center financial governance policies, this reconciliation engine verifies that actual source transactions are preserved without mutating historical records.
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
