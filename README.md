# Kasir Percetakan

**Aplikasi Kasir (POS) untuk Usaha Percetakan** — sistem berbasis web untuk mengelola transaksi penjualan, produk/jasa percetakan, data pelanggan, hingga laporan penjualan, lengkap dengan cetak nota (regular & thermal).

## ✨ Fitur

- **Kasir (Point of Sale)** — input transaksi cepat, pencarian produk, dan pencarian pelanggan langsung dari halaman kasir
- **Manajemen Transaksi** — lihat riwayat transaksi, cetak nota (regular & thermal), batalkan atau hapus transaksi
- **Manajemen Produk/Jasa** — CRUD daftar produk dan jasa percetakan (khusus admin)
- **Manajemen Pelanggan** — CRUD data pelanggan (khusus admin)
- **Laporan Penjualan** — lihat dan ekspor laporan transaksi (khusus admin)
- **Pengaturan** — atur informasi invoice/nota dan upload logo usaha (khusus admin)
- **Role-based Access** — pemisahan hak akses antara kasir biasa dan admin

## 🛠️ Tech Stack

- **Framework:** Laravel 10
- **PHP:** ^8.1
- **Database:** MySQL
- **Autentikasi API:** Laravel Sanctum
- **Frontend build:** Vite

## 📂 Struktur Singkat

- `app/Http/Controllers/`
  - `AuthController.php`
  - `DashboardController.php`
  - `KasirController.php` — proses transaksi POS
  - `TransactionController.php` — riwayat, cetak nota, batal/hapus transaksi
  - `ProductController.php` — CRUD produk/jasa
  - `CustomerController.php` — CRUD pelanggan
  - `SettingController.php` — pengaturan invoice & logo
  - `ReportController.php` — laporan penjualan
- `app/Models/` — Customer, ProductService, Transaction, TransactionItem, InvoiceSetting, User
- `database/migrations/` — skema tabel: products_services, customers, transactions, transaction_items, dll.
- `routes/web.php` — routing kasir, transaksi, produk, pelanggan, laporan, dan pengaturan

## 🚀 Cara Menjalankan

1. **Clone repo ini**

   ```bash
   git clone https://github.com/anwar-iman21/kasir-percetakan.git
   cd kasir-percetakan
   ```

2. **Install dependency**

   ```bash
   composer install
   npm install
   ```

3. **Siapkan file environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database**

   Buka `.env`, sesuaikan dengan database lokal kamu:

   ```env
   DB_DATABASE=kasir_percetakan
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Jalankan migrasi (dan seeder jika tersedia)**

   ```bash
   php artisan migrate --seed
   ```

6. **Buat symlink storage** (untuk logo & file upload)

   ```bash
   php artisan storage:link
   ```

7. **Jalankan server**

   ```bash
   php artisan serve
   npm run dev
   ```

   Buka `http://localhost:8000` di browser.

## 📌 Catatan

Project ini dikembangkan sebagai sistem kasir yang terfokus pada kebutuhan usaha percetakan, termasuk fitur cetak nota thermal untuk printer kasir. Cocok dijadikan referensi belajar sistem POS dan role-based access control di Laravel.

## 📄 Lisensi

Project ini open source untuk keperluan belajar, silakan digunakan sebagai referensi.
