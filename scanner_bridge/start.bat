@echo off
title MKDC Scanner Bridge v2.0
echo =========================================
echo  MKDC Scanner Bridge
echo  Menjalankan di http://localhost:7999
echo =========================================
echo.

:: Cek apakah node tersedia
where node >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Node.js tidak ditemukan di PATH.
    echo         Silakan install Node.js dari https://nodejs.org
    echo.
    pause
    exit /b 1
)

:: Cek apakah powershell tersedia
set PS_PATH=
if exist "%SystemRoot%\System32\WindowsPowerShell\v1.0\powershell.exe" (
    set PS_PATH=%SystemRoot%\System32\WindowsPowerShell\v1.0\powershell.exe
    echo [OK] PowerShell ditemukan: %PS_PATH%
) else if exist "C:\Program Files\PowerShell\7\pwsh.exe" (
    set PS_PATH=C:\Program Files\PowerShell\7\pwsh.exe
    echo [OK] PowerShell 7 ditemukan: %PS_PATH%
) else (
    echo [PERINGATAN] PowerShell tidak ditemukan. Fitur scan mungkin tidak berfungsi.
)
echo.

node index.js
pause
