<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
$selected_user = !empty($user_id) ? $user_id : '';
?>
<link href="common/extranal/css/finance/collection_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Discount & Concession Audit Report &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-tags text-warning"></i> Discount & Concession Audit Report</h2>
                    <p>Audit of all discounts granted with Referrer-absorbed vs Hospital-absorbed rule breakdown &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/discountReport" class="m-0">
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

                    <!-- Date & Cashier Filter Group -->
                    <div class="rep-filter-group">
                        <div class="rep-input-wrap">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">FROM:</span>
                            <input type="date" name="date_from" id="date_from" class="rep-date-input" value="<?php echo html_escape($period['date_from']); ?>">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">TO:</span>
                            <input type="date" name="date_to" id="date_to" class="rep-date-input" value="<?php echo html_escape($period['date_to']); ?>">
                        </div>

                        <!-- Authorized Cashier Filter -->
                        <select name="user_id" class="rep-select">
                            <option value="">-- All Cashiers --</option>
                            <?php if (!empty($cashiers)): foreach ($cashiers as $c): ?>
                                <option value="<?php echo $c->id; ?>" <?php echo ($selected_user == $c->id) ? 'selected' : ''; ?>>
                                    <?php echo html_escape($c->username); ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Filter</button>
                        <a href="finance/discountReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-percentage text-danger"></i> Total Discounts Granted</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['total_discount'], 2); ?></span>
                <span class="rep-kpi-sub">Across <?php echo number_format($summary['total_discounted_invoices']); ?> invoices (Avg: <?php echo $summary['average_discount_pct']; ?>%)</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-user-md text-warning"></i> Referrer-Absorbed</span>
                <span class="rep-kpi-val text-warning"><?php echo $currency . number_format($summary['referrer_absorbed'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo ($summary['total_discount'] > 0) ? round(($summary['referrer_absorbed'] / $summary['total_discount']) * 100, 1) : 0; ?>% deducted from doctor wallet</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-hospital text-info"></i> Hospital-Absorbed</span>
                <span class="rep-kpi-val text-info"><?php echo $currency . number_format($summary['hospital_absorbed'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo ($summary['total_discount'] > 0) ? round(($summary['hospital_absorbed'] / $summary['total_discount']) * 100, 1) : 0; ?>% hospital courtesy concession</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-file-invoice-dollar text-primary"></i> Gross Billed Amount</span>
                <span class="rep-kpi-val"><?php echo $currency . number_format($summary['total_gross'], 2); ?></span>
                <span class="rep-kpi-sub">Total catalog value of discounted tests</span>
            </div>
        </div>

        <!-- Table Card -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="discountReportTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th>Referrer Doctor</th>
                            <th class="text-right">Gross (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Discount (<?php echo trim($currency); ?>)</th>
                            <th class="text-center">Disc %</th>
                            <th class="text-right">Net Payable (<?php echo trim($currency); ?>)</th>
                            <th class="text-center">Discount Absorbed By</th>
                            <th>Authorized By</th>
                            <th class="text-center d-print-none">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            foreach ($records as $r): 
                                $pt_name = !empty($r->pt_name) ? $r->pt_name : (!empty($r->patient_name) ? $r->patient_name : 'Unknown');
                                $pt_code = !empty($r->pt_code) ? $r->pt_code : (!empty($r->patient) ? 'P-'.$r->patient : '-');
                                $ref_display = !empty($r->ref_name) ? $r->ref_name : (!empty($r->referrer_name) ? $r->referrer_name : 'None (Self)');

                                $is_ref = ($r->absorbed_type === 'referrer');
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td class="font-weight-bold">
                                    <a href="finance/invoice?id=<?php echo $r->id; ?>" target="_blank" class="text-primary">
                                        #<?php echo $r->id; ?>
                                    </a>
                                </td>
                                <td><?php echo date('d/m/Y', $r->date); ?></td>
                                <td><span class="badge badge-light border"><?php echo html_escape($pt_code); ?></span></td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($pt_name); ?></td>
                                <td><small class="text-dark font-weight-bold"><?php echo html_escape($ref_display); ?></small></td>
                                <td class="text-right"><?php echo number_format($r->gross_amount, 2); ?></td>
                                <td class="text-right text-danger font-weight-bold" style="font-size: 14px;">
                                    <?php echo number_format($r->discount_amount, 2); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border font-weight-bold"><?php echo $r->discount_percentage; ?>%</span>
                                </td>
                                <td class="text-right font-weight-bold"><?php echo number_format($r->net_payable, 2); ?></td>
                                <td class="text-center">
                                    <?php if ($is_ref): ?>
                                        <span class="badge badge-warning text-dark px-2 py-1" title="Deducted from referrer commission entitlement">
                                            <i class="fas fa-user-md"></i> Referrer Deducted
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-info px-2 py-1" title="Hospital absorbed full discount">
                                            <i class="fas fa-hospital"></i> Hospital Absorbed
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><i class="fas fa-user-check"></i> <?php echo html_escape($r->authorized_by); ?></small></td>
                                <td class="text-center d-print-none">
                                    <a href="finance/invoice?id=<?php echo $r->id; ?>" target="_blank" class="btn btn-xs btn-outline-primary" title="View Invoice">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="13" class="text-center text-muted py-5">
                                    <i class="fas fa-tags text-muted fa-3x mb-2 d-block"></i>
                                    <strong>No discounted invoices found in the selected period.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="6" class="text-right font-weight-bold">Grand Total (<?php echo count($records); ?> invoices):</td>
                            <td class="text-right font-weight-bold"><?php echo number_format($summary['total_gross'], 2); ?></td>
                            <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_discount'], 2); ?></td>
                            <td class="text-center font-weight-bold text-muted"><?php echo $summary['average_discount_pct']; ?>%</td>
                            <td class="text-right font-weight-bold">-</td>
                            <td colspan="3"></td>
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
                        <strong>Audit Officer</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Finance Manager</strong>
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

    if ($.fn.DataTable && $('#discountReportTable tbody tr td').length > 1) {
        $('#discountReportTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[7, 'desc']], // Highest discount first
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search patient, referrer, cashier..."
            }
        });
    }
});
</script>

