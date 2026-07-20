<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/mkdcnew/index.php/test_api_db');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
if (curl_errno($ch)) {
    $output = 'cURL Error: ' . curl_error($ch);
}
curl_close($ch);

file_put_contents('c:/xampp/htdocs/mkdcnew/db_diagnostic_output.txt', $output);
echo "Diagnostic output saved!";
