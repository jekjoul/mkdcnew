<?php
header('Content-Type: text/plain');

$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'mkdcnew';

$link = mysqli_connect($host, $user, $pass, $db_name);
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "=== COLUMNS OF pembelajaran_siswa ===\n";
$res = mysqli_query($link, "SHOW COLUMNS FROM `pembelajaran_siswa`");
while ($row = mysqli_fetch_assoc($res)) {
    echo "{$row['Field']}: {$row['Type']}\n";
}

echo "\n=== COUNTS IN TABLES ===\n";
foreach (['pembelajaran_siswa', 'pembelajaran', 'pembelajaran_tahun_pelajaran', 'siswa'] as $tbl) {
    $res = mysqli_query($link, "SELECT COUNT(*) FROM `$tbl`");
    $cnt = mysqli_fetch_row($res)[0];
    echo "Table `$tbl` count: $cnt\n";
}

echo "\n=== ACTIVE ACADEMIC YEARS ===\n";
$res = mysqli_query($link, "SELECT * FROM `pembelajaran_tahun_pelajaran` WHERE `status` = 'Aktif'");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "\n=== SAMPLE FROM pembelajaran_siswa ===\n";
$res = mysqli_query($link, "SELECT * FROM `pembelajaran_siswa` LIMIT 5");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

mysqli_close($link);
