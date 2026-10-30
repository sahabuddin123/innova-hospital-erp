<?php
$c = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$r = $c->query("SELECT report FROM lab WHERE id=8")->fetch_assoc();
file_put_contents('scratch/lab8_report.html', $r['report']);
echo "Dumped " . strlen($r['report']) . " bytes to scratch/lab8_report.html\n";
