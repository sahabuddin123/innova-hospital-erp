<link rel="stylesheet" href="common/extranal/css/logs/activity_reports.css">

<div class="content-wrapper bg-light">
    <section class="content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-sign-out-alt text-teal mr-2" style="color: #20c997;"></i><?php echo lang('user_logout_history'); ?>
                    </h1>
                    <small class="text-muted">User session termination audit, durations, and logout types (Manual, Timeout, Incomplete)</small>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php $this->load->view('logs/inc_activity_nav', ['active_report' => 'logout_history']); ?>

            <!-- Filter Card -->
            <div class="act-filter-card">
                <form method="GET" action="logs/userLogoutHistory" class="act-filter-row">
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
                        <label>Logout Type</label>
                        <select name="logout_type" class="form-control">
                            <option value="">All Logout Types</option>
                            <option value="MANUAL" <?php echo ($filters['logout_type'] ?? '') === 'MANUAL' ? 'selected' : ''; ?>>Manual Logout</option>
                            <option value="TIMEOUT" <?php echo ($filters['logout_type'] ?? '') === 'TIMEOUT' ? 'selected' : ''; ?>>Session Timeout</option>
                            <option value="FORCED" <?php echo ($filters['logout_type'] ?? '') === 'FORCED' ? 'selected' : ''; ?>>Forced / Reset</option>
                            <option value="INCOMPLETE" <?php echo ($filters['logout_type'] ?? '') === 'INCOMPLETE' ? 'selected' : ''; ?>>Incomplete / Historical</option>
                        </select>
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
                        <i class="fas fa-sign-out-alt text-info"></i> Session Terminations (<?php echo count($logins); ?>)
                    </h3>
                </div>
                <div class="act-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Session Reference</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Login Time</th>
                                    <th>Logout Time</th>
                                    <th>Session Duration</th>
                                    <th>Logout Type</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logins)): ?>
                                    <?php foreach ($logins as $l): ?>
                                        <tr>
                                            <td><code><?php echo html_escape($l['session_reference']); ?></code></td>
                                            <td><strong><?php echo html_escape($l['username']); ?></strong></td>
                                            <td><span class="badge badge-secondary"><?php echo html_escape($l['role']); ?></span></td>
                                            <td><?php echo html_escape($l['login_datetime']); ?></td>
                                            <td>
                                                <?php if (!empty($l['logout_datetime'])): ?>
                                                    <?php echo html_escape($l['logout_datetime']); ?>
                                                <?php elseif ($l['session_status'] === 'ACTIVE'): ?>
                                                    <span class="badge badge-primary"><i class="fas fa-circle text-light mr-1" style="font-size:8px;"></i>Session Active</span>
                                                <?php else: ?>
                                                    <span class="text-muted font-italic">Unrecorded (Legacy)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($l['session_duration'])): ?>
                                                    <strong class="text-dark"><?php echo gmdate("H:i:s", $l['session_duration']); ?></strong>
                                                    <span class="text-muted" style="font-size:11px;">(<?php echo $l['session_duration']; ?>s)</span>
                                                <?php elseif ($l['session_status'] === 'ACTIVE'): ?>
                                                    <span class="text-primary font-weight-bold">Ongoing</span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($l['logout_type'] === 'MANUAL'): ?>
                                                    <span class="badge badge-secondary"><i class="fas fa-user-lock mr-1"></i>Manual</span>
                                                <?php elseif ($l['logout_type'] === 'TIMEOUT'): ?>
                                                    <span class="badge badge-warning"><i class="fas fa-hourglass-end mr-1"></i>Timeout</span>
                                                <?php elseif ($l['logout_type'] === 'FORCED'): ?>
                                                    <span class="badge badge-danger"><i class="fas fa-ban mr-1"></i>Forced</span>
                                                <?php else: ?>
                                                    <span class="badge badge-light border text-muted">Incomplete</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><code><?php echo html_escape($l['ip_address']); ?></code></td>
                                            <td><?php echo html_escape($l['device'] . ' / ' . $l['operating_system']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            No session logout records match the specified filters.
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

<script>
function toggleCustomDates(val) {
    if (val === 'custom') {
        $('.custom-date-group').show();
    } else {
        $('.custom-date-group').hide();
    }
}
</script>
