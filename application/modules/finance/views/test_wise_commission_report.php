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
                    <h2><i class="fas fa-vial text-primary"></i> Test-Wise Commission Report</h2>
                    <p>Investigation test line items, configured test-specific rates, and collection-backed eligibility &bull; <strong><?php echo $period['label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <button type="button" onclick="window.print()" class="rep-action-btn"><i class="fas fa-print"></i> Print Report</button>
                    <a href="finance/commissionConfigurationReport" class="rep-action-btn"><i class="fas fa-cogs"></i> Rate Config</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/testWiseCommissionReport?period_type=today<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/testWiseCommissionReport?period_type=yesterday<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/testWiseCommissionReport?period_type=this_week<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/testWiseCommissionReport?period_type=this_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/testWiseCommissionReport?period_type=last_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/testWiseCommissionReport?period_type=this_year<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_year') ? 'active' : ''; ?>">This Year</a>
                <button type="button" class="rep-pill <?php echo ($period['type'] === 'custom') ? 'active' : ''; ?>" onclick="$('#filterCollapse').toggle();">Custom Filter <i class="fas fa-filter ml-1"></i></button>
            </div>

            <!-- Filter Controls -->
            <form action="finance/testWiseCommissionReport" method="get" class="d-print-none <?php echo ($period['type'] === 'custom') ? '' : 'collapse'; ?>" id="filterCollapse" style="<?php echo ($period['type'] === 'custom') ? 'display:block;' : 'display:none;'; ?> margin-top: 12px;">
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
                        <label>Test Category / Dept</label>
                        <select name="category_id" class="rep-form-control select2">
                            <option value="">All Tests / Services</option>
                            <?php foreach ($test_categories as $tc): ?>
                                <option value="<?php echo $tc->id; ?>" <?php echo (!empty($selected_category) && $selected_category == $tc->id) ? 'selected' : ''; ?>>
                                    <?php echo $tc->category; ?> (<?php echo !empty($tc->d_commission) ? $tc->d_commission . '%' : '0%'; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="rep-form-group">
                        <label>Commission Status</label>
                        <select name="status" class="rep-form-control">
                            <option value="all">All Statuses</option>
                            <option value="Fully Eligible" <?php echo (!empty($selected_status) && $selected_status === 'Fully Eligible') ? 'selected' : ''; ?>>Fully Eligible (100% Paid)</option>
                            <option value="Partially Eligible" <?php echo (!empty($selected_status) && $selected_status === 'Partially Eligible') ? 'selected' : ''; ?>>Partially Eligible</option>
                            <option value="Pending Collection" <?php echo (!empty($selected_status) && $selected_status === 'Pending Collection') ? 'selected' : ''; ?>>Pending Collection</option>
                        </select>
                    </div>
                    <div class="rep-form-group">
                        <button type="submit" class="rep-btn rep-btn-primary w-100"><i class="fas fa-search"></i> Apply Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card">
                <div class="kpi-icon blue"><i class="fas fa-vial"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Tests Billed</div>
                    <div class="kpi-val"><?php echo $summary['total_tests_billed']; ?></div>
                    <div class="kpi-sub">Investigation items</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon teal"><i class="fas fa-receipt"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Billed Test Amount</div>
                    <div class="kpi-val"><?php echo $currency; ?> <?php echo number_format($summary['total_test_amount'], 2); ?></div>
                    <div class="kpi-sub">Subtotal of investigations</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon purple"><i class="fas fa-calculator"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Generated Commission</div>
                    <div class="kpi-val"><?php echo $currency; ?> <?php echo number_format($summary['total_generated_commission'], 2); ?></div>
                    <div class="kpi-sub">Test-specific rate based</div>
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
                <div class="kpi-icon amber"><i class="fas fa-hourglass-half"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Pending Collection</div>
                    <div class="kpi-val text-secondary"><?php echo $currency; ?> <?php echo number_format($summary['total_pending_commission'], 2); ?></div>
                    <div class="kpi-sub">Awaiting invoice collection</div>
                </div>
            </div>
        </div>

        <!-- Test Wise Table -->
        <div class="rep-table-card">
            <div class="rep-table-title">
                <span><i class="fas fa-microscope text-primary mr-2"></i> Investigation Tests Commission Breakdown</span>
            </div>

            <div class="table-responsive">
                <table class="table rep-table datatable-export" id="test-wise-table" width="100%">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Test / Investigation Name</th>
                            <th>Category</th>
                            <th>Doctor</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th>Rate</th>
                            <th>Generated</th>
                            <th>Eligible</th>
                            <th>Pending</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><a href="finance/invoice?id=<?php echo $r['invoice_id']; ?>" class="font-weight-bold text-primary">#<?php echo $r['invoice_id']; ?></a></td>
                                <td><?php echo date('d/m/Y', $r['invoice_date']); ?></td>
                                <td><strong class="text-dark"><?php echo $r['item_name']; ?></strong></td>
                                <td><span class="badge badge-light border"><?php echo $r['test_category']; ?></span></td>
                                <td><?php echo $r['doctor_name']; ?></td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['test_price'], 2); ?></td>
                                <td><?php echo $r['quantity']; ?></td>
                                <td><strong><?php echo $currency; ?> <?php echo number_format($r['subtotal'], 2); ?></strong></td>
                                <td>
                                    <?php if ($r['commission_rate'] > 0): ?>
                                        <span class="badge badge-pill badge-primary"><?php echo $r['commission_rate']; ?>%</span>
                                    <?php else: ?>
                                        <span class="badge badge-pill badge-light border">0%</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['generated_commission'], 2); ?></td>
                                <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($r['eligible_commission'], 2); ?></td>
                                <td class="text-secondary"><?php echo $currency; ?> <?php echo number_format($r['pending_commission'], 2); ?></td>
                                <td>
                                    <?php if ($r['commission_status'] === 'Fully Eligible'): ?>
                                        <span class="badge badge-status success">Fully Eligible</span>
                                    <?php elseif ($r['commission_status'] === 'Partially Eligible'): ?>
                                        <span class="badge badge-status warning">Partial</span>
                                    <?php else: ?>
                                        <span class="badge badge-status secondary">Pending Coll.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-right">Total Aggregate:</td>
                            <td><?php echo $currency; ?> <?php echo number_format($summary['total_test_amount'], 2); ?></td>
                            <td></td>
                            <td><?php echo $currency; ?> <?php echo number_format($summary['total_generated_commission'], 2); ?></td>
                            <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($summary['total_eligible_commission'], 2); ?></td>
                            <td class="text-secondary"><?php echo $currency; ?> <?php echo number_format($summary['total_pending_commission'], 2); ?></td>
                            <td></td>
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
    if ($.fn.DataTable.isDataTable('#test-wise-table')) {
        $('#test-wise-table').DataTable().destroy();
    }
    $('#test-wise-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Test-Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Test-Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Test-Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Test-Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[0, "desc"]],
        pageLength: 25
    });
});
</script>

