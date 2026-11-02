<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Diagnostic Operational Efficiency & Workflow Throughput Scorecard</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-tachometer-alt text-primary"></i> <?php echo lang('diagnostic_operational_efficiency'); ?></h3>
                <p>Enterprise laboratory throughput scorecards, conversion funnels, and mathematically verified operational rates for <?php echo htmlspecialchars($period['label']); ?>.</p>
            </div>
            <form method="GET" action="lab/diagnosticOperationalEfficiency" class="diag-filter-form">
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

        <!-- 6 OPERATIONAL EFFICIENCY SCORECARDS -->
        <div class="row">
            <!-- 1. Sample Collection Efficiency -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="diag-table-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase font-weight-bold text-muted" style="font-size: 11px;">1. Sample Collection Efficiency</span>
                        <i class="fas fa-syringe text-cyan"></i>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: #0284c7;">
                        <?php echo $metrics['sample_collection_efficiency']['rate']; ?>%
                    </div>
                    <div class="progress my-2" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: <?php echo min(100, $metrics['sample_collection_efficiency']['rate']); ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size: 12px;">
                        <span>Collected: <strong><?php echo number_format($metrics['sample_collection_efficiency']['numerator']); ?></strong></span>
                        <span>Total Orders: <strong><?php echo number_format($metrics['sample_collection_efficiency']['denominator']); ?></strong></span>
                    </div>
                    <small class="text-muted d-block mt-2 font-italic">Formula: <?php echo $metrics['sample_collection_efficiency']['formula']; ?></small>
                </div>
            </div>

            <!-- 2. Test Completion Rate -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="diag-table-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase font-weight-bold text-muted" style="font-size: 11px;">2. Test Completion Rate</span>
                        <i class="fas fa-check-double text-success"></i>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: #10b981;">
                        <?php echo $metrics['test_completion_rate']['rate']; ?>%
                    </div>
                    <div class="progress my-2" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?php echo min(100, $metrics['test_completion_rate']['rate']); ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size: 12px;">
                        <span>Completed: <strong><?php echo number_format($metrics['test_completion_rate']['numerator']); ?></strong></span>
                        <span>Total Orders: <strong><?php echo number_format($metrics['test_completion_rate']['denominator']); ?></strong></span>
                    </div>
                    <small class="text-muted d-block mt-2 font-italic">Formula: <?php echo $metrics['test_completion_rate']['formula']; ?></small>
                </div>
            </div>

            <!-- 3. Report Verification Rate -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="diag-table-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase font-weight-bold text-muted" style="font-size: 11px;">3. Report Verification Rate</span>
                        <i class="fas fa-user-check text-purple"></i>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: #8b5cf6;">
                        <?php echo $metrics['report_verification_rate']['rate']; ?>%
                    </div>
                    <div class="progress my-2" style="height: 8px;">
                        <div class="progress-bar bg-purple" style="background-color: #8b5cf6; width: <?php echo min(100, $metrics['report_verification_rate']['rate']); ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size: 12px;">
                        <span>Verified: <strong><?php echo number_format($metrics['report_verification_rate']['numerator']); ?></strong></span>
                        <span>Prepared: <strong><?php echo number_format($metrics['report_verification_rate']['denominator']); ?></strong></span>
                    </div>
                    <small class="text-muted d-block mt-2 font-italic">Formula: <?php echo $metrics['report_verification_rate']['formula']; ?></small>
                </div>
            </div>

            <!-- 4. Report Delivery Rate -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="diag-table-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase font-weight-bold text-muted" style="font-size: 11px;">4. Report Delivery Rate</span>
                        <i class="fas fa-truck text-primary"></i>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: #0284c7;">
                        <?php echo $metrics['report_delivery_rate']['rate']; ?>%
                    </div>
                    <div class="progress my-2" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: <?php echo min(100, $metrics['report_delivery_rate']['rate']); ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size: 12px;">
                        <span>Delivered: <strong><?php echo number_format($metrics['report_delivery_rate']['numerator']); ?></strong></span>
                        <span>Prepared: <strong><?php echo number_format($metrics['report_delivery_rate']['denominator']); ?></strong></span>
                    </div>
                    <small class="text-muted d-block mt-2 font-italic">Formula: <?php echo $metrics['report_delivery_rate']['formula']; ?></small>
                </div>
            </div>

            <!-- 5. Order Cancellation Rate -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="diag-table-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase font-weight-bold text-muted" style="font-size: 11px;">5. Order Cancellation Rate</span>
                        <i class="fas fa-ban text-danger"></i>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: #ef4444;">
                        <?php echo $metrics['cancellation_rate']['rate']; ?>%
                    </div>
                    <div class="progress my-2" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: <?php echo min(100, $metrics['cancellation_rate']['rate']); ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size: 12px;">
                        <span>Cancelled: <strong><?php echo number_format($metrics['cancellation_rate']['numerator']); ?></strong></span>
                        <span>Total Orders: <strong><?php echo number_format($metrics['cancellation_rate']['denominator']); ?></strong></span>
                    </div>
                    <small class="text-muted d-block mt-2 font-italic">Formula: <?php echo $metrics['cancellation_rate']['formula']; ?></small>
                </div>
            </div>

            <!-- 6. Average Turnaround Time -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="diag-table-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase font-weight-bold text-muted" style="font-size: 11px;">6. Turnaround Time (Mean)</span>
                        <i class="fas fa-stopwatch text-warning"></i>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: #f59e0b;">
                        <?php echo $metrics['average_tat_hours']['value']; ?>
                    </div>
                    <div class="progress my-2" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: 75%"></div>
                    </div>
                    <div class="text-muted" style="font-size: 12px;">
                        Benchmark: Routine Diagnostic Routine Target: 24h
                    </div>
                    <small class="text-muted d-block mt-2 font-italic">Formula: <?php echo $metrics['average_tat_hours']['formula']; ?></small>
                </div>
            </div>
        </div>

        <div class="diag-print-signatures">
            <div class="diag-signature-box">Prepared By (Medical Technologist)</div>
            <div class="diag-signature-box">Verified By (Lab In-Charge)</div>
            <div class="diag-signature-box">Approved By (Head of Diagnostics)</div>
        </div>

    </div>
</div>
