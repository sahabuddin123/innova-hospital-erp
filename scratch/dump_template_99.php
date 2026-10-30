<?php
$m = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $m->query("SELECT template FROM template WHERE id = 99");
$r = $res->fetch_assoc();
echo $r['template'];
