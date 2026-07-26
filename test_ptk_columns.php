<?php
define('BASEPATH', TRUE);
require_once __DIR__ . '/application/config/database.php';

$db_cfg = $db['default'];
$mysqli = new mysqli($db_cfg['hostname'], $db_cfg['username'], $db_cfg['password'], $db_cfg['database']);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== COLUMNS OF ptk ===\n";
$res = $mysqli->query("SHOW COLUMNS FROM ptk");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        echo "{$row['Field']} => {$row['Type']}\n";
    }
} else {
    echo "Table ptk does not exist or query error: " . $mysqli->error . "\n";
}
