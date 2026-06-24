<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pempek Wong Kito'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;800&family=Plus+Jakarta+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fcf8f5; /* Background agak krem hangat seperti gambar */
        }
        .bg-maroon {
            background-color: #6b0b1a !important; /* Warna marun sesuai gambar */
        }
        .text-gold {
            color: #f1b815 !important;
        }
        .btn-gold {
            background-color: #f1b815;
            color: #000;
            font-weight: bold;
            border: none;
        }
        .btn-gold:hover {
            background-color: #d6a10f;
            color: #000;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-maroon py-3 sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-white" href="index.php">
                <span class="me-2">🍢</span> Pempek Wong Kito
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto gap-3 text-white">
                    <li class="nav-item"><a class="nav-link text-white active fw-medium" href="index.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="index.php?kat[]=Paket+Hemat">Paket Hemat</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="about.php">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50" href="contact.php">Kontak</a></li>
                </ul>
                <div class="ms-auto d-flex gap-2 align-items-center">
                    <a href="keranjang.php" class="btn btn-gold rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                        Keranjang Belanja 🛒 (<?= hitung_total_item(); ?>)
                    </a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle rounded-3 px-3 py-2" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['user_nama']) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><a class="dropdown-item py-2" href="logout.php"><i class="bi bi-box-arrow-right me-2 text-danger"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light rounded-3 px-3 py-2">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    <?php endif; ?>
                    
                    <a href="admin/index.php" class="btn btn-outline-light rounded-3 px-3 py-2" title="Admin Panel">
                        <i class="bi bi-person-lock"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>