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

echo "=== ALL PERMISSIONS CONTAINING 'perangkat' ===\n";
$res = mysqli_query($link, "SELECT * FROM permissions WHERE code LIKE '%perangkat%' OR name LIKE '%perangkat%'");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "\n=== ROLE PERMISSIONS FOR ADMIN (ROLE ID = 1) ===\n";
$res = mysqli_query($link, "
    SELECT p.code, p.name 
    FROM role_permissions rp
    JOIN permissions p ON p.id = rp.permission_id
    WHERE rp.role_id = 1 AND (p.code LIKE '%perangkat%' OR p.name LIKE '%perangkat%')
");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

mysqli_close($link);
