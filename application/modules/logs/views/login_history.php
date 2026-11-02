<link rel="stylesheet" href="common/extranal/css/logs/activity_reports.css">

<div class="content-wrapper bg-light">
    <section class="content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-sign-in-alt text-teal mr-2" style="color: #20c997;"></i><?php echo lang('user_login_history'); ?>
                    </h1>
                    <small class="text-muted">Chronological authentication logs, devices, browsers, and IP tracking</small>
                </div>
                <div class="col-sm-6 text-right">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => 'login_history']); ?>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php $this->load->view('logs/inc_activity_nav', ['active_report' => 'login_history']); ?>

            <!-- Filter Card -->
            <div class="act-filter-card">
                <form method="GET" action="logs/userLoginHistory" class="act-filter-row">
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
                        <label>Status</label>
                        <select name="login_status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="SUCCESS" <?php echo ($filters['login_status'] ?? '') === 'SUCCESS' ? 'selected' : ''; ?>>SUCCESS</option>
                            <option value="FAILED" <?php echo ($filters['login_status'] ?? '') === 'FAILED' ? 'selected' : ''; ?>>FAILED</option>
                        </select>
                    </div>

                    <div class="act-filter-group">
                        <label>IP Address</label>
                        <input type="text" name="ip_address" value="<?php echo html_escape($filters['ip_address'] ?? ''); ?>" placeholder="e.g. 192.168" class="form-control" style="width:120px;">
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
                        <i class="fas fa-list text-primary"></i> Login Records (<?php echo count($logins); ?>)
                    </h3>
                </div>
                <div class="act-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-sm" id="loginTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Login Time</th>
                                    <th>Session Reference</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>IP Address</th>
                                    <th>Device / OS</th>
                                    <th>Browser</th>
                                    <th>Risk</th>
                                    <th>Source</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logins)): ?>
                                    <?php foreach ($logins as $l): ?>
                                        <tr>
                                            <td><?php echo html_escape($l['login_datetime']); ?></td>
                                            <td><code><?php echo html_escape($l['session_reference']); ?></code></td>
                                            <td>
                                                <strong><?php echo html_escape($l['username'] ?: 'Guest'); ?></strong>
                                                <?php if (!empty($l['full_name']) && $l['full_name'] !== $l['username']): ?>
                                                    <div class="text-muted" style="font-size:11px;"><?php echo html_escape($l['full_name']); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge badge-secondary"><?php echo html_escape($l['role']); ?></span></td>
                                            <td>
                                                <?php if ($l['login_status'] === 'SUCCESS'): ?>
                                                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>SUCCESS</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>FAILED</span>
                                                    <?php if (!empty($l['failure_reason'])): ?>
                                                        <div class="text-danger" style="font-size:10px;"><?php echo html_escape($l['failure_reason']); ?></div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><code><?php echo html_escape($l['ip_address']); ?></code></td>
                                            <td><?php echo html_escape($l['device'] . ' / ' . $l['operating_system']); ?></td>
                                            <td><?php echo html_escape($l['browser']); ?></td>
                                            <td>
                                                <?php if ($l['is_suspicious']): ?>
                                                    <span class="badge badge-danger" title="<?php echo html_escape($l['suspicious_reason']); ?>">
                                                        <i class="fas fa-exclamation-triangle"></i> SUSPICIOUS
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-light text-muted">Normal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($l['is_historical']): ?>
                                                    <span class="badge-historical">Historical</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success" style="font-size:10px;">Live</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">
                                            No login records match the specified filters.
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
