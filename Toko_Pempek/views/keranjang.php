<div class="container py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-extrabold text-dark m-0">Keranjang Belanja 🛒</h2>
        <a href="index.php" class="btn btn-outline-dark btn-sm rounded-pill px-3">← Lanjut Pilih Pempek</a>
    </div>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-muted opacity-75">
            <h1 class="display-1">🍱</h1>
            <h4 class="fw-bold text-secondary mt-3">Keranjangmu Masih Kosong nih...</h4>
            <p class="text-muted">Isi dulu dengan pempek selam atau adaan yang lemak nian!</p>
            <a href="index.php" class="btn btn-danger fw-bold px-4 rounded-pill mt-2">Lihat Menu Sekarang</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="bg-white p-4 rounded-4 shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle m-0">
                            <thead>
                                <tr class="text-muted border-bottom small uppercase tracking-wider">
                                    <th scope="col">Menu</th>
                                    <th scope="col" class="text-center">Harga</th>
                                    <th scope="col" class="text-center">Jumlah</th>
                                    <th scope="col" class="text-end">Subtotal</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_belanja = 0;
                                $text_wa = "Halo Pempek Wong Kito, Saya mau pesan:\n\n";
                                
                                foreach ($_SESSION['cart'] as $id => $item): 
                                    $subtotal = $item['harga'] * $item['jumlah'];
                                    $total_belanja += $subtotal;
                                    
                                    // Membuat draf teks pesanan untuk WhatsApp
                                    $text_wa .= "- " . $item['nama'] . " (" . $item['jumlah'] . "x) : " . rupiah($subtotal) . "\n";
                                ?>
                                    <tr class="border-bottom-dashed">
                                        <td class="py-3">
                                            <span class="fw-bold text-dark d-block"><?= htmlspecialchars($item['nama']); ?></span>
                                        </td>
                                        <td class="text-center text-muted"><?= rupiah($item['harga']); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-3 py-2 fs-6 rounded-3"><?= $item['jumlah']; ?></span>
                                        </td>
                                        <td class="text-end fw-bold text-danger"><?= rupiah($subtotal); ?></td>
                                        <td class="text-center">
                                            <a href="keranjang.php?action=delete&id=<?= $id; ?>" class="text-decoration-none text-muted hover-danger fs-5">×</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="keranjang.php?action=clear" class="btn btn-sm btn-link text-muted p-0 text-decoration-none">🗑 Kosongkan Seluruh Keranjang</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="bg-white p-4 rounded-4 shadow-sm border-0 position-sticky" style="top: 20px;">
                    <h5 class="fw-bold text-dark mb-4">Ringkasan Pesanan</h5>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Level Kepedasan Cuko:</label>
                        <select class="form-select border-2 rounded-3" id="cukoLevel">
                            <option value="Pedas Asli Palembang">Pedas Asli Palembang (Mantap)</option>
                            <option value="Sedang Pas">Sedang Pas</option>
                            <option value="Manis/Tidak Pedas">Manis (Tidak Pedas)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Estimasi Berat:</label>
                        <p class="text-dark fw-bold bg-light p-2 rounded-3 small">📦 ~<?= count($_SESSION['cart']) * 0.5; ?> Kg (Termasuk packing & kuah cuko)</p>
                    </div>

                    <hr class="opacity-25">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="text-muted font-weight-medium">Total Pembayaran:</span>
                        <span class="fw-extrabold fs-4 text-danger"><?= rupiah($total_belanja); ?></span>
                    </div>

                    <?php 
                        // Selesaikan pembentukan link teks WA
                        $text_wa .= "\nTotal Belanja: " . rupiah($total_belanja);
                        $link_wa = "https://wa.me/62895379788123?text=" . urlencode($text_wa);
                    ?>
                    
                    <a href="checkout.php" class="btn btn-success w-100 fw-bold py-3 rounded-3 shadow mb-2 d-flex justify-content-center align-items-center gap-2">
                         Proses ke Checkout 🛒
                    </a>
                    <p class="text-center text-muted small m-0" style="font-size: 11px;">Isi data pengiriman & konfirmasi pesanan secara instan.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .border-bottom-dashed {
        border-bottom: 1px dashed #dee2e6;
    }
    .hover-danger:hover {
        color: #dc3545 !important;
    }
</style>