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
                    <h2><i class="fas fa-chart-line text-primary"></i> Referral Commission Summary</h2>
                    <p>Real-time referral performance, collection-backed eligibility, hospital share recovery, and wallet balances &bull; <strong><?php echo $period['period_label']; ?></strong></p>
                </div>
                <div class="rep-actions d-print-none">
                    <button type="button" onclick="window.print()" class="rep-action-btn"><i class="fas fa-print"></i> Print Report</button>
                    <a href="referral/reconciliationDashboard" class="rep-action-btn" style="border-color: #dc2626; color: #dc2626;"><i class="fas fa-balance-scale"></i> Reconciliation Audit</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <!-- Period Switcher Pills -->
            <div class="rep-pills d-print-none">
                <a href="referral/commissionSummary?period_type=today<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'today') ? 'active' : ''; ?>">Today</a>
                <a href="referral/commissionSummary?period_type=yesterday<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'yesterday') ? 'active' : ''; ?>">Yesterday</a>
                <a href="referral/commissionSummary?period_type=this_week<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'this_week') ? 'active' : ''; ?>">This Week</a>
                <a href="referral/commissionSummary?period_type=this_month<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'this_month') ? 'active' : ''; ?>">This Month</a>
                <a href="referral/commissionSummary?period_type=last_month<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'last_month') ? 'active' : ''; ?>">Last Month</a>
                <a href="referral/commissionSummary?period_type=this_year<?php echo !empty($selected_referrer) ? '&referrer_id=' . $selected_referrer : ''; ?>" class="rep-pill <?php echo ($period_type === 'this_year') ? 'active' : ''; ?>">This Year</a>
            </div>

            <!-- Custom Filter Form -->
            <form action="referral/commissionSummary" method="get" class="row g-3 align-items-end mt-2 d-print-none">
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
                    <a href="referral/commissionSummary" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>

        <!-- 8 KPI Cards Grid -->
        <div class="rep-kpi-grid">
            <div class="rep-card">
                <div class="rep-card-icon bg-blue"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="rep-card-label">Commission Generated</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['total_generated_commission'], 2); ?></div>
                <div class="rep-card-sub text-muted">Gross potential referral commission</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-cyan"><i class="fas fa-hospital-alt"></i></div>
                <div class="rep-card-label">Hospital Share Recovered</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['total_hospital_share_recovered'], 2); ?></div>
                <div class="rep-card-sub text-info">Protected revenue before commission</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-green"><i class="fas fa-check-circle"></i></div>
                <div class="rep-card-label">Commission Eligible</div>
                <div class="rep-card-val text-success"><?php echo $currency . ' ' . number_format($summary['total_eligible_commission'], 2); ?></div>
                <div class="rep-card-sub text-success">Backed by cash receipts</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-purple"><i class="fas fa-wallet"></i></div>
                <div class="rep-card-label">Commission Credited</div>
                <div class="rep-card-val text-primary"><?php echo $currency . ' ' . number_format($summary['total_credited_commission'], 2); ?></div>
                <div class="rep-card-sub text-muted">Transferred to referrer wallets</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-orange"><i class="fas fa-hourglass-half"></i></div>
                <div class="rep-card-label">Commission Payable</div>
                <div class="rep-card-val text-warning"><?php echo $currency . ' ' . number_format($summary['total_payable_commission'], 2); ?></div>
                <div class="rep-card-sub text-warning">Eligible minus credited</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-teal"><i class="fas fa-money-check"></i></div>
                <div class="rep-card-label">Total Withdrawn</div>
                <div class="rep-card-val"><?php echo $currency . ' ' . number_format($summary['total_withdrawn'], 2); ?></div>
                <div class="rep-card-sub text-muted">Paid out to referrers</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-indigo"><i class="fas fa-coins"></i></div>
                <div class="rep-card-label">Available Wallet Balance</div>
                <div class="rep-card-val text-primary"><?php echo $currency . ' ' . number_format($summary['total_available_wallet_balance'], 2); ?></div>
                <div class="rep-card-sub text-primary">Current wallet liability</div>
            </div>
            <div class="rep-card">
                <div class="rep-card-icon bg-gray"><i class="fas fa-user-friends"></i></div>
                <div class="rep-card-label">Active Referrers</div>
                <div class="rep-card-val"><?php echo number_format($summary['total_referrers']); ?></div>
                <div class="rep-card-sub text-muted">Registered promoters/doctors</div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="rep-table-card mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h4 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--rep-gray-800);">
                    <i class="fas fa-list text-primary mr-1"></i> Referrer Commission & Wallet Summary
                </h4>
                <div class="d-print-none small text-muted">
                    Showing <strong><?php echo count($records); ?></strong> referrers for <?php echo $period['period_label']; ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle rep-table" id="referral-summary-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th>Referrer Name</th>
                            <th>Type</th>
                            <th>Phone</th>
                            <th class="text-center">Invoices</th>
                            <th class="text-right">Gross Billing</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Hosp Share</th>
                            <th class="text-right">Ref Generated</th>
                            <th class="text-right">Ref Eligible</th>
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
                        foreach ($records as $row) { ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td>
                                    <strong><?php echo html_escape($row['name']); ?></strong>
                                    <div class="small text-muted">ID: #<?php echo $row['id']; ?></div>
                                </td>
                                <td><span class="badge badge-light border text-dark"><?php echo html_escape($row['type']); ?></span></td>
                                <td><?php echo !empty($row['phone']) ? html_escape($row['phone']) : '<span class="text-muted">N/A</span>'; ?></td>
                                <td class="text-center font-weight-bold"><?php echo number_format($row['invoices_count']); ?></td>
                                <td class="text-right"><?php echo $currency . ' ' . number_format($row['gross_billing'], 2); ?></td>
                                <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($row['discount_absorbed'], 2); ?></td>
                                <td class="text-right text-info"><?php echo $currency . ' ' . number_format($row['hospital_share_recovered'], 2); ?></td>
                                <td class="text-right font-weight-bold"><?php echo $currency . ' ' . number_format($row['commission_generated'], 2); ?></td>
                                <td class="text-right font-weight-bold text-success"><?php echo $currency . ' ' . number_format($row['commission_eligible'], 2); ?></td>
                                <td class="text-right text-primary"><?php echo $currency . ' ' . number_format($row['commission_credited'], 2); ?></td>
                                <td class="text-right text-warning font-weight-bold"><?php echo $currency . ' ' . number_format($row['commission_payable'], 2); ?></td>
                                <td class="text-right"><?php echo $currency . ' ' . number_format($row['commission_withdrawn'], 2); ?></td>
                                <td class="text-right font-weight-bold text-primary" style="background-color: #f0f9ff;"><?php echo $currency . ' ' . number_format($row['available_balance'], 2); ?></td>
                                <td class="text-center d-print-none">
                                    <div class="btn-group btn-group-sm">
                                        <a href="referral/statement?referrer_id=<?php echo $row['id']; ?>" class="btn btn-outline-info" title="View Statement"><i class="fas fa-file-invoice"></i></a>
                                        <a href="referral/invoiceWiseReport?referrer_id=<?php echo $row['id']; ?>" class="btn btn-outline-primary" title="Invoices"><i class="fas fa-list-ol"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f1f5f9; font-weight: 700;">
                            <td colspan="4" class="text-right">TOTAL SUMMARY:</td>
                            <td class="text-center"><?php echo number_format($summary['total_invoices']); ?></td>
                            <td class="text-right"><?php echo $currency . ' ' . number_format($summary['total_gross_billing'], 2); ?></td>
                            <td class="text-right text-danger"><?php echo $currency . ' ' . number_format($summary['total_discount_absorbed'], 2); ?></td>
                            <td class="text-right text-info"><?php echo $currency . ' ' . number_format($summary['total_hospital_share_recovered'], 2); ?></td>
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
    if ($.fn.DataTable.isDataTable('#referral-summary-table')) {
        $('#referral-summary-table').DataTable().destroy();
    }
    $('#referral-summary-table').DataTable({
        responsive: true,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'excel', title: 'Referral Commission Summary - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'csv', title: 'Referral Commission Summary - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'pdf', title: 'Referral Commission Summary - Lifecare', className: 'btn btn-sm btn-outline-secondary' },
            { extend: 'print', title: 'Referral Commission Summary - Lifecare', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[9, "desc"]], // Sort by Eligible commission desc
        pageLength: 25
    });
});
</script>
