<?php
require_once 'scratch/test_curve_generator.php';

// Generate curves for normal patient
$wbc_normal = CbcCurveGenerator::generateWbcCurve(7500, 55, 35, 6, 3, 1);
$plt_normal = CbcCurveGenerator::generatePltCurve(280000, 8.5, 13.5);
$rbc_normal = CbcCurveGenerator::generateRbcCurve(5.1, 85.0, 13.0);

// Generate curves for Lab 8 patient (Microcytic, low MCV 72.9, WBC 10850)
$wbc_lab8 = CbcCurveGenerator::generateWbcCurve(10850, 52, 41, 4, 3, 0);
$plt_lab8 = CbcCurveGenerator::generatePltCurve(314000, 7.3, 13.7);
$rbc_lab8 = CbcCurveGenerator::generateRbcCurve(6.19, 72.9, 15.2);

// Generate curves for Macrocytic / high MCV patient (MCV 102, low Platelet 80000, high WBC 18000)
$wbc_abnormal = CbcCurveGenerator::generateWbcCurve(18000, 82, 12, 4, 2, 0);
$plt_abnormal = CbcCurveGenerator::generatePltCurve(80000, 11.2, 17.5);
$rbc_abnormal = CbcCurveGenerator::generateRbcCurve(3.2, 102.0, 18.0);

$html = '<!DOCTYPE html><html><head><title>Curve Comparison</title><style>body{font-family:sans-serif;padding:20px;background:#f0f2f5;} .row{display:flex;gap:20px;margin-bottom:20px;} .card{background:#fff;padding:15px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);text-align:center;} h3{margin-top:0;}</style></head><body>';

$html .= '<h2>Dynamic Hematology Curve Verification</h2>';

$html .= '<div class="row">';
$html .= '<div class="card"><h3>Lab 8 (Patient actual: MCV 72.9, WBC 10850)</h3><div style="display:flex;gap:10px;"><img src="'.$wbc_lab8.'"><img src="'.$plt_lab8.'"><img src="'.$rbc_lab8.'"></div></div>';
$html .= '</div>';

$html .= '<div class="row">';
$html .= '<div class="card"><h3>Normal Reference (MCV 85, WBC 7500, PLT 280k)</h3><div style="display:flex;gap:10px;"><img src="'.$wbc_normal.'"><img src="'.$plt_normal.'"><img src="'.$rbc_normal.'"></div></div>';
$html .= '</div>';

$html .= '<div class="row">';
$html .= '<div class="card"><h3>Macrocytic / Leukocytosis / Thrombocytopenia (MCV 102, WBC 18k, PLT 80k)</h3><div style="display:flex;gap:10px;"><img src="'.$wbc_abnormal.'"><img src="'.$plt_abnormal.'"><img src="'.$rbc_abnormal.'"></div></div>';
$html .= '</div>';

$html .= '</body></html>';
file_put_contents('scratch/verify_curves.html', $html);
echo "Wrote scratch/verify_curves.html\n";
