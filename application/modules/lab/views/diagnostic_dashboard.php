<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">
<script src="common/assets/chart.js/Chart.bundle.min.js"></script>

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <!-- Top Submenu Navigation -->
        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <!-- Printable Header (Visible Only in Print) -->
        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Diagnostic Performance & Laboratory Overview Dashboard</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <!-- Header Card with Period Filters -->
        <div class="diag-card-header no-print">
            <div class="diag-header-title">
                <h3><i class="fas fa-chart-line text-primary"></i> <?php echo lang('diagnostic_dashboard'); ?></h3>
                <p>Real-time operational diagnostic throughput, laboratory workflow metrics, and test volume analytics.</p>
            </div>
            <form method="GET" action="lab/diagnosticDashboard" class="diag-filter-form">
                <select name="period_type" class="form-control form-control-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="today" <?php echo ($period_type == 'today') ? 'selected' : ''; ?>>Today</option>
                    <option value="yesterday" <?php echo ($period_type == 'yesterday') ? 'selected' : ''; ?>>Yesterday</option>
                    <option value="this_week" <?php echo ($period_type == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                    <option value="this_month" <?php echo ($period_type == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                    <option value="last_month" <?php echo ($period_type == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="this_year" <?php echo ($period_type == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                    <option value="custom" <?php echo ($period_type == 'custom') ? 'selected' : ''; ?>>Custom Range</option>
                </select>
                <?php if ($period_type == 'custom'): ?>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo htmlspecialchars($date_from); ?>">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo htmlspecialchars($date_to); ?>">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Apply</button>
                <?php endif; ?>
                <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            </form>
        </div>

        <!-- 11 KPI CARDS GRID -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Registered Patients</div>
                <div class="kpi-value"><?php echo number_format($summary['total_registered_patients']); ?></div>
                <div class="kpi-sub">Served: <?php echo number_format($summary['total_patients_served']); ?> in period</div>
                <i class="fas fa-user-plus kpi-icon text-primary"></i>
            </div>
            <div class="diag-kpi-card kpi-accent-indigo">
                <div class="kpi-label">Diagnostic Invoices</div>
                <div class="kpi-value"><?php echo number_format($summary['total_invoices']); ?></div>
                <div class="kpi-sub">Investigation orders</div>
                <i class="fas fa-file-invoice kpi-icon text-indigo"></i>
            </div>
            <div class="diag-kpi-card kpi-accent-purple">
                <div class="kpi-label">Investigation Items</div>
                <div class="kpi-value"><?php echo number_format($summary['total_investigation_items']); ?></div>
                <div class="kpi-sub">Billed test lines</div>
                <i class="fas fa-list-ol kpi-icon text-purple"></i>
            </div>
            <div class="diag-kpi-card kpi-accent-cyan">
                <div class="kpi-label">Total Tests Ordered</div>
                <div class="kpi-value"><?php echo number_format($summary['total_tests_ordered']); ?></div>
                <div class="kpi-sub">Active workflow count</div>
                <i class="fas fa-vial kpi-icon text-cyan"></i>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Samples Collected</div>
                <div class="kpi-value"><?php echo number_format($summary['samples_collected']); ?></div>
                <div class="kpi-sub">Specimens accessioned</div>
                <i class="fas fa-syringe kpi-icon text-success"></i>
            </div>
            <div class="diag-kpi-card kpi-accent-amber">
                <div class="kpi-label">Samples Pending</div>
                <div class="kpi-value"><?php echo number_format($summary['samples_pending']); ?></div>
                <div class="kpi-sub">Awaiting phlebotomy</div>
                <i class="fas fa-hourglass-start kpi-icon text-warning"></i>
            </div>
            <div class="diag-kpi-card kpi-accent-indigo">
                <div class="kpi-label">Tests In Progress</div>
                <div class="kpi-value"><?php echo number_format($summary['tests_in_progress']); ?></div>
                <div class="kpi-sub">On analyzer / bench</div>
                <i class="fas fa-microscope kpi-icon text-indigo"></i>
            </div>
            <div class="diag-kpi-card kpi-accent-amber">
                <div class="kpi-label">Reports Pending</div>
                <div class="kpi-value"><?php echo number_format($summary['reports_pending']); ?></div>
                <div class="kpi-sub">Awaiting draft/sign-off</div>
                <i class="fas fa-edit kpi-icon text-warning"></i>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label">Reports Verified</div>
                <div class="kpi-value"><?php echo number_format($summary['reports_verified']); ?></div>
                <div class="kpi-sub">Consultant signed</div>
                <i class="fas fa-user-check kpi-icon text-success"></i>
            </div>
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Reports Delivered</div>
                <div class="kpi-value"><?php echo number_format($summary['reports_delivered']); ?></div>
                <div class="kpi-sub">Patient handed over</div>
                <i class="fas fa-hand-holding-medical kpi-icon text-primary"></i>
            </div>
            <div class="diag-kpi-card kpi-accent-rose">
                <div class="kpi-label">Cancelled Tests</div>
                <div class="kpi-value"><?php echo number_format($summary['cancelled_tests']); ?></div>
                <div class="kpi-sub">Withdrawn orders</div>
                <i class="fas fa-ban kpi-icon text-danger"></i>
            </div>
        </div>

        <!-- Informational Financial Indicators Box -->
        <div class="diag-info-box">
            <i class="fas fa-info-circle mr-1"></i>
            <strong>Informational Financial Metrics (Existing Invoices):</strong>
            Gross Billing: ৳<?php echo number_format($summary['info_gross_billing'], 2); ?> | 
            Net Billed Revenue: ৳<?php echo number_format($summary['info_net_billing'], 2); ?>.
            <span class="text-muted ml-1">(Operational diagnostic performance tracks patient turnaround and specimen throughput independently from revenue.)</span>
        </div>

        <!-- 10 DASHBOARD CHARTS GRID -->
        <div class="row">
            <!-- 1. Daily Registration & Test Order Trend -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="diag-table-card">
                    <div class="diag-table-header">
                        <h4><i class="fas fa-chart-line text-primary mr-1"></i> Daily Patient Registration & Test Order Trend</h4>
                    </div>
                    <canvas id="chartDailyOrders" height="150"></canvas>
                </div>
            </div>

            <!-- 2. Workflow Stage Throughput Trend -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="diag-table-card">
                    <div class="diag-table-header">
                        <h4><i class="fas fa-tasks text-success mr-1"></i> Samples Collected vs Tests Completed vs Delivered</h4>
                    </div>
                    <canvas id="chartWorkflowThroughput" height="150"></canvas>
                </div>
            </div>

            <!-- 3. Category-wise Test Distribution -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="diag-table-card">
                    <div class="diag-table-header">
                        <h4><i class="fas fa-pie-chart text-info mr-1"></i> Category-wise Distribution</h4>
                    </div>
                    <canvas id="chartCatDist" height="200"></canvas>
                </div>
            </div>

            <!-- 4. Department-wise Test Distribution -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="diag-table-card">
                    <div class="diag-table-header">
                        <h4><i class="fas fa-hospital text-indigo mr-1"></i> Department-wise Distribution</h4>
                    </div>
                    <canvas id="chartDeptDist" height="200"></canvas>
                </div>
            </div>

            <!-- 5. Monthly Test Volume Trend (Last 12 Months) -->
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="diag-table-card">
                    <div class="diag-table-header">
                        <h4><i class="fas fa-calendar-alt text-purple mr-1"></i> Monthly Test Volume (12 Mo)</h4>
                    </div>
                    <canvas id="chartMonthlyVol" height="200"></canvas>
                </div>
            </div>

            <!-- 6. Top 10 Most Ordered Diagnostic Tests -->
            <div class="col-lg-12 col-md-12 mb-4">
                <div class="diag-table-card">
                    <div class="diag-table-header">
                        <h4><i class="fas fa-trophy text-warning mr-1"></i> Top 10 Most Ordered Diagnostic Tests</h4>
                    </div>
                    <canvas id="chartTopTests" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- 3-Tier Signatures on Print -->
        <div class="diag-print-signatures">
            <div class="diag-signature-box">Prepared By (Medical Technologist)</div>
            <div class="diag-signature-box">Verified By (Lab In-Charge)</div>
            <div class="diag-signature-box">Approved By (Head of Diagnostics)</div>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Daily Orders & Registrations Chart
    var dailyData = <?php echo json_encode($charts['daily_trends']); ?>;
    var dailyLabels = dailyData.map(function(d){ return d.label; });
    var dailyOrders = dailyData.map(function(d){ return d.tests_ordered; });
    var dailyPatients = dailyData.map(function(d){ return d.patients; });

    new Chart(document.getElementById('chartDailyOrders'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [
                {
                    label: 'Tests Ordered',
                    data: dailyOrders,
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(2, 132, 199, 0.1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Patients',
                    data: dailyPatients,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'transparent',
                    borderDash: [5, 5],
                    tension: 0.3
                }
            ]
        },
        options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
    });

    // 2. Workflow Throughput Chart
    var dailySamples = dailyData.map(function(d){ return d.samples_collected; });
    var dailyDone = dailyData.map(function(d){ return d.tests_completed; });
    var dailyDelivered = dailyData.map(function(d){ return d.reports_delivered; });

    new Chart(document.getElementById('chartWorkflowThroughput'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [
                { label: 'Samples Collected', data: dailySamples, borderColor: '#06b6d4', fill: false },
                { label: 'Tests Completed', data: dailyDone, borderColor: '#10b981', fill: false },
                { label: 'Reports Delivered', data: dailyDelivered, borderColor: '#3b82f6', fill: false }
            ]
        },
        options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
    });

    // 3. Category-wise Distribution
    var catData = <?php echo json_encode($charts['category_dist']); ?>;
    new Chart(document.getElementById('chartCatDist'), {
        type: 'doughnut',
        data: {
            labels: catData.map(function(c){ return c.cat_name; }),
            datasets: [{
                data: catData.map(function(c){ return c.test_count; }),
                backgroundColor: ['#0284c7', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#64748b']
            }]
        },
        options: { responsive: true, legend: { position: 'bottom' } }
    });

    // 4. Department-wise Distribution
    var deptData = <?php echo json_encode($charts['department_dist']); ?>;
    new Chart(document.getElementById('chartDeptDist'), {
        type: 'doughnut',
        data: {
            labels: deptData.map(function(d){ return d.dept_name; }),
            datasets: [{
                data: deptData.map(function(d){ return d.test_count; }),
                backgroundColor: ['#6366f1', '#14b8a6', '#f97316', '#a855f7', '#3b82f6', '#84cc16']
            }]
        },
        options: { responsive: true, legend: { position: 'bottom' } }
    });

    // 5. Monthly Volume (12 Months)
    var mData = <?php echo json_encode($charts['monthly_volume']); ?>;
    new Chart(document.getElementById('chartMonthlyVol'), {
        type: 'bar',
        data: {
            labels: mData.map(function(m){ return m.label; }),
            datasets: [{
                label: 'Tests Ordered',
                data: mData.map(function(m){ return m.count; }),
                backgroundColor: '#8b5cf6'
            }]
        },
        options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
    });

    // 6. Top 10 Tests
    var topData = <?php echo json_encode($charts['top_tests']); ?>;
    new Chart(document.getElementById('chartTopTests'), {
        type: 'horizontalBar',
        data: {
            labels: topData.map(function(t){ return t.test_name; }),
            datasets: [{
                label: 'Order Count',
                data: topData.map(function(t){ return t.order_count; }),
                backgroundColor: '#0284c7'
            }]
        },
        options: { responsive: true, scales: { xAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
    });
});
</script>
