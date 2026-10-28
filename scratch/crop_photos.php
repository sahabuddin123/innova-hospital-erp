<?php
$poster1 = "scratch/posters/media_1793182543226.png"; // Dr Mahbubur Rahman
$poster2 = "scratch/posters/media_1793182552588.png"; // Dr Rashelul Islam

if (!file_exists("uploads/doctors")) {
    mkdir("uploads/doctors", 0777, true);
}

// 1. Dr Mahbubur Rahman
$im1 = imagecreatefrompng($poster1);
$w1 = imagesx($im1);
$h1 = imagesy($im1);

$crop1_x = (int)($w1 * 0.62);
$crop1_y = (int)($h1 * 0.23);
$crop1_w = (int)($w1 * 0.36);
$crop1_h = (int)($w1 * 0.36);

$thumb1 = imagecreatetruecolor(300, 300);
$white = imagecolorallocate($thumb1, 255, 255, 255);
imagefill($thumb1, 0, 0, $white);
imagecopyresampled($thumb1, $im1, 0, 0, $crop1_x, $crop1_y, 300, 300, $crop1_w, $crop1_h);
imagejpeg($thumb1, "uploads/doctors/dr_mahbubur_rahman.jpg", 95);

// 2. Dr Rashelul Islam
$im2 = imagecreatefrompng($poster2);
$w2 = imagesx($im2);
$h2 = imagesy($im2);

$crop2_x = (int)($w2 * 0.04);
$crop2_y = (int)($h2 * 0.12);
$crop2_w = (int)($w2 * 0.34);
$crop2_h = (int)($w2 * 0.34);

$thumb2 = imagecreatetruecolor(300, 300);
imagefill($thumb2, 0, 0, $white);
imagecopyresampled($thumb2, $im2, 0, 0, $crop2_x, $crop2_y, 300, 300, $crop2_w, $crop2_h);
imagejpeg($thumb2, "uploads/doctors/dr_rashelul_islam.jpg", 95);

echo "Cropped photos successfully generated.\n";
