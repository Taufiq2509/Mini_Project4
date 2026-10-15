@extends('layouts.app')

@section('title', 'Dashboard Ringkasan Inventaris')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Dashboard Inventaris</h2>
        <p>Ringkasan status stok, valuasi persediaan, dan pergerakan barang secara real-time</p>
    </div>
    <div class="action-buttons">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <span>+</span> Tambah Produk Baru
        </a>
        <a href="{{ route('stock.index') }}" class="btn btn-secondary">
            <span>🔄</span> Catat Transaksi Stok
        </a>
    </div>
</div>

<!-- Stats Overview Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Produk</div>
            <div class="stat-value">{{ $totalProducts }}</div>
            <div class="stat-desc">{{ $totalCategories }} kategori &bull; {{ $totalSuppliers }} supplier</div>
        </div>
        <div class="stat-icon blue">📦</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Nilai Persediaan</div>
            <div class="stat-value" style="font-size: 24px;">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
            <div class="stat-desc">Estimasi total valuasi aset stok</div>
        </div>
        <div class="stat-icon emerald">💰</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Produk Stok Rendah</div>
            <div class="stat-value" style="color: {{ $lowStockProducts->count() > 0 ? 'var(--danger)' : 'var(--success)' }};">
                {{ $lowStockProducts->count() }}
            </div>
            <div class="stat-desc">Stok &le; batas minimum peringatan</div>
        </div>
        <div class="stat-icon amber">⚠️</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Aktivitas Transaksi</div>
            <div class="stat-value">{{ $countStockIn + $countStockOut }}</div>
            <div class="stat-desc">{{ $countStockIn }} masuk &bull; {{ $countStockOut }} keluar</div>
        </div>
        <div class="stat-icon purple">📊</div>
    </div>
</div>

<!-- Low Stock Warning Table -->
<div class="card">
    <div class="card-header">
        <div>
            <h3>⚠️ Peringatan Stok Rendah (Perlu Restock)</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                Daftar produk yang stok fisiknya sudah berada pada atau di bawah batas minimum
            </p>
        </div>
        @if($lowStockProducts->count() > 0)
            <span class="badge badge-danger">
                <span class="pulse-dot"></span> {{ $lowStockProducts->count() }} Perlu Perhatian
            </span>
        @else
            <span class="badge badge-success">✓ Semua Stok Aman</span>
        @endif
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Stok Saat Ini</th>
                    <th>Batas Minimum</th>
                    <th>Status</th>
                    <th>Aksi Cepat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStockProducts as $product)
                    <tr>
                        <td><code><b>{{ $product->sku }}</b></code></td>
                        <td><b>{{ $product->name }}</b></td>
                        <td><span class="badge badge-neutral">{{ $product->category->name }}</span></td>
                        <td>
                            <span class="badge badge-danger">
                                <span class="pulse-dot"></span> {{ $product->stock }} unit
                            </span>
                        </td>
                        <td>{{ $product->minimum_stock }} unit</td>
                        <td>
                            @if($product->stock === 0)
                                <span class="badge badge-danger">HABIS</span>
                            @else
                                <span class="badge badge-warning">KRITIS</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('stock.index') }}" class="btn btn-sm btn-primary" title="Tambah stok masuk">
                                    + Restock
                                </a>
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-secondary">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 28px; color: var(--text-muted);">
                            🎉 Luar biasa! Tidak ada produk dengan stok menipis saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Stock Movements Activity -->
<div class="card">
    <div class="card-header">
        <div>
            <h3>🔄 Riwayat Mutasi Persediaan Terbaru</h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                Catatan mutasi barang masuk dan keluar terakhir
            </p>
        </div>
        <a href="{{ route('stock.index') }}" class="btn btn-sm btn-secondary">
            Lihat Semua Transaksi ➔
        </a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>SKU & Produk</th>
                    <th>Jenis Transaksi</th>
                    <th>Jumlah</th>
                    <th>Petugas</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentMovements as $movement)
                    <tr>
                        <td style="color: var(--text-muted); font-size: 13px;">
                            {{ $movement->created_at->format('d M Y, H:i') }}
                        </td>
                        <td>
                            <b>{{ $movement->product->name }}</b>
                            <div style="font-size: 12px; color: var(--text-muted);">{{ $movement->product->sku }}</div>
                        </td>
                        <td>
                            @if($movement->type === 'in')
                                <span class="badge badge-success">⬇ Barang Masuk</span>
                            @else
                                <span class="badge badge-danger">⬆ Barang Keluar</span>
                            @endif
                        </td>
                        <td><b>{{ $movement->quantity }}</b> unit</td>
                        <td><span class="badge badge-neutral">👤 {{ $movement->user_name }}</span></td>
                        <td style="color: var(--text-muted); font-size: 13px;">{{ $movement->note ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted);">
                            Belum ada riwayat transaksi barang masuk/keluar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
