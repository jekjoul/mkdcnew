<?php
define('BASEPATH', TRUE);
require_once __DIR__ . '/application/config/database.php';

$db_cfg = $db['default'];
$mysqli = new mysqli($db_cfg['hostname'], $db_cfg['username'], $db_cfg['password'], $db_cfg['database']);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== COLUMNS OF presensi_harian ===\n";
$res = $mysqli->query("SHOW COLUMNS FROM presensi_harian");
while ($row = $res->fetch_assoc()) {
    echo "{$row['Field']} => {$row['Type']} (Null: {$row['Null']}, Key: {$row['Key']})\n";
}

echo "\n=== COLUMNS OF siswa ===\n";
$res2 = $mysqli->query("SHOW COLUMNS FROM siswa");
while ($row = $res2->fetch_assoc()) {
    if (in_array($row['Field'], ['id_siswa', 'nama_siswa', 'nipd', 'pin_fingerprint'])) {
        echo "{$row['Field']} => {$row['Type']} (Null: {$row['Null']}, Key: {$row['Key']})\n";
    }
}
