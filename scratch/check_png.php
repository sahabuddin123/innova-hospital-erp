<?php
$im = imagecreatefrompng('scratch/test_align_screenshot.png');
$w = imagesx($im);
$h = imagesy($im);

// Find box 1 text lines
for ($y = 70; $y < 150; $y += 3) {
    $minX = $w; $maxX = 0;
    for ($x = 40; $x < 360; $x++) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        // Text is not white (bg is 255, 255, 255)
        if ($r < 200 || $g < 200 || $b < 200) {
            if ($x < $minX) $minX = $x;
            if ($x > $maxX) $maxX = $x;
        }
    }
    if ($maxX > 0) {
        echo sprintf("Y=%03d: X from %03d to %03d (width=%03d)\n", $y, $minX, $maxX, $maxX - $minX);
    }
}
