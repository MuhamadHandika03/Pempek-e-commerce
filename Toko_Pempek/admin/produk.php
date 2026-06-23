<?php
session_start();
require_once '../config/database.php';
require_once '../core/Auth.php';
require_login();
generate_csrf_token();

// Tambah / Update produk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_post();
    $action = $_POST['action'] ?? '';
    $nama = trim($_POST['nama'] ?? '');
    $kategori = $_POST['kategori'] ?? '';
    $harga = intval($_POST['harga'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status = $_POST['status'] ?? 'tersedia';
    $foto_path = null;

    // Handle upload foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['foto']['tmp_name']);
        finfo_close($finfo);

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (in_array($mime, $allowed)) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('produk_') . '.' . $ext;
            $dest = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                $foto_path = 'assets/uploads/' . $filename;
            }
        }
    }

    if ($action === 'create') {
        $sql = $foto_path ? "INSERT INTO produk (nama, kategori, harga, deskripsi, foto, status) VALUES (?, ?, ?, ?, ?, ?)" : "INSERT INTO produk (nama, kategori, harga, deskripsi, status) VALUES (?, ?, ?, ?, ?)";
        $params = $foto_path ? [$nama, $kategori, $harga, $deskripsi, $foto_path, $status] : [$nama, $kategori, $harga, $deskripsi, $status];
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $success = 'Produk berhasil ditambahkan!';
    } elseif ($action === 'update') {
        $id = intval($_POST['id']);
        if ($foto_path) {
            $stmt = $conn->prepare("UPDATE produk SET nama=?, kategori=?, harga=?, deskripsi=?, foto=?, status=? WHERE id=?");
            $stmt->execute([$nama, $kategori, $harga, $deskripsi, $foto_path, $status, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE produk SET nama=?, kategori=?, harga=?, deskripsi=?, status=? WHERE id=?");
            $stmt->execute([$nama, $kategori, $harga, $deskripsi, $status, $id]);
        }
        $success = 'Produk berhasil diperbarui!';
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM produk WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Produk berhasil dihapus!';
    }
}

// Ambil semua produk
$produk_list = $conn->query("SELECT * FROM produk ORDER BY kategori, nama")->fetchAll(PDO::FETCH_ASSOC);

// Ambil data untuk edit
$edit_produk = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM produk WHERE id = ?");
    $stmt->execute([$id]);
    $edit_produk = $stmt->fetch(PDO::FETCH_ASSOC);
}

$kategori_options = ['Kapal Selam', 'Besar', 'Adaan', 'Lenjer', 'Kulit', 'Lenggang', 'Keriting', 'Paket Hemat'];

require_once 'admin_layout.php';
render_admin_header('Produk / Menu', 'produk');
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Produk / Menu</h4>
            <small class="text-muted">Kelola menu toko Pempek Wong Kito</small>
        </div>
        <button class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle me-1"></i> Tambah Menu
        </button>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success py-2"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card-panel">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($produk_list as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td class="fw-medium"><?= htmlspecialchars($p['nama']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($p['kategori']) ?></span></td>
                        <td class="fw-bold text-danger"><?= format_rupiah($p['harga']) ?></td>
                        <td><span class="badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                        <td><small class="text-muted"><?= htmlspecialchars(mb_substr($p['deskripsi'], 0, 50)) ?><?= (strlen($p['deskripsi']) > 50 ? '...' : '') ?></small></td>
                        <td>
                            <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-warning text-white">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Hapus menu ini?');">
                                <?= get_csrf_input() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($produk_list)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada menu. Tambahkan menu baru!</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header" style="background:#6b2d2d; color:#fff;">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i><?= $edit_produk ? 'Edit Menu' : 'Tambah Menu Baru' ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= get_csrf_input() ?>
                    <input type="hidden" name="action" value="<?= $edit_produk ? 'update' : 'create' ?>">
                    <?php if ($edit_produk): ?>
                        <input type="hidden" name="id" value="<?= $edit_produk['id'] ?>">
                    <?php endif; ?>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-medium">Nama Menu *</label>
                            <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($edit_produk['nama'] ?? '') ?>" placeholder="Contoh: Pempek Kapal Selam Spesial">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Kategori *</label>
                            <select name="kategori" class="form-select" required>
                                <?php foreach ($kategori_options as $kat): ?>
                                    <option value="<?= $kat ?>" <?= (($edit_produk['kategori'] ?? '') === $kat) ? 'selected' : '' ?>><?= $kat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Harga (Rp) *</label>
                            <input type="number" name="harga" class="form-control" required min="0" value="<?= $edit_produk['harga'] ?? '' ?>" placeholder="18000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Status *</label>
                            <select name="status" class="form-select">
                                <option value="tersedia" <?= (($edit_produk['status'] ?? 'tersedia') === 'tersedia') ? 'selected' : '' ?>>Tersedia</option>
                                <option value="habis" <?= (($edit_produk['status'] ?? '') === 'habis') ? 'selected' : '' ?>>Habis</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-medium">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi singkat menu..."><?= htmlspecialchars($edit_produk['deskripsi'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-medium">Foto Menu</label>
                            <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/webp" />
                            <?php if (!empty($edit_produk['foto'])): ?>
                                <small class="text-muted">Foto saat ini: <code><?= htmlspecialchars($edit_produk['foto']) ?></code></small>
                            <?php else: ?>
                                <small class="text-muted">Format: JPG, PNG, WebP. Maks 2MB.</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-maroon">
                        <i class="bi bi-check-circle me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($edit_produk): ?>
<script>document.addEventListener('DOMContentLoaded', () => { new bootstrap.Modal(document.getElementById('modalTambah')).show(); });</script>
<?php endif; ?>
<?php render_admin_footer(); ?>