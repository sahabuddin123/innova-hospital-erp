<?php
$currency = !empty($settings->currency) ? $settings->currency : '৳';
$period_label = !empty($summary['period_label']) ? $summary['period_label'] : ucfirst($period_type);
$is_pdf = !empty($pdf_mode);
$is_print = !empty($print_mode);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo html_escape($settings->title ?? 'Lifecare Diagnostic Center'); ?> - Financial Performance &amp; Profit/Loss Statement</title>
    <base href="<?php echo base_url(); ?>">
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: <?php echo $is_pdf ? '0' : '20px'; ?>;
        }
        .statement-wrap {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border: <?php echo $is_pdf ? 'none' : '1px solid #cbd5e1'; ?>;
            border-radius: 8px;
            padding: 24px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo-title {
            font-size: 20px;
            font-weight: 800;
            color: #2563eb;
            margin: 0 0 2px 0;
        }
        .logo-subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }
        .hospital-info {
            text-align: right;
            font-size: 11px;
            line-height: 1.4;
            color: #334155;
        }
        .hospital-info strong {
            font-size: 13px;
            color: #0f172a;
        }
        .statement-title-box {
            text-align: center;
            margin: 8px 0 12px 0;
        }
        .statement-title {
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin: 0 0 4px 0;
        }
        .statement-meta {
            font-size: 11px;
            color: #475569;
        }
        .statement-meta strong {
            color: #0f172a;
        }
        .columns-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .columns-table > tbody > tr > td {
            vertical-align: top;
            width: 50%;
            padding: 0 8px;
        }
        .columns-table > tbody > tr > td:first-child {
            padding-left: 0;
            padding-right: 8px;
        }
        .columns-table > tbody > tr > td:last-child {
            padding-left: 8px;
            padding-right: 0;
        }
        .section-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 10px;
            overflow: hidden;
            background: #ffffff;
        }
        .section-box-header {
            background: #f1f5f9;
            padding: 5px 8px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table td, .data-table th {
            padding: 4px 7px;
            font-size: 10px;
        }
        .data-table tr:not(:last-child) td {
            border-bottom: 1px solid #f1f5f9;
        }
        .data-table th {
            background: #f8fafc;
            border-bottom: 1px solid #cbd5e1;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            font-size: 9.5px;
        }
        .val-num {
            text-align: right;
            font-weight: 600;
        }
        .highlight-row {
            background: #eff6ff;
            font-weight: 700;
        }
        .highlight-row td {
            color: #1e3a8a;
        }
        .profit-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 8px 10px;
            margin-top: 6px;
        }
        .profit-final-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 800;
            color: #166534;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            margin-bottom: 8px;
        }
        .signatures-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            font-size: 10.5px;
            color: #475569;
        }
        .sig-line {
            display: block;
            border-top: 1px dotted #64748b;
            margin: 0 auto 6px auto;
            width: 75%;
        }
        .statement-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            text-align: center;
            font-size: 9.5px;
            color: #94a3b8;
        }
        .btn-print-bar {
            text-align: right;
            margin-bottom: 14px;
        }
        .btn-print {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 6mm 8mm 6mm 8mm;
            }
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }
            .btn-print-bar { display: none !important; }
            .statement-wrap {
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                margin: 0 auto !important;
                box-shadow: none !important;
            }
            .page-2 {
                page-break-before: always !important;
                break-before: page !important;
                margin-top: 0 !important;
                padding-top: 0 !important;
            }
            .test-detail-table tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
        .page-2 {
            margin-top: 24px;
        }
    </style>
</head>
<body>

<?php if ($is_print): ?>
<div class="statement-wrap btn-print-bar">
    <?php $this->load->view('report/inc_export_toolbar', ['export_report_key' => isset($active_report) ? $active_report : 'financial_performance_print']); ?>
</div>
<?php endif; ?>

<div class="statement-wrap">
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="logo-title">+ <?php echo html_escape($settings->title ?? 'Lifecare Diagnostic Center'); ?></div>
                <div class="logo-subtitle">লাইফ কেয়ার ডায়াগনস্টিক সেন্টার</div>
            </td>
            <td style="width: 50%;">
                <div class="hospital-info">
                    <strong><?php echo html_escape($settings->title ?? 'Lifecare Diagnostic Center'); ?></strong><br>
                    <?php echo html_escape($settings->address ?? 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali'); ?><br>
                    <?php echo html_escape($settings->phone ?? '01829 95 25 95, 01727 69 69 77'); ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- Statement Title & Period -->
    <div class="statement-title-box">
        <div class="statement-title">Financial Performance &amp; Profit/Loss Statement</div>
        <div class="statement-meta">
            Period: <strong><?php echo html_escape($period_label); ?> (<?php echo ucfirst($period_type); ?>)</strong><br>
            Generated Date: <?php echo date('d/m/Y, h:i A'); ?>
        </div>
    </div>

    <!-- 2 Column Section Layout -->
    <table class="columns-table" width="100%">
        <!-- Row 1: 1. Invoice & Billing Summary (Left) & 3. Commission & Recovery (Right) -->
        <tr>
            <td width="50%" valign="top" style="padding-right: 6px;">
                <!-- 1. Invoice & Billing Summary -->
                <div class="section-box">
                    <div class="section-box-header">1. Invoice &amp; Billing Summary</div>
                    <table class="data-table">
                        <tr>
                            <td>Total Invoices</td>
                            <td class="val-num"><?php echo number_format($summary['total_invoices']); ?></td>
                        </tr>
                        <tr>
                            <td>Gross Billing</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Total Discount (-)</td>
                            <td class="val-num" style="color: #dc2626;">- <?php echo $currency . ' ' . number_format($summary['total_discount'], 2); ?></td>
                        </tr>
                        <tr class="highlight-row">
                            <td>Net Billing</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Cancelled Invoices</td>
                            <td class="val-num"><?php echo number_format($summary['cancelled_invoices']); ?></td>
                        </tr>
                        <tr>
                            <td>Refunded Amount</td>
                            <td class="val-num" style="color: #dc2626;"><?php echo $currency . ' ' . number_format($summary['refunded_amount'], 2); ?></td>
                        </tr>
                    </table>
                </div>
            </td>
            <td width="50%" valign="top" style="padding-left: 6px;">
                <!-- 3. Commission & Recovery -->
                <div class="section-box">
                    <div class="section-box-header">3. Commission &amp; Recovery</div>
                    <table class="data-table">
                        <tr style="background: #f8fafc; font-weight: 700;">
                            <td colspan="2">Doctor Commission</td>
                        </tr>
                        <tr>
                            <td>Total Doctor Commission</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['doctor_commission'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Paid Commission</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['paid_doctor_comm'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Pending Commission</td>
                            <td class="val-num" style="color: #d97706;"><?php echo $currency . ' ' . number_format($summary['pending_doctor_comm'], 2); ?></td>
                        </tr>

                        <tr style="background: #f8fafc; font-weight: 700;">
                            <td colspan="2" style="border-top: 1px solid #cbd5e1;">Referral Commission</td>
                        </tr>
                        <tr>
                            <td>Total Referral Commission</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['referral_commission'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Earned Commission</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['earned_ref_comm'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Pending Commission</td>
                            <td class="val-num" style="color: #d97706;"><?php echo $currency . ' ' . number_format($summary['pending_ref_comm'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Withdrawn</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['withdrawn_ref_comm'], 2); ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: #16a34a;">Available Wallet Balance</td>
                            <td class="val-num" style="font-weight: 600; color: #16a34a;"><?php echo $currency . ' ' . number_format($summary['available_wallet_balance'], 2); ?></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>

        <!-- Row 2: 2. Collection & Due Summary (Left) & 4. Hospital Expenses (Right) -->
        <tr>
            <td width="50%" valign="top" style="padding-right: 6px;">
                <!-- 2. Collection & Due Summary -->
                <div class="section-box">
                    <div class="section-box-header">2. Collection &amp; Due Summary</div>
                    <table class="data-table">
                        <tr class="highlight-row">
                            <td>Total Collection</td>
                            <td class="val-num" style="color: #16a34a;"><?php echo $currency . ' ' . number_format($summary['total_collection'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Cash Collection</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['cash_collection'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>bKash Collection</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['bkash_collection'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Nagad Collection</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['nagad_collection'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Bank Collection</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['bank_collection'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Card Collection</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['card_collection'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Other Collection</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['other_collection'], 2); ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700; color: #dc2626;">Total Due</td>
                            <td class="val-num" style="color: #dc2626; font-weight: 700;"><?php echo $currency . ' ' . number_format($summary['total_due'], 2); ?></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b;">Previous Due Collected</td>
                            <td class="val-num" style="color: #64748b;"><?php echo $currency . ' ' . number_format($summary['prev_due_collected'], 2); ?></td>
                        </tr>
                    </table>
                </div>
            </td>
            <td width="50%" valign="top" style="padding-left: 6px;">
                <!-- 4. Hospital Expenses -->
                <div class="section-box">
                    <div class="section-box-header">4. Hospital Expenses</div>
                    <table class="data-table">
                        <?php if (!empty($expense_breakdown)): ?>
                            <?php foreach ($expense_breakdown as $c_name => $c_amt): ?>
                                <tr>
                                    <td><?php echo html_escape($c_name); ?></td>
                                    <td class="val-num"><?php echo $currency . ' ' . number_format($c_amt, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" style="text-align: center; color: #94a3b8;">No expenses recorded</td>
                            </tr>
                        <?php endif; ?>
                        <tr style="background: #f8fafc; font-weight: 700; border-top: 1px solid #cbd5e1;">
                            <td>Total Expenses</td>
                            <td class="val-num" style="color: #dc2626;"><?php echo $currency . ' ' . number_format($summary['total_expenses'], 2); ?></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>

        <!-- Row 3: 5. Profit Calculation (Left) & 6. Payment Method Wise Collection (Right) -->
        <tr>
            <td width="50%" valign="top" style="padding-right: 6px;">
                <!-- 5. Profit Calculation -->
                <div class="section-box">
                    <div class="section-box-header">5. Profit Calculation</div>
                    <table class="data-table">
                        <tr>
                            <td>Gross Billing</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['gross_billing'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>(-) Discount</td>
                            <td class="val-num" style="color: #dc2626;">- <?php echo $currency . ' ' . number_format($summary['total_discount'], 2); ?></td>
                        </tr>
                        <tr style="background: #f8fafc; font-weight: 700;">
                            <td>Net Billing</td>
                            <td class="val-num"><?php echo $currency . ' ' . number_format($summary['net_billing'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>(-) Doctor Commission</td>
                            <td class="val-num" style="color: #dc2626;">- <?php echo $currency . ' ' . number_format($summary['doctor_commission'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>(-) Referral Commission</td>
                            <td class="val-num" style="color: #dc2626;">- <?php echo $currency . ' ' . number_format($summary['referral_commission'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>(-) Hospital Expenses</td>
                            <td class="val-num" style="color: #dc2626;">- <?php echo $currency . ' ' . number_format($summary['total_expenses'], 2); ?></td>
                        </tr>
                        <tr style="background: #dcfce7; font-weight: 800; font-size: 11px;">
                            <td style="color: #166534;">NET PROFIT</td>
                            <td class="val-num" style="color: #166534; font-size: 11.5px;"><?php echo $currency . ' ' . number_format($summary['net_profit'], 2); ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700;">Net Margin %</td>
                            <td class="val-num" style="font-weight: 700; color: #166534;"><?php echo number_format($summary['net_margin'], 2); ?>%</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td width="50%" valign="top" style="padding-left: 6px;">
                <!-- 6. Payment Method Wise Collection -->
                <div class="section-box">
                    <div class="section-box-header">6. Payment Method Wise Collection</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Payment Method</th>
                                <th style="text-align: right;">Amount</th>
                                <th style="text-align: right;">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($methods as $m): ?>
                                <tr>
                                    <td style="font-weight: 600;"><?php echo html_escape($m['name']); ?></td>
                                    <td class="val-num"><?php echo $currency . ' ' . number_format($m['amount'], 2); ?></td>
                                    <td class="val-num" style="color: #64748b;"><?php echo number_format($m['percentage'], 1); ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr style="background: #f8fafc; font-weight: 700; border-top: 1px solid #cbd5e1;">
                                <td>Total</td>
                                <td class="val-num" style="color: #16a34a;"><?php echo $currency . ' ' . number_format($summary['total_collection'], 2); ?></td>
                                <td class="val-num">100%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <table class="signatures-table">
        <tr>
            <td>
                <span class="sig-line"></span>
                Prepared By
            </td>
            <td>
                <span class="sig-line"></span>
                Accounts Officer
            </td>
            <td>
                <span class="sig-line"></span>
                Authorized Signature
            </td>
        </tr>
    </table>

    <!-- Statement Footer -->
    <div class="statement-footer">
        Computer Generated Financial Statement &bull; Lifecare Diagnostic Center &bull; Page 1 of 2
    </div>
</div>

<!-- PAGE 2: DETAILED SERVICE & LAB TEST PERFORMANCE -->
<div class="statement-wrap page-2">
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="logo-title">+ <?php echo html_escape($settings->title ?? 'Lifecare Diagnostic Center'); ?></div>
                <div class="logo-subtitle">লাইফ কেয়ার ডায়াগনস্টিক সেন্টার</div>
            </td>
            <td style="width: 50%;">
                <div class="hospital-info">
                    <strong><?php echo html_escape($settings->title ?? 'Lifecare Diagnostic Center'); ?></strong><br>
                    <?php echo html_escape($settings->address ?? 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali'); ?><br>
                    <?php echo html_escape($settings->phone ?? '01829 95 25 95, 01727 69 69 77'); ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- Statement Title & Period -->
    <div class="statement-title-box">
        <div class="statement-title">Service / Lab Test Performance Statement</div>
        <div class="statement-meta">
            Period: <strong><?php echo html_escape($period_label); ?></strong> &bull;
            Generated Date: <strong><?php echo date('d/m/Y h:i A'); ?></strong> &bull;
            Total Unique Tests: <strong><?php echo count($service_perf); ?></strong>
        </div>
    </div>

    <!-- 7. Detailed Service / Test Breakdown Table -->
    <div class="section-box" style="margin-bottom: 20px;">
        <div class="section-box-header" style="background: #0f172a; color: #ffffff;">
            7. Service / Lab Test Performance &amp; Referral Commission Breakdown
        </div>
        <table class="data-table test-detail-table" style="font-size: 10.5px;">
            <thead>
                <tr style="background: #f1f5f9; font-weight: 700;">
                    <th style="width: 32px; text-align: center;">#</th>
                    <th style="text-align: left;">Service / Test Name</th>
                    <th style="width: 65px; text-align: center;">Qty (বার)</th>
                    <th style="width: 90px; text-align: right;">Gross Price</th>
                    <th style="width: 80px; text-align: right;">Discount</th>
                    <th style="width: 95px; text-align: right;">Net Billing</th>
                    <th style="width: 100px; text-align: right; color: #7e22ce;">Ref Comm (রেফ.)</th>
                    <th style="width: 110px; text-align: right; color: #15803d;">Hospital Net (হাসপাতাল)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $tot_qty = 0;
                $tot_gross = 0.00;
                $tot_disc = 0.00;
                $tot_net = 0.00;
                $tot_ref = 0.00;
                $tot_hosp = 0.00;
                if (!empty($service_perf)):
                    $sl = 1;
                    foreach ($service_perf as $sp):
                        $tot_qty += $sp['qty'];
                        $tot_gross += $sp['gross'];
                        $tot_disc += $sp['discount'];
                        $tot_net += $sp['net'];
                        $tot_ref += $sp['ref_comm'];
                        $tot_hosp += $sp['hospital_net'];
                ?>
                    <tr>
                        <td style="text-align: center; color: #64748b;"><?php echo $sl++; ?></td>
                        <td style="font-weight: 600; color: #0f172a;"><?php echo html_escape($sp['name']); ?></td>
                        <td style="text-align: center; font-weight: 700; color: #1e293b;"><?php echo $sp['qty']; ?></td>
                        <td class="val-num"><?php echo $currency . ' ' . number_format($sp['gross'], 2); ?></td>
                        <td class="val-num" style="color: #dc2626;"><?php echo ($sp['discount'] > 0) ? '- ' . $currency . ' ' . number_format($sp['discount'], 2) : '-'; ?></td>
                        <td class="val-num" style="font-weight: 700; color: #0284c7;"><?php echo $currency . ' ' . number_format($sp['net'], 2); ?></td>
                        <td class="val-num" style="font-weight: 600; color: #9333ea;"><?php echo $currency . ' ' . number_format($sp['ref_comm'], 2); ?></td>
                        <td class="val-num" style="font-weight: 700; color: #16a34a;"><?php echo $currency . ' ' . number_format($sp['hospital_net'], 2); ?></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #94a3b8; padding: 16px;">No service / test records for this period.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background: #0f172a; color: #ffffff; font-weight: 800; font-size: 11px;">
                    <td colspan="2" style="text-align: right; padding-right: 12px;">TOTAL:</td>
                    <td style="text-align: center;"><?php echo number_format($tot_qty); ?></td>
                    <td style="text-align: right;"><?php echo $currency . ' ' . number_format($tot_gross, 2); ?></td>
                    <td style="text-align: right; color: #fca5a5;">- <?php echo $currency . ' ' . number_format($tot_disc, 2); ?></td>
                    <td style="text-align: right; color: #93c5fd;"><?php echo $currency . ' ' . number_format($tot_net, 2); ?></td>
                    <td style="text-align: right; color: #d8b4fe;"><?php echo $currency . ' ' . number_format($tot_ref, 2); ?></td>
                    <td style="text-align: right; color: #86efac;"><?php echo $currency . ' ' . number_format($tot_hosp, 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Signatures on Page 2 -->
    <table class="signatures-table" style="margin-top: 35px;">
        <tr>
            <td>
                <span class="sig-line"></span>
                Prepared By
            </td>
            <td>
                <span class="sig-line"></span>
                Accounts Officer
            </td>
            <td>
                <span class="sig-line"></span>
                Authorized Signature
            </td>
        </tr>
    </table>

    <div class="statement-footer">
        Computer Generated Service Performance Statement &bull; Lifecare Diagnostic Center &bull; Page 2 of 2
    </div>
</div>

<?php if ($is_print): ?>
<script type="text/javascript">
    window.onload = function() {
        // Automatically open print dialog when loaded in print mode
        window.print();
    };
</script>
<?php endif; ?>

</body>
</html>
