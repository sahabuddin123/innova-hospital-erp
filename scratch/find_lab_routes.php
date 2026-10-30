<?php
$files = array_merge(
    glob('application/modules/lab/views/*.php'),
    glob('application/modules/lab/controllers/*.php'),
    glob('application/modules/finance/views/*.php')
);

foreach ($files as $f) {
    $c = file_get_contents($f);
    if (stripos($c, 'addLab') !== false) {
        echo "Found in $f\n";
    }
}
