<div class="bg-maroon text-white position-relative overflow-hidden mb-5 shadow-sm" style="min-height: 480px; display: flex; align-items: center;">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 text-start">
                <h1 class="display-3 fw-bold mb-3 text-white" style="font-family: 'Playfair Display', serif; line-height: 1.1;">
                    Cita Rasa Asli <br><span class="text-white">Palembang</span>
                </h1>
                <p class="lead text-white-50 fs-4 mb-0" style="font-family: 'Playfair Display', serif; font-style: italic;">
                    Lemak Nian Pas di Lidah!
                </p>
            </div>
            <div class="col-lg-7 text-center text-lg-end position-relative">
                <img src="assets/banner.jpg" 
                     alt="Pempek Plate" 
                     class="img-fluid object-fit-cover shadow-lg border border-4 border-opacity-10 border-white" 
                     style="width: 520px; height: 380px; border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;">
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">Kategori</h5>
                
                <form action="index.php" method="GET" id="filterForm">
                    <div class="d-flex flex-column gap-3">
                        <?php
                        if (!function_exists('check_active')) {
                            function check_active($value) {
                                if (isset($_GET['kat']) && is_array($_GET['kat']) && in_array($value, $_GET['kat'])) {
                                    echo 'checked';
                                }
                            }
                        }
                        ?>
                        <div class="form-check">
                            <input class="form-check-input custom-checkbox" type="checkbox" name="kat[]" value="Kapal Selam" id="kat1" <?= check_active('Kapal Selam'); ?>>
                            <label class="form-check-label text-secondary fw-medium" for="kat1">Pempek Kapal Selam</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input custom-checkbox" type="checkbox" name="kat[]" value="Besar" id="kat2" <?= check_active('Besar'); ?>>
                            <label class="form-check-label text-secondary fw-medium" for="kat2">Besar</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input custom-checkbox" type="checkbox" name="kat[]" value="Adaan" id="kat3" <?= check_active('Adaan'); ?>>
                            <label class="form-check-label text-secondary fw-medium" for="kat3">Adaan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input custom-checkbox" type="checkbox" name="kat[]" value="Lenjer" id="kat4" <?= check_active('Lenjer'); ?>>
                            <label class="form-check-label text-secondary fw-medium" for="kat4">Lenjer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input custom-checkbox" type="checkbox" name="kat[]" value="Kulit" id="kat5" <?= check_active('Kulit'); ?>>
                            <label class="form-check-label text-secondary fw-medium" for="kat5">Kulit</label>
                        </div>
                        
                        <div class="form-check border-top pt-2">
                            <input class="form-check-input custom-checkbox" type="checkbox" name="kat[]" value="Paket Hemat" id="kat6" <?= check_active('Paket Hemat'); ?>>
                            <label class="form-check-label text-success fw-bold" for="kat6">🎁 Paket Hemat</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="row g-4">
                <?php if (empty($produk_list)): ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted fs-5">Menu yang Anda cari tidak ditemukan 😢</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($produk_list as $produk): ?>
                        <?php 
                        $kategori_dipilih = isset($_GET['kat']) && is_array($_GET['kat']) ? $_GET['kat'] : [];

                        if (strpos(strtolower($produk['nama']), 'paket keluarga') !== false || strpos(strtolower($produk['nama']), 'paket hemat') !== false) {
                            if (!in_array('Paket Hemat', $kategori_dipilih)) {
                                continue; 
                            }
                        }

                        $gambar_tampil = $produk['foto']; 

                                                if (strpos(strtolower($produk['nama']), 'kapal selam') !== false) {
                                                    $gambar_tampil = "assets/kapal_selam.jpeg"; 
                                                } 
                                                elseif (strpos(strtolower($produk['nama']), 'lenjer') !== false) {
                                                    $gambar_tampil = "assets/pempek_lenjer.jpg"; 
                                                } 
                                                elseif (strpos(strtolower($produk['nama']), 'adaan') !== false) {
                                                    $gambar_tampil = "assets/pempek_adaan.jpg"; 
                                                } 
                                                elseif (strpos(strtolower($produk['nama']), 'kulit') !== false) {
                                                    $gambar_tampil = "assets/pempek_kulit.jpg"; 
                                                }
                                                elseif (strpos(strtolower($produk['nama']), 'lenggang') !== false) {
                                                    $gambar_tampil = "assets/pempek_lenggang.jpg"; 
                                                }
                                                elseif (strpos(strtolower($produk['nama']), 'keriting') !== false) {
                                                    $gambar_tampil = "assets/pempek_keriting.jpg"; 
                                                }
                                                elseif (strpos(strtolower($produk['nama']), 'paket hemat') !== false || strpos(strtolower($produk['nama']), 'paket keluarga') !== false) {
                                                    $gambar_tampil = "assets/paket_hemat.jpg"; 
                                                }
                                                ?>

                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-menu bg-white">
                                <div class="overflow-hidden" style="height: 180px; background-color: #f8f9fa;">
                                    <img src="<?= htmlspecialchars($gambar_tampil); ?>" class="w-100 h-100 object-fit-cover transition-img" alt="<?= htmlspecialchars($produk['nama']); ?>">
                                </div>
                                
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="<?= htmlspecialchars($produk['nama']); ?>">
                                            <?= htmlspecialchars($produk['nama']); ?>
                                        </h6>
                                        <p class="text-muted small text-truncate mb-2">Ikan Tenggiri Asli...</p>
                                        <h6 class="text-danger fw-bold mb-3">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></h6>
                                    </div>
                                    
                                    <div class="row g-2 mt-auto">
                                        <div class="col-5">
                                            <button class="btn btn-maroon btn-sm w-100 text-white rounded-3 py-2 fw-medium" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#detailModal<?= $produk['id']; ?>">Pesan</button>
                                        </div>
                                        <div class="col-7">
                                            <a href="index.php?action=add&id=<?= $produk['id']; ?>" class="btn btn-gold btn-sm w-100 rounded-3 py-2 fw-medium d-flex align-items-center justify-content-center gap-1" style="font-size: 12px;">
                                                Add to Cart 🛒
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="detailModal<?= $produk['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow-lg">
                                    <div class="modal-header border-0 bg-light p-3 px-4">
                                        <h5 class="modal-title fw-bold text-dark">Detail Menu</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <img src="<?= htmlspecialchars($gambar_tampil); ?>" class="img-fluid rounded-4 mb-3 w-100 object-fit-cover shadow-sm" style="height: 250px;" alt="<?= htmlspecialchars($produk['nama']); ?>">
                                        <h4 class="fw-bold text-dark mb-2"><?= htmlspecialchars($produk['nama']); ?></h4>
                                        <p class="text-muted small"><?= htmlspecialchars($produk['deskripsi']); ?></p>
                                        <hr class="text-muted opacity-25 my-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="fw-bold text-danger m-0">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></h4>
                                            <a href="index.php?action=add&id=<?= $produk['id']; ?>" class="btn btn-gold fw-bold px-4 py-2 rounded-3">Masukkan Keranjang</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Pagination Widget -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-5 d-flex justify-content-center">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?p=<?= $page - 1 ?><?= isset($_GET['kat']) ? '&' . http_build_query(['kat' => $_GET['kat']]) : '' ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?p=<?= $i ?><?= isset($_GET['kat']) ? '&' . http_build_query(['kat' => $_GET['kat']]) : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?p=<?= $page + 1 ?><?= isset($_GET['kat']) ? '&' . http_build_query(['kat' => $_GET['kat']]) : '' ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>

    </div>
</div>

<style>
    .btn-maroon {
        background-color: #6b0b1a;
        border: none;
    }
    .btn-maroon:hover {
        background-color: #520611;
    }
    .btn-gold {
        background-color: #d4af37;
        color: #fff;
        border: none;
    }
    .btn-gold:hover {
        background-color: #aa8c2c;
        color: #fff;
    }
    .custom-checkbox:checked {
        background-color: #6b0b1a;
        border-color: #6b0b1a;
    }
    .card-menu {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .card-menu:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,0.08) !important;
    }
    .transition-img {
        transition: transform 0.4s ease;
    }
    .card-menu:hover .transition-img {
        transform: scale(1.05);
    }
</style>

<script>
    const filterForm = document.getElementById('filterForm');
    const checkboxes = filterForm.querySelectorAll('.custom-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            sessionStorage.setItem('scrollPosition', window.scrollY);
            filterForm.submit();
        });
    });

    window.addEventListener('load', () => {
        const savedPosition = sessionStorage.getItem('scrollPosition');
        if (savedPosition) {
            window.scrollTo({
                top: parseInt(savedPosition),
                behavior: 'instant'
            });
            sessionStorage.removeItem('scrollPosition');
        }
    });
</script>