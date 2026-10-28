<?php
// Initialize data objects safely
$currency = !empty($settings->currency) ? $settings->currency : '৳';

// Formatted Identifiers
$expense_id_raw = !empty($expense->id) ? $expense->id : 0;
$expense_number = 'EXP-' . sprintf('%08d', $expense_id_raw);

// Dates with Asia/Dhaka timezone
date_default_timezone_set('Asia/Dhaka');
$invoice_date = !empty($expense->date) ? date('d/m/Y', $expense->date) : date('d/m/Y');
$invoice_time = !empty($expense->date) ? date('h:i A', $expense->date) : date('h:i A');
$print_date = date('d/m/Y h:i A');

// Cashier / Bill Creator Information
$cashier_name = '';
$user_id = !empty($expense->user) ? $expense->user : null;
if (!empty($user_id)) {
    $staff = $this->db->get_where('accountant', array('ion_user_id' => $user_id))->row();
    if (empty($staff) || empty($staff->name)) {
        $staff = $this->db->get_where('receptionist', array('ion_user_id' => $user_id))->row();
    }
    if (empty($staff) || empty($staff->name)) {
        $staff = $this->db->get_where('laboratorist', array('ion_user_id' => $user_id))->row();
    }
    if (empty($staff) || empty($staff->name)) {
        $staff = $this->db->get_where('pharmacist', array('ion_user_id' => $user_id))->row();
    }
    if (!empty($staff) && !empty($staff->name)) {
        $cashier_name = trim($staff->name);
    }
    if (empty($cashier_name)) {
        $cashier_user = $this->db->get_where('users', array('id' => $user_id))->row();
        if (!empty($cashier_user)) {
            if (!empty($cashier_user->username)) {
                $cashier_name = trim($cashier_user->username);
            } elseif (!empty($cashier_user->first_name) || !empty($cashier_user->last_name)) {
                $cashier_name = trim(($cashier_user->first_name ?? '') . ' ' . ($cashier_user->last_name ?? ''));
            }
        }
    }
}
if (empty($cashier_name)) {
    $cashier_name = 'Accounts Department';
}

// Official Hospital Identity
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

// Safe Helper for Amount in Words
if (!function_exists('invoiceAmountInWords')) {
    function invoiceAmountInWords($number) {
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
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Expense Invoice <?php echo $expense_number; ?></title>
<style>
body {
    font-family: freeserif, sans-serif;
    font-size: 8.5pt;
    color: #334155;
    margin: 0;
    padding: 0;
}
.header-table {
    width: 100%;
    border-collapse: collapse;
    border-bottom: 2pt solid #1e3a8a;
    padding-bottom: 6px;
    margin-bottom: 8px;
}
.brand-title {
    font-size: 13.5pt;
    font-weight: bold;
    color: #0f2942;
    text-transform: uppercase;
    letter-spacing: -0.2px;
}
.brand-bangla {
    font-family: freeserif, sans-serif;
    font-size: 11pt;
    font-weight: normal;
    color: #0f766e;
    margin-top: 1px;
}
.brand-addr {
    font-size: 7.5pt;
    color: #475569;
    margin-top: 2px;
}
.brand-phone {
    font-size: 8pt;
    font-weight: bold;
    color: #1e293b;
    margin-top: 1px;
}
.invoice-title {
    font-size: 13pt;
    font-weight: bold;
    color: #1e3a8a;
    text-align: right;
    letter-spacing: 0.5px;
}
.invoice-type {
    font-size: 7pt;
    font-weight: bold;
    color: #1e3a8a;
    background: #e0f2fe;
    text-align: right;
    padding: 1px 6px;
    border-radius: 2px;
}
.meta-table {
    font-size: 8pt;
    margin-top: 3px;
    float: right;
}
.meta-table td {
    padding: 1.5px 2px;
}
.info-box {
    width: 100%;
    border: 1pt solid #cbd5e1;
    background: #f8fafc;
    border-radius: 4px;
    margin-bottom: 8px;
    padding: 5px 7px;
}
.info-table {
    width: 100%;
    font-size: 8pt;
    border-collapse: collapse;
}
.info-table td {
    padding: 2.5px 3px;
}
.lbl {
    color: #64748b;
    font-weight: bold;
}
.doc-badge {
    background: #e0f2fe;
    color: #0284c7;
    border: 0.5pt solid #93c5fd;
    padding: 1px 5px;
    border-radius: 3px;
    font-size: 7.2pt;
    font-weight: bold;
}
.items-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8.5pt;
    margin-bottom: 8px;
}
.items-table th {
    background: #1e3a8a;
    color: #ffffff;
    font-size: 8pt;
    font-weight: bold;
    text-transform: uppercase;
    padding: 6px 7px;
    border: none;
}
.items-table td {
    padding: 6px 7px;
    border-bottom: 0.5pt solid #e2e8f0;
}
.items-table tr:nth-child(even) td {
    background: #fafcff;
}
.items-table tr {
    page-break-inside: avoid;
}
.bottom-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 6px;
    margin-bottom: 8px;
    page-break-inside: avoid;
}
.bottom-table td {
    vertical-align: top;
}
.status-card {
    background: #f8fafc;
    border: 1pt solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
}
.badge-status {
    display: inline-block;
    background: #dcfce7;
    color: #15803d;
    font-size: 7.5pt;
    font-weight: bold;
    padding: 2px 7px;
    border-radius: 8px;
}
.badge-paid {
    display: inline-block;
    background: #f0fdf4;
    color: #166534;
    border: 1pt solid #86efac;
    font-size: 9pt;
    font-weight: bold;
    padding: 2px 8px;
    border-radius: 3px;
    margin-left: 8px;
}
.in-words {
    font-size: 7.5pt;
    color: #334155;
    padding-top: 4px;
    margin-top: 4px;
    border-top: 0.5pt dashed #e2e8f0;
}
.instructions {
    font-size: 7pt;
    color: #475569;
    background: #ffffff;
    border: 0.5pt solid #e2e8f0;
    padding: 4px 6px;
    border-radius: 3px;
    margin-top: 4px;
    line-height: 1.35;
}
.summary-card {
    border: 1pt solid #cbd5e1;
    background: #ffffff;
    border-radius: 4px;
    padding: 6px 8px;
}
.summary-table {
    width: 100%;
    font-size: 8pt;
    border-collapse: collapse;
}
.summary-table td {
    padding: 3px 0;
}
.sum-title {
    font-size: 8pt;
    font-weight: bold;
    color: #1e3a8a;
    border-bottom: 0.5pt solid #e2e8f0;
    padding-bottom: 3px;
    margin-bottom: 4px;
}
.sum-net td {
    font-weight: bold;
    font-size: 9.5pt;
    color: #1e3a8a;
    border-top: 1pt solid #cbd5e1;
    border-bottom: 1pt solid #cbd5e1;
    padding: 4px 0;
}
.sig-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 28px;
    page-break-inside: avoid;
}
.sig-line {
    font-size: 8pt;
    color: #64748b;
    margin-bottom: 2px;
}
.sig-lbl {
    font-size: 7pt;
    font-weight: bold;
    color: #64748b;
    text-transform: uppercase;
}
.sig-val {
    font-size: 8pt;
    font-weight: bold;
    color: #0f172a;
}
.footer-sec {
    border-top: 1pt solid #cbd5e1;
    padding-top: 5px;
    text-align: center;
    font-size: 7pt;
    color: #64748b;
    width: 100%;
    page-break-inside: avoid;
}
.footer-thanks {
    font-size: 8pt;
    font-weight: bold;
    color: #1e3a8a;
}
</style>
</head>
<body>

<htmlpageheader name="continuationHeader">
    <div style="border-bottom: 1.5pt solid #1e3a8a; padding-bottom: 3px; font-size: 8pt; color: #1e3a8a; font-family: freeserif, sans-serif;">
        <table style="width: 100%;">
            <tr>
                <td style="font-weight: bold; font-size: 9.5pt;"><?php echo html_escape($hospital_title); ?> — EXPENSE INVOICE</td>
                <td style="text-align: right;"><strong>Invoice No:</strong> <?php echo $expense_number; ?> &nbsp;|&nbsp; <strong>Page</strong> {PAGENO}/{nbpg}</td>
            </tr>
        </table>
    </div>
</htmlpageheader>
<sethtmlpageheader name="continuationHeader" value="on" show-this-page="0" />

<!-- 1. Header Section -->
<table class="header-table">
    <tr>
        <td style="width: 60%; vertical-align: top;">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 52px; vertical-align: top;">
                        <?php if (!empty($hospital_logo) && file_exists($hospital_logo)) { ?>
                            <img src="<?php echo $hospital_logo; ?>" width="48" style="vertical-align: top;">
                        <?php } ?>
                    </td>
                    <td style="vertical-align: top; padding-left: 6px;">
                        <div class="brand-title"><?php echo html_escape($hospital_title); ?></div>
                        <div class="brand-bangla"><?php echo html_escape($hospital_bangla); ?></div>
                        <div class="brand-addr"><?php echo html_escape($hospital_address); ?></div>
                        <div class="brand-phone">Phone: <?php echo $phone_1; ?> | <?php echo $phone_2; ?></div>
                    </td>
                </tr>
            </table>
        </td>
        <td style="width: 40%; vertical-align: top; text-align: right;">
            <div class="invoice-title">EXPENSE INVOICE</div>
            <div style="text-align: right; margin: 2px 0;">
                <span class="invoice-type">OFFICIAL EXPENSE VOUCHER</span>
            </div>
            <div style="text-align: right; margin: 2px 0;">
                <img src="<?php echo site_url('lab/barcode'); ?>?text=<?php echo $expense_number; ?>&print=false&size=20&sizefactor=0.85" height="22">
            </div>
            <table class="meta-table" align="right">
                <tr><td class="lbl">Invoice No:</td><td style="font-weight: bold;"><?php echo $expense_number; ?></td></tr>
                <tr><td class="lbl">Date:</td><td><?php echo $invoice_date . ' ' . $invoice_time; ?></td></tr>
            </table>
        </td>
    </tr>
</table>

<!-- 2. Bill To & Invoice Info Box -->
<div class="info-box">
    <table class="info-table">
        <tr>
            <td class="lbl" style="width: 18%;">Bill To</td>
            <td style="width: 2%;">:</td>
            <td style="width: 40%;"><strong><?php echo html_escape($hospital_title); ?></strong></td>
            <td class="lbl" style="width: 18%;">Invoice No</td>
            <td style="width: 2%;">:</td>
            <td style="width: 20%;"><strong><?php echo $expense_number; ?></strong></td>
        </tr>
        <tr>
            <td class="lbl">Address</td>
            <td>:</td>
            <td><?php echo html_escape($hospital_address); ?></td>
            <td class="lbl">Expense ID</td>
            <td>:</td>
            <td>#<?php echo $expense->id; ?></td>
        </tr>
        <tr>
            <td class="lbl">Contact Phone</td>
            <td>:</td>
            <td><?php echo html_escape($phone_1); ?></td>
            <td class="lbl">Invoice Date</td>
            <td>:</td>
            <td><?php echo $invoice_date; ?></td>
        </tr>
        <tr>
            <td class="lbl">Expense Category</td>
            <td>:</td>
            <td colspan="4">
                <strong><?php echo html_escape($expense->category); ?></strong>
                <span class="doc-badge" style="margin-left: 6px;">Hospital Operating Expense</span>
            </td>
        </tr>
    </table>
</div>

<!-- 3. Expense Details Table -->
<table class="items-table">
    <thead>
        <tr>
            <th style="width: 8%; text-align: center;">SL</th>
            <th style="width: 38%; text-align: left;">DESCRIPTION</th>
            <th style="width: 34%; text-align: left;">NOTE</th>
            <th style="width: 20%; text-align: right;">AMOUNT (BDT)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center; font-weight: bold;">1</td>
            <td style="font-weight: bold; color: #0f172a;"><?php echo html_escape($expense->category); ?></td>
            <td style="color: #475569;"><?php echo !empty($expense->note) ? html_escape($expense->note) : '-'; ?></td>
            <td style="text-align: right; font-weight: bold; color: #0f172a;"><?php echo $currency . ' ' . number_format($expense->amount, 2); ?></td>
        </tr>
    </tbody>
</table>

<!-- 4. Bottom Section: Status, Words, Totals & Signatures -->
<table class="bottom-table">
    <tr>
        <!-- Left Side: Status & In Words -->
        <td style="width: 58%; padding-right: 10px;">
            <div class="status-card">
                <div>
                    <span class="badge-status">PAID / RECORDED</span>
                    <span class="badge-paid">Paid in Full</span>
                </div>
                <div class="in-words">
                    <strong>In Words:</strong> <?php echo invoiceAmountInWords($expense->amount); ?>
                </div>
                <div class="instructions">
                    <strong>Official Expense Voucher:</strong> Verified and passed for payment in accordance with hospital accounting procedures.
                </div>
            </div>

            <!-- Prepared By Signature Block -->
            <div style="margin-top: 30px;">
                <div style="height: 25px;"></div>
                <div class="sig-line">-----------------------------</div>
                <div class="sig-lbl">PREPARED BY / ACCOUNTS</div>
            </div>
        </td>

        <!-- Right Side: Bill Summary & Authorized Signature -->
        <td style="width: 42%;">
            <div class="summary-card">
                <div class="sum-title">
                    <table style="width: 100%;">
                        <tr>
                            <td>Expense Summary</td>
                            <td style="text-align: right; font-size: 7pt;"><?php echo $currency; ?> BDT</td>
                        </tr>
                    </table>
                </div>
                <table class="summary-table">
                    <tr>
                        <td class="lbl">Sub Total</td>
                        <td style="text-align: right;"><?php echo $currency . ' ' . number_format($expense->amount, 2); ?></td>
                    </tr>
                    <tr class="sum-net">
                        <td>Grand Total</td>
                        <td style="text-align: right;"><?php echo $currency . ' ' . number_format($expense->amount, 2); ?></td>
                    </tr>
                </table>
            </div>

            <!-- Authorized Signature Block -->
            <div style="margin-top: 40px; text-align: right;">
                <div class="sig-line">-----------------------------</div>
                <div class="sig-lbl">AUTHORIZED SIGNATURE</div>
            </div>
        </td>
    </tr>
</table>

<!-- 5. Footer Section -->
<div class="footer-sec" style="margin-top: 25px;">
    <div class="footer-thanks"><?php echo html_escape($hospital_title); ?> &bull; Hospital Administration & Accounts</div>
    <div><?php echo html_escape($hospital_address); ?> &nbsp;|&nbsp; Phone: <?php echo $phone_1; ?> / <?php echo $phone_2; ?></div>
    <div style="margin-top: 2px;">
        Printed: <?php echo $print_date; ?> &bull; Computer Generated Expense Invoice &bull; Software by INNOVA
    </div>
</div>

</body>
</html>
