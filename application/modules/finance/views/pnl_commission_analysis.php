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
                    <h2><i class="fas fa-hand-holding-usd text-primary"></i> Commission Expense Analysis</h2>
                    <p>Unified diagnostic commission audit distinguishing accrual obligations from completed cash disbursements &bull; <strong><?php echo $period['period_label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'pnl_commission_analysis']); ?>
                    <a href="finance/doctorsCommissionSummary" class="rep-action-btn"><i class="fas fa-user-md"></i> Doctor Suite</a>
                    <a href="referral/commissionSummary" class="rep-action-btn"><i class="fas fa-user-friends"></i> Referral Suite</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--pnl-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/commissionExpenseAnalysis?period_type=today" class="rep-pill <?php echo ($period_type === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/commissionExpenseAnalysis?period_type=yesterday" class="rep-pill <?php echo ($period_type === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/commissionExpenseAnalysis?period_type=this_week" class="rep-pill <?php echo ($period_type === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/commissionExpenseAnalysis?period_type=this_month" class="rep-pill <?php echo ($period_type === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/commissionExpenseAnalysis?period_type=last_month" class="rep-pill <?php echo ($period_type === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/commissionExpenseAnalysis?period_type=this_year" class="rep-pill <?php echo ($period_type === 'this_year') ? 'active' : ''; ?>">This Year</a>
            </div>

            <!-- Custom Filter Form -->
            <form action="finance/commissionExpenseAnalysis" method="get" class="row g-3 align-items-end mt-2 d-print-none">
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
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-filter"></i> Apply Filter</button>
                    <a href="finance/commissionExpenseAnalysis" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>

        <!-- 3 Executive High-Level Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-card">
                <div class="rep-card-icon bg-purple"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="rep-card-label">Total Commission Generated</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['total_commission_generated'], 2); ?></div>
                <div class="rep-card-sub text-muted">Doctor &plus; Referral entitlement</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-red"><i class="fas fa-money-bill-wave"></i></div>
                <div class="rep-card-label">Total Cash Paid Out</div>
                <div class="rep-card-val text-danger"><?php echo $currency . ' ' . number_format($summary['total_commission_paid_out'], 2); ?></div>
                <div class="rep-card-sub text-muted">Completed cash disbursements</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-orange"><i class="fas fa-wallet"></i></div>
                <div class="rep-card-label">Available Wallet Liability</div>
                <div class="rep-card-val text-warning"><?php echo $currency . ' ' . number_format($summary['total_wallet_liability'], 2); ?></div>
                <div class="rep-card-sub text-warning">Current referral wallet balance</div>
            </div>
        </div>

        <!-- Critical Accounting Separation Callout -->
        <div class="alert alert-info shadow-sm border-0 d-flex align-items-center mb-4" role="alert" style="border-radius: var(--pnl-radius);">
            <div class="mr-3 font-weight-bold" style="font-size: 20px;">
                <i class="fas fa-balance-scale text-info"></i>
            </div>
            <div>
                <strong>Critical Accounting Principle:</strong>
                Referral Wallet Credit is an <em>accrual liability</em>, not a completed cash outflow. Only completed <strong>Referral Withdrawals</strong> represent actual cash deductions in the cash-basis P&amp;L. Generated commissions and paid commissions must never be deducted simultaneously!
            </div>
        </div>

        <!-- Side-by-Side Analysis Cards -->
        <div class="row g-4 mb-4">
            <!-- 1. Doctor Commission Analysis -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 p-4 h-100" style="border-radius: var(--pnl-radius); background: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="font-weight-bold mb-0 text-dark">
                            <i class="fas fa-user-md text-primary mr-2"></i> Doctor Commission Analysis
                        </h4>
                        <span class="badge badge-primary px-2 py-1">Physician Shares</span>
                    </div>

                    <table class="table table-bordered rep-table mb-3">
                        <tbody>
                            <tr>
                                <td>Commission Generated (Invoices)</td>
                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($doctor_commission['commission_generated'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Collection-Backed Eligible Commission</td>
                                <td class="text-right font-weight-bold text-success"><?php echo $currency . ' ' . number_format($doctor_commission['commission_eligible'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Actually Paid Out (Disbursements)</td>
                                <td class="text-right font-weight-bold text-danger"><?php echo $currency . ' ' . number_format($doctor_commission['commission_paid'], 2); ?></td>
                            </tr>
                            <tr style="background-color: #fef2f2;">
                                <td><strong>Outstanding Doctor Payable</strong></td>
                                <td class="text-right font-weight-bold text-danger"><?php echo $currency . ' ' . number_format($doctor_commission['pending_payable'], 2); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="small text-muted">
                        <i class="fas fa-info-circle mr-1"></i> In Cash-Basis P&amp;L, only <strong><?php echo $currency . ' ' . number_format($doctor_commission['commission_paid'], 2); ?></strong> is deducted as completed payment. In Accrual-Basis P&amp;L, recognized commission of <strong><?php echo $currency . ' ' . number_format($doctor_commission['commission_generated'], 2); ?></strong> is charged.
                    </div>
                </div>
            </div>

            <!-- 2. Referral Commission Analysis -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 p-4 h-100" style="border-radius: var(--pnl-radius); background: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="font-weight-bold mb-0 text-dark">
                            <i class="fas fa-user-friends text-purple mr-2"></i> Referral Commission &amp; Wallet Audit
                        </h4>
                        <span class="badge badge-purple px-2 py-1" style="background: #ede9fe; color: #7c3aed;">Promoter Shares</span>
                    </div>

                    <table class="table table-bordered rep-table mb-3">
                        <tbody>
                            <tr>
                                <td>Referral Commission Generated</td>
                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($referral_commission['commission_generated'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Collection-Backed Eligible Commission</td>
                                <td class="text-right font-weight-bold text-success"><?php echo $currency . ' ' . number_format($referral_commission['commission_eligible'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Credited to Referrer Wallets (Period)</td>
                                <td class="text-right font-weight-bold text-primary"><?php echo $currency . ' ' . number_format($referral_commission['wallet_credited'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Actually Withdrawn / Paid Out</td>
                                <td class="text-right font-weight-bold text-danger"><?php echo $currency . ' ' . number_format($referral_commission['commission_withdrawn'], 2); ?></td>
                            </tr>
                            <tr style="background-color: #eff6ff;">
                                <td><strong>Current Available Wallet Balance</strong></td>
                                <td class="text-right font-weight-bold text-primary"><?php echo $currency . ' ' . number_format($referral_commission['available_wallet_balance'], 2); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="small text-muted">
                        <i class="fas fa-info-circle mr-1"></i> In Cash-Basis P&amp;L, only completed withdrawals of <strong><?php echo $currency . ' ' . number_format($referral_commission['commission_withdrawn'], 2); ?></strong> are deducted. Wallet balance of <strong><?php echo $currency . ' ' . number_format($referral_commission['available_wallet_balance'], 2); ?></strong> remains a balance sheet liability.
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Signatures -->
        <div class="d-none d-print-block rep-print-signatures">
            <div class="signature-box">Prepared By (Accounts Officer)</div>
            <div class="signature-box">Checked By (Audit Head)</div>
            <div class="signature-box">Approved By (Managing Director)</div>
        </div>

    </div>
</div>
