<?php
/**
 * Comprehensive Laboratory & Radiology Report Template Builder for Lifecare Diagnostic Center
 * Generates all 88 test-specific clinical templates into `template` table for hospital_id = 98.
 */
ini_set('memory_limit', '256M');
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

$templates[100003] = [
    'name' => '2 Hours After Breakfast Blood Sugar (2 hrs ABF)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Investigation</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Blood Glucose (2 hrs ABF)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mmol/L</td><td ' . tdStyle('left') . '>&lt; 7.8 mmol/L (Normal)<br>7.8 - 11.0 mmol/L (Impaired Glucose Tolerance)<br>&ge; 11.1 mmol/L (Diabetes Mellitus)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Corresponding Value in mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>&lt; 140 mg/dL (Normal)<br>140 - 199 mg/dL (IGT)<br>&ge; 200 mg/dL (Diabetes Mellitus)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Urine Sugar</td><td ' . tdStyle('center') . '>Nil</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('left') . '>Nil</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Specimen: Fluoride Plasma collected 2 hours after completion of standard meal. Method: GOD-POD Enzymatic.') . '
</div>'
];

$templates[100002] = [
    'name' => 'Fasting Blood Sugar (FBS)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Investigation</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Fasting Plasma Glucose (FBS)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mmol/L</td><td ' . tdStyle('left') . '>3.9 - 6.1 mmol/L (Normal)<br>6.1 - 6.9 mmol/L (Impaired Fasting Glucose)<br>&ge; 7.0 mmol/L (Diabetes Mellitus)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Corresponding Value in mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>70 - 110 mg/dL (Normal)<br>110 - 125 mg/dL (IFG)<br>&ge; 126 mg/dL (Diabetes Mellitus)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Urine Sugar</td><td ' . tdStyle('center') . '>Nil</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('left') . '>Nil</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Specimen: Fluoride Plasma collected after 8-10 hours overnight fasting. Method: GOD-POD Enzymatic.') . '
</div>'
];

$templates[100001] = [
    'name' => 'Random Blood Sugar (RBS)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Investigation</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Random Blood Sugar (RBS)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mmol/L</td><td ' . tdStyle('left') . '>4.4 - 7.8 mmol/L (Normal)<br>&ge; 11.1 mmol/L (Suggestive of Diabetes Mellitus)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Corresponding Value in mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>80 - 140 mg/dL (Normal)<br>&ge; 200 mg/dL (Suggestive of Diabetes Mellitus)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Urine Sugar</td><td ' . tdStyle('center') . '>Nil</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('left') . '>Nil</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Specimen: Plasma. Diagnosis of diabetes requires confirmation with Fasting Plasma Glucose or HbA1c.') . '
</div>'
];

$templates[100000] = [
    'name' => 'Thyroxine (T4)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Total Thyroxine (T4)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>ug/dL</td><td ' . tdStyle('left') . '>Adults: 4.5 - 12.0 ug/dL<br>Children (1-5 yrs): 7.3 - 15.0 ug/dL<br>Pregnancy: 1.5 times non-pregnant range</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Chemiluminescence Immunoassay (CLIA). Specimen: Serum.') . '
</div>'
];

$templates[99999] = [
    'name' => 'Triiodothyronine (T3)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Total Triiodothyronine (T3)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>ng/mL</td><td ' . tdStyle('left') . '>Adults: 0.80 - 2.00 ng/mL<br>Children: 1.00 - 2.60 ng/mL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Chemiluminescence Immunoassay (CLIA). Specimen: Serum.') . '
</div>'
];

$templates[99998] = [
    'name' => 'Serum Thyroid-Stimulating Hormone (TSH)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum TSH (3rd Generation)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>uIU/mL</td><td ' . tdStyle('left') . '>Adults (non-pregnant): 0.27 - 4.20 uIU/mL<br>Pregnancy (1st trimester): 0.10 - 2.50 uIU/mL<br>Pregnancy (2nd trimester): 0.20 - 3.00 uIU/mL<br>Pregnancy (3rd trimester): 0.30 - 3.00 uIU/mL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Electrochemiluminescence Immunoassay (ECLIA). Ultra-sensitive 3rd Generation TSH assay.') . '
</div>'
];

$templates[99997] = [
    'name' => 'Luteinizing Hormone (LH)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Luteinizing Hormone (LH)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mIU/mL</td><td ' . tdStyle('left') . '>Adult Males: 1.7 - 8.6 mIU/mL<br>Adult Females:<br>&bull; Follicular Phase: 2.4 - 12.6 mIU/mL<br>&bull; Mid-cycle Peak: 14.0 - 95.6 mIU/mL<br>&bull; Luteal Phase: 1.0 - 11.4 mIU/mL<br>&bull; Post-menopausal: 7.7 - 58.5 mIU/mL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Chemiluminescence Immunoassay (CLIA). Specimen: Serum.') . '
</div>'
];

$templates[99996] = [
    'name' => 'Follicle-Stimulating Hormone (FSH)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum FSH</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mIU/mL</td><td ' . tdStyle('left') . '>Adult Males: 1.5 - 12.4 mIU/mL<br>Adult Females:<br>&bull; Follicular Phase: 3.5 - 12.5 mIU/mL<br>&bull; Ovulatory Peak: 4.7 - 21.5 mIU/mL<br>&bull; Luteal Phase: 1.7 - 7.7 mIU/mL<br>&bull; Post-menopausal: 25.8 - 134.8 mIU/mL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Chemiluminescence Immunoassay (CLIA). Specimen: Serum.') . '
</div>'
];

$templates[99995] = [
    'name' => 'Free Thyroxine (FT4)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Free Thyroxine (FT4)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>ng/dL</td><td ' . tdStyle('left') . '>Adults: 0.93 - 1.70 ng/dL (12.0 - 22.0 pmol/L)<br>Pregnancy (1st trimester): 0.94 - 1.52 ng/dL<br>Pregnancy (2nd trimester): 0.75 - 1.32 ng/dL<br>Pregnancy (3rd trimester): 0.65 - 1.21 ng/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Electrochemiluminescence Immunoassay (ECLIA). Specimen: Serum.') . '
</div>'
];

echo "Category A loaded (9 templates).\n";

// =========================================================================
// Category B: Hematology, Biochemistry, Serology (10 - 65)
// =========================================================================

// 10. ESR - ID: 99994
$templates[99994] = [
    'name' => 'ESR',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Erythrocyte Sedimentation Rate (ESR)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mm / 1st hr</td><td ' . tdStyle('left') . '>Male: 0 - 10 mm in 1st hour<br>Female: 0 - 20 mm in 1st hour<br>Elderly (&gt; 50 yrs): Male: 0 - 15, Female: 0 - 25 mm</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Westergren Method. Specimen: Sodium Citrate / EDTA Whole Blood.') . '
</div>'
];

// 11. Febrile Antigen - ID: 179
$templates[179] = [
    'name' => 'Febrile Antigen',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Antigen</th><th ' . thStyle('center') . '>Titre</th><th ' . thStyle('left') . '>Reference Interval / Cutoff</th><th ' . thStyle('center') . '>Interpretation</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Salmonella typhi \'O\'</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('left') . '>Titre &lt; 1:80 (Normal)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Salmonella typhi \'H\'</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('left') . '>Titre &lt; 1:80 (Normal)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Salmonella paratyphi \'AH\'</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('left') . '>Titre &lt; 1:80 (Normal)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Salmonella paratyphi \'BH\'</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('left') . '>Titre &lt; 1:80 (Normal)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Brucella abortus</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('left') . '>Titre &lt; 1:80 (Negative)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Proteus OX19</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('left') . '>Titre &lt; 1:80 (Negative)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Slide & Tube Agglutination Test. Significant titre is &ge; 1:160 in endemic areas.') . '
</div>'
];

// 12. Hb Electrophoresis - ID: 178
$templates[178] = [
    'name' => 'Hb Electrophoresis',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Hemoglobin Variant</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Hemoglobin A (Hb A)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>95.0 - 98.0 %</td></tr>
<tr><td ' . tdStyle('left', true) . '>Hemoglobin A2 (Hb A2)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>1.5 - 3.5 % (&gt; 3.5% indicates Beta Thalassemia Trait)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Hemoglobin F (Hb F)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>&lt; 1.0 % (Adults)</td></tr>
<tr><td ' . tdStyle('left') . '>Hemoglobin S (Hb S)</td><td ' . tdStyle('center') . '>Nil</td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>Nil</td></tr>
<tr><td ' . tdStyle('left') . '>Hemoglobin E (Hb E)</td><td ' . tdStyle('center') . '>Nil</td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>Nil</td></tr>
</tbody>
</table>
' . narrativeHeader('Opinion / Impression') . '
<div style="padding:8px; border:1px solid #d0d7de; background:#fff; min-height:50px;">
Normal adult hemoglobin electrophoresis pattern. No abnormal hemoglobin variant detected.
</div>
' . notesBox('Method: Automated Capillary Electrophoresis / High-Performance Liquid Chromatography (HPLC).') . '
</div>'
];

// 13. S. Lipase - ID: 177
$templates[177] = [
    'name' => 'S. Lipase',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Lipase</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>U/L</td><td ' . tdStyle('left') . '>13 - 60 U/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Enzymatic Colorimetric. Specimen: Serum. Elevated values (&gt; 3x upper limit) are highly sensitive and specific for acute pancreatitis.') . '
</div>'
];

// 14. ANA - ID: 176
$templates[176] = [
    'name' => 'ANA',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Investigation</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Titre</th><th ' . thStyle('left') . '>Pattern / Remarks</th><th ' . thStyle('left') . '>Reference Value</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Antinuclear Antibodies (ANA)</td><td ' . tdStyle('center', true) . '><strong>NEGATIVE</strong></td><td ' . tdStyle('center') . '>&lt; 1:40</td><td ' . tdStyle('left') . '>No fluorescent pattern seen</td><td ' . tdStyle('left') . '>Negative: &lt; 1:40<br>Borderline: 1:40 - 1:80<br>Positive: &ge; 1:80</td></tr>
</tbody>
</table>
' . narrativeHeader('Clinical Interpretation') . '
<div style="padding:8px; border:1px solid #d0d7de; background:#fff; min-height:45px;">
Negative for Antinuclear Antibodies. Does not support a diagnosis of Systemic Lupus Erythematosus (SLE) or related systemic autoimmune diseases.
</div>
' . notesBox('Method: Indirect Immunofluorescence Assay (IFA) on HEp-2 cells / ELISA. Specimen: Serum.') . '
</div>'
];

// 15. Blood for C/S - ID: 175
$templates[175] = [
    'name' => 'Blood for C/S',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Examination</th><th ' . thStyle('left') . '>Observation / Findings</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Specimen</td><td ' . tdStyle('left') . '>Venous Blood in BHI Broth Bottle</td></tr>
<tr><td ' . tdStyle('left', true) . '>Incubation Period</td><td ' . tdStyle('left') . '>Incubated at 37&deg;C for 24, 48, 72 hours and up to 7 days</td></tr>
<tr><td ' . tdStyle('left', true) . '>Culture Result</td><td ' . tdStyle('left', true) . '>No bacterial growth observed after 72 hours of aerobic incubation at 37&deg;C.</td></tr>
<tr><td ' . tdStyle('left', true) . '>Organism Isolated</td><td ' . tdStyle('left') . '>None</td></tr>
</tbody>
</table>
' . narrativeHeader('Antibiotic Susceptibility Test (AST)') . '
<p style="font-size:12px; color:#666;"><em>(Antibiotic sensitivity table will be reported if pathogenic organism is isolated)</em></p>
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Antibiotic Name</th><th ' . thStyle('center') . '>Zone Diameter (mm)</th><th ' . thStyle('center') . '>Susceptibility</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left') . ' colspan="3" style="text-align:center; color:#888; font-style:italic;">No organism isolated; AST not applicable.</td></tr>
</tbody>
</table>
' . notesBox('Method: Automated Blood Culture System / Conventional Subculture on Blood Agar & MacConkey Agar. Final report issued after 7 days if negative.') . '
</div>'
];

// 16. Iron Profile - ID: 174
$templates[174] = [
    'name' => 'Iron Profile',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Iron</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>ug/dL</td><td ' . tdStyle('left') . '>Male: 65 - 175 ug/dL<br>Female: 50 - 170 ug/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Total Iron Binding Capacity (TIBC)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>ug/dL</td><td ' . tdStyle('left') . '>250 - 450 ug/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Transferrin Saturation</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>20 - 50 %</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Serum Ferritin</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>ng/mL</td><td ' . tdStyle('left') . '>Adult Male: 30 - 400 ng/mL<br>Adult Female: 15 - 150 ng/mL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Specimen: Fasting Morning Serum (Iron has marked diurnal variation). Method: Ferrozine / CLIA.') . '
</div>'
];

// 17. Vitamin D-Level - ID: 173
$templates[173] = [
    'name' => 'Vitamin D-Level',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interpretation</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>25-Hydroxy Vitamin D [25(OH)D]</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>ng/mL</td><td ' . tdStyle('left') . '>&lt; 20 ng/mL: Deficiency<br>20 - 29 ng/mL: Insufficiency<br>30 - 100 ng/mL: Sufficiency / Optimal<br>&gt; 100 ng/mL: Possible Toxicity</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Electrochemiluminescence Immunoassay (ECLIA). Specimen: Serum. Reference source: Endocrine Society Clinical Guidelines.') . '
</div>'
];

// 18. Semen Analysis - ID: 172
$templates[172] = [
    'name' => 'Semen Analysis',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('1. Physical & Macroscopic Examination') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>WHO 6th Edition (2021) Reference</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Period of Abstinence</td><td ' . tdStyle('center') . '>3 Days</td><td ' . tdStyle('left') . '>2 - 7 Days</td></tr>
<tr><td ' . tdStyle('left') . '>Time of Collection & Examination</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('left') . '>Examined within 60 mins of collection</td></tr>
<tr><td ' . tdStyle('left') . '>Color / Appearance</td><td ' . tdStyle('center') . '>Greyish White / Opalescent</td><td ' . tdStyle('left') . '>Greyish White / Opalescent</td></tr>
<tr><td ' . tdStyle('left') . '>Liquefaction Time</td><td ' . tdStyle('center') . '>25 mins</td><td ' . tdStyle('left') . '>Complete within 60 mins at 37&deg;C</td></tr>
<tr><td ' . tdStyle('left') . '>Volume</td><td ' . tdStyle('center') . '>&nbsp; mL</td><td ' . tdStyle('left') . '>&ge; 1.4 mL</td></tr>
<tr><td ' . tdStyle('left') . '>Viscosity</td><td ' . tdStyle('center') . '>Normal</td><td ' . tdStyle('left') . '>Normal (&lt; 2 cm thread)</td></tr>
<tr><td ' . tdStyle('left') . '>Reaction (pH)</td><td ' . tdStyle('center') . '>7.8</td><td ' . tdStyle('left') . '>&ge; 7.2</td></tr>
</tbody>
</table>

' . narrativeHeader('2. Microscopic & Quantitative Examination') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>WHO Reference Limits</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Sperm Concentration</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong> Mill/mL</td><td ' . tdStyle('left') . '>&ge; 16.0 Million/mL</td></tr>
<tr><td ' . tdStyle('left', true) . '>Total Sperm Count</td><td ' . tdStyle('center') . '>&nbsp; Mill/Ejaculate</td><td ' . tdStyle('left') . '>&ge; 39.0 Million/ejaculate</td></tr>
<tr><td ' . tdStyle('left') . '>Total Motility (PR + NP)</td><td ' . tdStyle('center') . '>&nbsp; %</td><td ' . tdStyle('left') . '>&ge; 42 %</td></tr>
<tr><td ' . tdStyle('left') . '>&bull; Progressive Motility (PR)</td><td ' . tdStyle('center') . '>&nbsp; %</td><td ' . tdStyle('left') . '>&ge; 30 %</td></tr>
<tr><td ' . tdStyle('left') . '>&bull; Non-Progressive Motility (NP)</td><td ' . tdStyle('center') . '>&nbsp; %</td><td ' . tdStyle('left') . '>-</td></tr>
<tr><td ' . tdStyle('left') . '>&bull; Immotile Spermatozoa</td><td ' . tdStyle('center') . '>&nbsp; %</td><td ' . tdStyle('left') . '>-</td></tr>
<tr><td ' . tdStyle('left') . '>Sperm Vitality (Viability)</td><td ' . tdStyle('center') . '>&nbsp; %</td><td ' . tdStyle('left') . '>&ge; 54 % (Eosin-Nigrosin)</td></tr>
<tr><td ' . tdStyle('left') . '>Normal Sperm Morphology</td><td ' . tdStyle('center') . '>&nbsp; %</td><td ' . tdStyle('left') . '>&ge; 4.0 % (Kruger strict criteria)</td></tr>
<tr><td ' . tdStyle('left') . '>Pus Cells (Leukocytes)</td><td ' . tdStyle('center') . '>&nbsp; /HPF</td><td ' . tdStyle('left') . '&lt; 1.0 Million/mL (&lt; 5/HPF)</td></tr>
<tr><td ' . tdStyle('left') . '>RBCs</td><td ' . tdStyle('center') . '>Nil /HPF</td><td ' . tdStyle('left') . '>Nil</td></tr>
<tr><td ' . tdStyle('left') . '>Epithelial Cells</td><td ' . tdStyle('center') . '>Few /HPF</td><td ' . tdStyle('left') . '>Few</td></tr>
<tr><td ' . tdStyle('left') . '>Sperm Agglutination</td><td ' . tdStyle('center') . '>Nil</td><td ' . tdStyle('left') . '>Nil</td></tr>
</tbody>
</table>
' . narrativeHeader('Impression / Opinion') . '
<div style="padding:8px; border:1px solid #d0d7de; background:#fff; min-height:45px;">
Normozoospermia (Normal semen parameters according to WHO 6th edition criteria).
</div>
</div>'
];

// 19. HbA1c - ID: 171
$templates[171] = [
    'name' => 'HbA1c',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Glycated Hemoglobin (HbA1c)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>&lt; 5.7 % : Non-Diabetic<br>5.7 - 6.4 % : Pre-diabetes (High risk)<br>&ge; 6.5 % : Diabetes Mellitus</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Estimated Average Glucose (eAG)</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>&lt; 117 mg/dL (Normal)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>eAG in mmol/L</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mmol/L</td><td ' . tdStyle('left') . '>&lt; 6.5 mmol/L (Normal)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: High Performance Liquid Chromatography (HPLC) / Ion Exchange. Certified by NGSP & standardized to IFCC reference method.') . '
</div>'
];

// 20. Pus for C/S - ID: 170
$templates[170] = [
    'name' => 'Pus for C/S',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Examination</th><th ' . thStyle('left') . '>Findings</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Specimen</td><td ' . tdStyle('left') . '>Pus / Wound Swab</td></tr>
<tr><td ' . tdStyle('left', true) . '>Gram Stain Microscopy</td><td ' . tdStyle('left') . '>Pus cells seen: Moderate; Organism: &nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Culture at 37&deg;C (48 hrs)</td><td ' . tdStyle('left', true) . '>No bacterial growth after 48 hours of aerobic incubation at 37&deg;C.</td></tr>
<tr><td ' . tdStyle('left', true) . '>Organism Isolated</td><td ' . tdStyle('left') . '>None</td></tr>
</tbody>
</table>
' . narrativeHeader('Antibiotic Sensitivity Pattern (Kirby-Bauer Disk Diffusion)') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Antimicrobial Agent</th><th ' . thStyle('center') . '>Zone (mm)</th><th ' . thStyle('center') . '>Susceptibility (S / I / R)</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left') . ' colspan="3" style="text-align:center; color:#888; font-style:italic;">No pathogenic growth isolated. AST not applicable.</td></tr>
</tbody>
</table>
' . notesBox('Guidelines: CLSI M100. Specimen cultured on Blood Agar & MacConkey Agar.') . '
</div>'
];

// 21. Hbs AG Confirmatory - ID: 169
$templates[169] = [
    'name' => 'Hbs AG Confirmatory',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Investigation</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Index Value (S/CO)</th><th ' . thStyle('left') . '>Reference Cutoff Criteria</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>HBsAg Confirmatory Assay</td><td ' . tdStyle('center', true) . '><strong>NON-REACTIVE</strong></td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('left') . '>Non-Reactive: &lt; 0.90 S/CO<br>Borderline: 0.90 - 1.00 S/CO<br>Reactive: &ge; 1.00 S/CO</td></tr>
</tbody>
</table>
' . notesBox('Method: Electrochemiluminescence Immunoassay (ECLIA) / ELISA. Specimen: Serum. Confirmatory neutralization assay performed when indicated.') . '
</div>'
];

// 22. Troponin I - ID: 168
$templates[168] = [
    'name' => 'Troponin I',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Cutoff</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Cardiac Troponin I (cTnI)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>ng/mL</td><td ' . tdStyle('left') . '>&lt; 0.04 ng/mL : Negative / Normal<br>&ge; 0.04 ng/mL : Positive (Suggestive of Myocardial Injury)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Quantitative Fluorescence Immunoassay / CLIA. Clinical correlation with ECG and clinical symptoms is essential for Acute Coronary Syndrome.') . '
</div>'
];

// 23. Testosteron - ID: 167
$templates[167] = [
    'name' => 'Testosteron',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Total Serum Testosterone</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>ng/dL</td><td ' . tdStyle('left') . '>Adult Male (20-49 yrs): 249 - 836 ng/dL<br>Adult Male (&ge; 50 yrs): 193 - 740 ng/dL<br>Adult Female: 8.4 - 48.1 ng/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Chemiluminescence Immunoassay (CLIA). Specimen: Morning Serum (testosterone peaks between 7 AM and 10 AM).') . '
</div>'
];

// 24. Dengue NS-1/IGG/IGM - ID: 166
$templates[166] = [
    'name' => 'Dengue NS-1/IGG/IGM',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Dengue Marker</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Method</th><th ' . thStyle('left') . '>Clinical Significance</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Dengue NS1 Antigen</td><td ' . tdStyle('center', true) . '><strong>NEGATIVE</strong></td><td ' . tdStyle('left') . '>Immunochromatography</td><td ' . tdStyle('left') . '>Detected in acute phase (Day 1 - 7 of fever)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Dengue IgM Antibody</td><td ' . tdStyle('center', true) . '><strong>NEGATIVE</strong></td><td ' . tdStyle('left') . '>Immunochromatography</td><td ' . tdStyle('left') . '>Appears after day 4-5; indicates primary infection</td></tr>
<tr><td ' . tdStyle('left', true) . '>Dengue IgG Antibody</td><td ' . tdStyle('center', true) . '><strong>NEGATIVE</strong></td><td ' . tdStyle('left') . '>Immunochromatography</td><td ' . tdStyle('left') . '>Indicates secondary infection or past exposure</td></tr>
</tbody>
</table>
' . narrativeHeader('Interpretation') . '
<div style="padding:8px; border:1px solid #d0d7de; background:#fff; min-height:45px;">
Negative for Dengue NS1 Antigen and Dengue IgM/IgG Antibodies.
</div>
' . notesBox('A negative result does not rule out Dengue infection if collected very early. Repeat testing or Platelet / Hematocrit monitoring recommended if clinical suspicion persists.') . '
</div>'
];

// 25. S.Electrolyte - ID: 165
$templates[165] = [
    'name' => 'S.Electrolyte',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Analyte</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Sodium (Na+)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mmol/L</td><td ' . tdStyle('left') . '>135 - 145 mmol/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Potassium (K+)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mmol/L</td><td ' . tdStyle('left') . '>3.5 - 5.1 mmol/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Chloride (Cl-)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mmol/L</td><td ' . tdStyle('left') . '>98 - 107 mmol/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Bicarbonate (HCO3-)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mmol/L</td><td ' . tdStyle('left') . '>22 - 29 mmol/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Anion Gap</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mmol/L</td><td ' . tdStyle('left') . '>8 - 16 mmol/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Ion Selective Electrode (ISE) Direct Method. Specimen: Serum (Non-hemolyzed).') . '
</div>'
];

// 26. S.Amylase - ID: 163
$templates[163] = [
    'name' => 'S.Amylase',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Amylase</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>U/L</td><td ' . tdStyle('left') . '>28 - 100 U/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Enzymatic Colorimetric (CNPG3). Specimen: Serum.') . '
</div>'
];

// 27. S.IGE (Level) - ID: 162
$templates[162] = [
    'name' => 'S.IGE (Level)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Total Serum IgE</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>IU/mL</td><td ' . tdStyle('left') . '>Adults: &lt; 100 IU/mL<br>Children (1-5 yrs): &lt; 60 IU/mL<br>Children (6-14 yrs): &lt; 90 IU/mL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Chemiluminescence Immunoassay (CLIA). Specimen: Serum. Elevated in atopic allergy, bronchial asthma, and parasitic infections.') . '
</div>'
];

// 28. Prothombin Time - ID: 161
$templates[161] = [
    'name' => 'Prothombin Time',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Coagulation Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Patient PT</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>Seconds</td><td ' . tdStyle('left') . '>11.0 - 15.0 Seconds</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Control PT</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>Seconds</td><td ' . tdStyle('left') . '>12.0 Seconds</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Prothrombin Ratio (PR)</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('left') . '>0.9 - 1.2</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>INR (Intl. Normalized Ratio)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('left') . '>Normal: 0.8 - 1.2<br>Therapeutic Target for Oral Anticoagulation (Warfarin): 2.0 - 3.0</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Specimen: Citrated Plasma (1:9 ratio). Method: Photo-optical Clot Detection.') . '
</div>'
];

// 29. S.PROLACTIN - ID: 160
$templates[160] = [
    'name' => 'S.PROLACTIN',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Prolactin</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>ng/mL</td><td ' . tdStyle('left') . '>Adult Males: 4.04 - 15.2 ng/mL<br>Non-pregnant Females: 4.79 - 23.3 ng/mL<br>Post-menopausal Females: 3.00 - 18.0 ng/mL<br>Pregnant Females: 34 - 386 ng/mL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Electrochemiluminescence Immunoassay (ECLIA). Specimen: Serum collected in morning at rest.') . '
</div>'
];

// 30. Urine C/S - ID: 136
$templates[136] = [
    'name' => 'Urine C/S',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Examination</th><th ' . thStyle('left') . '>Findings</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Specimen</td><td ' . tdStyle('left') . '>Clean-catch Midstream Urine (MSU)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Gram Stain Microscopy</td><td ' . tdStyle('left') . '>Pus cells: &nbsp; /HPF; Organisms: &nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Culture Result (37&deg;C, 24-48 hrs)</td><td ' . tdStyle('left', true) . '>No significant bacteriuria / bacterial growth after 48 hours of aerobic incubation.</td></tr>
<tr><td ' . tdStyle('left', true) . '>Colony Count</td><td ' . tdStyle('left') . '>&lt; 10,000 CFU/mL (Not significant)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Organism Isolated</td><td ' . tdStyle('left') . '>None</td></tr>
</tbody>
</table>
' . narrativeHeader('Antibiotic Sensitivity Pattern (AST)') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Antibiotic</th><th ' . thStyle('center') . '>Susceptibility</th><th ' . thStyle('left') . '>Antibiotic</th><th ' . thStyle('center') . '>Susceptibility</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left') . ' colspan="4" style="text-align:center; color:#888; font-style:italic;">No significant growth isolated. Sensitivity testing not indicated.</td></tr>
</tbody>
</table>
' . notesBox('Significant bacteriuria is defined as &ge; 10^5 CFU/mL of a single uropathogen according to Kass criteria.') . '
</div>'
];

// 31. Urine P/T - ID: 135
$templates[135] = [
    'name' => 'Urine P/T',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Investigation</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Sensitivity / Cutoff</th><th ' . thStyle('left') . '>Interpretation</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Urine Pregnancy Test (hCG)</td><td ' . tdStyle('center', true) . '><strong>NEGATIVE</strong></td><td ' . tdStyle('left') . '>25 mIU/mL human Chorionic Gonadotropin</td><td ' . tdStyle('left') . '>No pregnancy-specific hCG detected</td></tr>
</tbody>
</table>
' . notesBox('Method: Rapid Immunochromatographic Assay for beta-hCG. Specimen: Early Morning First Voided Urine.') . '
</div>'
];

// 32. Urine R/E - ID: 134
$templates[134] = [
    'name' => 'Urine R/E',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('1. Physical Examination') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Color</td><td ' . tdStyle('center') . '>Straw / Pale Yellow</td><td ' . tdStyle('left', true) . '>Specific Gravity</td><td ' . tdStyle('center') . '>1.015 (1.005 - 1.030)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Appearance</td><td ' . tdStyle('center') . '>Clear</td><td ' . tdStyle('left', true) . '>Sediment</td><td ' . tdStyle('center') . '>Nil</td></tr>
</tbody>
</table>

' . narrativeHeader('2. Chemical Examination') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Reaction (pH)</td><td ' . tdStyle('center') . '>6.0 (Acidic)</td><td ' . tdStyle('left', true) . '>Ketones</td><td ' . tdStyle('center') . '>Negative</td></tr>
<tr><td ' . tdStyle('left', true) . '>Albumin / Protein</td><td ' . tdStyle('center') . '>Nil</td><td ' . tdStyle('left', true) . '>Bilirubin</td><td ' . tdStyle('center') . '>Negative</td></tr>
<tr><td ' . tdStyle('left', true) . '>Sugar / Glucose</td><td ' . tdStyle('center') . '>Nil</td><td ' . tdStyle('left', true) . '>Urobilinogen</td><td ' . tdStyle('center') . '>Normal</td></tr>
<tr><td ' . tdStyle('left', true) . '>Blood / Hemoglobin</td><td ' . tdStyle('center') . '>Negative</td><td ' . tdStyle('left', true) . '>Nitrite</td><td ' . tdStyle('center') . '>Negative</td></tr>
</tbody>
</table>

' . narrativeHeader('3. Microscopic Examination (Centrifuged Deposit)') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Element</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Element</th><th ' . thStyle('center') . '>Result</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Pus Cells</td><td ' . tdStyle('center') . '>1 - 2 /HPF (0 - 5)</td><td ' . tdStyle('left', true) . '>Casts</td><td ' . tdStyle('center') . '>Nil</td></tr>
<tr><td ' . tdStyle('left', true) . '>RBCs</td><td ' . tdStyle('center') . '>Nil /HPF (0 - 2)</td><td ' . tdStyle('left', true) . '>Crystals</td><td ' . tdStyle('center') . '>Nil</td></tr>
<tr><td ' . tdStyle('left', true) . '>Epithelial Cells</td><td ' . tdStyle('center') . '>Few /HPF</td><td ' . tdStyle('left', true) . '>Amorphous Deposits</td><td ' . tdStyle('center') . '>Nil</td></tr>
<tr><td ' . tdStyle('left', true) . '>Yeast Cells</td><td ' . tdStyle('center') . '>Nil</td><td ' . tdStyle('left', true) . '>Bacteria</td><td ' . tdStyle('center') . '>Nil</td></tr>
</tbody>
</table>
</div>'
];

// 33. OBT - ID: 133
$templates[133] = [
    'name' => 'OBT',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Investigation</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Method</th><th ' . thStyle('left') . '>Clinical Significance</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Occult Blood in Stool (OBT)</td><td ' . tdStyle('center', true) . '><strong>NEGATIVE</strong></td><td ' . tdStyle('left') . '>Immunochemical (FOBT)</td><td ' . tdStyle('left') . '>Specific for human hemoglobin; no dietary restriction required</td></tr>
</tbody>
</table>
' . notesBox('Specimen: Fresh fecal sample. A negative test does not completely exclude lower GI lesions as bleeding may be intermittent.') . '
</div>'
];

// 34. Stool C/S - ID: 132
$templates[132] = [
    'name' => 'Stool C/S',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Examination</th><th ' . thStyle('left') . '>Findings</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Specimen</td><td ' . tdStyle('left') . '>Fresh Stool</td></tr>
<tr><td ' . tdStyle('left', true) . '>Direct Microscopy</td><td ' . tdStyle('left') . '>Pus cells: Few; RBCs: Nil; Cysts/Ova: Nil</td></tr>
<tr><td ' . tdStyle('left', true) . '>Culture Result (48 hrs)</td><td ' . tdStyle('left', true) . '>Normal fecal enteric flora isolated. No growth of enteric pathogens (Salmonella, Shigella, Vibrio cholerae).</td></tr>
<tr><td ' . tdStyle('left', true) . '>Pathogen Isolated</td><td ' . tdStyle('left') . '>None</td></tr>
</tbody>
</table>
' . narrativeHeader('Antibiotic Sensitivity Test') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Antimicrobial Agent</th><th ' . thStyle('center') . '>Zone (mm)</th><th ' . thStyle('center') . '>Susceptibility</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left') . ' colspan="3" style="text-align:center; color:#888; font-style:italic;">No enteric pathogen isolated. AST not applicable.</td></tr>
</tbody>
</table>
' . notesBox('Specimen inoculated onto MacConkey, SS Agar & TCBS. Incubated at 37&deg;C.') . '
</div>'
];

// 35. Stool R/E - ID: 131
$templates[131] = [
    'name' => 'Stool R/E',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('1. Physical Examination') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Color</td><td ' . tdStyle('center') . '>Yellowish Brown</td><td ' . tdStyle('left', true) . '>Mucus</td><td ' . tdStyle('center') . '>Absent</td></tr>
<tr><td ' . tdStyle('left', true) . '>Consistency</td><td ' . tdStyle('center') . '>Formed / Semi-formed</td><td ' . tdStyle('left', true) . '>Blood</td><td ' . tdStyle('center') . '>Absent</td></tr>
</tbody>
</table>

' . narrativeHeader('2. Chemical Examination') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Result</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Reaction (pH)</td><td ' . tdStyle('center') . '>Neutral / Slightly Acidic</td><td ' . tdStyle('left', true) . '>Reducing Substances</td><td ' . tdStyle('center') . '>Negative</td></tr>
<tr><td ' . tdStyle('left', true) . '>Occult Blood</td><td ' . tdStyle('center') . '>Negative</td><td ' . tdStyle('left', true) . '>&nbsp;</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>

' . narrativeHeader('3. Microscopic Examination') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Element</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Element</th><th ' . thStyle('center') . '>Result</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Protozoa (Cysts / Trophozoites)</td><td ' . tdStyle('center') . '>Not Seen</td><td ' . tdStyle('left', true) . '>Pus Cells</td><td ' . tdStyle('center') . '>0 - 2 /HPF</td></tr>
<tr><td ' . tdStyle('left', true) . '>Helminths (Ova / Larvae)</td><td ' . tdStyle('center') . '>Not Seen</td><td ' . tdStyle('left', true) . '>Red Blood Cells (RBCs)</td><td ' . tdStyle('center') . '>Nil</td></tr>
<tr><td ' . tdStyle('left', true) . '>E. histolytica / Giardia</td><td ' . tdStyle('center') . '>Not Seen</td><td ' . tdStyle('left', true) . '>Macrophages</td><td ' . tdStyle('center') . '>Nil</td></tr>
<tr><td ' . tdStyle('left', true) . '>Ascaris / Hookworm Ova</td><td ' . tdStyle('center') . '>Not Seen</td><td ' . tdStyle('left', true) . '>Vegetable / Starch Granules</td><td ' . tdStyle('center') . '>Present (Few)</td></tr>
</tbody>
</table>
</div>'
];

// 36. Alkaline phosphatase - ID: 130
$templates[130] = [
    'name' => 'Alkaline phosphatase',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Alkaline Phosphatase (ALP)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>U/L</td><td ' . tdStyle('left') . '>Adults: 44 - 147 U/L<br>Children (&lt; 15 yrs): Up to 350 U/L (Growing bones)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: p-Nitrophenylphosphate (pNPP) Kinetic Method (IFCC). Specimen: Serum.') . '
</div>'
];

// 37. LFT - ID: 129
$templates[129] = [
    'name' => 'LFT',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Liver Function Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Bilirubin (Total)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>0.2 - 1.2 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Serum Bilirubin (Direct / Conjugated)</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>0.0 - 0.3 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Serum Bilirubin (Indirect / Unconjugated)</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>0.2 - 0.8 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>SGPT / ALT</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>U/L</td><td ' . tdStyle('left') . '>Male: &lt; 45 U/L; Female: &lt; 34 U/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>SGOT / AST</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>U/L</td><td ' . tdStyle('left') . '>Male: &lt; 40 U/L; Female: &lt; 32 U/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Alkaline Phosphatase (ALP)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>U/L</td><td ' . tdStyle('left') . '>Adults: 44 - 147 U/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Total Protein</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>g/dL</td><td ' . tdStyle('left') . '>6.4 - 8.3 g/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Serum Albumin</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>g/dL</td><td ' . tdStyle('left') . '>3.5 - 5.2 g/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Serum Globulin</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>g/dL</td><td ' . tdStyle('left') . '>2.3 - 3.5 g/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>A / G Ratio</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('left') . '>1.2 - 2.2</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Specimen: Serum. Method: Fully Automated Clinical Chemistry Analyzer.') . '
</div>'
];

// 38. PBF - ID: 128
$templates[128] = [
    'name' => 'PBF',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('Peripheral Blood Film (PBF) Examination') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . ' style="width:25%;">Series</th><th ' . thStyle('left') . '>Morphological Findings</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>RBC Series (Erythrocytes)</td><td ' . tdStyle('left') . '>Predominantly normocytic and normochromic. Normal central pallor. No marked anisopoikilocytosis, polychromasia, or nucleated RBCs seen.</td></tr>
<tr><td ' . tdStyle('left', true) . '>WBC Series (Leukocytes)</td><td ' . tdStyle('left') . '>Total count appears normal. Mature neutrophils and lymphocytes predominate in normal proportion. No toxic granulation or atypical/blast cells seen.</td></tr>
<tr><td ' . tdStyle('left', true) . '>Platelets (Thrombocytes)</td><td ' . tdStyle('left') . '>Adequate in number on smear. Morphologically normal; normal granules and clumping noted.</td></tr>
<tr><td ' . tdStyle('left', true) . '>Hemoparasites</td><td ' . tdStyle('left') . '>No malarial parasite or microfilaria seen in the examined film.</td></tr>
</tbody>
</table>
' . narrativeHeader('Impression & Clinical Advice') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#fff; min-height:50px;">
<strong>Impression:</strong> Normocytic Normochromic Blood Picture.<br>
<small class="text-muted">Correlate with Complete Blood Count (CBC) and clinical profile.</small>
</div>
' . notesBox('Stain: Leishman / Giemsa Stain. Examined under 100x Oil Immersion lens.') . '
</div>'
];

// 39. Hb% - ID: 127
$templates[127] = [
    'name' => 'Hb%',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Hemoglobin (Hb%)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>g/dL</td><td ' . tdStyle('left') . '>Adult Males: 13.5 - 17.5 g/dL<br>Adult Females: 12.0 - 15.5 g/dL<br>Children: 11.0 - 14.5 g/dL<br>Pregnant Females: &ge; 11.0 g/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Cyanmethemoglobin Spectrophotometric Method / Automated Cell Counter. Specimen: EDTA Blood.') . '
</div>'
];

// 40. S. Albumin - ID: 126
$templates[126] = [
    'name' => 'S. Albumin',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Albumin</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>g/dL</td><td ' . tdStyle('left') . '>3.5 - 5.2 g/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Bromocresol Green (BCG) Dye Binding Method. Specimen: Serum.') . '
</div>'
];

// 41. Sputum for AFB - ID: 125
$templates[125] = [
    'name' => 'Sputum for AFB',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Specimen</th><th ' . thStyle('center') . '>Staining Method</th><th ' . thStyle('left') . '>Microscopic Result</th><th ' . thStyle('center') . '>Grading (WHO/RNTCP)</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Sample 1 (Spot)</td><td ' . tdStyle('center') . '>Ziehl-Neelsen (ZN)</td><td ' . tdStyle('left', true) . '><strong>No Acid-Fast Bacilli Seen</strong></td><td ' . tdStyle('center') . '>Negative</td></tr>
<tr><td ' . tdStyle('left', true) . '>Sample 2 (Early Morning)</td><td ' . tdStyle('center') . '>Ziehl-Neelsen (ZN)</td><td ' . tdStyle('left', true) . '><strong>No Acid-Fast Bacilli Seen</strong></td><td ' . tdStyle('center') . '>Negative</td></tr>
</tbody>
</table>
' . narrativeHeader('Grading Scale (WHO / RNTCP Criteria)') . '
<div style="font-size:12px; color:#555; line-height:1.6; padding:8px; border:1px solid #e1e4e8; background:#fafbfc;">
&bull; <strong>Negative:</strong> Zero AFB per 100 oil immersion fields.<br>
&bull; <strong>Scanty:</strong> 1-9 AFB per 100 fields (record exact number).<br>
&bull; <strong>1+:</strong> 10-99 AFB per 100 fields.<br>
&bull; <strong>2+:</strong> 1-10 AFB per field in at least 50 fields.<br>
&bull; <strong>3+:</strong> &gt; 10 AFB per field in at least 20 fields.
</div>
</div>'
];

// 42. Tuberculin Test (MT) - ID: 124
$templates[124] = [
    'name' => 'Tuberculin Test (MT)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('left') . '>Details</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Antigen Injected</td><td ' . tdStyle('left') . '>Purified Protein Derivative (PPD-RT 23) 2 TU / 5 TU (0.1 mL Intradermally)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Site of Injection</td><td ' . tdStyle('left') . '>Volar surface of Left Forearm</td></tr>
<tr><td ' . tdStyle('left', true) . '>Date & Time of Injection</td><td ' . tdStyle('left') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Date & Time of Reading</td><td ' . tdStyle('left') . '>&nbsp; (Read after 48 - 72 Hours)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Induration Diameter</td><td ' . tdStyle('left', true) . '><strong>&nbsp; mm</strong> (Measured transverse diameter of induration, not erythema)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Interpretation</td><td ' . tdStyle('left', true) . '><strong>NEGATIVE</strong> (&lt; 5 mm)</td></tr>
</tbody>
</table>
' . narrativeHeader('Interpretation Criteria') . '
<div style="font-size:12px; color:#555; padding:8px; border:1px solid #e1e4e8; background:#fafbfc;">
&bull; <strong>&ge; 5 mm:</strong> Positive in HIV-infected persons, recent contacts of active TB cases, or immunosuppressed patients.<br>
&bull; <strong>&ge; 10 mm:</strong> Positive in high-prevalence areas (e.g. Bangladesh), high-risk clinical conditions.<br>
&bull; <strong>&ge; 15 mm:</strong> Positive in persons with no known risk factors for TB.
</div>
</div>'
];

// 43. CRP - ID: 123
$templates[123] = [
    'name' => 'CRP',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>C-Reactive Protein (CRP)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/L</td><td ' . tdStyle('left') . '>&lt; 6.0 mg/L : Normal / Negative<br>&ge; 6.0 mg/L : Positive (Acute phase reactant / Inflammation)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Quantitative Immunoturbidimetric Assay. Specimen: Serum.') . '
</div>'
];

// 44. Blood Grouping - ID: 122
$templates[122] = [
    'name' => 'Blood Grouping',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Examination</th><th ' . thStyle('center') . '>Anti-A</th><th ' . thStyle('center') . '>Anti-B</th><th ' . thStyle('center') . '>Anti-D (Rh)</th><th ' . thStyle('center') . '>Final Blood Group</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Forward & Reverse Grouping</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center', true) . ' style="font-size:16px; color:#c00;"><strong>&nbsp;</strong></td></tr>
</tbody>
</table>
<table ' . tableStyle() . '>
<tbody>
<tr><td ' . tdStyle('left', true) . ' style="width:30%;">ABO Blood Group</td><td ' . tdStyle('left', true) . ' style="font-size:15px; color:#1a3a60;"><strong>&nbsp;</strong></td></tr>
<tr><td ' . tdStyle('left', true) . '>Rh (D) Factor</td><td ' . tdStyle('left', true) . ' style="font-size:15px; color:#1a3a60;"><strong>&nbsp;</strong></td></tr>
</tbody>
</table>
' . notesBox('Method: Slide & Tube Hemagglutination Technique using monoclonal Anti-A, Anti-B, and Anti-D sera.') . '
</div>'
];

// 45. TPHA - ID: 121
$templates[121] = [
    'name' => 'TPHA',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Titre</th><th ' . thStyle('left') . '>Reference Value</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Treponema Pallidum Hemagglutination (TPHA)</td><td ' . tdStyle('center', true) . '><strong>NON-REACTIVE</strong></td><td ' . tdStyle('center') . '>&lt; 1:80</td><td ' . tdStyle('left') . '>Non-Reactive: &lt; 1:80<br>Reactive: &ge; 1:80</td></tr>
</tbody>
</table>
' . notesBox('Method: Indirect Hemagglutination Assay for Treponema pallidum antibodies. Confirmatory treponemal test for Syphilis.') . '
</div>'
];

// 46. VDRL - ID: 120
$templates[120] = [
    'name' => 'VDRL',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Investigation</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Titre (if Reactive)</th><th ' . thStyle('left') . '>Reference Value</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>VDRL / RPR Flocculation Test</td><td ' . tdStyle('center', true) . '><strong>NON-REACTIVE</strong></td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('left') . '>Non-Reactive</td></tr>
</tbody>
</table>
' . notesBox('Method: Non-treponemal Carbon Antigen Flocculation (RPR / VDRL). Specimen: Serum. Biological false positives may occur in autoimmune diseases, leprosy, malaria, or pregnancy.') . '
</div>'
];

// 47. ASO Titre - ID: 119
$templates[119] = [
    'name' => 'ASO Titre',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Anti-Streptolysin O (ASO) Titre</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>IU/mL</td><td ' . tdStyle('left') . '>Adults: &lt; 200 IU/mL<br>Children (&le; 12 yrs): &lt; 300 IU/mL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Quantitative Immunoturbidimetric / Latex Agglutination Method. Elevated titres indicate recent Group A Streptococcus infection (Rheumatic Fever, Glomerulonephritis).') . '
</div>'
];

// 48. Widal Test - ID: 118
$templates[118] = [
    'name' => 'Widal Test',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Salmonella Antigen</th><th ' . thStyle('center') . '>1:20</th><th ' . thStyle('center') . '>1:40</th><th ' . thStyle('center') . '>1:80</th><th ' . thStyle('center') . '>1:160</th><th ' . thStyle('center') . '>1:320</th><th ' . thStyle('center') . '>Final Titre</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Salmonella typhi \'O\' (S. typhi O)</td><td ' . tdStyle('center') . '>+</td><td ' . tdStyle('center') . '>+</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center', true) . '>1:40</td></tr>
<tr><td ' . tdStyle('left', true) . '>Salmonella typhi \'H\' (S. typhi H)</td><td ' . tdStyle('center') . '>+</td><td ' . tdStyle('center') . '>+</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center', true) . '>1:40</td></tr>
<tr><td ' . tdStyle('left', true) . '>Salmonella paratyphi \'AH\'</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center', true) . '>&lt; 1:20</td></tr>
<tr><td ' . tdStyle('left', true) . '>Salmonella paratyphi \'BH\'</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('center', true) . '>&lt; 1:20</td></tr>
</tbody>
</table>
' . narrativeHeader('Clinical Interpretation') . '
<div style="padding:8px; border:1px solid #d0d7de; background:#fff; min-height:45px;">
Titre &lt; 1:80 for S. typhi O and H antigens is not significant. No serological evidence of active Enteric (Typhoid) Fever at present.
</div>
' . notesBox('Method: Tube Agglutination Technique. In endemic regions, a single titre of &ge; 1:160 for O and/or H antigen, or a four-fold rise in paired sera, is clinically significant.') . '
</div>'
];

// 49. R/A Factor - ID: 117
$templates[117] = [
    'name' => 'R/A Factor',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Rheumatoid Factor (RA / RF)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>IU/mL</td><td ' . tdStyle('left') . '>&lt; 14.0 IU/mL : Negative<br>&ge; 14.0 IU/mL : Positive</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Quantitative Immunoturbidimetric Assay. Specimen: Serum.') . '
</div>'
];

// 50. S. Uric Acid - ID: 116
$templates[116] = [
    'name' => 'S. Uric Acid',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Uric Acid</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>Adult Male: 3.5 - 7.2 mg/dL<br>Adult Female: 2.6 - 6.0 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Uricase / Enzymatic Colorimetric Method. Specimen: Serum.') . '
</div>'
];

// 51. S. Creatinine - ID: 115
$templates[115] = [
    'name' => 'S. Creatinine',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Creatinine</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>Adult Male: 0.70 - 1.30 mg/dL (62 - 115 umol/L)<br>Adult Female: 0.50 - 1.10 mg/dL (44 - 97 umol/L)<br>Children: 0.30 - 0.70 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>eGFR (CKD-EPI Formula)</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mL/min/1.73m&sup2;</td><td ' . tdStyle('left') . '>&ge; 90 : Normal kidney function<br>60 - 89 : Mild decrease<br>&lt; 60 : Reduced GFR (suspicious for CKD)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Modified Jaffe Kinetic / Enzymatic IDMS-traceable. Specimen: Serum.') . '
</div>'
];

// 52. Blood Urea - ID: 114
$templates[114] = [
    'name' => 'Blood Urea',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Blood Urea</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>15 - 45 mg/dL (2.5 - 7.5 mmol/L)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Blood Urea Nitrogen (BUN)</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>7 - 20 mg/dL (BUN = Urea &divide; 2.14)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Urease-GLDH Kinetic Method. Specimen: Serum.') . '
</div>'
];

// 53. Lipid Profile - ID: 113
$templates[113] = [
    'name' => 'Lipid Profile',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Lipid Fraction</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>NCEP ATP III Clinical Guidelines</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Total Cholesterol</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>Desirable: &lt; 200 mg/dL<br>Borderline High: 200 - 239 mg/dL<br>High: &ge; 240 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Serum Triglycerides (TG)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>Normal: &lt; 150 mg/dL<br>Borderline High: 150 - 199 mg/dL<br>High: 200 - 499 mg/dL<br>Very High: &ge; 500 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>HDL Cholesterol (\'Good\')</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>Male: &gt; 40 mg/dL<br>Female: &gt; 50 mg/dL<br>High (Protective): &ge; 60 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>LDL Cholesterol (\'Bad\')</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>Optimal: &lt; 100 mg/dL<br>Near Optimal: 100 - 129 mg/dL<br>Borderline High: 130 - 159 mg/dL<br>High: 160 - 189 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>VLDL Cholesterol</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>10 - 30 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Total Cholesterol / HDL Ratio</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>-</td><td ' . tdStyle('left') . '>Desirable: &lt; 4.5</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Specimen: Serum collected after 10-12 hours overnight fasting. Method: Enzymatic Colorimetric.') . '
</div>'
];

// 54. S. Cholesterol - ID: 112
$templates[112] = [
    'name' => 'S. Cholesterol',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval (NCEP ATP III)</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Total Cholesterol</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>Desirable: &lt; 200 mg/dL<br>Borderline High: 200 - 239 mg/dL<br>High: &ge; 240 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: CHOD-PAP Enzymatic Photometric Method. Specimen: Fasting Serum.') . '
</div>'
];

// 55. S. Calcium - ID: 111
$templates[111] = [
    'name' => 'S. Calcium',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Serum Total Calcium</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>Adults: 8.5 - 10.5 mg/dL (2.12 - 2.62 mmol/L)<br>Children: 8.8 - 10.8 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Arsenazo III / CPC Colorimetric. Corrected Calcium should be evaluated if serum albumin is abnormal.') . '
</div>'
];

// 56. SGPT - ID: 110
$templates[110] = [
    'name' => 'SGPT',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>SGPT (Alanine Aminotransferase / ALT)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>U/L</td><td ' . tdStyle('left') . '>Adult Males: &lt; 45 U/L<br>Adult Females: &lt; 34 U/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: IFCC without Pyridoxal Phosphate. Specimen: Serum (Non-hemolyzed).') . '
</div>'
];

// 57. SGOT - ID: 109
$templates[109] = [
    'name' => 'SGOT',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>SGOT (Aspartate Aminotransferase / AST)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>U/L</td><td ' . tdStyle('left') . '>Adult Males: &lt; 40 U/L<br>Adult Females: &lt; 32 U/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: IFCC without Pyridoxal Phosphate. Specimen: Serum (Non-hemolyzed).') . '
</div>'
];

// 58. HBS Ag (Screening) - ID: 108
$templates[108] = [
    'name' => 'HBS Ag (Screening)',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Investigation</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Method</th><th ' . thStyle('left') . '>Reference Value</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Hepatitis B Surface Antigen (HBsAg)</td><td ' . tdStyle('center', true) . '><strong>NON-REACTIVE</strong></td><td ' . tdStyle('left') . '>Immunochromatographic Rapid Assay (ICT)</td><td ' . tdStyle('left') . '>Non-Reactive</td></tr>
</tbody>
</table>
' . notesBox('Specimen: Serum. A non-reactive result does not rule out HBV infection during the early incubation/window period.') . '
</div>'
];

// 59. S. Bilirubin - ID: 107
$templates[107] = [
    'name' => 'S. Bilirubin',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Bilirubin Fraction</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Total Bilirubin</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>0.2 - 1.2 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Direct (Conjugated) Bilirubin</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>0.0 - 0.3 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Indirect (Unconjugated) Bilirubin</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>mg/dL</td><td ' . tdStyle('left') . '>0.2 - 0.8 mg/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: DPD / Diazo Colorimetric Method. Specimen: Serum protected from light.') . '
</div>'
];

// 60. OGTT - ID: 106
$templates[106] = [
    'name' => 'OGTT',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Sample Timing</th><th ' . thStyle('center') . '>Plasma Glucose</th><th ' . thStyle('center') . '>Corresponding mg/dL</th><th ' . thStyle('left') . '>WHO Diagnostic Thresholds</th><th ' . thStyle('center') . '>Urine Sugar</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>0 Hour (Fasting)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong> mmol/L</td><td ' . tdStyle('center') . '>&nbsp; mg/dL</td><td ' . tdStyle('left') . '>Normal: &lt; 6.1 mmol/L (&lt; 110 mg/dL)<br>Diabetes: &ge; 7.0 mmol/L (&ge; 126 mg/dL)</td><td ' . tdStyle('center') . '>Nil</td></tr>
<tr><td ' . tdStyle('left', true) . '>1 Hour (Post 75g Glucose)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong> mmol/L</td><td ' . tdStyle('center') . '>&nbsp; mg/dL</td><td ' . tdStyle('left') . '>&lt; 10.0 mmol/L (&lt; 180 mg/dL)</td><td ' . tdStyle('center') . '>Nil</td></tr>
<tr><td ' . tdStyle('left', true) . '>2 Hours (Post 75g Glucose)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong> mmol/L</td><td ' . tdStyle('center') . '>&nbsp; mg/dL</td><td ' . tdStyle('left') . '>Normal: &lt; 7.8 mmol/L (&lt; 140 mg/dL)<br>IGT: 7.8 - 11.0 mmol/L (140 - 199 mg/dL)<br>Diabetes: &ge; 11.1 mmol/L (&ge; 200 mg/dL)</td><td ' . tdStyle('center') . '>Nil</td></tr>
</tbody>
</table>
' . narrativeHeader('Conclusion / Interpretation') . '
<div style="padding:8px; border:1px solid #d0d7de; background:#fff; min-height:40px;">
Normal Glucose Tolerance.
</div>
' . notesBox('Procedure: 75g anhydrous oral glucose dissolved in 250-300 ml water consumed within 5 minutes after 8-10 hrs fasting.') . '
</div>'
];

// 61. BT, CT - ID: 104
$templates[104] = [
    'name' => 'BT, CT',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Investigation</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Method</th><th ' . thStyle('left') . '>Reference Interval</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Bleeding Time (BT)</td><td ' . tdStyle('center', true) . '><strong>&nbsp; Min &nbsp; Sec</strong></td><td ' . tdStyle('left') . '>Duke\'s Method</td><td ' . tdStyle('left') . '>1 - 5 Minutes</td></tr>
<tr><td ' . tdStyle('left', true) . '>Clotting Time (CT)</td><td ' . tdStyle('center', true) . '><strong>&nbsp; Min &nbsp; Sec</strong></td><td ' . tdStyle('left') . '>Capillary Glass Tube Method</td><td ' . tdStyle('left') . '>4 - 10 Minutes</td></tr>
</tbody>
</table>
' . notesBox('Primary screening tests for platelet function and intrinsic coagulation pathway.') . '
</div>'
];

// 62. C/E Count/Platelet Count - ID: 103
$templates[103] = [
    'name' => 'C/E Count/Platelet Count',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Test Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Total Circulating Eosinophils (AEC)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>/cu.mm</td><td ' . tdStyle('left') . '>50 - 450 /cu.mm (0.05 - 0.45 x 10&sup9;/L)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left', true) . '>Total Platelet Count</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>/cu.mm</td><td ' . tdStyle('left') . '>150,000 - 450,000 /cu.mm (150 - 450 x 10&sup9;/L)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Method: Direct Counting (Fuchs-Rosenthal Chamber / Neubauer Chamber) & Automated Cell Counter. Specimen: EDTA Blood.') . '
</div>'
];

// 63. ICT for Malaria - ID: 102
$templates[102] = [
    'name' => 'ICT for Malaria',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Malarial Antigen Target</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('left') . '>Target Species</th><th ' . thStyle('left') . '>Reference Value</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Plasmodium falciparum (HRP-2) Antigen</td><td ' . tdStyle('center', true) . '><strong>NEGATIVE</strong></td><td ' . tdStyle('left') . '>P. falciparum</td><td ' . tdStyle('left') . '>Negative</td></tr>
<tr><td ' . tdStyle('left', true) . '>Plasmodium vivax (pLDH) Antigen</td><td ' . tdStyle('center', true) . '><strong>NEGATIVE</strong></td><td ' . tdStyle('left') . '>P. vivax / Pan-species</td><td ' . tdStyle('left') . '>Negative</td></tr>
</tbody>
</table>
' . notesBox('Method: Rapid Immunochromatographic Assay (RDT). Specimen: Whole Blood. Microscopic blood film examination is recommended for parasite confirmation and species verification.') . '
</div>'
];

// 64. M.P - ID: 101
$templates[101] = [
    'name' => 'M.P',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Examination</th><th ' . thStyle('left') . '>Observation / Microscopic Findings</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Thick Blood Film (Dehemoglobinized)</td><td ' . tdStyle('left', true) . '>No Malarial Parasite (Trophozoite, Schizont, or Gametocyte) seen after examining 200 oil immersion fields.</td></tr>
<tr><td ' . tdStyle('left', true) . '>Thin Blood Film (Leishman / Giemsa)</td><td ' . tdStyle('left') . '>RBC morphology normal. No intracellular ring forms or Schuffner\'s/Maurer\'s dots seen.</td></tr>
<tr><td ' . tdStyle('left', true) . '>Final Result</td><td ' . tdStyle('left', true) . '><strong>NEGATIVE FOR MALARIAL PARASITES (MP)</strong></td></tr>
</tbody>
</table>
' . notesBox('Specimen: Capillary / EDTA Venous Blood. A single negative smear does not exclude malaria; repeat smear during fever spike recommended if fever persists.') . '
</div>'
];

// 65. CBC - ID: 100
$templates[100] = [
    'name' => 'CBC',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Hematology Parameter</th><th ' . thStyle('center') . '>Result</th><th ' . thStyle('center') . '>Unit</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('center') . '>Flag</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . ' colspan="5" style="background:#f8f9fa; font-weight:700; color:#1a3a60;">RED BLOOD CELL COUNT & INDICES</td></tr>
<tr><td ' . tdStyle('left', true) . '>Hemoglobin (Hb)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>g/dL</td><td ' . tdStyle('left') . '>Male: 13.5 - 17.5; Female: 12.0 - 15.5 g/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Total RBC Count</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>x10&sup1;&sup2;/L</td><td ' . tdStyle('left') . '>Male: 4.5 - 6.0; Female: 3.8 - 5.2 x10&sup1;&sup2;/L</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Packed Cell Volume (PCV / Hct)</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>Male: 40 - 52 %; Female: 36 - 48 %</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Mean Corpuscular Volume (MCV)</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>fL</td><td ' . tdStyle('left') . '>80 - 100 fL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Mean Corpuscular Hb (MCH)</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>pg</td><td ' . tdStyle('left') . '>27 - 33 pg</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>Mean Corpuscular Hb Conc. (MCHC)</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>g/dL</td><td ' . tdStyle('left') . '>32 - 36 g/dL</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>RDW-CV</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>11.5 - 14.5 %</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>

<tr><td ' . tdStyle('left', true) . ' colspan="5" style="background:#f8f9fa; font-weight:700; color:#1a3a60;">TOTAL & DIFFERENTIAL LEUKOCYTE COUNT</td></tr>
<tr><td ' . tdStyle('left', true) . '>Total WBC Count (TLC)</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>/cu.mm</td><td ' . tdStyle('left') . '>4,000 - 11,000 /cu.mm (4.0 - 11.0 x10&sup9;/L)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>&bull; Neutrophils</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>40 - 75 %</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>&bull; Lymphocytes</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>20 - 45 %</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>&bull; Eosinophils</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>01 - 06 %</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>&bull; Monocytes</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>02 - 10 %</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>&bull; Basophils</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>%</td><td ' . tdStyle('left') . '>00 - 01 %</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>

<tr><td ' . tdStyle('left', true) . ' colspan="5" style="background:#f8f9fa; font-weight:700; color:#1a3a60;">PLATELETS & ESR</td></tr>
<tr><td ' . tdStyle('left', true) . '>Platelet Count</td><td ' . tdStyle('center', true) . '><strong>&nbsp;</strong></td><td ' . tdStyle('center') . '>/cu.mm</td><td ' . tdStyle('left') . '>150,000 - 450,000 /cu.mm (150 - 450 x10&sup9;/L)</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
<tr><td ' . tdStyle('left') . '>ESR (Westergren)</td><td ' . tdStyle('center') . '>&nbsp;</td><td ' . tdStyle('center') . '>mm / 1st hr</td><td ' . tdStyle('left') . '>Male: 0 - 10; Female: 0 - 20 mm/1st hr</td><td ' . tdStyle('center') . '>&nbsp;</td></tr>
</tbody>
</table>
' . notesBox('Analyzed on Automated 5-Part Hematology Analyzer with microscopic slide review. Specimen: EDTA Whole Blood.') . '
</div>'
];

echo "Category B loaded (56 templates).\n";

// =========================================================================
// Category C: Imaging, Radiology and Diagnostic Procedures (66 - 88)
// =========================================================================

// 66. Echocardiography (2D & Color) - ID: 99993
$templates[99993] = [
    'name' => 'Echocardiography (2D & Color)',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('2D & M-Mode Measurements') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Value</th><th ' . thStyle('left') . '>Reference Interval</th><th ' . thStyle('left') . '>Parameter</th><th ' . thStyle('center') . '>Value</th><th ' . thStyle('left') . '>Reference Interval</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>LVDd</td><td ' . tdStyle('center') . '>&nbsp; mm</td><td ' . tdStyle('left') . '>36 - 54 mm</td><td ' . tdStyle('left', true) . '>Ejection Fraction (EF)</td><td ' . tdStyle('center', true) . ' style="font-size:14px; color:#1a3a60;"><strong>&nbsp; %</strong></td><td ' . tdStyle('left') . '>&ge; 55 %</td></tr>
<tr><td ' . tdStyle('left', true) . '>LVDs</td><td ' . tdStyle('center') . '>&nbsp; mm</td><td ' . tdStyle('left') . '>22 - 38 mm</td><td ' . tdStyle('left', true) . '>Fractional Shortening (FS)</td><td ' . tdStyle('center') . '>&nbsp; %</td><td ' . tdStyle('left') . '>28 - 44 %</td></tr>
<tr><td ' . tdStyle('left', true) . '>IVS (Diastole)</td><td ' . tdStyle('center') . '>&nbsp; mm</td><td ' . tdStyle('left') . '>6 - 11 mm</td><td ' . tdStyle('left', true) . '>Left Atrium (LA)</td><td ' . tdStyle('center') . '>&nbsp; mm</td><td ' . tdStyle('left') . '>20 - 40 mm</td></tr>
<tr><td ' . tdStyle('left', true) . '>LVPW (Diastole)</td><td ' . tdStyle('center') . '>&nbsp; mm</td><td ' . tdStyle('left') . '>6 - 11 mm</td><td ' . tdStyle('left', true) . '>Aortic Root</td><td ' . tdStyle('center') . '>&nbsp; mm</td><td ' . tdStyle('left') . '>20 - 37 mm</td></tr>
</tbody>
</table>

' . narrativeHeader('Color Doppler & Valvular Assessment') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; <strong>Mitral Valve:</strong> Normal leaflet motion. No mitral stenosis or significant regurgitation.<br>
&bull; <strong>Aortic Valve:</strong> Trileaflet, opens well. No aortic stenosis or regurgitation.<br>
&bull; <strong>Tricuspid Valve:</strong> Grossly normal. Trace tricuspid regurgitation (physiological). Estimated PASP normal.<br>
&bull; <strong>Pulmonary Valve:</strong> Normal anatomy and forward flow velocity.
</div>

' . narrativeHeader('Structural Findings') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; <strong>Left Ventricle:</strong> Normal LV chamber dimensions and wall thickness. Good global systolic contraction.<br>
&bull; <strong>Regional Wall Motion:</strong> No regional wall motion abnormality (RWMA) at rest.<br>
&bull; <strong>Right Ventricle & Atria:</strong> Normal size and function. TAPSE normal.<br>
&bull; <strong>Pericardium:</strong> No pericardial effusion seen.<br>
&bull; <strong>Intracardiac Clot / Shunt:</strong> No clot or mass seen. Interatrial septum appears intact.
</div>

' . narrativeHeader('Impression & Conclusion') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60; line-height:1.6;">
1. Normal cardiac chambers size with good global LV systolic function (EF: &nbsp;%).<br>
2. No resting regional wall motion abnormality (RWMA).<br>
3. Normal valvular morphology and Doppler hemodynamics.<br>
4. No pericardial effusion.
</div>
</div>'
];

// 67. Nebulization - ID: 158
$templates[158] = [
    'name' => 'Nebulization',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Procedure Record Parameter</th><th ' . thStyle('left') . '>Clinical Entry</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Indication</td><td ' . tdStyle('left') . '>Bronchospasm / Acute Wheeze / Acute Asthma Exacerbation</td></tr>
<tr><td ' . tdStyle('left', true) . '>Medication & Dosage Administered</td><td ' . tdStyle('left') . '>Nebulization with Salbutamol (2.5 mg) &amp; Ipratropium Bromide (0.5 mg) respule with 2.5 ml Normal Saline</td></tr>
<tr><td ' . tdStyle('left', true) . '>Delivery Method</td><td ' . tdStyle('left') . '>Jet Nebulizer with Face Mask via Medical Oxygen / Compressed Air (6 L/min)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Pre-Procedure Vitals</td><td ' . tdStyle('left') . '>Pulse: &nbsp; bpm; SpO2: &nbsp; % (on room air); RR: &nbsp; /min; Chest: Rhonchi present</td></tr>
<tr><td ' . tdStyle('left', true) . '>Post-Procedure Evaluation</td><td ' . tdStyle('left') . '>Pulse: &nbsp; bpm; SpO2: &nbsp; %; RR: &nbsp; /min; Chest: Significant reduction in wheeze; good air entry</td></tr>
<tr><td ' . tdStyle('left', true) . '>Adverse Reaction / Complication</td><td ' . tdStyle('left') . '>None reported. Procedure tolerated well.</td></tr>
</tbody>
</table>
</div>'
];

// 68. ECG - ID: 157
$templates[157] = [
    'name' => 'ECG',
    'content' => '<div style="font-family: Arial, sans-serif;">
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>ECG Parameter</th><th ' . thStyle('center') . '>Observed Value</th><th ' . thStyle('left') . '>ECG Parameter</th><th ' . thStyle('center') . '>Observed Value</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Heart Rate</td><td ' . tdStyle('center') . '>&nbsp; bpm</td><td ' . tdStyle('left', true) . '>P-R Interval</td><td ' . tdStyle('center') . '>0.16 sec (0.12 - 0.20)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Rhythm</td><td ' . tdStyle('center') . '>Normal Sinus Rhythm</td><td ' . tdStyle('left', true) . '>QRS Duration</td><td ' . tdStyle('center') . '>0.08 sec (&lt; 0.12)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Mean QRS Axis</td><td ' . tdStyle('center') . '>Normal Axis</td><td ' . tdStyle('left', true) . '>QT / QTc Interval</td><td ' . tdStyle('center') . '>0.38 / 0.42 sec (&lt; 0.44)</td></tr>
</tbody>
</table>

' . narrativeHeader('Detailed Waveform Analysis') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; <strong>P Wave:</strong> Normal amplitude and morphology in leads II and V1.<br>
&bull; <strong>QRS Complex:</strong> Normal R wave progression in chest leads V1 to V6. No pathological Q waves.<br>
&bull; <strong>ST Segment:</strong> Isoelectric in all standard and precordial leads. No ST elevation or depression.<br>
&bull; <strong>T Wave:</strong> Normal upright morphology in leads I, II, aVL, V2-V6. No pathological inversion.
</div>

' . narrativeHeader('Electrocardiographic Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL 12-LEAD RESTING ELECTROCARDIOGRAM (ECG).<br>
<small style="font-weight:normal; color:#666;">Normal sinus rhythm with normal axis, intervals, and repolarization pattern.</small>
</div>
' . notesBox('Recorded at 25 mm/sec speed and 10 mm/mV voltage calibration.') . '
</div>'
];

// 69. USG- KUB - ID: 156
$templates[156] = [
    'name' => 'USG- KUB',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('ULTRASONOGRAPHY OF KIDNEYS, URETERS & BLADDER (KUB)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; <strong>Right Kidney:</strong> Normal in size (&nbsp; x &nbsp; mm), shape, and position. Normal cortical thickness and parenchymal echogenicity. Corticomedullary differentiation is well preserved. No calculus, solid focal lesion, or hydronephrosis seen.<br>
&bull; <strong>Left Kidney:</strong> Normal in size (&nbsp; x &nbsp; mm), shape, and position. Normal cortical thickness and parenchymal echogenicity. Good corticomedullary demarcation. No calculus, solid mass, or hydronephrosis seen.<br>
&bull; <strong>Ureters:</strong> Both ureters are not dilated.<br>
&bull; <strong>Urinary Bladder:</strong> Well distended with clear anechoic lumen. Walls are smooth with normal thickness. No calculus, polyp, or mass lesion seen.<br>
&bull; <strong>Prostate (Male):</strong> Normal in size and volume (~ &nbsp; gm). Echo texture is homogeneous. Capsule is intact. Post-void residual (PVR) volume is insignificant (&lt; 20 mL).
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL ULTRASONOGRAPHIC STUDY OF KIDNEYS, URETERS AND URINARY BLADDER (KUB).<br>
<small style="font-weight:normal; color:#666;">No calculus, hydronephrosis, or space-occupying lesion seen.</small>
</div>
</div>'
];

// 70. USG-HBS - ID: 155
$templates[155] = [
    'name' => 'USG-HBS',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('ULTRASONOGRAPHY OF HEPATOBILIARY SYSTEM (HBS)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; <strong>Liver:</strong> Normal in size (Span: &nbsp; cm). Capsule is smooth and regular. Parenchymal echogenicity is normal and homogeneous. No focal mass, cyst, or space-occupying lesion seen. Intrahepatic biliary channels (IHBR) are not dilated.<br>
&bull; <strong>Portal Vein:</strong> Normal in caliber (&le; 11 mm) with normal hepatopetal flow.<br>
&bull; <strong>Gall Bladder:</strong> Well distended. Wall is thin, smooth, and measures &lt; 3 mm. Lumen is clear and anechoic. No calculus, sludge, or polyp seen.<br>
&bull; <strong>Common Bile Duct (CBD):</strong> Normal in caliber (measures &nbsp; mm; &le; 6 mm). No intraductal calculus or dilatation.<br>
&bull; <strong>Spleen:</strong> Normal in size (Length: &nbsp; cm; &le; 12 cm) and homogeneous in echogenicity. Splenic vein is normal.<br>
&bull; <strong>Pancreas:</strong> Head, body, and visualized tail appear normal in size and echotexture. Main pancreatic duct is not dilated.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL ULTRASONOGRAM OF HEPATOBILIARY SYSTEM (HBS).<br>
<small style="font-weight:normal; color:#666;">No hepatomegaly, focal liver lesion, cholelithiasis, or splenomegaly seen.</small>
</div>
</div>'
];

// 71. USG- Pregnancy Profile - ID: 154
$templates[154] = [
    'name' => 'USG- Pregnancy Profile',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('OBSTETRIC ULTRASONOGRAPHY REPORT (PREGNANCY PROFILE)') . '
<table ' . tableStyle() . '>
<thead><tr><th ' . thStyle('left') . '>Fetal Parameter</th><th ' . thStyle('left') . '>Measurement / Finding</th><th ' . thStyle('left') . '>Biometric Age</th></tr></thead>
<tbody>
<tr><td ' . tdStyle('left', true) . '>Number of Fetuses</td><td ' . tdStyle('left') . '>Single intrauterine pregnancy</td><td ' . tdStyle('left') . '>-</td></tr>
<tr><td ' . tdStyle('left', true) . '>Presentation &amp; Lie</td><td ' . tdStyle('left') . '>Cephalic / Longitudinal</td><td ' . tdStyle('left') . '>-</td></tr>
<tr><td ' . tdStyle('left', true) . '>Fetal Cardiac Activity</td><td ' . tdStyle('left', true) . '>Present &amp; Regular</td><td ' . tdStyle('left') . 'Fetal Heart Rate: &nbsp; bpm (120-160)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Fetal Movement</td><td ' . tdStyle('left') . '>Present &amp; Active</td><td ' . tdStyle('left') . '>-</td></tr>
<tr><td ' . tdStyle('left', true) . '>Biparietal Diameter (BPD)</td><td ' . tdStyle('left') . '<strong>&nbsp; mm</strong></td><td ' . tdStyle('left') . 'Corresponding to &nbsp; Wks &plusmn; &nbsp; Days</td></tr>
<tr><td ' . tdStyle('left', true) . '>Head Circumference (HC)</td><td ' . tdStyle('left') . '<strong>&nbsp; mm</strong></td><td ' . tdStyle('left') . 'Corresponding to &nbsp; Wks &plusmn; &nbsp; Days</td></tr>
<tr><td ' . tdStyle('left', true) . '>Abdominal Circumference (AC)</td><td ' . tdStyle('left') . '<strong>&nbsp; mm</strong></td><td ' . tdStyle('left') . 'Corresponding to &nbsp; Wks &plusmn; &nbsp; Days</td></tr>
<tr><td ' . tdStyle('left', true) . '>Femur Length (FL)</td><td ' . tdStyle('left') . '<strong>&nbsp; mm</strong></td><td ' . tdStyle('left') . 'Corresponding to &nbsp; Wks &plusmn; &nbsp; Days</td></tr>
<tr><td ' . tdStyle('left', true) . '>Estimated Fetal Weight (EFW)</td><td ' . tdStyle('left') . '<strong>&nbsp; grams (&plusmn; 10%)</strong></td><td ' . tdStyle('left') . '>Hadlock Formula</td></tr>
<tr><td ' . tdStyle('left', true) . '>Placenta</td><td ' . tdStyle('left') . '>Location: Anterior / Posterior; Maturity: Grade I/II; Distance from internal os is adequate (&gt; 2.5 cm)</td><td ' . tdStyle('left') . 'No previa / retroplacental clot</td></tr>
<tr><td ' . tdStyle('left', true) . '>Amniotic Fluid Index (AFI)</td><td ' . tdStyle('left') . '>AFI: &nbsp; cm (Single deepest pocket: &nbsp; cm)</td><td ' . tdStyle('left') . '>Adequate (Normal: 8 - 18 cm)</td></tr>
<tr><td ' . tdStyle('left', true) . '>Gross Anomaly Survey</td><td ' . tdStyle('left') . '>Cranium, spine, stomach bubble, 4-chamber heart view, kidneys, and bladder are visualized and appear grossly normal</td><td ' . tdStyle('left') . '>No gross anomaly detected</td></tr>
<tr><td ' . tdStyle('left', true) . '>Cervical Length</td><td ' . tdStyle('left') . '>Internal os closed. Cervix measures &nbsp; mm (&gt; 30 mm)</td><td ' . tdStyle('left') . '>Competent internal os</td></tr>
</tbody>
</table>

' . narrativeHeader('Impression & Obstetric Summary') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60; line-height:1.7;">
&bull; Single live intrauterine pregnancy of &nbsp; weeks &plusmn; &nbsp; days by composite ultrasound biometry.<br>
&bull; Presentation: Cephalic with active fetal cardiac activity.<br>
&bull; Placenta: Anterior/Posterior, Grade &nbsp;, no previa.<br>
&bull; Amniotic fluid volume is adequate.<br>
&bull; Expected Date of Delivery (EDD by USG): &nbsp;<br>
<small style="font-weight:normal; color:#666;">Clinical correlation with LMP and regular antenatal follow-up recommended.</small>
</div>
</div>'
];

// 72. USG- Lower Abdomen - ID: 153
$templates[153] = [
    'name' => 'USG- Lower Abdomen',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('ULTRASONOGRAPHY OF LOWER ABDOMEN & PELVIS') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; <strong>Urinary Bladder:</strong> Well distended. Wall is thin, regular, and normal in thickness. Lumen is free of calculus, mass, or intraluminal lesion.<br>
&bull; <strong>Prostate (Male):</strong> Normal size and shape. Measures approximately &nbsp; x &nbsp; x &nbsp; mm with estimated volume of &nbsp; grams (&le; 25 gm). Parenchyma is homogeneous. Capsule is smooth. Insignificant post-void residue.<br>
&bull; <strong>Uterus &amp; Adnexa (Female):</strong> Uterus is normal in size, anteverted, and regular in outline. Myometrium shows uniform echogenicity without fibroid or mass. Endometrial stripe is central and measures &nbsp; mm. Both ovaries are normal in size and follicular pattern. No adnexal mass or cyst seen.<br>
&bull; <strong>Pouch of Douglas (POD):</strong> No free fluid or collection seen in pelvis.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL ULTRASONOGRAPHIC STUDY OF LOWER ABDOMEN AND PELVIS.<br>
<small style="font-weight:normal; color:#666;">No pelvic mass, cyst, urinary bladder pathology, or free fluid detected.</small>
</div>
</div>'
];

// 73. USG- Upper Abdomen - ID: 152
$templates[152] = [
    'name' => 'USG- Upper Abdomen',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('ULTRASONOGRAPHY OF UPPER ABDOMEN') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; <strong>Liver:</strong> Normal in size (Span: &nbsp; cm). Outline is regular. Echo texture is homogeneous. No focal mass, cyst, or SOL seen. Intrahepatic biliary radicals are not dilated.<br>
&bull; <strong>Gall Bladder:</strong> Well filled. Wall is thin and smooth (&lt; 3 mm). No gallstones, sludge, or polyp seen. CBD is normal in caliber.<br>
&bull; <strong>Spleen:</strong> Normal in size and echogenicity. Splenic vein is normal.<br>
&bull; <strong>Pancreas:</strong> Normal in size and echotexture. No focal lesion or ductal dilatation.<br>
&bull; <strong>Kidneys (Upper Poles &amp; Vis.):</strong> Both kidneys are visualized and appear normal in size, position, and cortical thickness. No hydronephrosis or calculi seen.<br>
&bull; <strong>Peritoneum:</strong> No ascites or lymphadenopathy in upper abdomen.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL ULTRASONOGRAPHIC STUDY OF UPPER ABDOMEN.<br>
<small style="font-weight:normal; color:#666;">No hepatosplenomegaly, cholelithiasis, or solid organ focal lesion detected.</small>
</div>
</div>'
];

// 74. USG- Whole Abdomen - ID: 151
$templates[151] = [
    'name' => 'USG- Whole Abdomen',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('COMPREHENSIVE ULTRASONOGRAPHY OF WHOLE ABDOMEN & PELVIS') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; <strong>Liver:</strong> Normal in size (Span: &nbsp; cm). Smooth surface and uniform parenchymal echogenicity. No focal lesion, cyst, or SOL seen. Intrahepatic biliary channels and portal vein are normal.<br>
&bull; <strong>Gall Bladder:</strong> Well distended. Wall is thin and smooth (&lt; 3 mm). Lumen is clear without calculus, sludge, or mass.<br>
&bull; <strong>Common Bile Duct (CBD):</strong> Normal in caliber (&nbsp; mm; &le; 6 mm).<br>
&bull; <strong>Spleen:</strong> Normal in size (Length: &nbsp; cm) and homogeneous in echotexture. No focal lesion.<br>
&bull; <strong>Pancreas:</strong> Visualized head and body appear normal in size and echogenicity. Duct is not dilated.<br>
&bull; <strong>Right Kidney:</strong> Normal in size (&nbsp; x &nbsp; mm), shape, and cortical thickness. Good corticomedullary differentiation. No calculus, hydronephrosis, or space-occupying lesion seen.<br>
&bull; <strong>Left Kidney:</strong> Normal in size (&nbsp; x &nbsp; mm), shape, and cortical thickness. Good corticomedullary differentiation. No calculus, hydronephrosis, or space-occupying lesion seen.<br>
&bull; <strong>Urinary Bladder:</strong> Well distended with clear lumen. Smooth, normal wall thickness. No calculus or mass seen.<br>
&bull; <strong>Prostate / Uterus:</strong> Normal in size, contour, and internal echo pattern for age.<br>
&bull; <strong>Peritoneum &amp; Retroperitoneum:</strong> No ascites / free fluid in abdominal cavity or pelvis. No para-aortic lymphadenopathy.
</div>

' . narrativeHeader('Impression & Conclusion') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL ULTRASONOGRAPHIC STUDY OF WHOLE ABDOMEN.<br>
<small style="font-weight:normal; color:#666;">No organomegaly, focal parenchymal mass, cholelithiasis, nephrolithiasis, or ascites seen.</small>
</div>
</div>'
];

// 75. Leg B/V - ID: 150
$templates[150] = [
    'name' => 'Leg B/V',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY OF LEG (TIBIA & FIBULA) BOTH VIEWS (A/P & LATERAL)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Bony architecture and cortical continuity of Tibia and Fibula shafts are well maintained.<br>
&bull; No fracture line, cortical disruption, or dislocation seen.<br>
&bull; Knee joint and Ankle joint spaces included are within normal limits.<br>
&bull; No evidence of osteolytic or osteoblastic bone lesion.<br>
&bull; Surrounding soft tissue shadows appear normal without swelling or foreign body.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NO RADIOLOGICAL EVIDENCE OF BONE FRACTURE OR DISLOCATION IN THE EXAMINED LEG (TIBIA &amp; FIBULA).
</div>
</div>'
];

// 76. Hand B/V - ID: 149
$templates[149] = [
    'name' => 'Hand B/V',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY OF HAND BOTH VIEWS (A/P & OBLIQUE)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; All visualized metacarpals and phalanges show intact cortical margins.<br>
&bull; Visualized carpal bones are normal in alignment and architecture.<br>
&bull; No fracture, dislocation, or subluxation seen.<br>
&bull; Interphalangeal and metacarpophalangeal joint spaces are preserved.<br>
&bull; Soft tissue shadows are within normal limits.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL RADIOGRAPH OF HAND (A/P &amp; OBLIQUE VIEWS). NO EVIDENCE OF RECENT FRACTURE OR JOINT DISLOCATION.
</div>
</div>'
];

// 77. Pelvis A/P - ID: 148
$templates[148] = [
    'name' => 'Pelvis A/P',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY PELVIS WITH BOTH HIP JOINTS (A/P VIEW)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Pelvic ring and obturator rings are intact with smooth cortical margins.<br>
&bull; Both femoral heads are spherical and congruent within normal acetabular fossae.<br>
&bull; Femoral necks, greater and lesser trochanters appear intact without fracture.<br>
&bull; Both Sacroiliac (SI) joints and Pubic Symphysis are normal in alignment and width.<br>
&bull; No evidence of osteolytic/sclerotic lesion or pelvic bone fracture.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL RADIOGRAPH OF PELVIS (A/P VIEW). NO EVIDENCE OF FRACTURE, DISLOCATION, OR AVASCULAR NECROSIS.
</div>
</div>'
];

// 78. Dorsal Spine B/V - ID: 147
$templates[147] = [
    'name' => 'Dorsal Spine B/V',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY DORSAL (THORACIC) SPINE BOTH VIEWS (A/P & LATERAL)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Normal thoracic kyphosis and vertebral column alignment.<br>
&bull; Vertebral body heights are maintained without compression fracture or collapse.<br>
&bull; Intervertebral disc spaces are preserved throughout the thoracic spine.<br>
&bull; Pedicles, spinous processes, and costovertebral articulations are intact.<br>
&bull; No paravertebral soft tissue shadow or calcification seen.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL RADIOGRAPH OF DORSAL SPINE (A/P &amp; LATERAL VIEWS). NO EVIDENCE OF FRACTURE OR SIGNIFICANT SPONDYLOTIC CHANGE.
</div>
</div>'
];

// 79. L/S Spine B/V - ID: 146
$templates[146] = [
    'name' => 'L/S Spine B/V',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY LUMBO-SACRAL (L/S) SPINE BOTH VIEWS (A/P & LATERAL)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Normal lumbar lordotic curvature is preserved.<br>
&bull; Vertebral bodies (L1 to L5) show normal height, alignment, and bone density.<br>
&bull; Intervertebral disc spaces (particularly L4-L5 and L5-S1) appear adequate.<br>
&bull; No evidence of spondylolisthesis, pars interarticularis defect, or compression fracture.<br>
&bull; Pedicles and transverse processes are intact bilaterally.<br>
&bull; Sacroiliac joints appear normal. No significant osteophytosis.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL RADIOGRAPH OF LUMBO-SACRAL SPINE (A/P &amp; LATERAL VIEWS).<br>
<small style="font-weight:normal; color:#666;">No fracture, vertebral collapse, disc space narrowing, or listhesis seen.</small>
</div>
</div>'
];

// 80. Abdomen E/P - ID: 145
$templates[145] = [
    'name' => 'Abdomen E/P',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY ABDOMEN ERECT & PLAIN (A/P VIEW)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; <strong>Subdiaphragmatic Spaces:</strong> Both domes of diaphragm are well visualized. No free air / gas under right or left dome of diaphragm (No pneumoperitoneum).<br>
&bull; <strong>Bowel Gas Pattern:</strong> Normal stomach and colonic gas shadows. No multiple air-fluid levels or dilated small bowel loops (No bowel obstruction).<br>
&bull; <strong>Psoas &amp; Renal Shadows:</strong> Outlines of both psoas muscles are clear and symmetric.<br>
&bull; <strong>Radio-opaque Shadows:</strong> No abnormal radio-opaque shadow, calculus, or foreign body seen in the abdomen or pelvis.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL ERECT RADIOGRAPH OF ABDOMEN.<br>
<small style="font-weight:normal; color:#666;">No evidence of pneumoperitoneum (no bowel perforation) or bowel obstruction.</small>
</div>
</div>'
];

// 81. KUB - ID: 144
$templates[144] = [
    'name' => 'KUB',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY KIDNEYS, URETERS & BLADDER (KUB)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Outlines of both kidneys and psoas margins are visible in normal anatomical positions.<br>
&bull; No radio-opaque calculus shadow seen along the renal silhouettes, line of ureters, or bladder region.<br>
&bull; Normal abdominal bowel gas pattern.<br>
&bull; Visualized lower ribs, lumbar vertebrae, and pelvic bones appear normal without fracture or destructive bone lesion.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NO RADIO-OPAQUE CALCULUS DETECTED ALONG THE URINARY TRACT (KUB REGION).
</div>
</div>'
];

// 82. Cervical Spine B/V - ID: 143
$templates[143] = [
    'name' => 'Cervical Spine B/V',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY CERVICAL SPINE BOTH VIEWS (A/P & LATERAL)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Normal cervical lordosis is preserved.<br>
&bull; Alignment of vertebral bodies (C1 to C7) is anatomical without subluxation.<br>
&bull; Heights of all cervical vertebral bodies are maintained.<br>
&bull; Intervertebral disc spaces are well preserved without narrowing.<br>
&bull; Atlanto-dens interval (ADI) is normal (&lt; 3 mm). Spinous processes and pedicles are intact.<br>
&bull; Prevertebral soft tissue shadow is within normal limits.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL RADIOGRAPH OF CERVICAL SPINE (A/P &amp; LATERAL VIEWS).<br>
<small style="font-weight:normal; color:#666;">No evidence of fracture, dislocation, or cervical spondylosis.</small>
</div>
</div>'
];

// 83. Neck B/V - ID: 142
$templates[142] = [
    'name' => 'Neck B/V',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY SOFT TISSUE NECK BOTH VIEWS (A/P & LATERAL)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Retropharyngeal and prevertebral soft tissue spaces are within normal limits for age.<br>
&bull; Tracheal air column is central and patent without narrowing or deviation.<br>
&bull; Epiglottis and aryepiglottic folds appear normal. No \'thumb sign\'.<br>
&bull; Hyoid bone and laryngeal cartilages are normal.<br>
&bull; No radio-opaque foreign body or soft tissue calcification seen.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL RADIOGRAPH OF SOFT TISSUE NECK. NO EVIDENCE OF RETROPHARYNGEAL ABSCESS, AIRWAY OBSTRUCTION, OR RADIO-OPAQUE FOREIGN BODY.
</div>
</div>'
];

// 84. PNS O/M View - ID: 141
$templates[141] = [
    'name' => 'PNS O/M View',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY PARANASAL SINUSES OCCIPITO-MENTAL (WATER\'S) VIEW') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Both Maxillary Antra (Sinuses) are well aerated with clear, normal lucency.<br>
&bull; No mucosal thickening, clouding, haziness, or fluid level seen in either maxillary sinus.<br>
&bull; Frontal and Ethmoidal sinuses show normal pneumatization.<br>
&bull; Nasal septum is central in position without significant deviation.<br>
&bull; Orbital margins and facial bones appear intact.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL RADIOGRAPH OF PARANASAL SINUSES (WATER\'S VIEW).<br>
<small style="font-weight:normal; color:#666;">No radiological evidence of acute or chronic sinusitis.</small>
</div>
</div>'
];

// 85. Skull B/V - ID: 140
$templates[140] = [
    'name' => 'Skull B/V',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY SKULL BOTH VIEWS (A/P & LATERAL)') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Cranial vault bones (Frontal, Parietal, Occipital, and Temporal) show intact inner and outer tables.<br>
&bull; Cranial sutures are normal in appearance and width.<br>
&bull; No linear or depressed fracture line seen.<br>
&bull; Vascular markings and digital impressions are within normal physiological limits.<br>
&bull; Sella turcica is normal in size, shape, and cortical contour.<br>
&bull; No focal osteolytic or blastic lesion. Soft tissue shadow of scalp is normal.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL RADIOGRAPH OF SKULL (A/P &amp; LATERAL VIEWS). NO EVIDENCE OF VAULT FRACTURE OR BONY LESION.
</div>
</div>'
];

// 86. Chest Lateral View - ID: 139
$templates[139] = [
    'name' => 'Chest Lateral View',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY CHEST LATERAL VIEW') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Retrosternal and retrocardiac spaces are clear with normal lucency.<br>
&bull; Posterior costophrenic angle is sharp and clear without effusion.<br>
&bull; Thoracic vertebral bodies show normal height and density without collapse.<br>
&bull; Trachea and major airways are patent.<br>
&bull; Cardiac silhouette and hilar structures appear within normal limits.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL CHEST RADIOGRAPH (LATERAL VIEW). NO HILAR MASS, RETROCARDIAC CONSOLIDATION, OR EFFUSION.
</div>
</div>'
];

// 87. Chest A/P - ID: 138
$templates[138] = [
    'name' => 'Chest A/P',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY CHEST ANTERO-POSTERIOR (A/P) VIEW') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Bilateral lung fields are well aerated.<br>
&bull; No focal pulmonary consolidation, mass, cavitary lesion, or active infiltration seen.<br>
&bull; Bronchovascular markings are normal in distribution.<br>
&bull; Both costophrenic and cardiophrenic angles are clear.<br>
&bull; Cardiac shadow is within normal limits (allowing for magnification of A/P projection).<br>
&bull; Bony thorax and visualized ribs show no fracture.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL CHEST RADIOGRAPH (A/P VIEW). NO ACTIVE PULMONARY LESION OR PLEURAL EFFUSION SEEN.
</div>
</div>'
];

// 88. Chest P/A - ID: 137
$templates[137] = [
    'name' => 'Chest P/A',
    'content' => '<div style="font-family: Arial, sans-serif;">
' . narrativeHeader('PLAIN X-RAY CHEST POSTERO-ANTERIOR (P/A) VIEW') . '
<div style="font-size:13px; line-height:1.7; padding:8px 10px; border:1px solid #e1e4e8; background:#fff;">
&bull; Both lung fields are clear with normal bilateral bronchovascular markings.<br>
&bull; No parenchymal consolidation, infiltrate, nodule, mass, or cavity seen.<br>
&bull; Both costophrenic and cardiophrenic angles are sharp and clear.<br>
&bull; Cardiac silhouette is normal in size and contour. Cardiothoracic ratio (CTR) is &lt; 50%.<br>
&bull; Aorta and superior mediastinum are within normal limits.<br>
&bull; Trachea is central in position.<br>
&bull; Both domes of diaphragm are smooth and normal in level and curvature.<br>
&bull; Bony thoracic cage and surrounding soft tissues are unremarkable.
</div>

' . narrativeHeader('Impression') . '
<div style="padding:10px; border:1px solid #d0d7de; background:#f9fbfe; font-size:14px; font-weight:600; color:#1a3a60;">
NORMAL CHEST RADIOGRAPH (P/A VIEW).<br>
<small style="font-weight:normal; color:#666;">No radiographic evidence of active pulmonary tuberculosis, consolidation, cardiomegaly, or pleural effusion.</small>
</div>
</div>'
];

echo "All 88 templates defined! Total: " . count($templates) . "\n";

// Insert/Update into database for hospital_id = 98
$stmt_check = $conn->prepare("SELECT id FROM template WHERE category_id = ? AND hospital_id = '98'");
$stmt_update = $conn->prepare("UPDATE template SET name = ?, template = ? WHERE id = ?");
$stmt_insert = $conn->prepare("INSERT INTO template (name, template, category_id, hospital_id, user) VALUES (?, ?, ?, '98', '1')");

$inserted = 0;
$updated = 0;

foreach ($templates as $cat_id => $tpl) {
    $cat_id_str = (string)$cat_id;
    $stmt_check->bind_param("s", $cat_id_str);
    $stmt_check->execute();
    $res = $stmt_check->get_result();

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $existing_id = $row['id'];
        $stmt_update->bind_param("ssi", $tpl['name'], $tpl['content'], $existing_id);
        $stmt_update->execute();
        $updated++;
    } else {
        $stmt_insert->bind_param("sss", $tpl['name'], $tpl['content'], $cat_id_str);
        $stmt_insert->execute();
        $inserted++;
    }
}

echo "Database Seed Complete!\n";
echo "Inserted: $inserted\n";
echo "Updated: $updated\n";
echo "Total in template table for Hosp 98: " . $conn->query("SELECT COUNT(*) FROM template WHERE hospital_id = '98'")->fetch_row()[0] . "\n";
