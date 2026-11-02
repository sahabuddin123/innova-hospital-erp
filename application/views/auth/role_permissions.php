<!--sidebar end-->
<!--main content start-->
<div class="content-wrapper bg-light">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-sliders-h text-primary mr-2"></i>
                        Configure Permissions: <span class="text-primary"><?php echo html_escape($group->name); ?></span>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home"><i class="fas fa-home"></i> <?php echo lang('home'); ?></a></li>
                        <li class="breadcrumb-item"><a href="auth/permissions"><?php echo lang('role_permissions') ?: 'Role Permissions'; ?></a></li>
                        <li class="breadcrumb-item active"><?php echo html_escape($group->name); ?></li>
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

            <!-- Role Summary Card -->
            <div class="card card-outline card-primary mb-3">
                <div class="card-body py-2 d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <span class="badge badge-primary px-3 py-2 mr-2 font-weight-bold" style="font-size: 0.95rem;">
                            <i class="fas fa-user-tag mr-1"></i> Role: <?php echo html_escape($group->name); ?>
                        </span>
                        <span class="text-muted"><?php echo html_escape($group->description ?: 'System role permissions'); ?></span>
                        <?php if ($group->name === 'superadmin'): ?>
                            <span class="badge badge-danger ml-2"><i class="fas fa-lock mr-1"></i> Unrestricted Super Admin</span>
                        <?php endif; ?>
                    </div>
                    <div class="mt-2 mt-sm-0">
                        <button type="button" class="btn btn-outline-secondary btn-sm mr-2" id="btnExpandAll">
                            <i class="fas fa-expand-alt mr-1"></i> <?php echo lang('expand_all') ?: 'Expand All'; ?>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm mr-2" id="btnCollapseAll">
                            <i class="fas fa-compress-alt mr-1"></i> <?php echo lang('collapse_all') ?: 'Collapse All'; ?>
                        </button>
                        <a href="auth/permissions" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Permission Matrix Form -->
            <form id="formRolePermissions" method="POST" action="auth/save_role_permissions">
                <input type="hidden" name="group_id" value="<?php echo $group->id; ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="reason" id="inputChangeReason" value="">

                <div class="accordion" id="accordionPermissions">
                    <?php 
                    $group_index = 0;
                    foreach ($permission_groups as $pg_key => $pg_info): 
                        $group_index++;
                        $existing = $existing_perms[$pg_key] ?? [];
                        $is_view = !empty($existing['can_view']);
                        $is_print = !empty($existing['can_print']);
                        $is_pdf = !empty($existing['can_export_pdf']);
                        $is_excel = !empty($existing['can_export_excel']);
                        $is_csv = !empty($existing['can_export_csv']);
                        $is_details = !empty($existing['can_view_details']);
                        $is_sensitive = !empty($existing['can_view_sensitive']);
                    ?>
                        <div class="card card-default card-outline mb-3 elevation-1 perm-group-card" data-group-key="<?php echo $pg_key; ?>">
                            <div class="card-header bg-white d-flex align-items-center py-2" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse_<?php echo $pg_key; ?>">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <span class="badge <?php echo $pg_info['badge']; ?> mr-2 px-2 py-1">
                                        <i class="<?php echo $pg_info['icon']; ?>"></i> Group <?php echo $group_index; ?>
                                    </span>
                                    <h5 class="card-title font-weight-bold text-dark mb-0">
                                        <?php echo html_escape($pg_info['name']); ?>
                                    </h5>
                                    <small class="text-muted ml-3 d-none d-md-inline">
                                        <?php echo html_escape($pg_info['description']); ?>
                                    </small>
                                </div>
                                <div class="d-flex align-items-center" onclick="event.stopPropagation();">
                                    <button type="button" class="btn btn-xs btn-outline-success mr-2 btn-group-select-all" data-target-group="<?php echo $pg_key; ?>" title="Grant all in this group">
                                        <i class="fas fa-check-double mr-1"></i> Select Group
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary mr-3 btn-group-clear-all" data-target-group="<?php echo $pg_key; ?>" title="Clear all in this group">
                                        <i class="fas fa-ban mr-1"></i> Clear Group
                                    </button>
                                    <i class="fas fa-chevron-down text-muted accordion-toggle-icon"></i>
                                </div>
                            </div>

                            <div id="collapse_<?php echo $pg_key; ?>" class="collapse show" data-parent="#accordionPermissions">
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="bg-light text-center">
                                            <tr>
                                                <th style="width: 25%;" class="text-left">Permission Action</th>
                                                <th style="width: 10%;">VIEW</th>
                                                <th style="width: 10%;">PRINT</th>
                                                <th style="width: 10%;">PDF</th>
                                                <th style="width: 10%;">EXCEL</th>
                                                <th style="width: 10%;">CSV</th>
                                                <th style="width: 12%;">DETAILS</th>
                                                <th style="width: 13%;">SENSITIVE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-left align-middle font-weight-bold">
                                                    <i class="fas fa-layer-group text-primary mr-1"></i> All Sub-modules & Endpoints
                                                    <input type="hidden" name="permissions[<?php echo $pg_key; ?>][report_key]" value="all">
                                                </td>
                                                <!-- VIEW -->
                                                <td class="text-center align-middle bg-light-primary">
                                                    <div class="custom-control custom-switch d-inline-block">
                                                        <input type="checkbox" class="custom-control-input perm-cb perm-view" 
                                                               id="cb_<?php echo $pg_key; ?>_view" 
                                                               name="permissions[<?php echo $pg_key; ?>][can_view]" 
                                                               value="1" <?php echo $is_view ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="cb_<?php echo $pg_key; ?>_view"></label>
                                                    </div>
                                                </td>
                                                <!-- PRINT -->
                                                <td class="text-center align-middle">
                                                    <div class="custom-control custom-switch d-inline-block">
                                                        <input type="checkbox" class="custom-control-input perm-cb perm-action" 
                                                               id="cb_<?php echo $pg_key; ?>_print" 
                                                               name="permissions[<?php echo $pg_key; ?>][can_print]" 
                                                               value="1" <?php echo $is_print ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="cb_<?php echo $pg_key; ?>_print"></label>
                                                    </div>
                                                </td>
                                                <!-- PDF -->
                                                <td class="text-center align-middle">
                                                    <div class="custom-control custom-switch d-inline-block">
                                                        <input type="checkbox" class="custom-control-input perm-cb perm-action" 
                                                               id="cb_<?php echo $pg_key; ?>_pdf" 
                                                               name="permissions[<?php echo $pg_key; ?>][can_export_pdf]" 
                                                               value="1" <?php echo $is_pdf ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="cb_<?php echo $pg_key; ?>_pdf"></label>
                                                    </div>
                                                </td>
                                                <!-- EXCEL -->
                                                <td class="text-center align-middle">
                                                    <div class="custom-control custom-switch d-inline-block">
                                                        <input type="checkbox" class="custom-control-input perm-cb perm-action" 
                                                               id="cb_<?php echo $pg_key; ?>_excel" 
                                                               name="permissions[<?php echo $pg_key; ?>][can_export_excel]" 
                                                               value="1" <?php echo $is_excel ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="cb_<?php echo $pg_key; ?>_excel"></label>
                                                    </div>
                                                </td>
                                                <!-- CSV -->
                                                <td class="text-center align-middle">
                                                    <div class="custom-control custom-switch d-inline-block">
                                                        <input type="checkbox" class="custom-control-input perm-cb perm-action" 
                                                               id="cb_<?php echo $pg_key; ?>_csv" 
                                                               name="permissions[<?php echo $pg_key; ?>][can_export_csv]" 
                                                               value="1" <?php echo $is_csv ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="cb_<?php echo $pg_key; ?>_csv"></label>
                                                    </div>
                                                </td>
                                                <!-- DETAILS -->
                                                <td class="text-center align-middle">
                                                    <div class="custom-control custom-switch d-inline-block">
                                                        <input type="checkbox" class="custom-control-input perm-cb perm-action" 
                                                               id="cb_<?php echo $pg_key; ?>_details" 
                                                               name="permissions[<?php echo $pg_key; ?>][can_view_details]" 
                                                               value="1" <?php echo $is_details ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="cb_<?php echo $pg_key; ?>_details"></label>
                                                    </div>
                                                </td>
                                                <!-- SENSITIVE -->
                                                <td class="text-center align-middle bg-light-warning">
                                                    <div class="custom-control custom-switch d-inline-block">
                                                        <input type="checkbox" class="custom-control-input perm-cb perm-action" 
                                                               id="cb_<?php echo $pg_key; ?>_sensitive" 
                                                               name="permissions[<?php echo $pg_key; ?>][can_view_sensitive]" 
                                                               value="1" <?php echo $is_sensitive ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label text-danger font-weight-bold" for="cb_<?php echo $pg_key; ?>_sensitive"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Floating Bottom Actions Bar -->
                <div class="card elevation-2 sticky-bottom p-3 mb-4 bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted"><i class="fas fa-shield-alt mr-1 text-primary"></i> All changes are transaction-safe and logged to the Forensic Audit Trail.</span>
                        </div>
                        <div>
                            <a href="auth/permissions" class="btn btn-default mr-2">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                            <button type="button" class="btn btn-success px-4 font-weight-bold" id="btnSavePermissions">
                                <i class="fas fa-save mr-1"></i> <?php echo lang('save_permissions') ?: 'Save Changes'; ?>
                            </button>
                        </div>
                    </div>
                </div>

            </form>

            <!-- Recent Role Audit History -->
            <div class="card card-outline card-secondary mb-4 elevation-1">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-history mr-2 text-secondary"></i>Recent Permission Changes for <?php echo html_escape($group->name); ?>
                    </h3>
                    <div class="card-tools ml-auto">
                        <a href="auth/permission_audit?group_id=<?php echo $group->id; ?>" class="btn btn-xs btn-outline-secondary">
                            View Full History <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover table-striped table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 160px;">Date & Time</th>
                                <th>Group / Module</th>
                                <th>Action Changed</th>
                                <th class="text-center" style="width: 90px;">Old</th>
                                <th class="text-center" style="width: 90px;">New</th>
                                <th>Modified By</th>
                                <th>Reason</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($audit_logs)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-3">No permission modifications recorded for this role yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($audit_logs as $log): ?>
                                    <tr>
                                        <td><small><?php echo date('Y-m-d H:i:s', strtotime($log['changed_at'])); ?></small></td>
                                        <td><span class="badge badge-light border"><?php echo html_escape($log['permission_group']); ?></span></td>
                                        <td><code><?php echo html_escape($log['action_changed']); ?></code></td>
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

<!-- Confirmation Modal -->
<div class="modal fade" id="modalConfirmSave" tabindex="-1" role="dialog" aria-labelledby="modalConfirmSaveLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalConfirmSaveLabel">
                    <i class="fas fa-shield-alt mr-2"></i> Confirm Permission Updates
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to update permissions for role <strong><?php echo html_escape($group->name); ?></strong>?</p>
                <div class="alert alert-warning py-2 mb-3">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Changes will immediately affect active users and export capabilities.
                </div>
                <div class="form-group mb-0">
                    <label for="modalReasonInput" class="font-weight-bold text-dark">Reason for Modification (Optional):</label>
                    <input type="text" class="form-control" id="modalReasonInput" placeholder="e.g. Granted Excel export for end-of-month reporting">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success font-weight-bold" id="btnConfirmSubmit">
                    <i class="fas fa-check mr-1"></i> Confirm & Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Expand / Collapse All
    $('#btnExpandAll').on('click', function () {
        $('#accordionPermissions .collapse').collapse('show');
    });
    $('#btnCollapseAll').on('click', function () {
        $('#accordionPermissions .collapse').collapse('hide');
    });

    // 2. Select All in a Group
    $('.btn-group-select-all').on('click', function () {
        var groupKey = $(this).data('target-group');
        $('.perm-group-card[data-group-key="' + groupKey + '"] input[type="checkbox"]').prop('checked', true);
    });

    // 3. Clear All in a Group
    $('.btn-group-clear-all').on('click', function () {
        var groupKey = $(this).data('target-group');
        $('.perm-group-card[data-group-key="' + groupKey + '"] input[type="checkbox"]').prop('checked', false);
    });

    // 4. If VIEW unchecked, automatically uncheck export & detail permissions
    $('.perm-view').on('change', function () {
        var card = $(this).closest('.perm-group-card');
        if (!this.checked) {
            card.find('.perm-action').prop('checked', false);
        }
    });

    // 5. If any action checked, ensure VIEW is automatically checked
    $('.perm-action').on('change', function () {
        if (this.checked) {
            var card = $(this).closest('.perm-group-card');
            card.find('.perm-view').prop('checked', true);
        }
    });

    // 6. Save Flow with Modal Confirmation
    $('#btnSavePermissions').on('click', function () {
        $('#modalReasonInput').val('');
        $('#modalConfirmSave').modal('show');
    });

    $('#btnConfirmSubmit').on('click', function () {
        var reason = $('#modalReasonInput').val();
        $('#inputChangeReason').val(reason);
        $('#modalConfirmSave').modal('hide');
        $('#formRolePermissions').submit();
    });
});
</script>
