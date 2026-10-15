@extends('layouts.app')

@section('title', 'Data Kategori Produk')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Kategori Produk</h2>
        <p>Klasifikasi jenis barang inventaris untuk mempermudah grouping dan pelaporan</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
    <!-- Form Tambah Kategori -->
    <div class="card" style="align-self: flex-start;">
        <div class="card-header">
            <h3>+ Tambah Kategori Baru</h3>
        </div>

        <form method="POST" action="{{ route('categories.store') }}">
            @csrf

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="name">Nama Kategori <span style="color: var(--danger)">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    class="form-control @error('name') is-invalid @enderror" 
                    placeholder="Contoh: Perangkat Jaringan"
                    required
                >
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="description">Deskripsi Kategori</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="3" 
                    class="form-control @error('description') is-invalid @enderror" 
                    placeholder="Deskripsi singkat kategori barang..."
                >{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Simpan Kategori
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Kategori -->
    <div class="card">
        <div class="card-header">
            <h3>Daftar Kategori Terdaftar</h3>
            <span class="badge badge-neutral">{{ $categories->count() }} Kategori</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Produk</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td><b>{{ $category->name }}</b></td>
                            <td style="color: var(--text-muted); font-size: 13px;">{{ $category->description ?: '-' }}</td>
                            <td>
                                <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="badge badge-info" style="text-decoration: none;">
                                    {{ $category->products_count }} produk
                                </a>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-buttons" style="justify-content: flex-end;">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-secondary">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori {{ $category->name }}?')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                Belum ada kategori yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
