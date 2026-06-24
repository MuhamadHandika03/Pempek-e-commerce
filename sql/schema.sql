CREATE DATABASE IF NOT EXISTS `db_pempek` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_pempek`;

-- --------------------------------------------------------
-- Table: admin_users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `level` enum('owner','kasir') NOT NULL DEFAULT 'kasir',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default admin account (username: admin, password: admin123)
INSERT INTO `admin_users` (`username`, `password`, `nama`, `level`) 
VALUES ('admin', '$2y$10$CVuWKZOMqY.yWL/jitJAtug72gPKT.pDb8X9sRtw3CTsLHUsh8hka', 'Pemilik Toko', 'owner')
ON DUPLICATE KEY UPDATE `password`='$2y$10$CVuWKZOMqY.yWL/jitJAtug72gPKT.pDb8X9sRtw3CTsLHUsh8hka';

-- --------------------------------------------------------
-- Table: produk
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `harga` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('tersedia','habis') NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed initial products data
INSERT INTO `produk` (`nama`, `kategori`, `harga`, `deskripsi`, `foto`) VALUES
('Pempek Kapal Selam Besar', 'Kapal Selam', 18000, 'Pempek ukuran besar dengan isian telur utuh.', 'assets/kapal_selam.jpeg'),
('Pempek Lenjer Panjang', 'Lenjer', 15000, 'Pempek lenjer khas dengan ukuran panjang, gurih nian.', 'assets/pempek_lenjer.jpg'),
('Pempek Adaan Bulat', 'Adaan', 4000, 'Pempek bulat dengan bumbu bawang gurih.', 'assets/pempek_adaan.jpg'),
('Pempek Kulit Crispy', 'Kulit', 4000, 'Pempek berbahan dasar kulit ikan yang digoreng garing.', 'assets/pempek_kulit.jpg'),
('Pempek Lenggang', 'Lenggang', 16000, 'Pempek panggang dadar telur yang harum.', 'assets/pempek_lenggang.jpg'),
('Pempek Keriting', 'Keriting', 4000, 'Pempek keriting rebus yang lezat.', 'assets/pempek_keriting.jpg'),
('Paket Hemat Wong Kito', 'Paket Hemat', 50000, 'Paket campur pempek isi 12 pcs bonus cuko kental.', 'assets/paket_hemat.jpg')
ON DUPLICATE KEY UPDATE `nama`=`nama`;

-- --------------------------------------------------------
-- Table: pelanggan
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: pesanan
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pelanggan_id` int(11) DEFAULT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `metode_pembayaran` varchar(20) NOT NULL,
  `metode_pengiriman` varchar(20) NOT NULL DEFAULT 'diantar',
  `status` enum('menunggu','dikonfirmasi','diproses','siap','diambil','batal') NOT NULL DEFAULT 'menunggu',
  `total_harga` int(11) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: pesanan_detail
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pesanan_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pesanan_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `harga_saat_pesan` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
