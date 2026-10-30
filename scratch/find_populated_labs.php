<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SELECT id, patient_name, category_name, category_id, report FROM lab WHERE report IS NOT NULL AND report != '' LIMIT 5");
while ($r = $res->fetch_assoc()) {
    echo "Lab ID: " . $r['id'] . " | Patient: " . $r['patient_name'] . " | Cat: " . $r['category_name'] . " | Report len: " . strlen($r['report']) . "\n";
}
