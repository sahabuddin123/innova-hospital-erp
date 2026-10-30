<?php
$m = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $m->query("SELECT l.id, l.invoice_id, l.category_id, l.doctor, l.doctor_name, p.name as patient_name, p.age, p.sex FROM lab l LEFT JOIN patient p ON p.id = l.patient ORDER BY l.id DESC LIMIT 5");
while ($r = $res->fetch_assoc()) {
    print_r($r);
}
