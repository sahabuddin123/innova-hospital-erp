<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Pending Diagnostic Investigation Worklist & Queue Audit</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-hourglass-half text-primary"></i> <?php echo lang('pending_test_report'); ?></h3>
                <p>Investigation-item level pending queues classified by exact clinical and laboratory workflow stage.</p>
            </div>
            <form method="GET" action="lab/pendingTestReport" class="diag-filter-form">
                <select name="stage" class="form-control form-control-sm" style="width: 190px;" onchange="this.form.submit()">
                    <option value="all">All Workflow Stages</option>
                    <option value="Awaiting Sample Collection" <?php echo ($filters['stage'] == 'Awaiting Sample Collection') ? 'selected' : ''; ?>>Awaiting Sample Collection</option>
                    <option value="Sample Collected" <?php echo ($filters['stage'] == 'Sample Collected') ? 'selected' : ''; ?>>Sample Collected</option>
                    <option value="Processing" <?php echo ($filters['stage'] == 'Processing') ? 'selected' : ''; ?>>Processing / In Progress</option>
                    <option value="Awaiting Verification" <?php echo ($filters['stage'] == 'Awaiting Verification') ? 'selected' : ''; ?>>Awaiting Verification</option>
                    <option value="Awaiting Delivery" <?php echo ($filters['stage'] == 'Awaiting Delivery') ? 'selected' : ''; ?>>Awaiting Delivery</option>
                </select>
                <select name="department_id" class="form-control form-control-sm" style="width: 150px;">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dep): ?>
                        <option value="<?php echo $dep->id; ?>" <?php echo ($filters['department_id'] == $dep->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dep->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Apply</button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            </form>
        </div>

        <!-- STAGE SUMMARY CARDS -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-rose">
                <div class="kpi-label">Total Pending Queue</div>
                <div class="kpi-value"><?php echo number_format($summary['total_pending']); ?></div>
                <div class="kpi-sub">Active pending investigations</div>
            </div>
            <div class="diag-kpi-card kpi-accent-amber">
                <div class="kpi-label">Awaiting Phlebotomy</div>
                <div class="kpi-value"><?php echo number_format($summary['awaiting_sample_collection']); ?></div>
                <div class="kpi-sub">Sample pending</div>
            </div>
            <div class="diag-kpi-card kpi-accent-cyan">
                <div class="kpi-label">Sample Accessioned</div>
                <div class="kpi-value"><?php echo number_format($summary['sample_collected']); ?></div>
                <div class="kpi-sub">Ready for bench</div>
            </div>
            <div class="diag-kpi-card kpi-accent-indigo">
                <div class="kpi-label">Processing / Analyzer</div>
                <div class="kpi-value"><?php echo number_format($summary['processing']); ?></div>
                <div class="kpi-sub">Testing in progress</div>
            </div>
            <div class="diag-kpi-card kpi-accent-purple">
                <div class="kpi-label">Awaiting Sign-off</div>
                <div class="kpi-value"><?php echo number_format($summary['awaiting_verification']); ?></div>
                <div class="kpi-sub">Consultant verification</div>
            </div>
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Awaiting Delivery</div>
                <div class="kpi-value"><?php echo number_format($summary['awaiting_delivery']); ?></div>
                <div class="kpi-sub">Ready at front counter</div>
            </div>
        </div>

        <!-- PENDING WORKLIST TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-list text-primary mr-1"></i> Active Pending Worklist (<?php echo count($pending_tests); ?> Tests)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="pendingTable">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Inv #</th>
                            <th>Order Date/Time</th>
                            <th>Elapsed Time</th>
                            <th>Patient Name & Phone</th>
                            <th>Test Name</th>
                            <th>Department</th>
                            <th>Referring Doctor</th>
                            <th>Current Stage</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_tests as $p): ?>
                            <tr>
                                <td><strong>#<?php echo $p->id; ?></strong></td>
                                <td><a href="finance/invoice?id=<?php echo $p->invoice_id; ?>" target="_blank">#<?php echo $p->invoice_id; ?></a></td>
                                <td><?php echo date('d-m-Y h:i A', $p->date); ?></td>
                                <td>
                                    <?php 
                                    if ($p->elapsed_hours >= 48) {
                                        echo '<span class="badge badge-danger">' . $p->elapsed_hours . 'h</span>';
                                    } elseif ($p->elapsed_hours >= 24) {
                                        echo '<span class="badge badge-warning">' . $p->elapsed_hours . 'h</span>';
                                    } else {
                                        echo '<span class="badge badge-light border">' . $p->elapsed_hours . 'h</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($p->patient_name); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($p->patient_phone); ?></small>
                                </td>
                                <td><strong><?php echo htmlspecialchars($p->test_name); ?></strong></td>
                                <td><?php echo htmlspecialchars($p->department_name); ?></td>
                                <td><?php echo htmlspecialchars($p->doctor_name_resolved); ?></td>
                                <td>
                                    <?php 
                                    if ($p->workflow_stage == 'Awaiting Delivery') {
                                        echo '<span class="badge-diag badge-diag-delivered">Awaiting Delivery</span>';
                                    } elseif ($p->workflow_stage == 'Awaiting Verification') {
                                        echo '<span class="badge-diag badge-diag-done">Awaiting Verification</span>';
                                    } elseif ($p->workflow_stage == 'Processing') {
                                        echo '<span class="badge-diag badge-diag-waiting">Processing</span>';
                                    } elseif ($p->workflow_stage == 'Sample Collected') {
                                        echo '<span class="badge-diag badge-diag-collected">Sample Collected</span>';
                                    } else {
                                        echo '<span class="badge-diag badge-diag-pending">Awaiting Phlebotomy</span>';
                                    }
                                    ?>
                                </td>
                                <td class="no-print">
                                    <a href="lab?id=<?php echo $p->id; ?>" class="btn btn-xs btn-outline-primary" title="Update Report"><i class="fas fa-edit"></i> Report</a>
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
    $('#pendingTable').DataTable({
        responsive: true,
        dom: "<'row no-print'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row no-print'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
        pageLength: 25,
        order: [[2, 'asc']] // Oldest first
    });
});
</script>
