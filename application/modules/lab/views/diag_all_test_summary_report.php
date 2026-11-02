<?php
defined('BASEPATH') or exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : 'BDT';
?>
<link href="common/extranal/css/lab/diagnostic_reports.css" rel="stylesheet">

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
.btn-view-details {
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 500;
    border-radius: 4px;
    color: #0284c7;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    transition: all 0.15s ease;
}
.btn-view-details:hover {
    background: #0284c7;
    color: #ffffff;
}
</style>

<div class="content-wrapper diag-report-wrapper">
    <div class="container-fluid">

        <?php $this->load->view('lab/inc_diagnostic_reports_nav'); ?>

        <!-- Printable Header (Print/PDF view) -->
        <div class="diag-print-header">
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e3a8a;">LIFECARE DIAGNOSTIC CENTER</h2>
            <p style="margin: 2px 0 0; font-size: 13px; color: #475569;">All Test Items Summary Report</p>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Period: <?php echo htmlspecialchars($period['label']); ?> | Generated on: <?php echo date('d M Y, h:i A'); ?></p>
            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #cbd5e1;">
        </div>

        <!-- Filter & Header Section -->
        <div class="diag-filter-panel no-print">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 12px;">
                <div class="diag-header-title">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a;">
                        <i class="fas fa-list-ol text-primary mr-1"></i> <?php echo lang('all_test_items_report'); ?>
                    </h3>
                    <p style="margin: 2px 0 0; font-size: 13px; color: #64748b;">
                        Aggregated volume summary of all diagnostic tests performed in the period, ranked from highest to lowest.
                    </p>
                </div>
                <div class="diag-export-wrapper">
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => 'diag_test_items_summary']); ?>
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

            <form method="GET" action="lab/allTestItemsSummaryReport" id="allTestsFilterForm">
                <input type="hidden" name="period_type" id="period_type" value="<?php echo htmlspecialchars($period['type']); ?>">
                
                <div class="diag-form-row">
                    <!-- From Date -->
                    <div style="flex: 1; min-width: 160px;">
                        <label style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" style="height: 38px;"
                               value="<?php echo !empty($period['date_from']) ? $period['date_from'] : date('Y-m-01'); ?>"
                               onchange="onDateManualChange()">
                    </div>

                    <!-- To Date -->
                    <div style="flex: 1; min-width: 160px;">
                        <label style="font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" style="height: 38px;"
                               value="<?php echo !empty($period['date_to']) ? $period['date_to'] : date('Y-m-d'); ?>"
                               onchange="onDateManualChange()">
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0 24px; font-weight: 500;">
                            <i class="fas fa-search mr-1"></i> Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary KPI Cards -->
        <div class="diag-kpi-grid">
            <div class="diag-kpi-card kpi-accent-emerald">
                <div class="kpi-label"><?php echo lang('total_performed'); ?></div>
                <div class="kpi-value" style="color: #059669;">
                    <?php echo number_format($summary['total_performed']); ?>
                </div>
                <div class="kpi-sub">Total test items completed</div>
            </div>
            <div class="diag-kpi-card kpi-accent-blue">
                <div class="kpi-label"><?php echo lang('distinct_tests'); ?></div>
                <div class="kpi-value">
                    <?php echo number_format($summary['distinct_tests']); ?>
                </div>
                <div class="kpi-sub">Unique test types performed</div>
            </div>
            <div class="diag-kpi-card kpi-accent-purple">
                <div class="kpi-label"><?php echo lang('top_performed_test'); ?></div>
                <div class="kpi-value" style="font-size: 18px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?php echo htmlspecialchars($summary['top_test']); ?>">
                    <?php echo htmlspecialchars($summary['top_test']); ?>
                </div>
                <div class="kpi-sub">Highest volume test</div>
            </div>
            <div class="diag-kpi-card kpi-accent-cyan">
                <div class="kpi-label">Reporting Period</div>
                <div class="kpi-value" style="font-size: 15px; font-weight: 600;">
                    <?php echo htmlspecialchars($period['label']); ?>
                </div>
                <div class="kpi-sub">Active period date filter</div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="diag-table-card">
            <div class="table-responsive">
                <table class="diag-table table table-striped table-hover mb-0" id="allTestsTable">
                    <thead>
                        <tr>
                            <th style="width: 10%; text-align: center;">SL</th>
                            <th style="width: 55%; text-align: left;">Test Name</th>
                            <th style="width: 20%; text-align: right;"><?php echo lang('total_performed'); ?></th>
                            <th style="width: 15%; text-align: center;" class="no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): ?>
                            <?php foreach ($records as $r): ?>
                                <tr>
                                    <td style="text-align: center; color: #64748b; font-weight: 500;">
                                        <?php echo htmlspecialchars($r->sl); ?>
                                    </td>
                                    <td style="text-align: left;">
                                        <span class="font-weight-bold" style="color: #0f172a;">
                                            <?php echo htmlspecialchars($r->test_name); ?>
                                        </span>
                                        <?php if (!empty($r->test_code)): ?>
                                            <span class="badge badge-light border ml-1 text-muted" style="font-size: 11px;">
                                                <?php echo htmlspecialchars($r->test_code); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: #0284c7; font-size: 14px;">
                                        <?php echo number_format($r->total_performed); ?>
                                    </td>
                                    <td style="text-align: center;" class="no-print">
                                        <button type="button" class="btn btn-view-details" 
                                                onclick="openTestDetailModal('<?php echo $r->test_id; ?>', '<?php echo addslashes($r->test_name); ?>')">
                                            <i class="fas fa-eye mr-1"></i> <?php echo lang('view_details'); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">
                                    <div class="diag-empty-state py-5 text-center">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
                                        <h5 class="text-secondary font-weight-bold">No Diagnostic Records Found</h5>
                                        <p class="text-muted" style="font-size: 13px;">
                                            No completed tests were recorded during the period <strong><?php echo htmlspecialchars($period['label']); ?></strong>.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                        <tfoot>
                            <tr style="background: #f8fafc; font-weight: bold; border-top: 2px solid #cbd5e1;">
                                <td style="text-align: center;">&bull;</td>
                                <td style="text-align: left; color: #0f172a; font-size: 15px;">Grand Total</td>
                                <td style="text-align: right; color: #059669; font-size: 16px;">
                                    <?php echo number_format($summary['total_performed']); ?>
                                </td>
                                <td class="no-print"></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Test Details Modal -->
<div class="modal fade" id="testDetailModal" tabindex="-1" role="dialog" aria-labelledby="testDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <h5 class="modal-title font-weight-bold" id="testDetailModalLabel" style="color: #0f172a;">
                    <i class="fas fa-vial text-primary mr-1"></i> <span id="modalTestName">Test Details</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge badge-primary px-3 py-2" id="modalPeriodLabel" style="font-size: 12px;"></span>
                    <span class="text-muted" style="font-size: 13px;" id="modalTotalPerformed"></span>
                </div>

                <div id="modalLoadingSpinner" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                    <p class="text-muted" style="font-size: 13px;">Loading date-wise breakdown...</p>
                </div>

                <div id="modalContentWrapper" style="display: none;">
                    <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 35%; text-align: center;">Date</th>
                                <th style="width: 40%; text-align: left;">Test Name</th>
                                <th style="width: 25%; text-align: right;"><?php echo lang('total_performed'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="modalTableBody">
                            <!-- Populated via AJAX -->
                        </tbody>
                        <tfoot>
                            <tr style="font-weight: bold; background: #f8fafc;">
                                <td style="text-align: center;">Total</td>
                                <td style="text-align: left;" id="modalFooterTestName"></td>
                                <td style="text-align: right; color: #059669;" id="modalFooterGrandTotal"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#allTestsFilterForm').on('submit', function(e) {
        var dateFrom = $('#date_from').val();
        var dateTo = $('#date_to').val();

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
        var dayOfWeek = today.getDay();
        var diff = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
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

    $('#allTestsFilterForm').submit();
}

function openTestDetailModal(testId, testName) {
    $('#modalTestName').text(testName + ' - Date-wise Breakdown');
    $('#modalPeriodLabel').text('Period: ' + <?php echo json_encode($period['label']); ?>);
    $('#modalLoadingSpinner').show();
    $('#modalContentWrapper').hide();
    $('#testDetailModal').modal('show');

    var periodType = $('#period_type').val();
    var dateFrom = $('#date_from').val();
    var dateTo = $('#date_to').val();

    $.ajax({
        url: 'lab/getTestItemBreakdownAjax',
        type: 'GET',
        dataType: 'json',
        data: {
            test_id: testId,
            period_type: periodType,
            date_from: dateFrom,
            date_to: dateTo
        },
        success: function(resp) {
            $('#modalLoadingSpinner').hide();
            $('#modalContentWrapper').show();

            var tbody = $('#modalTableBody');
            tbody.empty();

            if (resp && resp.records && resp.records.length > 0) {
                var total = 0;
                $.each(resp.records, function(idx, item) {
                    total += parseInt(item.total_performed);
                    var row = '<tr>' +
                        '<td style="text-align: center; font-weight: 500;">' + item.date_formatted + '</td>' +
                        '<td style="text-align: left;">' + item.test_name + '</td>' +
                        '<td style="text-align: right; font-weight: 600; color: #0284c7;">' + item.total_performed + '</td>' +
                        '</tr>';
                    tbody.append(row);
                });
                $('#modalFooterTestName').text(resp.test_name);
                $('#modalFooterGrandTotal').text(total);
                $('#modalTotalPerformed').html('<strong>Grand Total:</strong> ' + total + ' performed');
            } else {
                tbody.append('<tr><td colspan="3" class="text-center text-muted py-3">No test records found for this date range.</td></tr>');
                $('#modalFooterTestName').text(resp.test_name);
                $('#modalFooterGrandTotal').text('0');
                $('#modalTotalPerformed').text('0 performed');
            }
        },
        error: function() {
            $('#modalLoadingSpinner').hide();
            $('#modalContentWrapper').show();
            $('#modalTableBody').html('<tr><td colspan="3" class="text-center text-danger py-3">Failed to load test details. Please try again.</td></tr>');
        }
    });
}
</script>
