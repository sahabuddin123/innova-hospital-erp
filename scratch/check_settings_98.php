<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SELECT title, address, phone, email, logo FROM settings WHERE hospital_id = '98'");
$row = $res->fetch_assoc();
print_r($row);
