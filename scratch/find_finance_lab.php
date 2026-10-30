<?php
$lines = file('application/modules/finance/controllers/Finance.php');
foreach ($lines as $i => $l) {
    if (stripos($l, 'insertLab') !== false) {
        echo ($i + 1) . ": " . trim($l) . "\n";
    }
}
