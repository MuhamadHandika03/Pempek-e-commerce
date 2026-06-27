<?php
session_start();
require_once 'config/database.php';
require_once 'config/app.php';
require_once 'core/Functions.php';
require_once 'core/Auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';
$tab = $_GET['tab'] ?? 'profil';

// Update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    csrf_validate_post();
    $nama  = trim($_POST['nama'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    if (empty($nama)) {
        $error = 'Nama tidak boleh kosong.';
    } else {
        $stmt = $conn->prepare("UPDATE pelanggan SET nama=?, no_hp=?, alamat=? WHERE id=?");
        if ($stmt->execute([$nama, $no_hp, $alamat, $user_id])) {
            $_SESSION['user_nama'] = $nama;
            $success = 'Profil berhasil diperbarui!';
        } else {
            $error = 'Gagal memperbarui profil.';
        }
    }
}

// Ambil data user
$stmt = $conn->prepare("SELECT * FROM pelanggan WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil history pesanan
$stmt = $conn->prepare("SELECT * FROM pesanan WHERE pelanggan_id=? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil detail item untuk setiap pesanan
$order_items = [];
if (!empty($orders)) {
    $ids = array_column($orders, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM pesanan_detail WHERE pesanan_id IN ($placeholders) ORDER BY id");
    $stmt->execute($ids);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($items as $it) {
        $order_items[$it['pesanan_id']][] = $it;
    }
}

$status_label = [
    'menunggu' => 'warning',
    'dikonfirmasi' => 'info',
    'diproses' => 'primary',
    'siap' => 'success',
    'diambil' => 'secondary',
    'batal' => 'danger'
];

$status_icon = [
    'menunggu' => 'bi-clock',
    'dikonfirmasi' => 'bi-check-circle',
    'diproses' => 'bi-gear',
    'siap' => 'bi-bag-check',
    'diambil' => 'bi-hand-thumbs-up',
    'batal' => 'bi-x-circle'
];

// Urutan status untuk progress bar
$status_order = ['menunggu', 'dikonfirmasi', 'diproses', 'siap', 'diambil'];
$status_text = [
    'menunggu' => 'Menunggu',
    'dikonfirmasi' => 'Dikonfirmasi',
    'diproses' => 'Diproses',
    'siap' => 'Siap Diambil',
    'diambil' => 'Selesai',
    'batal' => 'Dibatalkan'
];

$title = "Profil Saya - " . TOKO_NAME;
include 'views/templates/header.php';
?>
<style>
.progress-step { display: flex; align-items: center; justify-content: space-between; position: relative; }
.progress-step .step { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 1; flex: 1; }
.progress-step .step .circle { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 2px solid #dee2e6; background: #fff; color: #adb5bd; transition: .3s; }
.progress-step .step.active .circle { background: #6b0b1a; border-color: #6b0b1a; color: #fff; }
.progress-step .step.done .circle { background: #198754; border-color: #198754; color: #fff; }
.progress-step .step.batal .circle { background: #dc3545; border-color: #dc3545; color: #fff; }
.progress-step .step .label { font-size: 11px; margin-top: 4px; color: #adb5bd; font-weight: 500; text-align: center; }
.progress-step .step.active .label { color: #6b0b1a; font-weight: 700; }
.progress-step .step.done .label { color: #198754; }
.progress-step .step.batal .label { color: #dc3545; }
.progress-step .line { flex: 1; height: 2px; background: #dee2e6; margin: 0 -1px; z-index: 0; }
.progress-step .line.done { background: #198754; }
.progress-step .line.active { background: #6b0b1a; }
.progress-step .line.batal { background: #dc3545; }
.nav-link.active { background: rgba(220, 53, 69, .08); border-radius: .5rem; }
.order-card { transition: .2s; }
.order-card:hover { box-shadow: 0 .25rem 1rem rgba(0,0,0,.08); }
.detail-toggle { cursor: pointer; user-select: none; }
.detail-toggle:hover { color: #dc3545; }
</style>

<div class="container py-4">
    <div class="row">
        <!-- Sidebar Profil -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center" style="width:70px;height:70px;font-size:28px;">
                        <?= strtoupper(substr($user['nama'] ?? 'U', 0, 1)) ?>
                    </div>
                    <h6 class="fw-bold mt-2 mb-0"><?= htmlspecialchars($user['nama']) ?></h6>
                    <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                </div>
                <hr>
                <nav class="nav flex-column">
                    <a href="?tab=profil" class="nav-link <?= $tab === 'profil' ? 'active fw-bold text-danger' : 'text-dark' ?>">
                        <i class="bi bi-person me-2"></i> Profil Saya
                    </a>
                    <a href="?tab=pesanan" class="nav-link <?= $tab === 'pesanan' ? 'active fw-bold text-danger' : 'text-dark' ?>">
                        <i class="bi bi-receipt me-2"></i> Riwayat Pesanan (<?= count($orders) ?>)
                    </a>
                    <a href="logout.php" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </nav>
            </div>
        </div>

        <!-- Konten -->
        <div class="col-lg-9">
            <?php if ($tab === 'profil'): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2"></i>Edit Profil</h5>
                    <?php if ($success): ?><div class="alert alert-success py-2"><?= htmlspecialchars($success) ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                    <form method="POST">
                        <?= get_csrf_input() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                            <small class="text-muted">Email tidak dapat diubah</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">No. WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($user['no_hp'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Pengiriman</label>
                            <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-danger px-4">Simpan Perubahan</button>
                    </form>
                </div>

            <?php elseif ($tab === 'pesanan'): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-receipt me-2"></i>Riwayat Pesanan</h5>
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size:48px; opacity:.3;"></i>
                            <p class="text-muted mt-2">Belum ada pesanan.</p>
                            <a href="index.php" class="btn btn-danger rounded-pill px-4">Mulai Belanja</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orders as $o): 
                            $items = $order_items[$o['id']] ?? [];
                            $st = $o['status'];
                            $is_batal = $st === 'batal';
                        ?>
                            <div class="border rounded-3 p-3 mb-3 order-card">
                                <!-- Header -->
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <span class="fw-bold fs-6">#<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                        <span class="badge bg-<?= $status_label[$st] ?? 'secondary' ?> ms-2">
                                            <i class="<?= $status_icon[$st] ?? 'bi-question' ?> me-1"></i>
                                            <?= $status_text[$st] ?? ucfirst($st) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted"><?= format_tanggal($o['created_at']) ?></small>
                                </div>

                                <!-- Ringkasan -->
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="text-muted small">
                                        <?= count($items) ?> item &middot;
                                        <?= $o['metode_pembayaran'] === 'cod' ? 'Bayar di Tempat' : ($o['metode_pembayaran'] === 'qris' ? 'QRIS' : 'Transfer') ?> &middot;
                                        <?= $o['metode_pengiriman'] === 'diantar' ? 'Di antar' : 'Ambil sendiri' ?>
                                    </span>
                                    <span class="fw-bold text-danger"><?= format_rupiah($o['total_harga']) ?></span>
                                </div>

                                <!-- Tombol detail & link WA -->
                                <div class="d-flex gap-2 mt-2">
                                    <a class="detail-toggle text-decoration-none small fw-bold text-danger" data-bs-toggle="collapse" href="#detail-<?= $o['id'] ?>">
                                        <i class="bi bi-chevron-down"></i> Lihat Detail
                                    </a>
                                    <?php if ($st !== 'diambil' && $st !== 'batal'): ?>
                                        <a href="https://wa.me/<?= WA_NUMBER ?>?text=Halo%20kak,%20saya%20ingin%20tanya%20soal%20pesanan%20%23<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?>" target="_blank" class="small text-success text-decoration-none fw-bold ms-auto">
                                            <i class="bi bi-whatsapp"></i> Hubungi
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Detail & Tracking -->
                                <div class="collapse mt-3" id="detail-<?= $o['id'] ?>">
                                    <!-- Progress bar -->
                                    <?php if ($is_batal): ?>
                                        <div class="alert alert-danger py-2 small mb-3">
                                            <i class="bi bi-x-circle me-1"></i> Pesanan ini telah dibatalkan.
                                        </div>
                                    <?php else: ?>
                                        <div class="progress-step mb-3">
                                            <?php foreach ($status_order as $i => $s): 
                                                $idx = array_search($st, $status_order);
                                                $cls = $idx === false ? '' : ($idx > $i ? 'done' : ($idx === $i ? 'active' : ''));
                                                if ($idx === false && $st === 'diambil') $cls = 'done';
                                            ?>
                                                <?php if ($i > 0): ?>
                                                    <div class="line <?= $cls ?>"></div>
                                                <?php endif; ?>
                                                <div class="step <?= $cls ?>">
                                                    <div class="circle">
                                                        <i class="<?= $status_icon[$s] ?>"></i>
                                                    </div>
                                                    <div class="label"><?= $status_text[$s] ?></div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Item list -->
                                    <?php if (!empty($items)): ?>
                                        <table class="table table-sm table-borderless mb-2 small">
                                            <thead><tr class="text-muted"><th>Produk</th><th class="text-center">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr></thead>
                                            <tbody>
                                                <?php foreach ($items as $it): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($it['nama_produk']) ?></td>
                                                        <td class="text-center"><?= (int)$it['jumlah'] ?></td>
                                                        <td class="text-end"><?= format_rupiah($it['harga_saat_pesan']) ?></td>
                                                        <td class="text-end fw-bold"><?= format_rupiah($it['subtotal']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr><td colspan="3" class="text-end fw-bold">Total</td><td class="text-end fw-bold text-danger"><?= format_rupiah($o['total_harga']) ?></td></tr>
                                            </tfoot>
                                        </table>
                                    <?php endif; ?>

                                    <!-- Info tambahan -->
                                    <div class="row small text-muted">
                                        <div class="col-6">
                                            <div><strong>Nama:</strong> <?= htmlspecialchars($o['nama_pemesan']) ?></div>
                                            <div><strong>No. HP:</strong> <?= htmlspecialchars($o['no_hp']) ?></div>
                                            <div><strong>Alamat:</strong> <?= htmlspecialchars($o['alamat']) ?></div>
                                        </div>
                                        <div class="col-6">
                                            <div><strong>Bayar:</strong> <?= $o['metode_pembayaran'] ?></div>
                                            <div><strong>Kirim:</strong> <?= $o['metode_pengiriman'] ?></div>
                                            <?php if ($o['catatan']): ?><div><strong>Catatan:</strong> <?= htmlspecialchars($o['catatan']) ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'views/templates/footer.php'; ?>
