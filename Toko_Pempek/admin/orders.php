<?php
session_start();
require_once '../config/database.php';
require_once '../core/Auth.php';
require_login();

generate_csrf_token();

// Konfirmasi / update status pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    csrf_validate_post();
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $allowed = ['menunggu', 'dikonfirmasi', 'diproses', 'siap', 'diambil', 'batal'];
    if (in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE pesanan SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $id]);
        $success = "Status pesanan #$id berhasil diperbarui!";
    }
}

// Hapus pesanan (hanya owner)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && is_owner()) {
    csrf_validate_post();
    $id = intval($_POST['id']);
    $conn->prepare("DELETE FROM pesanan_detail WHERE pesanan_id = ?")->execute([$id]);
    $conn->prepare("DELETE FROM pesanan WHERE id = ?")->execute([$id]);
    $success = "Pesanan #$id berhasil dihapus!";
}

// Filter
$filter = $_GET['status'] ?? '';
$allowed_statuses = ['menunggu', 'dikonfirmasi', 'diproses', 'siap', 'diambil', 'batal'];
$orders = [];
if ($filter && in_array($filter, $allowed_statuses)) {
    $stmt = $conn->prepare("
        SELECT p.*, COUNT(pd.id) as item_count 
        FROM pesanan p 
        LEFT JOIN pesanan_detail pd ON pd.pesanan_id = p.id 
        WHERE p.status = ?
        GROUP BY p.id 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$filter]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $filter = '';
    $orders = $conn->query("
        SELECT p.*, COUNT(pd.id) as item_count 
        FROM pesanan p 
        LEFT JOIN pesanan_detail pd ON pd.pesanan_id = p.id 
        GROUP BY p.id 
        ORDER BY p.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

// Detail pesanan (modal)
$detail = null;
if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM pesanan WHERE id = ?");
    $stmt->execute([$id]);
    $detail = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($detail) {
        $stmt2 = $conn->prepare("SELECT * FROM pesanan_detail WHERE pesanan_id = ?");
        $stmt2->execute([$id]);
        $detail_items = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
}

$status_list = ['menunggu', 'dikonfirmasi', 'diproses', 'siap', 'diambil', 'batal'];

require_once 'admin_layout.php';
render_admin_header('Pesanan', 'orders');
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Pesanan</h4>
            <small class="text-muted">Kelola semua pesanan customer</small>
        </div>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success py-2"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Filter Status -->
    <div class="mb-3 d-flex gap-2 flex-wrap align-items-center">
        <span class="text-muted small me-1">Filter:</span>
        <a href="orders.php" class="filter-badge <?= !$filter ? 'active' : '' ?>">Semua</a>
        <?php foreach ($status_list as $s): ?>
            <a href="?status=<?= $s ?>" class="filter-badge <?= $filter === $s ? 'active' : '' ?>">
                <?= ucfirst($s) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="card-panel">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Pemesan</th>
                        <th>No. HP</th>
                        <th>Tanggal</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size:40px; opacity:.4;"></i>
                        <p class="mb-0 mt-2">Belum ada pesanan<?= $filter ? " dengan status \"$filter\"" : "" ?></p>
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><span class="fw-bold">#<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                        <td>
                            <div class="fw-medium"><?= htmlspecialchars($o['nama_pemesan']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars(mb_substr($o['alamat'], 0, 30)) ?></small>
                        </td>
                        <td><?= htmlspecialchars($o['no_hp']) ?></td>
                        <td><small><?= format_tanggal($o['created_at']) ?></small></td>
                        <td><span class="badge bg-secondary"><?= $o['item_count'] ?> item</span></td>
                        <td class="fw-bold text-danger"><?= format_rupiah($o['total_harga']) ?></td>
                        <td><span class="badge bg-dark"><?= strtoupper($o['metode_pembayaran']) ?></span></td>
                        <td>
                            <form method="POST" class="d-flex gap-1">
                                <?= get_csrf_input() ?>
                                <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                <select name="status" class="form-select form-select-sm" style="width:120px;" onchange="this.form.submit()">
                                    <?php foreach ($status_list as $s): ?>
                                        <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                        <td>
                            <a href="?action=view&id=<?= $o['id'] ?>" class="btn btn-sm btn-maroon text-white">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if (is_owner()): ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Hapus pesanan ini?');">
                                <?= get_csrf_input() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Pesanan -->
<?php if ($detail): ?>
<div class="modal fade show" id="modalDetail" tabindex="-1" style="display:block; background:rgba(0,0,0,.5);">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header" style="background:#6b2d2d; color:#fff;">
                <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Detail Pesanan #<?= str_pad($detail['id'], 4, '0', STR_PAD_LEFT) ?></h5>
                <a href="orders.php" class="btn-close btn-close-white"></a>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-2"><small class="text-muted">Nama Pemesan</small><div class="fw-medium"><?= htmlspecialchars($detail['nama_pemesan']) ?></div></div>
                        <div class="mb-2"><small class="text-muted">No. HP</small><div class="fw-medium"><?= htmlspecialchars($detail['no_hp']) ?></div></div>
                        <div class="mb-2"><small class="text-muted">Alamat</small><div class="fw-medium"><?= nl2br(htmlspecialchars($detail['alamat'] ?? '-')) ?></div></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2"><small class="text-muted">Waktu Pesan</small><div class="fw-medium"><?= format_tanggal($detail['created_at']) ?></div></div>
                        <div class="mb-2"><small class="text-muted">Pembayaran</small><div class="fw-medium"><span class="badge bg-dark"><?= strtoupper($detail['metode_pembayaran']) ?></span></div></div>
                        <div class="mb-2"><small class="text-muted">Catatan</small><div class="fw-medium text-muted"><?= nl2br(htmlspecialchars($detail['catatan'] ?? '-')) ?></div></div>
                    </div>
                </div>
                <hr>
                <h6 class="fw-bold mb-3"><i class="bi bi-list-ul me-1"></i>Daftar Pesanan</h6>
                <table class="table table-sm">
                    <thead><tr><th>Menu</th><th class="text-center">Jumlah</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                    <?php foreach ($detail_items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                            <td class="text-center"><?= $item['jumlah'] ?></td>
                            <td class="text-end"><?= format_rupiah($item['harga_saat_pesan']) ?></td>
                            <td class="text-end fw-bold"><?= format_rupiah($item['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-dark">
                        <td colspan="3" class="text-end fw-bold">TOTAL</td>
                        <td class="text-end fw-bold text-danger"><?= format_rupiah($detail['total_harga']) ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <a href="orders.php" class="btn btn-secondary">Tutup</a>
                <form method="POST" class="d-flex gap-2">
                    <?= get_csrf_input() ?>
                    <input type="hidden" name="id" value="<?= $detail['id'] ?>">
                    <select name="status" class="form-select">
                        <?php foreach ($status_list as $s): ?>
                            <option value="<?= $s ?>" <?= $detail['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="update_status" value="1">
                    <button type="submit" class="btn btn-maroon">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>document.addEventListener('DOMContentLoaded', () => { if (document.getElementById('modalDetail')) { document.getElementById('modalDetail').style.display = 'block'; } });</script>
<?php endif; ?>

<?php
render_admin_footer();
?>