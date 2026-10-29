<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'lifecare', 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "Updating template table...\n";
// Change template column to LONGTEXT if it is not already
$q = $conn->query("ALTER TABLE template MODIFY COLUMN template LONGTEXT NULL");
if ($q) {
    echo "Successfully updated template.template to LONGTEXT!\n";
} else {
    echo "Error: " . $conn->error . "\n";
}

// Add category_name column if helpful or ensure category_id is indexed
$q2 = $conn->query("SHOW INDEX FROM template WHERE Key_name = 'idx_hosp_cat'");
if ($q2->num_rows == 0) {
    $conn->query("ALTER TABLE template ADD INDEX idx_hosp_cat (hospital_id, category_id)");
    echo "Added index idx_hosp_cat!\n";
} else {
    echo "Index idx_hosp_cat already exists.\n";
}

echo "Done!\n";
