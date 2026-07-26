<?php
$mysqli = new mysqli('localhost', 'root', '', 'mkdcnew');

echo "=== PTK COLUMNS ===\n";
$res = $mysqli->query("DESCRIBE ptk");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        echo $row['Field'] . " | " . $row['Type'] . "\n";
    }
} else {
    echo "ptk table error: " . $mysqli->error . "\n";
}

echo "\n=== SISWA COLUMNS ===\n";
$res2 = $mysqli->query("DESCRIBE siswa");
if ($res2) {
    while ($row = $res2->fetch_assoc()) {
        echo $row['Field'] . " | " . $row['Type'] . "\n";
    }
} else {
    echo "siswa table error: " . $mysqli->error . "\n";
}
