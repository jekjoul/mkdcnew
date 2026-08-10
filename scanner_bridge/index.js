const http = require('http');
const { exec, execFile } = require('child_process');
const fs = require('fs');
const path = require('path');
const os = require('os');

const PORT = 7999;

// --------------------------------------------------------------------------
// Cari path powershell.exe yang valid (mendukung Windows tanpa powershell di PATH)
// --------------------------------------------------------------------------
const PS_CANDIDATES = [
    'C:\\Windows\\System32\\WindowsPowerShell\\v1.0\\powershell.exe',
    'C:\\Windows\\SysWOW64\\WindowsPowerShell\\v1.0\\powershell.exe',
    'C:\\Program Files\\PowerShell\\7\\pwsh.exe',
    'C:\\Program Files (x86)\\PowerShell\\7\\pwsh.exe',
];

let resolvedPS = null;
function findPowerShell() {
    if (resolvedPS) return resolvedPS;
    for (const p of PS_CANDIDATES) {
        if (fs.existsSync(p)) {
            resolvedPS = p;
            console.log('[Scanner Bridge] PowerShell ditemukan di:', p);
            return p;
        }
    }
    return null;
}

// --------------------------------------------------------------------------
// Jalankan perintah PowerShell menggunakan path absolut
// --------------------------------------------------------------------------
function runPowerShell(script) {
    return new Promise((resolve, reject) => {
        const psPath = findPowerShell();
        if (!psPath) {
            return reject('PowerShell tidak ditemukan di sistem ini. Pastikan Windows PowerShell terinstal.');
        }

        // Tulis script ke file temp agar aman dari masalah escaping
        const tmpFile = path.join(os.tmpdir(), `mkdc_scan_${Date.now()}.ps1`);
        fs.writeFileSync(tmpFile, script, 'utf8');

        const args = [
            '-NoProfile',
            '-NonInteractive',
            '-ExecutionPolicy', 'Bypass',
            '-File', tmpFile
        ];

        execFile(psPath, args, { maxBuffer: 1024 * 1024 * 20 }, (error, stdout, stderr) => {
            // Hapus file temp
            try { fs.unlinkSync(tmpFile); } catch (e) {}

            if (error) {
                reject(error.message + '\n' + stderr);
            } else {
                resolve(stdout.trim());
            }
        });
    });
}

// --------------------------------------------------------------------------
// Server HTTP
// --------------------------------------------------------------------------
const server = http.createServer(async (req, res) => {
    // Enable CORS & Private Network Access untuk web application yang memanggil local API ini
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Private-Network');
    res.setHeader('Access-Control-Allow-Private-Network', 'true');

    if (req.method === 'OPTIONS') {
        res.writeHead(204);
        res.end();
        return;
    }

    // GET / — Cek status bridge
    if (req.url === '/' && req.method === 'GET') {
        const psPath = findPowerShell();
        let defaultDeviceId = '';
        let defaultDeviceName = '';
        try {
            const configPath = path.join(__dirname, 'config.json');
            if (fs.existsSync(configPath)) {
                const conf = JSON.parse(fs.readFileSync(configPath, 'utf8'));
                defaultDeviceId = conf.defaultDeviceId || '';
                defaultDeviceName = conf.defaultDeviceName || '';
            }
        } catch (e) {}

        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            status: 'running',
            service: 'MKDC Scanner Bridge',
            version: '2.0.0',
            powershell: psPath || 'NOT_FOUND',
            defaultDeviceId,
            defaultDeviceName
        }));
        return;
    }

    // GET /devices — Daftar scanner WIA yang tersambung
    if (req.url === '/devices' && req.method === 'GET') {
        const psScript = `
$ErrorActionPreference = 'Stop'
try {
    $deviceManager = New-Object -ComObject WIA.DeviceManager
    $devices = @()
    foreach ($info in $deviceManager.DeviceInfos) {
        # Type 1 = Scanner
        if ($info.Type -eq 1) {
            $devId   = ''
            $devName = 'Scanner Tidak Diketahui'
            $devDesc = 'WIA Scanner'
            try { $devId   = $info.DeviceID } catch {}
            try { $devName = $info.Properties.Item('Name').Value } catch {}
            try { $devDesc = $info.Properties.Item('Description').Value } catch {
                try { $devDesc = $info.Properties.Item('Manufacturer').Value } catch {}
            }
            $devices += [PSCustomObject]@{
                id          = $devId
                name        = $devName
                description = $devDesc
            }
        }
    }
    if ($devices.Count -gt 0) {
        $devices | ConvertTo-Json -Compress
    } else {
        '[]'
    }
} catch {
    Write-Error $_.Exception.Message
    exit 1
}
`;

        try {
            const result = await runPowerShell(psScript);
            // Pastikan result adalah JSON valid
            let parsed;
            try {
                parsed = JSON.parse(result || '[]');
            } catch (e) {
                parsed = [];
            }
            // Jika objek tunggal (bukan array), bungkus dalam array
            if (parsed && !Array.isArray(parsed)) {
                parsed = [parsed];
            }
            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify(parsed));
        } catch (err) {
            res.writeHead(500, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({ error: 'Gagal mendaftar scanner', details: err.toString() }));
        }
        return;
    }

    // POST /scan — Mulai scan dari perangkat yang dipilih
    if (req.url === '/scan' && req.method === 'POST') {
        let body = '';
        req.on('data', chunk => { body += chunk.toString(); });
        req.on('end', async () => {
            let params = {};
            try { if (body) params = JSON.parse(body); } catch (e) {}

            let defaultDeviceId = '';
            try {
                const configPath = path.join(__dirname, 'config.json');
                if (fs.existsSync(configPath)) {
                    const conf = JSON.parse(fs.readFileSync(configPath, 'utf8'));
                    defaultDeviceId = conf.defaultDeviceId || '';
                }
            } catch (e) {}

            const deviceId = params.deviceId || defaultDeviceId || '';
            const format   = params.format === 'png' ? 'png' : 'jpg';

            // GUID Format WIA:
            // JPEG: {B96B3CAE-0728-11D3-9D7B-0000F81EF32E}
            // PNG:  {B96B3CAF-0728-11D3-9D7B-0000F81EF32E}
            const formatGuid = format === 'png'
                ? '{B96B3CAF-0728-11D3-9D7B-0000F81EF32E}'
                : '{B96B3CAE-0728-11D3-9D7B-0000F81EF32E}';

            const safeDeviceId = deviceId.replace(/"/g, '').replace(/'/g, '');

            const psScript = `
$ErrorActionPreference = 'Stop'
try {
    Add-Type -AssemblyName System.IO
    $deviceManager = New-Object -ComObject WIA.DeviceManager
    $scannerInfo = $null

    $targetId = '${safeDeviceId}'
    if ($targetId -ne '') {
        foreach ($info in $deviceManager.DeviceInfos) {
            if ($info.DeviceID -eq $targetId) {
                $scannerInfo = $info
                break
            }
        }
    }
    # Fallback ke scanner pertama yang ditemukan
    if (-not $scannerInfo) {
        foreach ($info in $deviceManager.DeviceInfos) {
            if ($info.Type -eq 1) {
                $scannerInfo = $info
                break
            }
        }
    }

    if (-not $scannerInfo) {
        Write-Error 'Tidak ada scanner yang ditemukan.'
        exit 1
    }

    $device = $scannerInfo.Connect()
    $item   = $device.Items.Item(1)

    # Konfigurasi ukuran A4 pada 150 DPI
    $dpi = 150
    try {
        $item.Properties.Item('6147').Value = $dpi           # X DPI
        $item.Properties.Item('6148').Value = $dpi           # Y DPI
        $item.Properties.Item('6149').Value = 0              # X pos
        $item.Properties.Item('6150').Value = 0              # Y pos
        $item.Properties.Item('6151').Value = [int](8.27 * $dpi)  # Width A4
        $item.Properties.Item('6152').Value = [int](11.69 * $dpi) # Height A4
    } catch {
        # Abaikan jika driver tidak mendukung pengaturan ukuran
    }

    $fmtGuid = '${formatGuid}'
    $image   = $item.Transfer($fmtGuid)

    $tempPath = [System.IO.Path]::Combine([System.IO.Path]::GetTempPath(), "mkdc_scan_$(Get-Date -Format 'yyyyMMddHHmmss').${format}")
    $image.SaveFile($tempPath)

    $bytes  = [System.IO.File]::ReadAllBytes($tempPath)
    $base64 = [System.Convert]::ToBase64String($bytes)
    Remove-Item $tempPath -Force -ErrorAction SilentlyContinue

    Write-Output $base64
} catch {
    Write-Error $_.Exception.Message
    exit 1
}
`;

            try {
                const base64Data = await runPowerShell(psScript);
                if (!base64Data) {
                    throw new Error('Tidak ada data yang dikembalikan dari scanner.');
                }
                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({
                    success: true,
                    format:  format,
                    mime:    `image/${format === 'jpg' ? 'jpeg' : 'png'}`,
                    base64:  base64Data
                }));
            } catch (err) {
                res.writeHead(500, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ error: 'Proses scan gagal', details: err.toString() }));
            }
        });
        return;
    }

    // 404 default
    res.writeHead(404, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ error: 'Endpoint tidak ditemukan' }));
});

server.listen(PORT, '127.0.0.1', () => {
    const psPath = findPowerShell();
    const timeStr = new Date().toLocaleTimeString();
    console.log(`[${timeStr}] =========================================`);
    console.log(`[${timeStr}] MKDC Scanner Bridge Server Berjalan!`);
    console.log(`[${timeStr}] URL: http://localhost:${PORT}`);
    console.log(`[${timeStr}] PowerShell: ${psPath || 'TIDAK DITEMUKAN!'}`);
    console.log(`[${timeStr}] =========================================`);
    if (!psPath) {
        console.warn(`[${timeStr}] [PERINGATAN] PowerShell tidak ditemukan. Fitur scan tidak akan berfungsi.`);
    }
});
