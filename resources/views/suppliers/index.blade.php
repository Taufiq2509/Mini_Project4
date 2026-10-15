@extends('layouts.app')

@section('title', 'Data Supplier Mitra')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Data Supplier Mitra</h2>
        <p>Manajemen vendor dan mitra pengadaan barang inventaris</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
    <!-- Form Tambah Supplier -->
    <div class="card" style="align-self: flex-start;">
        <div class="card-header">
            <h3>+ Tambah Supplier Baru</h3>
        </div>

        <form method="POST" action="{{ route('suppliers.store') }}">
            @csrf

            <div class="form-group" style="margin-bottom: 14px;">
                <label for="name">Nama Perusahaan / Supplier <span style="color: var(--danger)">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    class="form-control @error('name') is-invalid @enderror" 
                    placeholder="Contoh: PT Prima Logistik"
                    required
                >
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label for="phone">Nomor Telepon / WhatsApp</label>
                <input 
                    type="text" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone') }}" 
                    class="form-control @error('phone') is-invalid @enderror" 
                    placeholder="Contoh: 08123456789"
                >
                @error('phone')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label for="email">Alamat Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    class="form-control @error('email') is-invalid @enderror" 
                    placeholder="sales@perusahaan.com"
                >
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="address">Alamat Kantor / Gudang</label>
                <textarea 
                    id="address" 
                    name="address" 
                    rows="2" 
                    class="form-control @error('address') is-invalid @enderror" 
                    placeholder="Alamat lengkap..."
                >{{ old('address') }}</textarea>
                @error('address')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Simpan Supplier
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Supplier -->
    <div class="card">
        <div class="card-header">
            <h3>Daftar Supplier Mitra</h3>
            <span class="badge badge-neutral">{{ $suppliers->count() }} Supplier</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Produk</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <b>{{ $supplier->name }}</b>
                            </td>
                            <td style="font-size: 13px;">
                                <div>📞 {{ $supplier->phone ?: '-' }}</div>
                                <div style="color: var(--text-muted);">✉️ {{ $supplier->email ?: '-' }}</div>
                            </td>
                            <td style="color: var(--text-muted); font-size: 13px;">
                                {{ $supplier->address ?: '-' }}
                            </td>
                            <td>
                                <span class="badge badge-neutral">
                                    {{ $supplier->products_count }} item
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-buttons" style="justify-content: flex-end;">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-secondary">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Hapus supplier {{ $supplier->name }}?')" style="display:inline;">
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
                            <td colspan="5" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                Belum ada supplier yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
