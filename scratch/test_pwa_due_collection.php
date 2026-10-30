<?php
/**
 * Test Suite: PWA Due Collection Offline Suite & API Sync
 */

echo "========================================================================\n";
echo "  LIFECARE DIAGNOSTIC CENTER — PWA DUE COLLECTION TEST SUITE\n";
echo "========================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $name) {
    global $passCount, $failCount;
    if ($condition) {
        echo " [PASS] " . $name . "\n";
        $passCount++;
    } else {
        echo " [FAIL] " . $name . "\n";
        $failCount++;
    }
}

// 1. Verify sw.js includes due collection assets & cache version v2.1
$swContent = file_get_contents(__DIR__ . '/../sw.js');
assertTest(strpos($swContent, "lifecare-pwa-v2.1") !== false, "sw.js cache version is bumped to v2.1");
assertTest(strpos($swContent, "finance/payments.css") !== false, "sw.js precaches finance/payments.css");
assertTest(strpos($swContent, "finance/due_collection.js") !== false, "sw.js precaches finance/due_collection.js");
assertTest(strpos($swContent, "finance/patient_deposit.js") !== false, "sw.js precaches finance/patient_deposit.js");

// 2. Verify manifest.json has Due Collection shortcut
$manifestContent = file_get_contents(__DIR__ . '/../manifest.json');
$manifest = json_decode($manifestContent, true);
$hasDueShortcut = false;
if (!empty($manifest['shortcuts'])) {
    foreach ($manifest['shortcuts'] as $sc) {
        if (strpos($sc['url'], 'finance/dueCollection') !== false) {
            $hasDueShortcut = true;
            break;
        }
    }
}
assertTest($hasDueShortcut, "manifest.json contains Due Collection shortcut");

// 3. Verify pwa_offline.js has due deposit offline logic
$pwaJs = file_get_contents(__DIR__ . '/../common/extranal/js/pwa_offline.js');
assertTest(strpos($pwaJs, "window.OfflineDB = OfflineDB;") !== false, "pwa_offline.js exposes OfflineDB to global window");
assertTest(strpos($pwaJs, "initDueCollectionInterception") !== false, "pwa_offline.js defines initDueCollectionInterception");
assertTest(strpos($pwaJs, "captureDueDepositData") !== false, "pwa_offline.js defines captureDueDepositData");
assertTest(strpos($pwaJs, "showOfflineDueReceiptModal") !== false, "pwa_offline.js defines showOfflineDueReceiptModal");
assertTest(strpos($pwaJs, "processOfflineDepositSubmit") !== false, "pwa_offline.js defines processOfflineDepositSubmit");
assertTest(strpos($pwaJs, "inv.type === 'due_deposit'") !== false, "pwa_offline.js sync engine handles due_deposit type");
assertTest(strpos($pwaJs, "finance/deposit") !== false, "pwa_offline.js posts due deposits to finance/deposit");
assertTest(strpos($pwaJs, "OFF-DEP-") !== false, "pwa_offline.js generates and recognizes OFF-DEP- tokens");

// 4. Verify due_collection.js has offline catalog caching and deposit modal fallback
$dueJs = file_get_contents(__DIR__ . '/../common/extranal/js/finance/due_collection.js');
assertTest(strpos($dueJs, "due_invoices_cache") !== false, "due_collection.js caches due invoices in IndexedDB");
assertTest(strpos($dueJs, "btn_quick_due_lookup") !== false, "due_collection.js handles quick due lookup");
assertTest(strpos($dueJs, "due_offline_banner") !== false, "due_collection.js handles offline banner toggle");

// 5. Verify due_collection.php has quick search UI & offline banner
$dueView = file_get_contents(__DIR__ . '/../application/modules/finance/views/due_collection.php');
assertTest(strpos($dueView, "due_offline_banner") !== false, "due_collection.php contains offline banner element");
assertTest(strpos($dueView, "quick_due_search_input") !== false, "due_collection.php contains quick due search input");
assertTest(strpos($dueView, "btn_quick_due_lookup") !== false, "due_collection.php contains collect due button");

// 6. Verify Finance.php deposit method offline sync response
$financeController = file_get_contents(__DIR__ . '/../application/modules/finance/controllers/Finance.php');
assertTest(strpos($financeController, "is_offline_sync") !== false, "Finance.php deposit method checks is_offline_sync");
assertTest(strpos($financeController, "'deposited_amount' => \$deposited_amount") !== false, "Finance.php returns deposited_amount in JSON response");

// 7. Verify offline.html features
$offlineHtml = file_get_contents(__DIR__ . '/../offline.html');
assertTest(strpos($offlineHtml, "Due Collection & Deposit Receipts operational offline") !== false, "offline.html lists Due Collection");

echo "\n========================================================================\n";
echo "  TEST SUMMARY: {$passCount} Passed, {$failCount} Failed\n";
echo "========================================================================\n";

if ($failCount === 0) {
    echo "SUCCESS: ALL PWA DUE COLLECTION TESTS PASSED!\n";
    exit(0);
} else {
    echo "FAILURE: SOME TESTS FAILED!\n";
    exit(1);
}
