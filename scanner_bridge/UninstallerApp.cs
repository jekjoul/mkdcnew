using System;
using System.Drawing;
using System.Diagnostics;
using System.IO;
using System.Reflection;
using System.Windows.Forms;
using Microsoft.Win32;

namespace MKDCScannerBridgeUninstaller
{
    public class UninstallerForm : Form
    {
        private Panel panelHeader;
        private PictureBox picLogo;
        private Label lblHeaderTitle;
        private Label lblHeaderSub;

        private Label lblWarning;
        private ProgressBar progressBar;
        private Label lblStatus;

        private Button btnUninstall;
        private Button btnCancel;

        private string currentDir = "";

        [STAThread]
        public static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new UninstallerForm());
        }

        public UninstallerForm()
        {
            currentDir = AppDomain.CurrentDomain.BaseDirectory.TrimEnd('\\');
            InitializeComponent();
        }

        private void InitializeComponent()
        {
            this.Text = "MKDC Scanner Bridge - Penghapusan (Uninstall)";
            this.Size = new Size(540, 360);
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
            lblHeaderTitle.Text = "Uninstal MKDC Scanner Bridge";
            lblHeaderTitle.Font = new Font("Segoe UI", 14F, FontStyle.Bold);
            lblHeaderTitle.ForeColor = Color.White;
            lblHeaderTitle.AutoSize = true;
            lblHeaderTitle.Location = new Point(80, 15);

            lblHeaderSub = new Label();
            lblHeaderSub.Text = "Hapus aplikasi, service bridge scanner, dan komponen terkait";
            lblHeaderSub.Font = new Font("Segoe UI", 9F, FontStyle.Regular);
            lblHeaderSub.ForeColor = Color.FromArgb(148, 163, 184);
            lblHeaderSub.AutoSize = true;
            lblHeaderSub.Location = new Point(82, 44);

            panelHeader.Controls.Add(picLogo);
            panelHeader.Controls.Add(lblHeaderTitle);
            panelHeader.Controls.Add(lblHeaderSub);

            // Warning / Info Label
            lblWarning = new Label();
            lblWarning.Text = "Apakah Anda yakin ingin menghapus MKDC Scanner Bridge dari komputer Anda?\n\n" +
                              "Proses ini akan menghentikan service, menghapus pintasan Desktop & Start Menu, " +
                              "serta menghapus seluruh berkas instalasi aplikasi.";
            lblWarning.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular);
            lblWarning.ForeColor = Color.FromArgb(51, 65, 85);
            lblWarning.Location = new Point(20, 95);
            lblWarning.Size = new Size(484, 85);

            // Progress & Status
            lblStatus = new Label();
            lblStatus.Text = "Siap untuk menghapus aplikasi.";
            lblStatus.Location = new Point(20, 190);
            lblStatus.AutoSize = true;
            lblStatus.ForeColor = Color.FromArgb(100, 116, 139);

            progressBar = new ProgressBar();
            progressBar.Location = new Point(20, 215);
            progressBar.Size = new Size(484, 22);

            // Bottom Buttons
            btnUninstall = new Button();
            btnUninstall.Text = "🗑 Uninstal Sekarang";
            btnUninstall.Font = new Font("Segoe UI", 9.5F, FontStyle.Bold);
            btnUninstall.BackColor = Color.FromArgb(220, 38, 38); // Red
            btnUninstall.ForeColor = Color.White;
            btnUninstall.FlatStyle = FlatStyle.Flat;
            btnUninstall.FlatAppearance.BorderSize = 0;
            btnUninstall.Size = new Size(160, 36);
            btnUninstall.Location = new Point(224, 260);
            btnUninstall.Cursor = Cursors.Hand;
            btnUninstall.Click += (s, e) => StartUninstallation();

            btnCancel = new Button();
            btnCancel.Text = "Batal";
            btnCancel.Size = new Size(100, 36);
            btnCancel.Location = new Point(404, 260);
            btnCancel.Click += (s, e) => this.Close();

            this.Controls.Add(panelHeader);
            this.Controls.Add(lblWarning);
            this.Controls.Add(lblStatus);
            this.Controls.Add(progressBar);
            this.Controls.Add(btnUninstall);
            this.Controls.Add(btnCancel);
        }

        private void StartUninstallation()
        {
            DialogResult dr = MessageBox.Show(
                "Konfirmasi: Seluruh berkas MKDC Scanner Bridge akan dihapus.\nLanjutkan proses uninstalasi?",
                "Konfirmasi Uninstall", MessageBoxButtons.YesNo, MessageBoxIcon.Question);

            if (dr != DialogResult.Yes) return;

            btnUninstall.Enabled = false;
            btnCancel.Enabled = false;

            try
            {
                lblStatus.Text = "Menghentikan service dan proses running...";
                progressBar.Value = 20;
                Application.DoEvents();

                StopRunningProcesses();

                lblStatus.Text = "Menghapus entri registry Windows...";
                progressBar.Value = 45;
                Application.DoEvents();

                RemoveRegistryKeys();

                lblStatus.Text = "Menghapus shortcut Desktop & Start Menu...";
                progressBar.Value = 70;
                Application.DoEvents();

                RemoveShortcuts();

                lblStatus.Text = "Menghapus berkas aplikasi...";
                progressBar.Value = 90;
                Application.DoEvents();

                progressBar.Value = 100;
                lblStatus.Text = "Uninstalasi berhasil selesai!";
                Application.DoEvents();

                MessageBox.Show("MKDC Scanner Bridge telah berhasil dihapus dari komputer Anda.",
                    "Uninstal Selesai", MessageBoxButtons.OK, MessageBoxIcon.Information);

                // Schedule self-deletion of installation directory
                ScheduleFolderCleanup(currentDir);

                this.Close();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Gagal melakukan uninstalasi: " + ex.Message, "Error Uninstall", MessageBoxButtons.OK, MessageBoxIcon.Error);
                btnUninstall.Enabled = true;
                btnCancel.Enabled = true;
            }
        }

        private void StopRunningProcesses()
        {
            try
            {
                // Kill MKDC_Scanner_Bridge.exe process
                foreach (Process p in Process.GetProcessesByName("MKDC_Scanner_Bridge"))
                {
                    try { p.Kill(); p.WaitForExit(1000); } catch {}
                }

                // Kill node process listening on port 7999
                Process cmd = new Process();
                cmd.StartInfo.FileName = "cmd.exe";
                cmd.StartInfo.Arguments = "/c for /f \"tokens=5\" %a in ('netstat -aon ^| findstr :7999') do taskkill /f /pid %a";
                cmd.StartInfo.CreateNoWindow = true;
                cmd.StartInfo.UseShellExecute = false;
                cmd.Start();
                cmd.WaitForExit(2000);
            }
            catch {}
        }

        private void RemoveRegistryKeys()
        {
            try
            {
                // Remove Startup registry key
                using (RegistryKey key = Registry.CurrentUser.OpenSubKey(@"SOFTWARE\Microsoft\Windows\CurrentVersion\Run", true))
                {
                    if (key != null)
                    {
                        key.DeleteValue("MKDCScannerBridge", false);
                    }
                }

                // Remove Uninstall registry key
                using (RegistryKey key = Registry.CurrentUser.OpenSubKey(@"SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall", true))
                {
                    if (key != null)
                    {
                        key.DeleteSubKeyTree("MKDCScannerBridge", false);
                    }
                }
            }
            catch {}
        }

        private void RemoveShortcuts()
        {
            try
            {
                // Desktop shortcut
                string desktopFolder = Environment.GetFolderPath(Environment.SpecialFolder.DesktopDirectory);
                string desktopLnk = Path.Combine(desktopFolder, "MKDC Scanner Bridge.lnk");
                if (File.Exists(desktopLnk)) File.Delete(desktopLnk);

                // Start menu shortcut & folder
                string startMenuDir = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.Programs), "MKDC Scanner Bridge");
                if (Directory.Exists(startMenuDir))
                {
                    Directory.Delete(startMenuDir, true);
                }
            }
            catch {}
        }

        private void ScheduleFolderCleanup(string targetFolder)
        {
            try
            {
                // Launch delayed CMD script to delete folder after this process exits
                ProcessStartInfo psi = new ProcessStartInfo();
                psi.FileName = "cmd.exe";
                psi.Arguments = string.Format("/c timeout /t 2 /nobreak >nul & rmdir /s /q \"{0}\"", targetFolder);
                psi.CreateNoWindow = true;
                psi.UseShellExecute = false;
                Process.Start(psi);
            }
            catch {}
        }
    }
}
