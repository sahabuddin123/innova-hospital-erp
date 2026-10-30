<?php
/**
 * Test Patient Info Table Rendering for Lab ID 8
 */
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$lab = $conn->query("SELECT * FROM lab WHERE id = 8")->fetch_object();
$patient = $conn->query("SELECT * FROM patient WHERE id = " . $lab->patient)->fetch_object();
$invoice_details = $conn->query("SELECT * FROM payment WHERE id = " . $lab->invoice_id)->fetch_object();

$billing_id = !empty($lab->invoice_id) ? $lab->invoice_id : (!empty($invoice_details->id) ? $invoice_details->id : '-');
$patient_id = !empty($patient->patient_id) ? $patient->patient_id : (!empty($patient->id) ? $patient->id : (!empty($lab->patient) ? $lab->patient : '-'));

echo "Billing ID: " . $billing_id . "\n";
echo "Patient ID: " . $patient_id . "\n";

assert($billing_id == '2', 'Billing ID must be 2');
assert($patient_id == '378149', 'Patient ID must be 378149');

echo "SUCCESS: Both Billing ID and Patient ID resolved correctly!\n";
