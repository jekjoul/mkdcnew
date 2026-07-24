<?php
session_start();
require_once __DIR__ . '/lib/BridgeStorage.php';

// Redirect jika sudah login
if (isset($_SESSION['fp_bridge_admin'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (BridgeStorage::verifyLogin($username, $password)) {
        $_SESSION['fp_bridge_admin']      = true;
        $_SESSION['fp_bridge_user']       = $username;
        $_SESSION['fp_bridge_logged_time'] = time();
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau Password salah! (Default: admin / admin123)';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fingerprint WebDesktop Bridge</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 56px; height: 56px; background: #2563eb; color: white; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 0.75rem;">
                    <span class="iconify" data-icon="solar:scanner-bold"></span>
                </div>
                <h2 style="font-size: 1.4rem; font-weight: 700; color: #0f172a;">Fingerprint Bridge</h2>
                <p style="font-size: 0.85rem; color: #64748b;">StandAlone WebDesktop App (EasyLink SDK)</p>
            </div>

            <?php if ($error): ?>
                <div style="background: #fef2f2; color: #ef4444; padding: 0.75rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1.25rem; border: 1px solid #fecaca; text-align: center;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required value="admin">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; margin-top: 0.5rem; font-size: 0.95rem;">
                    <span class="iconify" data-icon="solar:login-2-bold"></span> Masuk ke Aplikasi
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem; font-size: 0.8rem; color: #94a3b8;">
                Default login: <strong>admin</strong> / <strong>admin123</strong>
            </div>
        </div>
    </div>
</body>
</html>
