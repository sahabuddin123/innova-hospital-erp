<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
$selected_vendor = !empty($selected_vendor) ? $selected_vendor : '';
?>
<link href="common/extranal/css/finance/expense_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_expense_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Vendor & Supplier Payment Report &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-truck-loading text-primary"></i> Vendor & Supplier Payment Report</h2>
                    <p>Disbursements to external diagnostic equipment, reagents, and service suppliers &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/vendorPaymentReport" class="m-0">
                <input type="hidden" name="period_type" id="period_type_input" value="<?php echo html_escape($period['period_type']); ?>">
                <div class="rep-controls-row">
                    <!-- Period Pills -->
                    <div class="rep-period-pills">
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'today') ? 'active' : ''; ?>" data-period="today">Today</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'yesterday') ? 'active' : ''; ?>" data-period="yesterday">Yesterday</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_week') ? 'active' : ''; ?>" data-period="this_week">This Week</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_month') ? 'active' : ''; ?>" data-period="this_month">This Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'last_month') ? 'active' : ''; ?>" data-period="last_month">Last Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_year') ? 'active' : ''; ?>" data-period="this_year">This Year</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'custom') ? 'active' : ''; ?>" data-period="custom">Custom</button>
                    </div>

                    <!-- Filter Inputs -->
                    <div class="rep-filter-group">
                        <div class="rep-input-wrap">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">FROM:</span>
                            <input type="date" name="date_from" id="date_from" class="rep-date-input" value="<?php echo html_escape($period['date_from']); ?>">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">TO:</span>
                            <input type="date" name="date_to" id="date_to" class="rep-date-input" value="<?php echo html_escape($period['date_to']); ?>">
                        </div>

                        <!-- Vendor Filter -->
                        <select name="vendor" class="rep-select">
                            <option value="">-- All Vendors / Payees --</option>
                            <?php if (!empty($vendor_list)): foreach ($vendor_list as $v): ?>
                                <option value="<?php echo html_escape($v->pay_to); ?>" <?php echo ($selected_vendor === $v->pay_to) ? 'selected' : ''; ?>>
                                    <?php echo html_escape($v->pay_to); ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Apply</button>
                        <a href="finance/vendorPaymentReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-money-bill-wave text-danger"></i> Total Disbursed</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['total_paid'], 2); ?></span>
                <span class="rep-kpi-sub">Across <?php echo number_format($summary['total_vouchers']); ?> vendor vouchers</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-truck text-primary"></i> Active Vendors</span>
                <span class="rep-kpi-val"><?php echo number_format($summary['total_vendors']); ?></span>
                <span class="rep-kpi-sub">Payees paid in period</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-crown text-warning"></i> Primary Vendor</span>
                <span class="rep-kpi-val text-warning" style="font-size: 17px; padding-top: 5px;">
                    <?php echo html_escape($summary['top_vendor']); ?>
                </span>
                <span class="rep-kpi-sub">Highest cumulative payments</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-receipt text-info"></i> Average Payment</span>
                <span class="rep-kpi-val text-info">
                    <?php echo ($summary['total_vouchers'] > 0) ? $currency . number_format($summary['total_paid'] / $summary['total_vouchers'], 2) : $currency . '0.00'; ?>
                </span>
                <span class="rep-kpi-sub">Per vendor voucher</span>
            </div>
        </div>

        <!-- Vendor Summary Aggregates Table -->
        <div class="rep-table-card mb-4">
            <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-users-cog text-primary mr-2"></i> Vendor Account Summary</h5>
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="vendorSummaryTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vendor / Payee Name</th>
                            <th class="text-center">Total Vouchers</th>
                            <th class="text-right">Total Paid (<?php echo trim($currency); ?>)</th>
                            <th>First Payment</th>
                            <th>Last Payment</th>
                            <th class="text-center d-print-none">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($vendor_groups)): 
                            $sl = 1;
                            foreach ($vendor_groups as $vg): 
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td class="font-weight-bold text-dark">
                                    <i class="fas fa-building text-secondary mr-1"></i> <?php echo html_escape($vg->vendor_name); ?>
                                </td>
                                <td class="text-center font-weight-bold"><?php echo number_format($vg->voucher_count); ?></td>
                                <td class="text-right font-weight-bold text-danger" style="font-size: 14.5px;">
                                    <?php echo number_format($vg->total_paid, 2); ?>
                                </td>
                                <td><?php echo !empty($vg->first_date) ? date('d/m/Y', $vg->first_date) : '-'; ?></td>
                                <td><?php echo !empty($vg->last_date) ? date('d/m/Y', $vg->last_date) : '-'; ?></td>
                                <td class="text-center d-print-none">
                                    <a href="finance/vendorPaymentReport?vendor=<?php echo urlencode($vg->vendor_name); ?>&period_type=<?php echo $period['period_type']; ?>" class="btn btn-xs btn-outline-primary" title="View details">
                                        <i class="fas fa-eye"></i> Filter Vendor
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No vendor payments found for this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Itemized Disbursements Table -->
        <div class="rep-table-card">
            <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-list text-success mr-2"></i> Itemized Vendor Vouchers</h5>
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="vendorVouchersTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Voucher #</th>
                            <th>Vendor / Payee</th>
                            <th>Category</th>
                            <th>Invoice / Ref Note</th>
                            <th class="text-center">Method</th>
                            <th class="text-right">Amount (<?php echo trim($currency); ?>)</th>
                            <th>Authorized By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            foreach ($records as $r): 
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><?php echo date('d/m/Y', $r->date); ?></td>
                                <td class="font-weight-bold">
                                    <a href="finance/expenseInvoice?id=<?php echo $r->id; ?>" target="_blank" class="text-primary">
                                        EXP-<?php echo $r->id; ?>
                                    </a>
                                </td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($r->pay_to ?: 'General Payee'); ?></td>
                                <td><span class="badge badge-light border"><?php echo html_escape($r->category ?: 'General'); ?></span></td>
                                <td><small class="text-muted"><?php echo html_escape($r->note ?: '-'); ?></small></td>
                                <td class="text-center"><span class="badge badge-secondary px-2 py-1"><?php echo html_escape($r->payment_method); ?></span></td>
                                <td class="text-right font-weight-bold text-danger"><?php echo number_format($r->amount, 2); ?></td>
                                <td><small class="text-muted"><?php echo html_escape($r->creator_name); ?></small></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">No itemized vouchers found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="7" class="text-right font-weight-bold">Total Paid:</td>
                            <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_paid'], 2); ?></td>
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
                        <strong>Procurement Officer</strong>
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

    if ($.fn.DataTable && $('#vendorSummaryTable tbody tr td').length > 1) {
        $('#vendorSummaryTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 10,
            order: [[3, 'desc']]
        });
    }

    if ($.fn.DataTable && $('#vendorVouchersTable tbody tr td').length > 1) {
        $('#vendorVouchersTable').DataTable({
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
