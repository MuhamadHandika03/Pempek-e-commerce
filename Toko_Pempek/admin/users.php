<?php
session_start();
require_once '../config/database.php';
require_once '../core/Auth.php';
require_login();
if (!is_owner()) { header('Location: dashboard.php'); exit; }
generate_csrf_token();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_post();
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $username = trim($_POST['username']);
        $nama = trim($_POST['nama']);
        $password = $_POST['password'];
        $level = $_POST['level'];
        if (strlen($username) < 3) {
            $error = 'Username minimal 3 karakter.';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $conn->prepare("INSERT INTO admin_users (username, password, nama, level) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $hash, $nama, $level]);
                $success = 'Pengguna berhasil ditambahkan!';
            } catch (PDOException $e) {
                $error = 'Username sudah digunakan.';
            }
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        if ($id == $_SESSION['admin_id']) {
            $error = 'Tidak bisa hapus akun sendiri.';
        } else {
            $conn->prepare("DELETE FROM admin_users WHERE id = ?")->execute([$id]);
            $success = 'Pengguna berhasil dihapus!';
        }
    } elseif ($action === 'reset_password') {
        $id = intval($_POST['id']);
        $new_pass = $_POST['new_password'];
        if (strlen($new_pass) < 6) {
            $error = 'Password minimal 6 karakter.';
        } else {
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?")->execute([$hash, $id]);
            $success = 'Password berhasil direset!';
        }
    }
}

$users = $conn->query("SELECT id, username, nama, level, last_login, created_at FROM admin_users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

require_once 'admin_layout.php';
render_admin_header('Pengguna', 'users');
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Pengguna</h4>
            <small class="text-muted">Kelola akun admin & kasir</small>
        </div>
        <button class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
            <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
        </button>
    </div>

    <?php if ($error): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success py-2"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <div class="card-panel">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr><th>No</th><th>Username</th><th>Nama</th><th>Level</th><th>Login Terakhir</th><th>Dibuat</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php foreach ($users as $i => $u): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td class="fw-medium"><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['nama']) ?></td>
                        <td><span class="badge bg-<?= $u['level'] === 'owner' ? 'danger' : 'primary' ?>"><?= ucfirst($u['level']) ?></span></td>
                        <td><small class="text-muted"><?= $u['last_login'] ? format_tanggal($u['last_login']) : '<span class="text-black-50">Belum pernah</span>' ?></small></td>
                        <td><small class="text-muted"><?= format_tanggal($u['created_at']) ?></small></td>
                        <td>
                            <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalReset<?= $u['id'] ?>">
                                <i class="bi bi-key"></i>
                            </button>
                            <?php if ($u['id'] != $_SESSION['admin_id']): ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Hapus pengguna ini?');">
                                <?= get_csrf_input() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header" style="background:#6b2d2d; color:#fff;">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Tambah Pengguna</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?= get_csrf_input() ?>
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Username *</label>
                        <input type="text" name="username" class="form-control" required minlength="3" placeholder="Minimal 3 karakter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Nama Lengkap *</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Password *</label>
                        <input type="password" name="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Level Akses *</label>
                        <select name="level" class="form-select">
                            <option value="kasir">Kasir</option>
                            <option value="owner">Owner</option>
                        </select>
                        <small class="text-muted">Owner = akses penuh, Kasir = manage pesanan & produk</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-maroon"><i class="bi bi-check-circle me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<?php foreach ($users as $u): ?>
<div class="modal fade" id="modalReset<?= $u['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header" style="background:#6b2d2d; color:#fff;">
                <h5 class="modal-title"><i class="bi bi-key me-2"></i>Reset Password - <?= htmlspecialchars($u['username']) ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?= get_csrf_input() ?>
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Password Baru *</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-maroon"><i class="bi bi-check-circle me-1"></i> Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php
render_admin_footer();
?>