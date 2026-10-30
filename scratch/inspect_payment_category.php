<?php
$m = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $m->query("SELECT DISTINCT payment_category, payment_category_name FROM payment_category WHERE type = 'diagnostic' ORDER BY payment_category");
while ($r = $res->fetch_assoc()) {
    echo "P_Cat: {$r['payment_category']} | Name: {$r['payment_category_name']}\n";
}
