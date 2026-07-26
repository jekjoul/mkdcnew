@echo off
title Hentikan Server Fingerprint Bridge
chcp 65001 >nul
cls

echo ===================================================================
echo     MEMATIKAN SERVER FINGERPRINT BRIDGE STANDALONE (PORT 8088)
echo ===================================================================
echo.

for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8088" ^| findstr "LISTENING"') do (
    echo Menghentikan proses PID: %%a...
    taskkill /F /PID %%a >nul 2>&1
)

echo.
echo Server Fingerprint Bridge berhasil dihentikan.
echo.
timeout /t 3
