# 🖨️ Kasir Percetakan - Aplikasi Kasir Modern untuk Percetakan

Aplikasi kasir berbasis web menggunakan **Laravel 10** yang dirancang khusus untuk usaha percetakan, fotokopi, desain grafis, dan jasa printing.

---

## ✨ Fitur Unggulan

- ✅ **Kasir / POS** — Input transaksi cepat dengan pilih produk 1 klik
- ✅ **Nota Profesional** — Cetak nota A4 dan struk thermal (58mm/80mm)
- ✅ **QR Code di Nota** — Invoice QR otomatis
- ✅ **Custom Order** — Tambah item custom dengan spesifikasi (ukuran, bahan, warna, deadline)
- ✅ **Dashboard** — Statistik harian, mingguan, bulanan + grafik
- ✅ **Manajemen Produk & Jasa** — CRUD lengkap dengan kategori
- ✅ **Manajemen Pelanggan** — Database pelanggan terintegrasi
- ✅ **Laporan & Export CSV** — Filter tanggal, rekap omset, jasa terlaris
- ✅ **Multi Role** — Admin & Kasir
- ✅ **Dark Mode** — Bisa diaktifkan dari pengaturan
- ✅ **Responsive** — Desktop & mobile friendly
- ✅ **Pengaturan Nota Lengkap** — Logo, warna tema, footer, pajak, dll

---

## 🛠️ Teknologi

| Komponen | Versi |
|----------|-------|
| PHP | ^8.1 |
| Laravel | ^10.x |
| MySQL | 5.7 / 8.0+ |
| Bootstrap | 5.3 |
| Chart.js | 4.x |
| SweetAlert2 | 11.x |

---

## 📦 Cara Install

### 1. Persyaratan

Pastikan sudah terinstall:
- PHP >= 8.1 + ekstensi: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- Composer
- MySQL / MariaDB
- Node.js (opsional, untuk asset)

---

### 2. Clone / Extract Project

```bash
# Jika menggunakan ZIP, extract ke folder htdocs atau www
# Lalu masuk ke folder project
cd kasir-percetakan
```

---

### 3. Install Dependensi

```bash
composer install
```

---

### 4. Setup Environment

```bash
# Salin file .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit file `.env` sesuaikan koneksi database:

```env
APP_NAME="Kasir Percetakan"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kasir_percetakan
DB_USERNAME=root
DB_PASSWORD=        ← isi password MySQL kamu
```

---

### 5. Buat Database & Import SQL

**Opsi A — via phpMyAdmin:**
1. Buka phpMyAdmin
2. Klik **"New"** → buat database baru bernama `kasir_percetakan`
3. Pilih database tersebut → klik tab **"Import"**
4. Pilih file: `database/kasir_percetakan.sql`
5. Klik **"Go"** / **"Import"**

**Opsi B — via Terminal:**
```bash
mysql -u root -p -e "CREATE DATABASE kasir_percetakan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p kasir_percetakan < database/kasir_percetakan.sql
```

---

### 6. Setup Storage (untuk upload logo)

```bash
php artisan storage:link
```

---

### 7. Jalankan Aplikasi

```bash
php artisan serve
```

Buka browser: **http://localhost:8000**

---

## 🔐 Akun Default

| Role  | Email                    | Password    |
|-------|--------------------------|-------------|
| Admin | admin@percetakan.com     | password123 |
| Kasir | kasir@percetakan.com     | password123 |

> ⚠️ **Penting:** Ganti password setelah login pertama!

---

## 📁 Struktur Folder Penting

```
kasir-percetakan/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── KasirController.php        ← Logika POS utama
│   │   │   ├── TransactionController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── SettingController.php
│   │   │   └── ReportController.php
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   └── Kernel.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Transaction.php
│   │   ├── TransactionItem.php
│   │   ├── ProductService.php
│   │   ├── Customer.php
│   │   └── InvoiceSetting.php
│   └── Providers/
│       └── AppServiceProvider.php         ← Share $setting global
├── database/
│   └── kasir_percetakan.sql               ← File SQL lengkap + seed data
├── resources/
│   └── views/
│       ├── layouts/app.blade.php          ← Layout utama + sidebar
│       ├── auth/login.blade.php
│       ├── dashboard/index.blade.php
│       ├── kasir/index.blade.php          ← Halaman POS utama
│       ├── transactions/
│       │   ├── index.blade.php
│       │   ├── show.blade.php
│       │   ├── nota.blade.php             ← Cetak nota A4
│       │   └── thermal.blade.php          ← Cetak struk thermal
│       ├── products/
│       ├── customers/
│       ├── settings/index.blade.php
│       └── reports/index.blade.php
└── routes/web.php
```

---

## 🖨️ Panduan Cetak Nota

### Cetak Nota A4
1. Buka halaman transaksi
2. Klik tombol **"Cetak Nota A4"**
3. Halaman print akan terbuka di tab baru
4. Klik **"Cetak Nota"** atau tekan `Ctrl+P`

### Cetak Struk Thermal (58mm / 80mm)
1. Klik tombol **"Cetak Thermal"**
2. Di dialog print browser, pilih printer thermal kamu
3. Set paper size ke **58mm** atau **80mm** sesuai printer
4. Margin: **None / Tanpa Margin**
5. Klik Print

### Tips Cetak Thermal:
- Di Google Chrome → More Settings → Paper size → **Custom** → set width sesuai printer
- Disable "Headers and footers" di browser print settings
- Gunakan driver printer thermal yang sudah terinstall

---

## ⚙️ Konfigurasi Pengaturan

Masuk ke menu **Pengaturan** untuk:
- Upload logo usaha
- Atur nama, alamat, no. telp, WhatsApp
- Kustomisasi warna tema
- Aktifkan/nonaktifkan pajak
- Atur prefix nomor invoice
- Tambah footer & catatan nota
- Toggle dark mode
- Toggle QR Code di nota

---

## 🗄️ Database

### Tabel Utama

| Tabel | Keterangan |
|-------|-----------|
| `users` | Data user (admin & kasir) |
| `customers` | Data pelanggan |
| `products_services` | Master produk & jasa |
| `transactions` | Header transaksi |
| `transaction_items` | Detail item per transaksi |
| `invoice_settings` | Pengaturan nota & aplikasi |

---

## 🔧 Troubleshooting

### Error: `SQLSTATE[HY000] [1045] Access denied`
→ Periksa konfigurasi `DB_USERNAME` dan `DB_PASSWORD` di `.env`

### Error: `Class "App\Models\InvoiceSetting" not found`
→ Jalankan: `composer dump-autoload`

### Storage link error
→ Jalankan: `php artisan storage:link`

### Halaman 403 saat akses admin
→ Pastikan login dengan akun role **admin**

### Logo tidak muncul
→ Pastikan sudah jalankan `php artisan storage:link`

### Blank page / Error 500
→ Cek file `.env` sudah ada dan `APP_KEY` sudah di-generate

---

## 🚀 Deploy ke Production (Hosting)

1. Upload semua file ke public_html atau subdomain
2. Pastikan `public/` menjadi document root
3. Edit `.env`:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   ```
4. Jalankan:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan storage:link
   ```

---

## 📝 Catatan Pengembangan

Untuk menambah fitur atau memodifikasi:

- **Tambah jasa baru** → Menu Produk & Jasa → Tambah Baru
- **Ubah warna tema** → Pengaturan → Warna Tema Utama
- **Ubah template nota** → Edit `resources/views/transactions/nota.blade.php`
- **Ubah template thermal** → Edit `resources/views/transactions/thermal.blade.php`

---

## 📄 Lisensi

Project ini bebas digunakan dan dimodifikasi untuk kebutuhan usaha percetakan.

---

**Dibuat dengan ❤️ untuk kemudahan operasional percetakan Indonesia**
