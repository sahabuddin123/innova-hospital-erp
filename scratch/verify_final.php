<?php
// Mock session and test Lab_model::renderDynamicCbcReport
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$res = $conn->query("SELECT report FROM lab WHERE id = 8");
$lab = $res->fetch_assoc();

define('BASEPATH', true);
class CI_model {}
require_once 'application/modules/lab/models/Lab_model.php';
// Check if class exists or mock
echo "Report contains WBC CURVE: " . (strpos($lab['report'], 'WBC CURVE') !== false ? 'YES' : 'NO') . "\n";
echo "Report contains PLT CURVE: " . (strpos($lab['report'], 'PLT CURVE') !== false ? 'YES' : 'NO') . "\n";
echo "Report contains RBC CURVE: " . (strpos($lab['report'], 'RBC CURVE') !== false ? 'YES' : 'NO') . "\n";

// Check newLabReport.php has watermark centered:
$view = file_get_contents('application/modules/lab/views/newLabReport.php');
if (strpos($view, 'top: 50%') !== false && strpos($view, 'left: 50%') !== false && strpos($view, 'transform: translate(-50%, -50%)') !== false) {
    echo "Watermark centering: VERIFIED OK\n";
} else {
    echo "Watermark centering: FAILED\n";
}

// Check print A4 styling:
if (strpos($view, 'size: A4 portrait') !== false && strpos($view, 'margin: 0') !== false && strpos($view, 'height: 296mm') !== false) {
    echo "Print A4 layout: VERIFIED OK\n";
} else {
    echo "Print A4 layout: FAILED\n";
}
