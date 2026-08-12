using System;
using System.Drawing;
using System.Diagnostics;
using System.IO;
using System.Reflection;
using System.Windows.Forms;
using Microsoft.Win32;

namespace MKDCScannerBridgeInstaller
{
    public class InstallerForm : Form
    {
        private Panel panelHeader;
        private PictureBox picLogo;
        private Label lblHeaderTitle;
        private Label lblHeaderSub;

        private Label lblPathTitle;
        private TextBox txtInstallPath;
        private Button btnBrowse;

        private GroupBox grpOptions;
        private CheckBox chkDesktopShortcut;
        private CheckBox chkStartMenuShortcut;
        private CheckBox chkAutoStartWindows;
        private CheckBox chkRunAfterInstall;

        private ProgressBar progressBar;
        private Label lblStatus;

        private Button btnInstall;
        private Button btnCancel;

        private string currentDir = "";
        private string defaultTargetDir = @"C:\MKDC_Scanner_Bridge";

        [STAThread]
        public static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new InstallerForm());
        }

        public InstallerForm()
        {
            currentDir = AppDomain.CurrentDomain.BaseDirectory;
            InitializeComponent();
        }

        private void InitializeComponent()
        {
            this.Text = "MKDC Scanner Bridge Setup v2.0";
            this.Size = new Size(580, 460);
            this.StartPosition = FormStartPosition.CenterScreen;
            this.FormBorderStyle = FormBorderStyle.FixedDialog;
            this.MaximizeBox = false;
            this.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular, GraphicsUnit.Point);
            this.BackColor = Color.FromArgb(248, 250, 252);

            try
            {
                string iconPath = Path.Combine(currentDir, "app.ico");
                if (File.Exists(iconPath)) this.Icon = new Icon(iconPath);
                else this.Icon = SystemIcons.Application;
            }
            catch {}

            // Header Panel
            panelHeader = new Panel();
            panelHeader.Dock = DockStyle.Top;
            panelHeader.Height = 80;
            panelHeader.BackColor = Color.FromArgb(15, 23, 42); // Dark Slate

            picLogo = new PictureBox();
            picLogo.Size = new Size(54, 54);
            picLogo.Location = new Point(16, 13);
            picLogo.SizeMode = PictureBoxSizeMode.Zoom;
            try
            {
                string iconPath = Path.Combine(currentDir, "app.ico");
                if (File.Exists(iconPath)) picLogo.Image = Image.FromFile(iconPath);
            }
            catch {}

            lblHeaderTitle = new Label();
            lblHeaderTitle.Text = "Pemasangan MKDC Scanner Bridge";
            lblHeaderTitle.Font = new Font("Segoe UI", 14F, FontStyle.Bold);
            lblHeaderTitle.ForeColor = Color.White;
            lblHeaderTitle.AutoSize = true;
            lblHeaderTitle.Location = new Point(80, 15);

            lblHeaderSub = new Label();
            lblHeaderSub.Text = "Instalasi Service Bridge & Aplikasi Desktop Scanner Dokumen";
            lblHeaderSub.Font = new Font("Segoe UI", 9F, FontStyle.Regular);
            lblHeaderSub.ForeColor = Color.FromArgb(148, 163, 184);
            lblHeaderSub.AutoSize = true;
            lblHeaderSub.Location = new Point(82, 44);

            panelHeader.Controls.Add(picLogo);
            panelHeader.Controls.Add(lblHeaderTitle);
            panelHeader.Controls.Add(lblHeaderSub);

            // Path Installation Card
            lblPathTitle = new Label();
            lblPathTitle.Text = "Folder Lokasi Instalasi:";
            lblPathTitle.Font = new Font("Segoe UI", 9.5F, FontStyle.Bold);
            lblPathTitle.Location = new Point(20, 100);
            lblPathTitle.AutoSize = true;

            txtInstallPath = new TextBox();
            txtInstallPath.Text = defaultTargetDir;
            txtInstallPath.Location = new Point(22, 125);
            txtInstallPath.Size = new Size(420, 27);

            btnBrowse = new Button();
            btnBrowse.Text = "Cari...";
            btnBrowse.Location = new Point(452, 124);
            btnBrowse.Size = new Size(90, 29);
            btnBrowse.Click += (s, e) => {
                FolderBrowserDialog fbd = new FolderBrowserDialog();
                fbd.Description = "Pilih folder tujuan instalasi Scanner Bridge:";
                if (fbd.ShowDialog() == DialogResult.OK)
                {
                    txtInstallPath.Text = fbd.SelectedPath;
                }
            };

            // Options GroupBox
            grpOptions = new GroupBox();
            grpOptions.Text = " Opsi Tambahan ";
            grpOptions.Font = new Font("Segoe UI", 9.5F, FontStyle.Bold);
            grpOptions.Location = new Point(22, 165);
            grpOptions.Size = new Size(520, 145);

            chkDesktopShortcut = new CheckBox();
            chkDesktopShortcut.Text = "Buat Shortcut di Desktop (dengan Icon Kustom)";
            chkDesktopShortcut.Checked = true;
            chkDesktopShortcut.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular);
            chkDesktopShortcut.Location = new Point(16, 25);
            chkDesktopShortcut.AutoSize = true;

            chkStartMenuShortcut = new CheckBox();
            chkStartMenuShortcut.Text = "Buat Shortcut di Start Menu Windows";
            chkStartMenuShortcut.Checked = true;
            chkStartMenuShortcut.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular);
            chkStartMenuShortcut.Location = new Point(16, 52);
            chkStartMenuShortcut.AutoSize = true;

            chkAutoStartWindows = new CheckBox();
            chkAutoStartWindows.Text = "Jalankan otomatis saat Windows Startup (Background System Tray)";
            chkAutoStartWindows.Checked = true;
            chkAutoStartWindows.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular);
            chkAutoStartWindows.Location = new Point(16, 79);
            chkAutoStartWindows.AutoSize = true;

            chkRunAfterInstall = new CheckBox();
            chkRunAfterInstall.Text = "Langsung jalankan MKDC Scanner Bridge setelah instalasi selesai";
            chkRunAfterInstall.Checked = true;
            chkRunAfterInstall.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular);
            chkRunAfterInstall.Location = new Point(16, 106);
            chkRunAfterInstall.AutoSize = true;

            grpOptions.Controls.Add(chkDesktopShortcut);
            grpOptions.Controls.Add(chkStartMenuShortcut);
            grpOptions.Controls.Add(chkAutoStartWindows);
            grpOptions.Controls.Add(chkRunAfterInstall);

            // Progress & Status
            lblStatus = new Label();
            lblStatus.Text = "Siap untuk menginstal.";
            lblStatus.Location = new Point(22, 320);
            lblStatus.AutoSize = true;

            progressBar = new ProgressBar();
            progressBar.Location = new Point(22, 342);
            progressBar.Size = new Size(520, 20);

            // Bottom Buttons
            btnInstall = new Button();
            btnInstall.Text = "📦 Pasang Sekarang";
            btnInstall.Font = new Font("Segoe UI", 10F, FontStyle.Bold);
            btnInstall.BackColor = Color.FromArgb(22, 163, 74);
            btnInstall.ForeColor = Color.White;
            btnInstall.FlatStyle = FlatStyle.Flat;
            btnInstall.FlatAppearance.BorderSize = 0;
            btnInstall.Size = new Size(160, 36);
            btnInstall.Location = new Point(262, 375);
            btnInstall.Cursor = Cursors.Hand;
            btnInstall.Click += (s, e) => StartInstallation();

            btnCancel = new Button();
            btnCancel.Text = "Batal";
            btnCancel.Size = new Size(100, 36);
            btnCancel.Location = new Point(442, 375);
            btnCancel.Click += (s, e) => this.Close();

            this.Controls.Add(panelHeader);
            this.Controls.Add(lblPathTitle);
            this.Controls.Add(txtInstallPath);
            this.Controls.Add(btnBrowse);
            this.Controls.Add(grpOptions);
            this.Controls.Add(lblStatus);
            this.Controls.Add(progressBar);
            this.Controls.Add(btnInstall);
            this.Controls.Add(btnCancel);
        }

        private void StartInstallation()
        {
            string targetDir = txtInstallPath.Text.Trim();
            if (string.IsNullOrEmpty(targetDir))
            {
                MessageBox.Show("Silakan tentukan folder tujuan instalasi.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            btnInstall.Enabled = false;
            btnBrowse.Enabled = false;
            txtInstallPath.Enabled = false;
            grpOptions.Enabled = false;

            try
            {
                lblStatus.Text = "Membuat folder instalasi...";
                progressBar.Value = 20;
                if (!Directory.Exists(targetDir))
                {
                    Directory.CreateDirectory(targetDir);
                }

                lblStatus.Text = "Menyalin berkas aplikasi...";
                progressBar.Value = 50;

                string[] filesToCopy = new string[] {
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
                }

                lblStatus.Text = "Membuat pintasan (shortcut)...";
                progressBar.Value = 75;

                string targetExe = Path.Combine(targetDir, "MKDC_Scanner_Bridge.exe");
                string targetIcon = Path.Combine(targetDir, "app.ico");

                if (chkDesktopShortcut.Checked)
                {
                    string desktopFolder = Environment.GetFolderPath(Environment.SpecialFolder.DesktopDirectory);
                    string shortcutPath = Path.Combine(desktopFolder, "MKDC Scanner Bridge.lnk");
                    CreateShortcut(shortcutPath, targetExe, targetDir, targetIcon, "MKDC Scanner Bridge Desktop Control");
                }

                if (chkStartMenuShortcut.Checked)
                {
                    string startMenuFolder = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.Programs), "MKDC Scanner Bridge");
                    if (!Directory.Exists(startMenuFolder)) Directory.CreateDirectory(startMenuFolder);

                    string shortcutPath = Path.Combine(startMenuFolder, "MKDC Scanner Bridge.lnk");
                    CreateShortcut(shortcutPath, targetExe, targetDir, targetIcon, "MKDC Scanner Bridge Desktop Control");

                    string targetUninstall = Path.Combine(targetDir, "Uninstall.exe");
                    string uninstallShortcutPath = Path.Combine(startMenuFolder, "Uninstal MKDC Scanner Bridge.lnk");
                    CreateShortcut(uninstallShortcutPath, targetUninstall, targetDir, targetIcon, "Uninstal MKDC Scanner Bridge");
                }

                if (chkAutoStartWindows.Checked)
                {
                    lblStatus.Text = "Mengonfigurasi Windows Startup...";
                    try
                    {
                        RegistryKey rk = Registry.CurrentUser.OpenSubKey(@"SOFTWARE\Microsoft\Windows\CurrentVersion\Run", true);
                        if (rk != null)
                        {
                            rk.SetValue("MKDCScannerBridge", "\"" + targetExe + "\"");
                            rk.Close();
                        }
                    }
                    catch {}
                }

                RegisterUninstaller(targetDir, targetExe, targetIcon);

                progressBar.Value = 100;
                lblStatus.Text = "Instalasi berhasil selesai!";

                MessageBox.Show("Instalasi MKDC Scanner Bridge berhasil selesai!\n\nShortcut telah dibuat di Desktop dan Start Menu dengan Icon Kustom.",
                    "Instalasi Berhasil", MessageBoxButtons.OK, MessageBoxIcon.Information);

                if (chkRunAfterInstall.Checked && File.Exists(targetExe))
                {
                    Process.Start(new ProcessStartInfo() {
                        FileName = targetExe,
                        WorkingDirectory = targetDir
                    });
                }

                this.Close();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Gagal melakukan instalasi: " + ex.Message, "Error Instalasi", MessageBoxButtons.OK, MessageBoxIcon.Error);
                btnInstall.Enabled = true;
                grpOptions.Enabled = true;
            }
        }

        private void CreateShortcut(string shortcutPath, string targetPath, string workingDir, string iconPath, string description)
        {
            try
            {
                Type shellType = Type.GetTypeFromProgID("WScript.Shell");
                dynamic shell = Activator.CreateInstance(shellType);
                dynamic shortcut = shell.CreateShortcut(shortcutPath);
                shortcut.TargetPath = targetPath;
                shortcut.WorkingDirectory = workingDir;
                shortcut.Description = description;
                if (File.Exists(iconPath))
                {
                    shortcut.IconLocation = iconPath + ",0";
                }
                shortcut.Save();
            }
            catch {}
        }

        private void RegisterUninstaller(string installDir, string exePath, string iconPath)
        {
            try
            {
                string regPath = @"SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\MKDCScannerBridge";
                using (RegistryKey key = Registry.CurrentUser.CreateSubKey(regPath))
                {
                    if (key != null)
                    {
                        string uninstallExe = Path.Combine(installDir, "Uninstall.exe");
                        key.SetValue("DisplayName", "MKDC Scanner Bridge v2.0");
                        key.SetValue("Publisher", "MKDC Team");
                        key.SetValue("DisplayVersion", "2.0.0");
                        key.SetValue("InstallLocation", installDir);
                        key.SetValue("DisplayIcon", iconPath);
                        key.SetValue("UninstallString", "\"" + uninstallExe + "\"");
                    }
                }
            }
            catch {}
        }
    }
}
