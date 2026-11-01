<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
?>
<link href="common/extranal/css/finance/collection_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Partial Payment & Installment Ledger &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-layer-group text-primary"></i> Partial Payment & Installment Ledger</h2>
                    <p>Detailed sequence of partial collections, previous balances, and remaining dues &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/partialPaymentReport" class="m-0">
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

                    <!-- Date & Invoice Filter Group -->
                    <div class="rep-filter-group">
                        <div class="rep-input-wrap">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">FROM:</span>
                            <input type="date" name="date_from" id="date_from" class="rep-date-input" value="<?php echo html_escape($period['date_from']); ?>">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">TO:</span>
                            <input type="date" name="date_to" id="date_to" class="rep-date-input" value="<?php echo html_escape($period['date_to']); ?>">
                        </div>

                        <div class="rep-input-wrap">
                            <input type="text" name="invoice_id" class="rep-select" placeholder="Invoice # (optional)" value="<?php echo html_escape($invoice_id ?? ''); ?>" style="min-width: 130px;">
                        </div>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Filter</button>
                        <a href="finance/partialPaymentReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card success">
                <span class="rep-kpi-label"><i class="fas fa-check-circle text-success"></i> Total Installment Collections</span>
                <span class="rep-kpi-val text-success"><?php echo $currency . number_format($summary['total_amount'], 2); ?></span>
                <span class="rep-kpi-sub">Actual cash & digital deposits</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-receipt text-primary"></i> Total Payments Logged</span>
                <span class="rep-kpi-val"><?php echo number_format($summary['total_installments']); ?></span>
                <span class="rep-kpi-sub">Across <?php echo number_format($summary['invoices_count']); ?> unique invoices</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-chart-line text-info"></i> Invoices Serviced</span>
                <span class="rep-kpi-val"><?php echo number_format($summary['invoices_count']); ?></span>
                <span class="rep-kpi-sub">Receiving partial settlements</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-calculator text-warning"></i> Average Payment Size</span>
                <span class="rep-kpi-val"><?php echo $currency . number_format($summary['average_installment'], 2); ?></span>
                <span class="rep-kpi-sub">Per deposit transaction</span>
            </div>
        </div>

        <!-- Installment Records Table Card -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="partialPaymentTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date & Time</th>
                            <th>Invoice #</th>
                            <th class="text-center">Installment</th>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th class="text-right">Net Bill (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Previous Due (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Amount Paid (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Remaining Due (<?php echo trim($currency); ?>)</th>
                            <th class="text-center">Payment Method</th>
                            <th>Collected By</th>
                            <th class="text-center d-print-none">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            foreach ($records as $r): 
                                $pt_name = !empty($r->pt_name) ? $r->pt_name : (!empty($r->p_name) ? $r->p_name : 'Unknown');
                                $pt_code = !empty($r->pt_code) ? $r->pt_code : (!empty($r->patient) ? 'P-'.$r->patient : '-');
                                $method = !empty($r->deposit_type) ? trim($r->deposit_type) : 'Cash';

                                $badge_class = 'badge-secondary';
                                if (strcasecmp($method, 'cash') === 0) $badge_class = 'badge-success';
                                elseif (strcasecmp($method, 'bkash') === 0) $badge_class = 'badge-danger';
                                elseif (strcasecmp($method, 'nagad') === 0) $badge_class = 'badge-warning text-dark';
                                elseif (strcasecmp($method, 'card') === 0) $badge_class = 'badge-info';
                                elseif (strcasecmp($method, 'bank') === 0) $badge_class = 'badge-primary';
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><?php echo date('d/m/Y h:i A', $r->date); ?></td>
                                <td class="font-weight-bold">
                                    <a href="finance/invoice?id=<?php echo $r->payment_id; ?>" target="_blank" class="text-primary">
                                        #<?php echo $r->payment_id; ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border px-2 py-1 font-weight-bold text-dark">
                                        #<?php echo $r->installment_no; ?>
                                    </span>
                                </td>
                                <td><span class="badge badge-light border"><?php echo html_escape($pt_code); ?></span></td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($pt_name); ?></td>
                                <td class="text-right"><?php echo number_format($r->invoice_net, 2); ?></td>
                                <td class="text-right text-muted"><?php echo number_format($r->previous_due, 2); ?></td>
                                <td class="text-right text-success font-weight-bold" style="font-size: 14px;">
                                    <?php echo number_format($r->deposited_amount, 2); ?>
                                </td>
                                <td class="text-right font-weight-bold <?php echo ($r->remaining_due > 0) ? 'text-danger' : 'text-muted'; ?>">
                                    <?php echo number_format($r->remaining_due, 2); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?php echo $badge_class; ?> px-2 py-1"><?php echo html_escape($method); ?></span>
                                </td>
                                <td><small class="text-muted"><i class="fas fa-user-check"></i> <?php echo html_escape($r->cashier_name); ?></small></td>
                                <td class="text-center d-print-none">
                                    <a href="finance/invoice?id=<?php echo $r->payment_id; ?>" target="_blank" class="btn btn-xs btn-outline-primary" title="View Invoice">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="13" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox text-muted fa-3x mb-2 d-block"></i>
                                    <strong>No partial payments recorded in the selected period.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="8" class="text-right font-weight-bold">Grand Total (<?php echo count($records); ?> deposits):</td>
                            <td class="text-right font-weight-bold text-success"><?php echo number_format($summary['total_amount'], 2); ?></td>
                            <td colspan="4"></td>
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
                        <strong>Cashier Signature</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Chief Accountant</strong>
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

    if ($.fn.DataTable && $('#partialPaymentTable tbody tr td').length > 1) {
        $('#partialPaymentTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[1, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search patient, invoice, cashier..."
            }
        });
    }
});
</script>

