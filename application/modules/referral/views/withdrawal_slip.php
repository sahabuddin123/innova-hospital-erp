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

<link rel="stylesheet" href="common/extranal/css/finance/modern_invoice.css?v=<?php echo time(); ?>">

<style>
/* ISO A5 Portrait Specification (148mm x 210mm) */
@page {
    size: 148mm 210mm;
    margin: 4mm 5mm;
}

@media print {
    @page {
        size: 148mm 210mm;
        margin: 4mm 5mm !important;
    }

    html, body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        overflow: visible !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Hide ALL non-printable AdminLTE elements */
    .no-print,
    .main-header,
    .main-sidebar,
    .content-header,
    .invoice-action-toolbar,
    .main-footer,
    footer.main-footer,
    .site-footer,
    .sidebar,
    #sidebar,
    .modal,
    .modal-backdrop,
    #loader,
    aside,
    nav {
        display: none !important;
    }

    /* Force reset all wrapper margins and widths from AdminLTE & changes.css */
    body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper,
    body .content-wrapper,
    .wrapper,
    .content-wrapper,
    .invoice-screen-wrapper,
    .invoice-container-wrapper,
    .container,
    .container-fluid {
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        position: static !important;
        overflow: visible !important;
        transform: none !important;
        -webkit-transform: none !important;
        box-shadow: none !important;
        border: none !important;
        margin-left: 0 !important;
        left: 0 !important;
    }

    .ws-screen-wrapper {
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
        min-height: 0 !important;
    }

    .ws-container-wrap {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .ws-paper {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        display: block !important;
        box-sizing: border-box !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        overflow: visible !important;
    }
}

/* Screen Wrapper */
.ws-screen-wrapper {
    background-color: #f1f5f9;
    min-height: calc(100vh - 110px);
    padding: 16px 10px 36px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #1e293b;
}

.ws-toolbar {
    width: 100%;
    max-width: 148mm;
    margin: 0 auto 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    padding: 8px 14px;
    background: #ffffff;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    border: 1px solid #e2e8f0;
}

.ws-container-wrap {
    width: 100%;
    max-width: 148mm;
    margin: 0 auto;
    overflow-x: auto;
    box-sizing: border-box;
}

.ws-paper {
    width: 100%;
    max-width: 148mm;
    box-sizing: border-box;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 12px 14px;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    color: #1e293b;
    page-break-inside: avoid;
    break-inside: avoid;
}

/* Header Table & Branding */
.ws-header-table {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse;
    border-bottom: 2.5px solid #1e3a8a;
    padding-bottom: 6px;
    margin-bottom: 8px;
}

.ws-header-table td {
    vertical-align: top;
    padding: 0;
}

.ws-brand-title {
    font-size: 14px;
    font-weight: 800;
    color: #1e3a8a;
    letter-spacing: -0.2px;
    margin: 0;
    line-height: 1.15;
    text-transform: uppercase;
}

.ws-brand-bangla {
    font-family: 'Hind Siliguri', 'Inter', sans-serif;
    font-size: 11px;
    font-weight: 700;
    color: #0d9488;
    margin-top: 1px;
    margin-bottom: 2px;
    line-height: 1.2;
}

.ws-brand-meta {
    font-size: 8.5px;
    color: #475569;
    line-height: 1.25;
}

.ws-title-badge {
    font-size: 14px;
    font-weight: 800;
    color: #1e3a8a;
    letter-spacing: 0.4px;
    line-height: 1;
    margin-bottom: 2px;
    text-align: right;
}

.ws-subbadge {
    display: inline-block;
    background: #dbeafe;
    color: #1e40af;
    padding: 1.5px 6px;
    border-radius: 9999px;
    font-size: 7.5px;
    font-weight: 700;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    text-align: right;
    margin-bottom: 4px;
}

.ws-meta-table {
    font-size: 8.5px;
    border-collapse: collapse;
    float: right;
}

.ws-meta-table td {
    padding: 1px 2px;
}

.ws-meta-table .lbl {
    font-weight: 600;
    color: #64748b;
    text-align: right;
}

.ws-meta-table .val {
    font-weight: 700;
    color: #0f172a;
    text-align: left;
}

/* Unified 2-Column Info Table (100% Fixed Width) */
.ws-info-unified-table {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse;
    margin-bottom: 8px;
}

.ws-info-unified-table td {
    vertical-align: top;
    padding: 0;
}

.ws-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 6px 8px;
    box-sizing: border-box;
}

.ws-box-header {
    font-size: 9.5px;
    font-weight: 700;
    color: #1e3a8a;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 3px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.ws-sub-table {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse;
    font-size: 9px;
}

.ws-sub-table td {
    padding: 1.5px 1px;
    vertical-align: top;
}

.ws-sub-table .lbl {
    width: 36%;
    color: #64748b;
    font-weight: 600;
}

.ws-sub-table .sep {
    width: 4%;
    color: #94a3b8;
    text-align: center;
}

.ws-sub-table .val {
    width: 60%;
    color: #0f172a;
    font-weight: 600;
}

/* Financial Ledger Table (100% Fixed Width) */
.ws-ledger-table {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse;
    font-size: 9px;
    border: 1px solid #cbd5e1;
    margin-bottom: 8px;
    box-sizing: border-box;
}

.ws-ledger-table th,
.ws-ledger-table td {
    overflow: hidden;
    word-wrap: break-word;
    box-sizing: border-box;
}

.ws-ledger-table thead th {
    background: #1e3a8a;
    color: #ffffff;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 5px 6px;
    font-size: 8.5px;
    border: none;
}

.ws-ledger-table tbody td {
    padding: 5px 6px;
    border-bottom: 1px solid #e2e8f0;
    color: #1e293b;
}

.ws-ledger-table .row-prev td {
    background: #ffffff;
}

.ws-ledger-table .row-withdrawal td {
    background: #fffbeb;
    font-weight: 700;
    color: #b45309;
}

.ws-ledger-table .row-remaining td {
    background: #f0fdf4;
    font-weight: 800;
    color: #15803d;
    font-size: 10px;
    border-top: 1.5px solid #86efac;
}

/* In-Words Card */
.ws-in-words-card {
    background: #f8fafc;
    border-left: 3px solid #1e3a8a;
    padding: 4px 8px;
    font-size: 8.5px;
    color: #334155;
    margin-bottom: 8px;
    border-radius: 0 4px 4px 0;
}

/* Official Status Bar */
.ws-status-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 4px;
    padding: 4px 8px;
    margin-bottom: 12px;
    font-size: 8.5px;
    width: 100%;
    box-sizing: border-box;
}

.ws-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-weight: 800;
    color: #15803d;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* Signatures Table (100% Fixed Width) */
.ws-sig-table {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse;
    margin-top: 16px;
    box-sizing: border-box;
}

.ws-sig-table td {
    text-align: center;
    vertical-align: bottom;
    padding: 0 6px;
    box-sizing: border-box;
}

.ws-sig-name {
    font-weight: 600;
    font-size: 8.5px;
    margin-bottom: 2px;
    color: #0f172a;
}

.ws-sig-line {
    border-top: 1px solid #475569;
    margin-bottom: 2px;
}

.ws-sig-label {
    font-size: 8px;
    font-weight: 700;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.2px;
}

.ws-sig-sub {
    font-size: 7.5px;
    color: #64748b;
}

/* Footer Note */
.ws-footer {
    border-top: 1px dashed #cbd5e1;
    margin-top: 10px;
    padding-top: 4px;
    text-align: center;
    font-size: 7.5px;
    color: #64748b;
    line-height: 1.35;
}
</style>

<div class="content-wrapper bg-white" style="margin: 0; padding: 0;">
    <div class="ws-screen-wrapper">
        <!-- Action Toolbar (No-Print) -->
        <div class="ws-toolbar no-print">
            <div class="d-flex align-items-center gap-2">
                <a href="referral/statement?referrer_id=<?php echo $referrer->id; ?>" class="btn btn-sm btn-outline-secondary mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Referrer Statement
                </a>
                <a href="referral/withdrawals" class="btn btn-sm btn-outline-secondary mr-2">
                    <i class="fas fa-list mr-1"></i> All Withdrawals
                </a>
                <span class="badge badge-success px-2 py-1">
                    <i class="fas fa-check-circle mr-1"></i> Status: PAID
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                <button type="button" class="btn btn-sm btn-primary px-3 mr-2 shadow-sm" onclick="window.print();">
                    <i class="fas fa-print mr-1"></i> Print Slip (A5)
                </button>
                <a href="referral/downloadWithdrawalSlip?id=<?php echo $withdrawal->id; ?>" class="btn btn-sm btn-success px-3 shadow-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Download PDF
                </a>
            </div>
        </div>

        <!-- Printable Withdrawal Slip Container (A5 Paper - 148mm x 210mm) -->
        <div class="ws-container-wrap">
            <div class="ws-paper" id="printable-slip">

                <!-- 1. Header Section (Fixed 2-Column Table) -->
                <table class="ws-header-table">
                    <tr>
                        <td style="width: 58%;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <?php if (!empty($hospital_logo) && file_exists($hospital_logo)) { ?>
                                        <td style="width: 55px; vertical-align: top; padding-right: 6px;">
                                            <img src="<?php echo $hospital_logo; ?>" alt="Logo" class="ws-logo" style="max-height: 42px; max-width: 52px; object-fit: contain;">
                                        </td>
                                    <?php } ?>
                                    <td style="vertical-align: top;">
                                        <h1 class="ws-brand-title"><?php echo html_escape($hospital_title); ?></h1>
                                        <div class="ws-brand-bangla"><?php echo html_escape($hospital_bangla); ?></div>
                                        <div class="ws-brand-meta">
                                            <i class="fas fa-map-marker-alt text-primary mr-1"></i> <?php echo html_escape($hospital_address); ?><br>
                                            <i class="fas fa-phone-alt text-primary mr-1"></i> <?php echo $phone_1; ?> &nbsp;|&nbsp; <?php echo $phone_2; ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 42%; text-align: right;">
                            <div class="ws-title-badge">WITHDRAWAL SLIP</div>
                            <div class="ws-subbadge">Referrer Wallet Disbursement</div>
                            
                            <div style="margin-bottom: 2px;">
                                <img src="<?php echo site_url('lab/barcode'); ?>?text=<?php echo urlencode($withdrawal->withdrawal_id); ?>&print=false&size=16&sizefactor=0.8" alt="Barcode" style="max-height: 18px; max-width: 110px;">
                            </div>

                            <table class="ws-meta-table">
                                <tr>
                                    <td class="lbl">Slip No</td>
                                    <td style="color:#94a3b8;">:</td>
                                    <td class="val font-monospace"><?php echo $slip_no; ?></td>
                                </tr>
                                <tr>
                                    <td class="lbl">Txn ID</td>
                                    <td style="color:#94a3b8;">:</td>
                                    <td class="val font-monospace"><?php echo html_escape($withdrawal->withdrawal_id); ?></td>
                                </tr>
                                <tr>
                                    <td class="lbl">Date &amp; Time</td>
                                    <td style="color:#94a3b8;">:</td>
                                    <td class="val"><?php echo $txn_time_formatted; ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- 2. Dual Information Boxes (Fixed 50/50 Table) -->
                <table class="ws-info-unified-table">
                    <tr>
                        <!-- Referrer Info Box -->
                        <td style="width: 50%; padding-right: 4px;">
                            <div class="ws-box">
                                <div class="ws-box-header">
                                    <i class="fas fa-user-tie text-primary mr-1"></i> Referrer Details
                                </div>
                                <table class="ws-sub-table">
                                    <tr>
                                        <td class="lbl">Name</td>
                                        <td class="sep">:</td>
                                        <td class="val"><strong><?php echo html_escape($referrer->name); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="lbl">Referrer ID</td>
                                        <td class="sep">:</td>
                                        <td class="val font-monospace">REF-<?php echo sprintf('%05d', $referrer->id); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="lbl">Category</td>
                                        <td class="sep">:</td>
                                        <td class="val"><span class="badge badge-info px-1 py-0" style="font-size: 8px;"><?php echo html_escape($referrer->type); ?></span></td>
                                    </tr>
                                    <tr>
                                        <td class="lbl">Phone</td>
                                        <td class="sep">:</td>
                                        <td class="val"><?php echo html_escape($referrer->phone); ?></td>
                                    </tr>
                                    <?php if (!empty($referrer->address)) { ?>
                                        <tr>
                                            <td class="lbl">Address</td>
                                            <td class="sep">:</td>
                                            <td class="val"><?php echo html_escape($referrer->address); ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </td>

                        <!-- Disbursement / Payment Info Box -->
                        <td style="width: 50%; padding-left: 4px;">
                            <div class="ws-box">
                                <div class="ws-box-header">
                                    <i class="fas fa-money-check-alt text-success mr-1"></i> Payment &amp; Recipient Info
                                </div>
                                <table class="ws-sub-table">
                                    <tr>
                                        <td class="lbl">Method</td>
                                        <td class="sep">:</td>
                                        <td class="val"><strong><?php echo html_escape($withdrawal->payment_method); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="lbl">Account / Number</td>
                                        <td class="sep">:</td>
                                        <td class="val font-monospace"><code><?php echo html_escape($withdrawal->account_number); ?></code></td>
                                    </tr>
                                    <tr>
                                        <td class="lbl">Payment Ref</td>
                                        <td class="sep">:</td>
                                        <td class="val"><?php echo !empty($withdrawal->payment_reference) ? html_escape($withdrawal->payment_reference) : '<span class="text-muted">Direct Cash / Disbursed</span>'; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="lbl">Authorized By</td>
                                        <td class="sep">:</td>
                                        <td class="val"><?php echo html_escape($processor_name); ?></td>
                                    </tr>
                                    <?php if (!empty($withdrawal->admin_notes)) { ?>
                                        <tr>
                                            <td class="lbl">Remarks</td>
                                            <td class="sep">:</td>
                                            <td class="val text-muted"><?php echo html_escape($withdrawal->admin_notes); ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- 3. Financial Breakdown Table (Fixed 100% Table) -->
                <table class="ws-ledger-table">
                    <thead>
                        <tr>
                            <th style="width: 52%;">Transaction Particulars</th>
                            <th style="width: 24%; text-align: center;">Wallet Impact</th>
                            <th style="width: 24%; text-align: right;">Amount (<?php echo $currency; ?> BDT)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="row-prev">
                            <td>
                                <strong>Previous Available Wallet Balance</strong>
                                <br><small class="text-muted">Balance prior to this withdrawal</small>
                            </td>
                            <td style="text-align: center; color: #475569;">Opening Balance</td>
                            <td style="text-align: right; font-weight: 700; color: #1e293b;">
                                <?php echo $currency . ' ' . number_format($previous_balance, 2); ?>
                            </td>
                        </tr>
                        <tr class="row-withdrawal">
                            <td>
                                <strong class="text-danger">Disbursed Withdrawal Amount</strong>
                                <br><small class="text-muted">Payment disbursed via <?php echo html_escape($withdrawal->payment_method); ?> to <?php echo html_escape($withdrawal->account_number); ?></small>
                            </td>
                            <td style="text-align: center; color: #b45309;">
                                <span class="badge badge-warning px-1 py-0" style="font-size: 7.5px;">DEBIT (-)</span>
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #b45309; font-size: 11px;">
                                -<?php echo $currency . ' ' . number_format($withdrawal_amount, 2); ?>
                            </td>
                        </tr>
                        <tr class="row-remaining">
                            <td>
                                <strong>Remaining Available Wallet Balance</strong>
                                <br><small class="text-muted">Post-disbursement balance in referrer wallet</small>
                            </td>
                            <td style="text-align: center; color: #15803d;">
                                <span class="badge badge-success px-1 py-0" style="font-size: 7.5px;">CLOSING BALANCE</span>
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #15803d; font-size: 11px;">
                                <?php echo $currency . ' ' . number_format($remaining_balance, 2); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- 4. Amount in Words & Status -->
                <div class="ws-in-words-card">
                    <strong>Disbursed in Words:</strong> <?php echo amountInWords($withdrawal_amount); ?>
                </div>

                <div class="ws-status-bar">
                    <div class="ws-status-pill">
                        <i class="fas fa-shield-alt mr-1"></i> Official Disbursement Voucher &bull; Verified &amp; Paid
                    </div>
                    <div class="text-muted font-weight-bold">
                        Disbursed: <?php echo $currency . ' ' . number_format($withdrawal_amount, 2); ?>
                    </div>
                </div>

                <!-- 5. Signature Section (Fixed 3-Column Table) -->
                <table class="ws-sig-table">
                    <tr>
                        <td style="width: 33.33%;">
                            <div class="ws-sig-space" style="height: 26px;"></div>
                            <div class="ws-sig-line"></div>
                            <div class="ws-sig-label">Referrer / Recipient</div>
                            <div class="ws-sig-sub">Received in Full</div>
                        </td>
                        <td style="width: 33.33%;">
                            <div class="ws-sig-space" style="height: 26px;"></div>
                            <div class="ws-sig-line"></div>
                            <div class="ws-sig-label">Prepared &amp; Processed By</div>
                            <div class="ws-sig-sub">Accounts Dept.</div>
                        </td>
                        <td style="width: 33.33%;">
                            <div class="ws-sig-space" style="height: 26px;"></div>
                            <div class="ws-sig-line"></div>
                            <div class="ws-sig-label">Authorized Signatory</div>
                            <div class="ws-sig-sub">LifeCare Management</div>
                        </td>
                    </tr>
                </table>

                <!-- 6. Footer Section -->
                <footer class="ws-footer">
                    <div>Thank you for partnering with <strong>LifeCare Diagnostic Center</strong>. This voucher confirms official disbursement from your commission wallet.</div>
                    <div>Printed on: <?php echo $print_time; ?> &bull; Computer Generated Voucher &bull; INNOVA Computers BD</div>
                </footer>

            </div>
        </div>
    </div>
</div>
