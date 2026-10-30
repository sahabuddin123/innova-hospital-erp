<?php
/**
 * Test script to verify labLabel2 view rendering for Specimen vs Imaging
 */

$m = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
if ($m->connect_error) die("DB error: " . $m->connect_error);

// Create dummy data mimicking CodeIgniter's $data array
$lab = (object)[
    'id' => 18,
    'invoice_id' => 9,
    'category_id' => 100, // CBC
    'date' => 1793281433,
    'patient' => 2,
    'doctor' => 52,
    'doctor_name' => 'Dr. Md. Mahbubur Rahman'
];

$patient = (object)[
    'id' => 2,
    'name' => 'SAKINA BEGUM',
    'age' => '32-0-0',
    'sex' => 'Female'
];

$doctor = (object)[
    'id' => 52,
    'name' => 'Dr. Md. Mahbubur Rahman'
];

$test_names = ['CBC', 'Blood Urea', 'Alkaline phosphatase'];
$specimen_type = 'BLOOD (EDTA)';
$is_imaging = false;
$default_mode = 'tube';
$silent = false;

// Mock CI functions
if (!function_exists('lang')) {
    function lang($k) { return $k; }
}
if (!function_exists('site_url')) {
    function site_url($uri) { return 'http://localhost/lifecare-hospital/' . $uri; }
}

ob_start();
include __DIR__ . '/../application/modules/lab/views/labLabel2.php';
$output = ob_get_clean();

echo "--- SPECIMEN TUBE RENDER TEST ---\n";
echo "Output length: " . strlen($output) . " bytes\n";

$checks = [
    'Contains 50mm' => strpos($output, '50mm') !== false,
    'Contains XP-350BM' => strpos($output, 'XP-350BM') !== false,
    'Contains SAKINA BEGUM' => strpos($output, 'SAKINA BEGUM') !== false,
    'Contains BLOOD (EDTA)' => strpos($output, 'BLOOD (EDTA)') !== false,
    'Contains 32Y' => strpos($output, '32Y') !== false,
    'Contains barcode URL' => strpos($output, 'lab/barcode') !== false,
    'Contains Silent Print modal' => strpos($output, 'xpGuideModal') !== false,
    'Contains Kiosk Printing' => strpos($output, '--kiosk-printing') !== false
];

$all_passed = true;
foreach ($checks as $name => $passed) {
    echo ($passed ? "[PASS]" : "[FAIL]") . " " . $name . "\n";
    if (!$passed) $all_passed = false;
}

if ($all_passed) {
    echo "\nSUCCESS: Specimen barcode view renders completely and accurately!\n";
} else {
    echo "\nERROR: Some checks failed!\n";
    exit(1);
}
