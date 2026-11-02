<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-light">
    <section class="content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-shield-alt text-primary mr-2"></i><?php echo lang('master_financial_audit_trail'); ?>
                    </h1>
                    <p class="text-muted small mb-0">Immutable forensic audit trail across all 14 hospital financial subsystems</p>
                </div>
                <div class="col-sm-6 text-right">
                    <span class="badge badge-secondary p-2"><i class="fas fa-database mr-1"></i> Records Found: <?php echo count($logs ?? []); ?></span>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Navigation Sub-Bar -->
            <?php $this->load->view('finance/inc_financial_audit_nav'); ?>

            <!-- Filter Panel -->
            <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                    <form method="get" action="finance/financialAuditTrail" class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-muted mb-1">From Date</label>
                            <input type="date" name="start_date" value="<?php echo html_escape($start_date); ?>" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-muted mb-1">To Date</label>
                            <input type="date" name="end_date" value="<?php echo html_escape($end_date); ?>" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-muted mb-1">Module</label>
                            <select name="module_name" class="form-control form-control-sm">
                                <option value="">All Financial Modules</option>
                                <option value="billing" <?php echo ($filters['module_name'] ?? '') === 'billing' ? 'selected' : ''; ?>>Billing & Invoices</option>
                                <option value="payments" <?php echo ($filters['module_name'] ?? '') === 'payments' ? 'selected' : ''; ?>>Payments & Deposits</option>
                                <option value="due_collection" <?php echo ($filters['module_name'] ?? '') === 'due_collection' ? 'selected' : ''; ?>>Due Collection</option>
                                <option value="expense" <?php echo ($filters['module_name'] ?? '') === 'expense' ? 'selected' : ''; ?>>Expenses</option>
                                <option value="doctor_commission" <?php echo ($filters['module_name'] ?? '') === 'doctor_commission' ? 'selected' : ''; ?>>Doctor Commission</option>
                                <option value="referral_commission" <?php echo ($filters['module_name'] ?? '') === 'referral_commission' ? 'selected' : ''; ?>>Referral Commission</option>
                                <option value="referral_wallet" <?php echo ($filters['module_name'] ?? '') === 'referral_wallet' ? 'selected' : ''; ?>>Referral Wallet</option>
                                <option value="accounting" <?php echo ($filters['module_name'] ?? '') === 'accounting' ? 'selected' : ''; ?>>Double-Entry Accounts</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-muted mb-1">Action Type</label>
                            <select name="action_type" class="form-control form-control-sm">
                                <option value="">All Action Types</option>
                                <option value="CREATE" <?php echo ($filters['action_type'] ?? '') === 'CREATE' ? 'selected' : ''; ?>>CREATE</option>
                                <option value="UPDATE" <?php echo ($filters['action_type'] ?? '') === 'UPDATE' ? 'selected' : ''; ?>>UPDATE</option>
                                <option value="DELETE" <?php echo ($filters['action_type'] ?? '') === 'DELETE' ? 'selected' : ''; ?>>DELETE</option>
                                <option value="PAYMENT" <?php echo ($filters['action_type'] ?? '') === 'PAYMENT' ? 'selected' : ''; ?>>PAYMENT</option>
                                <option value="ADJUSTMENT" <?php echo ($filters['action_type'] ?? '') === 'ADJUSTMENT' ? 'selected' : ''; ?>>ADJUSTMENT</option>
                                <option value="REFUND" <?php echo ($filters['action_type'] ?? '') === 'REFUND' ? 'selected' : ''; ?>>REFUND</option>
                                <option value="REVERSAL" <?php echo ($filters['action_type'] ?? '') === 'REVERSAL' ? 'selected' : ''; ?>>REVERSAL</option>
                                <option value="APPROVE" <?php echo ($filters['action_type'] ?? '') === 'APPROVE' ? 'selected' : ''; ?>>APPROVE</option>
                                <option value="REJECT" <?php echo ($filters['action_type'] ?? '') === 'REJECT' ? 'selected' : ''; ?>>REJECT</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-muted mb-1">Suspicious / Risk</label>
                            <select name="is_suspicious" class="form-control form-control-sm">
                                <option value="">All Events</option>
                                <option value="1" <?php echo ($filters['is_suspicious'] ?? '') === '1' ? 'selected' : ''; ?>>Flagged Suspicious Only</option>
                                <option value="0" <?php echo ($filters['is_suspicious'] ?? '') === '0' ? 'selected' : ''; ?>>Normal Events Only</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary btn-block">
                                <i class="fas fa-search mr-1"></i> Search Logs
                            </button>
                        </div>
                    <div class="ml-auto"><?php $this->load->view("report/inc_export_toolbar", ["export_report_key" => "fin_audit_trail"]); ?></div>
                    </form>
                </div>
            </div>

            <!-- Master Table Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-list-alt text-secondary mr-2"></i>Audit Records Ledger</h6>
                    <span class="text-muted small">Append-Only Immutable Records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="auditTable" class="table table-bordered table-hover table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Audit UUID</th>
                                    <th>Timestamp</th>
                                    <th>Module</th>
                                    <th>Action</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>User</th>
                                    <th>Description</th>
                                    <th>Status / Risk</th>
                                    <th class="text-center">Forensic View</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logs)): ?>
                                    <?php foreach ($logs as $l): ?>
                                        <tr>
                                            <td>
                                                <code class="small" title="<?php echo html_escape($l['audit_uuid']); ?>">
                                                    <?php echo html_escape(substr($l['audit_uuid'], 0, 14)); ?>...
                                                </code>
                                                <?php if ($l['is_historical'] == 1): ?>
                                                    <span class="badge badge-historical ml-1" title="Imported historical transaction">HIST</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="small text-nowrap"><?php echo html_escape($l['created_at_formatted']); ?></td>
                                            <td>
                                                <span class="badge badge-light border text-uppercase font-weight-bold">
                                                    <?php echo html_escape($l['module_name']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-action-<?php echo strtolower($l['action_type']); ?>">
                                                    <?php echo html_escape($l['action_type']); ?>
                                                </span>
                                            </td>
                                            <td><strong><?php echo html_escape($l['transaction_reference'] ?: '-' ); ?></strong></td>
                                            <td class="font-weight-bold text-right text-nowrap">
                                                ৳<?php echo number_format($l['amount'] ?? 0, 2); ?>
                                            </td>
                                            <td class="small"><?php echo html_escape($l['username'] ?: 'system'); ?></td>
                                            <td class="small"><?php echo html_escape($l['change_description']); ?></td>
                                            <td>
                                                <?php if ($l['is_suspicious'] == 1): ?>
                                                    <span class="badge badge-suspicious" title="<?php echo html_escape($l['suspicious_reason']); ?>">
                                                        <i class="fas fa-exclamation-triangle"></i> SUSPICIOUS
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-success small">Verified</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-xs btn-outline-primary view-audit-detail" data-id="<?php echo $l['id']; ?>" title="View Before & After Comparison">
                                                    <i class="fas fa-columns mr-1"></i> Diff View
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal Viewer -->
<div class="modal fade" id="auditDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-microscope mr-2"></i>Financial Audit Detail & Before/After Comparison</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" id="auditModalContent">
                <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#auditTable').DataTable({
        responsive: true,
        order: [[1, 'desc']],
        pageLength: 25,
        dom: "<'row'<'col-sm-6'l><'col-sm-6'Bf>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copyHtml5', text: '<i class="fas fa-copy mr-1"></i> Copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excelHtml5', text: '<i class="fas fa-file-excel mr-1"></i> Excel', className: 'btn btn-sm btn-outline-success' },
            { extend: 'csvHtml5', text: '<i class="fas fa-file-csv mr-1"></i> CSV', className: 'btn btn-sm btn-outline-info' },
            { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf mr-1"></i> PDF', className: 'btn btn-sm btn-outline-danger' },
            { extend: 'print', text: '<i class="fas fa-print mr-1"></i> Print', className: 'btn btn-sm btn-outline-dark' }
        ]
    });

    $(document).on('click', '.view-audit-detail', function() {
        var auditId = $(this).data('id');
        $('#auditDetailModal').modal('show');
        $('#auditModalContent').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>');

        $.ajax({
            url: 'finance/getAuditDetailAjax',
            type: 'GET',
            data: { id: auditId },
            dataType: 'json',
            success: function(resp) {
                if (resp.status && resp.data) {
                    var d = resp.data;
                    var html = '<div class="row mb-3">' +
                        '<div class="col-md-6"><strong>Audit UUID:</strong> <code>' + d.audit_uuid + '</code></div>' +
                        '<div class="col-md-6 text-right"><strong>Timestamp:</strong> ' + d.created_at_formatted + '</div>' +
                        '<div class="col-md-6 mt-2"><strong>Module:</strong> <span class="badge badge-light border text-uppercase">' + d.module_name + '</span></div>' +
                        '<div class="col-md-6 mt-2 text-right"><strong>Action:</strong> <span class="badge badge-action-' + d.action_type.toLowerCase() + '">' + d.action_type + '</span></div>' +
                        '<div class="col-md-6 mt-2"><strong>Reference:</strong> ' + (d.transaction_reference || '-') + '</div>' +
                        '<div class="col-md-6 mt-2 text-right"><strong>User:</strong> ' + d.username + ' (ID: ' + d.user_id + ')</div>' +
                        '<div class="col-md-6 mt-2"><strong>IP Address:</strong> ' + (d.ip_address || '-') + '</div>' +
                        '<div class="col-md-6 mt-2 text-right"><strong>Amount:</strong> ৳' + parseFloat(d.amount).toFixed(2) + '</div>' +
                        '</div>';

                    if (parseInt(d.is_suspicious) === 1) {
                        html += '<div class="alert alert-danger py-2 mb-3"><i class="fas fa-exclamation-triangle mr-2"></i><strong>Suspicious Alert:</strong> ' + (d.suspicious_reason || 'Flagged financial operation') + '</div>';
                    }

                    html += '<div class="card mb-3"><div class="card-header bg-light py-2"><strong>Change Description:</strong></div><div class="card-body py-2">' + (d.change_description || '-') + '</div></div>';

                    if (d.reason_for_change) {
                        html += '<div class="card mb-3"><div class="card-header bg-light py-2"><strong>Reason for Change:</strong></div><div class="card-body py-2 text-muted">' + d.reason_for_change + '</div></div>';
                    }

                    // Before vs After Comparison
                    html += '<h6 class="font-weight-bold mt-3 mb-2"><i class="fas fa-columns text-primary mr-1"></i>Before & After Value Comparison</h6>';

                    var prev = d.previous_value_decoded;
                    var next = d.new_value_decoded;

                    if (prev || next) {
                        html += '<table class="diff-table mb-2"><thead><tr><th>Field / Property</th><th>Previous Value (Before)</th><th>New Value (After)</th></tr></thead><tbody>';
                        var allKeys = {};
                        if (prev && typeof prev === 'object') { Object.keys(prev).forEach(function(k) { allKeys[k] = true; }); }
                        if (next && typeof next === 'object') { Object.keys(next).forEach(function(k) { allKeys[k] = true; }); }

                        var keys = Object.keys(allKeys);
                        if (keys.length > 0) {
                            keys.forEach(function(k) {
                                var pVal = (prev && prev[k] !== undefined) ? JSON.stringify(prev[k]) : '-';
                                var nVal = (next && next[k] !== undefined) ? JSON.stringify(next[k]) : '-';
                                var isDiff = (pVal !== nVal);
                                html += '<tr>' +
                                    '<td><strong>' + k + '</strong></td>' +
                                    '<td class="' + (isDiff && pVal !== '-' ? 'diff-old' : '') + '">' + pVal + '</td>' +
                                    '<td class="' + (isDiff && nVal !== '-' ? 'diff-new' : '') + '">' + nVal + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            html += '<tr><td colspan="3" class="text-center text-muted">No structured property changes recorded.</td></tr>';
                        }
                        html += '</tbody></table>';
                    } else if (d.previous_value || d.new_value) {
                        html += '<table class="diff-table mb-2"><thead><tr><th>Previous Raw Value</th><th>New Raw Value</th></tr></thead><tbody>' +
                            '<tr><td class="diff-old"><pre class="mb-0">' + (d.previous_value || '-') + '</pre></td>' +
                            '<td class="diff-new"><pre class="mb-0">' + (d.new_value || '-') + '</pre></td></tr></tbody></table>';
                    } else {
                        html += '<div class="alert alert-secondary py-2 text-center text-muted">No prior snapshot available (Event is an initial record creation or direct ledger entry).</div>';
                    }

                    $('#auditModalContent').html(html);
                } else {
                    $('#auditModalContent').html('<div class="alert alert-danger">Failed to load audit record details.</div>');
                }
            },
            error: function() {
                $('#auditModalContent').html('<div class="alert alert-danger">Error connecting to audit server.</div>');
            }
        });
    });
});
</script>
