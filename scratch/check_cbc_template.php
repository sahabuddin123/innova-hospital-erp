<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SELECT id, name, category_id, hospital_id FROM template WHERE name LIKE '%CBC%' OR name LIKE '%Complete Blood%'");
while ($r = $res->fetch_assoc()) {
    echo "ID: " . $r['id'] . " | Name: " . $r['name'] . " | Cat: " . $r['category_id'] . " | Hosp: " . $r['hospital_id'] . "\n";
}
$cat_res = $conn->query("SELECT id, category FROM payment_category WHERE category LIKE '%CBC%'");
while ($c = $cat_res->fetch_assoc()) {
    echo "Category ID: " . $c['id'] . " | Name: " . $c['category'] . "\n";
}
