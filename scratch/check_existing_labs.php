<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SELECT id, patient, category_name, date, invoice_id FROM lab ORDER BY id DESC LIMIT 5");
while ($r = $res->fetch_assoc()) {
    echo "Lab ID: " . $r['id'] . " | Patient: " . $r['patient'] . " | Test: " . $r['category_name'] . " | Date: " . $r['date'] . " | Inv: " . $r['invoice_id'] . "\n";
}
