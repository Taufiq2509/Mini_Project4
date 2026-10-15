# Penjelasan Arsitektur MVC & Request Lifecycle

Dokumen ini mendokumentasikan implementasi arsitektur **Model-View-Controller (MVC)** pada proyek **Inventory Management System (P5 Laravel)**.

---

## 1. Filosofi MVC pada Laravel

Arsitektur MVC memisahkan tanggung jawab (Separation of Concerns) dalam tiga pilar utama:
- **Model**: Mengelola data, skema, validasi level data, dan hubungan (relasi) antar entitas bisnis.
- **View**: Menyajikan data kepada pengguna melalui template HTML/Blade dengan escaping aman.
- **Controller**: Menerima request pengguna, menerapkan alur logika bisnis, memanggil model/database, dan memilih respon yang tepat.

---

## 2. Diagram Alur Request-to-Response

```
1. Request Masuk
   Pengguna / Browser ──[ HTTP GET /products ]──► routes/web.php
                                                        │
2. Filter Middleware                                    ▼
   Middleware DemoAuth ◄──────────────────── routes/web.php
   (Memeriksa sesi pengguna)
        │ (Lolos)
        ▼
3. Controller Menangani Request
   ProductController@index(Request $request)
        │
        │──► (A) Baca parameter: search, category_id, sort, direction
        │──► (B) Bangun query Eloquent dengan eager loading
        ▼
4. Interaksi Model & Database
   Model Product & Category ──[ SQL Query via PDO ]──► Database MySQL
        ▲                                                      │
        └─────────────────[ Recordset Hasil ]──────────────────┘
        │
5. Controller Memilih Respon
   return view('products.index', compact('products', 'categories'));
        │
        ▼
6. View Merender Output
   Blade Template Engine (layouts/app.blade.php + products/index.blade.php)
   - Melakukan escaping nilai dengan {{ $product->name }}
   - Melakukan looping dengan @forelse
   - Menghasilkan markup HTML utuh
        │
        ▼
7. Respon Dikirim ke Client
   HTTP Response 200 OK ──► Browser Pengguna
```

---

## 3. Rincian Tanggung Jawab Komponen

### A. Route (`routes/web.php`)
Route bertindak sebagai gerbang masuk pertama (router/dispatcher). Route memetakan URI dan HTTP method (GET, POST, PUT, DELETE) ke method controller yang bersangkutan.

Contoh deklarasi route resource pada proyek:
```php
Route::middleware('demo.auth')->group(function () {
    Route::resource('products', ProductController::class);
});
```

Perintah `Route::resource('products', ProductController::class)` secara otomatis menghasilkan 7 endpoint standar RESTful:
1. `GET /products` &rarr; `ProductController@index` (`products.index`)
2. `GET /products/create` &rarr; `ProductController@create` (`products.create`)
3. `POST /products` &rarr; `ProductController@store` (`products.store`)
4. `GET /products/{product}` &rarr; `ProductController@show` (`products.show`)
5. `GET /products/{product}/edit` &rarr; `ProductController@edit` (`products.edit`)
6. `PUT/PATCH /products/{product}` &rarr; `ProductController@update` (`products.update`)
7. `DELETE /products/{product}` &rarr; `ProductController@destroy` (`products.destroy`)

### B. Controller (`app/Http/Controllers/`)
Controller bertindak sebagai konduktor/orkestrator. Controller tidak mengandung logika bisnis yang berlebihan (Fat Model, Skinny Controller), melainkan:
1. Menerima input dari HTTP Request.
2. Memvalidasi format input (`$request->validate(...)`).
3. Memanggil model untuk membaca atau menyimpan perubahan data.
4. Menentukan respon (merender view atau melakukan HTTP redirect dengan flash data).

Contoh dari `ProductController@store`:
```php
public function store(Request $request): RedirectResponse
{
    // Validasi
    $validated = $request->validate([
        'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
        'name' => ['required', 'string', 'max:150'],
        'category_id' => ['required', 'integer', 'exists:categories,id'],
        'price' => ['required', 'numeric', 'min:0'],
        'stock' => ['required', 'integer', 'min:0'],
        'minimum_stock' => ['required', 'integer', 'min:0'],
        'suppliers' => ['nullable', 'array'],
    ]);

    // Eksekusi Model
    $product = Product::create($validated);
    if (!empty($validated['suppliers'])) {
        $product->suppliers()->sync($validated['suppliers']);
    }

    // Response Redirect
    return redirect()->route('products.index')->with('success', 'Produk berhasil disimpan.');
}
```

### C. Model (`app/Models/`)
Model merepresentasikan tabel dalam database dan menyediakan abstraksi Object-Relational Mapping (ORM) melalui Eloquent.

Contoh `Product.php`:
```php
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'sku', 'name', 'price', 'stock', 'minimum_stock'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
```

### D. View (`resources/views/`)
View bertugas mengonstruksi tampilan antarmuka (UI). Pada Laravel, View memanfaatkan Blade Templating Engine yang memberikan keunggulan:
- **Layout Inheritance**: Menghindari duplikasi markup HTML besar dengan `@extends('layouts.app')` dan `@section('content')`.
- **Reusable Component**: Komponen UI yang sering dipakai dibuat terpisah, misalnya `<x-alert type="success">`.
- **Output Escaping**: Mencegah celah keamanan Cross-Site Scripting (XSS) secara otomatis menggunakan kurung kurawal ganda `{{ $data }}`.
- **Direktif Kontrol**: Menggunakan `@if`, `@else`, `@forelse`, `@empty`, `@error`, dan `@csrf`.

---

## 4. Perlindungan Business Rule dari P4 ke P5

| Aturan Bisnis (P4 PHP Native) | Implementasi Arsitektur di Laravel (P5) |
|---|---|
| SKU harus unik di database | Validasi Request `'sku' => 'unique:products,sku'` dan Unique constraint pada database migration |
| Stok tidak boleh bernilai negatif saat transaksi keluar | Validasi kondisi di dalam `StockMovementController` dengan `ValidationException` dan `DB::transaction` |
| Kalkulasi nilai total persediaan | Menggunakan Eloquent agregasi `DB::raw('SUM(price * stock)')` di `DashboardController` |
| Peringatan stok menipis jika stok &le; minimum | Query kolom Eloquent `whereColumn('stock', '<=', 'minimum_stock')` dan visual badge warning |
| Kategori tidak boleh dihapus jika masih ada produk | Foreign key constraint `restrictOnDelete()` dan validasi controller `$category->products()->exists()` |
| Relasi multi-vendor/supplier per produk | Tabel pivot `product_supplier` dengan Eloquent `belongsToMany` dan sync method |
| Catatan mutasi persediaan (Audit trail) | Tabel `stock_movements` yang mencatat user, timestamp, dan perubahan kuantitas |

---

## 5. Kesimpulan

Migrasi dari PHP Native ke Laravel MVC memberikan keuntungan nyata:
1. **Maintainability**: Struktur kode terorganisir rapi pada folder masing-masing sesuai tanggung jawabnya.
2. **Security**: Terproteksi secara bawaan dari SQL Injection (PDO parameter binding di Eloquent), XSS (Blade escaping), dan CSRF (`@csrf`).
3. **Scalability**: Penambahan fitur baru di masa mendatang (misalnya REST API atau ekspor laporan) dapat dilakukan dengan mudah tanpa mengacaukan tampilan atau query database yang sudah ada.
