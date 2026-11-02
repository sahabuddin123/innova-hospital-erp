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
                    <h2><i class="fas fa-hand-holding-usd text-primary"></i> Commission Payable Report</h2>
                    <p>Current outstanding doctor commission liabilities and obligations backed by verified collections &bull; <strong><?php echo $period['label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'commission_payable']); ?>
                    <a href="finance/commissionPaidReport" class="rep-action-btn"><i class="fas fa-money-check-alt"></i> Commission Paid</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/commissionPayableReport?period_type=today<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/commissionPayableReport?period_type=yesterday<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/commissionPayableReport?period_type=this_week<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/commissionPayableReport?period_type=this_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/commissionPayableReport?period_type=last_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/commissionPayableReport?period_type=this_year<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_year') ? 'active' : ''; ?>">This Year</a>
                <button type="button" class="rep-pill <?php echo ($period['type'] === 'custom') ? 'active' : ''; ?>" onclick="$('#filterCollapse').toggle();">Custom Filter <i class="fas fa-filter ml-1"></i></button>
            </div>

            <!-- Filter Controls -->
            <form action="finance/commissionPayableReport" method="get" class="d-print-none <?php echo ($period['type'] === 'custom') ? '' : 'collapse'; ?>" id="filterCollapse" style="<?php echo ($period['type'] === 'custom') ? 'display:block;' : 'display:none;'; ?> margin-top: 12px;">
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
                        <label>Obligation Status</label>
                        <select name="status" class="rep-form-control">
                            <option value="all">All Statuses</option>
                            <option value="Pending" <?php echo (!empty($selected_status) && $selected_status === 'Pending') ? 'selected' : ''; ?>>Pending (Eligible & Unpaid)</option>
                            <option value="Partially Paid" <?php echo (!empty($selected_status) && $selected_status === 'Partially Paid') ? 'selected' : ''; ?>>Partially Paid</option>
                            <option value="Fully Paid" <?php echo (!empty($selected_status) && $selected_status === 'Fully Paid') ? 'selected' : ''; ?>>Fully Paid</option>
                            <option value="Pending Collection" <?php echo (!empty($selected_status) && $selected_status === 'Pending Collection') ? 'selected' : ''; ?>>Pending Collection (Unpaid Bill)</option>
                        </select>
                    </div>
                    <div class="rep-form-group">
                        <button type="submit" class="rep-btn rep-btn-primary w-100"><i class="fas fa-search"></i> Apply Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card">
                <div class="kpi-icon red"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Outstanding Payable</div>
                    <div class="kpi-val text-danger"><?php echo $currency; ?> <?php echo number_format($summary['total_outstanding_payable'], 2); ?></div>
                    <div class="kpi-sub">Total payable liability</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Total Eligible</div>
                    <div class="kpi-val text-success"><?php echo $currency; ?> <?php echo number_format($summary['total_eligible'], 2); ?></div>
                    <div class="kpi-sub">Collection-backed amount</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon blue"><i class="fas fa-calculator"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Total Generated</div>
                    <div class="kpi-val"><?php echo $currency; ?> <?php echo number_format($summary['total_generated'], 2); ?></div>
                    <div class="kpi-sub">Total commission booked</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon purple"><i class="fas fa-receipt"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Payable Invoices</div>
                    <div class="kpi-val"><?php echo $summary['total_payable_invoices']; ?></div>
                    <div class="kpi-sub">Qualifying transactions</div>
                </div>
            </div>
        </div>

        <!-- Payable Table -->
        <div class="rep-table-card">
            <div class="rep-table-title">
                <span><i class="fas fa-list text-primary mr-2"></i> Commission Payable Ledger & Status</span>
            </div>

            <div class="table-responsive">
                <table class="table rep-table datatable-export" id="payable-table" width="100%">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Doctor Name</th>
                            <th>Department</th>
                            <th>Patient Name</th>
                            <th>Net Bill</th>
                            <th>Collected</th>
                            <th>Generated</th>
                            <th>Eligible</th>
                            <th>Paid</th>
                            <th>Remaining Payable</th>
                            <th>Status</th>
                            <th class="d-print-none text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><a href="finance/invoice?id=<?php echo $r['invoice_id']; ?>" class="font-weight-bold text-primary">#<?php echo $r['invoice_id']; ?></a></td>
                                <td><?php echo date('d/m/Y', $r['invoice_date']); ?></td>
                                <td><strong><?php echo $r['doctor_name']; ?></strong></td>
                                <td><span class="badge badge-light border"><?php echo $r['department_name']; ?></span></td>
                                <td><?php echo $r['patient_name']; ?></td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['net_bill'], 2); ?></td>
                                <td class="text-success"><?php echo $currency; ?> <?php echo number_format($r['amount_collected'], 2); ?></td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['commission_generated'], 2); ?></td>
                                <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($r['commission_eligible'], 2); ?></td>
                                <td class="text-primary"><?php echo $currency; ?> <?php echo number_format($r['commission_paid'], 2); ?></td>
                                <td>
                                    <?php if ($r['remaining_payable'] > 0): ?>
                                        <span class="badge badge-status warning font-weight-bold" style="font-size: 12px;">
                                            <?php echo $currency; ?> <?php echo number_format($r['remaining_payable'], 2); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-status secondary">৳ 0.00</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($r['commission_status'] === 'Pending'): ?>
                                        <span class="badge badge-status warning">Pending</span>
                                    <?php elseif ($r['commission_status'] === 'Partially Paid'): ?>
                                        <span class="badge badge-status info">Partial</span>
                                    <?php elseif ($r['commission_status'] === 'Fully Paid'): ?>
                                        <span class="badge badge-status success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge badge-status secondary">Pending Coll.</span>
                                    <?php endif; ?>
                                </td>
                                <td class="d-print-none text-right">
                                    <a href="finance/doctorCommissionStatement?id=<?php echo $r['doctor_id']; ?>" class="btn btn-xs btn-outline-primary" title="Doctor Statement">
                                        <i class="fas fa-file-invoice"></i> Statement
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-right">Total Aggregate:</td>
                            <td><?php echo $currency; ?> <?php echo number_format($summary['total_generated'], 2); ?></td>
                            <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($summary['total_eligible'], 2); ?></td>
                            <td></td>
                            <td class="text-warning font-weight-bold"><?php echo $currency; ?> <?php echo number_format($summary['total_outstanding_payable'], 2); ?></td>
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
    if ($.fn.DataTable.isDataTable('#payable-table')) {
        $('#payable-table').DataTable().destroy();
    }
    $('#payable-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Commission Payable Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Commission Payable Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Commission Payable Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Commission Payable Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[10, "desc"]], // Highest payable first
        pageLength: 25
    });
});
</script>

