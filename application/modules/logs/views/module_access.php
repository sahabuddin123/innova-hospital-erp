<link rel="stylesheet" href="common/extranal/css/logs/activity_reports.css">

<div class="content-wrapper bg-light">
    <section class="content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-th-large text-teal mr-2" style="color: #20c997;"></i><?php echo lang('module_access_reports'); ?>
                    </h1>
                    <small class="text-muted">Module utilization statistics, unique user engagements, and operational frequency</small>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php $this->load->view('logs/inc_activity_nav', ['active_report' => 'module_access']); ?>

            <!-- Filter Card -->
            <div class="d-flex justify-content-end mb-2"><?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => 'module_access']); ?></div>
            <div class="act-filter-card">
                <form method="GET" action="logs/moduleAccessReport" class="act-filter-row">
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
                        <i class="fas fa-cubes text-info"></i> Module Utilization Breakdown
                    </h3>
                </div>
                <div class="act-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Module Name</th>
                                    <th>Total Visits / Views</th>
                                    <th>Unique Users</th>
                                    <th>Last Accessed</th>
                                    <th>Relative Utilization</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_views = 0;
                                foreach ($module_summary as $m) {
                                    $total_views += intval($m['access_count']);
                                }
                                ?>
                                <?php if (!empty($module_summary)): ?>
                                    <?php foreach ($module_summary as $m): ?>
                                        <?php 
                                        $pct = ($total_views > 0) ? round(($m['access_count'] / $total_views) * 100, 1) : 0;
                                        ?>
                                        <tr>
                                            <td>
                                                <strong class="text-primary font-weight-bold" style="font-size:14px;">
                                                    <i class="fas fa-folder mr-1"></i> <?php echo html_escape(strtoupper($m['module_name'])); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary px-2 py-1 font-weight-bold">
                                                    <?php echo number_format($m['access_count']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary px-2 py-1">
                                                    <i class="fas fa-users mr-1"></i> <?php echo number_format($m['unique_users']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo html_escape($m['last_access_datetime'] ?: '-'); ?></td>
                                            <td style="width: 30%;">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 mr-2" style="height: 10px; border-radius: 5px;">
                                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $pct; ?>%;" aria-valuenow="<?php echo $pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <span class="font-weight-bold text-muted" style="font-size: 11px; width: 45px;"><?php echo $pct; ?>%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            No module access records found for the selected period.
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
