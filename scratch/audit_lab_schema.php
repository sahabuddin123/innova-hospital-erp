<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SELECT id, category, code, c_price, hospital_id FROM payment_category ORDER BY id ASC");
$all_tests = [];
while ($row = $res->fetch_assoc()) {
    $all_tests[] = $row;
}

echo "Total tests: " . count($all_tests) . "\n";
foreach ($all_tests as $t) {
    echo "ID: {$t['id']} | Code: {$t['code']} | Name: {$t['category']} | Price: {$t['c_price']} | Hosp: {$t['hospital_id']}\n";
}
