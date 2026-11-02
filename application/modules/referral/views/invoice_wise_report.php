<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳';
?>
<link rel="stylesheet" href="common/extranal/css/referral/referral_reports.css">

<div class="content-wrapper">
    <div class="rep-wrapper">

        <?php $this->load->view('referral/inc_referral_reports_nav'); ?>

        <!-- Header Card -->
        <div class="rep-header-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 16px;">
                <div class="rep-title">
                    <h2><i class="fas fa-file-invoice text-primary"></i> Invoice-Wise Referral Commission Report</h2>
                    <p>Granular invoice-level audit tracking hospital share recovery, referral commission entitlement, and collection backing &bull; <strong><?php echo $period['period_label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <button type="button" onclick="window.print()" class="rep-action-btn"><i class="fas fa-print"></i> Print Report</button>
                    <a href="referral/discountReconciliationReport" class="rep-action-btn"><i class="fas fa-percent"></i> Discount Absorption</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="referral/invoiceWiseReport?period_type=today<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="referral/invoiceWiseReport?period_type=yesterday<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="referral/invoiceWiseReport?period_type=this_week<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="referral/invoiceWiseReport?period_type=this_month<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="referral/invoiceWiseReport?period_type=last_month<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="referral/invoiceWiseReport?period_type=this_year<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'this_year') ? 'active' : ''; ?>">This Year</a>
            </div>

            <!-- Custom Filter Form -->
            <form action="referral/invoiceWiseReport" method="get" class="row g-3 align-items-end mt-2 d-print-none">
                <input type="hidden" name="period_type" value="custom">
                <div class="col-md-3">
                    <label class="form-label small text-muted font-weight-bold">Referrer</label>
                    <select name="referrer_id" class="form-control form-control-sm select2">
                        <option value="">-- All Referrers --</option>
                        <?php foreach ($referrers as $r) { ?>
                            <option value="<?php echo $r->id; ?>" <?php echo (!empty($selected_referrer) && $selected_referrer == $r->id) ? 'selected' : ''; ?>>
                                <?php echo html_escape($r->name); ?> (<?php echo html_escape($r->type); ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted font-weight-bold">Allocation Status</label>
                    <select name="allocation_status" class="form-control form-control-sm">
                        <option value="">-- All Statuses --</option>
                        <option value="Fully Allocated" <?php echo (!empty($selected_status) && $selected_status === 'Fully Allocated') ? 'selected' : ''; ?>>Fully Allocated</option>
                        <option value="Partial Collection" <?php echo (!empty($selected_status) && $selected_status === 'Partial Collection') ? 'selected' : ''; ?>>Partial Collection</option>
                        <option value="Hospital Share Incomplete" <?php echo (!empty($selected_status) && $selected_status === 'Hospital Share Incomplete') ? 'selected' : ''; ?>>Hospital Share Incomplete</option>
                        <option value="Zero Collection" <?php echo (!empty($selected_status) && $selected_status === 'Zero Collection') ? 'selected' : ''; ?>>Zero Collection (Unpaid)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted font-weight-bold">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo html_escape($period['date_from']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted font-weight-bold">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo html_escape($period['date_to']); ?>">
                </div>
                <div class="col-md-3 d-flex" style="gap: 8px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-filter"></i> Filter</button>
                    <a href="referral/invoiceWiseReport" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>

        <!-- 8 KPI Cards Grid -->
        <div class="rep-kpi-grid">
            <div class="rep-card">
                <div class="rep-card-icon bg-blue"><i class="fas fa-receipt"></i></div>
                <div class="rep-card-label">Referred Invoices</div>
                <div class="rep-card-val"><?php echo number_format($summary['total_invoices']); ?></div>
                <div class="rep-card-sub text-muted">Audited transactions</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-purple"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="rep-card-label">Gross Billing</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['total_gross_amount'], 2); ?></div>
                <div class="rep-card-sub text-muted">Total test list prices</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-cyan"><i class="fas fa-hospital-user"></i></div>
                <div class="rep-card-label">Hospital Share Req.</div>
                <div class="rep-card-val text-info"><?php echo $currency . ' ' . number_format($summary['total_hospital_share_required'], 2); ?></div>
                <div class="rep-card-sub text-info">Protected baseline revenue</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-orange"><i class="fas fa-gift"></i></div>
                <div class="rep-card-label">Referral Entitlement</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['total_referral_entitlement'], 2); ?></div>
                <div class="rep-card-sub text-muted">Calculated commission</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-green"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="rep-card-label">Amount Collected</div>
                <div class="rep-card-val text-success"><?php echo $currency . ' ' . number_format($summary['total_amount_collected'], 2); ?></div>
                <div class="rep-card-sub text-success">Total cash deposits</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-teal"><i class="fas fa-shield-alt"></i></div>
                <div class="rep-card-label">Hospital Share Rec.</div>
                <div class="rep-card-val text-info"><?php echo $currency . ' ' . number_format($summary['total_hospital_share_recovered'], 2); ?></div>
                <div class="rep-card-sub text-info">Recovered 100% first</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-indigo"><i class="fas fa-check-circle"></i></div>
                <div class="rep-card-label">Referral Eligible</div>
                <div class="rep-card-val text-primary"><?php echo $currency . ' ' . number_format($summary['total_referral_eligible'], 2); ?></div>
                <div class="rep-card-sub text-primary">Surplus above hosp share</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-gray"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="rep-card-label">Outstanding Due</div>
                <div class="rep-card-val text-danger"><?php echo $currency . ' ' . number_format($summary['total_remaining_due'], 2); ?></div>
                <div class="rep-card-sub text-danger">Uncollected patient balances</div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="rep-table-card mt-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle rep-table" id="invoice-wise-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 35px;">#</th>
                            <th>Date</th>
                            <th>Invoice #</th>
                            <th>Patient Info</th>
                            <th>Referrer Name</th>
                            <th class="text-right">Gross</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Net</th>
                            <th class="text-right">Hosp Share</th>
                            <th class="text-right">Ref Entitle</th>
                            <th class="text-right">Collected</th>
                            <th class="text-right">Ref Eligible</th>
                            <th class="text-right">Credited</th>
                            <th class="text-right">Due</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        foreach ($records as $inv) { 
                            $status_class = 'badge-secondary';
                            if ($inv['allocation_status'] === 'Fully Allocated') {
                                $status_class = 'badge-success';
                            } elseif ($inv['allocation_status'] === 'Partial Collection') {
                                $status_class = 'badge-warning';
                            } elseif ($inv['allocation_status'] === 'Hospital Share Incomplete') {
                                $status_class = 'badge-danger';
                            }
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td class="small"><?php echo !empty($inv['date']) ? date('d/m/Y', $inv['date']) : 'N/A'; ?></td>
                                <td>
                                    <strong>#<?php echo $inv['invoice_id']; ?></strong>
                                </td>
                                <td>
                                    <div><strong><?php echo html_escape($inv['patient_name']); ?></strong></div>
                                    <div class="small text-muted">ID: <?php echo html_escape($inv['patient_id']); ?> | <?php echo html_escape($inv['patient_phone']); ?></div>
                                </td>
                                <td>
                                    <strong><?php echo html_escape($inv['referrer_name']); ?></strong>
                                    <div class="small text-muted"><?php echo html_escape($inv['referrer_type']); ?></div>
                                </td>
                                <td class="text-right"><?php echo $currency . ' ' . number_format($inv['gross_amount'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($inv['discount_amount'], 2); ?></td>
                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($inv['net_payable'], 2); ?></td>
                                <td class="text-right text-info"><?php echo $currency . ' ' . number_format($inv['hospital_share_required'], 2); ?></td>
                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($inv['referral_entitlement'], 2); ?></td>
                                <td class="text-right text-success font-weight-bold"><?php echo $currency . ' ' . number_format($inv['amount_collected'], 2); ?></td>
                                <td class="text-right font-weight-bold text-primary"><?php echo $currency . ' ' . number_format($inv['referral_eligible'], 2); ?></td>
                                <td class="text-right"><?php echo $currency . ' ' . number_format($inv['commission_credited'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($inv['remaining_due'], 2); ?></td>
                                <td class="text-center">
                                    <span class="badge <?php echo $status_class; ?>"><?php echo html_escape($inv['allocation_status']); ?></span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f1f5f9; font-weight: 700;">
                            <td colspan="5" class="text-right">TOTAL INVOICE AUDIT:</td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['total_gross_amount'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['total_discount_amount'], 2); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['total_net_payable'], 2); ?></td>
                            <td class="text-right text-info"><?php echo $currency . ' ' . number_format($summary['total_hospital_share_required'], 2); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['total_referral_entitlement'], 2); ?></td>
                            <td class="text-right text-success"><?php echo $currency . ' ' . number_format($summary['total_amount_collected'], 2); ?></td>
                            <td class="text-right text-primary"><?php echo $currency . ' ' . number_format($summary['total_referral_eligible'], 2); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['total_commission_credited'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['total_remaining_due'], 2); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Print Signatures -->
            <div class="d-none d-print-block rep-print-signatures">
                <div class="signature-box">Prepared By (Accounts Officer)</div>
                <div class="signature-box">Checked By (Audit Head)</div>
                <div class="signature-box">Approved By (Managing Director)</div>
            </div>
        </div>

    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#invoice-wise-table')) {
        $('#invoice-wise-table').DataTable().destroy();
    }
    $('#invoice-wise-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Invoice Wise Referral Commission - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Invoice Wise Referral Commission - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Invoice Wise Referral Commission - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Invoice Wise Referral Commission - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[2, "desc"]], // Highest invoice first
        pageLength: 25
    });
});
</script>
