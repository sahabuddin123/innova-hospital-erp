<?php
$lab = (object)[
    'id' => 18,
    'invoice_id' => 9,
    'category_id' => 100,
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

$test_names = ['CBC'];
$specimen_type = 'BLOOD (EDTA)';
$is_imaging = false;
$default_mode = 'tube';
$silent = true; // SILENT MODE

if (!function_exists('lang')) { function lang($k) { return $k; } }
if (!function_exists('site_url')) { function site_url($uri) { return 'http://localhost/lifecare-hospital/' . $uri; } }

ob_start();
include __DIR__ . '/../application/modules/lab/views/labLabel2.php';
$output = ob_get_clean();

echo "--- SILENT PRINT OUTPUT TEST ---\n";
echo "Output length: " . strlen($output) . " bytes\n";

$checks = [
    'Contains window.print()' => strpos($output, 'window.print()') !== false,
    'Contains 50mm 30mm page' => strpos($output, '50mm 30mm') !== false,
    'Contains SAKINA BEGUM' => strpos($output, 'SAKINA BEGUM') !== false,
    'Contains onafterprint' => strpos($output, 'window.onafterprint') !== false,
    'NO dashboard content' => strpos($output, 'content-wrapper') === false,
    'NO sidebar content' => strpos($output, 'main-sidebar') === false
];

$all_passed = true;
foreach ($checks as $name => $passed) {
    echo ($passed ? "[PASS]" : "[FAIL]") . " " . $name . "\n";
    if (!$passed) $all_passed = false;
}

if ($all_passed) {
    echo "\nSUCCESS: Silent mode outputs pure thermal markup!\n";
} else {
    echo "\nERROR: Silent mode checks failed!\n";
    exit(1);
}
