<?php
/**
 * Header Admin Shared Component
 */
function render_admin_header($title, $active_menu) {
    $level = get_admin_level();
    $name = get_admin_name();
    $is_owner = is_owner();
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?> - Admin Panel</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <style>
            body { background: #f5f0ee; }
            .sidebar { background: #4a1a1a; min-height: 100vh; position: fixed; width: 240px; top: 0; left: 0; padding-top: 20px; z-index: 1000; transition: transform 0.3s ease; }
            .sidebar .nav-link { color: rgba(255,255,255,0.75); padding: 12px 20px; border-radius: 8px; margin: 2px 10px; font-size: 14px; text-decoration: none; display: block; }
            .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.15); color: #fff; }
            .sidebar .brand { color: #fff; font-size: 18px; font-weight: bold; padding: 10px 20px 30px; display: block; text-align: center; }
            .main-content { margin-left: 240px; padding: 30px; transition: margin-left 0.3s ease; }
            .admin-navbar { display: none; background: #4a1a1a; color: #fff; }
            .card-panel { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
            .btn-maroon { background: #6b2d2d; color: #fff; border: none; }
            .btn-maroon:hover { background: #4a1a1a; color: #fff; }
            .btn-outline-maroon { color: #6b2d2d; border-color: #6b2d2d; }
            .btn-outline-maroon:hover { background: #6b2d2d; color: #fff; }
            @media(max-width: 991.98px) {
                .sidebar { transform: translateX(-100%); }
                .sidebar.show { transform: translateX(0); }
                .main-content { margin-left: 0; padding: 20px; padding-top: 80px; }
                .admin-navbar { display: flex; position: fixed; top: 0; left: 0; right: 0; height: 60px; z-index: 999; align-items: center; padding: 0 20px; }
            }
        </style>
    </head>
    <body>

    <!-- Mobile Navbar -->
    <header class="admin-navbar shadow-sm justify-content-between">
        <button class="btn text-white fs-4 p-0" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <span class="fw-bold">Pempek Wong Kito Admin</span>
        <div style="width: 24px;"></div>
    </header>

    <!-- Sidebar -->
    <nav class="sidebar" id="adminSidebar">
        <div class="brand"><i class="bi bi-shop me-2"></i>Pempek Wong Kito</div>
        <div class="px-3 mb-3"><small class="text-white-50 text-uppercase" style="font-size:11px;">Menu</small></div>
        <div class="nav flex-column">
            <a href="dashboard.php" class="nav-link <?= $active_menu === 'dashboard' ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            <a href="produk.php" class="nav-link <?= $active_menu === 'produk' ? 'active' : '' ?>"><i class="bi bi-box-seam me-2"></i>Produk / Menu</a>
            <a href="orders.php" class="nav-link <?= $active_menu === 'orders' ? 'active' : '' ?>"><i class="bi bi-receipt me-2"></i>Pesanan</a>
            <?php if ($is_owner): ?>
            <a href="users.php" class="nav-link <?= $active_menu === 'users' ? 'active' : '' ?>"><i class="bi bi-people me-2"></i>Pengguna</a>
            <?php endif; ?>
        </div>
        <div class="mt-4 px-3"><small class="text-white-50 text-uppercase" style="font-size:11px;">Akun</small></div>
        <div class="nav flex-column">
            <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </div>
        <div class="px-3 mt-4">
            <small class="text-white-50">
                <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($name) ?>
                <br><span class="badge bg-light text-dark mt-1"><?= ucfirst($level) ?></span>
            </small>
        </div>
    </nav>

    <div class="main-content">
    <?php
}

function render_admin_footer() {
    ?>
    </div> <!-- Close main-content -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('adminSidebar');
            if (toggle && sidebar) {
                toggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                });
                document.addEventListener('click', (e) => {
                    if (!sidebar.contains(e.target) && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>
    </body>
    </html>
    <?php
}
