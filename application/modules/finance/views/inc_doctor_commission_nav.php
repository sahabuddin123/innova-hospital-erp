<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$current = !empty($active_report) ? $active_report : '';
?>
<div class="rep-nav-bar d-print-none">
    <a href="dashboard/financial" class="rep-nav-item" style="color: #1e40af; font-weight: 700;">
        <i class="fas fa-arrow-left"></i> Financial Dashboard
    </a>
    <span class="text-muted d-none d-md-inline" style="padding-top:6px;">|</span>
    <a href="finance/doctorsCommissionSummary" class="rep-nav-item <?php echo ($current === 'doctor_summary') ? 'active' : ''; ?>">
        <i class="fas fa-chart-line"></i> Commission Summary
    </a>
    <a href="finance/doctorWiseCommissionReport" class="rep-nav-item <?php echo ($current === 'doctor_wise') ? 'active' : ''; ?>">
        <i class="fas fa-user-md"></i> Doctor-Wise Report
    </a>
    <a href="finance/testWiseCommissionReport" class="rep-nav-item <?php echo ($current === 'test_wise') ? 'active' : ''; ?>">
        <i class="fas fa-vial"></i> Test-Wise Report
    </a>
    <a href="finance/invoiceWiseCommissionReport" class="rep-nav-item <?php echo ($current === 'invoice_wise') ? 'active' : ''; ?>">
        <i class="fas fa-file-invoice"></i> Invoice-Wise Report
    </a>
    <a href="finance/commissionPayableReport" class="rep-nav-item <?php echo ($current === 'payable') ? 'active' : ''; ?>">
        <i class="fas fa-hand-holding-usd"></i> Commission Payable
    </a>
    <a href="finance/commissionPaidReport" class="rep-nav-item <?php echo ($current === 'paid') ? 'active' : ''; ?>">
        <i class="fas fa-money-check-alt"></i> Commission Paid
    </a>
    <a href="finance/doctorCommissionLedger" class="rep-nav-item <?php echo ($current === 'ledger') ? 'active' : ''; ?>">
        <i class="fas fa-book"></i> Commission Ledger
    </a>
    <a href="finance/doctorCommissionStatement" class="rep-nav-item <?php echo ($current === 'statement') ? 'active' : ''; ?>">
        <i class="fas fa-file-signature"></i> Doctor Statement
    </a>
    <a href="finance/commissionAdjustmentReport" class="rep-nav-item <?php echo ($current === 'adjustment') ? 'active' : ''; ?>">
        <i class="fas fa-sliders-h"></i> Adjustments & Reversals
    </a>
    <a href="finance/commissionConfigurationReport" class="rep-nav-item <?php echo ($current === 'config') ? 'active' : ''; ?>">
        <i class="fas fa-cogs"></i> Rate Configuration
    </a>
</div>

