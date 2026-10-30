<?php
$m = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $m->query('DESCRIBE lab');
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . ' (' . $row['Type'] . ")\n";
}
