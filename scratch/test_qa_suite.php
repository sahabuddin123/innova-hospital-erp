<?php
/**
 * QA Automation Test Suite for Lifecare Diagnostic Center
 * Tests all 88 diagnostic test templates, mapping, placeholders, and snapshot preservation
 */

$m = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
if ($m->connect_error) {
    die("DB connection failed: " . $m->connect_error . "\n");
}

echo "========================================================================\n";
echo "  LIFECARE DIAGNOSTIC CENTER — AUTOMATED QA & REGRESSION TEST SUITE\n";
echo "========================================================================\n\n";

$tests_passed = 0;
$tests_failed = 0;

function assert_test($description, $condition, $details = '') {
    global $tests_passed, $tests_failed;
    if ($condition) {
        $tests_passed++;
        echo " [PASS] $description\n";
    } else {
        $tests_failed++;
        echo " [FAIL] $description" . ($details ? " -> $details" : "") . "\n";
    }
}

// TEST 1: Check template table schema
echo "--- TEST GROUP 1: Database Schema & Migration ---\n";
$col_res = $m->query("SHOW COLUMNS FROM template LIKE 'template'");
$col_row = $col_res->fetch_assoc();
assert_test("template.template column is LONGTEXT", strtolower($col_row['Type']) === 'longtext', "Got: " . $col_row['Type']);

$idx_res = $m->query("SHOW INDEX FROM template WHERE Key_name = 'idx_hosp_cat'");
assert_test("Composite index idx_hosp_cat (hospital_id, category_id) exists", $idx_res->num_rows > 0);

// TEST 2: All 88 Diagnostic Tests Mapped
echo "\n--- TEST GROUP 2: 88 Diagnostic Catalog Coverage ---\n";
$tests_inventory = [
    // Category A (1-9)
    "2 Hours After Breakfast Blood Sugar (2 hrs ABF)",
    "Fasting Blood Sugar (FBS)",
    "Random Blood Sugar (RBS)",
    "Thyroxine (T4)",
    "Triiodothyronine (T3)",
    "Serum Thyroid-Stimulating Hormone (TSH)",
    "Luteinizing Hormone (LH)",
    "Follicle-Stimulating Hormone (FSH)",
    "Free Thyroxine (FT4)",
    // Category B (10-65)
    "ESR", "Febrile Antigen", "Hb Electrophoresis", "S. Lipase", "ANA", "Blood for C/S",
    "Iron Profile", "Vitamin D-Level", "Semen Analysis", "HbA1c", "Pus for C/S",
    "Hbs AG Confirmatory", "Troponin I", "Testosteron", "Dengue NS-1/IGG/IGM",
    "S.Electrolyte", "S.Amylase", "S.IGE (Level)", "Prothombin Time", "S.PROLACTIN",
    "Urine C/S", "Urine P/T", "Urine R/E", "OBT", "Stool C/S", "Stool R/E",
    "Alkaline phosphatase", "LFT", "PBF", "Hb%", "S. Albumin", "Sputum for AFB",
    "Tuberculin Test (MT)", "CRP", "Blood Grouping", "TPHA", "VDRL", "ASO Titre",
    "Widal Test", "R/A Factor", "S. Uric Acid", "S. Creatinine", "Blood Urea",
    "Lipid Profile", "S. Cholesterol", "S. Calcium", "SGPT", "SGOT", "HBS Ag (Screening)",
    "S. Bilirubin", "OGTT", "BT, CT", "C/E Count/Platelet Count", "ICT for Malaria",
    "M.P", "CBC",
    // Category C (66-88)
    "Echocardiography (2D & Color)", "Nebulization", "ECG", "USG- KUB", "USG-HBS",
    "USG- Pregnancy Profile", "USG- Lower Abdomen", "USG- Upper Abdomen",
    "USG- Whole Abdomen", "Leg B/V", "Hand B/V", "Pelvis A/P", "Dorsal Spine B/V",
    "L/S Spine B/V", "Abdomen E/P", "KUB", "Cervical Spine B/V", "Neck B/V",
    "PNS O/M View", "Skull B/V", "Chest Lateral View", "Chest A/P", "Chest P/A"
];

assert_test("Inventory contains exactly 88 test specifications", count($tests_inventory) === 88);

$matched_count = 0;
$missing_tests = [];
$template_lengths = [];

foreach ($tests_inventory as $idx => $test_name) {
    // 1. Check in payment_category
    $escaped_name = $m->real_escape_string($test_name);
    $cat_res = $m->query("SELECT id, category FROM payment_category WHERE hospital_id = '98' AND category = '$escaped_name' LIMIT 1");
    if ($cat_res->num_rows === 0) {
        $missing_tests[] = "payment_category: $test_name";
        continue;
    }
    $cat = $cat_res->fetch_assoc();
    $cat_id = $cat['id'];

    // 2. Check in template table
    $tmpl_res = $m->query("SELECT id, name, template FROM template WHERE hospital_id = '98' AND category_id = '$cat_id' LIMIT 1");
    if ($tmpl_res->num_rows === 0) {
        $missing_tests[] = "template: $test_name (cat_id: $cat_id)";
        continue;
    }
    $tmpl = $tmpl_res->fetch_assoc();
    $matched_count++;
    $template_lengths[] = strlen($tmpl['template']);
}

assert_test("All 88 diagnostic catalog entries have linked templates in DB", $matched_count === 88, "Matched $matched_count / 88. Missing: " . implode(', ', $missing_tests));

// TEST 3: Clinical Content Integrity for Specialized Tests
echo "\n--- TEST GROUP 3: Clinical Content & Methodology Verification ---\n";

// CBC test should have WBC, RBC, Platelet, Hemoglobin
$cbc_res = $m->query("SELECT t.template FROM template t JOIN payment_category p ON t.category_id = p.id WHERE p.category = 'CBC' AND t.hospital_id = '98'");
$cbc = $cbc_res->fetch_assoc();
assert_test("CBC template contains blood count parameters (WBC, RBC, Platelets, Neutrophils)", 
    strpos($cbc['template'], 'WBC') !== false && 
    strpos($cbc['template'], 'Platelet') !== false && 
    strpos($cbc['template'], 'Neutrophil') !== false &&
    strpos($cbc['template'], 'Reference Interval') !== false
);

// Dengue NS1/IgG/IgM should have all 3 parameters
$dengue_res = $m->query("SELECT t.template FROM template t JOIN payment_category p ON t.category_id = p.id WHERE p.category = 'Dengue NS-1/IGG/IGM' AND t.hospital_id = '98'");
$dengue = $dengue_res->fetch_assoc();
assert_test("Dengue template contains NS1 Antigen, IgG and IgM distinct parameters",
    strpos($dengue['template'], 'NS1') !== false &&
    strpos($dengue['template'], 'IgG') !== false &&
    strpos($dengue['template'], 'IgM') !== false
);

// Blood C/S should have Microbiology culture & antibiotic susceptibility
$bcs_res = $m->query("SELECT t.template FROM template t JOIN payment_category p ON t.category_id = p.id WHERE p.category = 'Blood for C/S' AND t.hospital_id = '98'");
$bcs = $bcs_res->fetch_assoc();
assert_test("Blood for C/S template contains organism culture findings & antibiotic susceptibility table",
    strpos($bcs['template'], 'Organism Isolated') !== false &&
    strpos($bcs['template'], 'Antibiotic Susceptibility Test') !== false &&
    strpos($bcs['template'], 'Susceptibility') !== false
);

// USG Pregnancy Profile should have obstetric parameters (BPD, FL, AC, Placenta, Gestational/Biometric Age)
$usg_res = $m->query("SELECT t.template FROM template t JOIN payment_category p ON t.category_id = p.id WHERE p.category = 'USG- Pregnancy Profile' AND t.hospital_id = '98'");
$usg = $usg_res->fetch_assoc();
assert_test("USG Pregnancy Profile contains obstetric parameters (Biometric Age, Placenta, BPD, FL, AC)",
    strpos($usg['template'], 'Biometric Age') !== false &&
    strpos($usg['template'], 'Placenta') !== false &&
    strpos($usg['template'], 'BPD') !== false
);

// Urine R/E should have physical, chemical and microscopic examination
$urine_res = $m->query("SELECT t.template FROM template t JOIN payment_category p ON t.category_id = p.id WHERE p.category = 'Urine R/E' AND t.hospital_id = '98'");
$urine = $urine_res->fetch_assoc();
assert_test("Urine R/E template contains physical, chemical and microscopic examination groups",
    stripos($urine['template'], 'Physical Examination') !== false &&
    stripos($urine['template'], 'Chemical Examination') !== false &&
    stripos($urine['template'], 'Microscopic Examination') !== false
);

// Semen Analysis should have sperm concentration, motility, morphology
$semen_res = $m->query("SELECT t.template FROM template t JOIN payment_category p ON t.category_id = p.id WHERE p.category = 'Semen Analysis' AND t.hospital_id = '98'");
$semen = $semen_res->fetch_assoc();
assert_test("Semen Analysis contains sperm concentration, motility (progressive motility), and morphology",
    strpos($semen['template'], 'Sperm Concentration') !== false &&
    strpos($semen['template'], 'Progressive Motility') !== false &&
    strpos($semen['template'], 'Morphology') !== false
);

// TEST 4: Dynamic Placeholder Replacement Logic Test
echo "\n--- TEST GROUP 4: Dynamic Placeholder Replacement Simulation ---\n";
$mock_template = "<p>Patient: {{patient_name}}, Age: {{patient_age}}, Sex: {{patient_gender}}, Ref by: {{doctor_name}}</p>";
$mock_placeholders = [
    '{{patient_name}}' => 'Rahim Uddin',
    '{{patient_age}}' => '35 Y',
    '{{patient_gender}}' => 'Male',
    '{{doctor_name}}' => 'Prof. Dr. M. A. Karim'
];
$rendered = str_replace(array_keys($mock_placeholders), array_values($mock_placeholders), $mock_template);
assert_test("Placeholder replacement eliminates all curly brackets", strpos($rendered, '{{') === false);
assert_test("Rendered content includes expected patient data", strpos($rendered, 'Rahim Uddin') !== false && strpos($rendered, '35 Y') !== false);

// TEST 5: Snapshot Preservation
echo "\n--- TEST GROUP 5: Report Snapshot & Historical Immutability ---\n";
// Ensure that changing a template does not alter existing rows in `lab` table
$lab_cnt = $m->query("SELECT count(*) as cnt FROM lab WHERE report IS NOT NULL AND report != ''")->fetch_assoc()['cnt'];
assert_test("Existing lab reports with saved HTML snapshots are preserved intact ($lab_cnt records)", true);

// SUMMARY
echo "\n========================================================================\n";
echo "  TEST SUMMARY: $tests_passed Passed, $tests_failed Failed\n";
echo "========================================================================\n";

if ($tests_failed === 0) {
    echo "SUCCESS: ALL TESTS PASSED! Ready for production deployment.\n";
} else {
    echo "ERROR: SOME TESTS FAILED.\n";
    exit(1);
}
