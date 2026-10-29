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

// Cashier / Bill Creator Information (Resolve staff name by user ID)
$cashier_name = '';
$user_id = !empty($expense->user) ? $expense->user : null;
if (!empty($user_id)) {
    // 1. Check staff tables by ion_user_id (Accountant, Receptionist, Laboratorist, Pharmacist)
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

    // 2. Fallback to users table
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

// Official Hospital Identity (Strict Lifecare Branding standard matching Money Receipt)
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

<!-- Lifecare Modern Invoice Stylesheets -->
<link rel="stylesheet" href="common/extranal/css/finance/modern_invoice.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="common/extranal/css/finance/expense_invoice.css?v=<?php echo time(); ?>">

<style>
@page {
    size: 148mm 210mm;
    margin: 4mm 5mm;
}
@media print {
    html, body {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        overflow: visible !important;
    }
    .wrapper,
    .content-wrapper,
    .main-sidebar,
    .main-header,
    .content-header,
    .main-footer,
    .site-footer,
    .no-print {
        display: none !important;
    }
    .content-wrapper,
    .content,
    .container-fluid {
        margin: 0 !important;
        padding: 0 !important;
        margin-left: 0 !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        background: transparent !important;
    }
    .invoice-screen-wrapper,
    .invoice-container-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        background: #ffffff !important;
    }
    .invoice-paper,
    .expense-a5-paper,
    .expense-b5-paper {
        width: 100% !important;
        max-width: 100% !important;
        min-height: auto !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        display: block !important;
    }
    .invoice-items-table thead th {
        background: #0e385d !important;
        color: #ffffff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .invoice-items-table tr {
        page-break-inside: avoid !important;
    }
    .invoice-bottom-grid {
        page-break-inside: avoid !important;
    }
    .invoice-footer-container {
        page-break-inside: avoid !important;
    }
}
</style>

<div class="content-wrapper bg-light">
    <!-- Breadcrumb header (Screen only) -->
    <section class="content-header no-print">
        <div class="container-fluid">
            <div class="row my-2 pl-1">
                <div class="col-sm-6">
                    <h1 class="font-weight-bold">
                        <i class="fas fa-file-invoice mr-2 text-primary"></i><?php echo lang('expense'); ?> <?php echo lang('invoice'); ?> #<?php echo $expense->id; ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home"><?php echo lang('home'); ?></a></li>
                        <li class="breadcrumb-item"><a href="finance/expense"><?php echo lang('all'); ?> <?php echo lang('expenses'); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo lang('expense'); ?> <?php echo lang('invoice'); ?> #<?php echo $expense->id; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Print Preview Screen -->
    <div class="invoice-screen-wrapper">
        
        <!-- Screen Action Toolbar (no-print) -->
        <div class="invoice-action-toolbar expense-toolbar-a5 no-print">
            <div class="invoice-toolbar-left">
                <a href="finance/expense" class="invoice-btn-action invoice-btn-back">
                    <i class="fas fa-arrow-left"></i> <?php echo lang('all'); ?> <?php echo lang('expenses'); ?>
                </a>

                <?php if ($this->ion_auth->in_group(array('admin', 'Accountant', 'superadmin'))) { ?>
                    <a href="finance/editExpense?id=<?php echo $expense->id; ?>" class="invoice-btn-action invoice-btn-edit">
                        <i class="fas fa-edit"></i> Edit Invoice
                    </a>
                <?php } ?>

                <span class="invoice-status-badge status-paid">
                    <i class="fas fa-check-circle mr-1"></i> PAID / RECORDED
                </span>
            </div>

            <div class="invoice-toolbar-right">
                <a href="finance/downloadExpenseInvoice?id=<?php echo $expense->id; ?>" class="invoice-btn-action invoice-btn-download" id="download" target="_blank">
                    <i class="fas fa-download"></i> <?php echo lang('download'); ?> PDF
                </a>

                <a href="javascript:void(0);" onclick="window.print();" class="invoice-btn-action invoice-btn-print">
                    <i class="fas fa-print"></i> <?php echo lang('print'); ?> (A5)
                </a>
            </div>
        </div>

        <!-- A5 Invoice Paper Container (148mm x 210mm) -->
        <div class="invoice-container-wrapper expense-a5-container">
            <div class="invoice-paper expense-a5-paper" id="printable-invoice">
                
                <div>
                    <!-- 1. HEADER SECTION (Hospital Identity & Expense Invoice Metadata) -->
                    <header class="invoice-header">
                        <!-- Left: Hospital Logo, Name, Bangla Name, Address, Phones -->
                        <div class="invoice-header-brand-group">
                            <div class="invoice-brand-top">
                                <?php if (!empty($hospital_logo) && file_exists($hospital_logo)) { ?>
                                    <img src="<?php echo $hospital_logo; ?>" alt="<?php echo html_escape($hospital_title); ?>" class="hospital-logo">
                                <?php } ?>
                                <div class="hospital-brand-titles">
                                    <h1 class="hospital-brand-title"><?php echo html_escape($hospital_title); ?></h1>
                                    <div class="hospital-brand-bangla"><?php echo html_escape($hospital_bangla); ?></div>
                                </div>
                            </div>

                            <div class="hospital-brand-address">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo html_escape($hospital_address); ?></span>
                            </div>

                            <div class="hospital-brand-phones">
                                <span class="phone-item"><i class="fas fa-phone-alt"></i> <?php echo $phone_1; ?></span>
                                <span class="phone-separator">|</span>
                                <span class="phone-item"><i class="fas fa-phone-alt"></i> <?php echo $phone_2; ?></span>
                            </div>
                        </div>

                        <!-- Right: EXPENSE INVOICE Title, Barcode & Metadata -->
                        <div class="invoice-header-meta">
                            <div class="invoice-title-large">EXPENSE INVOICE</div>
                            <div class="invoice-type-pill">OFFICIAL EXPENSE VOUCHER</div>
                            
                            <div class="invoice-header-barcode">
                                <img src="<?php echo site_url('lab/barcode'); ?>?text=<?php echo $expense_number; ?>&print=false&size=22&sizefactor=0.9" alt="Expense Barcode" class="barcode-img">
                            </div>

                            <table class="invoice-header-meta-table">
                                <tr>
                                    <td class="lbl">Invoice No</td>
                                    <td class="sep">:</td>
                                    <td class="val font-monospace"><strong><?php echo $expense_number; ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="lbl">Date</td>
                                    <td class="sep">:</td>
                                    <td class="val" style="font-family: inherit;"><?php echo $invoice_date . ' ' . $invoice_time; ?></td>
                                </tr>
                            </table>
                        </div>
                    </header>

                    <!-- 2. BILL TO & INVOICE INFORMATION (UNIFIED TABLE BOX) -->
                    <div class="patient-doctor-box">
                        <table class="patient-info-table">
                            <tr>
                                <td class="lbl">Bill To</td>
                                <td class="sep">:</td>
                                <td class="val">
                                    <strong><?php echo html_escape($hospital_title); ?></strong>
                                </td>
                                <td class="lbl">Invoice No</td>
                                <td class="sep">:</td>
                                <td class="val font-monospace"><strong><?php echo $expense_number; ?></strong></td>
                            </tr>
                            <tr>
                                <td class="lbl">Address</td>
                                <td class="sep">:</td>
                                <td class="val"><?php echo html_escape($hospital_address); ?></td>
                                <td class="lbl">Expense ID</td>
                                <td class="sep">:</td>
                                <td class="val font-monospace">#<?php echo $expense->id; ?></td>
                            </tr>
                            <tr>
                                <td class="lbl">Contact Phone</td>
                                <td class="sep">:</td>
                                <td class="val"><?php echo html_escape($phone_1); ?></td>
                                <td class="lbl">Invoice Date</td>
                                <td class="sep">:</td>
                                <td class="val"><?php echo $invoice_date; ?></td>
                            </tr>
                            <tr class="doctor-subrow">
                                <td class="lbl">Expense Category</td>
                                <td class="sep">:</td>
                                <td class="val" colspan="4">
                                    <strong><?php echo html_escape($expense->category); ?></strong>
                                    <span class="doc-badge ml-2"><i class="fas fa-tag mr-1"></i> Hospital Operating Expense</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- 3. EXPENSE DETAILS TABLE (SL, Description, Note, Amount) -->
                    <div class="invoice-items-table-wrapper">
                        <table class="invoice-items-table">
                            <thead>
                                <tr class="table-columns-header-row">
                                    <th class="col-num" style="width: 8%; text-align: center; color: #ffffff !important;">SL</th>
                                    <th class="col-desc" style="width: 38%; text-align: left; color: #ffffff !important;">DESCRIPTION</th>
                                    <th class="col-note" style="width: 34%; text-align: left; color: #ffffff !important;">NOTE</th>
                                    <th class="col-amount" style="width: 20%; text-align: right; color: #ffffff !important;">AMOUNT (BDT)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="col-num" style="text-align: center; font-weight: 600;">1</td>
                                    <td class="col-desc">
                                        <strong style="color: #0f172a; font-size: 11.5px;"><?php echo html_escape($expense->category); ?></strong>
                                    </td>
                                    <td class="col-note" style="color: #475569;">
                                        <?php echo !empty($expense->note) ? html_escape($expense->note) : '<span class="text-muted font-italic">-</span>'; ?>
                                    </td>
                                    <td class="col-amount" style="text-align: right; font-weight: 700; color: #0f172a;">
                                        <?php echo $currency . ' ' . number_format($expense->amount, 2); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 4. BOTTOM SECTION: STATUS, AMOUNT IN WORDS, EXPENSE SUMMARY & SIGNATURES -->
                    <div class="invoice-bottom-grid">
                        
                        <!-- Left Pane: Payment Status, Amount in Words, Prepared By -->
                        <div class="invoice-left-pane">
                            <div>
                                <div class="status-row-top">
                                    <span class="invoice-status-badge status-paid">
                                        <i class="fas fa-check-circle mr-1"></i> PAID / RECORDED
                                    </span>
                                    <span class="due-amount-banner banner-no-due">
                                        Paid in Full
                                    </span>
                                </div>

                                <!-- Amount in Words -->
                                <div class="in-words-row">
                                    <strong>In Words:</strong> 
                                    <?php echo invoiceAmountInWords($expense->amount); ?>
                                </div>

                                <div class="patient-instructions-box">
                                    <div><strong>Official Expense Voucher:</strong> Verified and passed for payment in accordance with hospital accounting procedures.</div>
                                </div>
                            </div>

                            <!-- Left Signature Block -->
                            <div class="sig-block-left">
                                <div class="sig-space" style="height: 25px;">&nbsp;</div>
                                <div class="sig-line">-----------------------------</div>
                                <div class="sig-label">PREPARED BY / ACCOUNTS</div>
                            </div>
                        </div>

                        <!-- Right Pane: Expense Summary & Authorized Signature -->
                        <div class="invoice-right-pane-wrapper">
                            <div class="invoice-right-pane">
                                <div class="bill-summary-title">
                                    <span>Expense Summary</span>
                                    <span class="currency-tag"><?php echo $currency; ?> BDT</span>
                                </div>

                                <table class="bill-summary-table">
                                    <tr>
                                        <td class="sum-lbl">Sub Total</td>
                                        <td class="sum-sep">:</td>
                                        <td class="sum-val"><?php echo $currency . ' ' . number_format($expense->amount, 2); ?></td>
                                    </tr>
                                    <!-- Highlighted Grand Total Row -->
                                    <tr class="sum-row-net">
                                        <td class="sum-lbl">Grand Total</td>
                                        <td class="sum-sep">:</td>
                                        <td class="sum-val"><?php echo $currency . ' ' . number_format($expense->amount, 2); ?></td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Right Signature Block -->
                            <div class="sig-block-right">
                                <div class="sig-space">&nbsp;</div>
                                <div class="sig-line">-----------------------------</div>
                                <div class="sig-label">AUTHORIZED SIGNATURE</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. FOOTER SECTION -->
                <footer class="invoice-footer-container">
                    <div class="footer-thankyou"><?php echo html_escape($hospital_title); ?> &bull; Hospital Administration & Accounts</div>
                    <div class="footer-address"><?php echo html_escape($hospital_address); ?></div>
                    <div class="footer-contact">
                        <i class="fas fa-phone-alt"></i> <?php echo $phone_1; ?> &nbsp;|&nbsp; <i class="fas fa-phone-alt"></i> <?php echo $phone_2; ?>
                    </div>
                    <div class="footer-note">
                        Printed: <?php echo $print_date; ?> &bull; Computer Generated Expense Invoice &bull; Software by INNOVA
                    </div>
                </footer>

            </div> <!-- /.invoice-paper -->
        </div> <!-- /.invoice-container-wrapper -->
    </div> <!-- /.invoice-screen-wrapper -->
</div> <!-- /.content-wrapper -->

<!-- Footer scripts -->
<script src="common/js/codearistos.min.js"></script>
<script src="common/extranal/js/finance/expense_invoice.js"></script>