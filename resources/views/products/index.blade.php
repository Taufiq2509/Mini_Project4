@extends('layouts.app')

@section('title', 'Data Produk Inventaris')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Data Inventaris Produk</h2>
        <p>Kelola seluruh katalog barang, harga satuan, status persediaan, dan supplier terkait</p>
    </div>
    <div class="action-buttons">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <span>+</span> Tambah Produk Baru
        </a>
    </div>
</div>

<!-- Search & Filtering Bar -->
<div class="card" style="padding: 20px;">
    <form method="GET" action="{{ route('products.index') }}">
        <div class="form-grid" style="margin-bottom: 14px;">
            <div class="form-group">
                <label for="search">Pencarian Produk</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ request('search') }}" 
                    class="form-control" 
                    placeholder="Ketik SKU atau Nama barang..."
                >
            </div>

            <div class="form-group">
                <label for="category_id">Filter Kategori</label>
                <select id="category_id" name="category_id" class="form-control">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="stock_status">Status Stok</label>
                <select id="stock_status" name="stock_status" class="form-control">
                    <option value="">-- Semua Status Stok --</option>
                    <option value="low" @selected(request('stock_status') === 'low')>⚠️ Stok Menipis (&le; Min)</option>
                    <option value="safe" @selected(request('stock_status') === 'safe')>✓ Stok Aman (> Min)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sort">Urutkan Berdasarkan</label>
                <select id="sort" name="sort" class="form-control">
                    <option value="name" @selected(request('sort', 'name') === 'name')>Nama Produk</option>
                    <option value="sku" @selected(request('sort') === 'sku')>SKU Produk</option>
                    <option value="price" @selected(request('sort') === 'price')>Harga Produk</option>
                    <option value="stock" @selected(request('sort') === 'stock')>Jumlah Stok</option>
                    <option value="created_at" @selected(request('sort') === 'created_at')>Waktu Dibuat</option>
                </select>
            </div>

            <div class="form-group">
                <label for="direction">Arah Urutan</label>
                <select id="direction" name="direction" class="form-control">
                    <option value="asc" @selected(request('direction', 'asc') === 'asc')>Naik (A-Z / Terendah)</option>
                    <option value="desc" @selected(request('direction') === 'desc')>Turun (Z-A / Tertinggi)</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end; align-items: center;">
            @if(request()->anyFilled(['search', 'category_id', 'stock_status', 'sort', 'direction']))
                <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                    Reset Filter
                </a>
            @endif
            <button type="submit" class="btn btn-primary btn-sm">
                🔍 Terapkan Filter
            </button>
        </div>
    </form>
</div>

<!-- Products Table Card -->
<div class="card">
    <div class="card-header">
        <div>
            <h3>Daftar Produk ({{ $products->total() }} Total)</h3>
        </div>
        <div style="font-size: 13px; color: var(--text-muted);">
            Menampilkan halaman <b>{{ $products->currentPage() }}</b> dari <b>{{ $products->lastPage() }}</b>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga Satuan</th>
                    <th>Stok Fisik</th>
                    <th>Supplier Mitra</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php
                        $isLow = $product->stock <= $product->minimum_stock;
                    @endphp
                    <tr>
                        <td>
                            <code><b>{{ $product->sku }}</b></code>
                        </td>
                        <td>
                            <b><a href="{{ route('products.show', $product) }}" style="color: var(--secondary); text-decoration: none;">{{ $product->name }}</a></b>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $product->category->name }}</span>
                        </td>
                        <td>
                            <b>Rp {{ number_format($product->price, 0, ',', '.') }}</b>
                        </td>
                        <td>
                            @if($isLow)
                                <span class="badge badge-danger">
                                    <span class="pulse-dot"></span> {{ $product->stock }} (Min: {{ $product->minimum_stock }})
                                </span>
                            @else
                                <span class="badge badge-success">
                                    {{ $product->stock }} unit
                                </span>
                            @endif
                        </td>
                        <td>
                            @forelse($product->suppliers as $sup)
                                <span class="badge badge-neutral" style="margin-right: 4px; margin-bottom: 2px;">{{ $sup->name }}</span>
                            @empty
                                <span style="color: var(--text-muted); font-size: 13px;">-</span>
                            @endforelse
                        </td>
                        <td style="text-align: right;">
                            <div class="action-buttons" style="justify-content: flex-end;">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-secondary" title="Detail">
                                    Detail
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-secondary" title="Edit">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $product->name }}?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 36px; color: var(--text-muted);">
                            <div style="font-size: 28px; margin-bottom: 8px;">📦</div>
                            <p style="font-weight: 600;">Tidak ada produk yang cocok dengan kriteria pencarian.</p>
                            <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary" style="margin-top: 12px;">
                                + Tambah Produk Sekarang
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination links -->
    @if($products->hasPages())
        <div class="pagination-container" style="padding-top: 20px; border-top: 1px solid var(--border);">
            <div style="font-size: 13px; color: var(--text-muted);">
                Menampilkan {{ $products->firstItem() }} sampai {{ $products->lastItem() }} dari {{ $products->total() }} hasil
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
