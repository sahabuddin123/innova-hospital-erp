<link rel="stylesheet" href="common/extranal/css/logs/activity_reports.css">

<div class="content-wrapper bg-light">
    <section class="content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-shield-virus text-danger mr-2"></i><?php echo lang('suspicious_activity_monitoring'); ?>
                    </h1>
                    <small class="text-muted">High-priority surveillance, brute-force indicators, and anomalous user activity</small>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php $this->load->view('logs/inc_activity_nav', ['active_report' => 'suspicious_activity']); ?>

            <!-- Filter Card -->
            <div class="act-filter-card">
                <form method="GET" action="logs/suspiciousActivity" class="act-filter-row">
                    <div class="act-filter-group">
                        <label><?php echo lang('filter_by_period'); ?></label>
                        <select name="period" class="form-control" onchange="toggleCustomDates(this.value)">
                            <option value="today" <?php echo $period === 'today' ? 'selected' : ''; ?>>Today</option>
                            <option value="yesterday" <?php echo $period === 'yesterday' ? 'selected' : ''; ?>>Yesterday</option>
                            <option value="this_week" <?php echo $period === 'this_week' ? 'selected' : ''; ?>>This Week</option>
                            <option value="this_month" <?php echo $period === 'this_month' ? 'selected' : ''; ?>>This Month</option>
                            <option value="last_month" <?php echo $period === 'last_month' ? 'selected' : ''; ?>>Last Month</option>
                            <option value="this_year" <?php echo $period === 'this_year' ? 'selected' : ''; ?>>This Year</option>
                            <option value="custom" <?php echo $period === 'custom' ? 'selected' : ''; ?>>Custom</option>
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
                        <label>Search</label>
                        <input type="text" name="search" value="<?php echo html_escape($filters['search'] ?? ''); ?>" placeholder="Keywords..." class="form-control" style="width:150px;">
                    </div>

                    <div class="act-filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary" style="height:35px; padding: 4px 15px;">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- 1. Suspicious Login Attempts Section -->
            <div class="act-card mb-4">
                <div class="act-card-header bg-white">
                    <h3 class="act-card-title text-danger">
                        <i class="fas fa-user-lock"></i> Suspicious Authentication & Brute-Force Attempts (<?php echo count($suspicious_logins); ?>)
                    </h3>
                </div>
                <div class="act-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Session Ref</th>
                                    <th>Target Username</th>
                                    <th>Failure Reason</th>
                                    <th>Suspicious Indicator</th>
                                    <th>IP Address</th>
                                    <th>Device / Browser</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($suspicious_logins)): ?>
                                    <?php foreach ($suspicious_logins as $sl): ?>
                                        <tr>
                                            <td><?php echo html_escape($sl['login_datetime']); ?></td>
                                            <td><code><?php echo html_escape($sl['session_reference']); ?></code></td>
                                            <td><strong class="text-danger"><?php echo html_escape($sl['username']); ?></strong></td>
                                            <td><?php echo html_escape($sl['failure_reason']); ?></td>
                                            <td>
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-radiation mr-1"></i> <?php echo html_escape($sl['suspicious_reason'] ?: 'Multiple Failed Attempts'); ?>
                                                </span>
                                            </td>
                                            <td><code><?php echo html_escape($sl['ip_address']); ?></code></td>
                                            <td><?php echo html_escape($sl['device'] . ' / ' . $sl['browser']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-shield-alt text-success fa-2x mb-2 d-block"></i>
                                            No suspicious login or brute-force attempts detected.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. Suspicious System Activities Section -->
            <div class="act-card">
                <div class="act-card-header bg-white">
                    <h3 class="act-card-title text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Flagged System Activities & Anomalies (<?php echo count($suspicious_activities); ?>)
                    </h3>
                </div>
                <div class="act-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Activity UUID</th>
                                    <th>User</th>
                                    <th>Module</th>
                                    <th>Action</th>
                                    <th>Risk Reason</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($suspicious_activities)): ?>
                                    <?php foreach ($suspicious_activities as $sa): ?>
                                        <tr>
                                            <td><?php echo html_escape($sa['created_at_formatted']); ?></td>
                                            <td><code><?php echo html_escape($sa['activity_uuid']); ?></code></td>
                                            <td><strong><?php echo html_escape($sa['username'] ?: 'Guest'); ?></strong></td>
                                            <td><span class="badge badge-info"><?php echo html_escape(strtoupper($sa['module_name'])); ?></span></td>
                                            <td><span class="badge badge-secondary"><?php echo html_escape($sa['action_type']); ?></span></td>
                                            <td>
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-radiation mr-1"></i> <?php echo html_escape($sa['suspicious_reason'] ?: 'Flagged'); ?>
                                                </span>
                                            </td>
                                            <td><?php echo html_escape($sa['description']); ?></td>
                                            <td><code><?php echo html_escape($sa['ip_address']); ?></code></td>
                                            <td>
                                                <button class="btn btn-xs btn-outline-dark btn-view-act" data-id="<?php echo $sa['id']; ?>">
                                                    <i class="fas fa-search"></i> Inspect
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="fas fa-check-double text-success fa-2x mb-2 d-block"></i>
                                            No flagged or anomalous system activities recorded.
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
                <h5 class="modal-title font-weight-bold">
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
                           '<div class="act-detail-row"><span class="act-detail-label">Risk Flag:</span><span class="act-detail-value"><span class="badge badge-danger">' + (d.suspicious_reason || 'SUSPICIOUS') + '</span></span></div>' +
                           '<div class="act-detail-row"><span class="act-detail-label">Description:</span><span class="act-detail-value">' + d.description + '</span></div>' +
                           '<div class="act-detail-row"><span class="act-detail-label">Record Ref:</span><span class="act-detail-value">' + (d.record_reference || 'N/A') + '</span></div>' +
                           '<div class="act-detail-row"><span class="act-detail-label">IP Address:</span><span class="act-detail-value"><code>' + d.ip_address + '</code></span></div>' +
                           '<div class="act-detail-row"><span class="act-detail-label">User Agent:</span><span class="act-detail-value text-muted" style="font-size:11px;">' + (d.user_agent || 'Unknown') + '</span></div>' +
                           '<div class="mt-3"><h6 class="font-weight-bold text-dark mb-1">Payload / Details:</h6>' + metaHtml + '</div>';
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
</script>
