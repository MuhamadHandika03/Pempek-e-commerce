<?php
require_once 'config/database.php';
require_once 'core/Functions.php';
require_once 'core/Auth.php';
generate_csrf_token();

if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = false;
$order_id = 0;
$total_belanja = 0;
foreach ($_SESSION['cart'] as $id => $item) {
    $total_belanja += $item['harga'] * $item['jumlah'];
}

// Auto-fill dari profil jika sudah login
$prefill = ['nama' => '', 'no_hp' => '', 'alamat' => ''];
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT nama, no_hp, alamat FROM pelanggan WHERE id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($profile) {
        $prefill['nama'] = $profile['nama'];
        $prefill['no_hp'] = $profile['no_hp'] ?? '';
        $prefill['alamat'] = $profile['alamat'] ?? '';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_post();
    $nama = trim($_POST['nama_pemesan'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $pembayaran = $_POST['metode_pembayaran'] ?? 'cod';
    $pengiriman = $_POST['pengiriman'] ?? 'diantar';
    $catatan = trim($_POST['catatan'] ?? '');

    if (empty($nama) || empty($no_hp) || empty($alamat)) {
        $error = 'Nama, No. HP, dan Alamat wajib diisi.';
    } elseif (!preg_match('/^(08|628|\+628)[0-9]{8,13}$/', str_replace(' ', '', $no_hp))) {
        $error = 'Format No. HP tidak valid. Gunakan format angka seperti 08123456789 atau 628123456789.';
    } else {
        try {
            $conn->beginTransaction();

            // Insert ke pesanan
            $pelanggan_id = $_SESSION['user_id'] ?? null;
            $stmt = $conn->prepare("
                INSERT INTO pesanan (pelanggan_id, nama_pemesan, no_hp, alamat, metode_pembayaran, metode_pengiriman, status, total_harga, catatan)
                VALUES (?, ?, ?, ?, ?, ?, 'menunggu', ?, ?)
            ");
            $stmt->execute([$pelanggan_id, $nama, $no_hp, $alamat, $pembayaran, $pengiriman, $total_belanja, $catatan]);
            $order_id = $conn->lastInsertId();

            // Insert detail pesanan
            $stmt_detail = $conn->prepare("
                INSERT INTO pesanan_detail (pesanan_id, produk_id, nama_produk, harga_saat_pesan, jumlah, subtotal)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            foreach ($_SESSION['cart'] as $prod_id => $item) {
                $subtotal = $item['harga'] * $item['jumlah'];
                $stmt_detail->execute([
                    $order_id,
                    $prod_id,
                    $item['nama'],
                    $item['harga'],
                    $item['jumlah'],
                    $subtotal
                ]);
            }

            // Simpan backup keranjang untuk WA karena $_SESSION['cart'] akan dikosongkan
            $_SESSION['checkout_cart_backup'] = $_SESSION['cart'];

            $conn->commit();
            
            // Bersihkan keranjang
            $_SESSION['cart'] = [];
            $success = true;

        } catch (Exception $e) {
            $conn->rollBack();
            $error = 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }
    }
}

$title = "Checkout Pesanan - Pempek Wong Kito";
include 'views/templates/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if ($success): ?>
                <div class="card border-0 shadow rounded-4 p-5 text-center bg-white">
                    <h1 class="display-1 text-success mb-4">🎉</h1>
                    <h2 class="fw-bold text-dark">Pesanan Berhasil Dikirim!</h2>
                    <p class="text-muted fs-5">Nomor Pesanan Anda: <span class="fw-bold text-danger">#<?= str_pad($order_id, 4, '0', STR_PAD_LEFT) ?></span></p>
                    <p class="text-secondary">Terima kasih telah memesan di Pempek Wong Kito. Kasir kami akan segera memverifikasi pesanan Anda.</p>
                    
                    <?php
                    // Label metode pembayaran
                    $label_bayar = [
                        'cod' => 'Bayar di Tempat (COD)',
                        'transfer' => 'Transfer Bank',
                        'qris' => 'QRIS / E-Wallet'
                    ];
                    $label_kirim = [
                        'diantar' => 'Di Antar (Delivery)',
                        'ambil_sendiri' => 'Ambil Sendiri (Pickup)'
                    ];
                    $metode_bayar = $label_bayar[$pembayaran] ?? $pembayaran;
                    $metode_kirim = $label_kirim[$pengiriman] ?? $pengiriman;

            // Detail item pesanan untuk WA
            $detail_items = "";
            $cart_data = isset($_SESSION['checkout_cart_backup']) ? $_SESSION['checkout_cart_backup'] : [];
            foreach ($cart_data as $item) {
                $sub = $item['harga'] * $item['jumlah'];
                $detail_items .= "- {$item['nama']} x{$item['jumlah']} = Rp " . number_format($sub, 0, ',', '.') . "\n";
            }

                    $text_wa = "*PEMESANAN WEBSITE - PEMPEK WONG KITO*\n\n";
                    $text_wa .= "*No. Pesanan:* #" . str_pad($order_id, 4, '0', STR_PAD_LEFT) . "\n";
                    $text_wa .= "*Nama:* {$nama}\n";
                    $text_wa .= "*No. HP:* {$no_hp}\n";
                    $text_wa .= "*Alamat:* {$alamat}\n";
                    $text_wa .= "*Metode Kirim:* {$metode_kirim}\n";
                    $text_wa .= "*Metode Bayar:* {$metode_bayar}\n";
                    $text_wa .= "\n*Detail Pesanan:*\n{$detail_items}\n";
                    $text_wa .= "*TOTAL:* Rp " . number_format($total_belanja, 0, ',', '.') . "\n";
                    if ($catatan) $text_wa .= "*Catatan:* {$catatan}\n";
                    $text_wa .= "\nMohon segera diproses ya. Terima kasih! 🙏";
                    $link_wa = "https://wa.me/62895379788123?text=" . urlencode($text_wa);
                    ?>

                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <a href="<?= $link_wa ?>" target="_blank" class="btn btn-success px-4 py-3 rounded-pill fw-bold">
                            <i class="bi bi-whatsapp"></i> Konfirmasi ke WhatsApp
                        </a>
                        <a href="index.php" class="btn btn-outline-dark px-4 py-3 rounded-pill fw-bold">Kembali Belanja</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow rounded-4 p-4 bg-white">
                    <h3 class="fw-bold text-dark mb-4 border-bottom pb-2">Formulir Pengiriman</h3>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <?= get_csrf_input() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Penerima *</label>
                            <input type="text" name="nama_pemesan" class="form-control rounded-3" placeholder="Nama lengkap Anda" value="<?= htmlspecialchars($prefill['nama']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">No. WhatsApp / HP *</label>
                            <input type="text" name="no_hp" class="form-control rounded-3" placeholder="Contoh: 08123456789" value="<?= htmlspecialchars($prefill['no_hp']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Lengkap Pengiriman (Isi '-' jika ambil sendiri) *</label>
                            <textarea id="alamatInput" name="alamat" class="form-control rounded-3" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, kecamatan, kota" required><?= htmlspecialchars($prefill['alamat']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Metode Pengiriman *</label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pengiriman" value="diantar" id="shipDelivery" checked onchange="toggleAlamat(this.value)">
                                    <label class="form-check-label" for="shipDelivery">Di Antar (Delivery)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pengiriman" value="ambil_sendiri" id="shipPickup" onchange="toggleAlamat(this.value)">
                                    <label class="form-check-label" for="shipPickup">Ambil Sendiri (Pickup)</label>
                                </div>
                            </div>
                        </div>
                        <script>
                            function toggleAlamat(val) {
                                const alamat = document.getElementById('alamatInput');
                                if(val === 'ambil_sendiri'){
                                    alamat.value = '-';
                                    alamat.readOnly = true;
                                } else {
                                    alamat.value = '<?= htmlspecialchars($prefill['alamat']) ?>';
                                    alamat.readOnly = false;
                                }
                            }
                        </script>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Metode Pembayaran *</label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" value="cod" id="payCod" checked>
                                    <label class="form-check-label" for="payCod">Bayar di Tempat (COD/Kasir)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" value="transfer" id="payTf">
                                    <label class="form-check-label" for="payTf">Transfer Bank</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" value="qris" id="payQris">
                                    <label class="form-check-label" for="payQris">QRIS / E-Wallet</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Catatan Pesanan</label>
                            <textarea name="catatan" class="form-control rounded-3" rows="2" placeholder="Contoh: Cuko agak pedas, atau tambahkan mie lebih banyak"></textarea>
                        </div>

                        <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">Total Pembayaran:</small>
                                <h4 class="fw-bold text-danger mb-0">Rp <?= number_format($total_belanja, 0, ',', '.') ?></h4>
                            </div>
                            <button type="submit" class="btn btn-danger px-4 py-3 rounded-3 fw-bold">
                                Kirim & Buat Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'views/templates/footer.php'; ?>