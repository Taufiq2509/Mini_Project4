@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Tambah Produk Baru</h2>
        <p>Alur Request: Form &rarr; Validasi &rarr; Simpan ke Database &rarr; Redirect ke Daftar Produk</p>
    </div>
    <div class="action-buttons">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            &larr; Kembali ke Daftar
        </a>
    </div>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <form method="POST" action="{{ route('products.store') }}">
        @csrf

        <div class="form-grid">
            <!-- SKU Input -->
            <div class="form-group">
                <label for="sku">Kode SKU Produk <span style="color: var(--danger)">*</span></label>
                <input 
                    type="text" 
                    id="sku" 
                    name="sku" 
                    value="{{ old('sku') }}" 
                    class="form-control @error('sku') is-invalid @enderror" 
                    placeholder="Contoh: EL-004 atau PRD-102"
                    required
                >
                <small style="color: var(--text-muted); font-size: 11px;">SKU harus unik dan tidak boleh sama dengan produk lain</small>
                @error('sku')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nama Produk -->
            <div class="form-group">
                <label for="name">Nama Produk <span style="color: var(--danger)">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    class="form-control @error('name') is-invalid @enderror" 
                    placeholder="Contoh: Headset Gaming 7.1 Surround"
                    required
                >
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kategori -->
            <div class="form-group">
                <label for="category_id">Kategori Produk <span style="color: var(--danger)">*</span></label>
                <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Harga Satuan -->
            <div class="form-group">
                <label for="price">Harga Satuan (Rp) <span style="color: var(--danger)">*</span></label>
                <input 
                    type="number" 
                    id="price" 
                    name="price" 
                    value="{{ old('price', 0) }}" 
                    min="0" 
                    step="0.01" 
                    class="form-control @error('price') is-invalid @enderror" 
                    required
                >
                @error('price')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Stok Awal -->
            <div class="form-group">
                <label for="stock">Stok Awal Fisik <span style="color: var(--danger)">*</span></label>
                <input 
                    type="number" 
                    id="stock" 
                    name="stock" 
                    value="{{ old('stock', 0) }}" 
                    min="0" 
                    class="form-control @error('stock') is-invalid @enderror" 
                    required
                >
                <small style="color: var(--text-muted); font-size: 11px;">Stok awal otomatis tercatat di histori mutasi</small>
                @error('stock')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Minimum Stok -->
            <div class="form-group">
                <label for="minimum_stock">Batas Minimum Stok (Peringatan) <span style="color: var(--danger)">*</span></label>
                <input 
                    type="number" 
                    id="minimum_stock" 
                    name="minimum_stock" 
                    value="{{ old('minimum_stock', 5) }}" 
                    min="0" 
                    class="form-control @error('minimum_stock') is-invalid @enderror" 
                    required
                >
                <small style="color: var(--text-muted); font-size: 11px;">Memicu peringatan stok rendah jika persediaan &le; batas ini</small>
                @error('minimum_stock')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Supplier Checkbox Group -->
        <div class="form-group" style="margin-top: 10px; margin-bottom: 24px;">
            <label>Supplier Penyedia (Pilih Satu atau Lebih Mitra):</label>
            <div class="checkbox-group">
                @foreach($suppliers as $sup)
                    <label class="checkbox-item">
                        <input 
                            type="checkbox" 
                            name="suppliers[]" 
                            value="{{ $sup->id }}" 
                            @checked(is_array(old('suppliers')) && in_array($sup->id, old('suppliers')))
                        >
                        <div>
                            <b>{{ $sup->name }}</b>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ $sup->phone ?: 'No telp -' }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('suppliers')
                <span class="invalid-feedback" style="display:block; margin-top: 6px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid var(--border);">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                💾 Simpan Produk ke Inventaris
            </button>
        </div>
    </form>
</div>
@endsection
