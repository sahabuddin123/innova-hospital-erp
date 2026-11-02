<?php
$current_report = isset($active_report) ? $active_report : 'audit_dashboard';
?>
<div class="acc-nav-bar">
    <a href="finance/financialAuditDashboard" class="acc-nav-btn <?php echo $current_report === 'audit_dashboard' ? 'active' : ''; ?>">
        <i class="fas fa-chart-line"></i> <?php echo lang('financial_audit_dashboard'); ?>
    </a>
    <a href="finance/invoiceAuditReport" class="acc-nav-btn <?php echo $current_report === 'audit_invoices' ? 'active' : ''; ?>">
        <i class="fas fa-file-invoice"></i> <?php echo lang('invoice_audit_report'); ?>
    </a>
    <a href="finance/paymentAuditReport" class="acc-nav-btn <?php echo $current_report === 'audit_payments' ? 'active' : ''; ?>">
        <i class="fas fa-hand-holding-usd"></i> <?php echo lang('payment_audit_report'); ?>
    </a>
    <a href="finance/expenseAuditReport" class="acc-nav-btn <?php echo $current_report === 'audit_expenses' ? 'active' : ''; ?>">
        <i class="fas fa-receipt"></i> <?php echo lang('expense_audit_report'); ?>
    </a>
    <a href="finance/doctorCommissionAuditReport" class="acc-nav-btn <?php echo $current_report === 'audit_doctor_commissions' ? 'active' : ''; ?>">
        <i class="fas fa-user-md"></i> <?php echo lang('doctor_commission_audit_report'); ?>
    </a>
    <a href="finance/referralCommissionAuditReport" class="acc-nav-btn <?php echo $current_report === 'audit_referral_commissions' ? 'active' : ''; ?>">
        <i class="fas fa-users-cog"></i> <?php echo lang('referral_commission_audit_report'); ?>
    </a>
    <a href="finance/cashBankAuditReport" class="acc-nav-btn <?php echo $current_report === 'audit_cash_bank' ? 'active' : ''; ?>">
        <i class="fas fa-university"></i> <?php echo lang('cash_bank_audit_report'); ?>
    </a>
    <a href="finance/financialAuditTrail" class="acc-nav-btn <?php echo $current_report === 'audit_trail' ? 'active' : ''; ?>">
        <i class="fas fa-shield-alt"></i> <?php echo lang('master_financial_audit_trail'); ?>
    </a>
</div>
