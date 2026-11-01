<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$conn->set_charset('utf8mb4');

$res = $conn->query("SELECT id, name, template, category_id, hospital_id, user FROM template WHERE id >= 35 ORDER BY id ASC");

$templates = [];
$cat_ids = [];
while ($r = $res->fetch_assoc()) {
    $templates[] = $r;
    if (!empty($r['category_id'])) {
        $cat_ids[] = "'" . $conn->real_escape_string($r['category_id']) . "'";
    }
}

$sql = "-- =========================================================================\n";
$sql .= "-- Laboratory & Radiology Report Templates (88 Tests)\n";
$sql .= "-- Lifecare Hospital / Diagnostic Management System\n";
$sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
$sql .= "-- =========================================================================\n\n";

$sql .= "-- -------------------------------------------------------------------------\n";
$sql .= "-- STEP 1: Ensure `template` column can store large styled HTML templates\n";
$sql .= "-- -------------------------------------------------------------------------\n";
$sql .= "ALTER TABLE `template` MODIFY COLUMN `template` LONGTEXT NULL;\n\n";

$sql .= "-- -------------------------------------------------------------------------\n";
$sql .= "-- STEP 2: Remove old templates for these categories (prevents duplicate rows)\n";
$sql .= "-- (Change hospital_id = '98' if your live server uses a different hospital ID)\n";
$sql .= "-- -------------------------------------------------------------------------\n";
$sql .= "DELETE FROM `template` \n";
$sql .= "WHERE `hospital_id` = '98' \n";
$sql .= "  AND `category_id` IN (" . implode(", ", $cat_ids) . ");\n\n";

$sql .= "-- -------------------------------------------------------------------------\n";
$sql .= "-- STEP 3: Insert 88 Clinical Report Templates\n";
$sql .= "-- -------------------------------------------------------------------------\n";

foreach ($templates as $idx => $r) {
    $num = $idx + 1;
    $name = $conn->real_escape_string($r['name']);
    $content = $conn->real_escape_string($r['template']);
    $category_id = $conn->real_escape_string($r['category_id']);
    $hospital_id = $conn->real_escape_string($r['hospital_id']);
    $user = !empty($r['user']) ? $conn->real_escape_string($r['user']) : '1';

    $sql .= "-- Template #{$num}: {$r['name']} (Category ID: {$category_id})\n";
    $sql .= "INSERT INTO `template` (`name`, `template`, `category_id`, `hospital_id`, `user`)\n";
    $sql .= "VALUES ('{$name}', '{$content}', '{$category_id}', '{$hospital_id}', '{$user}');\n\n";
}

file_put_contents('scratch/lab_templates_88.sql', $sql);
copy('scratch/lab_templates_88.sql', 'lab_templates_88.sql');

echo "Updated lab_templates_88.sql successfully! Total templates: " . count($templates) . "\n";
