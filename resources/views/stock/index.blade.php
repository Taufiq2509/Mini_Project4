@extends('layouts.app')

@section('title', 'Transaksi Persediaan (Barang Masuk / Keluar)')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Transaksi Persediaan Barang</h2>
        <p>Catat mutasi barang masuk (penerimaan) dan barang keluar (pengiriman) secara akurat</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
    <!-- Form Transaksi Mutasi -->
    <div class="card" style="align-self: flex-start;">
        <div class="card-header">
            <h3>+ Input Transaksi Mutasi Stok</h3>
        </div>

        <form method="POST" action="{{ route('stock.store') }}">
            @csrf

            <!-- Pilih Produk -->
            <div class="form-group" style="margin-bottom: 16px;">
                <label for="product_id">Pilih Produk <span style="color: var(--danger)">*</span></label>
                <select id="product_id" name="product_id" class="form-control @error('product_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Barang dari Inventaris --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" @selected(old('product_id') == $p->id)>
                            [{{ $p->sku }}] {{ $p->name }} — Stok Saat Ini: {{ $p->stock }} unit
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Jenis Transaksi -->
            <div class="form-group" style="margin-bottom: 16px;">
                <label for="type">Jenis Mutasi <span style="color: var(--danger)">*</span></label>
                <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
                    <option value="in" @selected(old('type') === 'in')>⬇ Barang Masuk (+ Tambah Stok Fisik)</option>
                    <option value="out" @selected(old('type') === 'out')>⬆ Barang Keluar (- Kurangi Stok Fisik)</option>
                </select>
                @error('type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Jumlah -->
            <div class="form-group" style="margin-bottom: 16px;">
                <label for="quantity">Jumlah Unit <span style="color: var(--danger)">*</span></label>
                <input 
                    type="number" 
                    id="quantity" 
                    name="quantity" 
                    value="{{ old('quantity', 1) }}" 
                    min="1" 
                    class="form-control @error('quantity') is-invalid @enderror" 
                    required
                >
                <small style="color: var(--text-muted); font-size: 11px;">Sistem akan memvalidasi agar stok keluar tidak melebihi stok yang tersedia</small>
                @error('quantity')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Catatan -->
            <div class="form-group" style="margin-bottom: 22px;">
                <label for="note">Catatan / Keterangan Transaksi</label>
                <input 
                    type="text" 
                    id="note" 
                    name="note" 
                    value="{{ old('note') }}" 
                    class="form-control @error('note') is-invalid @enderror" 
                    placeholder="Contoh: Pembelian PO-102 / Retur / Penjualan cabang"
                >
                @error('note')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                💾 Catat Transaksi Persediaan
            </button>
        </form>
    </div>

    <!-- Tabel Histori Mutasi Transaksi -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-header">
            <div>
                <h3>Histori Mutasi Stok (Audit Trail)</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                    Catatan seluruh perpindahan barang dengan timestamp dan user pencatat
                </p>
            </div>
            <span class="badge badge-neutral">{{ $movements->total() }} Catatan</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Waktu Transaksi</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Petugas</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                        <tr>
                            <td style="color: var(--text-muted); font-size: 13px; white-space: nowrap;">
                                {{ $m->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <b><a href="{{ route('products.show', $m->product) }}" style="color: var(--secondary); text-decoration: none;">{{ $m->product->name }}</a></b>
                                <div style="font-size: 12px; color: var(--text-muted);">{{ $m->product->sku }}</div>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $m->product->category->name }}</span>
                            </td>
                            <td>
                                @if($m->type === 'in')
                                    <span class="badge badge-success">⬇ Barang Masuk</span>
                                @else
                                    <span class="badge badge-danger">⬆ Barang Keluar</span>
                                @endif
                            </td>
                            <td>
                                <b style="font-size: 15px; color: {{ $m->type === 'in' ? 'var(--success)' : 'var(--danger)' }};">
                                    {{ $m->type === 'in' ? '+' : '-' }}{{ $m->quantity }} unit
                                </b>
                            </td>
                            <td>
                                <span class="badge badge-neutral">👤 {{ $m->user_name }}</span>
                            </td>
                            <td style="color: var(--text-muted); font-size: 13px;">
                                {{ $m->note ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                Belum ada transaksi mutasi stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="pagination-container" style="padding-top: 20px; border-top: 1px solid var(--border);">
                <div style="font-size: 13px; color: var(--text-muted);">
                    Halaman {{ $movements->currentPage() }} dari {{ $movements->lastPage() }}
                </div>
                <div>
                    {{ $movements->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
