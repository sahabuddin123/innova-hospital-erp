<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
$lab = $conn->query('SELECT * FROM lab WHERE id = 8')->fetch_object();
echo 'Lab ID: ' . $lab->id . PHP_EOL;
echo 'Category ID: ' . $lab->category_id . PHP_EOL;
echo 'Report contains WBC CURVE: ' . (strpos($lab->report, 'WBC CURVE') !== false ? 'YES' : 'NO') . PHP_EOL;
echo 'Report contains PLT CURVE: ' . (strpos($lab->report, 'PLT CURVE') !== false ? 'YES' : 'NO') . PHP_EOL;
echo 'Report contains RBC CURVE: ' . (strpos($lab->report, 'RBC CURVE') !== false ? 'YES' : 'NO') . PHP_EOL;
echo 'Report contains RAYTO RT-7600: ' . (strpos($lab->report, 'RAYTO RT-7600') !== false ? 'YES' : 'NO') . PHP_EOL;
echo 'Report contains HAEMATOLOGY REPORT: ' . (strpos($lab->report, 'HAEMATOLOGY REPORT') !== false ? 'YES' : 'NO') . PHP_EOL;

$tmpl = $conn->query('SELECT * FROM template WHERE id = 99')->fetch_object();
echo 'Template 99 contains WBC CURVE: ' . (strpos($tmpl->template, 'WBC CURVE') !== false ? 'YES' : 'NO') . PHP_EOL;
echo 'Template 99 contains PLT CURVE: ' . (strpos($tmpl->template, 'PLT CURVE') !== false ? 'YES' : 'NO') . PHP_EOL;
echo 'Template 99 contains RBC CURVE: ' . (strpos($tmpl->template, 'RBC CURVE') !== false ? 'YES' : 'NO') . PHP_EOL;
