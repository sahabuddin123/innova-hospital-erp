<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳ ';
$selected_aging = !empty($selected_aging) ? $selected_aging : 'all';
?>
<link href="common/extranal/css/finance/collection_reports.css" rel="stylesheet">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <!-- Navigation Submenu Bar -->
        <?php $this->load->view('finance/inc_reports_nav', ['active_report' => $active_report]); ?>

        <!-- Printable Header -->
        <div class="d-none d-print-block text-center mb-4">
            <h2 class="font-weight-bold mb-1">Lifecare Diagnostic Center</h2>
            <p class="text-muted mb-0">Patient Outstanding Due & Aging Report &bull; Filter: <?php echo html_escape(ucwords(str_replace('_', ' ', $selected_aging))); ?></p>
            <small class="text-muted">Generated: <?php echo date('d/m/Y h:i A'); ?> | By: <?php echo $this->ion_auth->user()->row()->username; ?></small>
            <hr class="my-2">
        </div>

        <!-- Header & Filter Card -->
        <div class="rep-header-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="rep-title">
                    <h2><i class="fas fa-exclamation-circle text-danger"></i> Patient Outstanding Due & Aging Report</h2>
                    <p>Unsettled diagnostic invoice receivables categorized by aging intervals &bull; <span class="badge badge-warning text-dark px-2 py-1"><?php echo number_format($summary['total_invoices']); ?> Invoices Due</span></p>
                </div>
                <div class="rep-actions">
                    <button type="button" class="rep-action-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <form method="GET" action="finance/dueReport" class="m-0">
                <input type="hidden" name="aging_bucket" id="aging_bucket_input" value="<?php echo html_escape($selected_aging); ?>">
                
                <div class="rep-controls-row">
                    <!-- Aging Bucket Selector Pills -->
                    <div class="rep-period-pills">
                        <button type="button" class="rep-period-btn <?php echo ($selected_aging === 'all') ? 'active' : ''; ?>" data-aging="all">All Dues</button>
                        <button type="button" class="rep-period-btn <?php echo ($selected_aging === '0-15 Days') ? 'active' : ''; ?>" data-aging="0-15 Days">0–15 Days</button>
                        <button type="button" class="rep-period-btn <?php echo ($selected_aging === '16-30 Days') ? 'active' : ''; ?>" data-aging="16-30 Days">16–30 Days</button>
                        <button type="button" class="rep-period-btn <?php echo ($selected_aging === '31-60 Days') ? 'active' : ''; ?>" data-aging="31-60 Days">31–60 Days</button>
                        <button type="button" class="rep-period-btn <?php echo ($selected_aging === '61+ Days') ? 'active' : ''; ?>" data-aging="61+ Days">61+ Days</button>
                    </div>

                    <!-- Date & Patient Filter Group -->
                    <div class="rep-filter-group">
                        <div class="rep-input-wrap">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">INV FROM:</span>
                            <input type="date" name="date_from" id="date_from" class="rep-date-input" value="<?php echo html_escape($date_from ?? ''); ?>">
                            <span class="text-muted" style="font-size: 11px; font-weight:700;">TO:</span>
                            <input type="date" name="date_to" id="date_to" class="rep-date-input" value="<?php echo html_escape($date_to ?? ''); ?>">
                        </div>

                        <button type="submit" class="rep-filter-submit"><i class="fas fa-filter"></i> Apply Filter</button>
                        <a href="finance/dueReport" class="rep-btn-clear"><i class="fas fa-redo"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- KPI Summary Cards -->
        <div class="rep-kpi-grid">
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-money-bill-wave text-danger"></i> Total Outstanding Due</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['total_due'], 2); ?></span>
                <span class="rep-kpi-sub">Across <?php echo number_format($summary['total_invoices']); ?> unpaid invoices</span>
            </div>
            <div class="rep-kpi-card primary">
                <span class="rep-kpi-label"><i class="fas fa-clock text-info"></i> Current (0–15 Days)</span>
                <span class="rep-kpi-val"><?php echo $currency . number_format($summary['bucket_0_15'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo ($summary['total_due'] > 0) ? round(($summary['bucket_0_15'] / $summary['total_due']) * 100, 1) : 0; ?>% of total outstanding</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-hourglass-half text-warning"></i> Due (16–30 Days)</span>
                <span class="rep-kpi-val text-warning"><?php echo $currency . number_format($summary['bucket_16_30'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo ($summary['total_due'] > 0) ? round(($summary['bucket_16_30'] / $summary['total_due']) * 100, 1) : 0; ?>% of total outstanding</span>
            </div>
            <div class="rep-kpi-card warning">
                <span class="rep-kpi-label"><i class="fas fa-calendar-times text-orange" style="color:#fd7e14;"></i> Overdue (31–60 Days)</span>
                <span class="rep-kpi-val" style="color:#fd7e14;"><?php echo $currency . number_format($summary['bucket_31_60'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo ($summary['total_due'] > 0) ? round(($summary['bucket_31_60'] / $summary['total_due']) * 100, 1) : 0; ?>% of total outstanding</span>
            </div>
            <div class="rep-kpi-card danger">
                <span class="rep-kpi-label"><i class="fas fa-exclamation-triangle text-danger"></i> High Risk (61+ Days)</span>
                <span class="rep-kpi-val text-danger"><?php echo $currency . number_format($summary['bucket_61_plus'], 2); ?></span>
                <span class="rep-kpi-sub"><?php echo ($summary['total_due'] > 0) ? round(($summary['bucket_61_plus'] / $summary['total_due']) * 100, 1) : 0; ?>% critical recovery</span>
            </div>
        </div>

        <!-- Due Records Table Card -->
        <div class="rep-table-card">
            <div class="table-responsive">
                <table class="table table-hover rep-table" id="dueReportTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Inv ID</th>
                            <th>Invoice Date</th>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th>Phone</th>
                            <th class="text-right">Gross (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Discount (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Net Payable (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Paid (<?php echo trim($currency); ?>)</th>
                            <th class="text-right">Due (<?php echo trim($currency); ?>)</th>
                            <th class="text-center">Age (Days)</th>
                            <th class="text-center">Aging Bracket</th>
                            <th class="text-center d-print-none">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): 
                            $sl = 1;
                            foreach ($records as $r): 
                                $pt_name = !empty($r->pt_name) ? $r->pt_name : (!empty($r->patient_name) ? $r->patient_name : 'Unknown');
                                $pt_code = !empty($r->pt_code) ? $r->pt_code : (!empty($r->patient) ? 'P-'.$r->patient : '-');
                                $phone = !empty($r->pt_phone) ? $r->pt_phone : '-';

                                // Badge class by aging
                                $badge_class = 'badge-success';
                                if ($r->age_days > 60) {
                                    $badge_class = 'badge-danger';
                                } elseif ($r->age_days > 30) {
                                    $badge_class = 'badge-warning text-dark';
                                } elseif ($r->age_days > 15) {
                                    $badge_class = 'badge-info';
                                }
                        ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td class="font-weight-bold">
                                    <a href="finance/invoice?id=<?php echo $r->id; ?>" target="_blank" class="text-primary font-weight-bold">
                                        #<?php echo $r->id; ?>
                                    </a>
                                </td>
                                <td><?php echo date('d/m/Y', $r->date); ?></td>
                                <td><span class="badge badge-light border"><?php echo html_escape($pt_code); ?></span></td>
                                <td class="font-weight-bold text-dark"><?php echo html_escape($pt_name); ?></td>
                                <td><?php echo html_escape($phone); ?></td>
                                <td class="text-right"><?php echo number_format($r->amount, 2); ?></td>
                                <td class="text-right text-muted"><?php echo number_format($r->calculated_discount, 2); ?></td>
                                <td class="text-right font-weight-bold"><?php echo number_format($r->gross_total, 2); ?></td>
                                <td class="text-right text-success font-weight-bold"><?php echo number_format($r->amount_received, 2); ?></td>
                                <td class="text-right text-danger font-weight-bold" style="font-size: 14px;"><?php echo number_format($r->due_amount, 2); ?></td>
                                <td class="text-center font-weight-bold"><?php echo $r->age_days; ?> d</td>
                                <td class="text-center">
                                    <span class="badge <?php echo $badge_class; ?> px-2 py-1"><?php echo html_escape($r->aging_bucket); ?></span>
                                </td>
                                <td class="text-center d-print-none">
                                    <a href="finance/duePayment?id=<?php echo $r->id; ?>" class="btn btn-xs btn-outline-danger font-weight-bold" title="Collect Due">
                                        <i class="fas fa-hand-holding-usd"></i> Collect
                                    </a>
                                    <a href="finance/invoice?id=<?php echo $r->id; ?>" target="_blank" class="btn btn-xs btn-outline-primary" title="View Invoice">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="14" class="text-center text-muted py-5">
                                    <i class="fas fa-check-circle text-success fa-3x mb-2 d-block"></i>
                                    <strong>No outstanding dues found matching this filter criteria.</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($records)): ?>
                    <tfoot>
                        <tr class="rep-total-row">
                            <td colspan="6" class="text-right font-weight-bold">Grand Total (<?php echo count($records); ?> invoices):</td>
                            <td class="text-right font-weight-bold"><?php echo number_format($summary['total_gross'], 2); ?></td>
                            <td class="text-right font-weight-bold text-muted">-</td>
                            <td class="text-right font-weight-bold">-</td>
                            <td class="text-right font-weight-bold text-success"><?php echo number_format($summary['total_paid'], 2); ?></td>
                            <td class="text-right font-weight-bold text-danger"><?php echo number_format($summary['total_due'], 2); ?></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Print Footer Signatures -->
        <div class="d-none d-print-block mt-5 pt-4">
            <div class="row text-center">
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Accounts Officer</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Audit Officer</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div style="border-top: 1px dashed #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                        <strong>Authorized Director</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    // Aging button pills click
    $('.rep-period-btn').on('click', function() {
        var aging = $(this).data('aging');
        $('#aging_bucket_input').val(aging);
        $(this).closest('form').submit();
    });

    if ($.fn.DataTable && $('#dueReportTable tbody tr td').length > 1) {
        $('#dueReportTable').DataTable({
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[10, 'desc']], // Order by Due amount descending
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search patient, phone, inv..."
            }
        });
    }
});
</script>

