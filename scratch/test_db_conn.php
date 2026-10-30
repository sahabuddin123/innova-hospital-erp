<?php
foreach (['lifecare', 'lcare_db'] as $db) {
    $c = @new mysqli('127.0.0.1', 'root', '', $db, 3306);
    if ($c->connect_error) {
        echo "$db: error: " . $c->connect_error . "\n";
    } else {
        $res = $c->query("SHOW TABLES LIKE 'lab'");
        echo "$db: connected, lab table rows: " . $res->num_rows . "\n";
    }
}
