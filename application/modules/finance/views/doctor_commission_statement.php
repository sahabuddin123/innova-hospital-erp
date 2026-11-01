<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$currency = !empty($settings->currency) ? $settings->currency : '৳';
$doc = !empty($statement['doctor']) ? $statement['doctor'] : null;
$sum = !empty($statement['summary']) ? $statement['summary'] : null;
$invs = !empty($statement['investigations']) ? $statement['investigations'] : [];
$disbs = !empty($statement['disbursements']) ? $statement['disbursements'] : [];
?>
<link rel="stylesheet" href="common/extranal/css/finance/doctor_commission.css">

<style>
@media print {
    .statement-card {
        border: 1px solid #333 !important;
        box-shadow: none !important;
    }
}
</style>

<div class="content-wrapper">
    <div class="rep-wrapper">

        <?php $this->load->view('finance/inc_doctor_commission_nav'); ?>

        <!-- Controls Card -->
        <div class="rep-header-card d-print-none">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 16px;">
                <div class="rep-title">
                    <h2><i class="fas fa-file-signature text-primary"></i> Doctor Commission Statement</h2>
                    <p>Official statement of investigation referrals, commission entitlements, and disbursements</p>
                </div>
                <div class="rep-actions">
                    <button type="button" onclick="window.print()" class="rep-action-btn"><i class="fas fa-print"></i> Print Statement</button>
                    <a href="finance/doctorsCommissionSummary" class="rep-action-btn"><i class="fas fa-chart-line"></i> Summary View</a>
                </div>
            </div>

            <hr style="margin: 16px 0; border-color: var(--rep-gray-200);">

            <!-- Filter Controls -->
            <form action="finance/doctorCommissionStatement" method="get">
                <div class="rep-filter-grid">
                    <div class="rep-form-group">
                        <label>Select Doctor</label>
                        <select name="doctor_id" class="rep-form-control select2" onchange="this.form.submit()">
                            <?php foreach ($doctors as $d): ?>
                                <option value="<?php echo $d->id; ?>" <?php echo (!empty($selected_doctor) && $selected_doctor == $d->id) ? 'selected' : ''; ?>>
                                    <?php echo $d->name; ?> (Dept: <?php echo !empty($d->department) ? $d->department : 'General'; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="rep-form-group">
                        <label>Period Type</label>
                        <select name="period_type" class="rep-form-control" onchange="this.form.submit()">
                            <option value="this_month" <?php echo ($period['type'] === 'this_month') ? 'selected' : ''; ?>>This Month</option>
                            <option value="last_month" <?php echo ($period['type'] === 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                            <option value="today" <?php echo ($period['type'] === 'today') ? 'selected' : ''; ?>>Today</option>
                            <option value="this_week" <?php echo ($period['type'] === 'this_week') ? 'selected' : ''; ?>>This Week</option>
                            <option value="this_year" <?php echo ($period['type'] === 'this_year') ? 'selected' : ''; ?>>This Year</option>
                            <option value="custom" <?php echo ($period['type'] === 'custom') ? 'selected' : ''; ?>>Custom Range</option>
                        </select>
                    </div>
                    <?php if ($period['type'] === 'custom'): ?>
                        <div class="rep-form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="rep-form-control" value="<?php echo $period['date_from']; ?>">
                        </div>
                        <div class="rep-form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="rep-form-control" value="<?php echo $period['date_to']; ?>">
                        </div>
                    <?php endif; ?>
                    <div class="rep-form-group">
                        <button type="submit" class="rep-btn rep-btn-primary w-100"><i class="fas fa-sync"></i> Refresh Statement</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (!empty($doc)): ?>
            <!-- Printable Statement Container -->
            <div class="statement-card rep-table-card" style="padding: 28px; background: #ffffff;">

                <!-- Hospital & Doctor Header -->
                <div class="row align-items-center mb-4 pb-3 border-bottom">
                    <div class="col-sm-7">
                        <h2 class="font-weight-bold text-dark mb-1" style="font-size: 24px; letter-spacing: -0.5px;">
                            <?php echo !empty($settings->system_vendor) ? $settings->system_vendor : 'LIFECARE DIAGNOSTIC CENTER'; ?>
                        </h2>
                        <div class="text-muted small">
                            <?php echo !empty($settings->address) ? $settings->address : 'Hospital Management & Diagnostic Services'; ?>
                        </div>
                        <div class="text-muted small">
                            Phone: <?php echo !empty($settings->phone) ? $settings->phone : 'N/A'; ?> | Email: <?php echo !empty($settings->email) ? $settings->email : 'N/A'; ?>
                        </div>
                    </div>
                    <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                        <div class="badge badge-pill badge-primary px-3 py-2" style="font-size: 13px;">DOCTOR COMMISSION STATEMENT</div>
                        <div class="text-muted small mt-2"><strong>Statement Period:</strong> <?php echo $statement['period']['formatted']; ?></div>
                        <div class="text-muted small"><strong>Generated On:</strong> <?php echo date('d M Y, h:i A'); ?></div>
                    </div>
                </div>

                <!-- Doctor Information Box -->
                <div class="row mb-4 p-3 rounded" style="background-color: var(--rep-gray-50); border: 1px solid var(--rep-gray-200);">
                    <div class="col-md-6">
                        <div class="small text-uppercase text-muted font-weight-bold">Doctor Profile</div>
                        <h4 class="font-weight-bold text-primary mb-1"><?php echo $doc->name; ?></h4>
                        <div class="text-dark small"><strong>Doctor ID:</strong> #<?php echo $doc->id; ?></div>
                        <div class="text-dark small"><strong>Department:</strong> <?php echo !empty($doc->department_name) ? $doc->department_name : 'General Practice'; ?></div>
                        <?php if (!empty($doc->profile)): ?>
                            <div class="text-muted small"><?php echo $doc->profile; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 text-md-right mt-2 mt-md-0">
                        <div class="text-dark small"><strong>Mobile:</strong> <?php echo !empty($doc->phone) ? $doc->phone : 'N/A'; ?></div>
                        <div class="text-dark small"><strong>Email:</strong> <?php echo !empty($doc->email) ? $doc->email : 'N/A'; ?></div>
                        <div class="text-dark small"><strong>Chamber / Address:</strong> <?php echo !empty($doc->address) ? $doc->address : 'Consultation Chamber'; ?></div>
                    </div>
                </div>

                <!-- Summary Breakdown Cards -->
                <div class="rep-kpi-grid mb-4" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
                    <div class="rep-kpi-card" style="padding: 12px 14px;">
                        <div class="kpi-info">
                            <div class="kpi-title">Opening Balance</div>
                            <div class="kpi-val" style="font-size: 17px;"><?php echo $currency; ?> <?php echo number_format($sum['opening_payable'], 2); ?></div>
                        </div>
                    </div>
                    <div class="rep-kpi-card" style="padding: 12px 14px;">
                        <div class="kpi-info">
                            <div class="kpi-title">Generated (+)</div>
                            <div class="kpi-val" style="font-size: 17px;"><?php echo $currency; ?> <?php echo number_format($sum['period_generated'], 2); ?></div>
                        </div>
                    </div>
                    <div class="rep-kpi-card" style="padding: 12px 14px;">
                        <div class="kpi-info">
                            <div class="kpi-title">Eligible (+)</div>
                            <div class="kpi-val text-success" style="font-size: 17px;"><?php echo $currency; ?> <?php echo number_format($sum['period_eligible'], 2); ?></div>
                        </div>
                    </div>
                    <div class="rep-kpi-card" style="padding: 12px 14px;">
                        <div class="kpi-info">
                            <div class="kpi-title">Disbursed (-)</div>
                            <div class="kpi-val text-primary" style="font-size: 17px;"><?php echo $currency; ?> <?php echo number_format($sum['period_paid'], 2); ?></div>
                        </div>
                    </div>
                    <div class="rep-kpi-card" style="padding: 12px 14px; background: #fffbeb; border-color: #fde68a;">
                        <div class="kpi-info">
                            <div class="kpi-title text-warning">Closing Payable (=)</div>
                            <div class="kpi-val text-warning font-weight-bold" style="font-size: 18px;"><?php echo $currency; ?> <?php echo number_format($sum['closing_payable'], 2); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Itemized Investigation Referrals Table -->
                <h5 class="font-weight-bold mb-3 text-dark"><i class="fas fa-microscope text-primary mr-2"></i> Itemized Investigation Referrals</h5>
                <div class="table-responsive mb-4">
                    <table class="table rep-table" style="font-size: 12px;">
                        <thead>
                            <tr class="bg-light">
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Investigation Test</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Comm. Rate</th>
                                <th>Generated</th>
                                <th>Eligible</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($invs)): ?>
                                <?php foreach ($invs as $inv): ?>
                                    <tr>
                                        <td><a href="finance/invoice?id=<?php echo $inv['invoice_id']; ?>" class="font-weight-bold text-primary">#<?php echo $inv['invoice_id']; ?></a></td>
                                        <td><?php echo date('d/m/Y', $inv['invoice_date']); ?></td>
                                        <td><strong><?php echo $inv['item_name']; ?></strong></td>
                                        <td><?php echo $inv['test_category']; ?></td>
                                        <td><?php echo $currency; ?> <?php echo number_format($inv['test_price'], 2); ?></td>
                                        <td><?php echo $inv['quantity']; ?></td>
                                        <td><?php echo $currency; ?> <?php echo number_format($inv['subtotal'], 2); ?></td>
                                        <td><span class="badge badge-light border"><?php echo $inv['commission_rate']; ?>%</span></td>
                                        <td><?php echo $currency; ?> <?php echo number_format($inv['generated_commission'], 2); ?></td>
                                        <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($inv['eligible_commission'], 2); ?></td>
                                        <td>
                                            <?php if ($inv['commission_status'] === 'Fully Eligible'): ?>
                                                <span class="badge badge-status success">Eligible</span>
                                            <?php elseif ($inv['commission_status'] === 'Partially Eligible'): ?>
                                                <span class="badge badge-status warning">Partial</span>
                                            <?php else: ?>
                                                <span class="badge badge-status secondary">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-3">No investigation referrals recorded for this doctor during the selected period.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8" class="text-right">Period Total Generated & Eligible:</td>
                                <td><?php echo $currency; ?> <?php echo number_format($sum['period_generated'], 2); ?></td>
                                <td class="text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($sum['period_eligible'], 2); ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Disbursement Vouchers Table -->
                <h5 class="font-weight-bold mb-3 text-dark"><i class="fas fa-hand-holding-usd text-primary mr-2"></i> Commission Disbursements Paid</h5>
                <div class="table-responsive mb-4">
                    <table class="table rep-table" style="font-size: 12px;">
                        <thead>
                            <tr class="bg-light">
                                <th>Voucher #</th>
                                <th>Date</th>
                                <th>Description / Reference</th>
                                <th>Payment Method</th>
                                <th>Paid By</th>
                                <th>Authorized By</th>
                                <th class="text-right">Amount Disbursed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($disbs)): ?>
                                <?php foreach ($disbs as $d): ?>
                                    <tr>
                                        <td>#<?php echo $d['voucher_id']; ?></td>
                                        <td><?php echo date('d/m/Y', $d['payment_date']); ?></td>
                                        <td><?php echo $d['note']; ?> (<code><?php echo $d['transaction_ref']; ?></code>)</td>
                                        <td><span class="badge badge-status info"><?php echo $d['payment_method']; ?></span></td>
                                        <td><?php echo $d['paid_by']; ?></td>
                                        <td><?php echo $d['approved_by']; ?></td>
                                        <td class="text-right text-success font-weight-bold"><?php echo $currency; ?> <?php echo number_format($d['amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-2">No disbursements recorded during this period.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-right">Total Disbursed:</td>
                                <td class="text-right text-primary font-weight-bold"><?php echo $currency; ?> <?php echo number_format($sum['period_paid'], 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- 3-Tier Signatures -->
                <div class="rep-print-signatures" style="display: flex; justify-content: space-between; margin-top: 50px; padding-top: 15px;">
                    <div class="signature-box" style="width: 28%; text-align: center; border-top: 1px solid #333; padding-top: 8px;">
                        <strong>Prepared By</strong><br><span class="text-muted small">Accounts Officer</span>
                    </div>
                    <div class="signature-box" style="width: 28%; text-align: center; border-top: 1px solid #333; padding-top: 8px;">
                        <strong>Verified & Audited</strong><br><span class="text-muted small">Audit Head</span>
                    </div>
                    <div class="signature-box" style="width: 28%; text-align: center; border-top: 1px solid #333; padding-top: 8px;">
                        <strong>Doctor's Acknowledgment</strong><br><span class="text-muted small"><?php echo $doc->name; ?></span>
                    </div>
                </div>

            </div>
        <?php else: ?>
            <div class="alert alert-info">Please select a doctor to generate the commission statement.</div>
        <?php endif; ?>

    </div>
</div>
