<?php
header('Content-Type: text/plain');

$dir = __DIR__;
$dir = str_replace('/', '\\', $dir);

// Berkas-berkas yang akan dimasukkan ke dalam SFX
$files = [
    'MKDC_Scanner_Bridge.exe',
    'Uninstall.exe',
    'index.js',
    'start.bat',
    'restart.bat',
    'start_gui.vbs',
    'app.ico',
    'scanner_gui.ps1'
];

echo "1. Membaca dan mengodekan berkas ke Base64...\n";
$filesBase64 = [];
foreach ($files as $f) {
    $filePath = $dir . '\\' . $f;
    if (!file_exists($filePath)) {
        die("[ERROR] Berkas $f tidak ditemukan di $dir! Jalankan kompilasi program utama terlebih dahulu.\n");
    }
    $content = file_get_contents($filePath);
    $filesBase64[$f] = base64_encode($content);
    echo "   - $f: " . strlen($filesBase64[$f]) . " karakter Base64\n";
}

echo "\n2. Menyusun kode sumber InstallerApp_SFX.cs...\n";

// Load InstallerApp template dan ubah logikanya
$template = file_get_contents($dir . '\\InstallerApp.cs');

// Kita ganti bagian load icon di form load
$oldIconLoad = '            try
            {
                string iconPath = Path.Combine(currentDir, "app.ico");
                if (File.Exists(iconPath)) this.Icon = new Icon(iconPath);
                else this.Icon = SystemIcons.Application;
            }
            catch {}';

$newIconLoad = '            try
            {
                byte[] iconBytes = Convert.FromBase64String(EmbeddedFiles.GetFile("app.ico"));
                MemoryStream ms = new MemoryStream(iconBytes);
                this.Icon = new Icon(ms);
            }
            catch {
                this.Icon = SystemIcons.Application;
            }';

$template = str_replace($oldIconLoad, $newIconLoad, $template);

// Kita ganti bagian load logo di header panel
$oldLogoLoad = '            try
            {
                string iconPath = Path.Combine(currentDir, "app.ico");
                if (File.Exists(iconPath)) picLogo.Image = Image.FromFile(iconPath);
            }
            catch {}';

$newLogoLoad = '            try
            {
                byte[] iconBytes = Convert.FromBase64String(EmbeddedFiles.GetFile("app.ico"));
                MemoryStream ms = new MemoryStream(iconBytes);
                picLogo.Image = Image.FromStream(ms);
            }
            catch {}';

$template = str_replace($oldLogoLoad, $newLogoLoad, $template);

// Kita ganti bagian penyalinan berkas di StartInstallation
$oldFileCopy = '                string[] filesToCopy = new string[] {
                    "MKDC_Scanner_Bridge.exe",
                    "Uninstall.exe",
                    "index.js",
                    "start.bat",
                    "restart.bat",
                    "start_gui.vbs",
                    "app.ico",
                    "scanner_gui.ps1"
                };

                foreach (string f in filesToCopy)
                {
                    string src = Path.Combine(currentDir, f);
                    if (File.Exists(src))
                    {
                        string dest = Path.Combine(targetDir, f);
                        File.Copy(src, dest, true);
                    }
                }';

$newFileCopy = '                string[] filesToCopy = new string[] {
                    "MKDC_Scanner_Bridge.exe",
                    "Uninstall.exe",
                    "index.js",
                    "start.bat",
                    "restart.bat",
                    "start_gui.vbs",
                    "app.ico",
                    "scanner_gui.ps1"
                };

                foreach (string f in filesToCopy)
                {
                    string dest = Path.Combine(targetDir, f);
                    string b64 = EmbeddedFiles.GetFile(f);
                    if (!string.IsNullOrEmpty(b64))
                    {
                        byte[] bytes = Convert.FromBase64String(b64);
                        File.WriteAllBytes(dest, bytes);
                    }
                }';

$template = str_replace($oldFileCopy, $newFileCopy, $template);

// Tambahkan pustaka Base64 ke dalam Class pembantu di akhir file
$embeddedClass = "\n    public static class EmbeddedFiles\n    {\n";
$embeddedClass .= "        public static string GetFile(string fileName)\n        {\n";
$embeddedClass .= "            switch(fileName.ToLower())\n            {\n";
foreach ($filesBase64 as $name => $b64) {
    $embeddedClass .= "                case \"" . strtolower($name) . "\":\n";
    // Agar tidak melebihi batas panjang string literal C# (maks 65535 karakter), 
    // kita bagi string base64 menjadi potongan-potongan kecil yang digabungkan.
    $chunks = str_split($b64, 40000);
    $embeddedClass .= "                    return ";
    foreach ($chunks as $idx => $chunk) {
        if ($idx > 0) $embeddedClass .= " + \n                           ";
        $embeddedClass .= "\"" . $chunk . "\"";
    }
    $embeddedClass .= ";\n";
}
$embeddedClass .= "                default: return \"\";\n";
$embeddedClass .= "            }\n";
$embeddedClass .= "        }\n";
$embeddedClass .= "    }\n";

// Sisipkan di baris sebelum namespace berakhir (yaitu kurung kurawal penutup paling akhir)
$pos = strrpos($template, '}');
if ($pos !== false) {
    $template = substr_replace($template, $embeddedClass, $pos, 0);
}

// Tulis berkas CS sementara
$sfxSource = $dir . '\\InstallerApp_SFX.cs';
file_put_contents($sfxSource, $template);
echo "   - Berkas InstallerApp_SFX.cs berhasil dibuat.\n";

echo "\n3. Mengompilasi InstallerApp_SFX.cs -> MKDC_Scanner_Bridge_Setup.exe...\n";

// Matikan proses setup lama jika berjalan
exec("taskkill /f /im MKDC_Scanner_Bridge_Setup.exe 2>&1", $outKill);

$cscPath = "C:\\Windows\\Microsoft.NET\\Framework64\\v4.0.30319\\csc.exe";
if (!file_exists($cscPath)) {
    $cscPath = "C:\\Windows\\Microsoft.NET\\Framework\\v4.0.30319\\csc.exe";
}

$output = $dir . '\\MKDC_Scanner_Bridge_Setup.exe';
$ico = $dir . '\\app.ico';

// Jika setup ada, pindahkan dulu untuk bypass lock
$backup = $dir . '\\MKDC_Scanner_Bridge_Setup_old_' . time() . '.exe';
if (file_exists($output)) {
    @rename($output, $backup);
}

$cmd = "\"{$cscPath}\" /target:winexe /win32icon:\"{$ico}\" /out:\"{$output}\" /r:System.dll,System.Drawing.dll,System.Windows.Forms.dll \"{$sfxSource}\" 2>&1";
exec($cmd, $outLines, $returnCode);

echo implode("\n", $outLines) . "\n";
echo "Return code: $returnCode\n";

if ($returnCode === 0 && file_exists($output)) {
    echo "\n[BERHASIL] Single-File Installer MKDC_Scanner_Bridge_Setup.exe berhasil dibuat!\n";
    // Hapus backup lama jika kompilasi sukses
    if (file_exists($backup)) @unlink($backup);
} else {
    echo "\n[GAGAL] Kompilasi installer SFX mengalami kendala.\n";
    // Kembalikan backup jika gagal
    if (file_exists($backup)) @rename($backup, $output);
}

// Bersihkan file CS temporary
if (file_exists($sfxSource)) @unlink($sfxSource);
?>
