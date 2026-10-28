<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$u = $mysqli->query("SELECT id, username, email, password FROM users WHERE id = 992")->fetch_assoc();
echo "Email: " . $u['email'] . "\n";
echo "Password hash: " . $u['password'] . "\n";

// Test common passwords against password_verify
$passwords = ['admin', 'password', '123456', '12345', 'admin123', 'admin@hms.com', 'lifecare'];
foreach ($passwords as $p) {
    if (password_verify($p, $u['password'])) {
        echo "MATCH FOUND: '$p'\n";
    }
}
