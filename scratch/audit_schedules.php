<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $mysqli->query("SELECT * FROM time_schedule WHERE doctor IN ('51','52','53','54','55','56','57')");
while ($r = $res->fetch_assoc()) {
    echo "ID: {$r['id']} | Doctor: {$r['doctor']} | Day: {$r['weekday']} | S: {$r['s_time']} | E: {$r['e_time']} | Duration: {$r['duration']}\n";
}
