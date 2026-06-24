<?php
session_start();
require_once '../config/database.php';
require_once '../core/Functions.php';
require_once '../core/Auth.php';
require_login();

// Statistik
$total_produk = $conn->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$produk_tersedia = $conn->query("SELECT COUNT(*) FROM produk WHERE status = 'tersedia'")->fetchColumn();
$pesanan_baru = $conn->query("SELECT COUNT(*) FROM pesanan WHERE status = 'menunggu'")->fetchColumn();
$total_pesanan = $conn->query("SELECT COUNT(*) FROM pesanan")->fetchColumn();
$total_pendapatan = $conn->query("SELECT COALESCE(SUM(total_harga), 0) FROM pesanan WHERE status != 'batal'")->fetchColumn();
$pesanan_hari_ini = $conn->query("SELECT COUNT(*) FROM pesanan WHERE DATE(created_at) = CURDATE()")->fetchColumn();

// Pesanan terbaru
$stmt_pesanan = $conn->query("
    SELECT p.*, COUNT(pd.id) as item_count 
    FROM pesanan p 
    LEFT JOIN pesanan_detail pd ON pd.pesanan_id = p.id 
    GROUP BY p.id 
    ORDER BY p.created_at DESC LIMIT 10
");
$pesanan_terbaru = $stmt_pesanan->fetchAll(PDO::FETCH_ASSOC);

require_once 'admin_layout.php';
render_admin_header('Dashboard', 'dashboard');
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Dashboard</h4>
            <small class="text-muted">Selamat datang, <?= htmlspecialchars(get_admin_name()) ?>!</small>
        </div>
        <div>
            <span class="text-muted small"><i class="bi bi-calendar3"></i> <?= date('d M Y') ?></span>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card produk">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="opacity-75">Total Menu</small>
                        <h2 class="mb-0 fw-bold"><?= $total_produk ?></h2>
                        <small class="opacity-75"><?= $produk_tersedia ?> tersedia</small>
                    </div>
                    <div class="opacity-50"><i class="bi bi-box-seam" style="font-size: 40px;"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card pesanan">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="opacity-75">Pesanan Baru</small>
                        <h2 class="mb-0 fw-bold"><?= $pesanan_baru ?></h2>
                        <small class="opacity-75">menunggu konfirmasi</small>
                    </div>
                    <div class="opacity-50"><i class="bi bi-bell" style="font-size: 40px;"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card pendapatan">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="opacity-75">Total Pendapatan</small>
                        <h2 class="mb-1 fw-bold" style="font-size: 22px;"><?= format_rupiah($total_pendapatan) ?></h2>
                        <small class="opacity-75"><?= $total_pesanan ?> pesanan</small>
                    </div>
                    <div class="opacity-50"><i class="bi bi-currency-dollar" style="font-size: 40px;"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card hariini">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="opacity-75">Pesanan Hari Ini</small>
                        <h2 class="mb-0 fw-bold"><?= $pesanan_hari_ini ?></h2>
                        <small class="opacity-75"><?= date('d M Y') ?></small>
                    </div>
                    <div class="opacity-50"><i class="bi bi-calendar-day" style="font-size: 40px;"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pesanan Terbaru -->
    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Pesanan Terbaru</h6>
            <a href="orders.php" class="btn btn-sm btn-maroon text-white">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Pemesan</th>
                        <th>Tanggal</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($pesanan_terbaru)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pesanan</td></tr>
                <?php else: ?>
                    <?php foreach ($pesanan_terbaru as $p): ?>
                    <tr>
                        <td><span class="fw-bold">#<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                        <td>
                            <div class="fw-medium"><?= htmlspecialchars($p['nama_pemesan']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($p['no_hp']) ?></small>
                        </td>
                        <td><small><?= format_tanggal($p['created_at']) ?></small></td>
                        <td><span class="badge bg-secondary"><?= $p['item_count'] ?> item</span></td>
                        <td class="fw-bold text-danger"><?= format_rupiah($p['total_harga']) ?></td>
                        <td><span class="badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                        <td>
                            <a href="orders.php?action=view&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<style>
.badge-menunggu { background: #e74c3c; }
.badge-dikonfirmasi { background: #3498db; }
.badge-diproses { background: #f39c12; }
.badge-siap { background: #27ae60; }
.badge-diambil { background: #2ecc71; }
.badge-batal { background: #95a5a6; }
.btn-maroon { background: #6b2d2d; color: #fff; border: none; }
.btn-maroon:hover { background: #4a1a1a; color: #fff; }
.stat-card { border-radius: 12px; padding: 24px; color: #fff; }
.stat-card.produk { background: linear-gradient(135deg, #6b2d2d, #8b3a3a); }
.stat-card.pesanan { background: linear-gradient(135deg, #2d6b4a, #3a8b5a); }
.stat-card.pendapatan { background: linear-gradient(135deg, #b8860b, #daa520); }
.stat-card.hariini { background: linear-gradient(135deg, #1a4a6b, #2a6a8b); }
.table-container { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
</style>

<?php
render_admin_footer();
?>
