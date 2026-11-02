<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Phlebotomy & Sample Collection Audit Log</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-syringe text-primary"></i> <?php echo lang('sample_collection_report'); ?></h3>
                <p>Specimen accessioning audit, collector assignment, and phlebotomy collection tracking for <?php echo htmlspecialchars($period['label']); ?>.</p>
            </div>
            <form method="GET" action="lab/sampleCollectionReport" class="diag-filter-form">
                <select name="period_type" class="form-control form-control-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="today" <?php echo ($period['type'] == 'today') ? 'selected' : ''; ?>>Today</option>
                    <option value="this_week" <?php echo ($period['type'] == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                    <option value="this_month" <?php echo ($period['type'] == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                    <option value="last_month" <?php echo ($period['type'] == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="this_year" <?php echo ($period['type'] == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                </select>
                <select name="department_id" class="form-control form-control-sm" style="width: 150px;">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dep): ?>
                        <option value="<?php echo $dep->id; ?>" <?php echo ($filters['department_id'] == $dep->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dep->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="sample_status" class="form-control form-control-sm" style="width: 140px;">
                    <option value="">All Sample Status</option>
                    <option value="collected" <?php echo ($filters['sample_status'] == 'collected') ? 'selected' : ''; ?>>Collected</option>
                    <option value="pending" <?php echo ($filters['sample_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Apply</button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            </form>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Total Test Orders</div>
                <div class="kpi-value"><?php echo number_format($summary['total_samples']); ?></div>
                <div class="kpi-sub">Investigation accession log</div>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Samples Collected</div>
                <div class="kpi-value"><?php echo number_format($summary['collected_samples']); ?></div>
                <div class="kpi-sub">Specimens accessioned</div>
            </div>
            <div class="diag-kpi-card kpi-accent-amber">
                <div class="kpi-label">Samples Pending</div>
                <div class="kpi-value"><?php echo number_format($summary['pending_samples']); ?></div>
                <div class="kpi-sub">Awaiting phlebotomy</div>
            </div>
            <div class="diag-kpi-card kpi-accent-indigo">
                <div class="kpi-label">Direct / Non-Fluid Tests</div>
                <div class="kpi-value"><?php echo number_format($summary['non_specimen_tests']); ?></div>
                <div class="kpi-sub">X-Ray, USG, ECG, Echo</div>
            </div>
            <div class="diag-kpi-card kpi-accent-purple">
                <div class="kpi-label">Collection Rate</div>
                <div class="kpi-value"><?php echo $summary['collection_rate']; ?>%</div>
                <div class="kpi-sub">Of fluid specimen orders</div>
            </div>
        </div>

        <!-- SAMPLE AUDIT TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-syringe text-primary mr-1"></i> Specimen Accession Log (<?php echo count($samples); ?> Records)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="sampleTable">
                    <thead>
                        <tr>
                            <th>Sample ID</th>
                            <th>Invoice #</th>
                            <th>Order Date/Time</th>
                            <th>Patient Name</th>
                            <th>Patient ID</th>
                            <th>Test Name</th>
                            <th>Specimen Type</th>
                            <th>Department</th>
                            <th>Collector / Staff</th>
                            <th>Collection Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($samples as $s): ?>
                            <tr>
                                <td><strong>#<?php echo $s->id; ?></strong></td>
                                <td><a href="finance/invoice?id=<?php echo $s->invoice_id; ?>" target="_blank">#<?php echo $s->invoice_id; ?></a></td>
                                <td><?php echo date('d-m-Y h:i A', $s->date); ?></td>
                                <td><strong><?php echo htmlspecialchars($s->patient_name); ?></strong></td>
                                <td><?php echo htmlspecialchars($s->patient); ?></td>
                                <td><strong><?php echo htmlspecialchars($s->test_name); ?></strong></td>
                                <td><span class="badge badge-light border"><?php echo $s->sample_type; ?></span></td>
                                <td><?php echo htmlspecialchars($s->department_name); ?></td>
                                <td><?php echo !empty($s->collector_name) ? htmlspecialchars($s->collector_name) : '<span class="text-muted">Phlebotomy Staff</span>'; ?></td>
                                <td>
                                    <?php 
                                    if ($s->collection_status == 'Collected') {
                                        echo '<span class="badge-diag badge-diag-collected">Collected</span>';
                                    } elseif ($s->collection_status == 'Not Applicable') {
                                        echo '<span class="badge badge-secondary">Direct Exam</span>';
                                    } else {
                                        echo '<span class="badge-diag badge-diag-pending">Pending</span>';
                                    }
                                    ?>
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
    $('#sampleTable').DataTable({
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
