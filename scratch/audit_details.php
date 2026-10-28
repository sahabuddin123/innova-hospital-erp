<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);

echo "=== DOCTOR VISIT (CHARGES / FEES) ===\n";
$res = $mysqli->query("SELECT * FROM doctor_visit");
while ($r = $res->fetch_assoc()) {
    print_r($r);
}

echo "=== HOLIDAYS ===\n";
$res = $mysqli->query("SELECT * FROM holidays");
while ($r = $res->fetch_assoc()) {
    print_r($r);
}

echo "=== TIME SCHEDULE (Sample 10) ===\n";
$res = $mysqli->query("SELECT * FROM time_schedule LIMIT 10");
while ($r = $res->fetch_assoc()) {
    print_r($r);
}

echo "=== TIME SCHEDULE DOCTORS ===\n";
$res = $mysqli->query("SELECT doctor, count(*) as cnt FROM time_schedule GROUP BY doctor");
while ($r = $res->fetch_assoc()) {
    print_r($r);
}

echo "=== TIME SLOT DOCTORS ===\n";
$res = $mysqli->query("SELECT doctor, count(*) as cnt FROM time_slot GROUP BY doctor");
while ($r = $res->fetch_assoc()) {
    print_r($r);
}

echo "=== LEAVES ===\n";
$res = $mysqli->query("SELECT * FROM leaves");
while ($r = $res->fetch_assoc()) {
    print_r($r);
}
