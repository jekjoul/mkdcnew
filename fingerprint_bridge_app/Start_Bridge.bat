@echo off
title Fingerprint Web Desktop Bridge Launcher
chcp 65001 >nul
cls

echo ===================================================================
echo     FINGERPRINT EASYLINK WEB DESKTOP BRIDGE v2.0 (STANDALONE)
echo ===================================================================
echo.

set PORT=8088
set APP_URL=http://127.0.0.1:%PORT%/index.php

rem 1. Cari PHP Executable
set PHP_BIN=
if exist "C:\xampp\php\php.exe" set PHP_BIN=C:\xampp\php\php.exe
if not defined PHP_BIN if exist "..\php\php.exe" set PHP_BIN=..\php\php.exe
if not defined PHP_BIN (
    where php >nul 2>&1
    if not errorlevel 1 set PHP_BIN=php
)

if not defined PHP_BIN (
    echo [ERROR] PHP executable tidak ditemukan di C:\xampp\php\php.exe atau PATH sistem!
    echo Silakan install PHP atau jalankan XAMPP terlebih dahulu.
    echo.
    pause
    exit /b 1
)

rem 2. Jalankan PHP Built-in Web Server di Background
echo [1/2] Memulai Server Web Standalone pada port %PORT%...
start "Fingerprint_Bridge_Server" /B "%PHP_BIN%" -S 127.0.0.1:%PORT% -t "%~dp0." "%~dp0router.php" >nul 2>&1

ping 127.0.0.1 -n 3 >nul

rem 3. Cari Browser (MS Edge / Chrome) untuk Mode App tanpa Address Bar
set BROWSER_BIN=

if exist "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe" set BROWSER_BIN=C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe
if not defined BROWSER_BIN if exist "C:\Program Files\Microsoft\Edge\Application\msedge.exe" set BROWSER_BIN=C:\Program Files\Microsoft\Edge\Application\msedge.exe
if not defined BROWSER_BIN if exist "C:\Program Files\Google\Chrome\Application\chrome.exe" set BROWSER_BIN=C:\Program Files\Google\Chrome\Application\chrome.exe
if not defined BROWSER_BIN if exist "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" set BROWSER_BIN=C:\Program Files (x86)\Google\Chrome\Application\chrome.exe
if not defined BROWSER_BIN if exist "%LOCALAPPDATA%\Google\Chrome\Application\chrome.exe" set BROWSER_BIN=%LOCALAPPDATA%\Google\Chrome\Application\chrome.exe

echo [2/2] Membuka Aplikasi Desktop (Mode App Standalone tanpa URL Bar)...

if defined BROWSER_BIN goto launch_browser
start %APP_URL%
goto print_success

:launch_browser
start "" "%BROWSER_BIN%" --app=%APP_URL% --name="Fingerprint Bridge App" --user-data-dir="%~dp0data\browser_profile"

:print_success
echo.
echo ===================================================================
echo  Aplikasi Fingerprint Bridge Berjalan di Port %PORT%!
echo  Jendela browser dibuka dalam Mode App (Tanpa Address/URL Bar).
echo ===================================================================
echo.
