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
                    <h2><i class="fas fa-book text-primary"></i> Doctor Commission Ledger</h2>
                    <p>Chronological general ledger of commission accruals, qualifying collections, payouts, and running payable balances &bull; <strong><?php echo $period['label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <button type="button" onclick="window.print()" class="rep-action-btn"><i class="fas fa-print"></i> Print Ledger</button>
                    <a href="finance/doctorCommissionStatement<?php echo !empty($selected_doctor) ? '?id=' . $selected_doctor : ''; ?>" class="rep-action-btn"><i class="fas fa-file-signature"></i> Doctor Statement</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="finance/doctorCommissionLedger?period_type=today<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="finance/doctorCommissionLedger?period_type=yesterday<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="finance/doctorCommissionLedger?period_type=this_week<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="finance/doctorCommissionLedger?period_type=this_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="finance/doctorCommissionLedger?period_type=last_month<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="finance/doctorCommissionLedger?period_type=this_year<?php echo !empty($selected_doctor) ? '&doctor_id=' . $selected_doctor : ''; ?>" class="rep-pill <?php echo ($period['type'] === 'this_year') ? 'active' : ''; ?>">This Year</a>
                <button type="button" class="rep-pill <?php echo ($period['type'] === 'custom') ? 'active' : ''; ?>" onclick="$('#filterCollapse').toggle();">Custom Filter <i class="fas fa-filter ml-1"></i></button>
            </div>

            <!-- Filter Controls -->
            <form action="finance/doctorCommissionLedger" method="get" class="d-print-none <?php echo ($period['type'] === 'custom') ? '' : 'collapse'; ?>" id="filterCollapse" style="<?php echo ($period['type'] === 'custom') ? 'display:block;' : 'display:none;'; ?> margin-top: 12px;">
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
                <div class="kpi-icon blue"><i class="fas fa-stream"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Ledger Entries</div>
                    <div class="kpi-val"><?php echo $summary['total_entries']; ?></div>
                    <div class="kpi-sub">Total chronological events</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon amber"><i class="fas fa-balance-scale"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Ending Running Balance</div>
                    <div class="kpi-val text-warning"><?php echo $currency; ?> <?php echo number_format($summary['final_running_balance'], 2); ?></div>
                    <div class="kpi-sub">Cumulative payable liability</div>
                </div>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="rep-table-card">
            <div class="rep-table-title">
                <span><i class="fas fa-list-ol text-primary mr-2"></i> Chronological Commission Ledger</span>
            </div>

            <div class="table-responsive">
                <table class="table rep-table datatable-export" id="ledger-table" width="100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Doctor</th>
                            <th>Entry Type</th>
                            <th>Reference</th>
                            <th>Investigation / Description</th>
                            <th>Generated (+)</th>
                            <th>Eligible (+)</th>
                            <th>Disbursed (-)</th>
                            <th>Reversal (-)</th>
                            <th>Running Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', $r['date']); ?></td>
                                <td><strong><?php echo $r['doctor_name']; ?></strong></td>
                                <td>
                                    <?php if ($r['type'] === 'Commission Accrued'): ?>
                                        <span class="badge badge-status info">Accrued</span>
                                    <?php elseif ($r['type'] === 'Commission Payout'): ?>
                                        <span class="badge badge-status success">Payout</span>
                                    <?php else: ?>
                                        <span class="badge badge-status secondary"><?php echo $r['type']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo $r['ref_no']; ?></code></td>
                                <td>
                                    <strong><?php echo $r['test_name']; ?></strong>
                                    <?php if (!empty($r['notes'])): ?>
                                        <div class="text-muted small"><?php echo $r['notes']; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo ($r['commission_generated'] > 0) ? $currency . ' ' . number_format($r['commission_generated'], 2) : '-'; ?></td>
                                <td class="text-success font-weight-bold"><?php echo ($r['commission_eligible'] > 0) ? $currency . ' ' . number_format($r['commission_eligible'], 2) : '-'; ?></td>
                                <td class="text-primary"><?php echo ($r['commission_paid'] > 0) ? $currency . ' ' . number_format($r['commission_paid'], 2) : '-'; ?></td>
                                <td class="text-danger"><?php echo ($r['reversal'] > 0) ? $currency . ' ' . number_format($r['reversal'], 2) : '-'; ?></td>
                                <td class="font-weight-bold text-dark">
                                    <?php echo $currency; ?> <?php echo number_format($r['running_balance'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9" class="text-right">Final Period Balance:</td>
                            <td class="font-weight-bold text-warning"><?php echo $currency; ?> <?php echo number_format($summary['final_running_balance'], 2); ?></td>
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
    if ($.fn.DataTable.isDataTable('#ledger-table')) {
        $('#ledger-table').DataTable().destroy();
    }
    $('#ledger-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Doctor Commission Ledger - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Doctor Commission Ledger - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Doctor Commission Ledger - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Doctor Commission Ledger - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[0, "asc"]], // Chronological order
        pageLength: 50
    });
});
</script>

