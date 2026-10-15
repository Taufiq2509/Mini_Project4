@extends('layouts.app')

@section('title', 'Edit Kategori: ' . $category->name)

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Edit Kategori</h2>
        <p>Perbarui informasi kategori <b>{{ $category->name }}</b></p>
    </div>
    <div class="action-buttons">
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
            &larr; Kembali ke Kategori
        </a>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form method="POST" action="{{ route('categories.update', $category) }}">
        @csrf
        @method('PUT')

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="name">Nama Kategori <span style="color: var(--danger)">*</span></label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name', $category->name) }}" 
                class="form-control @error('name') is-invalid @enderror" 
                required
            >
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label for="description">Deskripsi</label>
            <textarea 
                id="description" 
                name="description" 
                rows="3" 
                class="form-control @error('description') is-invalid @enderror"
            >{{ old('description', $category->description) }}</textarea>
            @error('description')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
