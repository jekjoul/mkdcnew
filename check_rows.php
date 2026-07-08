<?php
$conn = new mysqli('localhost', 'root', '', 'mkdcnew');
$res = $conn->query("SELECT * FROM jadwal_pelajaran_pengaturan");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
