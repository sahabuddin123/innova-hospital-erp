<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-book text-info mr-2"></i><?php echo lang('general_ledger'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Account-wise Running Ledger</small>
                </div>
                <div>
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'acc_general_ledger']); ?>
                </div>
            </div>
        </div>
    </section>

    <section class="content px-3">
        <div class="container-fluid">
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'general_ledger']); ?>

            <!-- Filter Box -->
            <div class="acc-filter-box">
                <form method="get" action="finance/generalLedger" class="row align-items-center">
                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold">Select General Ledger Account <span class="text-danger">*</span></label>
                        <select name="account_id" class="form-control form-control-sm select2" onchange="this.form.submit();">
                            <?php foreach ($accounts as $a): ?>
                                <option value="<?php echo $a['id']; ?>" <?php echo $selected_account_id == $a['id'] ? 'selected' : ''; ?>>
                                    [<?php echo $a['account_code']; ?>] <?php echo htmlspecialchars($a['account_name']); ?> (<?php echo ucfirst($a['account_type']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo $date_from; ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo $date_to; ?>">
                    </div>
                    <div class="col-md-1 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-info w-100 mt-4">Load</button>
                    </div>
                </form>
            </div>

            <?php if (!empty($ledger)): ?>
                <!-- KPI Cards for this account -->
                <div class="acc-kpi-grid">
                    <div class="acc-kpi-card primary">
                        <div class="acc-kpi-label">Opening Balance</div>
                        <div class="acc-kpi-val">৳ <?php echo number_format($ledger['opening_balance'], 2); ?></div>
                        <div class="acc-kpi-sub">As of <?php echo date('d M Y', strtotime($date_from)); ?></div>
                    </div>
                    <div class="acc-kpi-card info">
                        <div class="acc-kpi-label">Period Debits (Dr)</div>
                        <div class="acc-kpi-val text-primary">৳ <?php echo number_format($ledger['total_period_dr'], 2); ?></div>
                        <div class="acc-kpi-sub">Total Debit Vouchers</div>
                    </div>
                    <div class="acc-kpi-card warning">
                        <div class="acc-kpi-label">Period Credits (Cr)</div>
                        <div class="acc-kpi-val text-success">৳ <?php echo number_format($ledger['total_period_cr'], 2); ?></div>
                        <div class="acc-kpi-sub">Total Credit Vouchers</div>
                    </div>
                    <div class="acc-kpi-card success">
                        <div class="acc-kpi-label">Closing Balance</div>
                        <div class="acc-kpi-val font-weight-bold">৳ <?php echo number_format($ledger['closing_balance'], 2); ?></div>
                        <div class="acc-kpi-sub">Normal: <?php echo $ledger['is_debit_normal'] ? 'Debit (Asset/Exp)' : 'Credit (Liab/Eq/Rev)'; ?></div>
                    </div>
                </div>

                <!-- Ledger Table Card -->
                <div class="acc-card">
                    <div class="acc-card-header">
                        <h5 class="acc-card-title">
                            <i class="fas fa-file-invoice text-muted"></i>
                            General Ledger: [<?php echo $ledger['account']['account_code']; ?>] <?php echo htmlspecialchars($ledger['account']['account_name']); ?>
                        </h5>
                        <span class="badge badge-secondary"><?php echo date('d M Y', strtotime($date_from)); ?> &mdash; <?php echo date('d M Y', strtotime($date_to)); ?></span>
                    </div>
                    <div class="acc-card-body p-0">
                        <div class="table-responsive">
                            <table class="table acc-table mb-0 datatable-export">
                                <thead>
                                    <tr>
                                        <th style="width: 110px;">Date</th>
                                        <th style="width: 140px;">Journal #</th>
                                        <th style="width: 100px;">Type</th>
                                        <th>Description / Narration</th>
                                        <th style="width: 120px;">Reference</th>
                                        <th class="text-right" style="width: 130px;">Debit (Dr) ৳</th>
                                        <th class="text-right" style="width: 130px;">Credit (Cr) ৳</th>
                                        <th class="text-right" style="width: 150px;">Running Balance ৳</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Opening Balance Row -->
                                    <tr class="bg-light font-weight-bold">
                                        <td><?php echo date('d-m-Y', strtotime($date_from)); ?></td>
                                        <td colspan="4" class="text-muted"><i class="fas fa-history mr-1"></i> Opening Balance Brought Forward</td>
                                        <td class="text-right"><?php echo $ledger['is_debit_normal'] && $ledger['opening_balance'] > 0 ? number_format($ledger['opening_balance'], 2) : '-'; ?></td>
                                        <td class="text-right"><?php echo !$ledger['is_debit_normal'] && $ledger['opening_balance'] > 0 ? number_format($ledger['opening_balance'], 2) : '-'; ?></td>
                                        <td class="text-right acc-bal">৳ <?php echo number_format($ledger['opening_balance'], 2); ?></td>
                                    </tr>

                                    <?php if (!empty($ledger['lines'])): ?>
                                        <?php foreach ($ledger['lines'] as $line): ?>
                                            <tr>
                                                <td><?php echo date('d-m-Y', strtotime($line['journal_date'])); ?></td>
                                                <td>
                                                    <span class="font-weight-bold text-dark"><?php echo $line['journal_number']; ?></span>
                                                </td>
                                                <td><span class="badge badge-light border"><?php echo ucfirst($line['journal_type']); ?></span></td>
                                                <td>
                                                    <div><?php echo htmlspecialchars($line['description']); ?></div>
                                                    <?php if (!empty($line['header_desc']) && $line['header_desc'] !== $line['description']): ?>
                                                        <small class="text-muted"><?php echo htmlspecialchars($line['header_desc']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($line['journal_ref'] ?: '-'); ?></td>
                                                <td class="acc-amount acc-dr">
                                                    <?php echo $line['debit'] > 0 ? number_format($line['debit'], 2) : '-'; ?>
                                                </td>
                                                <td class="acc-amount acc-cr">
                                                    <?php echo $line['credit'] > 0 ? number_format($line['credit'], 2) : '-'; ?>
                                                </td>
                                                <td class="acc-amount acc-bal">
                                                    ৳ <?php echo number_format($line['running_balance'], 2); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No posted journal movements recorded for this account during the selected date period.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light font-weight-bold border-top">
                                        <td colspan="5" class="text-right text-uppercase">Period Activity Total:</td>
                                        <td class="acc-amount acc-dr text-right">৳ <?php echo number_format($ledger['total_period_dr'], 2); ?></td>
                                        <td class="acc-amount acc-cr text-right">৳ <?php echo number_format($ledger['total_period_cr'], 2); ?></td>
                                        <td class="acc-amount acc-bal text-right">৳ <?php echo number_format($ledger['closing_balance'], 2); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Please select an account from the Chart of Accounts to view its General Ledger.</div>
            <?php endif; ?>

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
    if ($.fn.DataTable.isDataTable('.datatable-export')) {
        $('.datatable-export').DataTable().destroy();
    }
    $('.datatable-export').DataTable({
        responsive: true,
        ordering: false, // Maintain running balance chronological sequence!
        pageLength: 50,
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
        buttons: [
            { extend: 'copy', className: 'btn btn-xs btn-outline-secondary' },
            { extend: 'excel', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_General_Ledger' },
            { extend: 'csv', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_General_Ledger' },
            { extend: 'pdf', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_General_Ledger' },
            { extend: 'print', className: 'btn btn-xs btn-outline-secondary' }
        ]
    });
});
</script>
