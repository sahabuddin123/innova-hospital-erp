<?php
$lines = file('application/modules/lab/controllers/Lab.php');
foreach ($lines as $i => $l) {
    if (preg_match('/function\s+getTemplate\s*\(/i', $l)) {
        echo ($i + 1) . ": " . trim($l) . "\n";
    }
}
