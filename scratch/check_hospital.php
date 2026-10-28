<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);

echo "=== HOSPITALS ===\n";
$res = $mysqli->query("SELECT id, name, email, phone FROM hospital");
while ($r = $res->fetch_assoc()) {
    print_r($r);
}

echo "=== USERS (admin/hospital) ===\n";
$res = $mysqli->query("SELECT id, username, email, active, hospital_id FROM users WHERE email LIKE '%admin%' OR hospital_id IS NOT NULL LIMIT 10");
while ($r = $res->fetch_assoc()) {
    print_r($r);
}

echo "=== SESSIONS ===\n";
$res = $mysqli->query("SHOW TABLES LIKE '%session%'");
while ($r = $res->fetch_array()) {
    echo $r[0] . "\n";
}
