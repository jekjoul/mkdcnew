<?php
/**
 * Router untuk Built-in Web Server Standalone (PHP -S)
 * Aplikasi Fingerprint Web Desktop Bridge v2.0
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$filePath = __DIR__ . $uri;

// 1. Jika file statis ada (CSS, JS, gambar, font, JSON), sajikan langsung
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// 2. Jika meminta root atau index.php, panggil index.php
if ($uri === '/' || $uri === '/index.php') {
    require_once __DIR__ . '/index.php';
    exit;
}

// 3. Jika meminta file PHP lain (misal ajax.php, sinkronisasi.php)
if (file_exists($filePath)) {
    require_once $filePath;
    exit;
}

// 4. Default fallback ke index.php
require_once __DIR__ . '/index.php';
