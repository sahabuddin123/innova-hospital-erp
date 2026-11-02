<link rel="stylesheet" href="common/extranal/css/logs/activity_reports.css">

<div class="content-wrapper bg-light">
    <section class="content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-user-shield text-teal mr-2" style="color: #20c997;"></i><?php echo lang('activity_dashboard'); ?>
                    </h1>
                    <small class="text-muted">Real-time user sessions, module access analytics, data modification, and risk monitoring</small>
                </div>
                <div class="col-sm-6 text-right">
                    <span class="badge badge-secondary p-2">
                        <i class="far fa-calendar-alt mr-1"></i> <?php echo html_escape($start_date); ?> &mdash; <?php echo html_escape($end_date); ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Navigation Sub-Menu -->
            <?php $this->load->view('logs/inc_activity_nav', ['active_report' => 'activity_dashboard']); ?>

            <!-- Interactive Period Filter Form -->
            <div class="act-filter-card">
                <form method="GET" action="logs/activityDashboard" class="act-filter-row">
                    <div class="act-filter-group">
                        <label><?php echo lang('filter_by_period'); ?></label>
                        <select name="period" id="periodSelect" class="form-control" onchange="toggleCustomDates(this.value)">
                            <option value="today" <?php echo $period === 'today' ? 'selected' : ''; ?>>Today</option>
                            <option value="yesterday" <?php echo $period === 'yesterday' ? 'selected' : ''; ?>>Yesterday</option>
                            <option value="this_week" <?php echo $period === 'this_week' ? 'selected' : ''; ?>>This Week</option>
                            <option value="this_month" <?php echo $period === 'this_month' ? 'selected' : ''; ?>>This Month</option>
                            <option value="last_month" <?php echo $period === 'last_month' ? 'selected' : ''; ?>>Last Month</option>
                            <option value="this_year" <?php echo $period === 'this_year' ? 'selected' : ''; ?>>This Year</option>
                            <option value="custom" <?php echo $period === 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                        </select>
                    </div>

                    <div class="act-filter-group custom-date-group" style="<?php echo $period === 'custom' ? '' : 'display:none;'; ?>">
                        <label>Start Date</label>
                        <input type="date" name="start_date" value="<?php echo html_escape($start_date); ?>" class="form-control">
                    </div>

                    <div class="act-filter-group custom-date-group" style="<?php echo $period === 'custom' ? '' : 'display:none;'; ?>">
                        <label>End Date</label>
                        <input type="date" name="end_date" value="<?php echo html_escape($end_date); ?>" class="form-control">
                    </div>

                    <div class="act-filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary" style="height:35px; padding: 4px 15px;">
                            <i class="fas fa-filter mr-1"></i> Apply Filter
                        </button>
                    </div>

                    <div class="act-filter-group ml-auto">
                        <label>&nbsp;</label>
                        <a href="logs/exportActivityCsv?start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>" class="btn btn-outline-success" style="height:35px; padding: 4px 12px;">
                            <i class="fas fa-file-csv mr-1"></i> Export Activity CSV
                        </a>
                    </div>
                </form>
            </div>

            <!-- 8 KPI Summary Cards -->
            <div class="act-kpi-grid">
                <div class="act-kpi-card teal">
                    <span class="act-kpi-label"><i class="fas fa-sign-in-alt mr-1"></i> Total Logins</span>
                    <span class="act-kpi-val"><?php echo number_format($kpi['total_login_attempts']); ?></span>
                    <span class="act-kpi-sub">Attempts in selected period</span>
                </div>

                <div class="act-kpi-card success">
                    <span class="act-kpi-label"><i class="fas fa-check-circle mr-1"></i> Successful Logins</span>
                    <span class="act-kpi-val text-success"><?php echo number_format($kpi['successful_logins']); ?></span>
                    <span class="act-kpi-sub">Authenticated successfully</span>
                </div>

                <div class="act-kpi-card danger">
                    <span class="act-kpi-label"><i class="fas fa-times-circle mr-1"></i> Failed Logins</span>
                    <span class="act-kpi-val text-danger"><?php echo number_format($kpi['failed_logins']); ?></span>
                    <span class="act-kpi-sub">Authentication rejected</span>
                </div>

                <div class="act-kpi-card info">
                    <span class="act-kpi-label"><i class="fas fa-user-check mr-1"></i> Active Users Today</span>
                    <span class="act-kpi-val text-info"><?php echo number_format($kpi['active_users_today']); ?></span>
                    <span class="act-kpi-sub">Unique logins today</span>
                </div>

                <div class="act-kpi-card primary">
                    <span class="act-kpi-label"><i class="fas fa-history mr-1"></i> Total Activities</span>
                    <span class="act-kpi-val text-primary"><?php echo number_format($kpi['total_activities']); ?></span>
                    <span class="act-kpi-sub">Actions & module visits</span>
                </div>

                <div class="act-kpi-card purple">
                    <span class="act-kpi-label"><i class="fas fa-user-tag mr-1"></i> Most Active User</span>
                    <span class="act-kpi-val text-purple" style="font-size:17px;"><?php echo html_escape($kpi['most_active_user']); ?></span>
                    <span class="act-kpi-sub"><?php echo number_format($kpi['most_active_user_count']); ?> logged events</span>
                </div>

                <div class="act-kpi-card dark">
                    <span class="act-kpi-label"><i class="fas fa-th-large mr-1"></i> Top Module</span>
                    <span class="act-kpi-val text-dark" style="font-size:17px;"><?php echo html_escape(strtoupper($kpi['most_accessed_module'])); ?></span>
                    <span class="act-kpi-sub"><?php echo number_format($kpi['most_accessed_module_count']); ?> accesses</span>
                </div>

                <div class="act-kpi-card <?php echo !empty($suspicious_events) ? 'danger' : 'success'; ?>">
                    <span class="act-kpi-label"><i class="fas fa-shield-virus mr-1"></i> Suspicious Events</span>
                    <span class="act-kpi-val <?php echo !empty($suspicious_events) ? 'text-danger' : 'text-success'; ?>">
                        <?php echo count($suspicious_events); ?>
                    </span>
                    <span class="act-kpi-sub">Flagged risk indicators</span>
                </div>
            </div>

            <!-- 8 Chart.js Charts Grid (2 per row) -->
            <div class="row">
                <!-- Chart 1: Daily Login Trends -->
                <div class="col-lg-6 mb-4">
                    <div class="act-card h-100">
                        <div class="act-card-header">
                            <h3 class="act-card-title"><i class="fas fa-chart-line text-primary"></i> 1. Daily Login Trends</h3>
                            <span class="badge badge-light">Success vs Failure</span>
                        </div>
                        <div class="act-card-body">
                            <canvas id="chartDailyLogins" height="150"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Login Success vs Failure -->
                <div class="col-lg-6 mb-4">
                    <div class="act-card h-100">
                        <div class="act-card-header">
                            <h3 class="act-card-title"><i class="fas fa-chart-pie text-success"></i> 2. Login Success vs. Failure Ratio</h3>
                            <span class="badge badge-light">Authentication Rate</span>
                        </div>
                        <div class="act-card-body">
                            <canvas id="chartLoginRatio" height="150"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 3: User-wise Activity Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="act-card h-100">
                        <div class="act-card-header">
                            <h3 class="act-card-title"><i class="fas fa-user-friends text-info"></i> 3. User-wise Activity Distribution</h3>
                            <span class="badge badge-light">Top 7 Active Users</span>
                        </div>
                        <div class="act-card-body">
                            <canvas id="chartUserActivity" height="150"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 4: Role-wise Activity Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="act-card h-100">
                        <div class="act-card-header">
                            <h3 class="act-card-title"><i class="fas fa-users-cog text-warning"></i> 4. Role-wise Activity Distribution</h3>
                            <span class="badge badge-light">By User Role</span>
                        </div>
                        <div class="act-card-body">
                            <canvas id="chartRoleActivity" height="150"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 5: Module Access Frequency -->
                <div class="col-lg-6 mb-4">
                    <div class="act-card h-100">
                        <div class="act-card-header">
                            <h3 class="act-card-title"><i class="fas fa-cubes text-purple" style="color:#6f42c1;"></i> 5. Module Access Frequency</h3>
                            <span class="badge badge-light">Top Modules Visited</span>
                        </div>
                        <div class="act-card-body">
                            <canvas id="chartModuleAccess" height="150"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 6: Hourly Activity Heatmap / Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="act-card h-100">
                        <div class="act-card-header">
                            <h3 class="act-card-title"><i class="fas fa-clock text-secondary"></i> 6. Hourly Activity Distribution (24 Hours)</h3>
                            <span class="badge badge-light">Operational Peak Hours</span>
                        </div>
                        <div class="act-card-body">
                            <canvas id="chartHourlyActivity" height="150"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 7: Print vs Export Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="act-card h-100">
                        <div class="act-card-header">
                            <h3 class="act-card-title"><i class="fas fa-file-export text-teal" style="color:#20c997;"></i> 7. Print vs. Export Distribution</h3>
                            <span class="badge badge-light">Data Output Audit</span>
                        </div>
                        <div class="act-card-body">
                            <canvas id="chartPrintExport" height="150"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 8: Data Modification Trends -->
                <div class="col-lg-6 mb-4">
                    <div class="act-card h-100">
                        <div class="act-card-header">
                            <h3 class="act-card-title"><i class="fas fa-database text-danger"></i> 8. Data Modification Trends</h3>
                            <span class="badge badge-light">Create / Update / Delete</span>
                        </div>
                        <div class="act-card-body">
                            <canvas id="chartCrudTrends" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Suspicious Activity Table -->
            <div class="act-card">
                <div class="act-card-header bg-white">
                    <h3 class="act-card-title text-danger">
                        <i class="fas fa-shield-virus"></i> Recent Suspicious Activity & Security Alerts
                    </h3>
                    <a href="logs/suspiciousActivity" class="btn btn-sm btn-outline-danger">
                        View All Suspicious Events <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="act-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Activity Ref</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Module</th>
                                    <th>Suspicious Reason</th>
                                    <th>IP Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($suspicious_events)): ?>
                                    <?php foreach ($suspicious_events as $ev): ?>
                                        <tr>
                                            <td><?php echo html_escape($ev['created_at_formatted']); ?></td>
                                            <td><code><?php echo html_escape($ev['activity_uuid']); ?></code></td>
                                            <td><strong><?php echo html_escape($ev['username'] ?: 'Guest'); ?></strong></td>
                                            <td><span class="badge badge-secondary"><?php echo html_escape($ev['role'] ?: 'Unknown'); ?></span></td>
                                            <td><span class="badge badge-info"><?php echo html_escape(strtoupper($ev['module_name'])); ?></span></td>
                                            <td><span class="badge badge-danger"><?php echo html_escape($ev['suspicious_reason'] ?: 'Flagged activity'); ?></span></td>
                                            <td><code><?php echo html_escape($ev['ip_address']); ?></code></td>
                                            <td>
                                                <button class="btn btn-xs btn-outline-dark btn-view-act" data-id="<?php echo $ev['id']; ?>">
                                                    <i class="fas fa-search"></i> Details
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="fas fa-shield-alt text-success fa-2x mb-2 d-block"></i>
                                            No suspicious activity detected in the selected period. System is secure.
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

<!-- Reusable Activity Detail Modal -->
<div class="modal fade" id="actDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white py-2">
                <h5 class="modal-title font-weight-bold" id="actModalTitle">
                    <i class="fas fa-info-circle mr-2"></i> Activity Log Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="actModalContent">
                <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i> Loading details...</div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCustomDates(val) {
    if (val === 'custom') {
        $('.custom-date-group').show();
    } else {
        $('.custom-date-group').hide();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // 1. Chart 1: Daily Login Trends
    var dailyData = <?php echo json_encode($daily_logins ?: []); ?>;
    var dailyLabels = dailyData.map(function(d) { return d.log_date; });
    var dailySuccess = dailyData.map(function(d) { return parseInt(d.success_cnt); });
    var dailyFail = dailyData.map(function(d) { return parseInt(d.fail_cnt); });

    var ctx1 = document.getElementById('chartDailyLogins');
    if (ctx1) {
        new Chart(ctx1.getContext('2d'), {
            type: 'line',
            data: {
                labels: dailyLabels.length ? dailyLabels : ['No Data'],
                datasets: [
                    {
                        label: 'Successful Logins',
                        data: dailySuccess.length ? dailySuccess : [0],
                        borderColor: '#20c997',
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Failed Attempts',
                        data: dailyFail.length ? dailyFail : [0],
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // 2. Chart 2: Login Success vs Failure Ratio
    var ctx2 = document.getElementById('chartLoginRatio');
    if (ctx2) {
        var succCnt = <?php echo intval($kpi['successful_logins']); ?>;
        var failCnt = <?php echo intval($kpi['failed_logins']); ?>;
        new Chart(ctx2.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Success', 'Failed'],
                datasets: [{
                    data: (succCnt + failCnt > 0) ? [succCnt, failCnt] : [1, 0],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // 3. Chart 3: User-wise Activity Distribution
    var userData = <?php echo json_encode($user_activity_chart ?: []); ?>;
    var userLabels = userData.map(function(u) { return u.username; });
    var userCounts = userData.map(function(u) { return parseInt(u.act_count); });
    var ctx3 = document.getElementById('chartUserActivity');
    if (ctx3) {
        new Chart(ctx3.getContext('2d'), {
            type: 'bar',
            data: {
                labels: userLabels.length ? userLabels : ['No Data'],
                datasets: [{
                    label: 'Activities',
                    data: userCounts.length ? userCounts : [0],
                    backgroundColor: '#17a2b8'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // 4. Chart 4: Role-wise Activity Distribution
    var roleData = <?php echo json_encode($role_activity_chart ?: []); ?>;
    var roleLabels = roleData.map(function(r) { return r.role; });
    var roleCounts = roleData.map(function(r) { return parseInt(r.act_count); });
    var ctx4 = document.getElementById('chartRoleActivity');
    if (ctx4) {
        new Chart(ctx4.getContext('2d'), {
            type: 'pie',
            data: {
                labels: roleLabels.length ? roleLabels : ['No Data'],
                datasets: [{
                    data: roleCounts.length ? roleCounts : [1],
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#6f42c1', '#e83e8c', '#6c757d']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // 5. Chart 5: Module Access Frequency (Horizontal Bar)
    var modData = <?php echo json_encode($module_access_chart ?: []); ?>;
    var modLabels = modData.map(function(m) { return m.module_name.toUpperCase(); });
    var modCounts = modData.map(function(m) { return parseInt(m.access_count); });
    var ctx5 = document.getElementById('chartModuleAccess');
    if (ctx5) {
        new Chart(ctx5.getContext('2d'), {
            type: 'horizontalBar',
            data: {
                labels: modLabels.length ? modLabels : ['None'],
                datasets: [{
                    label: 'Visits',
                    data: modCounts.length ? modCounts : [0],
                    backgroundColor: '#6f42c1'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // 6. Chart 6: Hourly Activity Distribution (0-23)
    var hourlyRaw = <?php echo json_encode($hourly_chart ?: []); ?>;
    var hourlyLabels = [];
    var hourlyValues = [];
    for (var h = 0; h < 24; h++) {
        hourlyLabels.push(h + ':00');
        var found = hourlyRaw.find(function(item) { return parseInt(item.act_hour) === h; });
        hourlyValues.push(found ? parseInt(found.count) : 0);
    }
    var ctx6 = document.getElementById('chartHourlyActivity');
    if (ctx6) {
        new Chart(ctx6.getContext('2d'), {
            type: 'bar',
            data: {
                labels: hourlyLabels,
                datasets: [{
                    label: 'Actions per Hour',
                    data: hourlyValues,
                    backgroundColor: '#fd7e14'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // 7. Chart 7: Print vs Export Distribution
    var peData = <?php echo json_encode($print_export_chart ?: []); ?>;
    var peLabels = peData.map(function(p) { return p.action_type; });
    var peCounts = peData.map(function(p) { return parseInt(p.count); });
    var ctx7 = document.getElementById('chartPrintExport');
    if (ctx7) {
        new Chart(ctx7.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: peLabels.length ? peLabels : ['None Recorded'],
                datasets: [{
                    data: peCounts.length ? peCounts : [1],
                    backgroundColor: ['#20c997', '#343a40', '#007bff', '#ffc107']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // 8. Chart 8: Data Modification Trends (CRUD)
    var crudData = <?php echo json_encode($crud_chart ?: []); ?>;
    var crudDates = crudData.map(function(c) { return c.act_date; });
    var crudCreates = crudData.map(function(c) { return parseInt(c.creates); });
    var crudUpdates = crudData.map(function(c) { return parseInt(c.updates); });
    var crudDeletes = crudData.map(function(c) { return parseInt(c.deletes); });

    var ctx8 = document.getElementById('chartCrudTrends');
    if (ctx8) {
        new Chart(ctx8.getContext('2d'), {
            type: 'bar',
            data: {
                labels: crudDates.length ? crudDates : ['No Data'],
                datasets: [
                    { label: 'Create', data: crudCreates.length ? crudCreates : [0], backgroundColor: '#28a745' },
                    { label: 'Update', data: crudUpdates.length ? crudUpdates : [0], backgroundColor: '#ffc107' },
                    { label: 'Delete', data: crudDeletes.length ? crudDeletes : [0], backgroundColor: '#dc3545' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { xAxes: [{ stacked: true }], yAxes: [{ stacked: true }] }
            }
        });
    }

    // Modal Details Handler
    $(document).on('click', '.btn-view-act', function() {
        var actId = $(this).data('id');
        $('#actModalContent').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i> Loading details...</div>');
        $('#actDetailModal').modal('show');

        $.ajax({
            url: 'logs/getActivityDetailAjax',
            type: 'GET',
            data: { id: actId },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success' && res.data) {
                    var d = res.data;
                    var metaHtml = d.metadata ? '<pre class="act-json-pre">' + JSON.stringify(JSON.parse(d.metadata), null, 2) + '</pre>' : '<span class="text-muted">None</span>';
                    var html = '<div class="act-detail-row"><span class="act-detail-label">Activity UUID:</span><span class="act-detail-value"><code>' + d.activity_uuid + '</code></span></div>' +
                               '<div class="act-detail-row"><span class="act-detail-label">Timestamp:</span><span class="act-detail-value">' + d.created_at_formatted + '</span></div>' +
                               '<div class="act-detail-row"><span class="act-detail-label">User / Role:</span><span class="act-detail-value"><strong>' + (d.username || 'System') + '</strong> (' + (d.role || 'N/A') + ')</span></div>' +
                               '<div class="act-detail-row"><span class="act-detail-label">Module / Action:</span><span class="act-detail-value"><span class="badge badge-info">' + d.module_name.toUpperCase() + '</span> <span class="badge badge-secondary">' + d.action_type + '</span></span></div>' +
                               '<div class="act-detail-row"><span class="act-detail-label">Description:</span><span class="act-detail-value">' + d.description + '</span></div>' +
                               '<div class="act-detail-row"><span class="act-detail-label">Record Ref:</span><span class="act-detail-value">' + (d.record_reference || 'N/A') + '</span></div>' +
                               '<div class="act-detail-row"><span class="act-detail-label">IP Address:</span><span class="act-detail-value"><code>' + d.ip_address + '</code></span></div>' +
                               '<div class="act-detail-row"><span class="act-detail-label">User Agent:</span><span class="act-detail-value text-muted" style="font-size:11px;">' + (d.user_agent || 'Unknown') + '</span></div>' +
                               '<div class="mt-3"><h6 class="font-weight-bold text-dark mb-1">Payload / Metadata:</h6>' + metaHtml + '</div>';
                    $('#actModalContent').html(html);
                } else {
                    $('#actModalContent').html('<div class="alert alert-danger">Failed to load activity details.</div>');
                }
            },
            error: function() {
                $('#actModalContent').html('<div class="alert alert-danger">Error fetching activity details.</div>');
            }
        });
    });
});
</script>
