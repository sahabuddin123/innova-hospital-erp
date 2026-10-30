<?php
$m = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $m->query("SELECT id, name, category_id, hospital_id, LENGTH(template) as len FROM template WHERE category_id = 100 OR name LIKE '%CBC%'");
while ($r = $res->fetch_assoc()) {
    echo "ID: {$r['id']} | Name: {$r['name']} | Cat ID: {$r['category_id']} | Hosp ID: {$r['hospital_id']} | Length: {$r['len']}\n";
}
