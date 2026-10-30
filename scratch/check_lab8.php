<?php
foreach (['lifecare', 'lcare_db'] as $db) {
    $c = new mysqli('127.0.0.1', 'root', '', $db, 3306);
    $res = $c->query("SELECT id, date, invoice_id, patient, LENGTH(report) as rlen FROM lab WHERE id=8");
    if ($res && $row = $res->fetch_assoc()) {
        echo "$db: Lab 8 found! Date: " . $row['date'] . ", invoice_id: " . $row['invoice_id'] . ", len: " . $row['rlen'] . "\n";
    } else {
        echo "$db: Lab 8 not found\n";
    }
}
