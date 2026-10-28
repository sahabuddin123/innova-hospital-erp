<?php
/**
 * Master Migration & Data Sync Script for Lifecare Hospital Doctor Management
 */

$mysqli = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error . "\n");
}
$mysqli->set_charset("utf8mb4");

echo "=== STEP 1: SCHEMA MIGRATION ===\n";

function addColumnIfNotExists($mysqli, $table, $column, $definition) {
    $res = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($res && $res->num_rows == 0) {
        $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
        if ($mysqli->query($sql)) {
            echo "Added column `$column` to `$table`.\n";
        } else {
            echo "Error adding `$column` to `$table`: " . $mysqli->error . "\n";
        }
    } else {
        echo "Column `$column` already exists in `$table`.\n";
    }
}

// Columns for doctor table
addColumnIfNotExists($mysqli, 'doctor', 'name_en', "VARCHAR(100) NULL DEFAULT NULL AFTER `name`");
addColumnIfNotExists($mysqli, 'doctor', 'designation', "VARCHAR(255) NULL DEFAULT NULL AFTER `name_en`");
addColumnIfNotExists($mysqli, 'doctor', 'specialization', "VARCHAR(500) NULL DEFAULT NULL AFTER `degrees`");
addColumnIfNotExists($mysqli, 'doctor', 'experience', "VARCHAR(255) NULL DEFAULT NULL AFTER `specialization`");
addColumnIfNotExists($mysqli, 'doctor', 'phone_secondary', "VARCHAR(50) NULL DEFAULT NULL AFTER `phone`");
addColumnIfNotExists($mysqli, 'doctor', 'chamber', "VARCHAR(255) NULL DEFAULT NULL AFTER `address`");
addColumnIfNotExists($mysqli, 'doctor', 'consultation_fee', "DECIMAL(10,2) NOT NULL DEFAULT 700.00 AFTER `profile`");
addColumnIfNotExists($mysqli, 'doctor', 'followup_fee', "DECIMAL(10,2) NULL DEFAULT 400.00 AFTER `consultation_fee`");
addColumnIfNotExists($mysqli, 'doctor', 'schedule_flag', "VARCHAR(255) NULL DEFAULT NULL AFTER `available`");

// Columns for holidays table
addColumnIfNotExists($mysqli, 'holidays', 'end_date', "VARCHAR(100) NULL DEFAULT NULL AFTER `date`");
addColumnIfNotExists($mysqli, 'holidays', 'leave_type', "VARCHAR(50) NOT NULL DEFAULT 'full_day' AFTER `end_date`");
addColumnIfNotExists($mysqli, 'holidays', 'start_time', "VARCHAR(50) NULL DEFAULT NULL AFTER `leave_type`");
addColumnIfNotExists($mysqli, 'holidays', 'end_time', "VARCHAR(50) NULL DEFAULT NULL AFTER `start_time`");
addColumnIfNotExists($mysqli, 'holidays', 'reason', "VARCHAR(500) NULL DEFAULT NULL AFTER `end_time`");

echo "\n=== STEP 2: CROPPING DOCTOR PROFILE PHOTOS ===\n";
if (!file_exists("uploads/doctors")) {
    mkdir("uploads/doctors", 0777, true);
}

// Crop Dr Mahbubur Rahman photo
$poster1 = "scratch/posters/media_1793182543226.png";
if (file_exists($poster1)) {
    $im1 = imagecreatefrompng($poster1);
    $w1 = imagesx($im1);
    $h1 = imagesy($im1);
    $crop1_x = (int)($w1 * 0.69);
    $crop1_y = (int)($h1 * 0.23);
    $crop1_w = (int)($w1 * 0.30);
    $crop1_h = (int)($w1 * 0.30);
    $thumb1 = imagecreatetruecolor(300, 300);
    $white = imagecolorallocate($thumb1, 255, 255, 255);
    imagefill($thumb1, 0, 0, $white);
    imagecopyresampled($thumb1, $im1, 0, 0, $crop1_x, $crop1_y, 300, 300, $crop1_w, $crop1_h);
    imagejpeg($thumb1, "uploads/doctors/dr_mahbubur_rahman.jpg", 95);
    imagedestroy($im1);
    imagedestroy($thumb1);
    echo "Saved uploads/doctors/dr_mahbubur_rahman.jpg\n";
}

// Crop Dr Rashelul Islam photo
$poster2 = "scratch/posters/media_1793182552588.png";
if (file_exists($poster2)) {
    $im2 = imagecreatefrompng($poster2);
    $w2 = imagesx($im2);
    $h2 = imagesy($im2);
    $crop2_x = (int)($w2 * 0.08);
    $crop2_y = (int)($h2 * 0.12);
    $crop2_w = (int)($w2 * 0.30);
    $crop2_h = (int)($w2 * 0.30);
    $thumb2 = imagecreatetruecolor(300, 300);
    $white = imagecolorallocate($thumb2, 255, 255, 255);
    imagefill($thumb2, 0, 0, $white);
    imagecopyresampled($thumb2, $im2, 0, 0, $crop2_x, $crop2_y, 300, 300, $crop2_w, $crop2_h);
    imagejpeg($thumb2, "uploads/doctors/dr_rashelul_islam.jpg", 95);
    imagedestroy($im2);
    imagedestroy($thumb2);
    echo "Saved uploads/doctors/dr_rashelul_islam.jpg\n";
}

echo "\n=== STEP 3: DEPARTMENTS SYNCHRONIZATION ===\n";
$departments = [
    'Orthopaedics & Trauma' => 'Specialized in Orthopaedics, Joint Disorders, Trauma & Spine Surgery',
    'Medicine & Neuro Medicine' => 'Specialized in Internal Medicine, Neuro Medicine, Heart Diseases & Diabetes',
    'Gynecology & Obstetrics' => 'Specialized in Obstetrics, Gynecology, Infertility, Women\'s Health & Sonology',
    'General Medicine & Pediatrics' => 'Specialized in General Medicine, Pediatrics, ENT, Dermatology, Allergy & Sonology',
    'Medicine & Cardiology' => 'Specialized in Internal Medicine, Cardiology, Chest Diseases & Diabetes'
];

$dept_map = [];
foreach ($departments as $dname => $ddesc) {
    $stmt = $mysqli->prepare("SELECT id FROM `department` WHERE `name` = ? AND `hospital_id` = '98'");
    $stmt->bind_param("s", $dname);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $dept_map[$dname] = $row['id'];
        echo "Found department: $dname (ID: {$row['id']})\n";
    } else {
        $instmt = $mysqli->prepare("INSERT INTO `department` (`name`, `description`, `hospital_id`) VALUES (?, ?, '98')");
        $instmt->bind_param("ss", $dname, $ddesc);
        $instmt->execute();
        $new_id = $mysqli->insert_id;
        $dept_map[$dname] = $new_id;
        echo "Created department: $dname (ID: $new_id)\n";
    }
}

echo "\n=== STEP 4: DOCTOR RECORDS UPDATE (ENGLISH ONLY, ZERO FABRICATION) ===\n";
$doctors_data = [
    52 => [
        'name' => 'Dr. Md. Mahbubur Rahman (Mahbub)',
        'name_en' => 'Dr. Md. Mahbubur Rahman (Mahbub)',
        'designation' => 'Assistant Professor',
        'degrees' => 'MBBS (RU), MS (Orthopaedic Surgery), Fellowship in Spine Surgery',
        'department_name' => 'Orthopaedics & Trauma',
        'specialization' => 'Orthopaedics, Joint Disorders, Trauma, Spine Surgery',
        'experience' => 'Assistant Professor, Dhaka International Medical College & Hospital',
        'chamber' => 'Dhaka International Medical College & Hospital / Lifecare Diagnostic Center',
        'address' => 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali',
        'email' => 'dr.mahbubur@lifecarediagnostic.com',
        'phone' => '01829952595',
        'phone_secondary' => '01727696977',
        'profile' => 'Assistant Professor at Dhaka International Medical College & Hospital. Specialist surgeon in Orthopaedics, Joint Disorders, Trauma, Spine Surgery, and Paralysis.',
        'img_url' => 'uploads/doctors/dr_mahbubur_rahman.jpg',
        'consultation_fee' => 700.00,
        'followup_fee' => 400.00,
        'schedule_flag' => NULL,
        'active' => 1
    ],
    51 => [
        'name' => 'Dr. Md. Rashelul Islam',
        'name_en' => 'Dr. Md. Rashelul Islam',
        'designation' => 'Specialist Physician',
        'degrees' => 'MBBS (CMC), FCPS (Medicine, FP), PGT (Neuro Medicine), PGT (Cardiology), CMU (Ultra)',
        'department_name' => 'Medicine & Neuro Medicine',
        'specialization' => 'Medicine, Neuro Medicine, Heart Disease, Diabetes',
        'experience' => 'Physician, Dhaka Medical College & Hospital',
        'chamber' => 'Dhaka Medical College & Hospital / Lifecare Diagnostic Center',
        'address' => 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali',
        'email' => 'dr.rashedul@lifecarediagnostic.com',
        'phone' => '01829952595',
        'phone_secondary' => '01727696977',
        'profile' => 'Specialist Physician at Dhaka Medical College & Hospital. Expert in Internal Medicine, Neuro Medicine, Heart Disease, and Diabetes.',
        'img_url' => 'uploads/doctors/dr_rashelul_islam.jpg',
        'consultation_fee' => 700.00,
        'followup_fee' => 400.00,
        'schedule_flag' => NULL,
        'active' => 1
    ],
    53 => [
        'name' => 'Dr. Basabi Ray',
        'name_en' => 'Dr. Basabi Ray',
        'designation' => 'Consultant Gynecologist and Surgeon',
        'degrees' => 'MBBS (Dhaka), BCS (Health), MS (Gynae & Obs), MCPS (Gynae & Obs)',
        'department_name' => 'Gynecology & Obstetrics',
        'specialization' => 'Gynecology, Obstetrics, Infertility, Women\'s Health',
        'experience' => 'Consultant, 250 Bed District General Hospital, Noakhali. Advanced training in Infertility.',
        'chamber' => '250 Bed District General Hospital, Noakhali / Lifecare Diagnostic Center',
        'address' => 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali',
        'email' => 'dr.basabi@lifecarediagnostic.com',
        'phone' => '01829952595',
        'phone_secondary' => '01727696977',
        'profile' => 'Consultant Gynecologist and Surgeon at 250 Bed District General Hospital, Noakhali. Specialist in Gynecology, Obstetrics, Infertility, and Women\'s Health with Advanced Training in Infertility Care.',
        'img_url' => 'uploads/cardiology-patient-icon-vector-6244713.jpg',
        'consultation_fee' => 700.00,
        'followup_fee' => 400.00,
        'schedule_flag' => 'Consultation hours unconfigured - Thursday only - administrator confirmation required',
        'active' => 1
    ],
    54 => [
        'name' => 'Dr. Sabiha Sultana',
        'name_en' => 'Dr. Sabiha Sultana',
        'designation' => 'Consultant Obstetrician & Gynecologist',
        'degrees' => 'MBBS, PGT (Gynae & Obs), CMU',
        'department_name' => 'Gynecology & Obstetrics',
        'specialization' => 'Obstetrics, Gynecology & Sonologist',
        'experience' => 'Consultant, Lifecare Diagnostic Center',
        'chamber' => 'Lifecare Diagnostic Center',
        'address' => 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali',
        'email' => 'dr.sabiha@lifecarediagnostic.com',
        'phone' => '01829952595',
        'phone_secondary' => '01727696977',
        'profile' => 'Experienced in Obstetrics, Gynecology, and Sonology.',
        'img_url' => 'uploads/cardiology-patient-icon-vector-6244713.jpg',
        'consultation_fee' => 700.00,
        'followup_fee' => 400.00,
        'schedule_flag' => NULL,
        'active' => 1
    ],
    55 => [
        'name' => 'Dr. Md. Abu Sufiyan',
        'name_en' => 'Dr. Md. Abu Sufiyan',
        'designation' => 'General Practitioner',
        'degrees' => 'MBBS, CMU (Ultrasound)',
        'department_name' => 'General Medicine & Pediatrics',
        'specialization' => 'General Medicine, Pediatrics, ENT, Dermatology, Allergy, Sonology',
        'experience' => 'General Practitioner & Sonologist, Lifecare Diagnostic Center',
        'chamber' => 'Lifecare Diagnostic Center',
        'address' => 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali',
        'email' => 'dr.sufiyan@lifecarediagnostic.com',
        'phone' => '01829952595',
        'phone_secondary' => '01727696977',
        'profile' => 'General Practitioner and Sonologist. Experienced in General Medicine, Pediatrics, Dermatology, Allergy, ENT, and Sonology.',
        'img_url' => 'uploads/cardiology-patient-icon-vector-6244713.jpg',
        'consultation_fee' => 700.00,
        'followup_fee' => 400.00,
        'schedule_flag' => NULL,
        'active' => 1
    ],
    56 => [
        'name' => 'Dr. Md. Mamunur Rashid',
        'name_en' => 'Dr. Md. Mamunur Rashid',
        'designation' => 'Specialist in Medicine & Cardiology',
        'degrees' => 'MBBS, PGT (Medicine), CCD (BIRDEM)',
        'department_name' => 'Medicine & Cardiology',
        'specialization' => 'Medicine, Neuro Medicine, Heart Disease, Chest Disease, Diabetes',
        'experience' => 'Specialist Physician, Lifecare Diagnostic Center',
        'chamber' => 'Lifecare Diagnostic Center',
        'address' => 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali',
        'email' => 'dr.mamunur@lifecarediagnostic.com',
        'phone' => '01829952595',
        'phone_secondary' => '01727696977',
        'profile' => 'Experienced in Medicine, Neuro Medicine, Heart Disease, Chest Disease, and Diabetes.',
        'img_url' => 'uploads/cardiology-patient-icon-vector-6244713.jpg',
        'consultation_fee' => 700.00,
        'followup_fee' => 400.00,
        'schedule_flag' => NULL,
        'active' => 1
    ],
    57 => [
        'name' => 'Dr. Jannatun Naim',
        'name_en' => 'Dr. Jannatun Naim',
        'designation' => 'Consultant in Obstetrics & Gynecology',
        'degrees' => 'MBBS, PGT (Gynae & Obs), CMU',
        'department_name' => 'Gynecology & Obstetrics',
        'specialization' => 'Obstetrics, Gynecology, Diabetes, Sonology',
        'experience' => 'Consultant, Lifecare Diagnostic Center',
        'chamber' => 'Lifecare Diagnostic Center',
        'address' => 'Khaserhat Rastermatha, Charbata, Subarnachar, Noakhali',
        'email' => 'dr.jannatun@lifecarediagnostic.com',
        'phone' => '01829952595',
        'phone_secondary' => '01727696977',
        'profile' => 'Experienced in Obstetrics, Gynecology, Diabetes, and Sonology.',
        'img_url' => 'uploads/cardiology-patient-icon-vector-6244713.jpg',
        'consultation_fee' => 700.00,
        'followup_fee' => 400.00,
        'schedule_flag' => 'Consultation hours unconfigured - Every day except Friday - administrator confirmation required',
        'active' => 1
    ],
];

foreach ($doctors_data as $id => $doc) {
    $dept_id = $dept_map[$doc['department_name']] ?? '';
    
    // Check if doctor currently has custom img_url
    $cur_doc = $mysqli->query("SELECT img_url FROM `doctor` WHERE id = $id")->fetch_assoc();
    $img_to_use = $doc['img_url'];
    if (!empty($cur_doc['img_url']) && strpos($cur_doc['img_url'], 'doctors/') !== false) {
        $img_to_use = $cur_doc['img_url'];
    }

    $stmt = $mysqli->prepare("UPDATE `doctor` SET 
        `name` = ?,
        `name_en` = ?,
        `designation` = ?,
        `degrees` = ?,
        `department` = ?,
        `department_name` = ?,
        `specialization` = ?,
        `experience` = ?,
        `chamber` = ?,
        `address` = ?,
        `email` = ?,
        `phone` = ?,
        `phone_secondary` = ?,
        `profile` = ?,
        `img_url` = ?,
        `consultation_fee` = ?,
        `followup_fee` = ?,
        `schedule_flag` = ?,
        `active` = ?
        WHERE `id` = ?");

    $stmt->bind_param(
        "sssssssssssssssddssi",
        $doc['name'],
        $doc['name_en'],
        $doc['designation'],
        $doc['degrees'],
        $dept_id,
        $doc['department_name'],
        $doc['specialization'],
        $doc['experience'],
        $doc['chamber'],
        $doc['address'],
        $doc['email'],
        $doc['phone'],
        $doc['phone_secondary'],
        $doc['profile'],
        $img_to_use,
        $doc['consultation_fee'],
        $doc['followup_fee'],
        $doc['schedule_flag'],
        $doc['active'],
        $id
    );
    $stmt->execute();
    echo "Doctor ID $id ({$doc['name']}) updated.\n";

    // Update users table for the doctor's ion user
    $ion_id_res = $mysqli->query("SELECT ion_user_id FROM `doctor` WHERE id = $id");
    if ($ion_row = $ion_id_res->fetch_assoc()) {
        $ion_id = $ion_row['ion_user_id'];
        if (!empty($ion_id)) {
            $ustmt = $mysqli->prepare("UPDATE `users` SET `username` = ?, `first_name` = ?, `email` = ? WHERE `id` = ?");
            $ustmt->bind_param("sssi", $doc['name'], $doc['name'], $doc['email'], $ion_id);
            $ustmt->execute();
            echo "  Ion User ID $ion_id updated to English name.\n";
        }
    }
}

// Ensure other existing doctors (e.g. 44, 42, 27, 50) have consultation_fee = 700
$mysqli->query("UPDATE `doctor` SET `consultation_fee` = 700.00 WHERE `consultation_fee` IS NULL OR `consultation_fee` = 0");

echo "\n=== STEP 5: CONSULTATION FEES (BDT 700 MANDATORY RULE) ===\n";
// Update doctor_visit table
// Check all doctors in doctor table
$all_doc_res = $mysqli->query("SELECT id, name, consultation_fee, followup_fee, hospital_id FROM `doctor`");
while ($doc_row = $all_doc_res->fetch_assoc()) {
    $doc_id = $doc_row['id'];
    $doc_name = $doc_row['name'];
    $hosp_id = $doc_row['hospital_id'] ?: '98';

    // 1. Consultation Fee record
    $vres = $mysqli->query("SELECT id, visit_charges, visit_description FROM `doctor_visit` WHERE `doctor_id` = '$doc_id' AND (`visit_description` LIKE '%1st%' OR `visit_description` LIKE '%Consultation%')");
    if ($vres && $vres->num_rows > 0) {
        $vrow = $vres->fetch_assoc();
        $vid = $vrow['id'];
        $mysqli->query("UPDATE `doctor_visit` SET `doctor_name` = '$doc_name', `visit_charges` = '700', `visit_description` = 'Standard Consultation Fee', `status` = 'active', `hospital_id` = '$hosp_id' WHERE `id` = $vid");
        echo "Updated doctor_visit ID $vid for Doctor ID $doc_id to 700 BDT.\n";
    } else {
        // Insert new consultation fee record
        $mysqli->query("INSERT INTO `doctor_visit` (`doctor_id`, `doctor_name`, `visit_description`, `visit_charges`, `status`, `hospital_id`) 
                        VALUES ('$doc_id', '$doc_name', 'Standard Consultation Fee', '700', 'active', '$hosp_id')");
        echo "Created doctor_visit for Doctor ID $doc_id: 700 BDT.\n";
    }

    // 2. Follow-up fee record
    $fres = $mysqli->query("SELECT id, visit_charges, visit_description FROM `doctor_visit` WHERE `doctor_id` = '$doc_id' AND (`visit_description` LIKE '%2nd%' OR `visit_description` LIKE '%Follow%')");
    if ($fres && $fres->num_rows > 0) {
        $frow = $fres->fetch_assoc();
        $fid = $frow['id'];
        $mysqli->query("UPDATE `doctor_visit` SET `doctor_name` = '$doc_name', `visit_description` = 'Follow-up Visit Fee', `status` = 'active', `hospital_id` = '$hosp_id' WHERE `id` = $fid");
        echo "Updated follow-up visit ID $fid for Doctor ID $doc_id (preserved {$frow['visit_charges']} BDT).\n";
    } else {
        $mysqli->query("INSERT INTO `doctor_visit` (`doctor_id`, `doctor_name`, `visit_description`, `visit_charges`, `status`, `hospital_id`) 
                        VALUES ('$doc_id', '$doc_name', 'Follow-up Visit Fee', '400', 'active', '$hosp_id')");
        echo "Created follow-up visit for Doctor ID $doc_id: 400 BDT.\n";
    }
}

echo "\n=== STEP 6: APPOINTMENT TIME SCHEDULES & SLOTS ===\n";

// Function to generate slots helper
function createScheduleAndSlots($mysqli, $doctor_id, $weekday, $s_time, $e_time, $slot_mins = 30) {
    // 5-min slot index map
    $slot_map = [];
    $start = strtotime('12:00 AM');
    for ($i = 0; $i < 288; $i++) {
        $slot_map[date('h:i A', $start + ($i * 300))] = $i;
    }

    $key1 = $slot_map[$s_time] ?? 0;
    $key2 = $slot_map[$e_time] ?? 0;
    $duration_units = (int)($slot_mins / 5);

    // Insert or update time_schedule
    $ch_res = $mysqli->query("SELECT id FROM `time_schedule` WHERE `doctor` = '$doctor_id' AND `weekday` = '$weekday'");
    if ($ch_res && $ch_res->num_rows > 0) {
        $sid = $ch_res->fetch_assoc()['id'];
        $mysqli->query("UPDATE `time_schedule` SET `s_time` = '$s_time', `e_time` = '$e_time', `s_time_key` = '$key1', `duration` = '$duration_units', `hospital_id` = '98' WHERE id = $sid");
    } else {
        $mysqli->query("INSERT INTO `time_schedule` (`doctor`, `weekday`, `s_time`, `e_time`, `s_time_key`, `duration`, `hospital_id`) 
                        VALUES ('$doctor_id', '$weekday', '$s_time', '$e_time', '$key1', '$duration_units', '98')");
    }

    // Delete existing time_slot for this doctor and weekday to regenerate cleanly
    $mysqli->query("DELETE FROM `time_slot` WHERE `doctor` = '$doctor_id' AND `weekday` = '$weekday'");

    // Generate slots
    $current_key = $key1;
    $slot_count = 0;
    while ($current_key + $duration_units <= $key2) {
        $next_key = $current_key + $duration_units;
        $slot_s_time = date('h:i A', $start + ($current_key * 300));
        $slot_e_time = date('h:i A', $start + ($next_key * 300));

        $mysqli->query("INSERT INTO `time_slot` (`doctor`, `s_time`, `e_time`, `weekday`, `s_time_key`, `hospital_id`) 
                        VALUES ('$doctor_id', '$slot_s_time', '$slot_e_time', '$weekday', '$current_key', '98')");
        $slot_count++;
        $current_key = $next_key;
    }
    echo "  $weekday: $s_time - $e_time ($slot_count slots of $slot_mins mins)\n";
}

// 1. Doctor 52: Dr. Md. Mahbubur Rahman (Mahbub)
// Friday 8:00 AM–11:00 AM
echo "Configuring Schedule for Doctor 52 (Dr. Md. Mahbubur Rahman):\n";
$mysqli->query("DELETE FROM `time_schedule` WHERE `doctor` = '52' AND `weekday` != 'Friday'");
$mysqli->query("DELETE FROM `time_slot` WHERE `doctor` = '52' AND `weekday` != 'Friday'");
createScheduleAndSlots($mysqli, '52', 'Friday', '08:00 AM', '11:00 AM', 30);

// 2. Doctor 51: Dr. Md. Rashelul Islam
// Friday 10:00 AM–04:00 PM
echo "Configuring Schedule for Doctor 51 (Dr. Md. Rashelul Islam):\n";
$mysqli->query("DELETE FROM `time_schedule` WHERE `doctor` = '51' AND `weekday` != 'Friday'");
$mysqli->query("DELETE FROM `time_slot` WHERE `doctor` = '51' AND `weekday` != 'Friday'");
createScheduleAndSlots($mysqli, '51', 'Friday', '10:00 AM', '04:00 PM', 30);

// 3. Doctor 53: Dr. Basabi Ray
// Thursday only, time unconfigured -> clear fabricated slots, flag for admin review
echo "Configuring Schedule for Doctor 53 (Dr. Basabi Ray):\n";
$mysqli->query("DELETE FROM `time_schedule` WHERE `doctor` = '53'");
$mysqli->query("DELETE FROM `time_slot` WHERE `doctor` = '53'");
echo "  Thursday consultation time left unconfigured and flagged for admin review.\n";

// 4. Doctor 55: Dr. Md. Abu Sufiyan
// Daily: Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday, Friday: 9:00 AM–9:00 PM
echo "Configuring Schedule for Doctor 55 (Dr. Md. Abu Sufiyan):\n";
$days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
foreach ($days as $day) {
    createScheduleAndSlots($mysqli, '55', $day, '09:00 AM', '09:00 PM', 30);
}

// 5. Doctor 57: Dr. Jannatun Naim
// Every day except Friday, hours unconfigured -> clear fabricated slots, flag for admin review
echo "Configuring Schedule for Doctor 57 (Dr. Jannatun Naim):\n";
$mysqli->query("DELETE FROM `time_schedule` WHERE `doctor` = '57'");
$mysqli->query("DELETE FROM `time_slot` WHERE `doctor` = '57'");
echo "  Every day except Friday left unconfigured and flagged for admin review.\n";

// 6. Doctor 56: Dr. Md. Mamunur Rashid
// Saturday to Friday 10:00 AM - 01:00 PM
echo "Configuring Schedule for Doctor 56 (Dr. Md. Mamunur Rashid):\n";
foreach ($days as $day) {
    createScheduleAndSlots($mysqli, '56', $day, '10:00 AM', '01:00 PM', 30);
}

// 7. Doctor 54: Dr. Sabiha Sultana
// Friday 09:00 AM - 02:00 PM
echo "Configuring Schedule for Doctor 54 (Dr. Sabiha Sultana):\n";
$mysqli->query("DELETE FROM `time_schedule` WHERE `doctor` = '54' AND `weekday` != 'Friday'");
$mysqli->query("DELETE FROM `time_slot` WHERE `doctor` = '54' AND `weekday` != 'Friday'");
createScheduleAndSlots($mysqli, '54', 'Friday', '09:00 AM', '02:00 PM', 30);

echo "\nMigration script completed successfully.\n";
