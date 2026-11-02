<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$current = !empty($active_report) ? $active_report : 'dashboard';
?>
<div class="rep-nav-bar d-print-none">
    <a href="dashboard/financial" class="rep-nav-item" style="color: #0369a1; font-weight: 700;">
        <i class="fas fa-arrow-left"></i> Financial Dashboard
    </a>
    <span class="text-muted d-none d-md-inline" style="padding-top:6px;">|</span>
    <a href="finance/profitLossDashboard" class="rep-nav-item <?php echo ($current === 'dashboard') ? 'active' : ''; ?>">
        <i class="fas fa-chart-pie"></i> P&L Dashboard
    </a>
    <a href="finance/dailyProfitLoss" class="rep-nav-item <?php echo ($current === 'daily') ? 'active' : ''; ?>">
        <i class="fas fa-calendar-day"></i> Daily P&L
    </a>
    <a href="finance/monthlyProfitLoss" class="rep-nav-item <?php echo ($current === 'monthly') ? 'active' : ''; ?>">
        <i class="fas fa-calendar-alt"></i> Monthly P&L
    </a>
    <a href="finance/yearlyProfitLoss" class="rep-nav-item <?php echo ($current === 'yearly') ? 'active' : ''; ?>">
        <i class="fas fa-calendar-check"></i> Yearly P&L
    </a>
    <a href="finance/departmentProfitability" class="rep-nav-item <?php echo ($current === 'department') ? 'active' : ''; ?>">
        <i class="fas fa-sitemap"></i> Department-Wise
    </a>
    <a href="finance/testProfitability" class="rep-nav-item <?php echo ($current === 'test') ? 'active' : ''; ?>">
        <i class="fas fa-vial"></i> Test-Wise
    </a>
    <a href="finance/collectionBasedPnl" class="rep-nav-item <?php echo ($current === 'cash_pnl') ? 'active' : ''; ?>">
        <i class="fas fa-money-bill-wave"></i> Cash Basis (Collections)
    </a>
    <a href="finance/billingBasedPnl" class="rep-nav-item <?php echo ($current === 'accrual_pnl') ? 'active' : ''; ?>">
        <i class="fas fa-file-invoice-dollar"></i> Accrual Basis (Billing)
    </a>
    <a href="finance/expenseProfitLossBreakdown" class="rep-nav-item <?php echo ($current === 'expense_breakdown') ? 'active' : ''; ?>">
        <i class="fas fa-receipt"></i> Expense Breakdown
    </a>
    <a href="finance/commissionExpenseAnalysis" class="rep-nav-item <?php echo ($current === 'commission_analysis') ? 'active' : ''; ?>">
        <i class="fas fa-hand-holding-usd"></i> Commission Analysis
    </a>
    <a href="finance/profitLossComparison" class="rep-nav-item <?php echo ($current === 'comparison') ? 'active' : ''; ?>">
        <i class="fas fa-exchange-alt"></i> Financial Comparison
    </a>
    <a href="finance/incomeVsExpenseReport" class="rep-nav-item <?php echo ($current === 'income_expense') ? 'active' : ''; ?>">
        <i class="fas fa-balance-scale"></i> Income vs Expense
    </a>
    <a href="finance/profitLossReconciliation" class="rep-nav-item <?php echo ($current === 'reconciliation') ? 'active' : ''; ?>" style="color: #b91c1c; font-weight: 700;">
        <i class="fas fa-check-double"></i> Reconciliation Audit
    </a>
</div>
