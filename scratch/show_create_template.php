<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SHOW CREATE TABLE template");
$row = $res->fetch_row();
echo $row[1] . "\n";

