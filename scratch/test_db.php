<?php
foreach ([3306, 3307, 3308] as $port) {
    try {
        $m = @new mysqli('127.0.0.1', 'root', '', '', $port);
        if (!$m->connect_error) {
            echo "Connected to port $port!\n";
            $res = $m->query('SHOW DATABASES');
            while ($r = $res->fetch_array()) {
                echo "  $r[0]\n";
            }
        }
    } catch (Exception $e) {
    }
}
