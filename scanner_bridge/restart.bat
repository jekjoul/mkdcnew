@echo off
title MKDC Scanner Bridge - Restart
echo =========================================
echo  Menghentikan proses Node.js lama...
echo =========================================

taskkill /F /IM node.exe /T 2>nul
if %errorlevel% equ 0 (
    echo [OK] Proses lama dihentikan.
) else (
    echo [--] Tidak ada proses node.exe sebelumnya.
)

timeout /t 2 /nobreak >nul
echo.

:: ---- Cari node.exe ----
set "NODE_EXE="

if exist "C:\Program Files\nodejs\node.exe"           set "NODE_EXE=C:\Program Files\nodejs\node.exe"
if exist "C:\Program Files (x86)\nodejs\node.exe"     set "NODE_EXE=C:\Program Files (x86)\nodejs\node.exe"
if exist "%LOCALAPPDATA%\Programs\nodejs\node.exe"    set "NODE_EXE=%LOCALAPPDATA%\Programs\nodejs\node.exe"
if exist "%APPDATA%\nvm\node.exe"                     set "NODE_EXE=%APPDATA%\nvm\node.exe"

if "%NODE_EXE%"=="" (
    for /f "delims=" %%N in ('where node.exe 2^>nul') do (
        set "NODE_EXE=%%N"
        goto :done_find
    )
)
:done_find

if "%NODE_EXE%"=="" (
    echo [ERROR] Node.js tidak ditemukan!
    echo         Pastikan Node.js terinstal di C:\Program Files\nodejs\
    pause
    exit /b 1
)

echo [OK] Node.js  : %NODE_EXE%

if exist "%SystemRoot%\System32\WindowsPowerShell\v1.0\powershell.exe" (
    echo [OK] PowerShell tersedia.
) else (
    echo [!]  PowerShell tidak ditemukan.
)

echo.
echo [INFO] Bridge berjalan di http://localhost:7999
echo [INFO] Tekan Ctrl+C untuk menghentikan.
echo.

"%NODE_EXE%" "%~dp0index.js"
pause
