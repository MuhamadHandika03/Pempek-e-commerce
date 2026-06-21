<?php
function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

function hitung_total_item() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['jumlah'];
        }
    }
    return $total;
}