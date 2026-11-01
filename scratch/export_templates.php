<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SELECT id, name, category_id, hospital_id FROM template WHERE id >= 35 ORDER BY id ASC");
$all = [];
while ($r = $res->fetch_assoc()) {
    $all[] = $r;
}
echo "Total newly added templates: " . count($all) . "\n";
foreach ($all as $t) {
    echo $t['id'] . "\t" . $t['category_id'] . "\t" . $t['name'] . "\n";
}
