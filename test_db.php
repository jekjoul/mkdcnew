<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=mkdcnew", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully\n";
    
    // Test table columns
    $q = $pdo->query("DESCRIBE siswa");
    $columns = $q->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in 'siswa': " . implode(", ", $columns) . "\n\n";
    
    // Execute query
    $stmt = $pdo->query("SELECT pin_fingerprint as pin, nama as nama FROM siswa WHERE status_keaktifan = 'Aktif' AND pin_fingerprint > 0 ORDER BY nama ASC LIMIT 5");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Query executed successfully. Sample:\n";
    print_r($results);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
