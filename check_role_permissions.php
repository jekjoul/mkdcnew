<?php
header('Content-Type: text/plain');

$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'mkdcnew';

$link = mysqli_connect($host, $user, $pass, $db_name);
if (!$link) {
    die("Connection failed");
}

echo "=== SHOW COLUMNS FROM role_permissions ===\n";
$res = mysqli_query($link, "SHOW COLUMNS FROM role_permissions");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "\n=== ALL ROLE PERMISSIONS WITH CODE LIKE '%perangkat%' ===\n";
$res = mysqli_query($link, "SELECT * FROM role_permissions WHERE permission LIKE '%perangkat%'");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

mysqli_close($link);
