# Inventory Management System (Laravel Application)

> **Tugas Praktikum Pertemuan 5 — Laravel Fundamental**  
> *Mengubah Inventory Management System P4 (PHP Native) Menjadi Laravel Application (MVC Architecture)*

Aplikasi ini merupakan hasil migrasi dan refaktorisasi arsitektur dari proyek sistem manajemen inventaris berbasis PHP Native ke kerangka kerja **Laravel** dengan menerapkan standar arsitektur **Model-View-Controller (MVC)**, database migration, factory, seeder, Eloquent ORM dengan relasi multi-tabel, Blade template layout & component, validasi input, proteksi CSRF, serta transactional safety untuk pergerakan persediaan stok.

---

## 📋 Daftar Isi
1. [Fitur Utama Aplikasi](#-fitur-utama-aplikasi)
2. [Arsitektur MVC & Request-Response Lifecycle](#-arsitektur-mvc--request-response-lifecycle)
3. [Alur Demonstrasi: Form → Validasi → Simpan → Redirect → Tampilkan Data](#-alur-demonstrasi-form--validasi--simpan--redirect--tampilkan-data)
4. [Persyaratan Lingkungan](#-persyaratan-lingkungan)
5. [Panduan Instalasi & Menjalankan](#-panduan-instalasi--menjalankan)
6. [Akun Pengguna Demo](#-akun-pengguna-demo)
7. [Struktur Database & Relasi Eloquent](#-struktur-database--relasi-eloquent)
8. [Struktur Proyek](#-struktur-proyek)
9. [Dokumentasi Screenshot](#-dokumentasi-screenshot)

---

## 🚀 Fitur Utama Aplikasi

### 1. Dashboard Inventaris Interaktif
- **Ringkasan Metrik**: Total produk, total kategori, total supplier, total nilai aset persediaan (otomatis dikalkulasi dari `SUM(price * stock)`), dan hitungan mutasi barang.
- **Peringatan Stok Rendah (Low Stock Alert)**: Menampilkan barang dengan kondisi `stock <= minimum_stock` disertai indikator visual kritis dan tombol aksi cepat restock.
- **Histori Mutasi Terkini**: Menampilkan 6 log transaksi terakhir barang masuk dan barang keluar.

### 2. Manajemen Data Produk (Full Resource CRUD)
- **Katalog Produk Lengkap**: SKU unik, nama barang, kategori, harga satuan terformat Rupiah, stok fisik, status ketersediaan, dan supplier mitra.
- **Pencarian & Multi-Filter**: Pencarian cerdas berbasis nama atau SKU (`LIKE %...%`), filter berdasarkan kategori, serta filter kondisi stok (stok aman vs stok menipis).
- **Sorting Dinamis**: Pengurutan berdasarkan kolom nama, SKU, harga satuan, jumlah stok, atau waktu pembuatan (Ascending / Descending).
- **Pagination Bersih**: Menampilkan navigasi halaman dengan mempertahankan parameter filter URL (`withQueryString()`).
- **Relasi Multi-Supplier**: Satu produk dapat disuplai oleh lebih dari satu vendor sekaligus melalui checkbox dinamis (tabel pivot `product_supplier`).
- **Integritas Audit**: Penghapusan produk dicegah jika produk bersangkutan telah memiliki riwayat pergerakan stok, guna mencegah rusaknya data audit mutasi.

### 3. Manajemen Kategori & Supplier
- **CRUD Kategori**: Tambah, edit, dan hapus kategori produk. Dilengkapi proteksi agar kategori yang masih digunakan produk tidak dapat dihapus sembarangan.
- **CRUD Supplier Mitra**: Tambah, edit, dan hapus vendor penyedia lengkap dengan nomor kontak telepon, email, dan alamat kantor/gudang.

### 4. Transaksi Persediaan (Stok Masuk / Stok Keluar)
- **Barang Masuk (Stock In)**: Menambah kuantitas stok fisik produk secara otomatis.
- **Barang Keluar (Stock Out)**: Mengurangi kuantitas stok fisik dengan validasi ketat agar stok tidak menjadi negatif.
- **Pencegahan Stok Negatif**: Jika kuantitas pengeluaran melebihi stok yang tersedia, transaksi ditolak melalui `ValidationException` dan menampilkan pesan error kepada user.
- **Database Transaction & Pessimistic Locking**: Menggunakan `DB::transaction()` dan `lockForUpdate()` untuk mencegah kondisi race condition saat transaksi stok bersamaan.
- **Histori Mutasi (Audit Trail)**: Setiap transaksi mencatat waktu mutasi, nama produk, jenis (in/out), kuantitas, catatan, dan nama petugas pelaksana.

### 5. Keamanan & Antarmuka
- **Blade Layout & Reusable Component**: Menggunakan template master `layouts/app.blade.php` dan komponen peringatan `<x-alert>` (sesuai Slide 23 materi perkuliahan).
- **Proteksi CSRF**: Semua form mutasi dan CRUD diamankan dengan `@csrf`.
- **Form Error Handling**: Pesan error field interaktif menggunakan `@error` dan pengisian kembali nilai sebelumnya dengan helper `old()`.
- **Demo Session Auth**: Pengamanan route menggunakan middleware `demo.auth`.

---

## 🏛️ Arsitektur MVC & Request-Response Lifecycle

Aplikasi ini mengimplementasikan alur baku Laravel MVC:

```
[ Browser / Pengguna ]
       │  (1) HTTP Request (GET/POST/PUT/DELETE)
       ▼
[ routes/web.php ] ───► Dilindungi oleh Middleware [ DemoAuth ]
       │  (2) Memanggil method yang sesuai
       ▼
[ Controller ] (Contoh: ProductController)
       │  (3) Memvalidasi Request ($request->validate)
       │  (4) Memanggil Model / Query DB
       ▼
[ Model Eloquent ] (Product, Category, Supplier, StockMovement)
       │  (5) Eksekusi Query SQL melalui PDO
       ▼
[ Database MySQL ]
       │  (6) Hasil Data Recordset
       ▼
[ Model Eloquent ] (Instance / Collection)
       │  (7) Return data ke Controller
       ▼
[ Controller ]
       │  (8) Memilih Response: view() atau redirect()->with()
       ▼
[ Blade View Engine ] (resources/views/)
       │  (9) Merender HTML dengan Layout, Escaping {{ }}, Loop & Kondisi
       ▼
[ Browser / Response HTTP 200/302 ]
```

Penjelasan teknis mendalam mengenai tanggung jawab masing-masing komponen dapat dibaca pada file [MVC.md](file:///c:/Users/SN55C/Documents/Mini_project4/MVC.md).

---

## 🎯 Alur Demonstrasi: Form → Validasi → Simpan → Redirect → Tampilkan Data

Sesuai instruksi tugas pertemuan (Slide 48), berikut tahapan alur lengkap satu siklus request-to-response:

1. **Membuka Form Tambah Produk (`GET /products/create`)**:
   - Pengguna menekan tombol **+ Tambah Produk Baru**.
   - Route memanggil `ProductController@create`.
   - Controller mengambil daftar kategori dan supplier dari database, lalu menyajikannya ke view `products/create.blade.php`.
   - Tampilan form dilengkapi token keamanan `@csrf`.

2. **Validasi Input Request (`POST /products`)**:
   - Pengguna mengisi data form (SKU, Nama, Kategori, Harga, Stok, Batas Minimum, Supplier) lalu klik **Simpan Produk**.
   - Request dikirim via method `POST` ke route `products.store`.
   - `ProductController@store` menjalankan validasi:
     ```php
     $validated = $request->validate([
         'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
         'name' => ['required', 'string', 'max:150'],
         'category_id' => ['required', 'integer', 'exists:categories,id'],
         'price' => ['required', 'numeric', 'min:0'],
         'stock' => ['required', 'integer', 'min:0'],
         'minimum_stock' => ['required', 'integer', 'min:0'],
         'suppliers' => ['nullable', 'array'],
     ]);
     ```
   - **Skenario Gagal Validasi**: Jika user memasukkan SKU duplikat atau membiarkan field wajib kosong, Laravel otomatis mengembalikan user ke halaman form sebelumnya (`back()`) dengan menyertakan pesan kesalahan (`$errors`) dan input sebelumnya (`old()`).

3. **Penyimpanan ke Database (Model Eloquent)**:
   - Jika lolos validasi, data disimpan menggunakan mass-assignment Eloquent:
     ```php
     $product = Product::create($validated);
     $product->suppliers()->sync($supplierIds);
     ```
   - Jika diisi stok awal > 0, sistem otomatis mencatat histori mutasi pertama ke tabel `stock_movements`.

4. **Redirect HTTP**:
   - Controller tidak langsung merender view secara mentah (mencegah duplikasi submit saat refresh / Post-Redirect-Get pattern):
     ```php
     return redirect()->route('products.index')->with('success', "Produk '{$product->name}' berhasil ditambahkan ke inventaris.");
     ```

5. **Tampilkan Data Hasil di View**:
   - Browser menerima respon redirect dan membuka halaman `GET /products`.
   - Komponen `<x-alert type="success">` menampilkan banner konfirmasi sukses.
   - Produk baru langsung tampil pada tabel katalog lengkap dengan SKU, badge kategori, harga terformat, dan kuantitas stok terkini.

---

## 💻 Persyaratan Lingkungan

- **PHP**: Versi 8.0.30+ atau 8.1 / 8.2 (ekstensi: `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`)
- **Web Server / Database**: Apache & MySQL / MariaDB (tersedia di XAMPP)
- **Package Manager**: Composer 2.x
- **Framework**: Laravel 9.52.x

---

## 🛠️ Panduan Instalasi & Menjalankan

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di komputer lokal:

### 1. Masuk ke Direktori Proyek
```bash
cd "C:\Users\SN55C\Documents\Mini_project4"
```

### 2. Konfigurasi Environment (`.env`)
Pastikan file `.env` sudah sesuai dengan konfigurasi database MySQL:
```env
APP_NAME="Inventory Management System"
APP_ENV=local
APP_KEY=base64:Cdh7JwMCV0yzfvf/P9e9E1y/xulowH3rSm9SvhS5I50=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Pastikan MySQL Aktif & Buat Database
Pastikan modul MySQL pada XAMPP Control Panel sudah berjalan (Running pada Port 3306), dan pastikan database `inventory_db` sudah dibuat di phpMyAdmin / MySQL CLI:
```sql
CREATE DATABASE IF NOT EXISTS inventory_db;
```

### 4. Jalankan Migration & Seeder
Perintah ini akan membuat seluruh tabel dari kondisi awal dan mengisi dataset awal pengujian (termasuk akun demo, kategori, supplier, produk P4, dan transaksi awal):
```bash
php artisan migrate:fresh --seed
```

### 5. Periksa Daftar Route (Opsional)
Untuk memverifikasi seluruh pemetaan route RESTful resource:
```bash
php artisan route:list
```

### 6. Jalankan Server Lokal Laravel
```bash
php artisan serve
```
Aplikasi dapat dibuka melalui peramban web pada alamat:  
👉 **`http://127.0.0.1:8000`**

---

## 🔑 Akun Pengguna Demo

Aplikasi menggunakan autentikasi demo berbasis session (`demo.auth`) untuk kemudahan pengujian praktikum:

| Username | Password | Role / Hak Akses |
|---|---|---|
| **`admin`** | `admin` | Administrator Utama (Akses seluruh menu & CRUD) |
| **`staff`** | `staff` | Staff Operasional Gudang |

---

## 🗄️ Struktur Database & Relasi Eloquent

```
+--------------------+            +------------------------+
|     categories     |            |       suppliers        |
+--------------------+            +------------------------+
| id (PK)            |            | id (PK)                |
| name (VARCHAR 100) |            | name (VARCHAR 150)     |
| description        |            | phone, email, address  |
| timestamps         |            | timestamps             |
+---------┬----------+            +-----------┬------------+
          │ 1                                 │ M
          │ hasMany                           │ belongsToMany
          │ belongsTo                         │
          │ M                                 │ M (via product_supplier)
+---------┴───────────────────────────────────┴------------+
|                        products                          |
+----------------------------------------------------------+
| id (PK)                                                  |
| category_id (FK -> categories.id)                        |
| sku (VARCHAR 50, UNIQUE)                                 |
| name (VARCHAR 150)                                       |
| price (DECIMAL 12,2)                                     |
| stock (UNSIGNED INT)                                     |
| minimum_stock (UNSIGNED INT)                             |
| timestamps                                               |
+----------------------------┬-----------------------------+
                             │ 1
                             │ hasMany
                             │ belongsTo
                             │ M
              +--------------┴---------------+
              |       stock_movements        |
              +------------------------------+
              | id (PK)                      |
              | product_id (FK -> products)  |
              | type ('in', 'out')           |
              | quantity (UNSIGNED INT)      |
              | note (VARCHAR 255)           |
              | user_name (VARCHAR 80)       |
              | timestamps                   |
              +------------------------------+
```

### Relasi Model Eloquent:
- **`Category`** `hasMany` `Product`: Satu kategori memiliki banyak produk.
- **`Product`** `belongsTo` `Category`: Setiap produk terkait ke satu kategori (`restrictOnDelete` untuk integritas data).
- **`Product`** `belongsToMany` `Supplier`: Relasi banyak-ke-banyak antara produk dan supplier melalui tabel pivot `product_supplier`.
- **`Product`** `hasMany` `StockMovement`: Satu produk mencatat banyak histori mutasi stok persediaan.
- **`StockMovement`** `belongsTo` `Product`: Setiap transaksi mutasi merujuk ke produk terkait.

---

## 📂 Struktur Proyek

```
Mini_project4/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php             # Login & Logout demo session
│   │   │   ├── CategoryController.php         # CRUD Kategori
│   │   │   ├── DashboardController.php        # Ringkasan statistik & alert stok
│   │   │   ├── ProductController.php          # Resource Controller CRUD Produk
│   │   │   ├── StockMovementController.php    # Transaksi Barang Masuk & Keluar
│   │   │   └── SupplierController.php         # CRUD Supplier Mitra
│   │   ├── Middleware/
│   │   │   └── DemoAuth.php                   # Proteksi route berbasis sesi demo
│   │   └── Kernel.php                         # Pendaftaran middleware route
│   └── Models/
│       ├── Category.php                       # Model Kategori
│       ├── Product.php                        # Model Produk & Relasi
│       ├── StockMovement.php                  # Model Transaksi Mutasi
│       ├── Supplier.php                       # Model Supplier Mitra
│       └── User.php                           # Model Pengguna
├── database/
│   ├── factories/
│   │   ├── CategoryFactory.php                # Factory data dummy Kategori
│   │   ├── ProductFactory.php                 # Factory data dummy Produk
│   │   ├── StockMovementFactory.php           # Factory data dummy Transaksi
│   │   └── SupplierFactory.php                # Factory data dummy Supplier
│   ├── migrations/
│   │   ├── 2026_01_01_000001_create_categories_table.php
│   │   ├── 2026_01_01_000002_create_products_table.php
│   │   ├── 2026_01_01_000003_create_suppliers_table.php
│   │   ├── 2026_01_01_000004_create_product_supplier_table.php
│   │   └── 2026_01_01_000005_create_stock_movements_table.php
│   └── seeders/
│       └── DatabaseSeeder.php                 # Seeder data acuan P4 & user
├── public/
│   └── screenshots/                           # Berkas tangkapan layar antarmuka
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php                # Halaman login modern
│       ├── categories/
│       │   ├── edit.blade.php
│       │   └── index.blade.php
│       ├── components/
│       │   └── alert.blade.php                # Blade Component Flash Alert
│       ├── dashboard/
│       │   └── index.blade.php                # Tampilan Dashboard
│       ├── layouts/
│       │   └── app.blade.php                  # Master Blade Layout Template
│       ├── products/
│       │   ├── create.blade.php               # Form Tambah Produk
│       │   ├── edit.blade.php                 # Form Edit Produk
│       │   ├── index.blade.php                # Listing Produk + Filter + Sort + Pagination
│       │   └── show.blade.php                 # Detail Produk & Histori Mutasi
│       ├── stock/
│       │   └── index.blade.php                # Form Transaksi & Log Mutasi
│       └── suppliers/
│           ├── edit.blade.php
│           └── index.blade.php
├── routes/
│   └── web.php                                # Deklarasi RESTful Routes & Grouping
├── MVC.md                                     # Penjelasan Arsitektur MVC Lengkap
├── README.md                                  # Dokumentasi Utama Proyek
└── .env                                       # Konfigurasi Environment Aplikasi
```

---

## 📸 Dokumentasi Screenshot

Dokumentasi gambar antarmuka tersimpan di dalam folder `public/screenshots/`:
1. **Halaman Login Demo**: `public/screenshots/login.png`
2. **Dashboard Inventaris & Low Stock**: `public/screenshots/dashboard.png`
3. **Katalog Produk & Filter**: `public/screenshots/products-list.png`
4. **Formulir Input Produk & CSRF**: `public/screenshots/products-form.png`
5. **Data Kategori**: `public/screenshots/categories.png`
6. **Data Supplier**: `public/screenshots/suppliers.png`
7. **Transaksi Stok Masuk / Keluar**: `public/screenshots/stock-transactions.png`
8. **Kode Sumber Route Resource**: `public/screenshots/route-code.png`
9. **Kode Sumber Controller CRUD**: `public/screenshots/crud-controller-code.png`
10. **Kode Sumber Blade Form**: `public/screenshots/blade-form-code.png`

---

## 🎓 Kesimpulan & Checklist Pemenuhan Modul P5

| No | Kriteria Penilaian / Checklist | Status | Catatan Implementasi |
|---|---|:---:|---|
| 1 | Project Laravel dapat dijalankan | ✅ LENGKAP | Server berjalan di port 8000 via `artisan serve` |
| 2 | `.env` dan koneksi database benar | ✅ LENGKAP | Terhubung ke MySQL `inventory_db` di port 3306 |
| 3 | `route:list` menampilkan route yang diharapkan | ✅ LENGKAP | `Route::resource('products')`, named routes, grouping |
| 4 | Migration dapat dijalankan dari database kosong | ✅ LENGKAP | 5 file migrasi baru berjalan rapi dengan foreign key |
| 5 | Seeder menghasilkan data uji | ✅ LENGKAP | Menghasilkan dataset P4, akun demo, dan log transaksi |
| 6 | Model memiliki relationship yang benar | ✅ LENGKAP | `hasMany`, `belongsTo`, dan `belongsToMany` pivot |
| 7 | Resource controller menangani CRUD | ✅ LENGKAP | `ProductController` mengimplementasikan 7 metode RESTful |
| 8 | Blade memakai layout dan escaping | ✅ LENGKAP | Layout master `layouts/app`, escaping `{{ }}`, layout component `<x-alert>` |
| 9 | Form memiliki `@csrf` dan validasi | ✅ LENGKAP | Proteksi CSRF, rule validasi SKU unik, `@error` message |
| 10 | Search & pagination mempertahankan query string | ✅ LENGKAP | Menggunakan `withQueryString()` pada pagination produk |
| 11 | Business rule stok dari P4 tetap berjalan | ✅ LENGKAP | Pencegahan stok negatif, kalkulasi aset, dan audit trail |
| 12 | Penjelasan arsitektur MVC | ✅ LENGKAP | Terdokumentasi lengkap di README.md dan MVC.md |
