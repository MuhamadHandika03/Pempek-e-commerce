<?php
session_start();
require_once 'config/database.php';
require_once 'core/Functions.php';

// Jika sudah login, lempar ke beranda
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (empty($nama) || empty($email) || empty($password)) {
        $error = 'Nama, Email, dan Password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // Cek email duplikat
        $stmt = $conn->prepare("SELECT id FROM pelanggan WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar. Silakan gunakan email lain atau login.';
        } else {
            // Hash password
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO pelanggan (email, password, nama, no_hp, alamat) VALUES (?, ?, ?, ?, ?)");
            if ($insert->execute([$email, $hash, $nama, $no_hp, $alamat])) {
                $success = 'Pendaftaran berhasil! Silakan login dengan akun Anda.';
            } else {
                $error = 'Terjadi kesalahan sistem saat mendaftar.';
            }
        }
    }
}

$title = "Daftar Akun - Pempek Wong Kito";
include 'views/templates/header.php';
?>

<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-danger text-white text-center py-4">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Daftar Akun Baru</h4>
                </div>
                <div class="card-body p-4 p-sm-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success py-2 small"><?= htmlspecialchars($success) ?></div>
                        <div class="text-center mt-3"><a href="login.php" class="btn btn-outline-danger w-100">Menuju Halaman Login</a></div>
                    <?php else: ?>
                        <form method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" placeholder="Nama Anda" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="Email aktif" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">No. WhatsApp</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="Misal: 08123456789">
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Alamat Pengiriman Default</label>
                                <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100 py-2 fw-medium shadow-sm mb-3">Buat Akun</button>
                        </form>
                        <p class="text-center small text-muted mb-0">Sudah punya akun? <a href="login.php" class="text-danger text-decoration-none fw-bold">Login di sini</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/templates/footer.php'; ?>