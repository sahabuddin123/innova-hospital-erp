<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SELECT id, name, template FROM template WHERE name LIKE '%Urine%' OR name LIKE '%Dengue%' LIMIT 2");
while ($r = $res->fetch_assoc()) {
    echo "=== Template: " . $r['name'] . " ===\n";
    echo substr($r['template'], 0, 300) . "\n...\n";
}
