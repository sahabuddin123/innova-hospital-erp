<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
echo "=== DOCTORS ===\n";
$res = $conn->query("SELECT id, name, profile FROM doctor WHERE hospital_id = '98' OR name LIKE '%Sufian%'");
while ($r = $res->fetch_assoc()) {
    echo $r['id'] . " | " . $r['name'] . " | " . $r['profile'] . "\n";
}
echo "=== LABORATORISTS ===\n";
$res2 = $conn->query("SELECT id, name, ion_user_id, signature FROM laboratorist");
while ($r = $res2->fetch_assoc()) {
    echo $r['id'] . " | " . $r['name'] . " | " . $r['signature'] . "\n";
}
