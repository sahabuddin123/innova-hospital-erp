<?php
$html = file_get_contents('scratch/lab8_report.html');

$extractVal = function($paramRegex, $default = null) use ($html) {
    // Only match inside a single <td> element without crossing <td> or </td>
    if (preg_match('/<td[^>]*>[^<]*?' . $paramRegex . '[^<]*?<\/td>\s*<td[^>]*>([\s\S]*?)<\/td>/i', $html, $m)) {
        $val = trim(strip_tags($m[1]));
        return $val !== '' ? $val : $default;
    }
    return $default;
};

$tests = [
    'TOTAL WBC COUNT' => 'TOTAL\s+WBC(?:\s+COUNT)?',
    'Neutrophils' => 'Neutrophils?',
    'Lymphocytes' => 'Lymphocytes?',
    'Monocytes' => 'Monocytes?',
    'Eosinophils' => 'Eosinophils?',
    'Basophil' => 'Basophils?',
    'TOTAL PLATELET COUNT' => 'TOTAL\s+PLATELET(?:\s+COUNT)?',
    'MPV' => 'MPV',
    'PDW' => 'PDW',
    'PCT' => 'PCT',
    'RBC COUNT' => 'RBC\s+COUNT',
    'HCT/PCV' => 'HCT\s*\/\s*PCV',
    'MCV' => 'MCV',
    'MCH' => 'MCH(?!\w)',
    'MCHC' => 'MCHC',
    'RDW-SD' => 'RDW\s*-\s*SD',
    'RDW-CV' => 'RDW\s*-\s*CV',
    'P LCR' => 'P\s*LCR'
];

echo "Extraction Test Results (Fixed):\n";
foreach ($tests as $label => $reg) {
    $val = $extractVal($reg, 'NOT FOUND');
    echo sprintf("%-25s: %s\n", $label, $val);
}
