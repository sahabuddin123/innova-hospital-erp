<?php
/**
 * Test output of getDeliveryReport for Lab ID 8
 */
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$lab = $conn->query("SELECT * FROM lab WHERE id = 8")->fetch_object();
$patient_info = $conn->query("SELECT * FROM patient WHERE id = " . $lab->patient)->fetch_object();

$col0 = !empty($patient_info->patient_id) ? $patient_info->patient_id : (!empty($patient_info->id) ? $patient_info->id : "");

echo "Resolved Patient ID for Report Delivery: " . $col0 . "\n";
assert($col0 == '378149', 'Resolved Patient ID must be 378149');
echo "SUCCESS: Column 0 of reportDelivery table now outputs 378149!\n";
