<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== TABLES MATCHING DOCTOR, SCHEDULE, HOLIDAY, TIME, VISIT, APPOINTMENT ===\n";
$res = $mysqli->query("SHOW TABLES");
$tables = [];
while ($row = $res->fetch_array()) {
    $t = $row[0];
    if (preg_match('/(doctor|schedul|holiday|leave|slot|time|visit|appoint|depart)/i', $t)) {
        $tables[] = $t;
    }
}
echo implode(", ", $tables) . "\n\n";

foreach ($tables as $t) {
    echo "--- Table: $t ---\n";
    $cres = $mysqli->query("DESCRIBE `$t`");
    while ($crow = $cres->fetch_assoc()) {
        echo "  {$crow['Field']} ({$crow['Type']})\n";
    }
    $cnt = $mysqli->query("SELECT count(*) as c FROM `$t`")->fetch_assoc()['c'];
    echo "  Total records: $cnt\n";
}

echo "\n=== ALL DOCTOR RECORDS ===\n";
$dres = $mysqli->query("SELECT * FROM `doctor`");
while ($drow = $dres->fetch_assoc()) {
    echo "ID: {$drow['id']} | IonUser: {$drow['ion_user_id']} | Name: {$drow['name']} | EngName: " . ($drow['name_en'] ?? 'N/A') . " | Email: {$drow['email']} | Phone: {$drow['phone']} | Dept: {$drow['department']} | Degrees: " . ($drow['degrees'] ?? 'N/A') . " | Status: " . ($drow['status'] ?? 'N/A') . "\n";
    echo "  Profile: " . substr(strip_tags($drow['profile'] ?? ''), 0, 120) . "\n";
}

echo "\n=== ALL DEPARTMENTS ===\n";
$depres = $mysqli->query("SELECT * FROM `department`");
while ($deprow = $depres->fetch_assoc()) {
    echo "ID: {$deprow['id']} | Name: {$deprow['name']} | Description: " . substr(strip_tags($deprow['description'] ?? ''), 0, 80) . "\n";
}

