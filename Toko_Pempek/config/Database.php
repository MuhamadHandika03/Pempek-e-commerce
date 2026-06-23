<?php
// session_start() dipindahkan ke setiap file yang membutuhkannya
// (index.php, keranjang.php, checkout.php) untuk menghindari duplicate call

$host     = getenv('DB_HOST') ?: 'db';
$username = getenv('DB_USER') ?: 'pempek_user';
$password = getenv('DB_PASS') ?: 'pempek_pass';
$database = getenv('DB_NAME') ?: 'db_pempek';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi ke database MySQL gagal: " . $e->getMessage());
}

// Konfigurasi Pagination
$limit = 9;
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$offset = ($page - 1) * $limit;

// Filter Kategori
$kategori_dipilih = [];
if (isset($_GET['kat']) && is_array($_GET['kat']) && count($_GET['kat']) > 0) {
    $kategori_dipilih = array_map(function($item) {
        return "%" . $item . "%";
    }, $_GET['kat']);
}

// Count Total Items
if (!empty($kategori_dipilih)) {
    $sql_count = "SELECT COUNT(*) FROM produk WHERE ";
    $conditions = [];
    foreach ($kategori_dipilih as $key => $val) {
        $conditions[] = "(nama LIKE :kat$key OR kategori LIKE :kat$key)";
    }
    $sql_count .= implode(" OR ", $conditions);
    $stmt_count = $conn->prepare($sql_count);
    foreach ($kategori_dipilih as $key => $val) {
        $stmt_count->bindValue(":kat$key", $val);
    }
    $stmt_count->execute();
    $total_items = $stmt_count->fetchColumn();
} else {
    $total_items = $conn->query("SELECT COUNT(*) FROM produk")->fetchColumn();
}

$total_pages = ceil($total_items / $limit);

// Fetch Paginated Items
if (!empty($kategori_dipilih)) {
    $sql = "SELECT * FROM produk WHERE ";
    $conditions = [];
    foreach ($kategori_dipilih as $key => $val) {
        $conditions[] = "(nama LIKE :kat$key OR kategori LIKE :kat$key)";
    }
    $sql .= implode(" OR ", $conditions);
    $sql .= " LIMIT :limit OFFSET :offset";
    
    $stmt = $conn->prepare($sql);
    foreach ($kategori_dipilih as $key => $val) {
        $stmt->bindValue(":kat$key", $val);
    }
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $produk_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $conn->prepare("SELECT * FROM produk LIMIT :limit OFFSET :offset");
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();
    $produk_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>