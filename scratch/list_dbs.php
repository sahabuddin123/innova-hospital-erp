<?php
$c = new mysqli('127.0.0.1', 'root', '', '', 3306);
$res = $c->query("SHOW DATABASES");
while ($r = $res->fetch_array()) {
    echo $r[0] . "\n";
}
