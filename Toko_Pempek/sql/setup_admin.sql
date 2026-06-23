-- ============================================================
-- Database: db_pempek
-- Admin Panel Tables
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_pempek;
USE db_pempek;

-- Table: admin_users
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    level ENUM('owner', 'kasir') DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Insert default admin
-- Password: admin123 (bcrypt hash)
INSERT INTO admin_users (username, password, nama, level) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pemilik Toko', 'owner')
ON DUPLICATE KEY UPDATE id=id;

-- Table: produk (menu makan)
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    kategori ENUM('Kapal Selam', 'Besar', 'Adaan', 'Lenjer', 'Kulit', 'Lenggang', 'Keriting', 'Paket Hemat') NOT NULL,
    harga INT NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    deskripsi TEXT,
    status ENUM('tersedia', 'habis') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: pesanan (orders dari customer)
CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemesan VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    alamat TEXT,
    metode_pembayaran ENUM('cod', 'transfer') DEFAULT 'cod',
    status ENUM('menunggu', 'dikonfirmasi', 'diproses', 'siap', 'diambil', 'batal') DEFAULT 'menunggu',
    total_harga INT NOT NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: pesanan_detail (item per pesanan)
CREATE TABLE IF NOT EXISTS pesanan_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    produk_id INT,
    nama_produk VARCHAR(150) NOT NULL,
    harga_saat_pesan INT NOT NULL,
    jumlah INT NOT NULL,
    subtotal INT NOT NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE SET NULL
);

-- Insert sample menu
INSERT INTO produk (nama, kategori, harga, foto, deskripsi, status) VALUES
('Pempek Kapal Selam Original', 'Kapal Selam', 18000, 'assets/kapal_selam.jpeg', 'Pempek isi telur jumbo khas Palembang', 'tersedia'),
('Pempek Kapal Selam Spesial', 'Kapal Selam', 22000, 'assets/kapal_selam.jpeg', 'Pempek kapal selam dengan udang', 'tersedia'),
('Pempek Lenjer', 'Lenjer', 10000, 'assets/pempek_lenjer.jpg', 'Pempek lenjer lembut isi ikan tenggiri asli', 'tersedia'),
('Pempek Adaan', 'Adaan', 12000, 'assets/pempek_adaan.jpg', 'Pempek bulat goreng renyah', 'tersedia'),
('Pempek Kulit Crispy', 'Kulit', 8000, 'assets/pempek_kulit.jpg', 'Pempek kulit goreng kriuk', 'tersedia'),
('Pempek Lenggang', 'Lenggang', 15000, 'assets/pempek_lenggang.jpg', 'Pempek rebus dengan kuah cuko', 'tersedia'),
('Pempek Keriting', 'Keriting', 10000, 'assets/pempek_keriting.jpg', 'Pempek keriting enak dan kenyal', 'tersedia'),
('Paket Hemat Pempek', 'Paket Hemat', 35000, 'assets/paket_hemat.jpg', 'Paket lengkap 3 jenis pempek + cuko + mie', 'tersedia'),
('Paket Keluarga', 'Paket Hemat', 85000, 'assets/paket_hemat.jpg', 'Paket keluarga isi 5 kapal selam + lenjer + kuah cuko', 'tersedia')
ON DUPLICATE KEY UPDATE id=id;