<?php
$m = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $m->query("SELECT id, invoice_id, category_id, report FROM lab WHERE id = 8");
$row = $res->fetch_assoc();
echo "Lab ID: " . $row['id'] . "\n";
echo "Invoice ID: " . $row['invoice_id'] . "\n";
echo "Category ID: " . $row['category_id'] . "\n";
echo "Report Length: " . strlen($row['report']) . "\n";
echo "Report snippet (first 1000 chars):\n" . substr($row['report'], 0, 1000) . "\n";
