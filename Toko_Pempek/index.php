<?php
session_start();
require_once 'config/database.php';
require_once 'core/Functions.php';

// Logika tambah item ke session cart
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $id = intval($_GET['id']);
    
    foreach ($produk_list as $prod) {
        if ($prod['id'] == $id) {
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['jumlah'] += 1;
            } else {
                $_SESSION['cart'][$id] = [
                    'nama' => $prod['nama'],
                    'harga' => $prod['harga'],
                    'jumlah' => 1
                ];
            }
            break;
        }
    }
    header('Location: index.php');
    exit;
}

$title = "Menu Pempek Wong Kito - Lezat & Asli";
include 'views/templates/header.php';
include 'views/katalog.php';
include 'views/templates/footer.php';