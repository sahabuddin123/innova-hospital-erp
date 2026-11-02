<?php
$current_report = isset($active_report) ? $active_report : 'chart_of_accounts';
?>
<div class="acc-nav-bar">
    <a href="finance/chartOfAccounts" class="acc-nav-btn <?php echo $current_report === 'chart_of_accounts' ? 'active' : ''; ?>">
        <i class="fas fa-sitemap"></i> <?php echo lang('chart_of_accounts'); ?>
    </a>
    <a href="finance/generalLedger" class="acc-nav-btn <?php echo $current_report === 'general_ledger' ? 'active' : ''; ?>">
        <i class="fas fa-book"></i> <?php echo lang('general_ledger'); ?>
    </a>
    <a href="finance/journalEntryRegister" class="acc-nav-btn <?php echo $current_report === 'journal_register' ? 'active' : ''; ?>">
        <i class="fas fa-file-invoice"></i> <?php echo lang('journal_entry_register'); ?>
    </a>
    <a href="finance/trialBalance" class="acc-nav-btn <?php echo $current_report === 'trial_balance' ? 'active' : ''; ?>">
        <i class="fas fa-balance-scale-left"></i> <?php echo lang('trial_balance'); ?>
    </a>
    <a href="finance/pnlStatement" class="acc-nav-btn <?php echo $current_report === 'pnl_statement' ? 'active' : ''; ?>">
        <i class="fas fa-file-invoice-dollar"></i> <?php echo lang('pnl_statement'); ?>
    </a>
    <a href="finance/balanceSheet" class="acc-nav-btn <?php echo $current_report === 'balance_sheet' ? 'active' : ''; ?>">
        <i class="fas fa-landmark"></i> <?php echo lang('balance_sheet'); ?>
    </a>
    <a href="finance/cashFlowStatement" class="acc-nav-btn <?php echo $current_report === 'cash_flow' ? 'active' : ''; ?>">
        <i class="fas fa-money-bill-wave"></i> <?php echo lang('cash_flow_statement'); ?>
    </a>
    <a href="finance/accountsReceivable" class="acc-nav-btn <?php echo $current_report === 'accounts_receivable' ? 'active' : ''; ?>">
        <i class="fas fa-hand-holding-usd"></i> <?php echo lang('accounts_receivable'); ?>
    </a>
    <a href="finance/accountsPayable" class="acc-nav-btn <?php echo $current_report === 'accounts_payable' ? 'active' : ''; ?>">
        <i class="fas fa-receipt"></i> <?php echo lang('accounts_payable'); ?>
    </a>
    <a href="finance/cashBook" class="acc-nav-btn <?php echo $current_report === 'cash_book' ? 'active' : ''; ?>">
        <i class="fas fa-coins"></i> <?php echo lang('cash_book'); ?>
    </a>
    <a href="finance/bankBook" class="acc-nav-btn <?php echo $current_report === 'bank_book' ? 'active' : ''; ?>">
        <i class="fas fa-university"></i> <?php echo lang('bank_book'); ?>
    </a>
    <a href="finance/bankReconciliation" class="acc-nav-btn <?php echo $current_report === 'bank_reconciliation' ? 'active' : ''; ?>">
        <i class="fas fa-check-double"></i> <?php echo lang('bank_reconciliation'); ?>
    </a>
    <a href="finance/openingBalanceReport" class="acc-nav-btn <?php echo $current_report === 'opening_balance' ? 'active' : ''; ?>">
        <i class="fas fa-hourglass-start"></i> <?php echo lang('opening_balance_report'); ?>
    </a>
    <a href="finance/periodClosing" class="acc-nav-btn <?php echo $current_report === 'period_closing' ? 'active' : ''; ?>">
        <i class="fas fa-calendar-check"></i> <?php echo lang('financial_period_closing'); ?>
    </a>
    <a href="finance/accountingAuditTrail" class="acc-nav-btn <?php echo $current_report === 'audit_trail' ? 'active' : ''; ?>">
        <i class="fas fa-history"></i> <?php echo lang('accounting_audit_trail'); ?>
    </a>
</div>
