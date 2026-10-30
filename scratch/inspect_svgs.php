<?php
require_once 'scratch/test_curve_generator.php';

$svg_wbc = base64_decode(substr(CbcCurveGenerator::generateWbcCurve(10850, 52, 41, 4, 3, 0), 26));
$svg_plt = base64_decode(substr(CbcCurveGenerator::generatePltCurve(314000, 7.3, 13.7), 26));
$svg_rbc = base64_decode(substr(CbcCurveGenerator::generateRbcCurve(6.19, 72.9, 15.2), 26));

file_put_contents('scratch/test_wbc.svg', $svg_wbc);
file_put_contents('scratch/test_plt.svg', $svg_plt);
file_put_contents('scratch/test_rbc.svg', $svg_rbc);

echo "WBC SVG:\n" . $svg_wbc . "\n\n";
echo "PLT SVG:\n" . $svg_plt . "\n\n";
echo "RBC SVG:\n" . $svg_rbc . "\n\n";
