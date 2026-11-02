<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Diagnostic Report Handover & Delivery Audit</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-truck text-primary"></i> <?php echo lang('report_delivery_report'); ?></h3>
                <p>Front-desk patient report handover audit, delivery timestamps, and receiver verification for <?php echo htmlspecialchars($period['label']); ?>.</p>
            </div>
            <form method="GET" action="lab/reportDeliveryReport" class="diag-filter-form">
                <select name="period_type" class="form-control form-control-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="today" <?php echo ($period['type'] == 'today') ? 'selected' : ''; ?>>Today</option>
                    <option value="this_week" <?php echo ($period['type'] == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                    <option value="this_month" <?php echo ($period['type'] == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                    <option value="last_month" <?php echo ($period['type'] == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="this_year" <?php echo ($period['type'] == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                </select>
                <select name="delivery_status" class="form-control form-control-sm" style="width: 140px;">
                    <option value="">All Delivery Status</option>
                    <option value="delivered" <?php echo ($filters['delivery_status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                    <option value="pending" <?php echo ($filters['delivery_status'] == 'pending') ? 'selected' : ''; ?>>Pending Handover</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Apply</button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            </form>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Reports Prepared</div>
                <div class="kpi-value"><?php echo number_format($summary['total_reports_prepared']); ?></div>
                <div class="kpi-sub">Completed diagnostic tests</div>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Reports Delivered</div>
                <div class="kpi-value"><?php echo number_format($summary['total_reports_delivered']); ?></div>
                <div class="kpi-sub">Handed to patient / party</div>
            </div>
            <div class="diag-kpi-card kpi-accent-amber">
                <div class="kpi-label">Pending Handover</div>
                <div class="kpi-value"><?php echo number_format($summary['pending_delivery']); ?></div>
                <div class="kpi-sub">Awaiting patient collection</div>
            </div>
            <div class="diag-kpi-card kpi-accent-purple">
                <div class="kpi-label">Delivery Rate</div>
                <div class="kpi-value"><?php echo $summary['delivery_rate']; ?>%</div>
                <div class="kpi-sub">Of completed reports</div>
            </div>
        </div>

        <!-- DELIVERY TABLE -->
        <div class="diag-table-card">
            <div class="diag-table-header">
                <h4><i class="fas fa-truck text-primary mr-1"></i> Patient Report Delivery Log (<?php echo count($reports); ?> Records)</h4>
            </div>
            <div class="table-responsive">
                <table class="table-diag" id="deliveryTable">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Invoice #</th>
                            <th>Patient Name</th>
                            <th>Patient Phone</th>
                            <th>Test Name</th>
                            <th>Department</th>
                            <th>Delivery Date/Time</th>
                            <th>Delivery Method</th>
                            <th>Receiver Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $r): ?>
                            <tr>
                                <td><strong>#<?php echo $r->id; ?></strong></td>
                                <td><a href="finance/invoice?id=<?php echo $r->invoice_id; ?>" target="_blank">#<?php echo $r->invoice_id; ?></a></td>
                                <td><strong><?php echo htmlspecialchars($r->patient_name); ?></strong></td>
                                <td><?php echo htmlspecialchars($r->patient_phone); ?></td>
                                <td><strong><?php echo htmlspecialchars($r->test_name); ?></strong></td>
                                <td><?php echo htmlspecialchars($r->department_name); ?></td>
                                <td>
                                    <?php echo !empty($r->delivery_status_date) ? date('d-m-Y h:i A', $r->delivery_status_date) : '<span class="text-muted">Awaiting Handover</span>'; ?>
                                </td>
                                <td><span class="badge badge-light border"><?php echo $r->delivery_method; ?></span></td>
                                <td>
                                    <?php echo !empty($r->receiver_name) ? htmlspecialchars($r->receiver_name) : (!empty($r->patient_name) ? htmlspecialchars($r->patient_name) . ' (Self)' : '-'); ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($r->delivery_status == 'delivered') {
                                        echo '<span class="badge-diag badge-diag-delivered">Delivered</span>';
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
    $('#deliveryTable').DataTable({
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
