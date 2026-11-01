<?php
/**
 * One-Click Laboratory Templates Importer for Live Server
 * Upload this file and `lab_templates_88.sql` to your live server root, then run this file via browser or CLI:
 * `https://lifecare.innovacomputersbd.com/import_templates_live.php`
 */

@ini_set('display_errors', 1);
@error_reporting(E_ALL);
@set_time_limit(300);

// CodeIgniter environment constants required by database.php
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'production');
}
if (!defined('BASEPATH')) {
    define('BASEPATH', true);
}

echo "<pre style='font-family: Consolas, monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; border-radius: 8px; line-height: 1.5;'>";
echo "<h3 style='color: #4ec9b0;'>Lifecare Lab Templates Live Importer</h3>\n";

$db_config_file = __DIR__ . '/application/config/database.php';
if (!file_exists($db_config_file)) {
    die("<span style='color: #f44747;'>ERROR: Database config file not found at " . htmlspecialchars($db_config_file) . "</span></pre>");
}

require_once $db_config_file;

$db = $db['default'];
$host = isset($_SERVER['DB_HOST']) ? $_SERVER['DB_HOST'] : (getenv('DB_HOST') ?: $db['hostname']);
$user = isset($_SERVER['DB_USER']) ? $_SERVER['DB_USER'] : (getenv('DB_USER') ?: $db['username']);
$pass = isset($_SERVER['DB_PASS']) ? $_SERVER['DB_PASS'] : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : $db['password']);
$name = isset($_SERVER['DB_NAME']) ? $_SERVER['DB_NAME'] : (getenv('DB_NAME') ?: $db['database']);
$port = isset($_SERVER['DB_PORT']) ? (int)$_SERVER['DB_PORT'] : (getenv('DB_PORT') ? (int)getenv('DB_PORT') : ($db['port'] ?? 3306));

echo "Connecting to Database: <strong>{$name}</strong> on <strong>{$host}</strong>...\n";
$conn = new mysqli($host, $user, $pass, $name, $port);

if ($conn->connect_error) {
    die("<span style='color: #f44747;'>Connection failed: " . htmlspecialchars($conn->connect_error) . "</span></pre>");
}

$conn->set_charset('utf8mb4');
echo "<span style='color: #6a9955;'>Database connected successfully!</span>\n\n";

$sql_file = __DIR__ . '/lab_templates_88.sql';
if (!file_exists($sql_file)) {
    die("<span style='color: #f44747;'>ERROR: SQL file not found at: " . htmlspecialchars($sql_file) . "</span>\nPlease make sure lab_templates_88.sql is uploaded to the same directory.</pre>");
}

echo "Reading lab_templates_88.sql (" . round(filesize($sql_file) / 1024, 1) . " KB)...\n";
$sql_content = file_get_contents($sql_file);

echo "Executing SQL queries...\n";
if ($conn->multi_query($sql_content)) {
    $executed = 0;
    do {
        $executed++;
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->more_results() && $conn->next_result());

    if ($conn->errno) {
        echo "<span style='color: #f44747;'>Query warning/error during execution: " . htmlspecialchars($conn->error) . "</span>\n";
    } else {
        echo "<span style='color: #4fc1ff; font-weight: bold;'>SUCCESS: All SQL queries executed successfully!</span>\n\n";
    }
} else {
    die("<span style='color: #f44747;'>ERROR executing SQL: " . htmlspecialchars($conn->error) . "</span></pre>");
}

// Verification count
$check = $conn->query("SELECT COUNT(*) AS total FROM `template`");
if ($check) {
    $total = $check->fetch_assoc()['total'];
    echo "<span style='color: #b5cea8; font-size: 15px;'>Verification: Total templates in live `template` table: <strong>{$total}</strong></span>\n\n";
}

echo "<span style='color: #ce9178;'>NOTE: Import complete! You can now safely delete `import_templates_live.php` from your server.</span>\n";
echo "</pre>";
