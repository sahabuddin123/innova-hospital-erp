<?php
// Ensure Asia/Dhaka timezone
date_default_timezone_set('Asia/Dhaka');
$currency = '৳';

// Amount in Words Helper
if (!function_exists('amountInWords')) {
    function amountInWords($number) {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty',
            50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty',
            90 => 'Ninety'
        );
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($i < $digits_length) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred
                    : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
            } else $str[] = null;
        }
        $taka = implode('', array_reverse($str));
        $poisha = ($decimal > 0) ? " and " . ($words[floor($decimal / 10) * 10] . " " . $words[$decimal % 10]) . ' Poisha' : '';
        return ($taka ? trim($taka) . ' Taka' : 'Zero Taka') . $poisha . ' Only';
    }
}

// Hospital Logo
if (!empty($settings->invoice_logo) && file_exists($settings->invoice_logo)) {
    $hospital_logo = $settings->invoice_logo;
} elseif (file_exists('uploads/logo-nonetext.png')) {
    $hospital_logo = 'uploads/logo-nonetext.png';
} elseif (file_exists('uploads/logo_1.png')) {
    $hospital_logo = 'uploads/logo_1.png';
} elseif (file_exists('uploads/logo.png')) {
    $hospital_logo = 'uploads/logo.png';
} else {
    $hospital_logo = !empty($settings->logo) ? $settings->logo : '';
}

$hospital_title = !empty($settings->title) ? $settings->title : 'LIFECARE DIAGNOSTIC CENTER';
$hospital_bangla = 'লাইফ কেয়ার ডায়াগনস্টিক সেন্টার';
$hospital_address = !empty($settings->address) ? $settings->address : 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali';
$phone_1 = '01829 95 25 95';
$phone_2 = '01727 69 69 77';

$slip_no = 'SLIP-' . sprintf('%06d', $withdrawal->id);
$txn_time_formatted = !empty($transaction_date) ? date('d/m/Y h:i A', $transaction_date) : date('d/m/Y h:i A', $withdrawal->created_at);
$print_time = date('d/m/Y h:i A');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Withdrawal Slip - <?php echo $withdrawal->withdrawal_id; ?></title>
    <style>
        body {
            font-family: 'freeserif', 'dejavusans', sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .header-table td {
            vertical-align: top;
        }
        .brand-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .brand-bangla {
            font-size: 9pt;
            color: #0d9488;
            font-weight: bold;
            margin-top: 1px;
            margin-bottom: 2px;
        }
        .brand-meta {
            font-size: 7.5pt;
            color: #475569;
            line-height: 1.25;
        }
        .title-badge {
            font-size: 11.5pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.3px;
            text-align: right;
        }
        .subbadge {
            font-size: 7pt;
            font-weight: bold;
            color: #0369a1;
            background-color: #e0f2fe;
            padding: 1px 5px;
            text-transform: uppercase;
            text-align: right;
            display: inline-block;
        }
        .info-box {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 5px 6px;
            margin-bottom: 5px;
        }
        .box-title {
            font-size: 7.5pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }
        .field-lbl {
            font-size: 7.5pt;
            color: #64748b;
            font-weight: bold;
            width: 38%;
        }
        .field-val {
            font-size: 7.5pt;
            color: #0f172a;
            font-weight: bold;
            width: 62%;
        }
        .ledger-table {
            border: 1px solid #cbd5e1;
            margin-top: 4px;
            margin-bottom: 6px;
        }
        .ledger-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 4px 6px;
            text-align: left;
        }
        .ledger-table td {
            font-size: 8pt;
            padding: 4px 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .in-words-box {
            background-color: #f1f5f9;
            border-left: 3px solid #1e3a8a;
            padding: 4px 6px;
            font-size: 7.5pt;
            margin-bottom: 6px;
        }
        .status-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 4px 6px;
            font-size: 7.5pt;
            color: #15803d;
            font-weight: bold;
            margin-bottom: 12px;
        }
        .sig-table {
            margin-top: 15px;
        }
        .sig-table td {
            text-align: center;
            vertical-align: bottom;
            font-size: 7pt;
            padding: 0 8px;
        }
        .sig-line {
            border-top: 1px solid #475569;
            padding-top: 2px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .footer-note {
            border-top: 1px dashed #cbd5e1;
            margin-top: 10px;
            padding-top: 4px;
            text-align: center;
            font-size: 7pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- 1. Header Section -->
    <table class="header-table" style="border-bottom: 2px solid #1e3a8a; padding-bottom: 8px; margin-bottom: 10px;">
        <tr>
            <td style="width: 60%;">
                <table>
                    <tr>
                        <?php if (!empty($hospital_logo) && file_exists($hospital_logo)) { ?>
                            <td style="width: 75px; vertical-align: top;">
                                <img src="<?php echo $hospital_logo; ?>" style="max-height: 52px; max-width: 70px;">
                            </td>
                        <?php } ?>
                        <td style="vertical-align: top;">
                            <div class="brand-title"><?php echo html_escape($hospital_title); ?></div>
                            <div class="brand-bangla"><?php echo html_escape($hospital_bangla); ?></div>
                            <div class="brand-meta">
                                <?php echo html_escape($hospital_address); ?><br>
                                Phone: <?php echo $phone_1; ?> | <?php echo $phone_2; ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="title-badge">WITHDRAWAL SLIP</div>
                <div style="margin-top: 2px; margin-bottom: 5px;">
                    <span class="subbadge">REFERRER WALLET DISBURSEMENT</span>
                </div>
                <table style="font-size: 8pt; float: right; width: auto;">
                    <tr>
                        <td style="color: #64748b; text-align: right; padding: 1px 4px;">Slip No:</td>
                        <td style="font-weight: bold; text-align: left; padding: 1px 4px;"><?php echo $slip_no; ?></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; text-align: right; padding: 1px 4px;">Txn ID:</td>
                        <td style="font-weight: bold; text-align: left; padding: 1px 4px;"><?php echo html_escape($withdrawal->withdrawal_id); ?></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; text-align: right; padding: 1px 4px;">Date:</td>
                        <td style="font-weight: bold; text-align: left; padding: 1px 4px;"><?php echo $txn_time_formatted; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 2. Dual Information Boxes -->
    <table style="margin-bottom: 6px;">
        <tr>
            <!-- Referrer Box -->
            <td style="width: 49%; vertical-align: top;">
                <div class="info-box">
                    <div class="box-title">Referrer Information</div>
                    <table>
                        <tr>
                            <td class="field-lbl">Name:</td>
                            <td class="field-val"><?php echo html_escape($referrer->name); ?></td>
                        </tr>
                        <tr>
                            <td class="field-lbl">Referrer ID:</td>
                            <td class="field-val">REF-<?php echo sprintf('%05d', $referrer->id); ?></td>
                        </tr>
                        <tr>
                            <td class="field-lbl">Category:</td>
                            <td class="field-val"><?php echo html_escape($referrer->type); ?></td>
                        </tr>
                        <tr>
                            <td class="field-lbl">Phone:</td>
                            <td class="field-val"><?php echo html_escape($referrer->phone); ?></td>
                        </tr>
                        <?php if (!empty($referrer->address)) { ?>
                            <tr>
                                <td class="field-lbl">Address:</td>
                                <td class="field-val"><?php echo html_escape($referrer->address); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </td>

            <td style="width: 2%;"></td>

            <!-- Disbursement Box -->
            <td style="width: 49%; vertical-align: top;">
                <div class="info-box">
                    <div class="box-title">Payment &amp; Recipient Details</div>
                    <table>
                        <tr>
                            <td class="field-lbl">Method:</td>
                            <td class="field-val"><?php echo html_escape($withdrawal->payment_method); ?></td>
                        </tr>
                        <tr>
                            <td class="field-lbl">Account / Info:</td>
                            <td class="field-val"><?php echo html_escape($withdrawal->account_number); ?></td>
                        </tr>
                        <tr>
                            <td class="field-lbl">Reference / Trx:</td>
                            <td class="field-val"><?php echo !empty($withdrawal->payment_reference) ? html_escape($withdrawal->payment_reference) : 'Cash Payment'; ?></td>
                        </tr>
                        <tr>
                            <td class="field-lbl">Authorized By:</td>
                            <td class="field-val"><?php echo html_escape($processor_name); ?></td>
                        </tr>
                        <?php if (!empty($withdrawal->admin_notes)) { ?>
                            <tr>
                                <td class="field-lbl">Notes:</td>
                                <td class="field-val"><?php echo html_escape($withdrawal->admin_notes); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- 3. Financial Breakdown Table -->
    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width: 50%;">Transaction Particulars</th>
                <th style="width: 25%; text-align: center;">Wallet Impact</th>
                <th style="width: 25%; text-align: right;">Amount (<?php echo $currency; ?> BDT)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Previous Available Wallet Balance</strong><br>
                    <span style="font-size: 7.5pt; color: #64748b;">Commission balance prior to this withdrawal</span>
                </td>
                <td style="text-align: center; color: #475569;">Opening Balance</td>
                <td style="text-align: right; font-weight: bold;">
                    <?php echo $currency . ' ' . number_format($previous_balance, 2); ?>
                </td>
            </tr>
            <tr style="background-color: #fffbeb;">
                <td>
                    <strong style="color: #b45309;">Disbursed Withdrawal Amount</strong><br>
                    <span style="font-size: 7.5pt; color: #64748b;">Disbursed via <?php echo html_escape($withdrawal->payment_method); ?> to <?php echo html_escape($withdrawal->account_number); ?></span>
                </td>
                <td style="text-align: center; font-weight: bold; color: #b45309;">DEBIT (-)</td>
                <td style="text-align: right; font-weight: bold; color: #b45309; font-size: 10pt;">
                    -<?php echo $currency . ' ' . number_format($withdrawal_amount, 2); ?>
                </td>
            </tr>
            <tr style="background-color: #f0fdf4; border-top: 1.5px solid #86efac;">
                <td>
                    <strong style="color: #15803d;">Remaining Available Wallet Balance</strong><br>
                    <span style="font-size: 7.5pt; color: #64748b;">Post-disbursement balance in referrer wallet</span>
                </td>
                <td style="text-align: center; font-weight: bold; color: #15803d;">CLOSING BALANCE</td>
                <td style="text-align: right; font-weight: bold; color: #15803d; font-size: 10.5pt;">
                    <?php echo $currency . ' ' . number_format($remaining_balance, 2); ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 4. Amount in Words -->
    <div class="in-words-box">
        <strong>Disbursed in Words:</strong> <?php echo amountInWords($withdrawal_amount); ?>
    </div>

    <!-- 5. Status Banner -->
    <table class="status-box">
        <tr>
            <td style="width: 60%; text-align: left;">
                &bull; Official Referral Disbursement Voucher &bull; Verified &amp; Paid
            </td>
            <td style="width: 40%; text-align: right; font-weight: bold;">
                Disbursed: <?php echo $currency . ' ' . number_format($withdrawal_amount, 2); ?>
            </td>
        </tr>
    </table>

    <!-- 6. Signatures Section -->
    <table class="sig-table">
        <tr>
            <td style="width: 33%;">
                <div style="height: 25px;"></div>
                <div class="sig-line">Referrer / Recipient</div>
                <div style="font-size: 7.5pt; color: #64748b;">Received in Full</div>
            </td>
            <td style="width: 33%;">
                <div style="height: 25px;"></div>
                <div class="sig-line">Prepared &amp; Processed By</div>
                <div style="font-size: 7.5pt; color: #64748b;">Accounts Dept.</div>
            </td>
            <td style="width: 33%;">
                <div style="height: 25px;"></div>
                <div class="sig-line">Authorized Signatory</div>
                <div style="font-size: 7.5pt; color: #64748b;">LifeCare Management</div>
            </td>
        </tr>
    </table>

    <!-- 7. Footer Section -->
    <div class="footer-note">
        Thank you for partnering with <strong>LifeCare Diagnostic Center</strong>. This voucher confirms official disbursement from your commission wallet.<br>
        Printed on: <?php echo $print_time; ?> &bull; Computer Generated Voucher &bull; INNOVA Computers BD
    </div>

</body>
</html>
