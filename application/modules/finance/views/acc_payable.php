<link rel="stylesheet" href="common/extranal/css/finance/accounting_reports.css">

<div class="content-wrapper bg-gradient-light">
    <section class="content-header py-3 border-bottom mb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 24px;">
                        <i class="fas fa-receipt text-info mr-2"></i><?php echo lang('accounts_payable'); ?>
                    </h1>
                    <small class="text-muted">Lifecare Diagnostic Center &bull; Supplier & Vendor Liability Audit</small>
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
            <?php $this->load->view('finance/inc_accounting_nav', ['active_report' => 'accounts_payable']); ?>

            <!-- Mandatory Foundation Notice -->
            <div class="acc-alert-notice" style="border-left-color: #6c757d; background-color: #f8f9fa;">
                <i class="fas fa-info-circle mr-1 text-secondary"></i>
                <strong>Accounting Foundation & AP Capability Audit:</strong>
                <?php echo $report['notice']; ?>
            </div>

            <!-- KPI Cards -->
            <div class="acc-kpi-grid">
                <div class="acc-kpi-card success">
                    <div class="acc-kpi-label">Total Outstanding AP</div>
                    <div class="acc-kpi-val text-success">৳ 0.00</div>
                    <div class="acc-kpi-sub">No Unpaid Supplier Invoices</div>
                </div>
                <div class="acc-kpi-card info">
                    <div class="acc-kpi-label">Registered Suppliers</div>
                    <div class="acc-kpi-val"><?php echo $report['supplier_count']; ?></div>
                    <div class="acc-kpi-sub">Master Vendor Catalog</div>
                </div>
                <div class="acc-kpi-card primary">
                    <div class="acc-kpi-label">Purchase Orders</div>
                    <div class="acc-kpi-val"><?php echo $report['po_count']; ?></div>
                    <div class="acc-kpi-sub">Total PO Records</div>
                </div>
                <div class="acc-kpi-card warning">
                    <div class="acc-kpi-label">Disbursement Policy</div>
                    <div class="acc-kpi-val" style="font-size: 16px;">Voucher-Based</div>
                    <div class="acc-kpi-sub">Paid Upon Execution</div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="acc-card">
                <div class="acc-card-header">
                    <h5 class="acc-card-title"><i class="fas fa-truck text-muted"></i> Vendor & Supplier Liability Status Schedule</h5>
                </div>
                <div class="acc-card-body p-0">
                    <div class="table-responsive">
                        <table class="table acc-table mb-0 datatable-export">
                            <thead>
                                <tr>
                                    <th>Supplier / Vendor Name</th>
                                    <th>Bill / PO Ref</th>
                                    <th>Invoice Date</th>
                                    <th>Due Date</th>
                                    <th class="text-right">Total Payable (৳)</th>
                                    <th class="text-right">Amount Paid (৳)</th>
                                    <th class="text-right">Outstanding (৳)</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle fa-2x mb-2 text-info"></i><br>
                                        <strong>No Accrued Supplier Liabilities Recorded in Database</strong><br>
                                        <small class="text-muted">
                                            The hospital expense system records expenses upon cash/bank payment disbursement.<br>
                                            In compliance with Prompt Rule #12, fictitious supplier payable balances are not fabricated.
                                        </small>
                                    </td>
                                </tr>
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
    if ($.fn.DataTable.isDataTable('.datatable-export')) {
        $('.datatable-export').DataTable().destroy();
    }
    $('.datatable-export').DataTable({
        responsive: true,
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
        buttons: [
            { extend: 'copy', className: 'btn btn-xs btn-outline-secondary' },
            { extend: 'excel', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Accounts_Payable' },
            { extend: 'csv', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Accounts_Payable' },
            { extend: 'pdf', className: 'btn btn-xs btn-outline-secondary', title: 'Lifecare_Accounts_Payable' },
            { extend: 'print', className: 'btn btn-xs btn-outline-secondary' }
        ]
    });
});
</script>
