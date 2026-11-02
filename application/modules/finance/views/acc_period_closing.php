<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-calendar-check text-info mr-2"></i><?php echo lang('financial_period_closing'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Financial Period Setup & Period Lock Control</small>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary shadow-sm" onclick="window.print();">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <div class="container-fluid">
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'period_closing']); ?>

            <!-- Mandatory Rule #17 Notice -->
            <div class="acc-alert-notice">
                <i class="fas fa-lock mr-1 text-info"></i>
                <strong>Financial Period Closing Rules:</strong>
                In strict accordance with Prompt Rule #17, closed accounting periods must not accept journal modifications or new postings. Reopening a closed period requires explicit authorization and logs a permanent reason to the Accounting Audit Trail.
            </div>

            <!-- Table Card -->
            <div class="acc-card">
                <div class="acc-card-header">
                    <h5 class="acc-card-title"><i class="fas fa-calendar-alt text-muted"></i> Accounting Periods & Closing Status Register</h5>
                </div>
                <div class="acc-card-body p-0">
                    <div class="table-responsive">
                        <table class="table acc-table mb-0 datatable-export">
                            <thead>
                                <tr>
                                    <th>Period Name</th>
                                    <th>Fiscal Year</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th class="text-center" style="width: 120px;">Status</th>
                                    <th>Closed Details</th>
                                    <th class="text-center" style="width: 140px;">Control Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($periods as $p): ?>
                                    <tr>
                                        <td><span class="font-weight-bold text-dark"><?php echo htmlspecialchars($p['period_name']); ?></span></td>
                                        <td><span class="badge badge-light border"><?php echo htmlspecialchars($p['fiscal_year']); ?></span></td>
                                        <td><?php echo date('d-m-Y', strtotime($p['start_date'])); ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($p['end_date'])); ?></td>
                                        <td class="text-center">
                                            <?php if ($p['status'] === 'closed'): ?>
                                                <span class="badge badge-danger p-2"><i class="fas fa-lock mr-1"></i> CLOSED</span>
                                            <?php else: ?>
                                                <span class="badge badge-success p-2"><i class="fas fa-lock-open mr-1"></i> OPEN</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($p['status'] === 'closed'): ?>
                                                <small class="text-muted">
                                                    Closed by: User #<?php echo htmlspecialchars($p['closed_by'] ?: 'Admin'); ?><br>
                                                    Date: <?php echo !empty($p['closed_at']) ? date('d-m-Y H:i', $p['closed_at']) : 'System Default'; ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted small">Active / Accepting Journal Entries</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($p['status'] === 'open'): ?>
                                                <button type="button" class="btn btn-xs btn-outline-danger close-period-btn" data-id="<?php echo $p['id']; ?>" data-name="<?php echo htmlspecialchars($p['period_name']); ?>">
                                                    <i class="fas fa-lock mr-1"></i> Close Period
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-xs btn-outline-warning reopen-period-btn" data-id="<?php echo $p['id']; ?>" data-name="<?php echo htmlspecialchars($p['period_name']); ?>">
                                                    <i class="fas fa-unlock mr-1"></i> Reopen
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Signatures for print -->
            <div class="acc-signatures-block">
                <div class="acc-sig-grid">
                    <div class="acc-sig-line">Prepared By<br><small>Senior Accountant</small></div>
                    <div class="acc-sig-line">Checked & Verified By<br><small>Head of Accounts & Finance</small></div>
                    <div class="acc-sig-line">Approved By<br><small>Managing Director / CFO</small></div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('.close-period-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        if (confirm('Are you sure you want to CLOSE period "' + name + '"? Once closed, no new journal entries or edits will be permitted for dates within this period.')) {
            $.ajax({
                url: 'finance/closePeriodAction',
                type: 'POST',
                data: { period_id: id },
                dataType: 'json',
                success: function(resp) {
                    if (resp.status) {
                        alert('Period closed successfully.');
                        location.reload();
                    } else {
                        alert(resp.message || 'Error closing period.');
                    }
                }
            });
        }
    });

    $('.reopen-period-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var reason = prompt('Please enter the mandatory audit reason for REOPENING period "' + name + '":');
        if (reason) {
            $.ajax({
                url: 'finance/reopenPeriodAction',
                type: 'POST',
                data: { period_id: id, reason: reason },
                dataType: 'json',
                success: function(resp) {
                    if (resp.status) {
                        alert('Period reopened. Audit trail record logged.');
                        location.reload();
                    } else {
                        alert(resp.message || 'Error reopening period.');
                    }
                }
            });
        }
    });
});
</script>
