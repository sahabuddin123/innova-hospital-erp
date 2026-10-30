<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("DESCRIBE lab");
while ($r = $res->fetch_assoc()) {
    echo $r['Field'] . " (" . $r['Type'] . ")\n";
}
