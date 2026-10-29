<?php
define('BASEPATH', 'something');
define('ENVIRONMENT', 'development');
require_once 'application/config/database.php';
$db_conf = $db['default'];
$pdo = new PDO("mysql:host={$db_conf['hostname']};dbname={$db_conf['database']};charset=utf8", $db_conf['username'], $db_conf['password']);

$ids = [105, 159, 164];
foreach ($ids as $id) {
    $stmt = $pdo->query("SELECT id, category_name FROM payment WHERE category_name LIKE '%{$id}*%'");
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Payment referencing ID $id: " . count($payments) . "\n";

    $stmt = $pdo->query("SELECT id, category_name FROM draft_payment WHERE category_name LIKE '%{$id}*%'");
    $drafts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Draft referencing ID $id: " . count($drafts) . "\n";
}
