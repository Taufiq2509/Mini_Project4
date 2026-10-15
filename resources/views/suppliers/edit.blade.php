@extends('layouts.app')

@section('title', 'Edit Supplier: ' . $supplier->name)

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Edit Data Supplier</h2>
        <p>Perbarui informasi mitra pengadaan <b>{{ $supplier->name }}</b></p>
    </div>
    <div class="action-buttons">
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
            &larr; Kembali ke Supplier
        </a>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
        @csrf
        @method('PUT')

        <div class="form-group" style="margin-bottom: 14px;">
            <label for="name">Nama Supplier <span style="color: var(--danger)">*</span></label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name', $supplier->name) }}" 
                class="form-control @error('name') is-invalid @enderror" 
                required
            >
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 14px;">
            <label for="phone">Nomor Telepon</label>
            <input 
                type="text" 
                id="phone" 
                name="phone" 
                value="{{ old('phone', $supplier->phone) }}" 
                class="form-control @error('phone') is-invalid @enderror"
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
                value="{{ old('email', $supplier->email) }}" 
                class="form-control @error('email') is-invalid @enderror"
            >
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label for="address">Alamat</label>
            <textarea 
                id="address" 
                name="address" 
                rows="2" 
                class="form-control @error('address') is-invalid @enderror"
            >{{ old('address', $supplier->address) }}</textarea>
            @error('address')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
