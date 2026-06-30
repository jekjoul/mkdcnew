const http = require('http');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

const PORT = 7999;

// Helper to run PowerShell commands safely
function runPowerShell(script) {
    return new Promise((resolve, reject) => {
        // Encode the script in base64 to avoid escaping issues on command line
        const buffer = Buffer.from(script, 'utf16le');
        const base64 = buffer.toString('base64');
        const command = `powershell -NoProfile -NonInteractive -EncodedCommand ${base64}`;

        exec(command, { maxBuffer: 1024 * 1024 * 10 }, (error, stdout, stderr) => {
            if (error) {
                reject(error.message + '\n' + stderr);
            } else {
                resolve(stdout.trim());
            }
        });
    });
}

const server = http.createServer(async (req, res) => {
    // Enable CORS for web applications calling this local api
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    if (req.method === 'OPTIONS') {
        res.writeHead(204);
        res.end();
        return;
    }

    if (req.url === '/' && req.method === 'GET') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ status: 'running', service: 'MKDC Scanner Bridge', version: '1.0.0' }));
        return;
    }

    // Endpoint: GET /devices
    // Lists WIA scanning devices connected to the PC
    if (req.url === '/devices' && req.method === 'GET') {
        const psScript = `
            $deviceManager = New-Object -ComObject WIA.DeviceManager
            $devices = @()
            foreach ($info in $deviceManager.DeviceInfos) {
                if ($info.Type -eq 1) { # Scanner type
                    $devices += [PSCustomObject]@{
                        id = $info.DeviceID
                        name = $info.Properties.Item("Name").Value
                        description = $info.Properties.Item("Description").Value
                    }
                }
            }
            if ($devices.Count -gt 0) {
                $devices | ConvertTo-Json -Compress
            } else {
                "[]"
            }
        `;

        try {
            const result = await runPowerShell(psScript);
            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(result || '[]');
        } catch (err) {
            res.writeHead(500, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify({ error: 'Failed to list scanners', details: err.toString() }));
        }
        return;
    }

    // Endpoint: POST /scan
    // Initiates scan from the selected device (or first available)
    if (req.url === '/scan' && req.method === 'POST') {
        let body = '';
        req.on('data', chunk => {
            body += chunk.toString();
        });
        req.on('end', async () => {
            let params = {};
            try {
                if (body) params = JSON.parse(body);
            } catch (e) {}

            const deviceId = params.deviceId || '';
            const format = params.format === 'png' ? 'png' : 'jpg';

            // GUID for WIA Formats
            // JPEG: {B96B3CAE-0728-11D3-9D7B-0000F81EF32E}
            // PNG:  {B96B3CAF-0728-11D3-9D7B-0000F81EF32E}
            const formatGuid = format === 'png' 
                ? '{B96B3CAF-0728-11D3-9D7B-0000F81EF32E}' 
                : '{B96B3CAE-0728-11D3-9D7B-0000F81EF32E}';

            const psScript = `
                [void][System.Reflection.Assembly]::LoadWithPartialName("System.IO")
                $deviceManager = New-Object -ComObject WIA.DeviceManager
                $scannerInfo = $null
                
                if ("${deviceId}" -ne "") {
                    foreach ($info in $deviceManager.DeviceInfos) {
                        if ($info.DeviceID -eq "${deviceId}") {
                            $scannerInfo = $info
                            break
                        }
                    }
                } else {
                    foreach ($info in $deviceManager.DeviceInfos) {
                        if ($info.Type -eq 1) {
                            $scannerInfo = $info
                            break
                        }
                    }
                }

                if (-not $scannerInfo) {
                    Write-Error "No scanner device found."
                    exit 1
                }

                $device = $scannerInfo.Connect()
                $item = $device.Items.Item(1)
                
                # Setup format
                $formatGuid = "${formatGuid}"
                
                $image = $item.Transfer($formatGuid)
                
                $tempPath = [System.IO.Path]::GetTempFileName() + ".${format}"
                $image.SaveFile($tempPath)
                
                $bytes = [System.IO.File]::ReadAllBytes($tempPath)
                $base64 = [System.Convert]::ToBase64String($bytes)
                Remove-Item $tempPath -Force
                
                Write-Output $base64
            `;

            try {
                const base64Data = await runPowerShell(psScript);
                if (!base64Data) {
                    throw new Error("No data returned from scanner");
                }
                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ 
                    success: true, 
                    format: format,
                    mime: `image/${format === 'jpg' ? 'jpeg' : 'png'}`,
                    base64: base64Data 
                }));
            } catch (err) {
                res.writeHead(500, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ error: 'Scanning failed', details: err.toString() }));
            }
        });
        return;
    }

    // Default 404
    res.writeHead(404, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ error: 'Endpoint not found' }));
});

server.listen(PORT, '127.0.0.1', () => {
    console.log(`=========================================`);
    console.log(` MKDC Scanner Bridge Server is running!`);
    console.log(` URL: http://localhost:${PORT}`);
    console.log(`=========================================`);
});
