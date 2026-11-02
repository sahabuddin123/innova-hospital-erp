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
                    <h2><i class="fas fa-money-check-alt text-primary"></i> Commission Paid Report</h2>
                    <p>Actual recorded commission disbursements, vouchers, and payment method audit trail &bull; <strong><?php echo $period['label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'commission_paid']); ?>
                    <a href="finance/commissionPayableReport" class="rep-action-btn"><i class="fas fa-hand-holding-usd"></i> Commission Payable</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/commissionPaidReport?period_type=today<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/commissionPaidReport?period_type=yesterday<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/commissionPaidReport?period_type=this_week<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/commissionPaidReport?period_type=this_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/commissionPaidReport?period_type=last_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/commissionPaidReport?period_type=this_year<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_year') ? 'active' : ''; ?>">This Year</a>
                <button type="button" class="rep-pill <?php echo ($period['type'] === 'custom') ? 'active' : ''; ?>" onclick="$('#filterCollapse').toggle();">Custom Filter <i class="fas fa-filter ml-1"></i></button>
            </div>

            <!-- Filter Controls -->
            <form action="finance/commissionPaidReport" method="get" class="d-print-none <?php echo ($period['type'] === 'custom') ? '' : 'collapse'; ?>" id="filterCollapse" style="<?php echo ($period['type'] === 'custom') ? 'display:block;' : 'display:none;'; ?> margin-top: 12px;">
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
                        <label>Payment Method</label>
                        <select name="payment_method" class="rep-form-control">
                            <option value="all">All Methods</option>
                            <option value="Cash" <?php echo (!empty($selected_method) && $selected_method === 'Cash') ? 'selected' : ''; ?>>Cash</option>
                            <option value="Bank Transfer" <?php echo (!empty($selected_method) && $selected_method === 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                            <option value="bKash" <?php echo (!empty($selected_method) && $selected_method === 'bKash') ? 'selected' : ''; ?>>bKash</option>
                            <option value="Nagad" <?php echo (!empty($selected_method) && $selected_method === 'Nagad') ? 'selected' : ''; ?>>Nagad</option>
                            <option value="Cheque" <?php echo (!empty($selected_method) && $selected_method === 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
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
                <div class="kpi-icon green"><i class="fas fa-money-bill-wave"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Total Disbursed</div>
                    <div class="kpi-val text-success"><?php echo $currency; ?> <?php echo number_format($summary['total_paid'], 2); ?></div>
                    <div class="kpi-sub">Actual paid vouchers</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon blue"><i class="fas fa-file-invoice"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Disbursement Vouchers</div>
                    <div class="kpi-val"><?php echo $summary['number_of_transactions']; ?></div>
                    <div class="kpi-sub">Total payout records</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon purple"><i class="fas fa-user-md"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Beneficiary Doctors</div>
                    <div class="kpi-val"><?php echo count($summary['doctor_wise_summary']); ?></div>
                    <div class="kpi-sub">Doctors receiving payout</div>
                </div>
            </div>
        </div>

        <!-- Paid Transactions Table -->
        <div class="rep-table-card">
            <div class="rep-table-title">
                <span><i class="fas fa-history text-primary mr-2"></i> Commission Disbursement Transactions</span>
            </div>

            <div class="table-responsive">
                <table class="table rep-table datatable-export" id="paid-table" width="100%">
                    <thead>
                        <tr>
                            <th>Voucher #</th>
                            <th>Payment Date</th>
                            <th>Doctor / Payee</th>
                            <th>Category</th>
                            <th>Amount Disbursed</th>
                            <th>Payment Method</th>
                            <th>Transaction Ref</th>
                            <th>Remarks / Note</th>
                            <th>Disbursed By</th>
                            <th>Authorized By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): ?>
                            <?php foreach ($records as $r): ?>
                                <tr>
                                    <td><span class="font-weight-bold text-muted">#<?php echo $r['voucher_id']; ?></span></td>
                                    <td><?php echo date('d/m/Y', $r['payment_date']); ?></td>
                                    <td><strong class="text-dark"><?php echo $r['doctor_name']; ?></strong></td>
                                    <td><span class="badge badge-light border"><?php echo $r['category_name']; ?></span></td>
                                    <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($r['amount'], 2); ?></td>
                                    <td><span class="badge badge-status info"><?php echo $r['payment_method']; ?></span></td>
                                    <td><code><?php echo $r['transaction_ref']; ?></code></td>
                                    <td><?php echo $r['note']; ?></td>
                                    <td><?php echo $r['paid_by']; ?></td>
                                    <td><?php echo $r['approved_by']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle fa-2x mb-2 text-muted"></i>
                                    <p class="mb-0">No commission disbursement vouchers recorded for this period. Historical transactions are strictly preserved without fabrication.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right">Total Disbursed:</td>
                            <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($summary['total_paid'], 2); ?></td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Print Signatures -->
            <div class="rep-print-signatures">
                <div class="signature-box">Disbursed By (Cashier / Accounts)</div>
                <div class="signature-box">Audited By (Internal Audit)</div>
                <div class="signature-box">Doctor's Acknowledgment</div>
            </div>
        </div>

    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#paid-table')) {
        $('#paid-table').DataTable().destroy();
    }
    $('#paid-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Commission Paid Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Commission Paid Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Commission Paid Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Commission Paid Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[1, "desc"]],
        pageLength: 25
    });
});
</script>

