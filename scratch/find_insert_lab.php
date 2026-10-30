<?php
$files = glob('application/modules/*/*/*.php');
foreach ($files as $f) {
    $c = file_get_contents($f);
    if (stripos($c, 'insertLab') !== false) {
        echo "Found in $f\n";
    }
}
