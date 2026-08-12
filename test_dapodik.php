<?php
$base_url = 'http://180.245.90.138:5774';
$endpoint = 'WebService/getSekolah';
$api_key = 'PWWR3Gm8WX0V50W';
$npsn = '69948104';

$url = $base_url . '/' . ltrim($endpoint, '/');
$separator = strpos($url, '?') === false ? '?' : '&';
$url .= $separator . http_build_query(['npsn' => $npsn]);

echo "Mengakses URL: $url\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 30,
        'ignore_errors' => true,
        'header' => "Accept: application/json\r\nAuthorization: Bearer " . $api_key . "\r\n",
    ],
]);

$content = file_get_contents($url, false, $context);
if ($content === false) {
    echo "Gagal file_get_contents!\n";
    $err = error_get_last();
    print_r($err);
} else {
    echo "Berhasil!\n";
    echo substr($content, 0, 500) . "\n";
}
