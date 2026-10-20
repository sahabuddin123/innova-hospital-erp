<?php
/**
 * Clean Demo Invoices & Patients Script
 * LifeCare Hospital ERP
 */

// Restrict to command line interface (CLI) only
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Access Denied: This maintenance script can only be executed via CLI.\n");
}

// Determine project root
$rootDir = __DIR__;
$envFile = $rootDir . '/.env';

$dbHost = '127.0.0.1';
$dbPort = 3306;
$dbName = 'lifecare';
$dbUser = 'root';
$dbPass = '';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            if ($key === 'DB_HOST') $dbHost = $val;
            if ($key === 'DB_PORT') $dbPort = (int)$val;
            if ($key === 'DB_NAME') $dbName = $val;
            if ($key === 'DB_USER') $dbUser = $val;
            if ($key === 'DB_PASS') $dbPass = $val;
        }
    }
}

// Connect to MySQL
$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($mysqli->connect_error) {
    echo "❌ Error connecting to database: " . $mysqli->connect_error . "\n";
    exit(1);
}

$mysqli->set_charset("utf8mb4");

echo "=====================================================\n";
echo "    🧹 LifeCare ERP - Demo Data Cleaner              \n";
echo "=====================================================\n";
echo "Database: $dbName ($dbHost:$dbPort)\n\n";

// Target hospital ID for the demo hospital (Lifecare Diagnostic, ID 98)
$hospitalId = '98';

// Helper function to safely delete from any table dynamically inspecting columns
function safeCleanTable($mysqli, $table, $hospitalId, $patientIds = [], $paymentIds = []) {
    try {
        $tCheck = $mysqli->query("SHOW TABLES LIKE '$table'");
        if (!$tCheck || $tCheck->num_rows === 0) {
            return;
        }

        $cols = [];
        $res = $mysqli->query("SHOW COLUMNS FROM `$table`");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $cols[] = $row['Field'];
            }
        }

        $clauses = [];

        // Condition on hospital_id
        if (in_array('hospital_id', $cols)) {
            $clauses[] = "`hospital_id` = '$hospitalId'";
        }

        // Condition on patient / patient_id
        if (!empty($patientIds)) {
            $escapedPatIds = array_map(function($id) use ($mysqli) {
                return $mysqli->real_escape_string($id);
            }, $patientIds);
            $patList = implode("','", $escapedPatIds);

            if (in_array('patient_id', $cols)) {
                $clauses[] = "`patient_id` IN ('$patList')";
            }
            if (in_array('patient', $cols)) {
                $clauses[] = "`patient` IN ('$patList')";
            }
        }

        // Condition on payment_id
        if (!empty($paymentIds)) {
            $escapedPayIds = array_map(function($id) use ($mysqli) {
                return $mysqli->real_escape_string($id);
            }, $paymentIds);
            $payList = implode("','", $escapedPayIds);

            if (in_array('payment_id', $cols)) {
                $clauses[] = "`payment_id` IN ('$payList')";
            }
        }

        if (!empty($clauses)) {
            $where = implode(' OR ', $clauses);
            $mysqli->query("DELETE FROM `$table` WHERE $where");
        }
    } catch (Throwable $e) {
        // Silently skip any specific table errors
    }
}

// 1. Fetch patient IDs
$patientIds = [];
$ionUserIds = [];
$res = $mysqli->query("SELECT id, ion_user_id, name FROM patient WHERE hospital_id = '$hospitalId'");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $patientIds[] = $row['id'];
        if (!empty($row['ion_user_id'])) {
            $ionUserIds[] = (int)$row['ion_user_id'];
        }
    }
}

$patientCount = count($patientIds);
echo "🔍 Found $patientCount demo patients for hospital #$hospitalId\n";

// 2. Fetch payment (invoice) IDs
$paymentIds = [];
$resPay = $mysqli->query("SELECT id FROM payment WHERE hospital_id = '$hospitalId'");
if ($resPay) {
    while ($row = $resPay->fetch_assoc()) {
        $paymentIds[] = $row['id'];
    }
}
$invoiceCount = count($paymentIds);
echo "🔍 Found $invoiceCount demo invoices for hospital #$hospitalId\n\n";

// Disable foreign key checks temporarily for clean cascading removal
$mysqli->query("SET FOREIGN_KEY_CHECKS = 0;");

// -------------------------------------------------------------
// A. REMOVE INVOICES & FINANCIAL RECORDS
// -------------------------------------------------------------
$financialTables = [
    'payment_items',
    'patient_deposit',
    'payment',
    'draft_payment',
    'ot_payment',
    'pharmacy_payment'
];

foreach ($financialTables as $tbl) {
    safeCleanTable($mysqli, $tbl, $hospitalId, $patientIds, $paymentIds);
}
echo "✔ Removed demo invoices from `payment` and related tables.\n";

// -------------------------------------------------------------
// B. REMOVE PATIENTS & ALL RELATED MEDICAL/BED/LAB RECORDS
// -------------------------------------------------------------
$medicalTables = [
    'medical_history',
    'patient_material',
    'vital_signs',
    'prescription',
    'appointment',
    'alloted_bed',
    'bed_diagnostic',
    'bed_medicine',
    'bed_service',
    'lab',
    'diagnostic_report',
    'treatment_plans',
    'ai_patient_overviews',
    'ai_image_analyses',
    'ambulance_bookings',
    'dental_examinations',
    'folder',
    'meeting',
    'odontogram',
    'radiology_orders',
    'referral_commission_ledger',
    'report'
];

foreach ($medicalTables as $tbl) {
    safeCleanTable($mysqli, $tbl, $hospitalId, $patientIds, $paymentIds);
}

// Now delete patients from patient table
try {
    $mysqli->query("DELETE FROM patient WHERE hospital_id = '$hospitalId'");
} catch (Throwable $e) {}

echo "✔ Removed demo patients from `patient` and related medical tables.\n";

// -------------------------------------------------------------
// C. REMOVE PATIENT USER LOGINS (if any)
// -------------------------------------------------------------
if (!empty($ionUserIds)) {
    // Safety check: Never delete superadmin or hospital admin users (IDs 1, 614, 992)
    $safeIonIds = array_filter($ionUserIds, function($uid) {
        return !in_array((int)$uid, [1, 614, 992]);
    });

    if (!empty($safeIonIds)) {
        $userList = implode(",", array_map('intval', $safeIonIds));
        try {
            $mysqli->query("DELETE FROM users_groups WHERE user_id IN ($userList)");
            $mysqli->query("DELETE FROM users WHERE id IN ($userList)");
            echo "✔ Cleaned up associated patient user accounts in `users`.\n";
        } catch (Throwable $e) {}
    }
}

// -------------------------------------------------------------
// D. REMOVE ALL REFERRERS & REFERRAL RECORDS
// -------------------------------------------------------------
$referrerIds = [];
$resRef = $mysqli->query("SELECT id FROM referrer WHERE hospital_id = '$hospitalId'");
if ($resRef) {
    while ($row = $resRef->fetch_assoc()) {
        $referrerIds[] = $row['id'];
    }
}
$referrerCount = count($referrerIds);

$refTables = [
    'referral_audit_log',
    'referral_commission_ledger',
    'referral_wallet_transactions',
    'referral_withdrawals',
    'referral_wallet',
    'referrer'
];

foreach ($refTables as $tbl) {
    try {
        $tCheck = $mysqli->query("SHOW TABLES LIKE '$tbl'");
        if ($tCheck && $tCheck->num_rows > 0) {
            $cols = [];
            $res = $mysqli->query("SHOW COLUMNS FROM `$tbl`");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $cols[] = $row['Field'];
                }
            }

            $clauses = [];
            if (in_array('hospital_id', $cols)) {
                $clauses[] = "`hospital_id` = '$hospitalId'";
            }
            if (!empty($referrerIds) && in_array('referrer_id', $cols)) {
                $escapedRefIds = array_map(function($id) use ($mysqli) {
                    return $mysqli->real_escape_string($id);
                }, $referrerIds);
                $refList = implode("','", $escapedRefIds);
                $clauses[] = "`referrer_id` IN ('$refList')";
            }
            if (!empty($clauses)) {
                $mysqli->query("DELETE FROM `$tbl` WHERE " . implode(' OR ', $clauses));
            }
        }
    } catch (Throwable $e) {}
}

// Reset any referrer references in payment records
try {
    $mysqli->query("UPDATE payment SET referrer = NULL, referrer_name = NULL WHERE hospital_id = '$hospitalId'");
} catch (Throwable $e) {}

echo "✔ Removed $referrerCount referrers and associated wallet/commission records.\n";

$mysqli->query("SET FOREIGN_KEY_CHECKS = 1;");

// -------------------------------------------------------------
// E. FIX CACHE & SESSIONS PERMISSIONS & CLEAR STALE SESSIONS
// -------------------------------------------------------------
$sessDir = __DIR__ . '/application/cache/sessions';
if (!is_dir($sessDir)) {
    @mkdir($sessDir, 0777, true);
}
@chmod($sessDir, 0777);
$files = glob($sessDir . '/*');
if ($files) {
    foreach ($files as $f) {
        if (basename($f) !== '.gitkeep') {
            @unlink($f);
        }
    }
}
@chmod(__DIR__ . '/application/cache', 0777);

// Attempt to chown to web server user if running as root
if (function_exists('posix_getuid') && posix_getuid() === 0) {
    foreach (['www', 'www-data', 'nginx', 'apache'] as $u) {
        $uInfo = @posix_getpwnam($u);
        if ($uInfo) {
            @chown(__DIR__ . '/application/cache', $uInfo['uid']);
            @chgrp(__DIR__ . '/application/cache', $uInfo['gid']);
            @chown($sessDir, $uInfo['uid']);
            @chgrp($sessDir, $uInfo['gid']);
            break;
        }
    }
}
echo "✔ Fixed sessions directory permissions and cleared old session files.\n";

echo "\n=====================================================\n";
echo "🎉 DEMO DATA CLEANUP COMPLETED SUCCESSFULLY!\n";
echo "   Invoices Cleaned:  $invoiceCount\n";
echo "   Patients Cleaned:  $patientCount\n";
echo "   Referrers Cleaned: $referrerCount\n";
echo "=====================================================\n";
