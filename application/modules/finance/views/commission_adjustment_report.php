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
                    <h2><i class="fas fa-sliders-h text-primary"></i> Commission Adjustment & Reversal Report</h2>
                    <p>Audit trail of test cancellations, refunds, manual corrections, and commission adjustments &bull; <strong><?php echo $period['label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'commission_adjustment']); ?>
                    <a href="finance/doctorsCommissionSummary" class="rep-action-btn"><i class="fas fa-chart-line"></i> Summary View</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/commissionAdjustmentReport?period_type=today<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/commissionAdjustmentReport?period_type=yesterday<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/commissionAdjustmentReport?period_type=this_week<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/commissionAdjustmentReport?period_type=this_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/commissionAdjustmentReport?period_type=last_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/commissionAdjustmentReport?period_type=this_year<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_year') ? 'active' : ''; ?>">This Year</a>
                <button type="button" class="rep-pill <?php echo ($period['type'] === 'custom') ? 'active' : ''; ?>" onclick="$('#filterCollapse').toggle();">Custom Filter <i class="fas fa-filter ml-1"></i></button>
            </div>

            <!-- Filter Controls -->
            <form action="finance/commissionAdjustmentReport" method="get" class="d-print-none <?php echo ($period['type'] === 'custom') ? '' : 'collapse'; ?>" id="filterCollapse" style="<?php echo ($period['type'] === 'custom') ? 'display:block;' : 'display:none;'; ?> margin-top: 12px;">
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
                        <button type="submit" class="rep-btn rep-btn-primary w-100"><i class="fas fa-search"></i> Apply Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card">
                <div class="kpi-icon amber"><i class="fas fa-sliders-h"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Adjustment Count</div>
                    <div class="kpi-val"><?php echo $summary['total_adjustments_count']; ?></div>
                    <div class="kpi-sub">Total audit events</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon red"><i class="fas fa-undo"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Adjusted / Reversed</div>
                    <div class="kpi-val text-danger"><?php echo $currency; ?> <?php echo number_format($summary['total_adjusted_amount'], 2); ?></div>
                    <div class="kpi-sub">Commission corrected/reversed</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="rep-table-card">
            <div class="rep-table-title">
                <span><i class="fas fa-history text-primary mr-2"></i> Audit Records of Adjustments and Reversals</span>
            </div>

            <div class="table-responsive">
                <table class="table rep-table datatable-export" id="adjustment-table" width="100%">
                    <thead>
                        <tr>
                            <th>Original Ref</th>
                            <th>Invoice #</th>
                            <th>Adjustment Date</th>
                            <th>Doctor Name</th>
                            <th>Adjustment Type</th>
                            <th>Adjusted Amount</th>
                            <th>Reason / Remarks</th>
                            <th>Processed By</th>
                            <th>Authorized By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): ?>
                            <?php foreach ($records as $r): ?>
                                <tr>
                                    <td><code><?php echo $r['original_ref']; ?></code></td>
                                    <td><a href="finance/invoice?id=<?php echo $r['invoice_id']; ?>" class="font-weight-bold text-primary">#<?php echo $r['invoice_id']; ?></a></td>
                                    <td><?php echo date('d/m/Y', $r['adjustment_date']); ?></td>
                                    <td><strong><?php echo $r['doctor_name']; ?></strong></td>
                                    <td>
                                        <span class="badge badge-status <?php echo ($r['adjustment_type'] === 'Invoice Cancellation') ? 'danger' : 'warning'; ?>">
                                            <?php echo $r['adjustment_type']; ?>
                                        </span>
                                    </td>
                                    <td class="text-danger font-weight-bold"><?php echo $currency; ?> <?php echo number_format($r['adjusted_amount'], 2); ?></td>
                                    <td><?php echo $r['reason']; ?></td>
                                    <td><?php echo $r['processed_by']; ?></td>
                                    <td><?php echo $r['authorized_by']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                    <p class="mb-0">No commission adjustments or reversals recorded for this period. Audit integrity 100% clean.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right">Total Adjusted Amount:</td>
                            <td class="text-danger font-weight-bold"><?php echo $currency; ?> <?php echo number_format($summary['total_adjusted_amount'], 2); ?></td>
                            <td colspan="3"></td>
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
    if ($.fn.DataTable.isDataTable('#adjustment-table')) {
        $('#adjustment-table').DataTable().destroy();
    }
    $('#adjustment-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Commission Adjustment Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Commission Adjustment Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Commission Adjustment Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Commission Adjustment Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[2, "desc"]],
        pageLength: 25
    });
});
</script>

