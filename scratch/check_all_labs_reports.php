<?php
$m = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $m->query("SELECT l.id, l.invoice_id, l.category_id, pc.category as test_name, LENGTH(l.report) as rlen FROM lab l LEFT JOIN payment_category pc ON pc.id = l.category_id ORDER BY l.id ASC");
while ($r = $res->fetch_assoc()) {
    echo "Lab ID: {$r['id']} | Inv: {$r['invoice_id']} | Cat: {$r['category_id']} ({$r['test_name']}) | Report Len: {$r['rlen']}\n";
}
