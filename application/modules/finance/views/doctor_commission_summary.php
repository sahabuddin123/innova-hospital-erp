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
                    <h2><i class="fas fa-chart-line text-primary"></i> Doctor Commission Summary</h2>
                    <p>Real-time doctor commission performance, collection-backed eligibility, and payable balances &bull; <strong><?php echo $period['label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <button type="button" onclick="window.print()" class="rep-action-btn"><i class="fas fa-print"></i> Print Report</button>
                    <a href="finance/doctorCommissionStatement<?php echo !empty($selected_doctor) ? '?id=' . $selected_doctor : ''; ?>" class="rep-action-btn"><i class="fas fa-file-signature"></i> Doctor Statement</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/doctorsCommissionSummary?period_type=today<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/doctorsCommissionSummary?period_type=yesterday<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/doctorsCommissionSummary?period_type=this_week<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/doctorsCommissionSummary?period_type=this_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/doctorsCommissionSummary?period_type=last_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/doctorsCommissionSummary?period_type=this_year<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_year') ? 'active' : ''; ?>">This Year</a>
                <button type="button" class="rep-pill <?php echo ($period['type'] === 'custom') ? 'active' : ''; ?>" onclick="$('#filterCollapse').toggle();">Custom Filter <i class="fas fa-filter ml-1"></i></button>
            </div>

            <!-- Filter Controls -->
            <form action="finance/doctorsCommissionSummary" method="get" class="d-print-none <?php echo ($period['type'] === 'custom') ? '' : 'collapse'; ?>" id="filterCollapse" style="<?php echo ($period['type'] === 'custom') ? 'display:block;' : 'display:none;'; ?> margin-top: 12px;">
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

        <!-- 7 KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card">
                <div class="kpi-icon blue"><i class="fas fa-calculator"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Generated Commission</div>
                    <div class="kpi-val"><?php echo $currency; ?> <?php echo number_format($summary['total_generated_commission'], 2); ?></div>
                    <div class="kpi-sub">Total billed commission</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Eligible Commission</div>
                    <div class="kpi-val text-success"><?php echo $currency; ?> <?php echo number_format($summary['total_eligible_commission'], 2); ?></div>
                    <div class="kpi-sub">Collection backed</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon amber"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Commission Payable</div>
                    <div class="kpi-val text-warning"><?php echo $currency; ?> <?php echo number_format($summary['total_commission_payable'], 2); ?></div>
                    <div class="kpi-sub">Net outstanding liability</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon purple"><i class="fas fa-money-check-alt"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Commission Paid</div>
                    <div class="kpi-val text-primary"><?php echo $currency; ?> <?php echo number_format($summary['total_commission_paid'], 2); ?></div>
                    <div class="kpi-sub">Actual disbursements</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon teal"><i class="fas fa-clock"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Pending Collection</div>
                    <div class="kpi-val text-secondary"><?php echo $currency; ?> <?php echo number_format($summary['pending_commission'], 2); ?></div>
                    <div class="kpi-sub">Awaiting patient due</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon amber"><i class="fas fa-sliders-h"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Adjustments</div>
                    <div class="kpi-val"><?php echo $currency; ?> <?php echo number_format($summary['commission_adjustments'], 2); ?></div>
                    <div class="kpi-sub">Manual & test audits</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon red"><i class="fas fa-undo-alt"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Reversed / Cancelled</div>
                    <div class="kpi-val text-danger"><?php echo $currency; ?> <?php echo number_format($summary['commission_reversed'], 2); ?></div>
                    <div class="kpi-sub">Refunded investigations</div>
                </div>
            </div>
        </div>

        <!-- Doctor Performance Table -->
        <div class="rep-table-card">
            <div class="rep-table-title">
                <span><i class="fas fa-user-md text-primary mr-2"></i> Doctor-wise Commission Performance</span>
                <span class="badge badge-light border"><?php echo count($records); ?> Doctors</span>
            </div>

            <div class="table-responsive">
                <table class="table rep-table datatable-export" id="doctor-summary-table" width="100%">
                    <thead>
                        <tr>
                            <th>Doctor ID</th>
                            <th>Doctor Name</th>
                            <th>Department</th>
                            <th>Invoices</th>
                            <th>Tests</th>
                            <th>Gross Billed</th>
                            <th>Discount</th>
                            <th>Net Billed</th>
                            <th>Collected</th>
                            <th>Coll. %</th>
                            <th>Generated</th>
                            <th>Eligible</th>
                            <th>Paid</th>
                            <th>Payable Balance</th>
                            <th class="d-print-none text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><span class="font-weight-bold text-muted">#<?php echo $r['doctor_id']; ?></span></td>
                                <td>
                                    <strong class="text-dark"><?php echo $r['doctor_name']; ?></strong>
                                    <?php if (!empty($r['phone'])): ?>
                                        <div class="text-muted small"><i class="fas fa-phone-alt"></i> <?php echo $r['phone']; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-light border"><?php echo $r['department_name']; ?></span></td>
                                <td><span class="badge badge-pill badge-info"><?php echo $r['total_invoices']; ?></span></td>
                                <td><?php echo $r['total_items']; ?></td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['gross_billed'], 2); ?></td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['total_discount'], 2); ?></td>
                                <td><strong><?php echo $currency; ?> <?php echo number_format($r['net_payable'], 2); ?></strong></td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['total_collected'], 2); ?></td>
                                <td>
                                    <span class="badge badge-status <?php echo ($r['collection_rate'] >= 80) ? 'success' : (($r['collection_rate'] >= 50) ? 'warning' : 'danger'); ?>">
                                        <?php echo $r['collection_rate']; ?>%
                                    </span>
                                </td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['generated_commission'], 2); ?></td>
                                <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($r['eligible_commission'], 2); ?></td>
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
                                <td class="d-print-none text-right">
                                    <a href="finance/doctorCommissionStatement?id=<?php echo $r['doctor_id']; ?>" class="btn btn-xs btn-outline-primary" title="View Statement">
                                        <i class="fas fa-file-invoice"></i> Statement
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right">Total Aggregate:</td>
                            <td><?php echo $currency; ?> <?php echo number_format($summary['total_billed_amount'], 2); ?></td>
                            <td></td>
                            <td></td>
                            <td><?php echo $currency; ?> <?php echo number_format($summary['total_collected_amount'], 2); ?></td>
                            <td></td>
                            <td><?php echo $currency; ?> <?php echo number_format($summary['total_generated_commission'], 2); ?></td>
                            <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($summary['total_eligible_commission'], 2); ?></td>
                            <td class="text-primary"><?php echo $currency; ?> <?php echo number_format($summary['total_commission_paid'], 2); ?></td>
                            <td class="text-warning font-weight-bold"><?php echo $currency; ?> <?php echo number_format($summary['total_commission_payable'], 2); ?></td>
                            <td class="d-print-none"></td>
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
    if ($.fn.DataTable.isDataTable('#doctor-summary-table')) {
        $('#doctor-summary-table').DataTable().destroy();
    }
    $('#doctor-summary-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Doctor Commission Summary - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Doctor Commission Summary - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Doctor Commission Summary - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Doctor Commission Summary - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[13, "desc"]], // Highest payable first
        pageLength: 25
    });
});
</script>
