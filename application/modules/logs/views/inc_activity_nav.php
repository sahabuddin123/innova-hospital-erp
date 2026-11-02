<?php
$current_report = isset($active_report) ? $active_report : 'activity_dashboard';
?>
<link rel="stylesheet" href="common/extranal/css/logs/activity_reports.css">

<div class="act-nav-bar">
    <a href="logs/activityDashboard" class="act-nav-btn <?php echo $current_report === 'activity_dashboard' ? 'active' : ''; ?>">
        <i class="fas fa-chart-line"></i> <?php echo lang('activity_dashboard'); ?>
    </a>
    <a href="logs/userLoginHistory" class="act-nav-btn <?php echo $current_report === 'login_history' ? 'active' : ''; ?>">
        <i class="fas fa-sign-in-alt"></i> <?php echo lang('user_login_history'); ?>
    </a>
    <a href="logs/userLogoutHistory" class="act-nav-btn <?php echo $current_report === 'logout_history' ? 'active' : ''; ?>">
        <i class="fas fa-sign-out-alt"></i> <?php echo lang('user_logout_history'); ?>
    </a>
    <a href="logs/failedLogins" class="act-nav-btn <?php echo $current_report === 'failed_logins' ? 'active' : ''; ?>">
        <i class="fas fa-user-times"></i> <?php echo lang('failed_login_attempts'); ?>
    </a>
    <a href="logs/userActivityReport" class="act-nav-btn <?php echo $current_report === 'user_activity' ? 'active' : ''; ?>">
        <i class="fas fa-history"></i> <?php echo lang('user_activity_reports'); ?>
    </a>
    <a href="logs/moduleAccessReport" class="act-nav-btn <?php echo $current_report === 'module_access' ? 'active' : ''; ?>">
        <i class="fas fa-th-large"></i> <?php echo lang('module_access_reports'); ?>
    </a>
    <a href="logs/dataEntryActivity" class="act-nav-btn <?php echo $current_report === 'data_entry' ? 'active' : ''; ?>">
        <i class="fas fa-edit"></i> <?php echo lang('data_entry_activity'); ?>
    </a>
    <a href="logs/userSessionReport" class="act-nav-btn <?php echo $current_report === 'session_report' ? 'active' : ''; ?>">
        <i class="fas fa-user-clock"></i> <?php echo lang('user_session_history'); ?>
    </a>
    <a href="logs/printExportActivity" class="act-nav-btn <?php echo $current_report === 'print_export' ? 'active' : ''; ?>">
        <i class="fas fa-print"></i> <?php echo lang('print_export_activity'); ?>
    </a>
    <a href="logs/suspiciousActivity" class="act-nav-btn suspicious-tab <?php echo $current_report === 'suspicious_activity' ? 'active' : ''; ?>">
        <i class="fas fa-shield-virus"></i> <?php echo lang('suspicious_activity_monitoring'); ?>
    </a>
</div>
