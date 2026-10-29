<?php
// Initialize data objects safely
$patient_info = !empty($payment->patient) ? $this->db->get_where('patient', array('id' => $payment->patient))->row() : null;

// Official Currency configuration: BDT (৳)
$currency = '৳';

// Formatted Identifiers (10-digit zero padded for diagnostic center record standards)
$vn = sprintf('%010d', $payment->id);
$hn = !empty($patient_info->id) ? sprintf('%010d', $patient_info->id) : (!empty($payment->patient) ? sprintf('%010d', $payment->patient) : '0000000000');

// Dates with Asia/Dhaka timezone
date_default_timezone_set('Asia/Dhaka');
$invoice_date = !empty($payment->date) ? date('d/m/Y', $payment->date) : date('d/m/Y');
$invoice_time = !empty($payment->date) ? date('h:i A', $payment->date) : date('h:i A');
$visit_date = !empty($payment->date) ? date('d/m/Y', $payment->date) : date('d/m/Y');
$print_date = date('d/m/Y h:i A');

// Patient Information Details
$patient_name = !empty($patient_info->name) ? $patient_info->name : (!empty($payment->patient_name) ? $payment->patient_name : '');
$patient_phone = !empty($patient_info->phone) ? $patient_info->phone : (!empty($payment->patient_phone) ? $payment->patient_phone : '');

// Patient Age / Gender
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

// -------------------------------------------------------------
// Doctor Information Resolution (FULL DETAILS STRICTLY IN ENGLISH)
// -------------------------------------------------------------
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
    'qualifications' => '',
    'designation' => '',
    'institution' => ''
];

if (!empty($doctor_obj)) {
    // 1. Doctor Name (English strictly)
    if (!empty($doctor_obj->name_en) && preg_match('/[A-Za-z]/', $doctor_obj->name_en)) {
        $doctor_details['name'] = trim($doctor_obj->name_en);
    } elseif (!empty($doctor_obj->name) && preg_match('/^[A-Za-z0-9\s\.\(\)\,\-]+$/', trim($doctor_obj->name))) {
        $doctor_details['name'] = trim($doctor_obj->name);
    } elseif (!empty($payment->doctor_name) && preg_match('/^[A-Za-z0-9\s\.\(\)\,\-]+$/', trim($payment->doctor_name))) {
        $doctor_details['name'] = trim($payment->doctor_name);
    }
    
    // Ensure "Dr." prefix
    if (!empty($doctor_details['name']) && !preg_match('/^(Dr|Dr\.)/i', $doctor_details['name'])) {
        $doctor_details['name'] = 'Dr. ' . $doctor_details['name'];
    }

    // 2. Department / Specialization (English strictly)
    if (!empty($doctor_obj->department_name) && preg_match('/[A-Za-z]/', $doctor_obj->department_name)) {
        $doctor_details['department'] = trim($doctor_obj->department_name);
    }

    // 3. Qualifications / Degrees (Dynamic from doctor->degrees, fallback to profile extraction)
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

// Referrer Information (if assigned)
$referrer_display = '';
if (!empty($payment->referrer_name)) {
    $referrer_display = trim($payment->referrer_name);
} elseif (!empty($payment->referrer)) {
    $ref_obj = $this->db->get_where('referrer', array('id' => $payment->referrer))->row();
    if (!empty($ref_obj->name)) {
        $referrer_display = trim($ref_obj->name);
    }
}

// Line Items Resolution (Historical Snapshot Compatibility & Automatic Delivery Date)
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
    // Fallback parser for older legacy invoices without payment_items
    if ($payment->payment_from == 'appointment') {
        if (!empty($payment->category_name)) {
            $appointment_details = $this->db->get_where('appointment', array('id' => $payment->appointment_id))->row();
            $items[] = array(
                'index' => 1,
                'name' => $payment->category_name . (!empty($appointment_details->time_slot) ? ' (' . $appointment_details->time_slot . ')' : ''),
                'delivery_date' => date('d/m/Y', $payment->date),
                'unit_price' => floatval($payment->gross_total),
                'qty' => 1,
                'amount' => floatval($payment->gross_total)
            );
        }
    } elseif ($payment->payment_from == 'admitted_patient_bed_medicine') {
        if (!empty($payment->category_name)) {
            $cats = explode('#', $payment->category_name);
            $idx = 0;
            foreach ($cats as $cat) {
                if (empty(trim($cat))) continue;
                $cat_new = explode('*', $cat);
                if (count($cat_new) >= 5) {
                    $idx++;
                    $items[] = array(
                        'index' => $idx,
                        'name' => $cat_new[1],
                        'delivery_date' => date('d/m/Y', $payment->date),
                        'unit_price' => floatval($cat_new[2]),
                        'qty' => floatval($cat_new[3]),
                        'amount' => floatval($cat_new[4])
                    );
                }
            }
        }
    } elseif ($payment->payment_from == 'admitted_patient_bed_service') {
        if (!empty($payment->category_name)) {
            $cats = explode('#', $payment->category_name);
            $idx = 0;
            foreach ($cats as $cat) {
                if (empty(trim($cat))) continue;
                $cat_new = explode('*', $cat);
                if (!empty($cat_new[0])) {
                    $idx++;
                    $pservice = $this->db->get_where('pservice', array('id' => $cat_new[0]))->row();
                    $s_name = !empty($pservice) ? $pservice->name : 'Service #' . $cat_new[0];
                    $price = floatval($cat_new[1] ?? 0);
                    $items[] = array(
                        'index' => $idx,
                        'name' => $s_name,
                        'delivery_date' => date('d/m/Y', $payment->date),
                        'unit_price' => $price,
                        'qty' => 1,
                        'amount' => $price
                    );
                }
            }
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

// Financial Summary (Authoritative values from database)
$gross_amount = floatval($payment->amount);
$vat_percent = floatval(!empty($payment->vat_amount_percent) ? $payment->vat_amount_percent : 0);
$vat_amount = floatval(!empty($payment->vat) ? $payment->vat : 0);

$discount_percent = floatval(!empty($payment->percent_discount) ? $payment->percent_discount : 0);
$discount_amount = floatval(!empty($payment->flat_discount) ? $payment->flat_discount : (!empty($payment->discount) ? $payment->discount : 0));

$net_payable = floatval($payment->gross_total);
$amount_received = floatval($this->finance_model->getDepositAmountByPaymentId($payment->id));
$balance_due = max(0.00, $net_payable - $amount_received);

// Status Badge Determination
$is_cancelled = (strtolower($payment->status ?? '') === 'cancelled');
if ($is_cancelled) {
    $status_text = 'CANCELLED';
    $status_class = 'status-cancelled';
} elseif ($balance_due <= 0.009) {
    $status_text = 'PAID IN FULL';
    $status_class = 'status-paid';
} elseif ($amount_received > 0.009) {
    $status_text = 'PARTIALLY PAID';
    $status_class = 'status-partial';
} else {
    $status_text = 'DUE';
    $status_class = 'status-due';
}

// Cashier / Bill Creator Information
$cashier_name = '';
$user_id = !empty($payment->user) ? $payment->user : null;
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

    // 2. If not found in staff tables, check users table (use username, e.g. Hospital Admin)
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

// 3. Default fallback for unspecified creator
if (empty($cashier_name)) {
    $cashier_name = 'Hospital Admin';
}

// Official Hospital Identity
if (file_exists('uploads/lifecare_logo_circle.png')) {
    $hospital_logo = 'uploads/lifecare_logo_circle.png';
} elseif (!empty($settings->invoice_logo) && file_exists($settings->invoice_logo)) {
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

$hospital_title = 'LIFECARE DIAGNOSTIC CENTER';
$hospital_bangla = 'লাইফ কেয়ার ডায়াগনস্টিক সেন্টার';
$hospital_address = 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali';
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

<!-- Modern Print & Screen CSS for A5 Portrait (148mm x 210mm) -->
<link rel="stylesheet" href="common/extranal/css/finance/modern_invoice.css?v=<?php echo time(); ?>">
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
        overflow: visible !important;
    }
    .wrapper,
    .content-wrapper,
    .invoice-screen-wrapper,
    .invoice-container-wrapper {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .invoice-paper {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        display: block !important;
        overflow: visible !important;
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
                        <i class="fas fa-file-invoice mr-2 text-primary"></i><?php echo lang('invoice') ?> #<?php echo $payment->id; ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><?php echo lang('home') ?></a></li>
                        <li class="breadcrumb-item"><a href="finance/payment"><?php echo lang('all') ?> <?php echo lang('invoices') ?></a></li>
                        <li class="breadcrumb-item active"><?php echo lang('invoice') ?> #<?php echo $payment->id; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Print Preview Screen -->
    <div class="invoice-screen-wrapper">
        
        <!-- Screen Action Toolbar (no-print) -->
        <div class="invoice-action-toolbar no-print">
            <div class="invoice-toolbar-left">
                <?php if ($payment->payment_from == 'payment' || empty($payment->payment_from)) { ?>
                    <a href="finance/payment" class="invoice-btn-action invoice-btn-back">
                        <i class="fas fa-arrow-left"></i> <?php echo lang('back_to_payment_modules'); ?>
                    </a>
                <?php } ?>

                <?php if ($this->ion_auth->in_group(array('admin', 'Accountant'))) {
                    $lab_pending = array();
                    $lab_reports_previous = $this->lab_model->getLabByInvoice($payment->id);
                    if (!empty($lab_reports_previous)) {
                        foreach ($lab_reports_previous as $lab) {
                            if ($lab->test_status == 'not_done' || empty($lab->test_status)) {
                                $lab_pending[] = 'no';
                            }
                        }
                    }
                    if (count($lab_reports_previous) == count($lab_pending) || empty($lab_reports_previous)) { ?>
                        <a href="finance/editPayment?id=<?php echo $payment->id; ?>" class="invoice-btn-action invoice-btn-edit">
                            <i class="fas fa-edit"></i> <?php echo lang('edit'); ?> <?php echo lang('invoice'); ?>
                        </a>
                    <?php }
                } ?>

                <span class="invoice-status-badge <?php echo $status_class; ?>">
                    <i class="fas fa-circle mr-1" style="font-size: 7px;"></i> <?php echo $status_text; ?>
                </span>
            </div>

            <div class="invoice-toolbar-right">
                <button type="button" class="invoice-btn-action invoice-btn-deposit depositButton" data-toggle="modal" data-id="<?php echo $payment->id ?>" data-from="<?php echo $payment->payment_from ?>">
                    <i class="far fa-credit-card"></i> <?php echo lang('submit'); ?> <?php echo lang('payment'); ?>
                </button>

                <a href="finance/download?id=<?php echo $payment->id; ?>" class="invoice-btn-action invoice-btn-download">
                    <i class="fas fa-download"></i> <?php echo lang('generate'); ?> PDF
                </a>

                <a href="javascript:void(0);" onclick="window.print();" class="invoice-btn-action invoice-btn-print">
                    <i class="fas fa-print"></i> <?php echo lang('print'); ?> (A5)
                </a>
            </div>
        </div>

        <!-- A5 Invoice Paper Container (148mm x 210mm) -->
        <div class="invoice-container-wrapper">
            <div class="invoice-paper <?php echo $is_cancelled ? 'is-cancelled' : ''; ?>" id="printable-invoice">
                
                <!-- Cancelled Watermark Stamp -->
                <div class="invoice-cancelled-stamp">CANCELLED</div>

                <!-- 1. HEADER SECTION -->
                <header class="invoice-header">
                    <!-- Left: Logo, Hospital Name, Bangla Name, Address, Phones -->
                    <div class="invoice-header-brand-group">
                        <div class="invoice-brand-top">
                            <?php if (!empty($hospital_logo) && file_exists($hospital_logo)) { ?>
                                <img src="<?php echo $hospital_logo; ?>?v=<?php echo time(); ?>" alt="<?php echo html_escape($hospital_title); ?>" class="hospital-logo">
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

                    <!-- Right: MONEY RECEIPT Title, Barcode & Bill Metadata -->
                    <div class="invoice-header-meta">
                        <div class="invoice-title-large">MONEY RECEIPT</div>
                        <div class="invoice-type-pill">DIAGNOSTIC SERVICE</div>
                        
                        <div class="invoice-header-barcode">
                            <img src="<?php echo site_url('lab/barcode'); ?>?text=<?php echo $vn; ?>&print=false&size=22&sizefactor=0.9" alt="VN Barcode" class="barcode-img">
                        </div>

                        <table class="invoice-header-meta-table">
                            <tr>
                                <td class="lbl">Bill No</td>
                                <td class="sep">:</td>
                                <td class="val"><?php echo $vn; ?></td>
                            </tr>
                            <tr>
                                <td class="lbl">Date</td>
                                <td class="sep">:</td>
                                <td class="val" style="font-family: inherit;"><?php echo $invoice_date . ' ' . $invoice_time; ?></td>
                            </tr>
                        </table>
                    </div>
                </header>

                <!-- 2. PATIENT & PRESCRIBER INFORMATION (UNIFIED TABLE BOX) -->
                <div class="patient-doctor-box">
                    <table class="patient-info-table">
                        <tr>
                            <td class="lbl">Patient Name</td>
                            <td class="sep">:</td>
                            <td class="val">
                                <strong><?php echo !empty($patient_name) ? html_escape($patient_name) : '-'; ?></strong>
                            </td>
                            <td class="lbl">Patient ID (HN)</td>
                            <td class="sep">:</td>
                            <td class="val font-monospace"><?php echo $hn; ?></td>
                        </tr>
                        <tr>
                            <td class="lbl">Age / Gender</td>
                            <td class="sep">:</td>
                            <td class="val"><?php echo html_escape($age_gender_display); ?></td>
                            <td class="lbl">Bill No (VN)</td>
                            <td class="sep">:</td>
                            <td class="val font-monospace"><?php echo $vn; ?></td>
                        </tr>
                        <tr>
                            <td class="lbl">Contact Phone</td>
                            <td class="sep">:</td>
                            <td class="val"><?php echo !empty($patient_phone) ? html_escape($patient_phone) : '-'; ?></td>
                            <td class="lbl">Billing Date</td>
                            <td class="sep">:</td>
                            <td class="val"><?php echo $invoice_date; ?></td>
                        </tr>
                        <!-- Doctor Row (English strictly) -->
                        <tr class="doctor-subrow">
                            <td class="lbl">Doctor / Ref.</td>
                            <td class="sep">:</td>
                            <td class="val" colspan="4">
                                <?php if (!empty($doctor_details['name'])) { ?>
                                    <strong><?php echo html_escape($doctor_details['name']); ?></strong>
                                    <?php if (!empty($doctor_details['qualifications'])) { ?>
                                        <span class="doc-badge ml-2"><?php echo html_escape($doctor_details['qualifications']); ?></span>
                                    <?php } elseif (!empty($doctor_details['department'])) { ?>
                                        <span class="doc-badge ml-2"><?php echo html_escape($doctor_details['department']); ?></span>
                                    <?php } ?>
                                <?php } else { ?>
                                    <span class="text-muted font-italic">Self / General</span>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- 3. SERVICE / TEST TABLE -->
                <div class="invoice-items-table-wrapper">
                    <table class="invoice-items-table">
                        <thead>
                            <tr class="table-columns-header-row">
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
                                    <td colspan="6" class="text-center py-3 text-muted">No diagnostic services or tests recorded on this invoice.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- 4. BOTTOM SECTION: PAYMENT NOTES & BILL SUMMARY (BALANCED PANES) -->
                <div class="invoice-bottom-grid">
                    
                    <!-- Left Pane: Payment Status, Advisory, Signatures -->
                    <div class="invoice-left-pane">
                        <div>
                            <div class="status-row-top">
                                <span class="invoice-status-badge <?php echo $status_class; ?>">
                                    <i class="fas fa-circle mr-1" style="font-size: 7px;"></i> <?php echo $status_text; ?>
                                </span>
                                <span class="due-amount-banner <?php echo ($balance_due > 0.009) ? 'banner-has-due' : 'banner-no-due'; ?>">
                                    Due : <?php echo $currency . ' ' . number_format($balance_due, 2); ?>
                                </span>
                            </div>

                            <!-- Amount in Words -->
                            <div class="in-words-row">
                                <strong>In Words:</strong> 
                                <?php 
                                $word_amount = ($amount_received > 0) ? $amount_received : $net_payable;
                                echo invoiceAmountInWords($word_amount); 
                                ?>
                            </div>

                            <div class="patient-instructions-box">
                                <div><strong>Instructions:</strong> Please preserve this receipt. It is required during diagnostic report collection.</div>
                                <?php if (!empty($invoice_rooms_line)) { ?>
                                    <div class="invoice-room-numbers">
                                        <strong>Room No:</strong> <?php echo html_escape($invoice_rooms_line); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Left Signature Block -->
                        <div class="sig-block-left">
                            <div class="sig-name"><?php echo !empty($cashier_name) ? html_escape($cashier_name) : 'Hospital Admin'; ?></div>
                            <div class="sig-line">-----------------------------</div>
                            <div class="sig-label">PREPARED BY</div>
                        </div>
                    </div>

                    <!-- Right Pane: Bill Summary & Authorized Signature -->
                    <div class="invoice-right-pane-wrapper">
                        <div class="invoice-right-pane">
                            <div class="bill-summary-title">
                                <span>Bill Summary</span>
                                <span class="currency-tag"><?php echo $currency; ?> BDT</span>
                            </div>

                            <table class="bill-summary-table">
                                <tr>
                                    <td class="sum-lbl">Gross Amount</td>
                                    <td class="sum-sep">:</td>
                                    <td class="sum-val"><?php echo $currency . ' ' . number_format($gross_amount, 2); ?></td>
                                </tr>
                                <?php if ($discount_amount > 0 || $discount_percent > 0) { ?>
                                    <tr>
                                        <td class="sum-lbl">Discount <?php if ($discount_percent > 0) { echo '(' . number_format($discount_percent, 2) . '%)'; } ?></td>
                                        <td class="sum-sep">:</td>
                                        <td class="sum-val">-<?php echo $currency . ' ' . number_format($discount_amount, 2); ?></td>
                                    </tr>
                                <?php } ?>
                                <?php if ($vat_amount > 0 || $vat_percent > 0) { ?>
                                    <tr>
                                        <td class="sum-lbl">VAT <?php if ($vat_percent > 0) { echo '(' . number_format($vat_percent, 2) . '%)'; } ?></td>
                                        <td class="sum-sep">:</td>
                                        <td class="sum-val"><?php echo $currency . ' ' . number_format($vat_amount, 2); ?></td>
                                    </tr>
                                <?php } ?>
                                
                                <!-- Highlighted Net Payable Row -->
                                <tr class="sum-row-net">
                                    <td class="sum-lbl">Net Payable</td>
                                    <td class="sum-sep">:</td>
                                    <td class="sum-val"><?php echo $currency . ' ' . number_format($net_payable, 2); ?></td>
                                </tr>

                                <tr>
                                    <td class="sum-lbl">Amount Received</td>
                                    <td class="sum-sep">:</td>
                                    <td class="sum-val"><?php echo $currency . ' ' . number_format($amount_received, 2); ?></td>
                                </tr>

                                <!-- Highlighted Balance Due Row -->
                                <tr class="sum-row-due">
                                    <td class="sum-lbl">Due Amount</td>
                                    <td class="sum-sep">:</td>
                                    <td class="sum-val <?php echo ($balance_due > 0.009) ? 'due-positive' : 'due-zero'; ?>">
                                        <?php echo $currency . ' ' . number_format($balance_due, 2); ?>
                                    </td>
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

                <!-- 5. FOOTER SECTION -->
                <footer class="invoice-footer-container">
                    <div class="footer-thankyou">Thank you for choosing LifeCare Diagnostic Center. We wish you good health!</div>
                    <div class="footer-address"><?php echo html_escape($hospital_address); ?></div>
                    <div class="footer-contact">
                        <i class="fas fa-phone-alt"></i> <?php echo $phone_1; ?> &nbsp;|&nbsp; <i class="fas fa-phone-alt"></i> <?php echo $phone_2; ?>
                    </div>
                    <div class="footer-note">
                        Printed: <?php echo $print_date; ?> &bull; Computer Generated Invoice &bull; Software by INNOVA
                    </div>
                </footer>

            </div> <!-- /.invoice-paper -->
        </div> <!-- /.invoice-container-wrapper -->

    </div> <!-- /.invoice-screen-wrapper -->
</div> <!-- /.content-wrapper -->

<!-- Deposit Modal (Preserved for cashier submit payment actions) -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title font-weight-bold">
                    <i class="far fa-credit-card mr-2 text-primary"></i><?php echo lang('submit'); ?> <?php echo lang('deposit'); ?>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <form role="form" action="finance/deposit" id="deposit-form" class="clearfix" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo lang('patient'); ?></label>
                        <input type="text" class="form-control" name="name" value='<?php echo html_escape($patient_name); ?>' placeholder="" readonly>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo lang('balance_due'); ?></label>
                        <input type="text" class="form-control" name="due" value='<?php echo number_format($balance_due, 2, '.', ''); ?>' placeholder="" readonly>
                    </div>
                    <?php $inv_prev_discount = floatval(!empty($payment->flat_discount) ? $payment->flat_discount : (!empty($payment->discount) ? $payment->discount : 0)); ?>
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo lang('deposit_amount'); ?></label>
                        <input type="number" step="any" min="0" class="form-control" name="deposited_amount" id="deposited_amount_inv" placeholder="0.00" onfocus="this.select()" onclick="this.select()">
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="additional_discount_inv" class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-tag text-warning mr-1"></i><?php echo lang('discount'); ?> <?php echo lang('amount'); ?>
                            </label>
                            <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="font-size:11px;">
                                Prev: ৳<?php echo number_format($inv_prev_discount, 2); ?>
                            </span>
                        </div>
                        <input type="number" step="any" min="0" class="form-control font-weight-bold text-success" name="additional_discount" id="additional_discount_inv" value="0" placeholder="0.00" onfocus="this.select()" onclick="this.select()">
                        <small class="form-text text-muted mt-1">
                            Total Discount: <strong class="text-success">৳<span id="total_discount_display_inv"><?php echo number_format($inv_prev_discount, 2); ?></span></strong>
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo lang('deposit_type'); ?></label>
                        <select class="form-control m-bot15 js-example-basic-single selecttype" id="selecttype" name="deposit_type" value=''>
                            <option value="Cash"><?php echo lang('cash'); ?> </option>
                            <option value="Card"><?php echo lang('card'); ?> </option>
                        </select>
                    </div>
                    <input type="hidden" name="id" value='<?php echo $payment->id; ?>'>
                    <input type="hidden" name="patient" value='<?php echo $payment->patient; ?>'>
                    <input type="hidden" name="payment_id" value='<?php echo $payment->id; ?>'>
                    <input type="hidden" name="payment_from" value='<?php echo $payment->payment_from; ?>'>
                    <div class="form-group text-right mb-0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang('cancel'); ?></button>
                        <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="common/js/codearistos.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    $(".depositButton").click(function (e) {
        e.preventDefault();
        $('#myModal').modal('show');
    });

    $('#deposited_amount_inv, #additional_discount_inv').on('focus click', function() {
        var self = this;
        setTimeout(function() {
            self.select();
        }, 50);
    });

    var prevDisc = <?php echo json_encode($inv_prev_discount); ?>;
    $('#additional_discount_inv').on('input keyup change', function() {
        var addDisc = parseFloat($(this).val()) || 0;
        if (addDisc < 0) { addDisc = 0; $(this).val(0); }
        var totDisc = prevDisc + addDisc;
        $('#total_discount_display_inv').text(totDisc.toFixed(2));
    });

    $('#deposit-form').on('submit', function(e) {
        var dep = parseFloat($('#deposited_amount_inv').val()) || 0;
        var addDisc = parseFloat($('#additional_discount_inv').val()) || 0;
        if (dep <= 0 && addDisc <= 0) {
            alert('Please enter a Deposit Amount or Discount Amount.');
            e.preventDefault();
            return false;
        }
    });
});
</script>