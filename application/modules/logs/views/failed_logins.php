<link rel="stylesheet" href="common/extranal/css/logs/activity_reports.css">

<div class="content-wrapper bg-light">
    <section class="content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-user-times text-danger mr-2"></i><?php echo lang('failed_login_attempts'); ?>
                    </h1>
                    <small class="text-muted">Security surveillance of unauthorized and failed authentication attempts</small>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php $this->load->view('logs/inc_activity_nav', ['active_report' => 'failed_logins']); ?>

            <!-- Filter Card -->
            <div class="act-filter-card">
                <form method="GET" action="logs/failedLogins" class="act-filter-row">
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
                        <label>IP Address</label>
                        <input type="text" name="ip_address" value="<?php echo html_escape($filters['ip_address'] ?? ''); ?>" placeholder="e.g. 192.168" class="form-control" style="width:130px;">
                    </div>

                    <div class="act-filter-group">
                        <label>Suspicious Only</label>
                        <select name="is_suspicious" class="form-control">
                            <option value="">All Failed Logins</option>
                            <option value="1" <?php echo ($filters['is_suspicious'] ?? '') === '1' ? 'selected' : ''; ?>>Suspicious Only</option>
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
                    <h3 class="act-card-title text-danger">
                        <i class="fas fa-shield-alt"></i> Failed Authentication Attempts (<?php echo count($failed_logins); ?>)
                    </h3>
                </div>
                <div class="act-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Attempt Time</th>
                                    <th>Session Reference</th>
                                    <th>Username Entered</th>
                                    <th>Failure Reason</th>
                                    <th>IP Address</th>
                                    <th>Device / OS</th>
                                    <th>Browser</th>
                                    <th>Risk Flag</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($failed_logins)): ?>
                                    <?php foreach ($failed_logins as $f): ?>
                                        <tr>
                                            <td><?php echo html_escape($f['login_datetime']); ?></td>
                                            <td><code><?php echo html_escape($f['session_reference']); ?></code></td>
                                            <td><strong class="text-danger"><?php echo html_escape($f['username']); ?></strong></td>
                                            <td><span class="badge badge-warning"><?php echo html_escape($f['failure_reason'] ?: 'Invalid credentials'); ?></span></td>
                                            <td><code><?php echo html_escape($f['ip_address']); ?></code></td>
                                            <td><?php echo html_escape($f['device'] . ' / ' . $f['operating_system']); ?></td>
                                            <td><?php echo html_escape($f['browser']); ?></td>
                                            <td>
                                                <?php if ($f['is_suspicious']): ?>
                                                    <span class="badge badge-danger" title="<?php echo html_escape($f['suspicious_reason']); ?>">
                                                        <i class="fas fa-exclamation-triangle"></i> <?php echo html_escape($f['suspicious_reason'] ?: 'Suspicious Attempt'); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-light border text-muted">Single Attempt</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                                            No failed login attempts recorded in the selected period.
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
