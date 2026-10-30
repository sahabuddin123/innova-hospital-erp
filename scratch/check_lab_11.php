<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SELECT id, patient_name, category_name, category_id, report FROM lab WHERE id = 11");
$r = $res->fetch_assoc();
echo "Lab ID 11: " . $r['patient_name'] . " | Cat: " . $r['category_name'] . " | Cat ID: " . $r['category_id'] . "\n";
echo "Report Length: " . strlen($r['report']) . "\n";
