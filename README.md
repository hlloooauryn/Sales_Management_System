# 🌸 BLOOM MOMENT — Integrated Laravel Inventory & Sales Management System 
> Dibuat dengan arsitektur **Laravel MVC**, database relasional **MySQL (XAMPP)**, dan antarmuka **Blade Templating** dengan estetika natural toko bunga.

---

## Ringkasan Proyek

**Bloom Moment** adalah sistem manajemen inventaris dan kasir terintegrasi untuk butik bunga yang memadukan katalog publik dan sistem backoffice internal. Sistem ini dirancang untuk memastikan **konsistensi stok fisik secara atomik** (`DB::transaction`), integritas data relasional antar-tabel, serta pembatasan akses berbasis peran (*Role-Based Access Control*).

---

## Akun Demo Pengujian

Sistem telah dilengkapi dengan akun bawaan (*seeder*):

| Peran (Role) | Alamat Email | Kata Sandi | Kewenangan & Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin / Owner** | `admin@bloommoment.com` | `admin123` | **Akses Penuh:** Kelola Pengguna & Peran, Kelola Kategori, Kelola Produk & Harga Pokok, Kelola Pemasok, Transaksi Pembelian/Restok Produk Masuk (+Stok), Penjualan Kasir (-Stok), Pembatalan Transaksi Aman, Laporan Eksekutif, dan Pengaturan Toko. |
| **Staff Florist** | `staff@bloommoment.com` | `staff123` | **Operasional Kasir:** Kelola harga jual & stok minimum produk, lihat data supplier, input Penjualan kasir (-stok), dan cetak struk nota belanja. *(Dilarang melakukan pembelian/restok dari supplier, mengelola user, kategori, atau menghapus produk berelasi)*. |
| **Pelanggan Publik** | *(Tanpa Login)* | *(Tanpa Password)* | Melihat Beranda, Katalog Bunga, Filter Kategori, Pencarian Bunga, Informasi Detail Bunga, Tentang Kami, dan Kontak Florist. |

---

## Persyaratan Lingkungan (*Prerequisites*)

1. **PHP**: Versi 8.2 atau lebih baru (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `fileinfo` aktif).
2. **Web Server & Database**: XAMPP (Apache & MySQL / MariaDB).
3. **Dependency Manager**: Composer v2.x.
4. **Browser**: Google Chrome, Mozilla Firefox, atau Microsoft Edge.

---

## Panduan Instalasi & Menjalankan Aplikasi

Jika Anda menyalin proyek ini ke komputer baru, ikuti langkah berikut:

### 1. Nyalakan XAMPP
Buka **XAMPP Control Panel**, lalu klik **Start** pada modul **Apache** dan **MySQL** hingga indikator berwarna hijau.

### 2. Konfigurasi Database (.env)
Buka file `.env` di direktori proyek dan pastikan konfigurasi koneksi mengarah ke MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bloom_moment_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi & Seeding Data Awal
Buka terminal (PowerShell atau Command Prompt) di folder proyek:
```powershell
php artisan migrate:fresh --seed
```
*Perintah ini akan membuat 8 tabel relasional dan mengisi data akun Admin, Staff, kategori bunga, 3 pemasok terdaftar, 10 produk bunga realistis, serta sampel transaksi.*

### 4. Jalankan Server Lokal
```powershell
php artisan serve
```
Buka peramban (*browser*) dan akses: **`http://127.0.0.1:8000`**

---

## Struktur Database & Relasi (8 Tabel Inti)

Sistem menggunakan 8 tabel yang saling terhubung dengan foreign key dan proteksi referensial:

```
[users] (admin, staff)
   │
   ├──< [purchases] (header pembelian/barang masuk)
   │        │
   │        └──< [purchase_items] >── [products] >── [categories]
   │                                      │
   └──< [sales] (header kasir/barang keluar)
            │                             │
            └──< [sale_items] >───────────┘
```

1. **`users`**: Menyimpan akun internal dengan kolom `role` (`admin` atau `staff`).
2. **`categories`**: Kategori bunga (misal: *Fresh Cut Flowers*, *Hand Bouquet*, *Flower Box*).
3. **`products`**: Data bunga (`name`, `slug`, `category_id`, `buy_price`, `sell_price`, `stock`, `min_stock`, `image`, `is_active`).
4. **`suppliers`**: Data pemasok/perkebunan bunga mitra toko.
5. **`purchases`**: Header transaksi barang masuk (No. referensi, tanggal, ID supplier, ID petugas, total biaya).
6. **`purchase_items`**: Detail item bunga masuk (`purchase_id`, `product_id`, `quantity`, `unit_cost`, `subtotal`).
7. **`sales`**: Header penjualan kasir (No. invoice, tanggal, ID kasir, nama pelanggan, metode pembayaran, total nilai).
8. **`sale_items`**: Detail item bunga terjual (`sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`).

---

## Aturan Bisnis & Konsistensi Stok (Atomisitas)

1. **Pembelian / Restock Bunga (Barang Masuk)**:
   - **Admin / Owner dan Staff** dapat melakukan restok di menu **Pembelian (Masuk)**.
   - Menggunakan `DB::beginTransaction()`.
   - Total dihitung ulang di server (`qty * unit_cost`) untuk mencegah manipulasi form.
   - Stok fisik produk bertambah otomatis: `stock = stock + quantity`.
   - Harga pokok beli produk terbarui otomatis sesuai nota supplier terbaru.

2. **Penjualan (Barang Keluar)**:
   - Dibuat melalui antarmuka kasir di menu **Penjualan (Kasir)** oleh Staff maupun Admin.
   - Menggunakan `Product::lockForUpdate()` saat pengecekan stok untuk mencegah kondisi balapan (*race condition*).
   - **Validasi Stok Ketat**: Kuantitas tidak boleh melebihi stok yang ada (`stock >= quantity`). Jika stok kurang, transaksi otomatis dibatalkan (*rollback*) dan memunculkan notifikasi error.
   - Stok fisik berkurang otomatis: `stock = stock - quantity`.

3. **Integritas Penghapusan (Foreign Key Restrict)**:
   - Produk atau supplier yang sudah memiliki riwayat transaksi **tidak dapat dihapus** sembarangan untuk menjaga keutuhan audit laporan keuangan.

4. **Pembatalan Transaksi Aman**:
   - Khusus Admin/Owner, transaksi penjualan dapat dibatalkan di mana seluruh stok fisik bunga dikembalikan (*increment*) ke sistem secara terkontrol.

---

## Skenario Pengujian untuk Sidang

| No | Skenario Uji | Langkah Pengujian | Hasil yang Diharapkan | Status |
| :---: | :--- | :--- | :--- | :---: |
| 1 | **Akses Publik Tanpa Login** | Buka URL `/`, `/catalog`, `/about`, `/contact`. | Halaman terbuka lancar, menu dashboard internal tidak muncul. | ✅ Berhasil |
| 2 | **Proteksi Rute Internal** | Buka `/admin/dashboard` tanpa login. | Otomatis dialihkan ke halaman `/login` dengan peringatan. | ✅ Berhasil |
| 3 | **Login & Otorisasi Staff** | Login dengan `staff@bloommoment.com`. | Masuk ke dashboard operasional kasir. Menu Kategori, User, dan Pembelian/Restok tidak muncul. | ✅ Berhasil |
| 4 | **Blokir Otorisasi Staff** | Saat login sebagai Staff, coba ketik URL `/admin/users`, `/admin/categories`, atau `/admin/purchases`. | Server mengembalikan **HTTP 403 Forbidden** (Akses Ditolak). | ✅ Berhasil |
| 5 | **Pembelian Admin (+Stok)** | Login Admin &rarr; menu Pembelian &rarr; Tambah Pembelian 20 tangkai Mawar Merah. | Transaksi tersimpan, faktur tampil, stok mawar merah bertambah 20 tangkai. | ✅ Berhasil |
| 6 | **Penjualan (-Stok)** | Masuk menu Penjualan &rarr; Kasir &rarr; Jual 3 tangkai Mawar Merah. | Struk tercetak, tagihan akurat, stok berkurang 3 tangkai. | ✅ Berhasil |
| 7 | **Penolakan Stok Minus** | Coba lakukan penjualan 999 tangkai bunga saat stok hanya tersisa sedikit. | Transaksi ditolak, muncul alert stok tidak cukup, stok fisik tidak berubah. | ✅ Berhasil |
| 8 | **Peringatan Stok Menipis** | Periksa dashboard saat ada produk dengan `stok <= min_stock`. | Tabel peringatan kuning muncul di dashboard (Admin ada tombol restok cepat, Staff melihat peringatan restok). | ✅ Berhasil |
| 9 | **Laporan Berkala** | Masuk ke menu Laporan &rarr; Filter rentang tanggal &rarr; Cetak. | Rekapitulasi penjualan, pembelian, dan selisih kas tampil rapi siap cetak. | ✅ Berhasil |

---

## Menjalankan Pengujian Otomatis

Sistem telah dilengkapi dengan test suite lengkap berbasis PHPUnit:
```powershell
php artisan test tests/Feature/InventoryAndSalesTest.php
```
Hasil: **8 Tests Passed (16 Assertions)** mencakup verifikasi guest, login staff, otorisasi 403, penambahan stok, pengurangan stok, dan penolakan stok tidak cukup.

---

## Struktur File Penting Proyek

- **Model & Relasi**: `app/Models/` (`User`, `Category`, `Product`, `Supplier`, `Purchase`, `PurchaseItem`, `Sale`, `SaleItem`).
- **Controller**: `app/Http/Controllers/` (`PublicController`, `AuthController`, `DashboardController`, `ProductController`, `CategoryController`, `SupplierController`, `PurchaseController`, `SaleController`, `ReportController`).
- **Middleware Role**: `app/Http/Middleware/RoleMiddleware.php`.
- **Rute Web**: `routes/web.php`.
- **Tampilan Blade Publik**: `resources/views/public/` (`home`, `catalog`, `detail`, `about`, `contact`).
- **Tampilan Blade Backoffice**: `resources/views/admin/` (`dashboard`, `categories/`, `products/`, `suppliers/`, `purchases/`, `sales/`, `reports/`, `users/`).
- **Desain CSS**: `public/css/style.css` (Palet natural: Warm Ivory, Cream, Dusty Pink, Sage Green, Muted Olive, Warm Brown).

---

*Selamat menggunakan dan menguji sistem Bloom Moment.* 🌸
