<!--sidebar end-->
<!--main content start-->
<div class="content-wrapper bg-light">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-history text-secondary mr-2"></i>
                        <?php echo lang('permission_audit_trail') ?: 'Role Permission Audit Trail'; ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home"><i class="fas fa-home"></i> <?php echo lang('home'); ?></a></li>
                        <li class="breadcrumb-item"><a href="auth/permissions"><?php echo lang('role_permissions') ?: 'Role Permissions'; ?></a></li>
                        <li class="breadcrumb-item active"><?php echo lang('permission_audit') ?: 'Permission Audit'; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Filter Card -->
            <div class="card card-outline card-secondary mb-3 elevation-1">
                <div class="card-body py-3">
                    <form method="GET" action="auth/permission_audit" class="form-inline d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-2 mb-sm-0">
                            <label class="mr-2 font-weight-bold text-muted"><i class="fas fa-filter mr-1"></i> Filter by Role:</label>
                            <select name="group_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                <option value="">-- All Roles --</option>
                                <?php foreach ($groups as $g): ?>
                                    <option value="<?php echo $g->id; ?>" <?php echo ($selected_group_id == $g->id) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($g->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                            <?php if (!empty($selected_group_id)): ?>
                                <a href="auth/permission_audit" class="btn btn-outline-secondary btn-sm ml-2">Reset</a>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="badge badge-secondary px-3 py-2 font-weight-normal">
                                Total Events: <strong><?php echo $total_logs; ?></strong>
                            </span>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Audit Trail Log Table -->
            <div class="card card-default elevation-2">
                <div class="card-header bg-white d-flex align-items-center">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-shield-alt mr-2 text-primary"></i>Immutable Forensic Permission Change Records
                    </h3>
                    <div class="card-tools ml-auto">
                        <a href="auth/permissions" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Roles
                        </a>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 70px;" class="text-center">#</th>
                                <th style="width: 170px;">Changed At</th>
                                <th style="width: 140px;">Role Name</th>
                                <th style="width: 200px;">Permission Group</th>
                                <th style="width: 160px;">Action Changed</th>
                                <th class="text-center" style="width: 90px;">Previous</th>
                                <th class="text-center" style="width: 90px;">New Value</th>
                                <th>Changed By</th>
                                <th>Reason</th>
                                <th style="width: 120px;">IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($audit_logs)): ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block text-secondary"></i>
                                        No permission changes recorded in the audit log yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($audit_logs as $log): ?>
                                    <tr>
                                        <td class="text-center font-weight-bold text-muted"><?php echo $log['id']; ?></td>
                                        <td><small class="font-weight-bold"><?php echo date('Y-m-d H:i:s', strtotime($log['changed_at'])); ?></small></td>
                                        <td><span class="badge badge-info px-2 py-1"><?php echo html_escape($log['role_name']); ?></span></td>
                                        <td><code><?php echo html_escape($log['permission_group']); ?></code></td>
                                        <td><strong><?php echo html_escape($log['action_changed']); ?></strong></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?php echo ($log['previous_value'] == '1') ? 'success' : 'secondary'; ?>">
                                                <?php echo ($log['previous_value'] == '1') ? 'ALLOWED' : 'DENIED'; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-<?php echo ($log['new_value'] == '1') ? 'success' : 'secondary'; ?>">
                                                <?php echo ($log['new_value'] == '1') ? 'ALLOWED' : 'DENIED'; ?>
                                            </span>
                                        </td>
                                        <td><i class="fas fa-user-edit mr-1 text-muted"></i><?php echo html_escape($log['changed_by_name']); ?></td>
                                        <td><small class="text-muted"><?php echo html_escape($log['reason'] ?: 'Standard update'); ?></small></td>
                                        <td><small class="text-monospace"><?php echo html_escape($log['ip_address'] ?: '-'); ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
