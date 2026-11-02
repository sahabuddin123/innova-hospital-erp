<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-hourglass-start text-info mr-2"></i><?php echo lang('opening_balance_report'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Verified Account Opening Balances</small>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#editOpeningModal">
                        <i class="fas fa-edit mr-1"></i> Adjust Opening Balances
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary ml-2 shadow-sm" onclick="window.print();">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <div class="container-fluid">
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'opening_balance']); ?>

            <!-- Mandatory Rule #16 Notice -->
            <div class="acc-alert-notice">
                <i class="fas fa-shield-alt mr-1 text-info"></i>
                <strong>Opening Balance Integrity Rules:</strong>
                In strict accordance with Prompt Rule #16, opening balances must be explicitly entered or migrated from verified records. Total Opening Debit must equal Total Opening Credit. The system will never automatically infer or fabricate opening balances from incomplete historical transactions.
            </div>

            <!-- KPI Cards -->
            <div class="acc-kpi-grid">
                <div class="acc-kpi-card info">
                    <div class="acc-kpi-label">Total Opening Debit</div>
                    <div class="acc-kpi-val text-primary">৳ <?php echo number_format($report['total_opening_dr'], 2); ?></div>
                    <div class="acc-kpi-sub">Debit Balances Brought Forward</div>
                </div>
                <div class="acc-kpi-card success">
                    <div class="acc-kpi-label">Total Opening Credit</div>
                    <div class="acc-kpi-val text-success">৳ <?php echo number_format($report['total_opening_cr'], 2); ?></div>
                    <div class="acc-kpi-sub">Credit Balances Brought Forward</div>
                </div>
                <div class="acc-kpi-card <?php echo $report['is_balanced'] ? 'success' : 'danger'; ?>">
                    <div class="acc-kpi-label">Opening Equality Status</div>
                    <div class="acc-kpi-val <?php echo $report['is_balanced'] ? 'text-success' : 'text-danger'; ?>">
                        <?php echo $report['is_balanced'] ? 'BALANCED' : 'VARIANCE'; ?>
                    </div>
                    <div class="acc-kpi-sub">
                        Diff: ৳ <?php echo number_format($report['difference'], 2); ?>
                    </div>
                </div>
                <div class="acc-kpi-card primary">
                    <div class="acc-kpi-label">Accounts Configured</div>
                    <div class="acc-kpi-val"><?php echo count($report['accounts']); ?></div>
                    <div class="acc-kpi-sub">Active Chart of Accounts</div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="acc-card">
                <div class="acc-card-header">
                    <h5 class="acc-card-title"><i class="fas fa-balance-scale text-muted"></i> Account Opening Balance Register</h5>
                </div>
                <div class="acc-card-body p-0">
                    <div class="table-responsive">
                        <table class="table acc-table mb-0 datatable-export">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Account Code</th>
                                    <th>Account Title</th>
                                    <th>Account Type</th>
                                    <th>Group</th>
                                    <th class="text-right" style="width: 160px;">Opening Debit (৳)</th>
                                    <th class="text-right" style="width: 160px;">Opening Credit (৳)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report['accounts'] as $acc): ?>
                                    <tr>
                                        <td><span class="font-weight-bold text-dark"><?php echo $acc['account_code']; ?></span></td>
                                        <td>
                                            <div class="font-weight-bold"><?php echo htmlspecialchars($acc['account_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($acc['description'] ?? ''); ?></small>
                                        </td>
                                        <td><span class="badge badge-light border"><?php echo ucfirst($acc['account_type']); ?></span></td>
                                        <td><?php echo htmlspecialchars($acc['group_name']); ?></td>
                                        <td class="acc-amount acc-dr text-right">
                                            <?php echo $acc['opening_debit'] > 0 ? number_format($acc['opening_debit'], 2) : '-'; ?>
                                        </td>
                                        <td class="acc-amount acc-cr text-right">
                                            <?php echo $acc['opening_credit'] > 0 ? number_format($acc['opening_credit'], 2) : '-'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold border-top" style="font-size: 14px;">
                                    <td colspan="4" class="text-right text-uppercase">Total Opening Balances:</td>
                                    <td class="acc-amount acc-dr text-right">৳ <?php echo number_format($report['total_opening_dr'], 2); ?></td>
                                    <td class="acc-amount acc-cr text-right">৳ <?php echo number_format($report['total_opening_cr'], 2); ?></td>
                                </tr>
                                <tr class="<?php echo $report['is_balanced'] ? 'table-success' : 'table-danger'; ?> font-weight-bold">
                                    <td colspan="4" class="text-right text-uppercase">Balance Status:</td>
                                    <td colspan="2" class="text-center">
                                        <?php if ($report['is_balanced']): ?>
                                            <i class="fas fa-check-circle text-success mr-1"></i> VERIFIED EQUALITY: OPENING DR = OPENING CR (0.00 Variance)
                                        <?php else: ?>
                                            <i class="fas fa-times-circle text-danger mr-1"></i> UNBALANCED: Variance of ৳ <?php echo number_format($report['difference'], 2); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tfoot>
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

<!-- Edit Opening Balance Modal -->
<div class="modal fade" id="editOpeningModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="editOpeningForm" method="post" action="finance/saveOpeningBalancesAction">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i> Update Account Opening Balances</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">Ensure that Total Opening Debit exactly equals Total Opening Credit before saving.</p>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Account</th>
                                    <th style="width: 160px;" class="text-right">Debit (Dr) ৳</th>
                                    <th style="width: 160px;" class="text-right">Credit (Cr) ৳</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report['accounts'] as $acc): ?>
                                    <tr class="modal-op-row" data-id="<?php echo $acc['id']; ?>">
                                        <td>
                                            <span class="font-weight-bold">[<?php echo $acc['account_code']; ?>]</span>
                                            <?php echo htmlspecialchars($acc['account_name']); ?>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm text-right modal-op-dr" value="<?php echo $acc['opening_debit']; ?>">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm text-right modal-op-cr" value="<?php echo $acc['opening_credit']; ?>">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td class="text-right">Totals:</td>
                                    <td class="text-right text-primary" id="modalTotalDr">৳ 0.00</td>
                                    <td class="text-right text-success" id="modalTotalCr">৳ 0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-right">Balance Check:</td>
                                    <td colspan="2" class="text-center" id="modalBalanceCheck">
                                        <span class="badge badge-success">Balanced</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-info" id="saveOpeningBtn">Save Opening Balances</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('.datatable-export')) {
        $('.datatable-export').DataTable().destroy();
    }
    $('.datatable-export').DataTable({
        responsive: true,
        pageLength: 50,
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
        buttons: [
            { extend: 'copy', className: 'btn btn-xs btn-outline-secondary' },
            { extend: 'excel', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Opening_Balances' },
            { extend: 'csv', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Opening_Balances' },
            { extend: 'pdf', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Opening_Balances' },
            { extend: 'print', className: 'btn btn-xs btn-outline-secondary' }
        ]
    });

    function checkModalTotals() {
        var dr = 0, cr = 0;
        $('.modal-op-row').each(function() {
            dr += parseFloat($(this).find('.modal-op-dr').val()) || 0;
            cr += parseFloat($(this).find('.modal-op-cr').val()) || 0;
        });

        $('#modalTotalDr').text('৳ ' + dr.toFixed(2));
        $('#modalTotalCr').text('৳ ' + cr.toFixed(2));

        var diff = Math.abs(dr - cr);
        if (diff < 0.009) {
            $('#modalBalanceCheck').html('<span class="badge badge-success"><i class="fas fa-check-circle"></i> Balanced (৳ 0.00 Diff)</span>');
            $('#saveOpeningBtn').prop('disabled', false);
        } else {
            $('#modalBalanceCheck').html('<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Out of Balance (৳ ' + diff.toFixed(2) + ' Diff)</span>');
            $('#saveOpeningBtn').prop('disabled', true);
        }
    }

    $(document).on('input', '.modal-op-dr, .modal-op-cr', function() {
        checkModalTotals();
    });

    $('#editOpeningModal').on('shown.bs.modal', function() {
        checkModalTotals();
    });

    $('#editOpeningForm').on('submit', function(e) {
        e.preventDefault();
        var accountsData = [];
        $('.modal-op-row').each(function() {
            var accId = $(this).data('id');
            var dr = parseFloat($(this).find('.modal-op-dr').val()) || 0;
            var cr = parseFloat($(this).find('.modal-op-cr').val()) || 0;
            accountsData.push({
                account_id: accId,
                opening_debit: dr,
                opening_credit: cr
            });
        });

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: { accounts: JSON.stringify(accountsData) },
            dataType: 'json',
            success: function(resp) {
                if (resp.status) {
                    alert('Opening balances updated successfully.');
                    location.reload();
                } else {
                    alert(resp.message || 'Error updating opening balances.');
                }
            },
            error: function() {
                alert('Server error occurred.');
            }
        });
    });
});
</script>
