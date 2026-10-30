<?php
$m = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $m->query("SELECT template FROM template WHERE id = 99");
$row = $res->fetch_assoc();
echo "Template length: " . strlen($row['template']) . "\n";
echo "First 1000 chars:\n" . substr($row['template'], 0, 1000) . "\n";
