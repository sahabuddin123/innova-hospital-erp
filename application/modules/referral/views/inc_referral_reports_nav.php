<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$current = !empty($active_report) ? $active_report : '';
?>
<div class="rep-nav-bar d-print-none">
    <a href="dashboard/financial" class="rep-nav-item" style="color: #0369a1; font-weight: 700;">
        <i class="fas fa-arrow-left"></i> Financial Dashboard
    </a>
    <span class="text-muted d-none d-md-inline" style="padding-top:6px;">|</span>
    <a href="referral/commissionSummary" class="rep-nav-item <?php echo ($current === 'summary') ? 'active' : ''; ?>">
        <i class="fas fa-chart-line"></i> Commission Summary
    </a>
    <a href="referral/referrerWiseReport" class="rep-nav-item <?php echo ($current === 'referrer_wise') ? 'active' : ''; ?>">
        <i class="fas fa-user-friends"></i> Referrer-Wise Report
    </a>
    <a href="referral/invoiceWiseReport" class="rep-nav-item <?php echo ($current === 'invoice_wise') ? 'active' : ''; ?>">
        <i class="fas fa-file-invoice"></i> Invoice-Wise Report
    </a>
    <a href="referral/testWiseReport" class="rep-nav-item <?php echo ($current === 'test_wise') ? 'active' : ''; ?>">
        <i class="fas fa-vial"></i> Test-Wise Report
    </a>
    <a href="referral/commissionPayableReport" class="rep-nav-item <?php echo ($current === 'payable') ? 'active' : ''; ?>">
        <i class="fas fa-hand-holding-usd"></i> Commission Payable
    </a>
    <a href="referral/wallets" class="rep-nav-item <?php echo ($current === 'wallets') ? 'active' : ''; ?>">
        <i class="fas fa-wallet"></i> Referrer Wallets
    </a>
    <a href="referral/commissionLedger" class="rep-nav-item <?php echo ($current === 'ledger') ? 'active' : ''; ?>">
        <i class="fas fa-book"></i> Commission Ledger
    </a>
    <a href="referral/withdrawals" class="rep-nav-item <?php echo ($current === 'withdrawals') ? 'active' : ''; ?>">
        <i class="fas fa-money-check-alt"></i> Withdrawals
    </a>
    <a href="referral/adjustmentReport" class="rep-nav-item <?php echo ($current === 'adjustments') ? 'active' : ''; ?>">
        <i class="fas fa-sliders-h"></i> Adjustments & Reversals
    </a>
    <a href="referral/discountReconciliationReport" class="rep-nav-item <?php echo ($current === 'discount_recon') ? 'active' : ''; ?>">
        <i class="fas fa-percent"></i> Discount Absorption
    </a>
    <a href="referral/statement" class="rep-nav-item <?php echo ($current === 'statement') ? 'active' : ''; ?>">
        <i class="fas fa-file-signature"></i> Referrer Statement
    </a>
    <a href="referral/reconciliationDashboard" class="rep-nav-item <?php echo ($current === 'recon_dashboard') ? 'active' : ''; ?>" style="color: #b91c1c; font-weight: 700;">
        <i class="fas fa-balance-scale"></i> Reconciliation Dashboard
    </a>
</div>
