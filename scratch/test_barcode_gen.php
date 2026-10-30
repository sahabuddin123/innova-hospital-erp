<?php
define('BASEPATH', true);
class MX_Controller {}
require_once __DIR__ . '/../application/modules/lab/controllers/Barcode.php';

ob_start();
barcode('', '0000000002', 30, 'horizontal', 'code128', false, 1);
$img_data = ob_get_clean();

echo "Barcode generated successfully, size: " . strlen($img_data) . " bytes\n";
