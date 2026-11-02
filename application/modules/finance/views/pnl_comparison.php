<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳';
$is_yearly = ($compare_mode === 'yearly');
?>
<link rel="stylesheet" href="common/extranal/css/finance/pnl_reports.css">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <?php $this->load->view('finance/inc_pnl_reports_nav'); ?>

        <!-- Header Card -->
        <div class="rep-header-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 16px;">
                <div class="rep-title">
                    <h2><i class="fas fa-exchange-alt text-primary"></i> Financial Performance Comparison</h2>
                    <p>Comparative growth, variance analysis, and operational performance trends &bull; <strong><?php echo $current_label; ?> vs <?php echo $compare_label; ?></strong></p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div class="basis-toggle-group d-print-none">
                        <a href="finance/profitLossComparison?compare_mode=monthly&month=<?php echo $target_month; ?>" class="basis-toggle-btn <?php echo (!$is_yearly) ? 'active-cash' : ''; ?>">
                            <i class="fas fa-calendar-alt"></i> Month-over-Month
                        </a>
                        <a href="finance/profitLossComparison?compare_mode=yearly&year=<?php echo $target_year; ?>" class="basis-toggle-btn <?php echo ($is_yearly) ? 'active-accrual' : ''; ?>">
                            <i class="fas fa-calendar-check"></i> Year-over-Year
                        </a>
                    </div>
                    <div class="rep-actions d-print-none">
                        <button type="button" onclick="window.print()" class="rep-action-btn"><i class="fas fa-print"></i> Print</button>
                    </div>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--pnl-gray-200);">

            <!-- Filter Form -->
            <form action="finance/profitLossComparison" method="get" class="row g-3 align-items-end mt-2 d-print-none">
                <input type="hidden" name="compare_mode" value="<?php echo $compare_mode; ?>">
                <?php if ($is_yearly) { ?>
                    <div class="col-md-4">
                        <label class="form-label small text-muted font-weight-bold">Select Year to Compare</label>
                        <select name="year" class="form-control form-control-sm">
                            <?php 
                            $cur_y = intval(date('Y'));
                            for ($y = $cur_y; $y >= $cur_y - 5; $y--) { ?>
                                <option value="<?php echo $y; ?>" <?php echo ($target_year == $y) ? 'selected' : ''; ?>>Year <?php echo $y; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } else { ?>
                    <div class="col-md-4">
                        <label class="form-label small text-muted font-weight-bold">Select Month to Compare</label>
                        <input type="month" name="month" class="form-control form-control-sm" value="<?php echo html_escape($target_month); ?>">
                    </div>
                <?php } ?>
                <div class="col-md-4 d-flex" style="gap: 8px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-search"></i> Compare Periods</button>
                </div>
            </form>
        </div>

        <!-- Comparative KPI Overview -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-3 h-100 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="small text-muted text-uppercase font-weight-bold"><?php echo $current_label; ?> Surplus</div>
                    <div class="h2 font-weight-bold mb-0 text-success"><?php echo $currency . ' ' . number_format($current_summary['cash_surplus'], 2); ?></div>
                    <div class="small text-muted mt-2">Collections: <strong><?php echo $currency . ' ' . number_format($current_summary['net_collections'], 2); ?></strong></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-3 h-100 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="small text-muted text-uppercase font-weight-bold"><?php echo $compare_label; ?> Surplus</div>
                    <div class="h2 font-weight-bold mb-0 text-muted"><?php echo $currency . ' ' . number_format($comparison['summary']['cash_surplus'], 2); ?></div>
                    <div class="small text-muted mt-2">Collections: <strong><?php echo $currency . ' ' . number_format($comparison['summary']['net_collections'], 2); ?></strong></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-3 h-100 bg-white" style="border-radius: var(--pnl-radius);">
                    <div class="small text-muted text-uppercase font-weight-bold">Growth &amp; Variance</div>
                    <div class="h2 font-weight-bold mb-0 <?php echo ($comparison['growth_pct'] >= 0) ? 'text-success' : 'text-danger'; ?>">
                        <?php echo ($comparison['growth_pct'] >= 0) ? '+' : ''; ?><?php echo number_format($comparison['growth_pct'], 2); ?>%
                    </div>
                    <div class="small text-muted mt-2">
                        Variance: <strong><?php echo ($comparison['variance_surplus'] >= 0) ? '+' : ''; ?><?php echo $currency . ' ' . number_format($comparison['variance_surplus'], 2); ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Side-by-Side Comparison Table -->
        <div class="rep-table-card mt-4">
            <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--pnl-gray-800);">
                <i class="fas fa-balance-scale text-primary mr-1"></i> Line-by-Line Financial Metric Comparison
            </h4>

            <?php
            $rows_to_compare = [
                ['label' => 'Gross Diagnostic Billing', 'curr' => $current_summary['gross_billing'], 'prev' => $comparison['summary']['gross_billing'] ?? 0, 'type' => 'inc'],
                ['label' => 'Total Invoices Discount', 'curr' => $current_summary['discount'], 'prev' => $comparison['summary']['discount'] ?? 0, 'type' => 'dec'],
                ['label' => 'Net Diagnostic Billing', 'curr' => $current_summary['net_billing'], 'prev' => $comparison['summary']['net_billing'] ?? 0, 'type' => 'inc'],
                ['label' => 'Actual Patient Collections', 'curr' => $current_summary['patient_collections'], 'prev' => $comparison['summary']['patient_collections'] ?? 0, 'type' => 'inc'],
                ['label' => 'Patient Refunds', 'curr' => $current_summary['refunds'], 'prev' => $comparison['summary']['refunds'] ?? 0, 'type' => 'dec'],
                ['label' => 'Net Patient Collections', 'curr' => $current_summary['net_collections'], 'prev' => $comparison['summary']['net_collections'] ?? 0, 'type' => 'inc'],
                ['label' => 'Operating Expenses Disbursed', 'curr' => $current_summary['operating_expenses'], 'prev' => $comparison['summary']['total_expenses'] ?? 0, 'type' => 'dec'],
                ['label' => 'Doctor Commission Paid', 'curr' => $current_summary['doctor_comm_paid'], 'prev' => $comparison['summary']['doctor_comm_paid'] ?? 0, 'type' => 'dec'],
                ['label' => 'Referral Commission Withdrawn', 'curr' => $current_summary['referral_comm_withdrawn'], 'prev' => $comparison['summary']['referral_comm_withdrawn'] ?? 0, 'type' => 'dec'],
                ['label' => 'Net Cash Operating Surplus', 'curr' => $current_summary['cash_surplus'], 'prev' => $comparison['summary']['cash_surplus'] ?? 0, 'type' => 'inc']
            ];
            ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle rep-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Financial Line Item</th>
                            <th class="text-right"><?php echo $current_label; ?> (৳)</th>
                            <th class="text-right"><?php echo $compare_label; ?> (৳)</th>
                            <th class="text-right">Monetary Variance (৳)</th>
                            <th class="text-center">Percentage Delta (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows_to_compare as $item) { 
                            $c_val = floatval($item['curr']);
                            $p_val = floatval($item['prev']);
                            $diff = $c_val - $p_val;
                            $pct = ($p_val != 0) ? round(($diff / abs($p_val)) * 100, 2) : (($c_val > 0) ? 100.00 : 0.00);
                            $is_good = ($item['type'] === 'inc') ? ($diff >= 0) : ($diff <= 0);
                            $badge_cls = $is_good ? 'badge-success' : 'badge-danger';
                        ?>
                            <tr>
                                <td><strong><?php echo $item['label']; ?></strong></td>
                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($c_val, 2); ?></td>
                                <td class="text-right text-muted"><?php echo $currency . ' ' . number_format($p_val, 2); ?></td>
                                <td class="text-right font-weight-bold <?php echo ($diff >= 0) ? 'text-dark' : 'text-danger'; ?>">
                                    <?php echo ($diff >= 0) ? '+' : ''; ?><?php echo $currency . ' ' . number_format($diff, 2); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?php echo $badge_cls; ?> px-2 py-1">
                                        <?php echo ($pct >= 0) ? '+' : ''; ?><?php echo number_format($pct, 2); ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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
