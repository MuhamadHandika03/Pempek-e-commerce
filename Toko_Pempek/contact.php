<?php
session_start();
require_once 'config/database.php';
require_once 'config/app.php';
require_once 'core/Functions.php';
require_once 'core/Auth.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Wajib login untuk kirim pesan
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }

    csrf_validate_post();
    $nama  = trim($_POST['nama'] ?? '');
    $kontak = trim($_POST['kontak'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    if (empty($nama) || empty($kontak) || empty($pesan)) {
        $error = 'Semua kolom wajib diisi.';
    } else {
        $stmt = $conn->prepare("INSERT INTO messages (nama, kontak, pesan) VALUES (?, ?, ?)");
        if ($stmt->execute([$nama, $kontak, $pesan])) {
            $success = 'Pesan berhasil dikirim! Kami akan segera menghubungi Anda.';
        } else {
            $error = 'Terjadi kesalahan. Coba lagi nanti.';
        }
    }
}

$title = "Hubungi Kami - " . TOKO_NAME;
include 'views/templates/header.php';
?>

<div class="container py-5 my-4">
    <div class="row g-5">
        <div class="col-lg-5">
            <h6 class="text-danger fw-bold text-uppercase">Kontak</h6>
            <h2 class="display-6 fw-bold text-dark mb-4">Hubungi Kami</h2>
            <p class="text-muted mb-4">Ada pertanyaan tentang menu, pemesanan katering, atau pengiriman? Staf kami siap melayani Anda.</p>

            <div class="d-flex align-items-start mb-3 gap-3">
                <div class="text-danger fs-4"><i class="bi bi-geo-alt"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">Alamat Outlet</h6>
                    <small class="text-muted"><?= TOKO_ALAMAT ?></small>
                </div>
            </div>

            <div class="d-flex align-items-start mb-3 gap-3">
                <div class="text-danger fs-4"><i class="bi bi-phone"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">WhatsApp / No. Telp</h6>
                    <small class="text-muted"><a href="https://wa.me/<?= WA_NUMBER ?>">0895-3797-88123</a></small>
                </div>
            </div>

            <div class="d-flex align-items-start gap-3">
                <div class="text-danger fs-4"><i class="bi bi-envelope"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">Email</h6>
                    <small class="text-muted">halo@pempek.com</small>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow rounded-4 p-4 bg-white">
                <h5 class="fw-bold text-dark mb-3">Kirim Masukan</h5>
                <?php if ($success): ?>
                    <div class="alert alert-success py-2"><?= htmlspecialchars($success) ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <?= get_csrf_input() ?>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama</label>
                        <input type="text" name="nama" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email / No. HP</label>
                        <input type="text" name="kontak" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pesan</label>
                        <textarea name="pesan" class="form-control rounded-3" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 rounded-3 py-3 fw-bold">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'views/templates/footer.php'; ?>
