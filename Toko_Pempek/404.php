<?php
session_start();
require_once 'config/database.php';
require_once 'core/Functions.php';

$title = "Halaman Tidak Ditemukan - Pempek Wong Kito";
include 'views/templates/header.php';
?>

<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="display-1 fw-bold text-danger">404</h1>
            <h3 class="fw-bold text-dark mt-3">Waduh, Halaman Dak Katering! 🍱</h3>
            <p class="text-muted fs-5 mt-2">Halaman yang Anda cari tidak ada atau sudah dipindahkan. Mending kita balik makan pempek kapal selam yang lemak nian!</p>
            <div class="mt-4">
                <a href="index.php" class="btn btn-danger px-4 py-2 rounded-pill fw-bold">Kembali ke Katalog</a>
            </div>
        </div>
    </div>
</div>

<?php include 'views/templates/footer.php'; ?>
