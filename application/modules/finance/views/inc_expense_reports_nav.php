<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$current = !empty($active_report) ? $active_report : '';
?>
<div class="rep-nav-bar d-print-none">
    <a href="dashboard/financial" class="rep-nav-item" style="color: #1e40af; font-weight: 700;">
        <i class="fas fa-arrow-left"></i> Financial Dashboard
    </a>
    <span class="text-muted d-none d-md-inline" style="padding-top:6px;">|</span>
    <a href="finance/dailyExpenseReport" class="rep-nav-item <?php echo ($current === 'daily_expense') ? 'active' : ''; ?>">
        <i class="fas fa-calendar-day"></i> Daily Expense
    </a>
    <a href="finance/monthlyExpenseReport" class="rep-nav-item <?php echo ($current === 'monthly_expense') ? 'active' : ''; ?>">
        <i class="fas fa-calendar-alt"></i> Monthly Expense
    </a>
    <a href="finance/expenseLedgerReport" class="rep-nav-item <?php echo ($current === 'expense_ledger') ? 'active' : ''; ?>">
        <i class="fas fa-book"></i> Expense Ledger
    </a>
    <a href="finance/expenseCategoryReport" class="rep-nav-item <?php echo ($current === 'expense_category') ? 'active' : ''; ?>">
        <i class="fas fa-chart-pie"></i> Category Analysis
    </a>
    <a href="finance/vendorPaymentReport" class="rep-nav-item <?php echo ($current === 'vendor_payment') ? 'active' : ''; ?>">
        <i class="fas fa-truck-loading"></i> Vendor Payments
    </a>
    <a href="finance/employeeSalaryReport" class="rep-nav-item <?php echo ($current === 'employee_salary') ? 'active' : ''; ?>">
        <i class="fas fa-user-tie"></i> Employee Salary
    </a>
    <a href="finance/utilityBillReport" class="rep-nav-item <?php echo ($current === 'utility_bills') ? 'active' : ''; ?>">
        <i class="fas fa-bolt"></i> Utility Bills
    </a>
    <a href="finance/assetPurchaseReport" class="rep-nav-item <?php echo ($current === 'asset_purchase') ? 'active' : ''; ?>">
        <i class="fas fa-cogs"></i> Asset Purchases
    </a>
    <a href="finance/pettyCashReport" class="rep-nav-item <?php echo ($current === 'petty_cash') ? 'active' : ''; ?>">
        <i class="fas fa-coins"></i> Petty Cash
    </a>
    <a href="finance/cashBookReport" class="rep-nav-item <?php echo ($current === 'cash_book') ? 'active' : ''; ?>">
        <i class="fas fa-money-bill-wave"></i> Cash Book
    </a>
    <a href="finance/bankBookReport" class="rep-nav-item <?php echo ($current === 'bank_book') ? 'active' : ''; ?>">
        <i class="fas fa-university"></i> Bank Book
    </a>
    <a href="finance/expenseApprovalReport" class="rep-nav-item <?php echo ($current === 'expense_approval') ? 'active' : ''; ?>">
        <i class="fas fa-check-double"></i> Expense Approval
    </a>
</div>

