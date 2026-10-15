@extends('layouts.app')

@section('title', 'Detail Produk - ' . $product->name)

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Detail Produk: {{ $product->name }}</h2>
        <p>Kode SKU: <code><b>{{ $product->sku }}</b></code> &bull; Kategori: <b>{{ $product->category->name }}</b></p>
    </div>
    <div class="action-buttons">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
            ✏️ Edit Produk
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            &larr; Kembali ke Daftar
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 24px;">
    <!-- Product Info Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3>Informasi Produk</h3>
            @if($product->stock <= $product->minimum_stock)
                <span class="badge badge-danger"><span class="pulse-dot"></span> Stok Rendah</span>
            @else
                <span class="badge badge-success">✓ Stok Aman</span>
            @endif
        </div>

        <table style="font-size: 14px;">
            <tbody>
                <tr>
                    <td style="width: 35%; color: var(--text-muted); font-weight: 600;">SKU Produk</td>
                    <td><code><b>{{ $product->sku }}</b></code></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 600;">Nama Produk</td>
                    <td><b>{{ $product->name }}</b></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 600;">Kategori</td>
                    <td><span class="badge badge-info">{{ $product->category->name }}</span></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 600;">Harga Satuan</td>
                    <td><b style="font-size: 16px; color: var(--primary);">Rp {{ number_format($product->price, 0, ',', '.') }}</b></td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 600;">Stok Tersedia</td>
                    <td><b>{{ $product->stock }}</b> unit</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 600;">Batas Minimum</td>
                    <td>{{ $product->minimum_stock }} unit</td>
                </tr>
                <tr>
                    <td style="color: var(--text-muted); font-weight: 600;">Total Valuasi Stok</td>
                    <td><b>Rp {{ number_format($product->price * $product->stock, 0, ',', '.') }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Suppliers Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3>Supplier Mitra ({{ $product->suppliers->count() }})</h3>
        </div>

        @if($product->suppliers->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($product->suppliers as $sup)
                    <div style="padding: 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg);">
                        <div style="font-weight: 700; color: var(--secondary);">{{ $sup->name }}</div>
                        <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                            📞 {{ $sup->phone ?: '-' }} &bull; ✉️ {{ $sup->email ?: '-' }}
                        </div>
                        @if($sup->address)
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                                📍 {{ $sup->address }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--text-muted); font-size: 14px;">Belum ada supplier yang ditautkan ke produk ini.</p>
        @endif
    </div>
</div>

<!-- Stock Movements History for this product -->
<div class="card">
    <div class="card-header">
        <div>
            <h3>Riwayat Mutasi Persediaan Produk Ini</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                Semua catatan barang masuk dan keluar untuk produk {{ $product->name }}
            </p>
        </div>
        <a href="{{ route('stock.index') }}" class="btn btn-sm btn-primary">
            + Tambah Transaksi Mutasi
        </a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Waktu Catat</th>
                    <th>Jenis Transaksi</th>
                    <th>Jumlah</th>
                    <th>Petugas</th>
                    <th>Catatan Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($product->movements as $m)
                    <tr>
                        <td style="color: var(--text-muted); font-size: 13px;">
                            {{ $m->created_at->format('d M Y, H:i') }}
                        </td>
                        <td>
                            @if($m->type === 'in')
                                <span class="badge badge-success">⬇ Barang Masuk</span>
                            @else
                                <span class="badge badge-danger">⬆ Barang Keluar</span>
                            @endif
                        </td>
                        <td><b>{{ $m->quantity }}</b> unit</td>
                        <td><span class="badge badge-neutral">👤 {{ $m->user_name }}</span></td>
                        <td>{{ $m->note ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 24px; color: var(--text-muted);">
                            Belum ada riwayat transaksi mutasi untuk produk ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
