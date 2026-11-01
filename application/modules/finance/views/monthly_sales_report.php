<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Year ' . $year;
?>
<link href="common/extranal/css/finance/collection_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Monthly Sales &amp; Revenue Report &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-calendar-alt text-primary"></i> Monthly Sales Report</h2>
                    <p>Month-by-month billing, collections, dues, and refunds audit &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/monthlySalesReport" class="m-0">
                <input type="hidden" name="period_type" id="period_type_input" value="<?php echo html_escape($period['period_type']); ?>">
                <div class="rep-controls-row">
                    <div class="rep-period-pills">
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_year') ? 'active' : ''; ?>" data-period="this_year">This Year</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_month') ? 'active' : ''; ?>" data-period="this_month">This Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'last_month') ? 'active' : ''; ?>" data-period="last_month">Last Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'custom') ? 'active' : ''; ?>" data-period="custom">Custom Range</button>
                    </div>

                    <div class="rep-filter-group">
                        <div class="rep-input-wrap">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">FROM:</span>
                            <input type="date" name="date_from" class="rep-date-input" value="<?php echo html_escape($period['date_from']); ?>">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">TO:</span>
                            <input type="date" name="date_to" class="rep-date-input" value="<?php echo html_escape($period['date_to']); ?>">
                        </div>

                        <select name="year" class="rep-select">
                            <?php 
                            $c_yr = date('Y');
                            for ($y = $c_yr; $y >= $c_yr - 5; $y--): ?>
                                <option value="<?php echo $y; ?>" <?php echo ($year == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>

                        <button type="submit" class="rep-btn-submit"><i class="fas fa-filter"></i> Apply Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="rep-summary-grid">
            <div class="rep-summary-card rep-card-blue">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Invoice Count</span>
                    <div class="rep-summary-icon"><i class="fas fa-file-invoice"></i></div>
                </div>
                <div class="rep-summary-value"><?php echo number_format($summary['invoice_count']); ?></div>
                <div class="rep-summary-meta">Completed Invoices</div>
            </div>

            <div class="rep-summary-card rep-card-teal">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Gross Billing</span>
                    <div class="rep-summary-icon"><i class="fas fa-calculator"></i></div>
                </div>
                <div class="rep-summary-value"><?php echo $currency; ?> <?php echo number_format($summary['gross_billing'], 2); ?></div>
                <div class="rep-summary-meta">Before Concessions</div>
            </div>

            <div class="rep-summary-card rep-card-red">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Total Discount</span>
                    <div class="rep-summary-icon"><i class="fas fa-tags"></i></div>
                </div>
                <div class="rep-summary-value text-danger">- <?php echo $currency; ?> <?php echo number_format($summary['total_discount'], 2); ?></div>
                <div class="rep-summary-meta">Discounts Granted</div>
            </div>

            <div class="rep-summary-card rep-card-blue">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Net Payable</span>
                    <div class="rep-summary-icon"><i class="fas fa-receipt"></i></div>
                </div>
                <div class="rep-summary-value"><?php echo $currency; ?> <?php echo number_format($summary['net_payable'], 2); ?></div>
                <div class="rep-summary-meta">Gross - Discount</div>
            </div>

            <div class="rep-summary-card rep-card-green">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Total Collection</span>
                    <div class="rep-summary-icon"><i class="fas fa-hand-holding-usd"></i></div>
                </div>
                <div class="rep-summary-value text-success"><?php echo $currency; ?> <?php echo number_format($summary['total_collection'], 2); ?></div>
                <div class="rep-summary-meta">Actual Received Cash</div>
            </div>

            <div class="rep-summary-card rep-card-amber">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Outstanding Due</span>
                    <div class="rep-summary-icon"><i class="fas fa-exclamation-circle"></i></div>
                </div>
                <div class="rep-summary-value text-warning"><?php echo $currency; ?> <?php echo number_format($summary['outstanding_due'], 2); ?></div>
                <div class="rep-summary-meta">Uncollected Receivables</div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="rep-table-card">
            <div class="rep-table-header">
                <h3><i class="fas fa-table text-primary"></i> Monthly Sales Performance Matrix</h3>
            </div>

            <div class="table-responsive">
                <table class="table table-hover rep-table" id="monthly_sales_table">
                    <thead>
                        <tr>
                            <th>Month / Period</th>
                            <th class="text-center">Invoices</th>
                            <th class="text-right">Gross Billing</th>
                            <th class="text-right">Total Discount</th>
                            <th class="text-right">Net Payable</th>
                            <th class="text-right">Total Collection</th>
                            <th class="text-right">Outstanding Due</th>
                            <th class="text-right">Refund Amount</th>
                            <th class="text-right">Net Collection</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): foreach ($records as $m): ?>
                        <tr>
                            <td class="font-weight-bold text-dark"><?php echo html_escape($m['month_name']); ?></td>
                            <td class="text-center font-weight-bold"><?php echo number_format($m['invoice_count']); ?></td>
                            <td class="text-right"><?php echo $currency; ?> <?php echo number_format($m['gross_billing'], 2); ?></td>
                            <td class="text-right text-danger">- <?php echo $currency; ?> <?php echo number_format($m['total_discount'], 2); ?></td>
                            <td class="text-right font-weight-bold text-dark"><?php echo $currency; ?> <?php echo number_format($m['net_payable'], 2); ?></td>
                            <td class="text-right text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($m['total_collection'], 2); ?></td>
                            <td class="text-right text-warning font-weight-bold"><?php echo $currency; ?> <?php echo number_format($m['outstanding_due'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency; ?> <?php echo number_format($m['refund_amount'], 2); ?></td>
                            <td class="text-right font-weight-bold text-primary"><?php echo $currency; ?> <?php echo number_format($m['net_collection'], 2); ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">No sales records found for this period</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total:</th>
                            <th class="text-center"><?php echo number_format($summary['invoice_count']); ?></th>
                            <th class="text-right"><?php echo $currency; ?> <?php echo number_format($summary['gross_billing'], 2); ?></th>
                            <th class="text-right text-danger">- <?php echo $currency; ?> <?php echo number_format($summary['total_discount'], 2); ?></th>
                            <th class="text-right text-dark"><?php echo $currency; ?> <?php echo number_format($summary['net_payable'], 2); ?></th>
                            <th class="text-right text-success"><?php echo $currency; ?> <?php echo number_format($summary['total_collection'], 2); ?></th>
                            <th class="text-right text-warning"><?php echo $currency; ?> <?php echo number_format($summary['outstanding_due'], 2); ?></th>
                            <th class="text-right text-danger"><?php echo $currency; ?> <?php echo number_format($summary['refund_amount'], 2); ?></th>
                            <th class="text-right text-primary"><?php echo $currency; ?> <?php echo number_format($summary['net_collection'], 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Print Signature Block -->
            <div class="print-signature-area d-none d-print-flex">
                <div class="signature-box">Prepared By (Accounts)</div>
                <div class="signature-box">Audited By (Finance)</div>
                <div class="signature-box">Director / Authority</div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    $('#monthly_sales_table').DataTable({
        responsive: true,
        dom: "<'row'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5', 'print'],
        paging: false,
        ordering: false
    });

    $('.rep-period-btn').on('click', function(e) {
        e.preventDefault();
        var period = $(this).data('period');
        $('#period_type_input').val(period);
        $(this).closest('form').submit();
    });
});
</script>

