<?php
$cscPath = "C:\\Windows\\Microsoft.NET\\Framework64\\v4.0.30319\\csc.exe";
if (!file_exists($cscPath)) {
    $cscPath = "C:\\Windows\\Microsoft.NET\\Framework\\v4.0.30319\\csc.exe";
}

$dir    = __DIR__;
$source = $dir . "\\UninstallerApp.cs";
$output = $dir . "\\Uninstall.exe";
$ico    = $dir . "\\app.ico";

if (file_exists($output)) @unlink($output);

$cmd = "\"{$cscPath}\" /target:winexe /win32icon:\"{$ico}\" /out:\"{$output}\" /r:System.dll,System.Drawing.dll,System.Windows.Forms.dll \"{$source}\" 2>&1";

echo "Mengompilasi Uninstaller App (Uninstall.exe)...\n";
echo "Command: $cmd\n\n";

exec($cmd, $outLines, $returnCode);

echo implode("\n", $outLines) . "\n";
echo "Return code: $returnCode\n";

if ($returnCode === 0 && file_exists($output)) {
    echo "\n[BERHASIL] File Uninstaller Uninstall.exe berhasil dibuat!\n";
} else {
    echo "\n[GAGAL] Kompilasi uninstaller mengalami kendala.\n";
}
