<?php
$m = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
foreach (['patient', 'doctor', 'settings', 'payment_category'] as $tbl) {
    echo "=== $tbl ===\n";
    $res = $m->query("DESCRIBE $tbl");
    while ($r = $res->fetch_assoc()) {
        echo $r['Field'] . ' (' . $r['Type'] . ")\n";
    }
}
