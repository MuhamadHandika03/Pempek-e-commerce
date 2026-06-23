<?php
session_start();
require_once '../config/database.php';
require_once '../core/Auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_user'] = $user['username'];
            $_SESSION['admin_nama'] = $user['nama']; // Fix key mismatch (nama vs name)
            $_SESSION['admin_level'] = $user['level'];

            // Auto-rehash password jika algo / cost berubah
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $upd_hash = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                $upd_hash->execute([$new_hash, $user['id']]);
            }

            // Update last login
            $upd = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $upd->execute([$user['id']]);

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Toko Pempek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #4a1a1a; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #fff; border-radius: 16px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .brand-icon { font-size: 48px; color: #6b2d2d; }
        .btn-maroon { background: #6b2d2d; color: #fff; border: none; }
        .btn-maroon:hover { background: #4a1a1a; color: #fff; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon"><i class="bi bi-shop"></i></div>
            <h4 class="mt-2 fw-bold text-dark">Admin Panel</h4>
            <p class="text-muted small">Toko Pempek Wong Kito</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger small py-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label small fw-medium">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-maroon w-100 py-2 fw-medium">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                <a href="../index.php" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke Toko</a>
            </small>
        </div>
    </div>
</body>
</html>