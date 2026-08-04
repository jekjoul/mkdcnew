@echo off
title MKDC Scanner Bridge v2.0 - Desktop Launcher
echo =========================================
echo  MKDC Scanner Bridge - Desktop Control
echo =========================================
echo.

set "APP_DIR=%~dp0"
cd /d "%APP_DIR%"

:: 1. Jika file .exe belum ada, coba kompilasi otomatis
if not exist "%APP_DIR%MKDC_Scanner_Bridge.exe" (
    echo [INFO] Menyiapkan aplikasi desktop MKDC_Scanner_Bridge.exe...
    call "%APP_DIR%build_app.bat"
)

:: 2. Jalankan aplikasi GUI desktop
if exist "%APP_DIR%MKDC_Scanner_Bridge.exe" (
    echo [OK] Membuka Aplikasi Desktop MKDC Scanner Bridge...
    start "" "%APP_DIR%MKDC_Scanner_Bridge.exe"
    exit /b 0
)

:: 3. Fallback jika .exe tidak ada: Jalankan via PowerShell GUI
if exist "%APP_DIR%scanner_gui.ps1" (
    echo [OK] Membuka Aplikasi Desktop Scanner Bridge via PowerShell GUI...
    powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File "%APP_DIR%scanner_gui.ps1"
    exit /b 0
)

:: 4. Fallback terakhir: Jalankan CLI index.js
echo [!] Fallback: Menjalankan mode console...
node index.js
pause
