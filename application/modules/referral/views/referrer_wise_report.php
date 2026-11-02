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
                    <h2><i class="fas fa-user-friends text-primary"></i> Referrer-Wise Commission Report</h2>
                    <p>Comprehensive portfolio breakdown of each referrer, referral volume, discount absorption, and payouts &bull; <strong><?php echo $period['period_label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <button type="button" onclick="window.print()" class="rep-action-btn"><i class="fas fa-print"></i> Print Report</button>
                    <a href="referral/statement" class="rep-action-btn"><i class="fas fa-file-invoice"></i> Statements</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="referral/referrerWiseReport?period_type=today<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="referral/referrerWiseReport?period_type=yesterday<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="referral/referrerWiseReport?period_type=this_week<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="referral/referrerWiseReport?period_type=this_month<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="referral/referrerWiseReport?period_type=last_month<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="referral/referrerWiseReport?period_type=this_year<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'this_year') ? 'active' : ''; ?>">This Year</a>
            </div>

            <!-- Custom Filter Form -->
            <form action="referral/referrerWiseReport" method="get" class="row g-3 align-items-end mt-2 d-print-none">
                <input type="hidden" name="period_type" value="custom">
                <div class="col-md-3">
                    <label class="form-label small text-muted font-weight-bold">Referrer</label>
                    <select name="referrer_id" class="form-control form-control-sm select2">
                        <option value="">-- All Active Referrers --</option>
                        <?php foreach ($referrers as $r) { ?>
                            <option value="<?php echo $r->id; ?>" <?php echo (!empty($selected_referrer) && $selected_referrer == $r->id) ? 'selected' : ''; ?>>
                                <?php echo html_escape($r->name); ?> (<?php echo html_escape($r->type); ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted font-weight-bold">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo html_escape($period['date_from']); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted font-weight-bold">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo html_escape($period['date_to']); ?>">
                </div>
                <div class="col-md-3 d-flex" style="gap: 8px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-filter"></i> Apply Filter</button>
                    <a href="referral/referrerWiseReport" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>

        <!-- KPI Grid -->
        <div class="rep-kpi-grid">
            <div class="rep-card">
                <div class="rep-card-icon bg-blue"><i class="fas fa-users"></i></div>
                <div class="rep-card-label">Active Referrers</div>
                <div class="rep-card-val"><?php echo number_format($summary['total_referrers']); ?></div>
                <div class="rep-card-sub text-muted">Doctors, staff & agents</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-cyan"><i class="fas fa-file-invoice"></i></div>
                <div class="rep-card-label">Referred Invoices</div>
                <div class="rep-card-val"><?php echo number_format($summary['total_invoices']); ?></div>
                <div class="rep-card-sub text-info">Diagnostic billings generated</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-purple"><i class="fas fa-receipt"></i></div>
                <div class="rep-card-label">Gross Billing</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['total_gross_billing'], 2); ?></div>
                <div class="rep-card-sub text-muted">Total list price volume</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-green"><i class="fas fa-cash-register"></i></div>
                <div class="rep-card-label">Net Billing</div>
                <div class="rep-card-val text-success"><?php echo $currency . ' ' . number_format($summary['total_net_billing'], 2); ?></div>
                <div class="rep-card-sub text-success">After discount deduction</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-orange"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="rep-card-label">Commission Generated</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['total_generated_commission'], 2); ?></div>
                <div class="rep-card-sub text-muted">Gross entitlement</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-teal"><i class="fas fa-check-double"></i></div>
                <div class="rep-card-label">Commission Eligible</div>
                <div class="rep-card-val text-success"><?php echo $currency . ' ' . number_format($summary['total_eligible_commission'], 2); ?></div>
                <div class="rep-card-sub text-success">100% Hospital share cleared</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-indigo"><i class="fas fa-wallet"></i></div>
                <div class="rep-card-label">Total Credited</div>
                <div class="rep-card-val text-primary"><?php echo $currency . ' ' . number_format($summary['total_credited_commission'], 2); ?></div>
                <div class="rep-card-sub text-primary">Moved to wallet ledger</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-gray"><i class="fas fa-coins"></i></div>
                <div class="rep-card-label">Current Wallet Balance</div>
                <div class="rep-card-val text-primary"><?php echo $currency . ' ' . number_format($summary['total_available_wallet_balance'], 2); ?></div>
                <div class="rep-card-sub text-muted">Ready for withdrawal</div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="rep-table-card mt-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle rep-table" id="referrer-wise-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th>Referrer Name</th>
                            <th>Category</th>
                            <th>Contact Phone</th>
                            <th class="text-center">Invoices</th>
                            <th class="text-right">Gross Billing</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Net Billing</th>
                            <th class="text-right">Generated</th>
                            <th class="text-right">Eligible</th>
                            <th class="text-right">Credited</th>
                            <th class="text-right">Payable</th>
                            <th class="text-right">Withdrawn</th>
                            <th class="text-right">Wallet Balance</th>
                            <th class="text-center d-print-none">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        foreach ($records as $r) { ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td>
                                    <strong><?php echo html_escape($r['name']); ?></strong>
                                    <div class="small text-muted">ID: #<?php echo $r['id']; ?></div>
                                </td>
                                <td><span class="badge badge-light border text-dark"><?php echo html_escape($r['type']); ?></span></td>
                                <td><?php echo !empty($r['phone']) ? html_escape($r['phone']) : '<span class="text-muted">N/A</span>'; ?></td>
                                <td class="text-center font-weight-bold"><?php echo number_format($r['invoices_count']); ?></td>
                                <td class="text-right"><?php echo $currency . ' ' . number_format($r['gross_billing'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($r['discount_absorbed'], 2); ?></td>
                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($r['net_billing'], 2); ?></td>
                                <td class="text-right"><?php echo $currency . ' ' . number_format($r['commission_generated'], 2); ?></td>
                                <td class="text-right font-weight-bold text-success"><?php echo $currency . ' ' . number_format($r['commission_eligible'], 2); ?></td>
                                <td class="text-right text-primary"><?php echo $currency . ' ' . number_format($r['commission_credited'], 2); ?></td>
                                <td class="text-right text-warning font-weight-bold"><?php echo $currency . ' ' . number_format($r['commission_payable'], 2); ?></td>
                                <td class="text-right"><?php echo $currency . ' ' . number_format($r['commission_withdrawn'], 2); ?></td>
                                <td class="text-right font-weight-bold text-primary" style="background-color: #f0f9ff;"><?php echo $currency . ' ' . number_format($r['available_balance'], 2); ?></td>
                                <td class="text-center d-print-none">
                                    <div class="btn-group btn-group-sm">
                                        <a href="referral/statement?referrer_id=<?php echo $r['id']; ?>" class="btn btn-outline-info" title="View Statement"><i class="fas fa-file-signature"></i></a>
                                        <a href="referral/invoiceWiseReport?referrer_id=<?php echo $r['id']; ?>" class="btn btn-outline-primary" title="Invoices"><i class="fas fa-list"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f1f5f9; font-weight: 700;">
                            <td colspan="4" class="text-right">TOTAL PORTFOLIO:</td>
                            <td class="text-center"><?php echo number_format($summary['total_invoices']); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['total_gross_billing'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['total_discount_absorbed'], 2); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['total_net_billing'], 2); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['total_generated_commission'], 2); ?></td>
                            <td class="text-right text-success"><?php echo $currency . ' ' . number_format($summary['total_eligible_commission'], 2); ?></td>
                            <td class="text-right text-primary"><?php echo $currency . ' ' . number_format($summary['total_credited_commission'], 2); ?></td>
                            <td class="text-right text-warning"><?php echo $currency . ' ' . number_format($summary['total_payable_commission'], 2); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['total_withdrawn'], 2); ?></td>
                            <td class="text-right text-primary"><?php echo $currency . ' ' . number_format($summary['total_available_wallet_balance'], 2); ?></td>
                            <td class="d-print-none"></td>
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
    if ($.fn.DataTable.isDataTable('#referrer-wise-table')) {
        $('#referrer-wise-table').DataTable().destroy();
    }
    $('#referrer-wise-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Referrer Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Referrer Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Referrer Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Referrer Wise Commission Report - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[9, "desc"]], // Highest eligible first
        pageLength: 25
    });
});
</script>
