<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SELECT id, name, category_id, hospital_id FROM template WHERE category_id IN (89, 100) OR name LIKE '%CBC%'");
while ($r = $res->fetch_assoc()) {
    echo "ID: " . $r['id'] . " | Name: " . $r['name'] . " | Cat: " . $r['category_id'] . "\n";
}
