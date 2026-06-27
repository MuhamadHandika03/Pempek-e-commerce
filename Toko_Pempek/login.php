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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan Password wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT id, email, password, nama FROM pelanggan WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session pelanggan
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_email'] = $user['email'];
            
            // Rehash otomatis jika PHP up-to-date config ganti
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE pelanggan SET password=? WHERE id=?");
                $upd->execute([$new_hash, $user['id']]);
            }
            
            // Redirect: ke halaman asal (redirect param) atau keranjang atau home
            $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? ($_SESSION['cart'] ? 'keranjang.php' : 'index.php');
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Email atau password salah!';
        }
    }
}

$title = "Login Pelanggan - Pempek Wong Kito";
include 'views/templates/header.php';
?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-danger text-white text-center py-4">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-box-arrow-in-right me-2"></i>Login Pelanggan</h4>
                </div>
                <div class="card-body p-4 p-sm-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small text-center"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" autocomplete="off">
                        <?php if (isset($_GET['redirect'])): ?><input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect']) ?>"><?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg fs-6" placeholder="Masukkan email" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg fs-6" placeholder="Masukkan password" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 py-2 fw-medium shadow-sm mb-3">Login Sekarang</button>
                    </form>
                    
                    <p class="text-center small text-muted mb-0">Belum punya akun? <a href="register.php" class="text-danger text-decoration-none fw-bold">Daftar sekarang</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/templates/footer.php'; ?>