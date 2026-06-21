<?php
session_start();

$host     = "localhost";
$username = "root";
$password = ""; 
$database = "db_pempek";

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi ke database MySQL gagal: " . $e->getMessage());
}

if (isset($_GET['kat']) && is_array($_GET['kat']) && count($_GET['kat']) > 0) {
    
    $kategori_dipilih = array_map(function($item) {
        return "%" . $item . "%";
    }, $_GET['kat']);

    $sql = "SELECT * FROM produk WHERE ";
    $conditions = [];
    foreach ($kategori_dipilih as $key => $val) {
        $conditions[] = "(nama LIKE :kat$key OR kategori LIKE :kat$key)";
    }
    $sql .= implode(" OR ", $conditions);
    
    $stmt = $conn->prepare($sql);
    
    foreach ($kategori_dipilih as $key => $val) {
        $stmt->bindValue(":kat$key", $val);
    }
    
    $stmt->execute();
    $produk_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    $query = $conn->query("SELECT * FROM produk");
    $produk_list = $query->fetchAll(PDO::FETCH_ASSOC);
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>