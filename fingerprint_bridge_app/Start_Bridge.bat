@echo off
title Fingerprint WebDesktop Bridge App Launcher (EasyLink SDK)
color 0A

echo =========================================================
echo   FINGERPRINT WEBDESKTOP BRIDGE - STANDALONE APP
echo   EasyLink SDK & Embedded PHP Server
echo =========================================================
echo.

:: Cek keberadaan PHP di XAMPP atau PATH
set PHP_BIN=C:\xampp\php\php.exe
if not exist "%PHP_BIN%" (
    set PHP_BIN=php
)

echo [1/2] Menjalankan Server PHP Lokal di http://127.0.0.1:8088 ...
start /B "" "%PHP_BIN%" -S 127.0.0.1:8088 -t "%~dp0" >NUL 2>&1

timeout /t 2 /nobreak >NUL

echo [2/2] Membuka WebDesktop Bridge di Browser ...
start http://127.0.0.1:8088/

echo.
echo =========================================================
echo   SERVER WEBDESKTOP BERJALAN! (PORT 8088)
echo   Jangan tutup jendela console ini selama menggunakan aplikasi.
echo =========================================================
echo.
pause
