@echo off
title MKDC Scanner Bridge - Restart
echo =========================================
echo  Menghentikan proses MKDC Scanner Bridge...
echo =========================================

taskkill /F /IM MKDC_Scanner_Bridge.exe /T 2>nul
taskkill /F /IM node.exe /T 2>nul

timeout /t 1 /nobreak >nul
echo [OK] Proses lama dihentikan.
echo.

set "APP_DIR=%~dp0"
cd /d "%APP_DIR%"

if exist "%APP_DIR%MKDC_Scanner_Bridge.exe" (
    echo [OK] Membuka kembali Aplikasi Desktop MKDC Scanner Bridge...
    start "" "%APP_DIR%MKDC_Scanner_Bridge.exe"
    exit /b 0
)

if exist "%APP_DIR%scanner_gui.ps1" (
    echo [OK] Membuka kembali via PowerShell GUI...
    powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File "%APP_DIR%scanner_gui.ps1"
    exit /b 0
)

start "" "%APP_DIR%start.bat"
