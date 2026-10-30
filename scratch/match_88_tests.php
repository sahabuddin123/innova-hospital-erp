<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);

$prompt_tests = [
    // A. Blood Sugar and Hormone Tests
    "2 Hours After Breakfast Blood Sugar (2 hrs ABF)",
    "Fasting Blood Sugar (FBS)",
    "Random Blood Sugar (RBS)",
    "Thyroxine (T4)",
    "Triiodothyronine (T3)",
    "Serum Thyroid-Stimulating Hormone (TSH)",
    "Luteinizing Hormone (LH)",
    "Follicle-Stimulating Hormone (FSH)",
    "Free Thyroxine (FT4)",

    // B. Hematology, Biochemistry, Serology
    "ESR",
    "Febrile Antigen",
    "Hb Electrophoresis",
    "S. Lipase",
    "ANA",
    "Blood for C/S",
    "Iron Profile",
    "Vitamin D-Level",
    "Semen Analysis",
    "HbA1c",
    "Pus for C/S",
    "Hbs AG Confirmatory",
    "Troponin I",
    "Testosteron",
    "Dengue NS-1/IGG/IGM",
    "S.Electrolyte",
    "S.Amylase",
    "S.IGE (Level)",
    "Prothombin Time",
    "S.PROLACTIN",
    "Urine C/S",
    "Urine P/T",
    "Urine R/E",
    "OBT",
    "Stool C/S",
    "Stool R/E",
    "Alkaline phosphatase",
    "LFT",
    "PBF",
    "Hb%",
    "S. Albumin",
    "Sputum for AFB",
    "Tuberculin Test (MT)",
    "CRP",
    "Blood Grouping",
    "TPHA",
    "VDRL",
    "ASO Titre",
    "Widal Test",
    "R/A Factor",
    "S. Uric Acid",
    "S. Creatinine",
    "Blood Urea",
    "Lipid Profile",
    "S. Cholesterol",
    "S. Calcium",
    "SGPT",
    "SGOT",
    "HBS Ag (Screening)",
    "S. Bilirubin",
    "OGTT",
    "BT, CT",
    "C/E Count/Platelet Count",
    "ICT for Malaria",
    "M.P",
    "CBC",

    // C. Imaging, Radiology and Diagnostic Procedures
    "Echocardiography (2D & Color)",
    "Nebulization",
    "ECG",
    "USG- KUB",
    "USG-HBS",
    "USG- Pregnancy Profile",
    "USG- Lower Abdomen",
    "USG- Upper Abdomen",
    "USG- Whole Abdomen",
    "Leg B/V",
    "Hand B/V",
    "Pelvis A/P",
    "Dorsal Spine B/V",
    "L/S Spine B/V",
    "Abdomen E/P",
    "KUB",
    "Cervical Spine B/V",
    "Neck B/V",
    "PNS O/M View",
    "Skull B/V",
    "Chest Lateral View",
    "Chest A/P",
    "Chest P/A"
];

$matched = [];
$missing = [];

foreach ($prompt_tests as $idx => $test_name) {
    // Exact or trimmed match
    $escaped = $conn->real_escape_string($test_name);
    $q = $conn->query("SELECT id, category, code, c_price FROM payment_category WHERE category = '$escaped' AND hospital_id = 98 LIMIT 1");
    if ($q && $q->num_rows > 0) {
        $row = $q->fetch_assoc();
        $matched[] = ['num' => $idx + 1, 'name' => $test_name, 'db_id' => $row['id'], 'code' => $row['code'], 'price' => $row['c_price']];
    } else {
        // Try like search
        $q2 = $conn->query("SELECT id, category, code, c_price FROM payment_category WHERE category LIKE '%$escaped%' AND hospital_id = 98 LIMIT 1");
        if ($q2 && $q2->num_rows > 0) {
            $row = $q2->fetch_assoc();
            $matched[] = ['num' => $idx + 1, 'name' => $test_name, 'db_id' => $row['id'], 'code' => $row['code'], 'price' => $row['c_price']];
        } else {
            $missing[] = ['num' => $idx + 1, 'name' => $test_name];
        }
    }
}

echo "Matched: " . count($matched) . " / " . count($prompt_tests) . "\n";
if (!empty($missing)) {
    echo "Missing:\n";
    print_r($missing);
}
foreach ($matched as $m) {
    echo "{$m['num']}. {$m['name']} -> DB ID: {$m['db_id']}, Code: '{$m['code']}', Price: {$m['price']}\n";
}
