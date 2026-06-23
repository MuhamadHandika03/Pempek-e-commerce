<?php
session_start();
require_once 'config/database.php';
require_once 'core/Functions.php';

$title = "Hubungi Kami - Pempek Wong Kito";
include 'views/templates/header.php';
?>

<div class="container py-5 my-4">
    <div class="row g-5">
        <div class="col-lg-5">
            <h6 class="text-danger fw-bold text-uppercase tracking-wider">Kontak</h6>
            <h2 class="display-6 fw-bold text-dark mb-4">Hubungi Kami</h2>
            <p class="text-muted mb-4">Ada pertanyaan tentang menu, pemesanan katering, atau pengiriman? Staf kami siap melayani Anda.</p>
            
            <div class="d-flex align-items-start mb-3 gap-3">
                <div class="text-danger fs-4"><i class="bi bi-geo-alt"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">Alamat Outlet</h6>
                    <small class="text-muted">Jl. Jenderal Sudirman No. 123, Palembang, Sumatera Selatan</small>
                </div>
            </div>
            
            <div class="d-flex align-items-start mb-3 gap-3">
                <div class="text-danger fs-4"><i class="bi bi-phone"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">WhatsApp / No. Telp</h6>
                    <small class="text-muted">0895-3797-88123</small>
                </div>
            </div>

            <div class="d-flex align-items-start gap-3">
                <div class="text-danger fs-4"><i class="bi bi-envelope"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">E-mail</h6>
                    <small class="text-muted">halo@pempecwongkito.com</small>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow rounded-4 p-4 bg-white">
                <h5 class="fw-bold text-dark mb-3">Kirim Masukan</h5>
                <form action="#" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama</label>
                        <input type="text" class="form-control rounded-3" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">E-mail / No. HP</label>
                        <input type="text" class="form-control rounded-3" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pesan</label>
                        <textarea class="form-control rounded-3" rows="4" required></textarea>
                    </div>
                    <button class="btn btn-danger w-100 rounded-3 py-3 fw-bold">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'views/templates/footer.php'; ?>
