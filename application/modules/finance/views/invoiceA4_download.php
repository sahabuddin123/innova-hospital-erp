<?php
// Initialize data objects safely
$patient_info = !empty($payment->patient) ? $this->db->get_where('patient', array('id' => $payment->patient))->row() : null;

// Currency: BDT (৳)
$currency = '<span style="font-weight: normal; font-style: normal;">৳</span>';

// Formatted Identifiers (10-digit zero padded for diagnostic center record standards)
$vn = sprintf('%010d', $payment->id);
$hn = !empty($patient_info->id) ? sprintf('%010d', $patient_info->id) : (!empty($payment->patient) ? sprintf('%010d', $payment->patient) : '0000000000');

// Dates with Asia/Dhaka timezone
date_default_timezone_set('Asia/Dhaka');
$invoice_date = !empty($payment->date) ? date('d/m/Y', $payment->date) : date('d/m/Y');
$invoice_time = !empty($payment->date) ? date('h:i A', $payment->date) : date('h:i A');
$visit_date = !empty($payment->date) ? date('d/m/Y', $payment->date) : date('d/m/Y');
$print_date = date('d/m/Y h:i A');

// Patient Details
$patient_name = !empty($patient_info->name) ? $patient_info->name : (!empty($payment->patient_name) ? $payment->patient_name : '');
$patient_phone = !empty($patient_info->phone) ? $patient_info->phone : (!empty($payment->patient_phone) ? $payment->patient_phone : '');

$age_str = '';
if (!empty($patient_info) && !empty($patient_info->age)) {
    $age_parts = explode('-', $patient_info->age);
    if (count($age_parts) >= 2) {
        $age_str = trim($age_parts[0] . ' Y ' . $age_parts[1] . ' M');
    } else {
        $age_str = trim($patient_info->age . ' Y');
    }
}
$gender_str = !empty($patient_info->sex) ? $patient_info->sex : '';
$ag = [];
if (!empty($age_str)) $ag[] = $age_str;
if (!empty($gender_str)) $ag[] = $gender_str;
$age_gender_display = !empty($ag) ? implode(' / ', $ag) : '-';

// Doctor Details in English
$doctor_obj = null;
$doc_id = !empty($payment->doctor) ? $payment->doctor : null;
if (!empty($doc_id)) {
    $doctor_obj = $this->doctor_model->getDoctorById($doc_id);
}
if (empty($doctor_obj) && !empty($payment->doctor_name)) {
    $doc_by_name = $this->db->get_where('doctor', array('name' => trim($payment->doctor_name)))->row();
    if (empty($doc_by_name)) {
        $doc_by_name = $this->db->get_where('doctor', array('name_en' => trim($payment->doctor_name)))->row();
    }
    if (!empty($doc_by_name)) {
        $doctor_obj = $doc_by_name;
    }
}

$doctor_details = [
    'name' => '',
    'department' => '',
    'qualifications' => ''
];

if (!empty($doctor_obj)) {
    if (!empty($doctor_obj->name_en) && preg_match('/[A-Za-z]/', $doctor_obj->name_en)) {
        $doctor_details['name'] = trim($doctor_obj->name_en);
    } elseif (!empty($doctor_obj->name) && preg_match('/^[A-Za-z0-9\s\.\(\)\,\-]+$/', trim($doctor_obj->name))) {
        $doctor_details['name'] = trim($doctor_obj->name);
    } elseif (!empty($payment->doctor_name) && preg_match('/^[A-Za-z0-9\s\.\(\)\,\-]+$/', trim($payment->doctor_name))) {
        $doctor_details['name'] = trim($payment->doctor_name);
    }
    if (!empty($doctor_details['name']) && !preg_match('/^(Dr|Dr\.)/i', $doctor_details['name'])) {
        $doctor_details['name'] = 'Dr. ' . $doctor_details['name'];
    }
    if (!empty($doctor_obj->department_name) && preg_match('/[A-Za-z]/', $doctor_obj->department_name)) {
        $doctor_details['department'] = trim($doctor_obj->department_name);
    }
    // Dynamic qualifications from degrees, fallback to profile extraction
    if (!empty($doctor_obj->degrees)) {
        $doctor_details['qualifications'] = trim($doctor_obj->degrees);
    } elseif (!empty($doctor_obj->profile)) {
        $rawProfile = trim(strip_tags($doctor_obj->profile));
        if (preg_match('/\(([A-Za-z0-9\s,&\/\-\.\+]+)\)/', $rawProfile, $m)) {
            $doctor_details['qualifications'] = trim($m[1]);
        } elseif (preg_match('/[A-Za-z]{2,}/', $rawProfile)) {
            $englishOnly = preg_replace('/[\x{0980}-\x{09FF}]+/u', '', $rawProfile);
            $englishOnly = trim(preg_replace('/[\s\|\-\:\,]+$/', '', preg_replace('/^[\s\|\-\:\,]+/', '', $englishOnly)));
            if (!empty($englishOnly) && strlen($englishOnly) > 3) {
                $doctor_details['qualifications'] = $englishOnly;
            }
        }
    }
} elseif (!empty($payment->doctor_name)) {
    if (preg_match('/^[A-Za-z0-9\s\.\(\)\,\-]+$/', trim($payment->doctor_name))) {
        $doctor_details['name'] = trim($payment->doctor_name);
        if (!preg_match('/^(Dr|Dr\.)/i', $doctor_details['name'])) {
            $doctor_details['name'] = 'Dr. ' . $doctor_details['name'];
        }
    }
}

// Referrer Information
$referrer_display = '';
if (!empty($payment->referrer_name)) {
    $referrer_display = trim($payment->referrer_name);
} elseif (!empty($payment->referrer)) {
    $ref_obj = $this->db->get_where('referrer', array('id' => $payment->referrer))->row();
    if (!empty($ref_obj->name)) {
        $referrer_display = trim($ref_obj->name);
    }
}

// Items Resolution & Automatic Delivery Date
$items = array();
$calc_delivery_date = function($cat_info, $invoice_ts) {
    $delivery_days = 0;
    if (!empty($cat_info)) {
        if (isset($cat_info->delivery_days) && intval($cat_info->delivery_days) > 0) {
            $delivery_days = intval($cat_info->delivery_days);
        } elseif (!empty($cat_info->is_hormone) || strcasecmp($cat_info->payment_category_name ?? '', 'Hormone') === 0 || strcasecmp($cat_info->payment_category_name ?? '', 'Hormonal') === 0) {
            $delivery_days = 3;
        }
    }
    
    $dt = new DateTime();
    $dt->setTimestamp($invoice_ts);
    if ($delivery_days > 0) {
        $dt->modify("+$delivery_days days");
    }
    return $dt->format('d/m/Y');
};

$detected_rooms = array();
$detect_test_room = function($cat_info, $item_name) use (&$detected_rooms) {
    $cid = 0;
    $cname = '';
    $tname = strtolower(trim($item_name ?? ''));
    
    if (!empty($cat_info)) {
        if (!empty($cat_info->payment_category)) {
            $cid = intval($cat_info->payment_category);
        }
        if (!empty($cat_info->payment_category_name)) {
            $cname = strtolower(trim($cat_info->payment_category_name));
        }
        if (empty($tname) && !empty($cat_info->category)) {
            $tname = strtolower(trim($cat_info->category));
        }
    }

    // 1. X-ray: Room 106
    if ($cid === 16 || strpos($cname, 'x-ray') !== false || strpos($cname, 'xray') !== false || strpos($tname, 'x-ray') !== false || strpos($tname, 'xray') !== false) {
        $detected_rooms['xray'] = 'X-ray: 106';
        return;
    }

    // 2. ECG: Room 104
    if ($cid === 18 || $cname === 'ecg' || strpos($tname, 'ecg') !== false) {
        $detected_rooms['ecg'] = 'ECG: 104';
        return;
    }

    // 3. Ultrasound / USG: Room 104
    if ($cid === 17 || strpos($cname, 'ultra') !== false || strpos($cname, 'usg') !== false || strpos($tname, 'usg') !== false || strpos($tname, 'ultrasound') !== false) {
        $detected_rooms['usg'] = 'USG: 104';
        return;
    }

    // 4. Echo: Room 104
    if ($cid === 22 || strpos($cname, 'echo') !== false || strpos($tname, 'echo') !== false) {
        $detected_rooms['echo'] = 'Echo: 104';
        return;
    }

    // 5. Pathology: Room 105
    $path_cat_ids = [11, 12, 13, 14, 15, 20, 21];
    if (in_array($cid, $path_cat_ids) || (!empty($cat_info->is_hormone)) || strpos($cname, 'path') !== false || strpos($cname, 'blood') !== false || strpos($cname, 'stool') !== false || strpos($cname, 'urine') !== false || strpos($cname, 'hormon') !== false) {
        $detected_rooms['pathology'] = 'Pathology: 105';
        return;
    }
};

$payment_items = $this->db->get_where('payment_items', array('payment_id' => $payment->id))->result();
if (!empty($payment_items)) {
    $idx = 0;
    foreach ($payment_items as $p_item) {
        $idx++;
        $cat_info = !empty($p_item->item_id) ? $this->finance_model->getPaymentcategoryById($p_item->item_id) : null;
        $delivery_date = $calc_delivery_date($cat_info, $payment->date);
        $detect_test_room($cat_info, $p_item->item_name);
        $items[] = array(
            'index' => $idx,
            'name' => $p_item->item_name,
            'delivery_date' => $delivery_date,
            'unit_price' => floatval($p_item->item_price),
            'qty' => floatval($p_item->quantity),
            'amount' => floatval($p_item->subtotal)
        );
    }
} else {
    if (!empty($payment->category_name)) {
        $cats = explode(',', $payment->category_name);
        $idx = 0;
        foreach ($cats as $cat) {
            if (empty(trim($cat))) continue;
            $parts = explode('*', $cat);
            if (!empty($parts[0])) {
                $cat_info = $this->finance_model->getPaymentcategoryById($parts[0]);
                $name = !empty($cat_info->category) ? $cat_info->category : (!empty($parts[2]) ? $parts[2] : 'Item #' . $parts[0]);
                $delivery_date = $calc_delivery_date($cat_info, $payment->date);
                $detect_test_room($cat_info, $name);
                $price = isset($parts[1]) ? floatval($parts[1]) : 0;
                $qty = isset($parts[3]) ? floatval($parts[3]) : 1;
                $amount = $price * $qty;
                if ($qty > 0) {
                    $idx++;
                    $items[] = array(
                        'index' => $idx,
                        'name' => $name,
                        'delivery_date' => $delivery_date,
                        'unit_price' => $price,
                        'qty' => $qty,
                        'amount' => $amount
                    );
                }
            }
        }
    }
}

// Assemble comma-separated room assignments in standard logical order
$room_order = ['xray', 'ecg', 'usg', 'echo', 'pathology'];
$room_assignments = array();
foreach ($room_order as $rk) {
    if (isset($detected_rooms[$rk])) {
        $room_assignments[] = $detected_rooms[$rk];
    }
}
$invoice_rooms_line = implode(', ', $room_assignments);

// Financial Summary
$gross_amount = floatval($payment->amount);
$vat_percent = floatval(!empty($payment->vat_amount_percent) ? $payment->vat_amount_percent : 0);
$vat_amount = floatval(!empty($payment->vat) ? $payment->vat : 0);
$discount_percent = floatval(!empty($payment->percent_discount) ? $payment->percent_discount : 0);
$discount_amount = floatval(!empty($payment->flat_discount) ? $payment->flat_discount : (!empty($payment->discount) ? $payment->discount : 0));
$net_payable = floatval($payment->gross_total);
$amount_received = floatval($this->finance_model->getDepositAmountByPaymentId($payment->id));
$balance_due = max(0.00, $net_payable - $amount_received);

// Status Badge
$is_cancelled = (strtolower($payment->status ?? '') === 'cancelled');
if ($is_cancelled) {
    $status_text = 'CANCELLED';
    $status_bg = '#fee2e2';
    $status_color = '#dc2626';
} elseif ($balance_due <= 0.009) {
    $status_text = 'PAID IN FULL';
    $status_bg = '#dcfce7';
    $status_color = '#15803d';
} elseif ($amount_received > 0.009) {
    $status_text = 'PARTIALLY PAID';
    $status_bg = '#fef3c7';
    $status_color = '#b45309';
} else {
    $status_text = 'DUE';
    $status_bg = '#fee2e2';
    $status_color = '#dc2626';
}

// Cashier Name
$cashier_name = '';
$user_id = !empty($payment->user) ? $payment->user : null;
if (!empty($user_id)) {
    $staff = $this->db->get_where('accountant', array('ion_user_id' => $user_id))->row();
    if (empty($staff) || empty($staff->name)) {
        $staff = $this->db->get_where('receptionist', array('ion_user_id' => $user_id))->row();
    }
    if (!empty($staff) && !empty($staff->name)) {
        $cashier_name = trim($staff->name);
    } else {
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
    $cashier_name = 'Hospital Admin';
}

// Logo & Hospital Details
if (!empty($settings->invoice_logo) && file_exists($settings->invoice_logo)) {
    $hospital_logo = $settings->invoice_logo;
} elseif (file_exists('uploads/logo-nonetext.png')) {
    $hospital_logo = 'uploads/logo-nonetext.png';
} elseif (file_exists('uploads/logo_1.png')) {
    $hospital_logo = 'uploads/logo_1.png';
} else {
    $hospital_logo = !empty($settings->logo) ? $settings->logo : '';
}

$hospital_title = 'LIFECARE DIAGNOSTIC CENTER';
$hospital_bangla = 'লাইফ কেয়ার ডায়াগনস্টিক সেন্টার';
$hospital_address = 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali';
$phone_1 = '01829 95 25 95';
$phone_2 = '01727 69 69 77';

// Words Helper
if (!function_exists('invoiceA4AmountWords')) {
    function invoiceA4AmountWords($number) {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty',
            50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
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
$word_amount = ($amount_received > 0) ? $amount_received : $net_payable;
$amount_in_words = invoiceA4AmountWords($word_amount);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body {
    font-family: freeserif, freesans, sans-serif;
    color: #1e293b;
    font-size: 8.5pt;
    line-height: 1.25;
    margin: 0;
    padding: 0;
}
.header-table {
    width: 100%;
    border-bottom: 2pt solid #1e3a8a;
    padding-bottom: 4px;
    margin-bottom: 5px;
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
    font-size: 10.5pt;
    font-weight: normal;
    color: #0f766e;
    margin-top: 1px;
}
.brand-addr {
    font-size: 7.2pt;
    color: #475569;
    margin-top: 2px;
}
.brand-phone {
    font-size: 7.8pt;
    font-weight: bold;
    color: #1e293b;
    margin-top: 1px;
}
.receipt-title {
    font-size: 12.5pt;
    font-weight: bold;
    color: #1e3a8a;
    text-align: right;
    letter-spacing: 0.5px;
}
.receipt-type {
    font-size: 6.8pt;
    font-weight: bold;
    color: #1e3a8a;
    background: #e0f2fe;
    text-align: right;
    padding: 1px 5px;
    border-radius: 2px;
}
.meta-table {
    font-size: 7.8pt;
    margin-top: 3px;
    float: right;
}
.meta-table td {
    padding: 1px 2px;
}
.patient-box {
    width: 100%;
    border: 1pt solid #cbd5e1;
    background: #f8fafc;
    border-radius: 4px;
    margin-bottom: 6px;
    padding: 4px 6px;
}
.patient-table {
    width: 100%;
    font-size: 7.8pt;
    border-collapse: collapse;
}
.patient-table td {
    padding: 2.2px 3px;
}
.lbl {
    color: #64748b;
    font-weight: bold;
}
.items-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8.5pt;
    margin-bottom: 6px;
}
.items-table th {
    background: #1e3a8a;
    color: #ffffff;
    font-size: 7.8pt;
    font-weight: bold;
    text-transform: uppercase;
    padding: 5px 6px;
    border: none;
}
.items-table td {
    padding: 5px 6px;
    border-bottom: 0.5pt solid #e2e8f0;
}
.items-table tr:nth-child(even) td {
    background: #fafcff;
}
.col-num { width: 6%; text-align: center; font-weight: bold; }
.col-test-name { width: 36%; text-align: left; font-weight: bold; color: #0f172a; }
.col-delivery-date { width: 18%; text-align: center; color: #334155; }
.col-unit-price { width: 15%; text-align: right; font-weight: bold; }
.col-qty { width: 8%; text-align: center; color: #334155; }
.col-amount { width: 17%; text-align: right; font-weight: bold; color: #0f172a; }
.doc-badge {
    background: #e0f2fe;
    color: #0284c7;
    border: 0.5pt solid #93c5fd;
    padding: 1px 5px;
    border-radius: 3px;
    font-size: 7.2pt;
    font-weight: bold;
}
.items-table tr {
    page-break-inside: avoid;
}
.bottom-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 4px;
    margin-bottom: 6px;
    page-break-inside: avoid;
}
.bottom-table td {
    vertical-align: top;
}
.status-card {
    background: #f8fafc;
    border: 1pt solid #cbd5e1;
    border-radius: 4px;
    padding: 5px 8px;
    margin-bottom: 6px;
}
.badge-status {
    display: inline-block;
    background: <?php echo $status_bg; ?>;
    color: <?php echo $status_color; ?>;
    font-size: 7.2pt;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 8px;
}
.badge-due {
    display: inline-block;
    background: #fef2f2;
    color: #dc2626;
    border: 1pt solid #f87171;
    font-size: 10pt;
    font-weight: bold;
    padding: 2px 8px;
    border-radius: 3px;
    margin-left: 8px;
}
.in-words {
    font-size: 7.2pt;
    color: #334155;
    padding-top: 3px;
    margin-top: 3px;
    border-top: 0.5pt dashed #e2e8f0;
}
.instructions {
    font-size: 6.8pt;
    color: #475569;
    background: #ffffff;
    border: 0.5pt solid #e2e8f0;
    padding: 3px 5px;
    border-radius: 3px;
    margin-top: 3px;
    line-height: 1.35;
}
.summary-card {
    border: 1pt solid #cbd5e1;
    background: #ffffff;
    border-radius: 4px;
    padding: 5px 8px;
}
.summary-table {
    width: 100%;
    font-size: 7.8pt;
    border-collapse: collapse;
}
.summary-table td {
    padding: 2.2px 0;
}
.sum-title {
    font-size: 7.8pt;
    font-weight: bold;
    color: #1e3a8a;
    border-bottom: 0.5pt solid #e2e8f0;
    padding-bottom: 2px;
    margin-bottom: 3px;
}
.sum-net td {
    font-weight: bold;
    color: #1e3a8a;
    font-size: 8.8pt;
    border-top: 1pt solid #1e3a8a;
    border-bottom: 1pt solid #1e3a8a;
    padding: 3px 0;
}
.sum-due td {
    font-weight: bold;
    color: #dc2626;
    font-size: 9.2pt;
    padding: 3px 0;
}
.sig-table {
    width: 100%;
    margin-top: 20px;
}
.sig-line {
    border-top: 0.5pt dashed #64748b;
    margin-bottom: 2px;
}
.sig-lbl {
    font-size: 6.8pt;
    font-weight: bold;
    color: #64748b;
    text-transform: uppercase;
}
.sig-val {
    font-size: 7.8pt;
    font-weight: bold;
    color: #0f172a;
}
.footer-sec {
    border-top: 1pt solid #cbd5e1;
    padding-top: 4px;
    text-align: center;
    font-size: 6.8pt;
    color: #64748b;
    width: 100%;
    page-break-inside: avoid;
}
.footer-thanks {
    font-size: 7.8pt;
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
                <td style="font-weight: bold; font-size: 9.5pt;">LIFECARE DIAGNOSTIC CENTER — MONEY RECEIPT</td>
                <td style="text-align: right;"><strong>Bill No:</strong> <?php echo $vn; ?> &nbsp;|&nbsp; <strong>Patient:</strong> <?php echo html_escape($patient_name); ?> (HN: <?php echo $hn; ?>) &nbsp;|&nbsp; <strong>Page</strong> {PAGENO}/{nbpg}</td>
            </tr>
        </table>
    </div>
</htmlpageheader>
<sethtmlpageheader name="continuationHeader" value="on" show-this-page="0" />

<!-- 1. Header -->
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
            <div class="receipt-title">MONEY RECEIPT</div>
            <div style="text-align: right; margin: 2px 0;">
                <span class="receipt-type">DIAGNOSTIC SERVICE</span>
            </div>
            <div style="text-align: right; margin: 2px 0;">
                <img src="<?php echo site_url('lab/barcode'); ?>?text=<?php echo $vn; ?>&print=false&size=20&sizefactor=0.85" height="22">
            </div>
            <table class="meta-table" align="right">
                <tr><td class="lbl">Bill No:</td><td style="font-weight: bold;"><?php echo $vn; ?></td></tr>
                <tr><td class="lbl">Date:</td><td><?php echo $invoice_date . ' ' . $invoice_time; ?></td></tr>
            </table>
        </td>
    </tr>
</table>

<!-- 2. Patient & Prescriber Information -->
<div class="patient-box">
    <table class="patient-table">
        <tr>
            <td class="lbl" style="width: 15%;">Patient Name</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;"><strong><?php echo !empty($patient_name) ? html_escape($patient_name) : '-'; ?></strong></td>
            <td class="lbl" style="width: 18%;">Patient ID (HN)</td>
            <td style="width: 2%;">:</td>
            <td style="width: 28%;"><?php echo $hn; ?></td>
        </tr>
        <tr>
            <td class="lbl">Age / Gender</td>
            <td>:</td>
            <td><?php echo html_escape($age_gender_display); ?></td>
            <td class="lbl">Bill No (VN)</td>
            <td>:</td>
            <td><?php echo $vn; ?></td>
        </tr>
        <tr>
            <td class="lbl">Contact Phone</td>
            <td>:</td>
            <td><?php echo !empty($patient_phone) ? html_escape($patient_phone) : '-'; ?></td>
            <td class="lbl">Billing Date</td>
            <td>:</td>
            <td><?php echo $invoice_date; ?></td>
        </tr>
        <tr>
            <td class="lbl">Doctor / Ref.</td>
            <td>:</td>
            <td colspan="4">
                <?php if (!empty($doctor_details['name'])) { ?>
                    <strong><?php echo html_escape($doctor_details['name']); ?></strong>
                    <?php if (!empty($doctor_details['qualifications'])) { ?>
                        &nbsp;<span class="doc-badge"><?php echo html_escape($doctor_details['qualifications']); ?></span>
                    <?php } elseif (!empty($doctor_details['department'])) { ?>
                        &nbsp;<span class="doc-badge"><?php echo html_escape($doctor_details['department']); ?></span>
                    <?php } ?>
                <?php } else { ?>
                    <span>Self / General</span>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>

<!-- 3. Investigation Items Table -->
<table class="items-table">
    <thead>
        <tr>
            <th class="col-num">SL</th>
            <th class="col-test-name">TEST NAME</th>
            <th class="col-delivery-date">DELIVERY DATE</th>
            <th class="col-unit-price">UNIT PRICE</th>
            <th class="col-qty">QTY</th>
            <th class="col-amount">AMOUNT</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if (!empty($items)) {
            foreach ($items as $row) { ?>
                <tr>
                    <td class="col-num"><?php echo $row['index']; ?></td>
                    <td class="col-test-name"><?php echo html_escape($row['name']); ?></td>
                    <td class="col-delivery-date"><?php echo $row['delivery_date']; ?></td>
                    <td class="col-unit-price"><?php echo $currency . ' ' . number_format($row['unit_price'], 2); ?></td>
                    <td class="col-qty"><?php echo $row['qty']; ?></td>
                    <td class="col-amount"><?php echo $currency . ' ' . number_format($row['amount'], 2); ?></td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 10px; color: #64748b;">No diagnostic tests recorded on this invoice.</td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- 4. Bottom Section -->
<table class="bottom-table">
    <tr>
        <!-- Left Pane (54%) -->
        <td style="width: 54%; padding-right: 8px;">
            <div class="status-card">
                <div>
                    <span class="badge-status"><?php echo $status_text; ?></span>
                    <span class="badge-due">Due : <?php echo $currency . ' ' . number_format($balance_due, 2); ?></span>
                </div>
                <div class="in-words">
                    <strong>In Words:</strong> <?php echo html_escape($amount_in_words); ?>
                </div>
                <div class="instructions">
                    <div><strong>Instructions:</strong> Please preserve this receipt. It is required during diagnostic report collection.</div>
                    <?php if (!empty($invoice_rooms_line)) { ?>
                        <div style="margin-top: 2px; font-weight: bold; color: #1e3a8a; font-size: 7.5pt;">
                            <strong style="color: #0f172a;">Room No:</strong> <?php echo html_escape($invoice_rooms_line); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Signatures -->
            <table class="sig-table">
                <tr>
                    <td style="width: 50%; text-align: left;">
                        <div style="width: 140px; text-align: center;">
                            <div class="sig-val"><?php echo html_escape($cashier_name); ?></div>
                            <div class="sig-line"></div>
                            <div class="sig-lbl">Prepared By</div>
                        </div>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <div style="width: 140px; text-align: center; float: right;">
                            <div class="sig-val">&nbsp;</div>
                            <div class="sig-line"></div>
                            <div class="sig-lbl">Authorized Signature</div>
                        </div>
                    </td>
                </tr>
            </table>
        </td>

        <!-- Right Pane (46%) -->
        <td style="width: 46%;">
            <div class="summary-card">
                <div class="sum-title">BILL SUMMARY (BDT)</div>
                <table class="summary-table">
                    <tr>
                        <td style="color: #64748b;">Gross Amount</td>
                        <td style="text-align: right; font-weight: bold;"><?php echo $currency . ' ' . number_format($gross_amount, 2); ?></td>
                    </tr>
                    <?php if ($discount_amount > 0 || $discount_percent > 0) { ?>
                        <tr>
                            <td style="color: #64748b;">Discount <?php if ($discount_percent > 0) { echo '(' . number_format($discount_percent, 2) . '%)'; } ?></td>
                            <td style="text-align: right; font-weight: bold;">-<?php echo $currency . ' ' . number_format($discount_amount, 2); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($vat_amount > 0 || $vat_percent > 0) { ?>
                        <tr>
                            <td style="color: #64748b;">VAT <?php if ($vat_percent > 0) { echo '(' . number_format($vat_percent, 2) . '%)'; } ?></td>
                            <td style="text-align: right; font-weight: bold;"><?php echo $currency . ' ' . number_format($vat_amount, 2); ?></td>
                        </tr>
                    <?php } ?>
                    <tr class="sum-net">
                        <td>Net Payable</td>
                        <td style="text-align: right;"><?php echo $currency . ' ' . number_format($net_payable, 2); ?></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Amount Received</td>
                        <td style="text-align: right; font-weight: bold;"><?php echo $currency . ' ' . number_format($amount_received, 2); ?></td>
                    </tr>
                    <tr class="sum-due">
                        <td>Due Amount</td>
                        <td style="text-align: right;"><?php echo $currency . ' ' . number_format($balance_due, 2); ?></td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

<!-- 5. Footer -->
<div class="footer-sec">
    <div class="footer-thanks">Thank you for choosing LifeCare Diagnostic Center. We wish you good health!</div>
    <div style="margin: 2px 0;"><?php echo html_escape($hospital_address); ?> &nbsp;|&nbsp; Phone: <?php echo $phone_1; ?>, <?php echo $phone_2; ?></div>
    <div style="font-style: italic;">Printed: <?php echo $print_date; ?> • Computer Generated Invoice • Software by INNOVA</div>
</div>

</body>
</html>