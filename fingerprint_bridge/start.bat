@echo off
title MKDC Fingerprint Bridge Service
echo Memulai MKDC Fingerprint Bridge...
echo.

:: 1. Cek apakah Node.js terinstall
where node >nul 2>nul
if %errorlevel% equ 0 (
    echo [INFO] Menggunakan Node.js runtime untuk menjalankan bridge...
    node bridge.js
    goto end
)

:: 2. Cek apakah PHP XAMPP bawaan terinstall di folder default
if exist "C:\xampp\php\php.exe" (
    echo [INFO] Node.js tidak terdeteksi. Menggunakan PHP XAMPP (C:\xampp\php\php.exe) untuk menjalankan bridge...
    "C:\xampp\php\php.exe" bridge.php
    goto end
)

:: 3. Cek apakah PHP terdaftar di environment path
where php >nul 2>nul
if %errorlevel% equ 0 (
    echo [INFO] Node.js tidak terdeteksi. Menggunakan PHP runtime global untuk menjalankan bridge...
    php bridge.php
    goto end
)

:: 4. Jika keduanya tidak ditemukan
echo [ERROR] Gagal menjalankan bridge!
echo PC sekolah ini belum menginstal Node.js dan path PHP XAMPP (C:\xampp\php\php.exe) tidak ditemukan.
echo Silakan install Node.js terlebih dahulu atau pastikan file PHP XAMPP berada di lokasi default.
echo.
pause

:end
pause
