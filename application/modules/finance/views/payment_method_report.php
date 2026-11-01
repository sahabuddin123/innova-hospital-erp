<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳';
$period_label = !empty($period['period_label']) ? $period['period_label'] : 'Selected Period';
?>
<link href="common/extranal/css/finance/collection_reports.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Payment Method Collection Report &bull; <?php echo html_escape($period_label); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-wallet text-success"></i> Payment Method Collection Report</h2>
                    <p>Collections by payment channel (Cash, bKash, Nagad, Rocket, Bank, Card, Others) &bull; <span class="font-weight-bold text-dark"><?php echo html_escape($period_label); ?></span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/paymentMethodReport" class="m-0">
                <input type="hidden" name="period_type" id="period_type_input" value="<?php echo html_escape($period['period_type']); ?>">
                <div class="rep-controls-row">
                    <div class="rep-period-pills">
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'today') ? 'active' : ''; ?>" data-period="today">Today</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'yesterday') ? 'active' : ''; ?>" data-period="yesterday">Yesterday</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_week') ? 'active' : ''; ?>" data-period="this_week">This Week</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_month') ? 'active' : ''; ?>" data-period="this_month">This Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'last_month') ? 'active' : ''; ?>" data-period="last_month">Last Month</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'this_year') ? 'active' : ''; ?>" data-period="this_year">This Year</button>
                        <button type="button" class="rep-period-btn <?php echo ($period['period_type'] === 'custom') ? 'active' : ''; ?>" data-period="custom">Custom</button>
                    </div>

                    <div class="rep-filter-group">
                        <div class="rep-input-wrap">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">FROM:</span>
                            <input type="date" name="date_from" class="rep-date-input" value="<?php echo html_escape($period['date_from']); ?>">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">TO:</span>
                            <input type="date" name="date_to" class="rep-date-input" value="<?php echo html_escape($period['date_to']); ?>">
                        </div>
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
                <div class="rep-summary-meta">Deposits Processed</div>
            </div>

            <div class="rep-summary-card rep-card-purple">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Active Methods</span>
                    <div class="rep-summary-icon"><i class="fas fa-network-wired"></i></div>
                </div>
                <div class="rep-summary-value"><?php echo count($records); ?> Channels</div>
                <div class="rep-summary-meta">Configured Gateways</div>
            </div>

            <div class="rep-summary-card rep-card-green" style="border: 2px solid #10b981;">
                <div class="rep-summary-top">
                    <span class="rep-summary-label">Total Collection</span>
                    <div class="rep-summary-icon" style="background:#d1fae5; color:#047857;"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="rep-summary-value text-success"><?php echo $currency; ?> <?php echo number_format($summary['total_collection'], 2); ?></div>
                <div class="rep-summary-meta font-weight-bold">100% Reconciled</div>
            </div>
        </div>

        <!-- Chart & Table Grid -->
        <div class="row">
            <!-- Visual Distribution Chart -->
            <div class="col-lg-5 mb-4">
                <div class="rep-table-card h-100">
                    <div class="rep-table-header">
                        <h3><i class="fas fa-chart-pie text-success"></i> Payment Channel Distribution</h3>
                    </div>
                    <div style="height: 280px; position: relative;">
                        <canvas id="paymentDistributionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Detailed Table -->
            <div class="col-lg-7 mb-4">
                <div class="rep-table-card h-100">
                    <div class="rep-table-header">
                        <h3><i class="fas fa-list-ul text-primary"></i> Payment Channel Breakdown</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover rep-table" id="payment_method_table">
                            <thead>
                                <tr>
                                    <th>Payment Method</th>
                                    <th class="text-center">Transaction Count</th>
                                    <th class="text-right">Total Amount</th>
                                    <th class="text-right">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($records)): foreach ($records as $m): 
                                    $m_badge = 'rep-badge-cash';
                                    if (in_array(strtolower($m['method']), ['bkash', 'nagad', 'rocket', 'card', 'online'])) {
                                        $m_badge = 'rep-badge-online';
                                    } elseif (strtolower($m['method']) === 'bank') {
                                        $m_badge = 'rep-badge-bank';
                                    }
                                ?>
                                <tr>
                                    <td class="font-weight-bold text-dark">
                                        <span class="rep-badge <?php echo $m_badge; ?> mr-2"><?php echo html_escape($m['method']); ?></span>
                                    </td>
                                    <td class="text-center font-weight-bold"><?php echo number_format($m['tx_count']); ?></td>
                                    <td class="text-right font-weight-bold text-success"><?php echo $currency; ?> <?php echo number_format($m['total_amount'], 2); ?></td>
                                    <td class="text-right font-weight-bold text-primary"><?php echo $m['percentage']; ?>%</td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No transactions recorded for this period</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total Collection:</th>
                                    <th class="text-center"><?php echo number_format($summary['total_transactions']); ?></th>
                                    <th class="text-right text-success"><?php echo $currency; ?> <?php echo number_format($summary['total_collection'], 2); ?></th>
                                    <th class="text-right text-primary">100.0%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    $('#payment_method_table').DataTable({
        responsive: true,
        dom: "<'row'<'col-md-6'l><'col-md-6 text-right'B>>" +
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

    // Chart.js Donut
    var ctx = document.getElementById('paymentDistributionChart');
    if (ctx) {
        var labels = <?php echo json_encode($chart_data['labels'] ?? []); ?>;
        var dataValues = <?php echo json_encode($chart_data['data'] ?? []); ?>;
        var colors = ['#10b981', '#3b82f6', '#e11d48', '#f97316', '#8b5cf6', '#06b6d4', '#64748b'];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var val = context.parsed;
                                return context.label + ': ' + '<?php echo $currency; ?> ' + val.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

