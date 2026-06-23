# Pempek E-Commerce - Docker Setup

Aplikasi E-Commerce Pempek Wong Kito dengan panel Admin, Database MySQL, dan phpMyAdmin menggunakan Docker.

## Persyaratan
- Docker & Docker Compose terinstal di PC Anda.

## Cara Menjalankan

1. Buka terminal di folder project `Pempek-e-commerce`.
2. Jalankan perintah berikut untuk membuild dan menjalankan container di background:
   ```bash
   docker compose up -d --build
   ```
3. Tunggu hingga database MySQL selesai menginisialisasi tabel (sekitar 10-15 detik).

## Akses Layanan

- **Website Utama (Katalog & Cart)**: [http://localhost:8080/Toko_Pempek/](http://localhost:8080/Toko_Pempek/)
- **Halaman Admin Panel**: [http://localhost:8080/Toko_Pempek/admin/](http://localhost:8080/Toko_Pempek/admin/)
  - **Username**: `admin`
  - **Password**: `admin123`
- **phpMyAdmin (Manajemen DB)**: [http://localhost:8081](http://localhost:8081)
  - **Username**: `root`
  - **Password**: `rootpassword`

## Menghentikan Layanan

Untuk mematikan container, jalankan:
```bash
docker compose down
```
Untuk mematikan sekaligus menghapus data database (reset):
```bash
docker compose down -v
```
