<?php
$im = imagecreatefrompng('scratch/red_box_exact.png');
$w = imagesx($im);
$h = imagesy($im);

for ($y = 0; $y < $h; $y += 2) {
    $line = "";
    for ($x = 0; $x < $w; $x += 2) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        
        // Check if red
        if ($r > 160 && $g < 70 && $b < 70) {
            $line .= "#"; // Red markup
        } elseif ($r < 200 || $g < 200 || $b < 200) {
            // Not white/light background
            $line .= "*";
        } else {
            $line .= " ";
        }
    }
    echo sprintf("%02d: %s\n", $y, substr($line, 0, 110));
}
