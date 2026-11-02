<link rel="stylesheet" href="common/extranal/css/logs/activity_reports.css">

<div class="content-wrapper bg-light">
    <section class="content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-print text-teal mr-2" style="color: #20c997;"></i><?php echo lang('print_export_activity'); ?>
                    </h1>
                    <small class="text-muted">Surveillance of data exfiltration, printed receipts, spreadsheets, and PDF exports</small>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php $this->load->view('logs/inc_activity_nav', ['active_report' => 'print_export']); ?>

            <!-- Filter Card -->
            <div class="d-flex justify-content-end mb-2"><?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => 'print_export']); ?></div>
            <div class="act-filter-card">
                <form method="GET" action="logs/printExportActivity" class="act-filter-row">
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
                        <label>Action</label>
                        <select name="action_type" class="form-control">
                            <option value="">All Print & Exports</option>
                            <option value="PRINT" <?php echo ($filters['action_type'] ?? '') === 'PRINT' ? 'selected' : ''; ?>>PRINT</option>
                            <option value="EXPORT_EXCEL" <?php echo ($filters['action_type'] ?? '') === 'EXPORT_EXCEL' ? 'selected' : ''; ?>>EXCEL EXPORT</option>
                            <option value="EXPORT_PDF" <?php echo ($filters['action_type'] ?? '') === 'EXPORT_PDF' ? 'selected' : ''; ?>>PDF EXPORT</option>
                            <option value="EXPORT_CSV" <?php echo ($filters['action_type'] ?? '') === 'EXPORT_CSV' ? 'selected' : ''; ?>>CSV EXPORT</option>
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
                        <i class="fas fa-file-export text-teal" style="color: #20c997;"></i> Document Prints & Data Exports (<?php echo count($activities); ?>)
                    </h3>
                </div>
                <div class="act-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Module</th>
                                    <th>Action Type</th>
                                    <th>Document / Record Ref</th>
                                    <th>Action Summary</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($activities)): ?>
                                    <?php foreach ($activities as $a): ?>
                                        <tr>
                                            <td><?php echo html_escape($a['created_at_formatted']); ?></td>
                                            <td><strong><?php echo html_escape($a['username'] ?: 'System'); ?></strong></td>
                                            <td><span class="badge badge-secondary"><?php echo html_escape($a['role'] ?: 'System'); ?></span></td>
                                            <td><span class="badge badge-info"><?php echo html_escape(strtoupper($a['module_name'])); ?></span></td>
                                            <td>
                                                <?php if (strpos($a['action_type'], 'PRINT') !== false): ?>
                                                    <span class="badge badge-primary"><i class="fas fa-print mr-1"></i>PRINT</span>
                                                <?php elseif (strpos($a['action_type'], 'EXCEL') !== false): ?>
                                                    <span class="badge badge-success"><i class="fas fa-file-excel mr-1"></i>EXCEL</span>
                                                <?php elseif (strpos($a['action_type'], 'PDF') !== false): ?>
                                                    <span class="badge badge-danger"><i class="fas fa-file-pdf mr-1"></i>PDF</span>
                                                <?php elseif (strpos($a['action_type'], 'CSV') !== false): ?>
                                                    <span class="badge badge-info"><i class="fas fa-file-csv mr-1"></i>CSV</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><?php echo html_escape($a['action_type']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?php echo html_escape($a['record_reference'] ?: '-'); ?></strong></td>
                                            <td><?php echo html_escape($a['description']); ?></td>
                                            <td><code><?php echo html_escape($a['ip_address']); ?></code></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            No print or export operations recorded for this period.
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
