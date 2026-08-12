<?php
$sourceJpg = 'C:\\Users\\ServerMK\\.gemini\\antigravity-ide\\brain\\2fd8371f-1be0-41b3-9a6a-f3aa13f337fd\\media__1785829899712.jpg';
$targetIco = 'C:\\xampp\\htdocs\\mkdcnew\\scanner_bridge\\app.ico';

if (!file_exists($sourceJpg)) {
    die("[ERROR] File gambar tidak ditemukan: $sourceJpg");
}

// 1. Resample gambar ke 256x256 PNG
$img = imagecreatefromjpeg($sourceJpg);
if (!$img) {
    die("[ERROR] Gagal membaca gambar JPEG");
}

$w = imagesx($img);
$h = imagesy($img);

$square = imagecreatetruecolor(256, 256);
imagealphablending($square, false);
imagesavealpha($square, true);

// Crop / Scale center
imagecopyresampled($square, $img, 0, 0, 0, 0, 256, 256, $w, $h);

ob_start();
imagepng($square);
$pngData = ob_get_clean();

imagedestroy($img);
imagedestroy($square);

// 2. Buat header ICO format
$pngSize = strlen($pngData);

$icoHeader = pack('v3', 0, 1, 1); // Reserved=0, Type=1(ICO), Count=1
$icoDir    = pack('CCCCvVVV',
    0,         // Width (0 = 256px)
    0,         // Height (0 = 256px)
    0,         // Color count
    0,         // Reserved
    1,         // Color planes
    32,        // Bits per pixel
    $pngSize,  // Size of PNG data
    22         // Offset (6 + 16)
);

$icoBinary = $icoHeader . $icoDir . $pngData;

file_put_contents($targetIco, $icoBinary);

echo "[BERHASIL] Icon app.ico berhasil dibuat dari gambar logo! Ukuran: " . strlen($icoBinary) . " bytes\n";
