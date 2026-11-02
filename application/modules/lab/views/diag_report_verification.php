<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Medical Report Sign-off & Consultant Verification Audit</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-user-check text-primary"></i> <?php echo lang('report_verification_report'); ?></h3>
                <p>Medical verification audit trail, consultant electronic signatures, and quality sign-off log for <?php echo htmlspecialchars($period['label']); ?>.</p>
            </div>
            <form method="GET" action="lab/reportVerificationReport" class="diag-filter-form">
                <select name="period_type" class="form-control form-control-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="today" <?php echo ($period['type'] == 'today') ? 'selected' : ''; ?>>Today</option>
                    <option value="this_week" <?php echo ($period['type'] == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                    <option value="this_month" <?php echo ($period['type'] == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                    <option value="last_month" <?php echo ($period['type'] == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="this_year" <?php echo ($period['type'] == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Apply</button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            </form>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Reports Authored</div>
                <div class="kpi-value"><?php echo number_format($summary['total_authored']); ?></div>
                <div class="kpi-sub">Tests with drafted reports</div>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Verified & Signed</div>
                <div class="kpi-value"><?php echo number_format($summary['total_verified']); ?></div>
                <div class="kpi-sub">Consultant verified</div>
            </div>
            <div class="diag-kpi-card kpi-accent-amber">
                <div class="kpi-label">Pending Sign-off</div>
                <div class="kpi-value"><?php echo number_format($summary['pending_verification']); ?></div>
                <div class="kpi-sub">Awaiting medical sign-off</div>
            </div>
            <div class="diag-kpi-card kpi-accent-purple">
                <div class="kpi-label">Verification Rate</div>
                <div class="kpi-value"><?php echo $summary['verification_rate']; ?>%</div>
                <div class="kpi-sub">Of authored reports</div>
            </div>
        </div>

        <!-- VERIFICATION TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-user-check text-primary mr-1"></i> Quality Assurance & Verification Audit Log (<?php echo count($verifications); ?> Records)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="verificationTable">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Invoice #</th>
                            <th>Patient Name</th>
                            <th>Test Name</th>
                            <th>Department</th>
                            <th>Prepared Date</th>
                            <th>Prepared By</th>
                            <th>Verified By</th>
                            <th>Verification Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($verifications as $v): ?>
                            <tr>
                                <td><strong>#<?php echo $v->id; ?></strong></td>
                                <td><a href="finance/invoice?id=<?php echo $v->invoice_id; ?>" target="_blank">#<?php echo $v->invoice_id; ?></a></td>
                                <td><strong><?php echo htmlspecialchars($v->patient_name); ?></strong></td>
                                <td><strong><?php echo htmlspecialchars($v->test_name); ?></strong></td>
                                <td><?php echo htmlspecialchars($v->department_name); ?></td>
                                <td><?php echo date('d-m-Y', $v->date); ?></td>
                                <td><?php echo !empty($v->preparer_name) ? htmlspecialchars($v->preparer_name) : 'Technologist'; ?></td>
                                <td><?php echo !empty($v->verifier_name) ? htmlspecialchars($v->verifier_name) : '<span class="text-muted">Awaiting Sign-off</span>'; ?></td>
                                <td>
                                    <?php 
                                    if ($v->verification_status == 'Verified & Signed') {
                                        echo '<span class="badge-diag badge-diag-done">Verified & Signed</span>';
                                    } else {
                                        echo '<span class="badge-diag badge-diag-pending">Awaiting Sign-off</span>';
                                    }
                                    ?>
                                </td>
                                <td class="no-print">
                                    <a href="lab/testPdf?id=<?php echo $v->id; ?>" target="_blank" class="btn btn-xs btn-outline-info"><i class="fas fa-file-pdf"></i> View PDF</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="diag-print-signatures">
            <div class="diag-signature-box">Prepared By (Medical Technologist)</div>
            <div class="diag-signature-box">Verified By (Lab In-Charge)</div>
            <div class="diag-signature-box">Approved By (Head of Diagnostics)</div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    $('#verificationTable').DataTable({
        responsive: true,
        dom: "<'row no-print'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row no-print'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
        pageLength: 25,
        order: [[0, 'desc']]
    });
});
</script>
