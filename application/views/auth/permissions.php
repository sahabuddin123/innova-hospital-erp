<!--sidebar end-->
<!--main content start-->
<div class="content-wrapper bg-light">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-user-lock text-primary mr-2"></i><?php echo lang('role_permission_management') ?: 'Role & Permission Management'; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home"><i class="fas fa-home"></i> <?php echo lang('home'); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo lang('role_permissions') ?: 'Role Permissions'; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <?php if ($this->session->flashdata('feedback')): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle mr-1"></i> <?php echo $this->session->flashdata('feedback'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Metric Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info elevation-2">
                        <div class="inner">
                            <h3><?php echo count($groups); ?></h3>
                            <p>Total ERP Roles</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success elevation-2">
                        <div class="inner">
                            <h3>9</h3>
                            <p>Report Permission Groups</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning elevation-2">
                        <div class="inner">
                            <h3>10</h3>
                            <p>Standard Action Capabilities</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-key"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary elevation-2">
                        <div class="inner">
                            <h3><?php echo $total_audit_logs; ?></h3>
                            <p>Permission Audit Events</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <a href="auth/permission_audit" class="small-box-footer">
                            View Audit Trail <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Role Matrix Card -->
            <div class="card card-primary card-outline elevation-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-list-alt mr-2 text-primary"></i>System Roles & Access Overview
                    </h3>
                    <div class="card-tools ml-auto">
                        <a href="auth/permission_audit" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-history mr-1"></i> Audit Trail
                        </a>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 60px;" class="text-center">#</th>
                                <th>Role Name</th>
                                <th>Description</th>
                                <th class="text-center" style="width: 130px;">Assigned Users</th>
                                <th class="text-center" style="width: 150px;">Visible Modules</th>
                                <th class="text-center" style="width: 140px;">Export Enabled</th>
                                <th class="text-center" style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groups as $i => $g): 
                                $stats = $group_stats[$g->id] ?? ['user_count' => 0, 'view_count' => 0, 'export_count' => 0];
                                $is_superadmin = ($g->name === 'superadmin');
                                $is_admin = ($g->name === 'admin');
                            ?>
                                <tr>
                                    <td class="text-center font-weight-bold"><?php echo $g->id; ?></td>
                                    <td>
                                        <span class="font-weight-bold text-dark"><?php echo html_escape($g->name); ?></span>
                                        <?php if ($is_superadmin): ?>
                                            <span class="badge badge-danger ml-1">Super Admin</span>
                                        <?php elseif ($is_admin): ?>
                                            <span class="badge badge-primary ml-1">Administrator</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary ml-1">Role</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted"><?php echo html_escape($g->description ?: 'System ERP user group'); ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-pill badge-info px-2 py-1"><?php echo $stats['user_count']; ?> users</span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($is_superadmin): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-infinity mr-1"></i> All (9/9)</span>
                                        <?php else: ?>
                                            <span class="badge badge-<?php echo ($stats['view_count'] > 0) ? 'primary' : 'light'; ?> px-2 py-1">
                                                <?php echo $stats['view_count']; ?> / 9 Groups
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($is_superadmin): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Full Access</span>
                                        <?php else: ?>
                                            <span class="badge badge-<?php echo ($stats['export_count'] > 0) ? 'info' : 'secondary'; ?> px-2 py-1">
                                                <?php echo $stats['export_count']; ?> Groups
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="auth/role_permissions/<?php echo $g->id; ?>" class="btn btn-primary" title="Configure Permissions">
                                                <i class="fas fa-sliders-h mr-1"></i> Configure
                                            </a>
                                            <a href="auth/permission_audit?group_id=<?php echo $g->id; ?>" class="btn btn-outline-secondary" title="View Permission Change Log">
                                                <i class="fas fa-history"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
