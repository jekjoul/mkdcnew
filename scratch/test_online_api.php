<?php
$url = 'https://datacenter.miftahulkhoer.org/api/presensi/sync';
$payload = [
    'token' => 'MKDC_FINGERPRINT_SECRET_KEY_2026',
    'logs' => [
        [
            'pin' => '2627020006',
            'scan_date' => '2026-07-26 07:15:00'
        ]
    ]
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_FOLLOWLOCATION => true
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

echo "HTTP CODE: " . $http_code . "\n";
echo "RESPONSE HEADER & BODY:\n" . $response . "\n";
if ($err) echo "CURL ERROR: " . $err . "\n";
