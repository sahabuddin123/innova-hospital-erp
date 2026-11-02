<link rel="stylesheet" href="common/extranal/css/logs/activity_reports.css">

<div class="content-wrapper bg-light">
    <section class="content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-history text-teal mr-2" style="color: #20c997;"></i><?php echo lang('user_activity_reports'); ?>
                    </h1>
                    <small class="text-muted">Master user action logs, module navigation, and operational audit trail</small>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="logs/exportActivityCsv?start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>&user_id=<?php echo urlencode($filters['user_id'] ?? ''); ?>&module_name=<?php echo urlencode($filters['module_name'] ?? ''); ?>&action_type=<?php echo urlencode($filters['action_type'] ?? ''); ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-file-csv mr-1"></i> Export Activity CSV
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php $this->load->view('logs/inc_activity_nav', ['active_report' => 'user_activity']); ?>

            <!-- Filter Card -->
            <div class="act-filter-card">
                <form method="GET" action="logs/userActivityReport" class="act-filter-row">
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
                        <label><?php echo lang('all_users'); ?></label>
                        <select name="user_id" class="form-control">
                            <option value="">All Users</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?php echo $u->id; ?>" <?php echo ($filters['user_id'] ?? '') == $u->id ? 'selected' : ''; ?>>
                                    <?php echo html_escape($u->username . ' (' . trim($u->first_name . ' ' . $u->last_name) . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="act-filter-group">
                        <label><?php echo lang('all_roles'); ?></label>
                        <select name="role" class="form-control">
                            <option value="">All Roles</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo $r->name; ?>" <?php echo ($filters['role'] ?? '') == $r->name ? 'selected' : ''; ?>>
                                    <?php echo html_escape($r->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="act-filter-group">
                        <label>Module</label>
                        <select name="module_name" class="form-control">
                            <option value="">All Modules</option>
                            <?php foreach ($modules as $m): ?>
                                <option value="<?php echo $m; ?>" <?php echo ($filters['module_name'] ?? '') === $m ? 'selected' : ''; ?>>
                                    <?php echo html_escape(strtoupper($m)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="act-filter-group">
                        <label>Action</label>
                        <select name="action_type" class="form-control">
                            <option value="">All Actions</option>
                            <?php foreach ($action_types as $act): ?>
                                <option value="<?php echo $act; ?>" <?php echo ($filters['action_type'] ?? '') === $act ? 'selected' : ''; ?>>
                                    <?php echo html_escape($act); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="act-filter-group">
                        <label>Search</label>
                        <input type="text" name="search" value="<?php echo html_escape($filters['search'] ?? ''); ?>" placeholder="Keywords..." class="form-control" style="width:130px;">
                    </div>

                    <div class="act-filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary" style="height:35px; padding: 4px 15px;">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="act-card">
                <div class="act-card-header bg-white">
                    <h3 class="act-card-title">
                        <i class="fas fa-list text-primary"></i> User Activities (<?php echo count($activities); ?>)
                    </h3>
                </div>
                <div class="act-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Activity Ref</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Module</th>
                                    <th>Action Type</th>
                                    <th>Record Reference</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($activities)): ?>
                                    <?php foreach ($activities as $a): ?>
                                        <tr>
                                            <td><?php echo html_escape($a['created_at_formatted']); ?></td>
                                            <td><code><?php echo html_escape($a['activity_uuid']); ?></code></td>
                                            <td><strong><?php echo html_escape($a['username'] ?: 'System'); ?></strong></td>
                                            <td><span class="badge badge-secondary"><?php echo html_escape($a['role'] ?: 'System'); ?></span></td>
                                            <td><span class="badge badge-info"><?php echo html_escape(strtoupper($a['module_name'])); ?></span></td>
                                            <td>
                                                <?php
                                                $badge_class = 'badge-light';
                                                if (in_array($a['action_type'], ['CREATE', 'LOGIN'])) $badge_class = 'badge-success';
                                                elseif (in_array($a['action_type'], ['UPDATE'])) $badge_class = 'badge-warning';
                                                elseif (in_array($a['action_type'], ['DELETE', 'FAILED'])) $badge_class = 'badge-danger';
                                                elseif (in_array($a['action_type'], ['PRINT', 'EXPORT_EXCEL', 'EXPORT_PDF', 'EXPORT_CSV'])) $badge_class = 'badge-primary';
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>"><?php echo html_escape($a['action_type']); ?></span>
                                            </td>
                                            <td><?php echo html_escape($a['record_reference'] ?: '-'); ?></td>
                                            <td><?php echo html_escape($a['description']); ?></td>
                                            <td><code><?php echo html_escape($a['ip_address']); ?></code></td>
                                            <td>
                                                <button class="btn btn-xs btn-outline-dark btn-view-act" data-id="<?php echo $a['id']; ?>">
                                                    <i class="fas fa-search"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">
                                            No user activities match the specified filters.
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
</script>
