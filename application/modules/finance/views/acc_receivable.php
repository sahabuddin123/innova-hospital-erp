<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-hand-holding-usd text-info mr-2"></i><?php echo lang('accounts_receivable'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Patient Invoice Outstanding Aging Ledger</small>
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
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'accounts_receivable']); ?>

            <!-- Aging KPI Cards -->
            <div class="acc-kpi-grid">
                <div class="acc-kpi-card danger">
                    <div class="acc-kpi-label">Total Outstanding Due</div>
                    <div class="acc-kpi-val text-danger">৳ <?php echo number_format($report['total_outstanding'], 2); ?></div>
                    <div class="acc-kpi-sub"><?php echo $report['open_receivable_count']; ?> Unsettled Invoices</div>
                </div>
                <div class="acc-kpi-card success">
                    <div class="acc-kpi-label">Current (0 - 15 Days)</div>
                    <div class="acc-kpi-val text-success">৳ <?php echo number_format($report['aging']['0_15'], 2); ?></div>
                    <div class="acc-kpi-sub">Within Normal Terms</div>
                </div>
                <div class="acc-kpi-card info">
                    <div class="acc-kpi-label">16 - 30 Days Due</div>
                    <div class="acc-kpi-val text-info">৳ <?php echo number_format($report['aging']['16_30'], 2); ?></div>
                    <div class="acc-kpi-sub">Follow-up Required</div>
                </div>
                <div class="acc-kpi-card warning">
                    <div class="acc-kpi-label">31 - 60 Days Due</div>
                    <div class="acc-kpi-val text-warning">৳ <?php echo number_format($report['aging']['31_60'], 2); ?></div>
                    <div class="acc-kpi-sub">Overdue Notice Needed</div>
                </div>
                <div class="acc-kpi-card danger">
                    <div class="acc-kpi-label">61 - 90 Days Due</div>
                    <div class="acc-kpi-val" style="color: #fd7e14;">৳ <?php echo number_format($report['aging']['61_90'], 2); ?></div>
                    <div class="acc-kpi-sub">High Risk Collection</div>
                </div>
                <div class="acc-kpi-card dark">
                    <div class="acc-kpi-label">Over 90 Days Due</div>
                    <div class="acc-kpi-val text-danger font-weight-bold">৳ <?php echo number_format($report['aging']['90_plus'], 2); ?></div>
                    <div class="acc-kpi-sub">Doubtful Debt Review</div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="acc-card">
                <div class="acc-card-header">
                    <h5 class="acc-card-title"><i class="fas fa-list text-muted"></i> Patient Outstanding Receivables & Aging Schedule</h5>
                    <div>
                        <span class="badge badge-success p-2 mr-2">Overall Collection Rate: <?php echo $report['collection_rate']; ?>%</span>
                        <span class="badge badge-danger p-2"><?php echo $report['open_receivable_count']; ?> Open Due Invoices</span>
                    </div>
                </div>
                <div class="acc-card-body p-0">
                    <div class="table-responsive">
                        <table class="table acc-table mb-0 datatable-export">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Invoice #</th>
                                    <th style="width: 100px;">Date</th>
                                    <th>Patient Details</th>
                                    <th class="text-right">Gross (৳)</th>
                                    <th class="text-right">Discount (৳)</th>
                                    <th class="text-right">Net Payable (৳)</th>
                                    <th class="text-right">Collected (৳)</th>
                                    <th class="text-right">Outstanding (৳)</th>
                                    <th class="text-center" style="width: 130px;">Aging Category</th>
                                    <th class="text-center" style="width: 70px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($report['receivables'])): ?>
                                    <?php foreach ($report['receivables'] as $inv): ?>
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold text-dark">#<?php echo $inv['invoice_id']; ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                    $dt = is_numeric($inv['invoice_date']) ? intval($inv['invoice_date']) : strtotime($inv['invoice_date']);
                                                    echo date('d-m-Y', $dt);
                                                ?>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold"><?php echo htmlspecialchars($inv['patient_name'] ?: 'Patient #' . $inv['patient']); ?></div>
                                                <small class="text-muted">ID: <?php echo $inv['patient']; ?> &bull; Phone: <?php echo htmlspecialchars($inv['patient_phone'] ?: '-'); ?></small>
                                            </td>
                                            <td class="acc-amount"><?php echo number_format($inv['gross_total'], 2); ?></td>
                                            <td class="acc-amount text-muted"><?php echo number_format($inv['total_discount'], 2); ?></td>
                                            <td class="acc-amount font-weight-bold">৳ <?php echo number_format($inv['net_amount'], 2); ?></td>
                                            <td class="acc-amount text-success">৳ <?php echo number_format($inv['total_collected'], 2); ?></td>
                                            <td class="acc-amount text-danger font-weight-bold" style="font-size: 14px;">
                                                ৳ <?php echo number_format($inv['due_amount'], 2); ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?php echo $inv['badge_class']; ?> p-1">
                                                    <?php echo $inv['bucket_label']; ?> (<?php echo $inv['age_days']; ?>d)
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="finance/invoice?id=<?php echo $inv['invoice_id']; ?>" class="btn btn-xs btn-outline-info" title="View Patient Invoice" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-success py-4">
                                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                                            <strong>Zero Outstanding Receivables!</strong> All patient invoices have been fully collected.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold border-top" style="font-size: 14px;">
                                    <td colspan="3" class="text-right text-uppercase">Total Receivables Schedule:</td>
                                    <td class="acc-amount text-right">৳ <?php echo number_format($report['total_gross'], 2); ?></td>
                                    <td class="acc-amount text-right">৳ <?php echo number_format($report['total_discount'], 2); ?></td>
                                    <td class="acc-amount text-right">৳ <?php echo number_format($report['total_net'], 2); ?></td>
                                    <td class="acc-amount text-right text-success">৳ <?php echo number_format($report['total_collected'], 2); ?></td>
                                    <td class="acc-amount text-right text-danger font-weight-bold">৳ <?php echo number_format($report['total_outstanding'], 2); ?></td>
                                    <td colspan="2"></td>
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

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('.datatable-export')) {
        $('.datatable-export').DataTable().destroy();
    }
    $('.datatable-export').DataTable({
        responsive: true,
        pageLength: 25,
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
        buttons: [
            { extend: 'copy', className: 'btn btn-xs btn-outline-secondary' },
            { extend: 'excel', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Accounts_Receivable' },
            { extend: 'csv', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Accounts_Receivable' },
            { extend: 'pdf', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Accounts_Receivable' },
            { extend: 'print', className: 'btn btn-xs btn-outline-secondary' }
        ]
    });
});
</script>
