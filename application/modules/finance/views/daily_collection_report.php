<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
?>
<link href="common/extranal/css/finance/collection_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header (Only Visible in Print) -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Daily Collection Report &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-hand-holding-usd text-primary"></i> Daily Collection Report</h2>
                    <p>Cash and digital payment receipts collected from patients &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'daily_collection']); ?>
                </div>
            </div>

            <form method="GET" action="finance/dailyCollectionReport" class="m-0">
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

                        <!-- Cashier Filter -->
                        <select name="cashier_id" class="rep-select">
                            <option value="">-- All Cashiers --</option>
                            <?php if (!empty($cashiers)): foreach ($cashiers as $c): ?>
                                <option value="<?php echo $c->id; ?>" <?php echo ($selected_cashier == $c->id) ? 'selected' : ''; ?>>
                                    <?php echo html_escape(!empty($c->first_name) ? $c->first_name . ' ' . $c->last_name : $c->username); ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>

                        <!-- Method Filter -->
                        <select name="deposit_type" class="rep-select">
                            <option value="">-- All Payment Methods --</option>
                            <option value="Cash" <?php echo ($selected_deposit_type === 'Cash') ? 'selected' : ''; ?>>Cash</option>
                            <option value="Online" <?php echo ($selected_deposit_type === 'Online') ? 'selected' : ''; ?>>Digital / Online (bKash/Nagad/Card)</option>
                            <option value="Bank" <?php echo ($selected_deposit_type === 'Bank') ? 'selected' : ''; ?>>Bank Transfer</option>
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
                    <span class="rep-summary-label">Total Transactions</span>
                    <div class="rep-summary-icon"><i class="fas fa-receipt"></i></div>
                </div>
                <div class="rep-summary-value"><?php echo number_format($summary['total_transactions']); ?></div>
                <div class="rep-summary-meta">Deposits Received</div>
            </div>

            <div class="rep-summary-card rep-card-green">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Cash Collection</span>
                    <div class="rep-summary-icon"><i class="fas fa-money-bill-wave"></i></div>
                </div>
                <div class="rep-summary-value"><?php echo $currency; ?> <?php echo number_format($summary['cash_collection'], 2); ?></div>
                <div class="rep-summary-meta">Physical Counter Cash</div>
            </div>

            <div class="rep-summary-card rep-card-purple">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Online / Digital</span>
                    <div class="rep-summary-icon"><i class="fas fa-mobile-alt"></i></div>
                </div>
                <div class="rep-summary-value"><?php echo $currency; ?> <?php echo number_format($summary['online_collection'], 2); ?></div>
                <div class="rep-summary-meta">bKash, Nagad, Rocket, Card</div>
            </div>

            <div class="rep-summary-card rep-card-teal">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Bank Collection</span>
                    <div class="rep-summary-icon"><i class="fas fa-university"></i></div>
                </div>
                <div class="rep-summary-value"><?php echo $currency; ?> <?php echo number_format($summary['bank_collection'], 2); ?></div>
                <div class="rep-summary-meta">Direct Bank Deposits</div>
            </div>

            <div class="rep-summary-card rep-card-green" style="border: 2px solid #10b981;">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Total Collection</span>
                    <div class="rep-summary-icon" style="background:#d1fae5; color:#047857;"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="rep-summary-value text-success"><?php echo $currency; ?> <?php echo number_format($summary['total_collection'], 2); ?></div>
                <div class="rep-summary-meta font-weight-bold">Cleared Net Inflow</div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="rep-table-card">
            <div class="rep-table-header">
                <h3><i class="fas fa-list-alt text-primary"></i> Daily Collections Transaction Log</h3>
                <span class="badge badge-light border px-3 py-2 text-muted"><?php echo count($records); ?> Entries Found</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover rep-table" id="daily_collection_table">
                    <thead>
                        <tr>
                            <th>Date &amp; Time</th>
                            <th>Invoice #</th>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th>Mobile</th>
                            <th>Method</th>
                            <th>Trans Ref</th>
                            <th class="text-right">Amount Received</th>
                            <th>Collected By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): foreach ($records as $r): 
                            $m_badge = 'rep-badge-cash';
                            if (in_array(strtolower($r->deposit_type ?? ''), ['bkash', 'nagad', 'rocket', 'card', 'online'])) {
                                $m_badge = 'rep-badge-online';
                            } elseif (strtolower($r->deposit_type ?? '') === 'bank') {
                                $m_badge = 'rep-badge-bank';
                            }
                            $p_name = !empty($r->pt_name) ? $r->pt_name : (!empty($r->p_name) ? $r->p_name : 'Walk-in Patient');
                            $p_phone = !empty($r->pt_phone) ? $r->pt_phone : (!empty($r->p_phone) ? $r->p_phone : '-');
                            $p_code = !empty($r->pt_code) ? $r->pt_code : (!empty($r->patient) ? $r->patient : '-');
                        ?>
                        <tr>
                            <td><?php echo date('d/m/Y h:i A', $r->date); ?></td>
                            <td>
                                <?php if (!empty($r->invoice_no)): ?>
                                    <a href="finance/invoice?id=<?php echo $r->invoice_no; ?>" target="_blank" class="font-weight-bold text-primary">
                                        #<?php echo $r->invoice_no; ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Advance</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo html_escape($p_code); ?></td>
                            <td class="font-weight-bold text-dark"><?php echo html_escape($p_name); ?></td>
                            <td><?php echo html_escape($p_phone); ?></td>
                            <td>
                                <span class="rep-badge <?php echo $m_badge; ?>">
                                    <?php echo html_escape(!empty($r->deposit_type) ? $r->deposit_type : 'Cash'); ?>
                                </span>
                            </td>
                            <td><?php echo html_escape(!empty($r->gateway) ? $r->gateway : '-'); ?></td>
                            <td class="text-right font-weight-bold text-success"><?php echo $currency; ?> <?php echo number_format($r->deposited_amount, 2); ?></td>
                            <td><i class="far fa-user text-muted mr-1"></i><?php echo html_escape($r->cashier_name ?: 'System'); ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">No collections recorded for the selected criteria</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-right">Grand Total Collection:</th>
                            <th class="text-right text-success"><?php echo $currency; ?> <?php echo number_format($summary['total_collection'], 2); ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Print Signature Block -->
            <div class="print-signature-area d-none d-print-flex">
                <div class="signature-box">Prepared By (Cashier)</div>
                <div class="signature-box">Verified By (Accounts)</div>
                <div class="signature-box">Hospital Authority</div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    $('#daily_collection_table').DataTable({
        responsive: true,
        dom: "<'row'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5', 'print'],
        aLengthMenu: [[15, 25, 50, 100, -1], [15, 25, 50, 100, "All"]],
        iDisplayLength: 25,
        order: [[0, 'desc']]
    });

    $('.rep-period-btn').on('click', function(e) {
        e.preventDefault();
        var period = $(this).data('period');
        $('#period_type_input').val(period);
        $(this).closest('form').submit();
    });
});
</script>

