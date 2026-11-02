<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳';
?>
<link rel="stylesheet" href="common/extranal/css/finance/doctor_commission.css">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <?php $this->load->view('finance/inc_doctor_commission_nav'); ?>

        <!-- Header Card -->
        <div class="rep-header-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 16px;">
                <div class="rep-title">
                    <h2><i class="fas fa-file-invoice text-primary"></i> Invoice-Wise Commission Report</h2>
                    <p>Invoice-level doctor commission breakdown, payment status, and net payable calculations &bull; <strong><?php echo $period['label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'invoice_wise_commission']); ?>
                    <a href="finance/commissionPayableReport" class="rep-action-btn"><i class="fas fa-hand-holding-usd"></i> Commission Payable</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/invoiceWiseCommissionReport?period_type=today<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/invoiceWiseCommissionReport?period_type=yesterday<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/invoiceWiseCommissionReport?period_type=this_week<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/invoiceWiseCommissionReport?period_type=this_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/invoiceWiseCommissionReport?period_type=last_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/invoiceWiseCommissionReport?period_type=this_year<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_year') ? 'active' : ''; ?>">This Year</a>
                <button type="button" class="rep-pill <?php echo ($period['type'] === 'custom') ? 'active' : ''; ?>" onclick="$('#filterCollapse').toggle();">Custom Filter <i class="fas fa-filter ml-1"></i></button>
            </div>

            <!-- Filter Controls -->
            <form action="finance/invoiceWiseCommissionReport" method="get" class="d-print-none <?php echo ($period['type'] === 'custom') ? '' : 'collapse'; ?>" id="filterCollapse" style="<?php echo ($period['type'] === 'custom') ? 'display:block;' : 'display:none;'; ?> margin-top: 12px;">
                <input type="hidden" name="period_type" value="custom">
                <div class="rep-filter-grid">
                    <div class="rep-form-group">
                        <label>Date From</label>
                        <input type="date" name="date_from" class="rep-form-control" value="<?php echo !empty($period['date_from']) ? $period['date_from'] : date('Y-m-01'); ?>">
                    </div>
                    <div class="rep-form-group">
                        <label>Date To</label>
                        <input type="date" name="date_to" class="rep-form-control" value="<?php echo !empty($period['date_to']) ? $period['date_to'] : date('Y-m-d'); ?>">
                    </div>
                    <div class="rep-form-group">
                        <label>Doctor</label>
                        <select name="doctor_id" class="rep-form-control select2">
                            <option value="">All Doctors</option>
                            <?php foreach ($doctors as $d): ?>
                                <option value="<?php echo $d->id; ?>" <?php echo (!empty($selected_doctor) && $selected_doctor == $d->id) ? 'selected' : ''; ?>>
                                    <?php echo $d->name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="rep-form-group">
                        <label>Invoice Status</label>
                        <select name="status" class="rep-form-control">
                            <option value="all">All Statuses</option>
                            <option value="Paid" <?php echo (!empty($selected_status) && $selected_status === 'Paid') ? 'selected' : ''; ?>>Paid</option>
                            <option value="Partial" <?php echo (!empty($selected_status) && $selected_status === 'Partial') ? 'selected' : ''; ?>>Partial</option>
                            <option value="Unpaid" <?php echo (!empty($selected_status) && $selected_status === 'Unpaid') ? 'selected' : ''; ?>>Unpaid</option>
                        </select>
                    </div>
                    <div class="rep-form-group">
                        <button type="submit" class="rep-btn rep-btn-primary w-100"><i class="fas fa-search"></i> Apply Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary KPI Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card">
                <div class="kpi-icon blue"><i class="fas fa-file-invoice"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Invoices Count</div>
                    <div class="kpi-val"><?php echo $summary['total_invoices']; ?></div>
                    <div class="kpi-sub">Total referred bills</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon teal"><i class="fas fa-money-bill-alt"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Net Payable</div>
                    <div class="kpi-val"><?php echo $currency; ?> <?php echo number_format($summary['total_net_payable'], 2); ?></div>
                    <div class="kpi-sub">Billed after discounts</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon green"><i class="fas fa-wallet"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Patient Collection</div>
                    <div class="kpi-val text-success"><?php echo $currency; ?> <?php echo number_format($summary['total_collected'], 2); ?></div>
                    <div class="kpi-sub">Total payments received</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon purple"><i class="fas fa-calculator"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Generated Commission</div>
                    <div class="kpi-val"><?php echo $currency; ?> <?php echo number_format($summary['total_generated_commission'], 2); ?></div>
                    <div class="kpi-sub">Calculated commission</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon amber"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Eligible Commission</div>
                    <div class="kpi-val text-warning"><?php echo $currency; ?> <?php echo number_format($summary['total_eligible_commission'], 2); ?></div>
                    <div class="kpi-sub">Collection backed</div>
                </div>
            </div>
        </div>

        <!-- Invoice Table -->
        <div class="rep-table-card">
            <div class="rep-table-title">
                <span><i class="fas fa-receipt text-primary mr-2"></i> Invoices and Doctor Commission Calculation</span>
            </div>

            <div class="table-responsive">
                <table class="table rep-table datatable-export" id="invoice-wise-table" width="100%">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Department</th>
                            <th>Investigation Tests</th>
                            <th>Gross</th>
                            <th>Discount</th>
                            <th>Net Payable</th>
                            <th>Collected</th>
                            <th>Due</th>
                            <th>Generated</th>
                            <th>Eligible</th>
                            <th>Status</th>
                            <th class="d-print-none text-right">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><a href="finance/invoice?id=<?php echo $r['invoice_id']; ?>" class="font-weight-bold text-primary">#<?php echo $r['invoice_id']; ?></a></td>
                                <td><?php echo date('d/m/Y', $r['invoice_date']); ?></td>
                                <td>
                                    <strong class="text-dark"><?php echo $r['patient_name']; ?></strong>
                                    <?php if (!empty($r['patient_phone'])): ?>
                                        <div class="text-muted small"><?php echo $r['patient_phone']; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo $r['doctor_name']; ?></strong></td>
                                <td><span class="badge badge-light border"><?php echo $r['department_name']; ?></span></td>
                                <td>
                                    <span class="badge badge-pill badge-secondary" title="<?php echo htmlspecialchars(implode(', ', $r['items_list'])); ?>">
                                        <?php echo $r['items_count']; ?> tests
                                    </span>
                                    <div class="small text-muted" style="max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo $r['items_summary']; ?>
                                    </div>
                                </td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['gross_amount'], 2); ?></td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['discount'], 2); ?></td>
                                <td><strong><?php echo $currency; ?> <?php echo number_format($r['net_payable'], 2); ?></strong></td>
                                <td class="text-success"><?php echo $currency; ?> <?php echo number_format($r['amount_collected'], 2); ?></td>
                                <td class="text-danger"><?php echo $currency; ?> <?php echo number_format($r['due_amount'], 2); ?></td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['generated_commission'], 2); ?></td>
                                <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($r['eligible_commission'], 2); ?></td>
                                <td>
                                    <?php if ($r['payment_status'] === 'Paid'): ?>
                                        <span class="badge badge-status success">Paid</span>
                                    <?php elseif ($r['payment_status'] === 'Partial'): ?>
                                        <span class="badge badge-status warning">Partial</span>
                                    <?php else: ?>
                                        <span class="badge badge-status danger">Unpaid</span>
                                    <?php endif; ?>
                                </td>
                                <td class="d-print-none text-right">
                                    <a href="finance/invoice?id=<?php echo $r['invoice_id']; ?>" class="btn btn-xs btn-outline-info" title="Full Invoice">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-right">Total Aggregate:</td>
                            <td><?php echo $currency; ?> <?php echo number_format($summary['total_gross'], 2); ?></td>
                            <td><?php echo $currency; ?> <?php echo number_format($summary['total_discount'], 2); ?></td>
                            <td><strong><?php echo $currency; ?> <?php echo number_format($summary['total_net_payable'], 2); ?></strong></td>
                            <td class="text-success"><?php echo $currency; ?> <?php echo number_format($summary['total_collected'], 2); ?></td>
                            <td></td>
                            <td><?php echo $currency; ?> <?php echo number_format($summary['total_generated_commission'], 2); ?></td>
                            <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($summary['total_eligible_commission'], 2); ?></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Print Signatures -->
            <div class="rep-print-signatures">
                <div class="signature-box">Prepared By (Accounts Officer)</div>
                <div class="signature-box">Checked By (Audit Head)</div>
                <div class="signature-box">Approved By (Medical Director)</div>
            </div>
        </div>

    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#invoice-wise-table')) {
        $('#invoice-wise-table').DataTable().destroy();
    }
    $('#invoice-wise-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Invoice-Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Invoice-Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Invoice-Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Invoice-Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[0, "desc"]],
        pageLength: 25
    });
});
</script>

