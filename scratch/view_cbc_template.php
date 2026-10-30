<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SELECT template FROM template WHERE id = 99");
$row = $res->fetch_assoc();
echo $row['template'];
