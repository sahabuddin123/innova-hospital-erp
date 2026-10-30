<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$lab = $conn->query("SELECT id, patient, invoice_id, patient_name FROM lab WHERE id = 8")->fetch_assoc();
echo "LAB:\n";
print_r($lab);

$payment = $conn->query("SELECT id, patient, patient_name, doctor_name FROM payment WHERE id = " . $lab['invoice_id'])->fetch_assoc();
echo "PAYMENT:\n";
print_r($payment);

$patient = $conn->query("SELECT id, patient_id, name, age, sex FROM patient WHERE id = " . $payment['patient'])->fetch_assoc();
echo "PATIENT:\n";
print_r($patient);
