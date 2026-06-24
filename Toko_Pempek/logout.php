<?php
session_start();
session_unset();
// Hapus session tapi biarkan keranjang jika ada? 
// Kebanyakan e-commerce menghapus session user tapi membiarkan cookie/cart utuh jika tidak ingin merugikan customer.
// Di sini kita unset session login spesifik, tapi biarkan $_SESSION['cart'] terselamatkan.
$cart = $_SESSION['cart'] ?? [];
session_destroy();

session_start();
$_SESSION['cart'] = $cart;

header('Location: index.php');
exit;