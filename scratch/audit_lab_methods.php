<?php
$lines = file('application/modules/lab/controllers/Lab.php');
foreach ($lines as $num => $line) {
    if (preg_match('/public\s+function\s+([a-zA-Z0-9_]*template[a-zA-Z0-9_]*|addLabView|addLab|labReport)/i', $line, $m)) {
        echo ($num + 1) . ": " . trim($line) . "\n";
    }
}
