<?php
session_start();
require_once 'config/database.php';
require_once 'core/Functions.php';

$title = "Tentang Kami - Pempek Wong Kito";
include 'views/templates/header.php';
?>

<div class="container py-5 my-4">
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <h6 class="text-danger fw-bold text-uppercase tracking-wider">Warisan Kuliner</h6>
            <h2 class="display-5 fw-bold text-dark mb-4">Pempek Asli Wong Kito</h2>
            <p class="text-muted leading-relaxed">Berdiri sejak tahun 2018, Pempek Wong Kito menghadirkan cita rasa otentik kuliner khas Palembang langsung ke meja makan Anda. Dibuat dengan bahan baku ikan tenggiri segar pilihan dan resep rahasia keluarga secara turun-temurun.</p>
            <p class="text-muted leading-relaxed">Komitmen kami adalah menyajikan pempek yang tidak hanya lezat, tetapi juga higienis dan menggunakan 100% bahan alami tanpa pengawet. Nikmati kelezatan cuko kental khas kami yang pedas, manis, dan gurihnya pas nian!</p>
        </div>
        <div class="col-lg-6">
            <img src="assets/banner.jpg" class="img-fluid rounded-4 shadow-lg" alt="Tentang Pempek Wong Kito">
        </div>
    </div>
</div>

<?php include 'views/templates/footer.php'; ?>
