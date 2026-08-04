@echo off
title MKDC Scanner Bridge - Compiler
echo =========================================
echo  Mengompilasi MKDC Scanner Bridge App...
echo =========================================
echo.

set "CSC_EXE="
if exist "C:\Windows\Microsoft.NET\Framework64\v4.0.30319\csc.exe" set "CSC_EXE=C:\Windows\Microsoft.NET\Framework64\v4.0.30319\csc.exe"
if "%CSC_EXE%"=="" if exist "C:\Windows\Microsoft.NET\Framework\v4.0.30319\csc.exe" set "CSC_EXE=C:\Windows\Microsoft.NET\Framework\v4.0.30319\csc.exe"

if "%CSC_EXE%"=="" (
    for /f "delims=" %%C in ('where csc.exe 2^>nul') do (
        set "CSC_EXE=%%C"
        goto :done_find_csc
    )
)
:done_find_csc

if "%CSC_EXE%"=="" (
    echo [ERROR] C# Compiler (csc.exe) tidak ditemukan!
    echo         Pastikan .NET Framework v4.0 terpasang di Windows.
    pause
    exit /b 1
)

echo [OK] Compiler: %CSC_EXE%

echo [1/4] Membuat Icon Win32 (make_icon.php)...
if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" "%~dp0make_icon.php"
)

echo [2/4] Mengompilasi ScannerBridgeApp.cs -> MKDC_Scanner_Bridge.exe...
"%CSC_EXE%" /target:winexe /win32icon:"%~dp0app.ico" /out:"%~dp0MKDC_Scanner_Bridge.exe" /r:System.dll,System.Drawing.dll,System.Windows.Forms.dll "%~dp0ScannerBridgeApp.cs"

echo [3/4] Mengompilasi UninstallerApp.cs -> Uninstall.exe...
"%CSC_EXE%" /target:winexe /win32icon:"%~dp0app.ico" /out:"%~dp0Uninstall.exe" /r:System.dll,System.Drawing.dll,System.Windows.Forms.dll "%~dp0UninstallerApp.cs"

echo [4/4] Mengompilasi InstallerApp.cs -> MKDC_Scanner_Bridge_Setup.exe...
"%CSC_EXE%" /target:winexe /win32icon:"%~dp0app.ico" /out:"%~dp0MKDC_Scanner_Bridge_Setup.exe" /r:System.dll,System.Drawing.dll,System.Windows.Forms.dll "%~dp0InstallerApp.cs"

if %errorlevel% equ 0 (
    echo.
    echo ========================================================
    echo  [BERHASIL] Seluruh paket MKDC Scanner Bridge dibuat!
    echo   - MKDC_Scanner_Bridge.exe (Aplikasi Utama)
    echo   - Uninstall.exe (Aplikasi Uninstaller)
    echo   - MKDC_Scanner_Bridge_Setup.exe (Installer Setup)
    echo ========================================================
    timeout /t 3 >nul
) else (
    echo.
    echo [ERROR] Kompilasi gagal! Periksa pesan kesalahan di atas.
    pause
)
