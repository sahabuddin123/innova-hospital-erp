<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$current = !empty($active_report) ? $active_report : '';
?>
<div class="rep-nav-bar d-print-none">
    <a href="dashboard/financial" class="rep-nav-item" style="color: #1e40af; font-weight: 700;">
        <i class="fas fa-arrow-left"></i> Financial Dashboard
    </a>
    <span class="text-muted d-none d-md-inline" style="padding-top:6px;">|</span>
    <a href="finance/dailyCollectionReport" class="rep-nav-item <?php echo ($current === 'daily_collection') ? 'active' : ''; ?>">
        <i class="fas fa-hand-holding-usd"></i> Daily Collection
    </a>
    <a href="finance/monthlySalesReport" class="rep-nav-item <?php echo ($current === 'monthly_sales') ? 'active' : ''; ?>">
        <i class="fas fa-calendar-alt"></i> Monthly Sales
    </a>
    <a href="finance/paymentMethodReport" class="rep-nav-item <?php echo ($current === 'payment_methods') ? 'active' : ''; ?>">
        <i class="fas fa-wallet"></i> Payment Methods
    </a>
    <a href="finance/dueReport" class="rep-nav-item <?php echo ($current === 'due_report') ? 'active' : ''; ?>">
        <i class="fas fa-exclamation-circle"></i> Outstanding Due
    </a>
    <a href="finance/partialPaymentReport" class="rep-nav-item <?php echo ($current === 'partial_payment') ? 'active' : ''; ?>">
        <i class="fas fa-layer-group"></i> Partial Payments
    </a>
    <a href="finance/advancePaymentReport" class="rep-nav-item <?php echo ($current === 'advance_payment') ? 'active' : ''; ?>">
        <i class="fas fa-piggy-bank"></i> Advance Deposits
    </a>
    <a href="finance/refundReport" class="rep-nav-item <?php echo ($current === 'refund_report') ? 'active' : ''; ?>">
        <i class="fas fa-undo-alt"></i> Refunds
    </a>
    <a href="finance/discountReport" class="rep-nav-item <?php echo ($current === 'discount_report') ? 'active' : ''; ?>">
        <i class="fas fa-tags"></i> Discount Audit
    </a>
    <a href="finance/cashierReport" class="rep-nav-item <?php echo ($current === 'cashier_report') ? 'active' : ''; ?>">
        <i class="fas fa-user-check"></i> Cashier-wise
    </a>
    <a href="finance/collectionSummaryReport" class="rep-nav-item <?php echo ($current === 'collection_summary') ? 'active' : ''; ?>">
        <i class="fas fa-balance-scale"></i> Collection Summary
    </a>
</div>

