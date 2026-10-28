<?php
$files = glob("scratch/posters/*.png");
foreach ($files as $f) {
    $info = getimagesize($f);
    echo basename($f) . ": " . ($info ? "{$info[0]}x{$info[1]}" : "unknown") . " (" . filesize($f) . " bytes)\n";
}
