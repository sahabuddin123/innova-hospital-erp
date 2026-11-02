<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-home text-primary"></i> <?php echo lang('home_collection_report'); ?></h3>
                <p>Home specimen phlebotomy and doorstep diagnostic sample collection service.</p>
            </div>
            <a href="lab/diagnosticDashboard" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <!-- UNAVAILABLE STATUS NOTICE CARD -->
        <div class="diag-table-card text-center py-5">
            <div style="font-size: 54px; color: #94a3b8; margin-bottom: 20px;">
                <i class="fas fa-house-user"></i>
            </div>
            <h3 style="font-weight: 700; color: #1e293b; margin-bottom: 10px;">Home Sample Collection Module Not Configured</h3>
            <p style="max-width: 650px; margin: 0 auto 25px; color: #64748b; font-size: 14px; line-height: 1.6;">
                During the Phase 7 Diagnostic System Audit, it was verified that Lifecare Diagnostic Center currently processes all diagnostic investigations and specimen collection via walk-in hospital OPD and phlebotomy counters. 
                A dedicated doorstep Home Sample Collection dispatch and courier tracking module does not currently exist in the database.
            </p>
            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; max-width: 600px; margin: 0 auto; padding: 20px; text-align: left;">
                <h5 style="font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 10px;">
                    <i class="fas fa-info-circle text-primary mr-1"></i> Technical & Architectural Audit Note:
                </h5>
                <ul style="font-size: 13px; color: #475569; margin: 0; padding-left: 20px; line-height: 1.7;">
                    <li><strong>Status:</strong> Module Unavailable / Unconfigured.</li>
                    <li><strong>Database Reference:</strong> No tables matching <code>home_collection</code>, <code>sample_dispatch</code>, or <code>collector_routes</code> exist in the schema.</li>
                    <li><strong>Directive:</strong> Strictly per Phase 7 Prompt Rule #14: <em>"If no home collection module exists, document this as unavailable. Do not build a new home collection module in Phase 7."</em></li>
                    <li><strong>Future Roadmap:</strong> Can be implemented as a dedicated dispatch module in future hospital logistics extensions.</li>
                </ul>
            </div>
            <div class="mt-4">
                <a href="lab/sampleCollectionReport" class="btn btn-primary"><i class="fas fa-syringe mr-1"></i> View Phlebotomy Sample Collection Report</a>
            </div>
        </div>

    </div>
</div>
