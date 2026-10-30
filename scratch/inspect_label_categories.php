<?php
$m = new mysqli('127.0.0.1', 'root', '', '', 3306);
$res = $m->query("SHOW DATABASES");
while ($r = $res->fetch_row()) {
    echo $r[0] . "\n";
}
