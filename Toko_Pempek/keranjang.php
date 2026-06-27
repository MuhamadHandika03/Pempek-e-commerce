<?php
session_start();
require_once 'config/database.php';
require_once 'config/app.php';
require_once 'core/Functions.php';

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = intval($_GET['id']);
    unset($_SESSION['cart'][$id]);
    header('Location: keranjang.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    $_SESSION['cart'] = [];
    header('Location: keranjang.php');
    exit;
}

$title = "Keranjang Belanjaan Pempek";
include 'views/templates/header.php';
include 'views/keranjang.php';
include 'views/templates/footer.php';