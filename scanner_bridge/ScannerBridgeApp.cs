using System;
using System.Drawing;
using System.Diagnostics;
using System.IO;
using System.Net;
using System.Threading.Tasks;
using System.Windows.Forms;
using System.Text.RegularExpressions;

namespace MKDCScannerBridge
{
    public class MainForm : Form
    {
        // -------------------------------------------------------------------
        // UI Controls
        // -------------------------------------------------------------------
        private Panel panelHeader;
        private PictureBox picLogo;
        private Label lblTitle;
        private Label lblSubtitle;
        private Label lblServerBadge;

        private Panel panelScannerCard;
        private Label lblScannerTitle;
        private Label lblScannerStatus;
        private ComboBox cmbScanners;
        private Button btnRefreshScanner;

        private Panel panelControl;
        private Button btnStart;
        private Button btnRestart;
        private Button btnStop;
        private Button btnMinimizeTray;

        private CheckBox chkMinimizeToTray;
        private CheckBox chkAutoStart;

        private GroupBox grpLog;
        private RichTextBox txtLog;
        private Button btnClearLog;

        private NotifyIcon notifyIcon;
        private ContextMenuStrip trayContextMenu;
        private ToolStripMenuItem menuShow;
        private ToolStripMenuItem menuStart;
        private ToolStripMenuItem menuRestart;
        private ToolStripMenuItem menuStop;
        private ToolStripMenuItem menuExit;

        private Timer timerStatusCheck;

        // -------------------------------------------------------------------
        // State & Process
        // -------------------------------------------------------------------
        private Process nodeProcess = null;
        private bool isServerRunning = false;
        private string currentAppDir = "";
        private const int PORT = 7999;
        private const string API_URL = "http://127.0.0.1:7999";

        [STAThread]
        public static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new MainForm());
        }

        public MainForm()
        {
            currentAppDir = AppDomain.CurrentDomain.BaseDirectory;
            InitializeComponent();
            SetupTrayIcon();

            // Startup timer
            Timer timerStartup = new Timer();
            timerStartup.Interval = 800;
            timerStartup.Tick += (s, e) =>
            {
                timerStartup.Stop();
                timerStartup.Dispose();

                if (chkAutoStart.Checked)
                {
                    StartBridgeService();
                }
                else
                {
                    CheckServerStatus();
                }

                if (chkMinimizeToTray.Checked)
                {
                    MinimizeToTray(true);
                }
            };
            timerStartup.Start();
        }

        private void InitializeComponent()
        {
            this.Text = "MKDC Scanner Bridge v2.0 - Desktop Control";
            this.Size = new Size(680, 580);
            this.MinimumSize = new Size(640, 520);
            this.StartPosition = FormStartPosition.CenterScreen;
            this.BackColor = Color.FromArgb(248, 250, 252);
            this.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular, GraphicsUnit.Point);

            try
            {
                string iconPath = Path.Combine(currentAppDir, "app.ico");
                if (File.Exists(iconPath)) this.Icon = new Icon(iconPath);
                else this.Icon = SystemIcons.Application;
            }
            catch {}

            // 1. Header Panel
            panelHeader = new Panel();
            panelHeader.Dock = DockStyle.Top;
            panelHeader.Height = 85;
            panelHeader.BackColor = Color.FromArgb(30, 41, 59);
            panelHeader.Padding = new Padding(20, 12, 20, 12);

            picLogo = new PictureBox();
            picLogo.Size = new Size(58, 58);
            picLogo.Location = new Point(18, 13);
            picLogo.SizeMode = PictureBoxSizeMode.Zoom;
            try
            {
                string iconPath = Path.Combine(currentAppDir, "app.ico");
                if (File.Exists(iconPath)) picLogo.Image = Image.FromFile(iconPath);
            }
            catch {}

            lblTitle = new Label();
            lblTitle.Text = "MKDC Scanner Bridge";
            lblTitle.Font = new Font("Segoe UI", 16F, FontStyle.Bold);
            lblTitle.ForeColor = Color.White;
            lblTitle.AutoSize = true;
            lblTitle.Location = new Point(84, 14);

            lblSubtitle = new Label();
            lblSubtitle.Text = "Service Bridge Scanner Dokumen Web - Port 7999";
            lblSubtitle.Font = new Font("Segoe UI", 9F, FontStyle.Regular);
            lblSubtitle.ForeColor = Color.FromArgb(148, 163, 184);
            lblSubtitle.AutoSize = true;
            lblSubtitle.Location = new Point(86, 45);

            lblServerBadge = new Label();
            lblServerBadge.Text = "● DITERHENTIKAN";
            lblServerBadge.Font = new Font("Segoe UI", 9F, FontStyle.Bold);
            lblServerBadge.ForeColor = Color.White;
            lblServerBadge.BackColor = Color.FromArgb(239, 68, 68);
            lblServerBadge.AutoSize = false;
            lblServerBadge.Size = new Size(160, 28);
            lblServerBadge.TextAlign = ContentAlignment.MiddleCenter;
            lblServerBadge.Anchor = AnchorStyles.Top | AnchorStyles.Right;
            lblServerBadge.Location = new Point(480, 26);
            lblServerBadge.FlatStyle = FlatStyle.Flat;

            panelHeader.Controls.Add(picLogo);
            panelHeader.Controls.Add(lblTitle);
            panelHeader.Controls.Add(lblSubtitle);
            panelHeader.Controls.Add(lblServerBadge);

            // 2. Card Status Scanner
            panelScannerCard = new Panel();
            panelScannerCard.Location = new Point(20, 100);
            panelScannerCard.Size = new Size(624, 85);
            panelScannerCard.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right;
            panelScannerCard.BackColor = Color.White;
            panelScannerCard.BorderStyle = BorderStyle.FixedSingle;

            lblScannerTitle = new Label();
            lblScannerTitle.Text = "Status Perangkat Scanner (WIA):";
            lblScannerTitle.Font = new Font("Segoe UI", 9F, FontStyle.Bold);
            lblScannerTitle.ForeColor = Color.FromArgb(71, 85, 105);
            lblScannerTitle.AutoSize = true;
            lblScannerTitle.Location = new Point(12, 10);

            lblScannerStatus = new Label();
            lblScannerStatus.Text = "Memeriksa perangkat scanner...";
            lblScannerStatus.Font = new Font("Segoe UI", 9.5F, FontStyle.Bold);
            lblScannerStatus.ForeColor = Color.FromArgb(217, 119, 6);
            lblScannerStatus.AutoSize = true;
            lblScannerStatus.Location = new Point(12, 30);

            cmbScanners = new ComboBox();
            cmbScanners.Location = new Point(14, 52);
            cmbScanners.Size = new Size(460, 25);
            cmbScanners.DropDownStyle = ComboBoxStyle.DropDownList;
            cmbScanners.Enabled = false;
            cmbScanners.SelectedIndexChanged += CmbScanners_SelectedIndexChanged;

            btnRefreshScanner = new Button();
            btnRefreshScanner.Text = "🔄 Cek Scanner";
            btnRefreshScanner.Font = new Font("Segoe UI", 9F, FontStyle.Bold);
            btnRefreshScanner.BackColor = Color.FromArgb(241, 245, 249);
            btnRefreshScanner.ForeColor = Color.FromArgb(51, 65, 85);
            btnRefreshScanner.FlatStyle = FlatStyle.Flat;
            btnRefreshScanner.FlatAppearance.BorderSize = 1;
            btnRefreshScanner.FlatAppearance.BorderColor = Color.FromArgb(203, 213, 225);
            btnRefreshScanner.Size = new Size(120, 36);
            btnRefreshScanner.Location = new Point(488, 24);
            btnRefreshScanner.Anchor = AnchorStyles.Top | AnchorStyles.Right;
            btnRefreshScanner.Cursor = Cursors.Hand;
            btnRefreshScanner.Click += (s, e) => { RefreshScannerDevices(); };

            panelScannerCard.Controls.Add(lblScannerTitle);
            panelScannerCard.Controls.Add(lblScannerStatus);
            panelScannerCard.Controls.Add(cmbScanners);
            panelScannerCard.Controls.Add(btnRefreshScanner);

            // 3. Panel Tombol Kontrol
            panelControl = new Panel();
            panelControl.Location = new Point(20, 195);
            panelControl.Size = new Size(624, 45);
            panelControl.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right;

            btnStart = new Button();
            btnStart.Text = "▶ Start Service";
            btnStart.Font = new Font("Segoe UI", 10F, FontStyle.Bold);
            btnStart.BackColor = Color.FromArgb(22, 163, 74);
            btnStart.ForeColor = Color.White;
            btnStart.FlatStyle = FlatStyle.Flat;
            btnStart.FlatAppearance.BorderSize = 0;
            btnStart.Size = new Size(140, 40);
            btnStart.Location = new Point(0, 0);
            btnStart.Cursor = Cursors.Hand;
            btnStart.Click += (s, e) => { StartBridgeService(); };

            btnRestart = new Button();
            btnRestart.Text = "🔄 Restart Service";
            btnRestart.Font = new Font("Segoe UI", 10F, FontStyle.Bold);
            btnRestart.BackColor = Color.FromArgb(37, 99, 235);
            btnRestart.ForeColor = Color.White;
            btnRestart.FlatStyle = FlatStyle.Flat;
            btnRestart.FlatAppearance.BorderSize = 0;
            btnRestart.Size = new Size(140, 40);
            btnRestart.Location = new Point(150, 0);
            btnRestart.Cursor = Cursors.Hand;
            btnRestart.Click += (s, e) => { RestartBridgeService(); };

            btnStop = new Button();
            btnStop.Text = "⏹ Stop Service";
            btnStop.Font = new Font("Segoe UI", 10F, FontStyle.Bold);
            btnStop.BackColor = Color.FromArgb(220, 38, 38);
            btnStop.ForeColor = Color.White;
            btnStop.FlatStyle = FlatStyle.Flat;
            btnStop.FlatAppearance.BorderSize = 0;
            btnStop.Size = new Size(140, 40);
            btnStop.Location = new Point(300, 0);
            btnStop.Cursor = Cursors.Hand;
            btnStop.Click += (s, e) => { StopBridgeService(); };

            btnMinimizeTray = new Button();
            btnMinimizeTray.Text = "📥 Minimize ke Tray";
            btnMinimizeTray.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular);
            btnMinimizeTray.BackColor = Color.FromArgb(226, 232, 240);
            btnMinimizeTray.ForeColor = Color.FromArgb(30, 41, 59);
            btnMinimizeTray.FlatStyle = FlatStyle.Flat;
            btnMinimizeTray.FlatAppearance.BorderSize = 1;
            btnMinimizeTray.FlatAppearance.BorderColor = Color.FromArgb(203, 213, 225);
            btnMinimizeTray.Size = new Size(150, 40);
            btnMinimizeTray.Location = new Point(474, 0);
            btnMinimizeTray.Anchor = AnchorStyles.Top | AnchorStyles.Right;
            btnMinimizeTray.Cursor = Cursors.Hand;
            btnMinimizeTray.Click += (s, e) => { MinimizeToTray(false); };

            panelControl.Controls.Add(btnStart);
            panelControl.Controls.Add(btnRestart);
            panelControl.Controls.Add(btnStop);
            panelControl.Controls.Add(btnMinimizeTray);

            // 4. Options Checkboxes
            chkAutoStart = new CheckBox();
            chkAutoStart.Text = "Mulai service otomatis saat aplikasi dibuka";
            chkAutoStart.Checked = true;
            chkAutoStart.AutoSize = true;
            chkAutoStart.Location = new Point(22, 248);

            chkMinimizeToTray = new CheckBox();
            chkMinimizeToTray.Text = "Minimize ke System Tray secara otomatis saat aplikasi dibuka";
            chkMinimizeToTray.Checked = true;
            chkMinimizeToTray.AutoSize = true;
            chkMinimizeToTray.Location = new Point(300, 248);

            // 5. Activity Log Box
            grpLog = new GroupBox();
            grpLog.Text = " Aktivitas & Log Service ";
            grpLog.Font = new Font("Segoe UI", 9.5F, FontStyle.Bold);
            grpLog.ForeColor = Color.FromArgb(51, 65, 85);
            grpLog.Location = new Point(20, 278);
            grpLog.Size = new Size(624, 237);
            grpLog.Anchor = AnchorStyles.Top | AnchorStyles.Bottom | AnchorStyles.Left | AnchorStyles.Right;

            txtLog = new RichTextBox();
            txtLog.Dock = DockStyle.Fill;
            txtLog.BackColor = Color.FromArgb(15, 23, 42);
            txtLog.ForeColor = Color.FromArgb(226, 232, 240);
            txtLog.Font = new Font("Consolas", 9.5F, FontStyle.Regular);
            txtLog.ReadOnly = true;
            txtLog.BorderStyle = BorderStyle.None;

            btnClearLog = new Button();
            btnClearLog.Text = "🗑 Hapus Log";
            btnClearLog.Font = new Font("Segoe UI", 8.5F, FontStyle.Regular);
            btnClearLog.BackColor = Color.FromArgb(241, 245, 249);
            btnClearLog.ForeColor = Color.FromArgb(100, 116, 139);
            btnClearLog.FlatStyle = FlatStyle.Flat;
            btnClearLog.FlatAppearance.BorderSize = 0;
            btnClearLog.Size = new Size(90, 24);
            btnClearLog.Location = new Point(524, 0);
            btnClearLog.Anchor = AnchorStyles.Top | AnchorStyles.Right;
            btnClearLog.Cursor = Cursors.Hand;
            btnClearLog.Click += (s, e) => { txtLog.Clear(); LogInfo("Log dibersihkan."); };

            grpLog.Controls.Add(btnClearLog);
            grpLog.Controls.Add(txtLog);

            // Controls Assembly
            this.Controls.Add(panelHeader);
            this.Controls.Add(panelScannerCard);
            this.Controls.Add(panelControl);
            this.Controls.Add(chkAutoStart);
            this.Controls.Add(chkMinimizeToTray);
            this.Controls.Add(grpLog);

            // Timer periodic check
            timerStatusCheck = new Timer();
            timerStatusCheck.Interval = 5000;
            timerStatusCheck.Tick += (s, e) => { CheckServerStatus(); };
            timerStatusCheck.Start();

            this.FormClosing += MainForm_FormClosing;
            this.Resize += MainForm_Resize;
        }

        // -------------------------------------------------------------------
        // System Tray Setup
        // -------------------------------------------------------------------
        private void SetupTrayIcon()
        {
            trayContextMenu = new ContextMenuStrip();

            menuShow = new ToolStripMenuItem("Tampilkan Window Utama", null, (s, e) => RestoreFromTray());
            menuShow.Font = new Font(trayContextMenu.Font, FontStyle.Bold);

            menuStart = new ToolStripMenuItem("Start Service", null, (s, e) => StartBridgeService());
            menuRestart = new ToolStripMenuItem("Restart Service", null, (s, e) => RestartBridgeService());
            menuStop = new ToolStripMenuItem("Stop Service", null, (s, e) => StopBridgeService());
            ToolStripMenuItem menuUninstall = new ToolStripMenuItem("Uninstal Scanner Bridge", null, (s, e) => {
                string uninstallerPath = Path.Combine(currentAppDir, "Uninstall.exe");
                if (File.Exists(uninstallerPath))
                {
                    Process.Start(uninstallerPath);
                }
                else
                {
                    MessageBox.Show("File Uninstall.exe tidak ditemukan.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                }
            });

            menuExit = new ToolStripMenuItem("Keluar Aplikasi", null, (s, e) => {
                notifyIcon.Visible = false;
                StopBridgeService();
                Application.Exit();
            });

            trayContextMenu.Items.Add(menuShow);
            trayContextMenu.Items.Add(new ToolStripSeparator());
            trayContextMenu.Items.Add(menuStart);
            trayContextMenu.Items.Add(menuRestart);
            trayContextMenu.Items.Add(menuStop);
            trayContextMenu.Items.Add(new ToolStripSeparator());
            trayContextMenu.Items.Add(menuUninstall);
            trayContextMenu.Items.Add(menuExit);

            notifyIcon = new NotifyIcon();
            notifyIcon.Text = "MKDC Scanner Bridge v2.0";
            try
            {
                string iconPath = Path.Combine(currentAppDir, "app.ico");
                if (File.Exists(iconPath)) notifyIcon.Icon = new Icon(iconPath);
                else notifyIcon.Icon = SystemIcons.Application;
            }
            catch { notifyIcon.Icon = SystemIcons.Application; }

            notifyIcon.ContextMenuStrip = trayContextMenu;
            notifyIcon.Visible = true;
            notifyIcon.DoubleClick += (s, e) => RestoreFromTray();
        }

        private void MinimizeToTray(bool showTip)
        {
            this.Hide();
            this.ShowInTaskbar = false;
            notifyIcon.Visible = true;

            if (showTip)
            {
                notifyIcon.ShowBalloonTip(3000, "MKDC Scanner Bridge",
                    "Aplikasi telah aktif di System Tray (dekat jam Windows).", ToolTipIcon.Info);
            }
        }

        private void RestoreFromTray()
        {
            this.Show();
            this.WindowState = FormWindowState.Normal;
            this.ShowInTaskbar = true;
            this.Activate();
        }

        private void MainForm_Resize(object sender, EventArgs e)
        {
            if (this.WindowState == FormWindowState.Minimized)
            {
                MinimizeToTray(false);
            }
        }

        private void MainForm_FormClosing(object sender, FormClosingEventArgs e)
        {
            if (e.CloseReason == CloseReason.UserClosing)
            {
                DialogResult dr = MessageBox.Show(
                    "Apakah Anda ingin menghentikan service dan keluar sepenuhnya?\n\n" +
                    "• Klik [Ya] untuk Keluar sepenuhnya.\n" +
                    "• Klik [Tidak] untuk Minimize ke System Tray.\n" +
                    "• Klik [Batal] untuk membatalkan.",
                    "Konfirmasi Keluar - MKDC Scanner Bridge",
                    MessageBoxButtons.YesNoCancel,
                    MessageBoxIcon.Question);

                if (dr == DialogResult.No)
                {
                    e.Cancel = true;
                    MinimizeToTray(true);
                    return;
                }
                else if (dr == DialogResult.Cancel)
                {
                    e.Cancel = true;
                    return;
                }
            }

            notifyIcon.Visible = false;
            StopBridgeService();
        }

        // -------------------------------------------------------------------
        // Service Process Management
        // -------------------------------------------------------------------
        private void StartBridgeService()
        {
            if (isServerRunning)
            {
                LogInfo("Service sudah berjalan di port " + PORT);
                return;
            }

            LogInfo("Memulai MKDC Scanner Bridge Service...");

            string nodeExe = FindNodeExecutable();
            if (string.IsNullOrEmpty(nodeExe))
            {
                LogError("Node.js tidak ditemukan! Pastikan Node.js terpasang di komputer Anda.");
                MessageBox.Show("Node.js tidak ditemukan!\nSilakan install Node.js terlebih dahulu dari https://nodejs.org",
                    "Error Node.js", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            string indexPath = Path.Combine(currentAppDir, "index.js");
            if (!File.Exists(indexPath))
            {
                LogError("File index.js tidak ditemukan di folder: " + currentAppDir);
                return;
            }

            try
            {
                ProcessStartInfo psi = new ProcessStartInfo();
                psi.FileName = nodeExe;
                psi.Arguments = "\"" + indexPath + "\"";
                psi.WorkingDirectory = currentAppDir;
                psi.UseShellExecute = false;
                psi.CreateNoWindow = true;
                psi.RedirectStandardOutput = true;
                psi.RedirectStandardError = true;

                nodeProcess = new Process();
                nodeProcess.StartInfo = psi;
                nodeProcess.EnableRaisingEvents = true;

                nodeProcess.OutputDataReceived += (s, e) => {
                    if (!string.IsNullOrEmpty(e.Data)) LogNodeOutput(e.Data);
                };
                nodeProcess.ErrorDataReceived += (s, e) => {
                    if (!string.IsNullOrEmpty(e.Data)) LogError(e.Data);
                };
                nodeProcess.Exited += (s, e) => {
                    this.Invoke((Action)(() => {
                        isServerRunning = false;
                        UpdateUIState();
                        LogWarning("Proses bridge service dihentikan.");
                    }));
                };

                nodeProcess.Start();
                nodeProcess.BeginOutputReadLine();
                nodeProcess.BeginErrorReadLine();

                LogSuccess("Proses Node.js berhasil diluncurkan (PID: " + nodeProcess.Id + ").");

                Timer t = new Timer();
                t.Interval = 1000;
                t.Tick += (s, e) => {
                    t.Stop();
                    t.Dispose();
                    CheckServerStatus();
                    RefreshScannerDevices();
                };
                t.Start();
            }
            catch (Exception ex)
            {
                LogError("Gagal menjalankan service Node.js: " + ex.Message);
            }
        }

        private void StopBridgeService()
        {
            LogInfo("Menghentikan service...");
            try
            {
                if (nodeProcess != null && !nodeProcess.HasExited)
                {
                    nodeProcess.Kill();
                    nodeProcess.Dispose();
                    nodeProcess = null;
                }

                KillProcessByPort(PORT);

                isServerRunning = false;
                UpdateUIState();
                LogInfo("Service berhasil dihentikan.");
            }
            catch (Exception ex)
            {
                LogError("Error saat menghentikan service: " + ex.Message);
            }
        }

        private void RestartBridgeService()
        {
            LogInfo("Memuat ulang (restart) service scanner bridge...");
            StopBridgeService();

            Timer t = new Timer();
            t.Interval = 1500;
            t.Tick += (s, e) => {
                t.Stop();
                t.Dispose();
                StartBridgeService();
            };
            t.Start();
        }

        private void KillProcessByPort(int port)
        {
            try
            {
                Process p = new Process();
                p.StartInfo.FileName = "cmd.exe";
                p.StartInfo.Arguments = string.Format("/c for /f \"tokens=5\" %a in ('netstat -aon ^| findstr :{0}') do taskkill /f /pid %a", port);
                p.StartInfo.CreateNoWindow = true;
                p.StartInfo.UseShellExecute = false;
                p.Start();
                p.WaitForExit(2000);
            }
            catch {}
        }

        private string FindNodeExecutable()
        {
            string[] candidates = new string[] {
                @"C:\Program Files\nodejs\node.exe",
                @"C:\Program Files (x86)\nodejs\node.exe",
                Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData), @"Programs\nodejs\node.exe"),
                Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.ApplicationData), @"nvm\node.exe")
            };

            foreach (string p in candidates)
            {
                if (File.Exists(p)) return p;
            }

            try
            {
                Process p = new Process();
                p.StartInfo.FileName = "where.exe";
                p.StartInfo.Arguments = "node.exe";
                p.StartInfo.UseShellExecute = false;
                p.StartInfo.RedirectStandardOutput = true;
                p.StartInfo.CreateNoWindow = true;
                p.Start();
                string outStr = p.StandardOutput.ReadToEnd();
                p.WaitForExit(2000);

                if (!string.IsNullOrEmpty(outStr))
                {
                    string[] lines = outStr.Split(new[] { '\r', '\n' }, StringSplitOptions.RemoveEmptyEntries);
                    if (lines.Length > 0 && File.Exists(lines[0])) return lines[0];
                }
            }
            catch {}

            return null;
        }

        // -------------------------------------------------------------------
        // HTTP API & Scanner Check (.NET 4.0 Compatible)
        // -------------------------------------------------------------------
        private void CheckServerStatus()
        {
            Task.Factory.StartNew(() => {
                try
                {
                    HttpWebRequest req = (HttpWebRequest)WebRequest.Create(API_URL);
                    req.Timeout = 2000;
                    req.Method = "GET";

                    using (HttpWebResponse resp = (HttpWebResponse)req.GetResponse())
                    {
                        if (resp.StatusCode == HttpStatusCode.OK)
                        {
                            this.Invoke((Action)(() => {
                                if (!isServerRunning)
                                {
                                    isServerRunning = true;
                                    UpdateUIState();
                                    LogSuccess("Service bridge aktif dan merespon di " + API_URL);
                                    RefreshScannerDevices();
                                }
                            }));
                            return;
                        }
                    }
                }
                catch
                {
                    this.Invoke((Action)(() => {
                        if (isServerRunning)
                        {
                            isServerRunning = false;
                            UpdateUIState();
                            LogWarning("Koneksi service terputus.");
                        }
                    }));
                }
            });
        }

        private void CmbScanners_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (cmbScanners.SelectedItem == null) return;
            ScannerItem selected = (ScannerItem)cmbScanners.SelectedItem;
            string savedDefaultId = LoadDefaultScannerId();
            if (selected.Id != savedDefaultId)
            {
                SaveDefaultScanner(selected.Id, selected.Name);
                LogInfo("Scanner default diubah ke: " + selected.Name);
            }
        }

        public class ScannerItem
        {
            public string Id { get; set; }
            public string Name { get; set; }
            public override string ToString()
            {
                return Name;
            }
        }

        private void SaveDefaultScanner(string id, string name)
        {
            try
            {
                string configPath = Path.Combine(currentAppDir, "config.json");
                string json = string.Format(
                    "{{\r\n  \"defaultDeviceId\": \"{0}\",\r\n  \"defaultDeviceName\": \"{1}\"\r\n}}",
                    id.Replace("\\", "\\\\").Replace("\"", "\\\""),
                    name.Replace("\\", "\\\\").Replace("\"", "\\\"")
                );
                File.WriteAllText(configPath, json, System.Text.Encoding.UTF8);
            }
            catch (Exception ex)
            {
                LogError("Gagal menyimpan default scanner: " + ex.Message);
            }
        }

        private string LoadDefaultScannerId()
        {
            try
            {
                string configPath = Path.Combine(currentAppDir, "config.json");
                if (File.Exists(configPath))
                {
                    string json = File.ReadAllText(configPath);
                    Match m = Regex.Match(json, @"""defaultDeviceId""\s*:\s*""([^""]+)""");
                    if (m.Success)
                    {
                        return m.Groups[1].Value.Replace("\\\\", "\\").Replace("\\\"", "\"");
                    }
                }
            }
            catch {}
            return "";
        }

        private void RefreshScannerDevices()
        {
            lblScannerStatus.Text = "⏳ Memeriksa perangkat scanner lokal...";
            lblScannerStatus.ForeColor = Color.FromArgb(217, 119, 6);

            Task.Factory.StartNew(() => {
                try
                {
                    Type deviceManagerType = Type.GetTypeFromProgID("WIA.DeviceManager");
                    if (deviceManagerType == null)
                    {
                        throw new Exception("WIA DeviceManager tidak ditemukan di Windows ini.");
                    }

                    object deviceManager = Activator.CreateInstance(deviceManagerType);
                    object deviceInfos = deviceManagerType.InvokeMember("DeviceInfos", 
                        System.Reflection.BindingFlags.GetProperty, null, deviceManager, null);

                    int count = (int)deviceInfos.GetType().InvokeMember("Count", 
                        System.Reflection.BindingFlags.GetProperty, null, deviceInfos, null);

                    var items = new System.Collections.Generic.List<ScannerItem>();
                    string savedDefaultId = LoadDefaultScannerId();
                    int selectedIndex = -1;

                    for (int i = 1; i <= count; i++)
                    {
                        object info = deviceInfos.GetType().InvokeMember("Item", 
                            System.Reflection.BindingFlags.GetProperty, null, deviceInfos, new object[] { i });

                        int type = (int)info.GetType().InvokeMember("Type", 
                            System.Reflection.BindingFlags.GetProperty, null, info, null);

                        if (type == 1) // 1 = Scanner
                        {
                            string devId = (string)info.GetType().InvokeMember("DeviceID", 
                                System.Reflection.BindingFlags.GetProperty, null, info, null);
                            
                            object properties = info.GetType().InvokeMember("Properties", 
                                System.Reflection.BindingFlags.GetProperty, null, info, null);

                            // Dapatkan Name
                            object propName = properties.GetType().InvokeMember("Item", 
                                System.Reflection.BindingFlags.GetProperty, null, properties, new object[] { "Name" });
                            string devName = (string)propName.GetType().InvokeMember("Value", 
                                System.Reflection.BindingFlags.GetProperty, null, propName, null);

                            items.Add(new ScannerItem { Id = devId, Name = devName });
                            
                            if (devId == savedDefaultId)
                            {
                                selectedIndex = items.Count - 1;
                            }
                        }
                    }

                    this.Invoke((Action)(() => {
                        cmbScanners.Items.Clear();
                        foreach (var item in items)
                        {
                            cmbScanners.Items.Add(item);
                        }

                        if (cmbScanners.Items.Count > 0)
                        {
                            cmbScanners.Enabled = true;
                            if (selectedIndex >= 0)
                            {
                                cmbScanners.SelectedIndex = selectedIndex;
                                lblScannerStatus.Text = "🟢 AKTIF (DEFAULT): " + ((ScannerItem)cmbScanners.SelectedItem).Name;
                            }
                            else
                            {
                                cmbScanners.SelectedIndex = 0;
                                lblScannerStatus.Text = "🟢 TERHUBUNG: " + ((ScannerItem)cmbScanners.SelectedItem).Name;
                                ScannerItem first = (ScannerItem)cmbScanners.SelectedItem;
                                SaveDefaultScanner(first.Id, first.Name);
                            }
                            lblScannerStatus.ForeColor = Color.FromArgb(22, 163, 74);
                        }
                        else
                        {
                            cmbScanners.Enabled = false;
                            lblScannerStatus.Text = "🔴 Tidak ada scanner terhubung";
                            lblScannerStatus.ForeColor = Color.FromArgb(220, 38, 38);
                            LogWarning("Tidak ada perangkat scanner WIA lokal yang terdeteksi.");
                        }
                    }));
                }
                catch (Exception ex)
                {
                    this.Invoke((Action)(() => {
                        cmbScanners.Enabled = false;
                        lblScannerStatus.Text = "🔴 Gagal deteksi scanner lokal";
                        lblScannerStatus.ForeColor = Color.FromArgb(220, 38, 38);
                        LogError("Gagal mendeteksi scanner lokal: " + ex.Message);
                    }));
                }
            });
        }

        private void UpdateUIState()
        {
            if (isServerRunning)
            {
                lblServerBadge.Text = "● BERJALAN (PORT 7999)";
                lblServerBadge.BackColor = Color.FromArgb(22, 163, 74);
                btnStart.Enabled = false;
                btnRestart.Enabled = true;
                btnStop.Enabled = true;

                menuStart.Enabled = false;
                menuRestart.Enabled = true;
                menuStop.Enabled = true;
            }
            else
            {
                lblServerBadge.Text = "● DITERHENTIKAN";
                lblServerBadge.BackColor = Color.FromArgb(239, 68, 68);
                btnStart.Enabled = true;
                btnRestart.Enabled = false;
                btnStop.Enabled = false;

                menuStart.Enabled = true;
                menuRestart.Enabled = false;
                menuStop.Enabled = false;

                lblScannerStatus.Text = "🔴 Service Bridge belum berjalan";
                lblScannerStatus.ForeColor = Color.FromArgb(100, 116, 139);
            }
        }

        // -------------------------------------------------------------------
        // Logging Helpers
        // -------------------------------------------------------------------
        private void LogNodeOutput(string message)
        {
            AppendLog(message, Color.FromArgb(148, 163, 184));
        }

        private void LogInfo(string message)
        {
            AppendLog("[" + DateTime.Now.ToString("HH:mm:ss") + "] [INFO] " + message, Color.FromArgb(56, 189, 248));
        }

        private void LogSuccess(string message)
        {
            AppendLog("[" + DateTime.Now.ToString("HH:mm:ss") + "] [OK] " + message, Color.FromArgb(74, 222, 128));
        }

        private void LogWarning(string message)
        {
            AppendLog("[" + DateTime.Now.ToString("HH:mm:ss") + "] [WARN] " + message, Color.FromArgb(251, 191, 36));
        }

        private void LogError(string message)
        {
            AppendLog("[" + DateTime.Now.ToString("HH:mm:ss") + "] [ERROR] " + message, Color.FromArgb(248, 113, 113));
        }

        private void AppendLog(string text, Color color)
        {
            if (txtLog.InvokeRequired)
            {
                txtLog.Invoke((Action)(() => AppendLog(text, color)));
                return;
            }

            txtLog.SelectionStart = txtLog.TextLength;
            txtLog.SelectionLength = 0;
            txtLog.SelectionColor = color;
            txtLog.AppendText(text + "\n");
            txtLog.ScrollToCaret();
        }
    }
}
