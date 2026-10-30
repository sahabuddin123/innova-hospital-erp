<?php
/**
 * Script to generate all 88 Standard Laboratory & Radiology Report Templates
 * for Lifecare Diagnostic Center (hospital_id = 98).
 */
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

function tableStyle() {
    return 'style="width:100%; border-collapse:collapse; margin-bottom:15px; font-family:\'Segoe UI\', Roboto, Arial, sans-serif; font-size:13px; color:#333;"';
}

function thStyle($align = 'left') {
    return 'style="background:#f1f4f9; color:#1a3a60; font-weight:700; border:1px solid #d0d7de; padding:8px 10px; text-align:' . $align . ';"';
}

function tdStyle($align = 'left', $bold = false) {
    return 'style="border:1px solid #e1e4e8; padding:7px 10px; text-align:' . $align . ';' . ($bold ? ' font-weight:600;' : '') . '"';
}

function narrativeHeader($title) {
    return '<div style="margin-top:14px; margin-bottom:6px; font-weight:700; color:#1a3a60; text-transform:uppercase; font-size:13px; border-bottom:1px solid #1a3a60; padding-bottom:3px;">' . $title . '</div>';
}

function notesBox($note) {
    return '<div style="margin-top:12px; padding:8px 12px; background:#fafbfc; border-left:3px solid #007bff; font-size:12px; color:#555;"><strong>Note:</strong> ' . $note . '</div>';
}

$templates = [];

// =========================================================================
// Category A: Blood Sugar and Hormone Tests (1 - 9)
// =========================================================================

// 1. 2 Hours After Breakfast Blood Sugar (2 hrs ABF) - ID: 100003
$templates[100003] = [
    'name' => '2 Hours After Breakfast Blood Sugar (2 hrs ABF)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead>
<tr>
<th ' . thStyle('left') . '>Investigation</th>
<th ' . thStyle('center') . '>Result</th>
<th ' . thStyle('center') . '>Unit</th>
<th ' . thStyle('left') . '>Reference Interval</th>
<th ' . thStyle('center') . '>Flag</th>
</tr>
</thead>
<tbody>
<tr>
<td ' . tdStyle('left', true) . '>Blood Glucose (2 hrs ABF)</td>
<td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td>
<td ' . tdStyle('center') . '>mmol/L</td>
<td ' . tdStyle('left') . '>&lt; 7.8 mmol/L (Normal)<br>7.8 - 11.0 mmol/L (Impaired Glucose Tolerance)<br>&ge; 11.1 mmol/L (Diabetes Mellitus)</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
<tr>
<td ' . tdStyle('left') . '>Corresponding Value in mg/dL</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
<td ' . tdStyle('center') . '>mg/dL</td>
<td ' . tdStyle('left') . '>&lt; 140 mg/dL (Normal)<br>140 - 199 mg/dL (IGT)<br>&ge; 200 mg/dL (Diabetes Mellitus)</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
<tr>
<td ' . tdStyle('left') . '>Urine Sugar</td>
<td ' . tdStyle('center') . '>Nil</td>
<td ' . tdStyle('center') . '>-</td>
<td ' . tdStyle('left') . '>Nil</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
</tbody>
</table>
' . notesBox('Specimen: Fluoride Plasma. Method: Glucose Oxidase-Peroxidase (GOD-POD) Spectrophotometric Method.') . '
</div>'
];

// 2. Fasting Blood Sugar (FBS) - ID: 100002
$templates[100002] = [
    'name' => 'Fasting Blood Sugar (FBS)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead>
<tr>
<th ' . thStyle('left') . '>Investigation</th>
<th ' . thStyle('center') . '>Result</th>
<th ' . thStyle('center') . '>Unit</th>
<th ' . thStyle('left') . '>Reference Interval</th>
<th ' . thStyle('center') . '>Flag</th>
</tr>
</thead>
<tbody>
<tr>
<td ' . tdStyle('left', true) . '>Fasting Plasma Glucose (FBS)</td>
<td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td>
<td ' . tdStyle('center') . '>mmol/L</td>
<td ' . tdStyle('left') . '>3.9 - 6.1 mmol/L (Normal)<br>6.1 - 6.9 mmol/L (Impaired Fasting Glucose)<br>&ge; 7.0 mmol/L (Diabetes Mellitus)</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
<tr>
<td ' . tdStyle('left') . '>Corresponding Value in mg/dL</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
<td ' . tdStyle('center') . '>mg/dL</td>
<td ' . tdStyle('left') . '>70 - 110 mg/dL (Normal)<br>110 - 125 mg/dL (IFG)<br>&ge; 126 mg/dL (Diabetes Mellitus)</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
<tr>
<td ' . tdStyle('left') . '>Urine Sugar</td>
<td ' . tdStyle('center') . '>Nil</td>
<td ' . tdStyle('center') . '>-</td>
<td ' . tdStyle('left') . '>Nil</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
</tbody>
</table>
' . notesBox('Specimen: Fluoride Plasma collected after 8-10 hours overnight fasting. Method: GOD-POD Enzymatic.') . '
</div>'
];

// 3. Random Blood Sugar (RBS) - ID: 100001
$templates[100001] = [
    'name' => 'Random Blood Sugar (RBS)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead>
<tr>
<th ' . thStyle('left') . '>Investigation</th>
<th ' . thStyle('center') . '>Result</th>
<th ' . thStyle('center') . '>Unit</th>
<th ' . thStyle('left') . '>Reference Interval</th>
<th ' . thStyle('center') . '>Flag</th>
</tr>
</thead>
<tbody>
<tr>
<td ' . tdStyle('left', true) . '>Random Blood Sugar (RBS)</td>
<td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td>
<td ' . tdStyle('center') . '>mmol/L</td>
<td ' . tdStyle('left') . '>4.4 - 7.8 mmol/L (Normal)<br>&ge; 11.1 mmol/L (Suggestive of Diabetes Mellitus)</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
<tr>
<td ' . tdStyle('left') . '>Corresponding Value in mg/dL</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
<td ' . tdStyle('center') . '>mg/dL</td>
<td ' . tdStyle('left') . '>80 - 140 mg/dL (Normal)<br>&ge; 200 mg/dL (Suggestive of Diabetes Mellitus)</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
<tr>
<td ' . tdStyle('left') . '>Urine Sugar</td>
<td ' . tdStyle('center') . '>Nil</td>
<td ' . tdStyle('center') . '>-</td>
<td ' . tdStyle('left') . '>Nil</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
</tbody>
</table>
' . notesBox('Specimen: Plasma. Diagnosis of diabetes requires confirmation with Fasting Plasma Glucose or HbA1c.') . '
</div>'
];

// 4. Thyroxine (T4) - ID: 100000
$templates[100000] = [
    'name' => 'Thyroxine (T4)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead>
<tr>
<th ' . thStyle('left') . '>Test Parameter</th>
<th ' . thStyle('center') . '>Result</th>
<th ' . thStyle('center') . '>Unit</th>
<th ' . thStyle('left') . '>Reference Interval</th>
<th ' . thStyle('center') . '>Flag</th>
</tr>
</thead>
<tbody>
<tr>
<td ' . tdStyle('left', true) . '>Total Thyroxine (T4)</td>
<td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td>
<td ' . tdStyle('center') . '>ug/dL</td>
<td ' . tdStyle('left') . '>Adults: 4.5 - 12.0 ug/dL<br>Children (1-5 yrs): 7.3 - 15.0 ug/dL<br>Pregnancy: 1.5 times non-pregnant range</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
</tbody>
</table>
' . notesBox('Method: Chemiluminescence Immunoassay (CLIA). Specimen: Serum.') . '
</div>'
];

// 5. Triiodothyronine (T3) - ID: 99999
$templates[99999] = [
    'name' => 'Triiodothyronine (T3)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead>
<tr>
<th ' . thStyle('left') . '>Test Parameter</th>
<th ' . thStyle('center') . '>Result</th>
<th ' . thStyle('center') . '>Unit</th>
<th ' . thStyle('left') . '>Reference Interval</th>
<th ' . thStyle('center') . '>Flag</th>
</tr>
</thead>
<tbody>
<tr>
<td ' . tdStyle('left', true) . '>Total Triiodothyronine (T3)</td>
<td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td>
<td ' . tdStyle('center') . '>ng/mL</td>
<td ' . tdStyle('left') . '>Adults: 0.80 - 2.00 ng/mL<br>Children: 1.00 - 2.60 ng/mL</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
</tbody>
</table>
' . notesBox('Method: Chemiluminescence Immunoassay (CLIA). Specimen: Serum.') . '
</div>'
];

// 6. Serum Thyroid-Stimulating Hormone (TSH) - ID: 99998
$templates[99998] = [
    'name' => 'Serum Thyroid-Stimulating Hormone (TSH)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead>
<tr>
<th ' . thStyle('left') . '>Test Parameter</th>
<th ' . thStyle('center') . '>Result</th>
<th ' . thStyle('center') . '>Unit</th>
<th ' . thStyle('left') . '>Reference Interval</th>
<th ' . thStyle('center') . '>Flag</th>
</tr>
</thead>
<tbody>
<tr>
<td ' . tdStyle('left', true) . '>Serum TSH (3rd Generation)</td>
<td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td>
<td ' . tdStyle('center') . '>uIU/mL</td>
<td ' . tdStyle('left') . '>Adults (non-pregnant): 0.27 - 4.20 uIU/mL<br>Pregnancy (1st trimester): 0.10 - 2.50 uIU/mL<br>Pregnancy (2nd trimester): 0.20 - 3.00 uIU/mL<br>Pregnancy (3rd trimester): 0.30 - 3.00 uIU/mL</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
</tbody>
</table>
' . notesBox('Method: Electrochemiluminescence Immunoassay (ECLIA). Ultra-sensitive 3rd Generation TSH assay.') . '
</div>'
];

// 7. Luteinizing Hormone (LH) - ID: 99997
$templates[99997] = [
    'name' => 'Luteinizing Hormone (LH)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead>
<tr>
<th ' . thStyle('left') . '>Test Parameter</th>
<th ' . thStyle('center') . '>Result</th>
<th ' . thStyle('center') . '>Unit</th>
<th ' . thStyle('left') . '>Reference Interval</th>
<th ' . thStyle('center') . '>Flag</th>
</tr>
</thead>
<tbody>
<tr>
<td ' . tdStyle('left', true) . '>Serum Luteinizing Hormone (LH)</td>
<td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td>
<td ' . tdStyle('center') . '>mIU/mL</td>
<td ' . tdStyle('left') . '>Adult Males: 1.7 - 8.6 mIU/mL<br>Adult Females:<br>&bull; Follicular Phase: 2.4 - 12.6 mIU/mL<br>&bull; Mid-cycle Peak: 14.0 - 95.6 mIU/mL<br>&bull; Luteal Phase: 1.0 - 11.4 mIU/mL<br>&bull; Post-menopausal: 7.7 - 58.5 mIU/mL</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
</tbody>
</table>
' . notesBox('Method: Chemiluminescence Immunoassay (CLIA). Specimen: Serum.') . '
</div>'
];

// 8. Follicle-Stimulating Hormone (FSH) - ID: 99996
$templates[99996] = [
    'name' => 'Follicle-Stimulating Hormone (FSH)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead>
<tr>
<th ' . thStyle('left') . '>Test Parameter</th>
<th ' . thStyle('center') . '>Result</th>
<th ' . thStyle('center') . '>Unit</th>
<th ' . thStyle('left') . '>Reference Interval</th>
<th ' . thStyle('center') . '>Flag</th>
</tr>
</thead>
<tbody>
<tr>
<td ' . tdStyle('left', true) . '>Serum FSH</td>
<td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td>
<td ' . tdStyle('center') . '>mIU/mL</td>
<td ' . tdStyle('left') . '>Adult Males: 1.5 - 12.4 mIU/mL<br>Adult Females:<br>&bull; Follicular Phase: 3.5 - 12.5 mIU/mL<br>&bull; Ovulatory Peak: 4.7 - 21.5 mIU/mL<br>&bull; Luteal Phase: 1.7 - 7.7 mIU/mL<br>&bull; Post-menopausal: 25.8 - 134.8 mIU/mL</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
</tbody>
</table>
' . notesBox('Method: Chemiluminescence Immunoassay (CLIA). Specimen: Serum.') . '
</div>'
];

// 9. Free Thyroxine (FT4) - ID: 99995
$templates[99995] = [
    'name' => 'Free Thyroxine (FT4)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead>
<tr>
<th ' . thStyle('left') . '>Test Parameter</th>
<th ' . thStyle('center') . '>Result</th>
<th ' . thStyle('center') . '>Unit</th>
<th ' . thStyle('left') . '>Reference Interval</th>
<th ' . thStyle('center') . '>Flag</th>
</tr>
</thead>
<tbody>
<tr>
<td ' . tdStyle('left', true) . '>Free Thyroxine (FT4)</td>
<td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td>
<td ' . tdStyle('center') . '>ng/dL</td>
<td ' . tdStyle('left') . '>Adults: 0.93 - 1.70 ng/dL (12.0 - 22.0 pmol/L)<br>Pregnancy (1st trimester): 0.94 - 1.52 ng/dL<br>Pregnancy (2nd trimester): 0.75 - 1.32 ng/dL<br>Pregnancy (3rd trimester): 0.65 - 1.21 ng/dL</td>
<td ' . tdStyle('center') . '>&nbsp;</td>
</tr>
</tbody>
</table>
' . notesBox('Method: Electrochemiluminescence Immunoassay (ECLIA). Specimen: Serum.') . '
</div>'
];

echo "Category A prepared (" . count($templates) . " templates).\n";
file_put_contents('scratch/templates_cat_a.json', json_encode($templates, JSON_PRETTY_PRINT));
