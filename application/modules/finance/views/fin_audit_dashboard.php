<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-light">
    <section class="content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-shield-alt text-primary mr-2"></i><?php echo lang('financial_audit_dashboard'); ?>
                    </h1>
                    <p class="text-muted small mb-0">Unified financial activity tracking, risk monitoring, and forensic event intelligence</p>
                </div>
                <div class="col-sm-6 text-right">
                    <span class="badge badge-info p-2 font-weight-normal"><i class="fas fa-calendar mr-1"></i> Period: <?php echo html_escape($start_date); ?> to <?php echo html_escape($end_date); ?></span>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Navigation Sub-Bar -->
            <?php $this->load->view('finance/inc_financial_audit_nav'); ?>

            <!-- Filter Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body py-2">
                    <form method="get" action="finance/financialAuditDashboard" class="form-inline flex-wrap gap-2">
                        <label class="mr-2 font-weight-bold small text-muted">From:</label>
                        <input type="date" name="start_date" value="<?php echo html_escape($start_date); ?>" class="form-control form-control-sm mr-3">
                        <label class="mr-2 font-weight-bold small text-muted">To:</label>
                        <input type="date" name="end_date" value="<?php echo html_escape($end_date); ?>" class="form-control form-control-sm mr-3">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-filter mr-1"></i> Apply Filter
                        </button>
                        <a href="finance/financialAuditDashboard" class="btn btn-sm btn-outline-secondary ml-2">Reset</a>
                    </form>
                </div>
            </div>

            <!-- KPI Cards Grid -->
            <?php $kpi = $dashboard['kpi'] ?? []; ?>
            <div class="acc-kpi-grid">
                <div class="acc-kpi-card card-blue">
                    <div class="acc-kpi-label"><i class="fas fa-list-ol mr-1"></i> <?php echo lang('total_financial_events'); ?></div>
                    <div class="acc-kpi-val"><?php echo number_format($kpi['total_events'] ?? 0); ?></div>
                    <div class="acc-kpi-sub">Total Audited Volume: ৳<?php echo number_format($kpi['total_audited_volume'] ?? 0, 2); ?></div>
                </div>
                <div class="acc-kpi-card card-teal">
                    <div class="acc-kpi-label"><i class="fas fa-file-invoice mr-1"></i> <?php echo lang('invoice_changes'); ?></div>
                    <div class="acc-kpi-val"><?php echo number_format($kpi['invoice_events'] ?? 0); ?></div>
                    <div class="acc-kpi-sub">Billing creation & modifications</div>
                </div>
                <div class="acc-kpi-card card-green">
                    <div class="acc-kpi-label"><i class="fas fa-hand-holding-usd mr-1"></i> <?php echo lang('payment_changes'); ?></div>
                    <div class="acc-kpi-val"><?php echo number_format($kpi['payment_events'] ?? 0); ?></div>
                    <div class="acc-kpi-sub">Collections & deposits</div>
                </div>
                <div class="acc-kpi-card card-purple">
                    <div class="acc-kpi-label"><i class="fas fa-undo mr-1"></i> <?php echo lang('refund_events'); ?></div>
                    <div class="acc-kpi-val"><?php echo number_format($kpi['refund_events'] ?? 0); ?></div>
                    <div class="acc-kpi-sub">Patient refunds processed</div>
                </div>
                <div class="acc-kpi-card card-red">
                    <div class="acc-kpi-label"><i class="fas fa-receipt mr-1"></i> <?php echo lang('expense_changes'); ?></div>
                    <div class="acc-kpi-val"><?php echo number_format($kpi['expense_events'] ?? 0); ?></div>
                    <div class="acc-kpi-sub">Vouchers & payments</div>
                </div>
                <div class="acc-kpi-card card-amber">
                    <div class="acc-kpi-label"><i class="fas fa-user-md mr-1"></i> <?php echo lang('commission_changes'); ?></div>
                    <div class="acc-kpi-val"><?php echo number_format($kpi['commission_events'] ?? 0); ?></div>
                    <div class="acc-kpi-sub">Doctor & referral credits</div>
                </div>
                <div class="acc-kpi-card card-cyan">
                    <div class="acc-kpi-label"><i class="fas fa-wallet mr-1"></i> <?php echo lang('wallet_transactions'); ?></div>
                    <div class="acc-kpi-val"><?php echo number_format($kpi['wallet_events'] ?? 0); ?></div>
                    <div class="acc-kpi-sub">Withdrawals & adjustments</div>
                </div>
                <div class="acc-kpi-card card-dark">
                    <div class="acc-kpi-label"><i class="fas fa-history mr-1"></i> <?php echo lang('reversals'); ?></div>
                    <div class="acc-kpi-val"><?php echo number_format($kpi['reversal_events'] ?? 0); ?></div>
                    <div class="acc-kpi-sub">Reversals & cancelled items</div>
                </div>
                <div class="acc-kpi-card card-red" style="border-left: 4px solid #dc3545;">
                    <div class="acc-kpi-label text-danger font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i> <?php echo lang('suspicious_changes'); ?></div>
                    <div class="acc-kpi-val text-danger"><?php echo number_format($kpi['suspicious_events'] ?? 0); ?></div>
                    <div class="acc-kpi-sub">Flagged anomalies & high risks</div>
                </div>
            </div>

            <!-- Analytics Charts Section -->
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-chart-area text-primary mr-2"></i>Daily Financial Events & Transaction Volume</h6>
                            <span class="badge badge-light border">Daily Aggregation</span>
                        </div>
                        <div class="card-body">
                            <div style="height: 280px; position: relative;">
                                <canvas id="trendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-chart-pie text-success mr-2"></i>Module Breakdown</h6>
                            <span class="badge badge-light border">By Volume</span>
                        </div>
                        <div class="card-body">
                            <div style="height: 280px; position: relative;">
                                <canvas id="moduleChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Suspicious Activities Table -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-circle mr-2"></i>Flagged Suspicious Financial Events (Forensic Alert)
                    </h6>
                    <a href="finance/financialAuditTrail?is_suspicious=1" class="btn btn-xs btn-outline-danger font-weight-bold">View All Suspicious Events</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Audit UUID</th>
                                    <th>Date & Time</th>
                                    <th>Module</th>
                                    <th>Action</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>User</th>
                                    <th>Flag Reason</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dashboard['suspicious_list'])): ?>
                                    <?php foreach ($dashboard['suspicious_list'] as $s): ?>
                                        <tr>
                                            <td><code class="small"><?php echo html_escape(substr($s['audit_uuid'], 0, 16)); ?>...</code></td>
                                            <td><?php echo html_escape($s['created_at_formatted']); ?></td>
                                            <td><span class="badge badge-light border text-uppercase font-weight-bold"><?php echo html_escape($s['module_name']); ?></span></td>
                                            <td>
                                                <span class="badge badge-action-<?php echo strtolower($s['action_type']); ?>">
                                                    <?php echo html_escape($s['action_type']); ?>
                                                </span>
                                            </td>
                                            <td><strong><?php echo html_escape($s['transaction_reference'] ?: '-' ); ?></strong></td>
                                            <td class="font-weight-bold text-dark">৳<?php echo number_format($s['amount'] ?? 0, 2); ?></td>
                                            <td><?php echo html_escape($s['username']); ?></td>
                                            <td><span class="badge badge-danger"><?php echo html_escape($s['suspicious_reason'] ?: 'Flagged Action'); ?></span></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-xs btn-outline-primary view-audit-detail" data-id="<?php echo $s['id']; ?>">
                                                    <i class="fas fa-eye"></i> View Details
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="fas fa-check-circle text-success mr-2 fa-2x"></i><br>
                                            No suspicious or high-risk financial events flagged in this period.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Before & After Comparison Modal -->
<div class="modal fade" id="auditDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-microscope mr-2"></i>Financial Audit Detail & Before/After Comparison</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" id="auditModalContent">
                <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="common/assets/chart/Chart.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Daily Trend Chart
    var trendData = <?php echo json_encode($dashboard['daily_trend'] ?? []); ?>;
    var trendLabels = trendData.map(function(d) { return d.event_date; });
    var trendCounts = trendData.map(function(d) { return parseInt(d.event_count); });
    var trendVolumes = trendData.map(function(d) { return parseFloat(d.total_amount); });

    var ctxTrend = document.getElementById('trendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: trendLabels.length ? trendLabels : ['No Data'],
            datasets: [
                {
                    label: 'Event Count',
                    data: trendCounts.length ? trendCounts : [0],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.3,
                    yAxisID: 'y'
                },
                {
                    label: 'Volume (৳)',
                    data: trendVolumes.length ? trendVolumes : [0],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: false,
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Events' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: 'Amount (৳)' }
                }
            }
        }
    });

    // 2. Module Distribution Donut Chart
    var modData = <?php echo json_encode($dashboard['module_dist'] ?? []); ?>;
    var modLabels = modData.map(function(d) { return d.module_name.toUpperCase(); });
    var modCounts = modData.map(function(d) { return parseInt(d.count); });

    var ctxMod = document.getElementById('moduleChart').getContext('2d');
    new Chart(ctxMod, {
        type: 'doughnut',
        data: {
            labels: modLabels.length ? modLabels : ['None'],
            datasets: [{
                data: modCounts.length ? modCounts : [1],
                backgroundColor: ['#007bff', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
            }
        }
    });

    // 3. View Audit Detail Modal Handler
    $(document).on('click', '.view-audit-detail', function() {
        var auditId = $(this).data('id');
        $('#auditDetailModal').modal('show');
        $('#auditModalContent').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>');

        $.ajax({
            url: 'finance/getAuditDetailAjax',
            type: 'GET',
            data: { id: auditId },
            dataType: 'json',
            success: function(resp) {
                if (resp.status && resp.data) {
                    var d = resp.data;
                    var html = '<div class="row mb-3">' +
                        '<div class="col-md-6"><strong>Audit UUID:</strong> <code>' + d.audit_uuid + '</code></div>' +
                        '<div class="col-md-6 text-right"><strong>Timestamp:</strong> ' + d.created_at_formatted + '</div>' +
                        '<div class="col-md-6 mt-2"><strong>Module:</strong> <span class="badge badge-light border text-uppercase">' + d.module_name + '</span></div>' +
                        '<div class="col-md-6 mt-2 text-right"><strong>Action:</strong> <span class="badge badge-action-' + d.action_type.toLowerCase() + '">' + d.action_type + '</span></div>' +
                        '<div class="col-md-6 mt-2"><strong>Reference:</strong> ' + (d.transaction_reference || '-') + '</div>' +
                        '<div class="col-md-6 mt-2 text-right"><strong>User:</strong> ' + d.username + ' (ID: ' + d.user_id + ')</div>' +
                        '<div class="col-md-6 mt-2"><strong>IP Address:</strong> ' + (d.ip_address || '-') + '</div>' +
                        '<div class="col-md-6 mt-2 text-right"><strong>Amount:</strong> ৳' + parseFloat(d.amount).toFixed(2) + '</div>' +
                        '</div>';

                    if (parseInt(d.is_suspicious) === 1) {
                        html += '<div class="alert alert-danger py-2 mb-3"><i class="fas fa-exclamation-triangle mr-2"></i><strong>Suspicious Alert:</strong> ' + (d.suspicious_reason || 'Flagged financial operation') + '</div>';
                    }

                    html += '<div class="card mb-3"><div class="card-header bg-light py-2"><strong>Change Description:</strong></div><div class="card-body py-2">' + (d.change_description || '-') + '</div></div>';

                    if (d.reason_for_change) {
                        html += '<div class="card mb-3"><div class="card-header bg-light py-2"><strong>Reason for Change:</strong></div><div class="card-body py-2 text-muted">' + d.reason_for_change + '</div></div>';
                    }

                    // Before vs After Comparison
                    html += '<h6 class="font-weight-bold mt-3 mb-2"><i class="fas fa-columns text-primary mr-1"></i>Before & After Value Comparison</h6>';

                    var prev = d.previous_value_decoded;
                    var next = d.new_value_decoded;

                    if (prev || next) {
                        html += '<table class="diff-table mb-2"><thead><tr><th>Field / Property</th><th>Previous Value (Before)</th><th>New Value (After)</th></tr></thead><tbody>';
                        var allKeys = {};
                        if (prev && typeof prev === 'object') { Object.keys(prev).forEach(function(k) { allKeys[k] = true; }); }
                        if (next && typeof next === 'object') { Object.keys(next).forEach(function(k) { allKeys[k] = true; }); }

                        var keys = Object.keys(allKeys);
                        if (keys.length > 0) {
                            keys.forEach(function(k) {
                                var pVal = (prev && prev[k] !== undefined) ? JSON.stringify(prev[k]) : '-';
                                var nVal = (next && next[k] !== undefined) ? JSON.stringify(next[k]) : '-';
                                var isDiff = (pVal !== nVal);
                                html += '<tr>' +
                                    '<td><strong>' + k + '</strong></td>' +
                                    '<td class="' + (isDiff && pVal !== '-' ? 'diff-old' : '') + '">' + pVal + '</td>' +
                                    '<td class="' + (isDiff && nVal !== '-' ? 'diff-new' : '') + '">' + nVal + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            html += '<tr><td colspan="3" class="text-center text-muted">No structured property changes recorded.</td></tr>';
                        }
                        html += '</tbody></table>';
                    } else if (d.previous_value || d.new_value) {
                        html += '<table class="diff-table mb-2"><thead><tr><th>Previous Raw Value</th><th>New Raw Value</th></tr></thead><tbody>' +
                            '<tr><td class="diff-old"><pre class="mb-0">' + (d.previous_value || '-') + '</pre></td>' +
                            '<td class="diff-new"><pre class="mb-0">' + (d.new_value || '-') + '</pre></td></tr></tbody></table>';
                    } else {
                        html += '<div class="alert alert-secondary py-2 text-center text-muted">No prior snapshot available (Event is an initial record creation or direct ledger entry).</div>';
                    }

                    $('#auditModalContent').html(html);
                } else {
                    $('#auditModalContent').html('<div class="alert alert-danger">Failed to load audit record details.</div>');
                }
            },
            error: function() {
                $('#auditModalContent').html('<div class="alert alert-danger">Error connecting to audit server.</div>');
            }
        });
    });
});
</script>
