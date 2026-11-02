<?php
$current_page = $this->router->fetch_method();
?>
<div class="diag-nav-pills no-print">
    <a href="lab/diagnosticDashboard" class="<?php echo ($current_page == 'diagnosticDashboard') ? 'active' : ''; ?>">
        <i class="fas fa-chart-line mr-1"></i> <?php echo lang('diagnostic_dashboard'); ?>
    </a>
    <a href="lab/dailyDiagnosticPerformance" class="<?php echo ($current_page == 'dailyDiagnosticPerformance') ? 'active' : ''; ?>">
        <i class="fas fa-calendar-day mr-1"></i> <?php echo lang('daily_diagnostic_performance'); ?>
    </a>
    <a href="lab/monthlyDiagnosticPerformance" class="<?php echo ($current_page == 'monthlyDiagnosticPerformance') ? 'active' : ''; ?>">
        <i class="fas fa-calendar-alt mr-1"></i> <?php echo lang('monthly_diagnostic_performance'); ?>
    </a>
    <a href="lab/yearlyDiagnosticPerformance" class="<?php echo ($current_page == 'yearlyDiagnosticPerformance') ? 'active' : ''; ?>">
        <i class="fas fa-calendar mr-1"></i> <?php echo lang('yearly_diagnostic_performance'); ?>
    </a>
    <a href="lab/testPerformanceReport" class="<?php echo ($current_page == 'testPerformanceReport') ? 'active' : ''; ?>">
        <i class="fas fa-vial mr-1"></i> <?php echo lang('test_performance_report'); ?>
    </a>
    <a href="lab/selectedTestItemReport" class="<?php echo ($current_page == 'selectedTestItemReport') ? 'active' : ''; ?>">
        <i class="fas fa-filter mr-1"></i> <?php echo lang('selected_test_item_report'); ?>
    </a>
    <a href="lab/allTestItemsSummaryReport" class="<?php echo ($current_page == 'allTestItemsSummaryReport') ? 'active' : ''; ?>">
        <i class="fas fa-list-ol mr-1"></i> <?php echo lang('all_test_items_report'); ?>
    </a>
    <a href="lab/categoryPerformanceReport" class="<?php echo ($current_page == 'categoryPerformanceReport') ? 'active' : ''; ?>">
        <i class="fas fa-tags mr-1"></i> <?php echo lang('category_performance_report'); ?>
    </a>
    <a href="lab/departmentPerformanceReport" class="<?php echo ($current_page == 'departmentPerformanceReport') ? 'active' : ''; ?>">
        <i class="fas fa-hospital mr-1"></i> <?php echo lang('department_performance_report'); ?>
    </a>
    <a href="lab/sampleCollectionReport" class="<?php echo ($current_page == 'sampleCollectionReport') ? 'active' : ''; ?>">
        <i class="fas fa-syringe mr-1"></i> <?php echo lang('sample_collection_report'); ?>
    </a>
    <a href="lab/pendingTestReport" class="<?php echo ($current_page == 'pendingTestReport') ? 'active' : ''; ?>">
        <i class="fas fa-hourglass-half mr-1"></i> <?php echo lang('pending_test_report'); ?>
    </a>
    <a href="lab/reportDeliveryReport" class="<?php echo ($current_page == 'reportDeliveryReport') ? 'active' : ''; ?>">
        <i class="fas fa-truck mr-1"></i> <?php echo lang('report_delivery_report'); ?>
    </a>
    <a href="lab/reportVerificationReport" class="<?php echo ($current_page == 'reportVerificationReport') ? 'active' : ''; ?>">
        <i class="fas fa-user-check mr-1"></i> <?php echo lang('report_verification_report'); ?>
    </a>
    <a href="lab/turnaroundTimeReport" class="<?php echo ($current_page == 'turnaroundTimeReport') ? 'active' : ''; ?>">
        <i class="fas fa-stopwatch mr-1"></i> <?php echo lang('turnaround_time_report'); ?>
    </a>
    <a href="lab/cancelledTestReport" class="<?php echo ($current_page == 'cancelledTestReport') ? 'active' : ''; ?>">
        <i class="fas fa-ban mr-1"></i> <?php echo lang('cancelled_test_report'); ?>
    </a>
    <a href="lab/doctorDiagnosticPerformance" class="<?php echo ($current_page == 'doctorDiagnosticPerformance') ? 'active' : ''; ?>">
        <i class="fas fa-user-md mr-1"></i> <?php echo lang('doctor_diagnostic_performance'); ?>
    </a>
    <a href="lab/diagnosticOperationalEfficiency" class="<?php echo ($current_page == 'diagnosticOperationalEfficiency') ? 'active' : ''; ?>">
        <i class="fas fa-tachometer-alt mr-1"></i> <?php echo lang('diagnostic_operational_efficiency'); ?>
    </a>
    <a href="lab/homeCollectionReport" class="<?php echo ($current_page == 'homeCollectionReport') ? 'active' : ''; ?>">
        <i class="fas fa-home mr-1"></i> <?php echo lang('home_collection_report'); ?>
    </a>
</div>
