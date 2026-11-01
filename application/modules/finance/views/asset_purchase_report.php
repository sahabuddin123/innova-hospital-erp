<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
?>
<link href="common/extranal/css/finance/expense_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_expense_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Asset Purchase & Equipment Maintenance Report &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-cogs text-info"></i> Asset Purchase & Maintenance Report</h2>
                    <p>Capital diagnostic hardware procurement, clinical equipment, and machinery maintenance &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/assetPurchaseReport" class="m-0">
                <input type="hidden" name="period_type" id="period_type_input" value="<?php echo html_escape($period['period_type']); ?>">
                <div class="rep-controls-row">
                    <!-- Period Pills -->
                    <div class="rep-period-pills">
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'today') ? 'active' : ''; ?>" data-period="today">Today</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_month') ? 'active' : ''; ?>" data-period="this_month">This Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'last_month') ? 'active' : ''; ?>" data-period="last_month">Last Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_year') ? 'active' : ''; ?>" data-period="this_year">This Year</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'custom') ? 'active' : ''; ?>" data-period="custom">Custom</button>
                    </div>

                    <!-- Date Filter Group -->
                    <div class="rep-filter-group">
                        <div class="rep-input-wrap">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">FROM:</span>
                            <input type="date" name="date_from" id="date_from" class="rep-date-input" value="<?php echo html_escape($period['date_from']); ?>">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">TO:</span>
                            <input type="date" name="date_to" id="date_to" class="rep-date-input" value="<?php echo html_escape($period['date_to']); ?>">
                        </div>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Apply</button>
                        <a href="finance/assetPurchaseReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-tools text-info"></i> Total Asset Outlay</span>
                <span class="rep-kpi-val text-info"><?php echo $currency . number_format($summary['total_asset_spend'], 2); ?></span>
                <span class="rep-kpi-sub">Across <?php echo number_format($summary['item_count']); ?> procurement / repair entries</span>
            </div>
            <div class="rep-kpi-card success">
                <span class="rep-kpi-label"><i class="fas fa-boxes text-success"></i> Capital Purchases</span>
                <span class="rep-kpi-val text-success"><?php echo $currency . number_format($summary['total_purchases'], 2); ?></span>
                <span class="rep-kpi-sub">New equipment & physical hardware</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-wrench text-warning"></i> Repairs & Servicing</span>
                <span class="rep-kpi-val text-warning"><?php echo $currency . number_format($summary['total_repairs'], 2); ?></span>
                <span class="rep-kpi-sub">Equipment maintenance & repairs</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-balance-scale text-primary"></i> Accounting Treatment</span>
                <span class="rep-kpi-val" style="font-size:16px; padding-top:6px;">Cash-Basis Expensed</span>
                <span class="rep-kpi-sub">Non-capitalized cash outflow</span>
            </div>
        </div>

        <!-- Disclosure Alert -->
        <div class="alert alert-light border mb-4 p-3 d-print-none" style="border-radius: 8px; font-size: 13px;">
            <i class="fas fa-info-circle text-info mr-1"></i>
            <strong>Accounting Clarification:</strong> Lifecare Diagnostic Center currently accounts for physical asset procurements and repairs on an immediate cash outflow basis. Depreciation schedules and capital asset amortization are handled outside the operational voucher system.
        </div>

        <!-- Asset Records Table Card -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="assetTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Procurement Date</th>
                            <th>Voucher #</th>
                            <th>Asset Classification</th>
                            <th>Description / Specifications</th>
                            <th>Supplier / Payee</th>
                            <th>Purchaser / Staff</th>
                            <th class="text-center">Method</th>
                            <th class="text-right">Outlay Amount (<?php echo trim($currency); ?>)</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            foreach ($records as $r): 
                                $is_repair = (strpos(strtolower($r->category), 'repair') !== false);
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><?php echo date('d/m/Y', $r->date); ?></td>
                                <td class="font-weight-bold">
                                    <a href="finance/expenseInvoice?id=<?php echo $r->id; ?>" target="_blank" class="text-primary">
                                        EXP-<?php echo $r->id; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($is_repair): ?>
                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-wrench"></i> Repair / Service</span>
                                    <?php else: ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-box"></i> Capital Asset</span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($r->note ?: $r->category); ?></td>
                                <td><?php echo html_escape($r->pay_to ?: 'Vendor'); ?></td>
                                <td><small class="text-muted"><?php echo html_escape($r->payer_name ?: $r->creator_name); ?></small></td>
                                <td class="text-center"><span class="badge badge-light border"><?php echo html_escape($r->payment_method); ?></span></td>
                                <td class="text-right font-weight-bold text-danger" style="font-size: 14.5px;">
                                    <?php echo number_format($r->amount, 2); ?>
                                </td>
                                <td class="text-center"><span class="badge badge-info px-2 py-1">Settled</span></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="fas fa-cogs text-muted fa-3x mb-2 d-block"></i>
                                    <strong>No asset procurement or repair records found for this period.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="8" class="text-right font-weight-bold">Grand Total (<?php echo count($records); ?> records):</td>
                            <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_asset_spend'], 2); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Print Signatures -->
        <div class="d-none d-print-block mt-5 pt-4">
            <div class="row text-center">
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Bio-Medical Engineer / Admin</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Audit Head</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Managing Director</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    $('.rep-period-btn').on('click', function() {
        var period = $(this).data('period');
        $('#period_type_input').val(period);
        $(this).closest('form').submit();
    });

    if ($.fn.DataTable && $('#assetTable tbody tr td').length > 1) {
        $('#assetTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 25,
            order: [[1, 'desc']]
        });
    }
});
</script>
