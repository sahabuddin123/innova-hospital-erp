<?php
defined('BASEPATH') or exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : 'BDT';
?>
<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">
<link href="common/assets/select2/css/select2.min.css" rel="stylesheet" />

<style>
.diag-quick-filter-btn {
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 16px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}
.diag-quick-filter-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.diag-quick-filter-btn.active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
    box-shadow: 0 1px 3px rgba(2, 132, 199, 0.3);
}
.diag-filter-panel {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.diag-form-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 15px;
}
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 5px 8px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}
</style>

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <!-- Printable Header (Print/PDF view) -->
        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">Selected Test Item Report &bull; <?php echo htmlspecialchars($test_name); ?></p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <!-- Filter & Header Section -->
        <div class="diag-filter-panel no-print">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 12px;">
                <div class="diag-header-title">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a;">
                        <i class="fas fa-filter text-primary mr-1"></i> <?php echo lang('selected_test_item_report'); ?>
                    </h3>
                    <p style="margin: 2px 0 0; font-size: 13px; color: #64748b;">
                        Daily chronological count of completed diagnostic tests for selected test item.
                    </p>
                </div>
                <div class="diag-export-wrapper">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => 'diag_test_item_selected']); ?>
                </div>
            </div>

            <!-- Quick Date Filter Buttons -->
            <div class="d-flex flex-wrap align-items-center mb-3" style="gap: 6px;">
                <span style="font-size: 12px; font-weight: 600; color: #64748b; margin-right: 4px;">Quick Range:</span>
                <button type="button" class="diag-quick-filter-btn <?php echo ($period['type'] == 'today') ? 'active' : ''; ?>" onclick="applyQuickFilter('today')">Today</button>
                <button type="button" class="diag-quick-filter-btn <?php echo ($period['type'] == 'this_week') ? 'active' : ''; ?>" onclick="applyQuickFilter('this_week')">This Week</button>
                <button type="button" class="diag-quick-filter-btn <?php echo ($period['type'] == 'this_month') ? 'active' : ''; ?>" onclick="applyQuickFilter('this_month')">This Month</button>
                <button type="button" class="diag-quick-filter-btn <?php echo ($period['type'] == 'last_month') ? 'active' : ''; ?>" onclick="applyQuickFilter('last_month')">Last Month</button>
                <button type="button" class="diag-quick-filter-btn <?php echo ($period['type'] == 'this_year') ? 'active' : ''; ?>" onclick="applyQuickFilter('this_year')">This Year</button>
                <button type="button" class="diag-quick-filter-btn <?php echo ($period['type'] == 'last_year') ? 'active' : ''; ?>" onclick="applyQuickFilter('last_year')">Last Year</button>
                <button type="button" class="diag-quick-filter-btn <?php echo ($period['type'] == 'custom') ? 'active' : ''; ?>" onclick="setCustomRange()">Custom Date Range</button>
            </div>

            <?php if (!empty($validation_error)): ?>
                <div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 13px;">
                    <i class="fas fa-exclamation-triangle mr-1"></i> <?php echo htmlspecialchars($validation_error); ?>
                </div>
            <?php endif; ?>

            <form method="GET" action="lab/selectedTestItemReport" id="selectedTestFilterForm">
                <input type="hidden" name="period_type" id="period_type" value="<?php echo htmlspecialchars($period['type']); ?>">
                
                <div class="diag-form-row">
                    <!-- Test Item Searchable Dropdown -->
                    <div style="flex: 2; min-width: 250px;">
                        <label style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                            <?php echo lang('select_test_item'); ?> <span class="text-danger">*</span>
                        </label>
                        <select name="test_id" id="test_id_select" class="form-control select2" required style="width: 100%;">
                            <option value="">-- Choose Diagnostic Test --</option>
                            <?php foreach ($active_tests as $at): ?>
                                <option value="<?php echo $at->id; ?>" <?php echo ($selected_test_id == $at->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($at->name); ?> <?php echo !empty($at->code) ? '(' . htmlspecialchars($at->code) . ')' : ''; ?> - <?php echo $currency . ' ' . number_format($at->c_price, 2); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- From Date -->
                    <div style="flex: 1; min-width: 140px;">
                        <label style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" style="height: 38px;"
                               value="<?php echo !empty($period['date_from']) ? $period['date_from'] : date('Y-m-01'); ?>"
                               onchange="onDateManualChange()">
                    </div>

                    <!-- To Date -->
                    <div style="flex: 1; min-width: 140px;">
                        <label style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" style="height: 38px;"
                               value="<?php echo !empty($period['date_to']) ? $period['date_to'] : date('Y-m-d'); ?>"
                               onchange="onDateManualChange()">
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0 20px; font-weight: 500;">
                            <i class="fas fa-search mr-1"></i> Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary KPI Cards -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label">Selected Test</div>
                <div class="kpi-value" style="font-size: 20px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?php echo htmlspecialchars($test_name); ?>">
                    <?php echo htmlspecialchars($test_name ?: 'None Selected'); ?>
                </div>
                <div class="kpi-sub">
                    Rate: <?php echo (!empty($test_info->c_price)) ? $currency . ' ' . number_format($test_info->c_price, 2) : 'N/A'; ?>
                </div>
            </div>
            <div class="diag-kpi-card kpi-accent-cyan">
                <div class="kpi-label">Selected Period</div>
                <div class="kpi-value" style="font-size: 16px; font-weight: 600;">
                    <?php echo htmlspecialchars($period['label']); ?>
                </div>
                <div class="kpi-sub">
                    From <?php echo !empty($period['date_from']) ? date('d-m-Y', strtotime($period['date_from'])) : ''; ?> to <?php echo !empty($period['date_to']) ? date('d-m-Y', strtotime($period['date_to'])) : ''; ?>
                </div>
            </div>
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label"><?php echo lang('total_performed'); ?></div>
                <div class="kpi-value" style="color: #059669;">
                    <?php echo number_format($summary['total_performed']); ?>
                </div>
                <div class="kpi-sub">Completed diagnostic tests</div>
            </div>
            <div class="diag-kpi-card kpi-accent-purple">
                <div class="kpi-label"><?php echo lang('active_days'); ?></div>
                <div class="kpi-value">
                    <?php echo number_format($summary['active_days']); ?>
                </div>
                <div class="kpi-sub">Days with test activity</div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="diag-table-card">
            <div class="table-responsive">
                <table class="diag-table table table-striped table-hover mb-0" id="selectedTestTable">
                    <thead>
                        <tr>
                            <th style="width: 25%; text-align: center;">Date</th>
                            <th style="width: 45%; text-align: left;">Test Name</th>
                            <th style="width: 30%; text-align: right;"><?php echo lang('total_performed'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): ?>
                            <?php foreach ($records as $r): ?>
                                <tr>
                                    <td style="text-align: center; font-weight: 500;">
                                        <?php echo htmlspecialchars($r->date_formatted); ?>
                                    </td>
                                    <td style="text-align: left;">
                                        <span class="badge badge-light border font-weight-normal mr-2" style="font-size: 12px;">
                                            <i class="fas fa-vial text-info mr-1"></i> <?php echo htmlspecialchars($r->test_name); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right; font-weight: 600; color: #0284c7;">
                                        <?php echo number_format($r->total_performed); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">
                                    <div class="diag-empty-state py-5 text-center">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
                                        <h5 class="text-secondary font-weight-bold">No Diagnostic Test Records Found</h5>
                                        <p class="text-muted" style="font-size: 13px;">
                                            No completed tests were recorded for <strong><?php echo htmlspecialchars($test_name); ?></strong> during the period <strong><?php echo htmlspecialchars($period['label']); ?></strong>.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                        <tfoot>
                            <tr style="background: #f8fafc; font-weight: bold; border-top: 2px solid #cbd5e1;">
                                <td style="text-align: center; color: #0f172a;">Total</td>
                                <td style="text-align: left; color: #0f172a;"><?php echo htmlspecialchars($test_name); ?></td>
                                <td style="text-align: right; color: #059669; font-size: 16px;">
                                    <?php echo number_format($summary['total_performed']); ?>
                                </td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="common/assets/select2/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 on the test item selector
    $('#test_id_select').select2({
        placeholder: "Search test by name or code...",
        allowClear: false
    });

    // Form validation before submit
    $('#selectedTestFilterForm').on('submit', function(e) {
        var testId = $('#test_id_select').val();
        var dateFrom = $('#date_from').val();
        var dateTo = $('#date_to').val();

        if (!testId) {
            alert('Please select a diagnostic test item.');
            e.preventDefault();
            return false;
        }

        if (dateFrom && dateTo && dateFrom > dateTo) {
            alert('From Date cannot be later than To Date. Please select a valid date range.');
            e.preventDefault();
            return false;
        }
    });
});

function onDateManualChange() {
    $('#period_type').val('custom');
    $('.diag-quick-filter-btn').removeClass('active');
}

function setCustomRange() {
    $('#period_type').val('custom');
    $('.diag-quick-filter-btn').removeClass('active');
    event.target.classList.add('active');
    $('#date_from').focus();
}

function applyQuickFilter(type) {
    $('#period_type').val(type);
    $('.diag-quick-filter-btn').removeClass('active');
    
    var today = new Date();
    var fromDate, toDate;

    function formatDate(d) {
        var month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [year, month, day].join('-');
    }

    if (type === 'today') {
        fromDate = formatDate(today);
        toDate = formatDate(today);
    } else if (type === 'this_week') {
        var firstDayOfWeek = new Date(today);
        var dayOfWeek = today.getDay(); // 0 is Sunday
        var diff = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1); // Monday
        firstDayOfWeek.setDate(diff);
        var lastDayOfWeek = new Date(firstDayOfWeek);
        lastDayOfWeek.setDate(firstDayOfWeek.getDate() + 6);
        fromDate = formatDate(firstDayOfWeek);
        toDate = formatDate(lastDayOfWeek);
    } else if (type === 'this_month') {
        var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        var lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        fromDate = formatDate(firstDay);
        toDate = formatDate(lastDay);
    } else if (type === 'last_month') {
        var firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        var lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
        fromDate = formatDate(firstDay);
        toDate = formatDate(lastDay);
    } else if (type === 'this_year') {
        var firstDay = new Date(today.getFullYear(), 0, 1);
        var lastDay = new Date(today.getFullYear(), 11, 31);
        fromDate = formatDate(firstDay);
        toDate = formatDate(lastDay);
    } else if (type === 'last_year') {
        var firstDay = new Date(today.getFullYear() - 1, 0, 1);
        var lastDay = new Date(today.getFullYear() - 1, 11, 31);
        fromDate = formatDate(firstDay);
        toDate = formatDate(lastDay);
    }

    if (fromDate && toDate) {
        $('#date_from').val(fromDate);
        $('#date_to').val(toDate);
    }

    // Submit form automatically on quick filter click if test is selected
    if ($('#test_id_select').val()) {
        $('#selectedTestFilterForm').submit();
    }
}
</script>
