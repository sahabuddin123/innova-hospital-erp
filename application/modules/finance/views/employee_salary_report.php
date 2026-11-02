<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$selected_staff = !empty($selected_staff) ? $selected_staff : '';
$selected_month = !empty($month) ? $month : 'all';
?>
<link href="common/extranal/css/finance/expense_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_expense_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Employee Salary & Payroll Disbursement Report &bull; <?php echo ($selected_month !== 'all') ? $selected_month . ' ' . $year : 'Year ' . $year; ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-user-tie text-purple" style="color:#7c3aed;"></i> Employee Salary & Payroll Report</h2>
                    <p>Staff monthly salary statements, gross earnings, statutory deductions, and disbursement records &bull; <span class="font-weight-bold text-dark">Year <?php echo html_escape($year); ?></span></p>
                </div>
                <div class="rep-actions">
                    <a href="payroll" class="rep-action-btn text-primary font-weight-bold"><i class="fas fa-money-check-alt"></i> Payroll Module</a>
                    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'employee_salary']); ?>
                </div>
            </div>

            <form method="GET" action="finance/employeeSalaryReport" class="m-0">
                <div class="rep-controls-row">
                    <div class="rep-filter-group">
                        <span class="text-muted font-weight-bold" style="font-size: 12px;">YEAR:</span>
                        <select name="year" class="rep-select" onchange="this.form.submit()">
                            <?php for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?php echo $y; ?>" <?php echo ($year == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>

                        <span class="text-muted font-weight-bold" style="font-size: 12px;">MONTH:</span>
                        <select name="month" class="rep-select" onchange="this.form.submit()">
                            <option value="all">-- All Months --</option>
                            <?php foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m_name): ?>
                                <option value="<?php echo $m_name; ?>" <?php echo ($selected_month === $m_name) ? 'selected' : ''; ?>><?php echo $m_name; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Staff Filter -->
                        <select name="staff_id" class="rep-select" onchange="this.form.submit()">
                            <option value="">-- All Staff Members --</option>
                            <?php if (!empty($staff_list)): foreach ($staff_list as $st): ?>
                                <option value="<?php echo $st->staff; ?>" <?php echo ($selected_staff == $st->staff) ? 'selected' : ''; ?>>
                                    <?php echo html_escape(!empty($st->username) ? $st->username : 'Staff #' . $st->staff); ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Apply</button>
                        <a href="finance/employeeSalaryReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card purple">
                <span class="rep-kpi-label"><i class="fas fa-money-check text-purple"></i> Total Net Payroll</span>
                <span class="rep-kpi-val" style="color:#7c3aed;"><?php echo $currency . number_format($summary['total_net'], 2); ?></span>
                <span class="rep-kpi-sub">Gross: <?php echo $currency . number_format($summary['total_gross'], 2); ?></span>
            </div>
            <div class="rep-kpi-card success">
                <span class="rep-kpi-label"><i class="fas fa-check-circle text-success"></i> Disbursed / Paid</span>
                <span class="rep-kpi-val text-success"><?php echo $currency . number_format($summary['total_paid'], 2); ?></span>
                <span class="rep-kpi-sub">Successfully paid to staff</span>
            </div>
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-clock text-danger"></i> Pending Payable</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['total_pending'], 2); ?></span>
                <span class="rep-kpi-sub">Generated salaries awaiting disbursement</span>
            </div>
            <div class="rep-kpi-card info">
                <span class="rep-kpi-label"><i class="fas fa-cut text-info"></i> Deductions Total</span>
                <span class="rep-kpi-val text-info"><?php echo $currency . number_format($summary['total_deductions'], 2); ?></span>
                <span class="rep-kpi-sub">Advance/tax deductions</span>
            </div>
        </div>

        <!-- Salary Records Table Card -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="salaryReportTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Staff Name</th>
                            <th>Staff ID</th>
                            <th>Salary Month</th>
                            <th class="text-right">Gross (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Deductions (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Net Payable (<?php echo trim($currency); ?>)</th>
                            <th class="text-center">Status</th>
                            <th>Disbursed Date</th>
                            <th>Expense Ref</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            foreach ($records as $r): 
                                $is_paid = (strcasecmp($r->status, 'Paid') === 0);
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td class="font-weight-bold text-dark">
                                    <i class="fas fa-user-circle text-primary mr-1"></i> <?php echo html_escape($r->staff_name); ?>
                                </td>
                                <td><span class="badge badge-light border">#<?php echo html_escape($r->staff); ?></span></td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($r->month . ' ' . $r->year); ?></td>
                                <td class="text-right"><?php echo number_format($r->gross_salary, 2); ?></td>
                                <td class="text-right text-muted"><?php echo number_format($r->deductions, 2); ?></td>
                                <td class="text-right font-weight-bold" style="font-size: 14px; color:#7c3aed;">
                                    <?php echo number_format($r->net_salary, 2); ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($is_paid): ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Paid</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock"></i> Generated</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo !empty($r->paid_on) ? html_escape($r->paid_on) : '-'; ?></td>
                                <td>
                                    <?php if (!empty($r->expense)): ?>
                                        <a href="finance/expenseInvoice?id=<?php echo $r->expense; ?>" target="_blank" class="badge badge-light border text-primary">
                                            EXP-<?php echo $r->expense; ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="fas fa-user-slash text-muted fa-3x mb-2 d-block"></i>
                                    <strong>No payroll or salary disbursement records found for this period.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="4" class="text-right font-weight-bold">Grand Total (<?php echo count($records); ?> staff records):</td>
                            <td class="text-right font-weight-bold"><?php echo number_format($summary['total_gross'], 2); ?></td>
                            <td class="text-right font-weight-bold text-muted"><?php echo number_format($summary['total_deductions'], 2); ?></td>
                            <td class="text-right font-weight-bold" style="color:#7c3aed;"><?php echo number_format($summary['total_net'], 2); ?></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Print Signatures -->
        <div class="d-none d-print-block mt-5 pt-4">
            <div class="row text-center">
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>HR / Payroll Officer</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Accounts Head</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Managing Director</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable && $('#salaryReportTable tbody tr td').length > 1) {
        $('#salaryReportTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 25,
            order: [[3, 'desc'], [1, 'asc']]
        });
    }
});
</script>

