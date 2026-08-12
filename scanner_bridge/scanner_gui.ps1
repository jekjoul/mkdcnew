# MKDC Scanner Bridge - Standalone PowerShell Desktop GUI App v2.0
Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing
Add-Type -AssemblyName System.Net.Http

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $scriptDir

$PORT = 7999
$API_URL = "http://127.0.0.1:$PORT"
$global:nodeProcess = $null
$global:isServerRunning = $false

# -------------------------------------------------------------------
# Window Form Setup
# -------------------------------------------------------------------
$form = New-Object System.Windows.Forms.Form
$form.Text = "MKDC Scanner Bridge v2.0 - Desktop Control"
$form.Size = New-Object System.Drawing.Size(680, 580)
$form.MinimumSize = New-Object System.Drawing.Size(640, 520)
$form.StartPosition = "CenterScreen"
$form.BackColor = [System.Drawing.Color]::FromArgb(248, 250, 252)
$form.Font = New-Object System.Drawing.Font("Segoe UI", 9.5)

# Icon
try {
    $iconPath = Join-Path $scriptDir "app.ico"
    if (Test-Path $iconPath) {
        $form.Icon = New-Object System.Drawing.Icon($iconPath)
    } else {
        $form.Icon = [System.Drawing.SystemIcons]::Application
    }
} catch {}

# 1. Header Panel
$panelHeader = New-Object System.Windows.Forms.Panel
$panelHeader.Dock = "Top"
$panelHeader.Height = 85
$panelHeader.BackColor = [System.Drawing.Color]::FromArgb(30, 41, 59) # Slate-800
$panelHeader.Padding = New-Object System.Windows.Forms.Padding(20, 12, 20, 12)

$lblTitle = New-Object System.Windows.Forms.Label
$lblTitle.Text = "MKDC Scanner Bridge"
$lblTitle.Font = New-Object System.Drawing.Font("Segoe UI", 16, [System.Drawing.FontStyle]::Bold)
$lblTitle.ForeColor = [System.Drawing.Color]::White
$lblTitle.AutoSize = $true
$lblTitle.Location = New-Object System.Drawing.Point(18, 12)

$lblSubtitle = New-Object System.Windows.Forms.Label
$lblSubtitle.Text = "Service Bridge Scanner Dokumen Web - Port 7999"
$lblSubtitle.Font = New-Object System.Drawing.Font("Segoe UI", 9)
$lblSubtitle.ForeColor = [System.Drawing.Color]::FromArgb(148, 163, 184)
$lblSubtitle.AutoSize = $true
$lblSubtitle.Location = New-Object System.Drawing.Point(20, 44)

$lblServerBadge = New-Object System.Windows.Forms.Label
$lblServerBadge.Text = "● DITERHENTIKAN"
$lblServerBadge.Font = New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)
$lblServerBadge.ForeColor = [System.Drawing.Color]::White
$lblServerBadge.BackColor = [System.Drawing.Color]::FromArgb(239, 68, 68) # Red-500
$lblServerBadge.Size = New-Object System.Drawing.Size(160, 28)
$lblServerBadge.TextAlign = "MiddleCenter"
$lblServerBadge.Anchor = "Top, Right"
$lblServerBadge.Location = New-Object System.Drawing.Point(480, 26)

$panelHeader.Controls.Add($lblTitle)
$panelHeader.Controls.Add($lblSubtitle)
$panelHeader.Controls.Add($lblServerBadge)

# 2. Scanner Card Panel
$panelScannerCard = New-Object System.Windows.Forms.Panel
$panelScannerCard.Location = New-Object System.Drawing.Point(20, 100)
$panelScannerCard.Size = New-Object System.Drawing.Size(624, 85)
$panelScannerCard.Anchor = "Top, Left, Right"
$panelScannerCard.BackColor = [System.Drawing.Color]::White
$panelScannerCard.BorderStyle = "FixedSingle"

$lblScannerTitle = New-Object System.Windows.Forms.Label
$lblScannerTitle.Text = "Status Perangkat Scanner (WIA):"
$lblScannerTitle.Font = New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)
$lblScannerTitle.ForeColor = [System.Drawing.Color]::FromArgb(71, 85, 105)
$lblScannerTitle.AutoSize = $true
$lblScannerTitle.Location = New-Object System.Drawing.Point(12, 10)

$lblScannerStatus = New-Object System.Windows.Forms.Label
$lblScannerStatus.Text = "Memeriksa perangkat scanner..."
$lblScannerStatus.Font = New-Object System.Drawing.Font("Segoe UI", 9.5, [System.Drawing.FontStyle]::Bold)
$lblScannerStatus.ForeColor = [System.Drawing.Color]::FromArgb(217, 119, 6)
$lblScannerStatus.AutoSize = $true
$lblScannerStatus.Location = New-Object System.Drawing.Point(12, 30)

$cmbScanners = New-Object System.Windows.Forms.ComboBox
$cmbScanners.Location = New-Object System.Drawing.Point(14, 52)
$cmbScanners.Size = New-Object System.Drawing.Size(460, 25)
$cmbScanners.DropDownStyle = "DropDownList"
$cmbScanners.Enabled = $false

$btnRefreshScanner = New-Object System.Windows.Forms.Button
$btnRefreshScanner.Text = "🔄 Cek Scanner"
$btnRefreshScanner.Font = New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)
$btnRefreshScanner.BackColor = [System.Drawing.Color]::FromArgb(241, 245, 249)
$btnRefreshScanner.ForeColor = [System.Drawing.Color]::FromArgb(51, 65, 85)
$btnRefreshScanner.FlatStyle = "Flat"
$btnRefreshScanner.Size = New-Object System.Drawing.Size(120, 36)
$btnRefreshScanner.Location = New-Object System.Drawing.Point(488, 24)
$btnRefreshScanner.Anchor = "Top, Right"
$btnRefreshScanner.Cursor = [System.Windows.Forms.Cursors]::Hand

$panelScannerCard.Controls.Add($lblScannerTitle)
$panelScannerCard.Controls.Add($lblScannerStatus)
$panelScannerCard.Controls.Add($cmbScanners)
$panelScannerCard.Controls.Add($btnRefreshScanner)

# 3. Control Panel Buttons
$panelControl = New-Object System.Windows.Forms.Panel
$panelControl.Location = New-Object System.Drawing.Point(20, 195)
$panelControl.Size = New-Object System.Drawing.Size(624, 45)
$panelControl.Anchor = "Top, Left, Right"

$btnStart = New-Object System.Windows.Forms.Button
$btnStart.Text = "▶ Start Service"
$btnStart.Font = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)
$btnStart.BackColor = [System.Drawing.Color]::FromArgb(22, 163, 74) # Green-600
$btnStart.ForeColor = [System.Drawing.Color]::White
$btnStart.FlatStyle = "Flat"
$btnStart.Size = New-Object System.Drawing.Size(140, 40)
$btnStart.Location = New-Object System.Drawing.Point(0, 0)
$btnStart.Cursor = [System.Windows.Forms.Cursors]::Hand

$btnRestart = New-Object System.Windows.Forms.Button
$btnRestart.Text = "🔄 Restart Service"
$btnRestart.Font = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)
$btnRestart.BackColor = [System.Drawing.Color]::FromArgb(37, 99, 235) # Blue-600
$btnRestart.ForeColor = [System.Drawing.Color]::White
$btnRestart.FlatStyle = "Flat"
$btnRestart.Size = New-Object System.Drawing.Size(140, 40)
$btnRestart.Location = New-Object System.Drawing.Point(150, 0)
$btnRestart.Cursor = [System.Windows.Forms.Cursors]::Hand

$btnStop = New-Object System.Windows.Forms.Button
$btnStop.Text = "⏹ Stop Service"
$btnStop.Font = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)
$btnStop.BackColor = [System.Drawing.Color]::FromArgb(220, 38, 38) # Red-600
$btnStop.ForeColor = [System.Drawing.Color]::White
$btnStop.FlatStyle = "Flat"
$btnStop.Size = New-Object System.Drawing.Size(140, 40)
$btnStop.Location = New-Object System.Drawing.Point(300, 0)
$btnStop.Cursor = [System.Windows.Forms.Cursors]::Hand

$btnMinimizeTray = New-Object System.Windows.Forms.Button
$btnMinimizeTray.Text = "📥 Minimize ke Tray"
$btnMinimizeTray.Font = New-Object System.Drawing.Font("Segoe UI", 9.5)
$btnMinimizeTray.BackColor = [System.Drawing.Color]::FromArgb(226, 232, 240)
$btnMinimizeTray.ForeColor = [System.Drawing.Color]::FromArgb(30, 41, 59)
$btnMinimizeTray.FlatStyle = "Flat"
$btnMinimizeTray.Size = New-Object System.Drawing.Size(150, 40)
$btnMinimizeTray.Location = New-Object System.Drawing.Point(474, 0)
$btnMinimizeTray.Anchor = "Top, Right"
$btnMinimizeTray.Cursor = [System.Windows.Forms.Cursors]::Hand

$panelControl.Controls.Add($btnStart)
$panelControl.Controls.Add($btnRestart)
$panelControl.Controls.Add($btnStop)
$panelControl.Controls.Add($btnMinimizeTray)

# 4. Checkboxes Options
$chkAutoStart = New-Object System.Windows.Forms.CheckBox
$chkAutoStart.Text = "Mulai service otomatis saat aplikasi dibuka"
$chkAutoStart.Checked = $true
$chkAutoStart.AutoSize = $true
$chkAutoStart.Location = New-Object System.Drawing.Point(22, 248)

$chkMinimizeToTray = New-Object System.Windows.Forms.CheckBox
$chkMinimizeToTray.Text = "Minimize ke System Tray secara otomatis saat aplikasi dibuka"
$chkMinimizeToTray.Checked = $true
$chkMinimizeToTray.AutoSize = $true
$chkMinimizeToTray.Location = New-Object System.Drawing.Point(300, 248)

# 5. GroupBox Activity Log
$grpLog = New-Object System.Windows.Forms.GroupBox
$grpLog.Text = " Aktivitas & Log Service "
$grpLog.Font = New-Object System.Drawing.Font("Segoe UI", 9.5, [System.Drawing.FontStyle]::Bold)
$grpLog.ForeColor = [System.Drawing.Color]::FromArgb(51, 65, 85)
$grpLog.Location = New-Object System.Drawing.Point(20, 278)
$grpLog.Size = New-Object System.Drawing.Size(624, 237)
$grpLog.Anchor = "Top, Bottom, Left, Right"

$txtLog = New-Object System.Windows.Forms.RichTextBox
$txtLog.Dock = "Fill"
$txtLog.BackColor = [System.Drawing.Color]::FromArgb(15, 23, 42) # Slate dark
$txtLog.ForeColor = [System.Drawing.Color]::FromArgb(226, 232, 240)
$txtLog.Font = New-Object System.Drawing.Font("Consolas", 9.5)
$txtLog.ReadOnly = $true
$txtLog.BorderStyle = "None"

$btnClearLog = New-Object System.Windows.Forms.Button
$btnClearLog.Text = "🗑 Hapus Log"
$btnClearLog.Font = New-Object System.Drawing.Font("Segoe UI", 8.5)
$btnClearLog.BackColor = [System.Drawing.Color]::FromArgb(241, 245, 249)
$btnClearLog.ForeColor = [System.Drawing.Color]::FromArgb(100, 116, 139)
$btnClearLog.FlatStyle = "Flat"
$btnClearLog.Size = New-Object System.Drawing.Size(90, 24)
$btnClearLog.Location = New-Object System.Drawing.Point(524, 0)
$btnClearLog.Anchor = "Top, Right"
$btnClearLog.Cursor = [System.Windows.Forms.Cursors]::Hand
$btnClearLog.Add_Click({ $txtLog.Clear(); Log-Info "Log dibersihkan." })

$grpLog.Controls.Add($btnClearLog)
$grpLog.Controls.Add($txtLog)

$form.Controls.Add($panelHeader)
$form.Controls.Add($panelScannerCard)
$form.Controls.Add($panelControl)
$form.Controls.Add($chkAutoStart)
$form.Controls.Add($chkMinimizeToTray)
$form.Controls.Add($grpLog)

# -------------------------------------------------------------------
# Helper Logging & UI Functions
# -------------------------------------------------------------------
function Log-Append($msg, $color) {
    if ($txtLog.InvokeRequired) {
        $form.Invoke([Action[string, System.Drawing.Color]]{ param($m, $c) Log-Append $m $c }, $msg, $color)
        return
    }
    $txtLog.SelectionStart = $txtLog.TextLength
    $txtLog.SelectionLength = 0
    $txtLog.SelectionColor = $color
    $txtLog.AppendText("$msg`n")
    $txtLog.ScrollToCaret()
}

function Log-Info($msg) { Log-Append "[$((Get-Date).ToString('HH:mm:ss'))] [INFO] $msg" ([System.Drawing.Color]::FromArgb(56, 189, 248)) }
function Log-Success($msg) { Log-Append "[$((Get-Date).ToString('HH:mm:ss'))] [OK] $msg" ([System.Drawing.Color]::FromArgb(74, 222, 128)) }
function Log-Warn($msg) { Log-Append "[$((Get-Date).ToString('HH:mm:ss'))] [WARN] $msg" ([System.Drawing.Color]::FromArgb(251, 191, 36)) }
function Log-Err($msg) { Log-Append "[$((Get-Date).ToString('HH:mm:ss'))] [ERROR] $msg" ([System.Drawing.Color]::FromArgb(248, 113, 113)) }

function Update-UIState {
    if ($global:isServerRunning) {
        $lblServerBadge.Text = "● BERJALAN (PORT 7999)"
        $lblServerBadge.BackColor = [System.Drawing.Color]::FromArgb(22, 163, 74)
        $btnStart.Enabled = $false
        $btnRestart.Enabled = $true
        $btnStop.Enabled = $true
    } else {
        $lblServerBadge.Text = "● DITERHENTIKAN"
        $lblServerBadge.BackColor = [System.Drawing.Color]::FromArgb(239, 68, 68)
        $btnStart.Enabled = $true
        $btnRestart.Enabled = $false
        $btnStop.Enabled = $false
        $lblScannerStatus.Text = "🔴 Service Bridge belum berjalan"
        $lblScannerStatus.ForeColor = [System.Drawing.Color]::FromArgb(100, 116, 139)
    }
}

function Minimize-ToTray($showTip) {
    $form.Hide()
    $form.ShowInTaskbar = $false
    $notifyIcon.Visible = $true
    if ($showTip) {
        $notifyIcon.ShowBalloonTip(3000, "MKDC Scanner Bridge", "Aplikasi berjalan di System Tray dan siap menerima scan.", [System.Windows.Forms.ToolTipIcon]::Info)
    }
}

function Restore-FromTray {
    $form.Show()
    $form.WindowState = "Normal"
    $form.ShowInTaskbar = $true
    $form.Activate()
}

# -------------------------------------------------------------------
# Service Logic
# -------------------------------------------------------------------
function Find-Node {
    $paths = @(
        "C:\Program Files\nodejs\node.exe",
        "C:\Program Files (x86)\nodejs\node.exe",
        "$env:LOCALAPPDATA\Programs\nodejs\node.exe",
        "$env:APPDATA\nvm\node.exe"
    )
    foreach ($p in $paths) { if (Test-Path $p) { return $p } }
    $where = where.exe node.exe 2>$null
    if ($where) { return $where[0] }
    return $null
}

function Start-BridgeService {
    if ($global:isServerRunning) { Log-Info "Service sudah berjalan."; return }

    Log-Info "Memulai MKDC Scanner Bridge Service..."
    $nodeExe = Find-Node
    if (-not $nodeExe) {
        Log-Err "Node.js tidak ditemukan!"
        [System.Windows.Forms.MessageBox]::Show("Node.js tidak ditemukan! Silakan pasang Node.js terlebih dahulu.", "Error Node.js", "OK", "Error")
        return
    }

    $indexPath = Join-Path $scriptDir "index.js"
    try {
        $psi = New-Object System.Diagnostics.ProcessStartInfo
        $psi.FileName = $nodeExe
        $psi.Arguments = "`"$indexPath`""
        $psi.WorkingDirectory = $scriptDir
        $psi.UseShellExecute = $false
        $psi.CreateNoWindow = $true
        $psi.RedirectStandardOutput = $true
        $psi.RedirectStandardError = $true

        $global:nodeProcess = New-Object System.Diagnostics.Process
        $global:nodeProcess.StartInfo = $psi
        $global:nodeProcess.EnableRaisingEvents = $true

        $actionOut = [System.Diagnostics.DataReceivedEventHandler]{ param($s, $e) if ($e.Data) { Log-Append $e.Data ([System.Drawing.Color]::FromArgb(148, 163, 184)) } }
        $actionErr = [System.Diagnostics.DataReceivedEventHandler]{ param($s, $e) if ($e.Data) { Log-Err $e.Data } }

        $global:nodeProcess.add_OutputDataReceived($actionOut)
        $global:nodeProcess.add_ErrorDataReceived($actionErr)

        $null = $global:nodeProcess.Start()
        $global:nodeProcess.BeginOutputReadLine()
        $global:nodeProcess.BeginErrorReadLine()

        Log-Success "Service Node.js berjalan (PID: $($global:nodeProcess.Id))."
        $global:isServerRunning = $true
        Update-UIState
        Refresh-ScannerDevices
    } catch {
        Log-Err "Gagal menjalankan service: $_"
    }
}

function Stop-BridgeService {
    Log-Info "Menghentikan service..."
    try {
        if ($global:nodeProcess -and -not $global:nodeProcess.HasExited) {
            $global:nodeProcess.Kill()
            $global:nodeProcess.Dispose()
            $global:nodeProcess = $null
        }
        cmd /c "for /f `"tokens=5`" %a in ('netstat -aon ^| findstr :$PORT') do taskkill /f /pid %a" 2>$null
        $global:isServerRunning = $false
        Update-UIState
        Log-Info "Service berhasil dihentikan."
    } catch {
        Log-Err "Error saat menghentikan service: $_"
    }
}

function Restart-BridgeService {
    Stop-BridgeService
    Start-Sleep -Seconds 1
    Start-BridgeService
}

function Save-DefaultScanner($id, $name) {
    try {
        $configPath = Join-Path $scriptDir "config.json"
        $json = @{
            defaultDeviceId = $id
            defaultDeviceName = $name
        } | ConvertTo-Json
        [System.IO.File]::WriteAllText($configPath, $json)
    } catch {
        Log-Err "Gagal menyimpan default scanner: $_"
    }
}

function Load-DefaultScannerId {
    try {
        $configPath = Join-Path $scriptDir "config.json"
        if (Test-Path $configPath) {
            $conf = Get-Content $configPath -Raw | ConvertFrom-Json
            return $conf.defaultDeviceId
        }
    } catch {}
    return ""
}

$cmbScanners.Add_SelectedIndexChanged({
    if ($cmbScanners.SelectedItem -ne $null) {
        $selected = $cmbScanners.SelectedItem
        $savedDefaultId = Load-DefaultScannerId
        if ($selected.Id -ne $savedDefaultId) {
            Save-DefaultScanner $selected.Id $selected.Name
            Log-Info "Scanner default diubah ke: $($selected.Name)"
        }
    }
})

function Refresh-ScannerDevices {
    $lblScannerStatus.Text = "⏳ Memeriksa perangkat scanner lokal..."
    $lblScannerStatus.ForeColor = [System.Drawing.Color]::FromArgb(217, 119, 6)

    try {
        $cmbScanners.Items.Clear()
        $savedDefaultId = Load-DefaultScannerId
        $selectedIndex = -1

        $deviceManager = New-Object -ComObject WIA.DeviceManager
        $devices = @()
        
        foreach ($info in $deviceManager.DeviceInfos) {
            # Type 1 = Scanner
            if ($info.Type -eq 1) {
                $devId = $info.DeviceID
                $devName = "Scanner Tidak Diketahui"
                try { $devName = $info.Properties.Item('Name').Value } catch {}
                
                $item = [PSCustomObject]@{
                    Id = $devId
                    Name = $devName
                }
                # Custom ToString method for ComboBox display
                $item | Add-Member -MemberType ScriptMethod -Name ToString -Value { return $this.Name } -Force
                $devices += $item
            }
        }

        for ($i = 0; $i -lt $devices.Count; $i++) {
            $dev = $devices[$i]
            $null = $cmbScanners.Items.Add($dev)
            if ($dev.Id -eq $savedDefaultId) {
                $selectedIndex = $i
            }
        }

        if ($cmbScanners.Items.Count -gt 0) {
            $cmbScanners.Enabled = $true
            if ($selectedIndex -ge 0) {
                $cmbScanners.SelectedIndex = $selectedIndex
                $lblScannerStatus.Text = "🟢 AKTIF (DEFAULT): $($cmbScanners.SelectedItem.Name)"
            } else {
                $cmbScanners.SelectedIndex = 0
                $lblScannerStatus.Text = "🟢 TERHUBUNG: $($cmbScanners.SelectedItem.Name)"
                # Simpan otomatis default scanner pertama jika belum ada
                Save-DefaultScanner $cmbScanners.SelectedItem.Id $cmbScanners.SelectedItem.Name
            }
            $lblScannerStatus.ForeColor = [System.Drawing.Color]::FromArgb(22, 163, 74)
        } else {
            $cmbScanners.Enabled = $false
            $lblScannerStatus.Text = "🔴 Tidak ada scanner terhubung"
            $lblScannerStatus.ForeColor = [System.Drawing.Color]::FromArgb(220, 38, 38)
            Log-Warn "Tidak ada perangkat scanner WIA lokal terhubung."
        }
    } catch {
        $cmbScanners.Enabled = $false
        $lblScannerStatus.Text = "🔴 Gagal deteksi scanner lokal"
        $lblScannerStatus.ForeColor = [System.Drawing.Color]::FromArgb(220, 38, 38)
        Log-Err "Gagal mendeteksi scanner lokal: $_"
    }
}

# Event Listeners
$btnStart.Add_Click({ Start-BridgeService })
$btnRestart.Add_Click({ Restart-BridgeService })
$btnStop.Add_Click({ Stop-BridgeService })
$btnRefreshScanner.Add_Click({ Refresh-ScannerDevices })
$btnMinimizeTray.Add_Click({ Minimize-ToTray $false })

# System Tray
$notifyIcon = New-Object System.Windows.Forms.NotifyIcon
$notifyIcon.Text = "MKDC Scanner Bridge v2.0"
$notifyIcon.Icon = $form.Icon
$notifyIcon.Visible = $true
$notifyIcon.Add_DoubleClick({ Restore-FromTray })

$ctxMenu = New-Object System.Windows.Forms.ContextMenuStrip
$mShow = $ctxMenu.Items.Add("Tampilkan Window Utama")
$mShow.Font = New-Object System.Drawing.Font($ctxMenu.Font, [System.Drawing.FontStyle]::Bold)
$mShow.Add_Click({ Restore-FromTray })

$ctxMenu.Items.Add("-") | Out-Null
$ctxMenu.Items.Add("Start Service").Add_Click({ Start-BridgeService })
$ctxMenu.Items.Add("Restart Service").Add_Click({ Restart-BridgeService })
$ctxMenu.Items.Add("Stop Service").Add_Click({ Stop-BridgeService })
$ctxMenu.Items.Add("-") | Out-Null
$ctxMenu.Items.Add("Keluar").Add_Click({
    $notifyIcon.Visible = $false
    Stop-BridgeService
    $form.Close()
})
$notifyIcon.ContextMenuStrip = $ctxMenu

$form.Add_FormClosing({
    param($s, $e)
    if ($e.CloseReason -eq "UserClosing") {
        $res = [System.Windows.Forms.MessageBox]::Show(
            "Apakah Anda ingin menghentikan service dan keluar sepenuhnya?`n`n• [Ya] Keluar sepenuhnya.`n• [Tidak] Minimize ke System Tray.",
            "Konfirmasi Keluar", "YesNoCancel", "Question")
        if ($res -eq "No") {
            $e.Cancel = $true
            Minimize-ToTray $true
            return
        } elseif ($res -eq "Cancel") {
            $e.Cancel = $true
            return
        }
    }
    $notifyIcon.Visible = $false
    Stop-BridgeService
})

# Startup Handler
$timerInit = New-Object System.Windows.Forms.Timer
$timerInit.Interval = 500
$timerInit.Add_Tick({
    $timerInit.Stop()
    if ($chkAutoStart.Checked) { Start-BridgeService }
    if ($chkMinimizeToTray.Checked) { Minimize-ToTray $true }
})
$timerInit.Start()

[void]$form.ShowDialog()
