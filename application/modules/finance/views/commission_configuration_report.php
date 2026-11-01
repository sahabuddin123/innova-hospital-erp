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
                    <h2><i class="fas fa-cogs text-primary"></i> Commission Configuration Report</h2>
                    <p>Audit of test-wise doctor commission rates, standard pricing, and zero-commission compliance flags</p>
                </div>
                <div class="rep-actions d-print-none">
                    <button type="button" onclick="window.print()" class="rep-action-btn"><i class="fas fa-print"></i> Print Configuration</button>
                    <a href="finance/paymentCategory" class="rep-action-btn"><i class="fas fa-edit"></i> Edit Categories</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <div class="alert alert-warning py-2 mb-0 d-print-none" style="font-size: 13px;">
                <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Audit Policy:</strong> This report is strictly for visibility and compliance verification. Tests flagged with <span class="badge badge-warning text-dark font-weight-bold">Zero Commission</span> are highlighted for administration review to ensure no commission entitlements are missed. Existing rates are preserved without modification.
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card">
                <div class="kpi-icon blue"><i class="fas fa-list-ul"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Catalog Tests</div>
                    <div class="kpi-val"><?php echo $summary['total_tests']; ?></div>
                    <div class="kpi-sub">Total active & inactive tests</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Configured Tests</div>
                    <div class="kpi-val text-success"><?php echo $summary['configured_count']; ?></div>
                    <div class="kpi-sub">Rate &gt; 0% set</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon amber"><i class="fas fa-exclamation-circle"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Zero Commission</div>
                    <div class="kpi-val text-warning"><?php echo $summary['zero_commission_count']; ?></div>
                    <div class="kpi-sub">Flagged for admin review</div>
                </div>
            </div>

            <div class="rep-kpi-card">
                <div class="kpi-icon red"><i class="fas fa-ban"></i></div>
                <div class="kpi-info">
                    <div class="kpi-title">Inactive Tests</div>
                    <div class="kpi-val text-secondary"><?php echo $summary['inactive_count']; ?></div>
                    <div class="kpi-sub">Disabled in catalog</div>
                </div>
            </div>
        </div>

        <!-- Configuration Table -->
        <div class="rep-table-card">
            <div class="rep-table-title">
                <span><i class="fas fa-sliders-h text-primary mr-2"></i> Investigation Tests & Commission Schedule</span>
            </div>

            <div class="table-responsive">
                <table class="table rep-table datatable-export" id="config-table" width="100%">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Test / Investigation Name</th>
                            <th>Category Group</th>
                            <th>Standard Price</th>
                            <th>Doctor Commission %</th>
                            <th>Nominal Comm. (৳)</th>
                            <th>Referral Comm. %</th>
                            <th>Catalog Status</th>
                            <th>Compliance Audit</th>
                            <th class="d-print-none text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><code><?php echo $r['code']; ?></code></td>
                                <td><strong class="text-dark"><?php echo $r['test_name']; ?></strong></td>
                                <td><span class="badge badge-light border"><?php echo $r['category_group']; ?></span></td>
                                <td><strong><?php echo $currency; ?> <?php echo number_format($r['standard_price'], 2); ?></strong></td>
                                <td>
                                    <?php if ($r['doctor_commission_rate'] > 0): ?>
                                        <span class="badge badge-pill badge-primary font-weight-bold"><?php echo $r['doctor_commission_rate']; ?>%</span>
                                    <?php else: ?>
                                        <span class="badge badge-pill badge-light border text-muted">0%</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $currency; ?> <?php echo number_format($r['nominal_commission'], 2); ?></td>
                                <td><span class="badge badge-light border"><?php echo $r['referrer_commission_rate']; ?>%</span></td>
                                <td>
                                    <?php if ($r['status'] === 'Active'): ?>
                                        <span class="badge badge-status success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-status danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($r['compliance_status'] === 'Configured'): ?>
                                        <span class="badge badge-status success"><i class="fas fa-check-circle mr-1"></i> Configured</span>
                                    <?php else: ?>
                                        <span class="badge badge-status warning"><i class="fas fa-flag mr-1"></i> Zero Rate (Review)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="d-print-none text-right">
                                    <a href="finance/editPaymentCategory?id=<?php echo $r['item_id']; ?>" class="btn btn-xs btn-outline-secondary" title="Edit in Catalog">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Print Signatures -->
            <div class="rep-print-signatures">
                <div class="signature-box">Audited By (Finance Department)</div>
                <div class="signature-box">Reviewed By (Chief Pathologist / Lab Head)</div>
                <div class="signature-box">Approved By (Managing Director)</div>
            </div>
        </div>

    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#config-table')) {
        $('#config-table').DataTable().destroy();
    }
    $('#config-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Commission Configuration Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Commission Configuration Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Commission Configuration Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Commission Configuration Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[4, "desc"]], // Highest rate first
        pageLength: 50
    });
});
</script>
