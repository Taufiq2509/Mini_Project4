@extends('layouts.app')

@section('title', 'Login ke Sistem')

@section('content')
<div style="max-width: 480px; margin: 40px auto;">
    <div class="card" style="padding: 32px; box-shadow: var(--shadow-lg);">
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%); margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 26px; box-shadow: 0 8px 16px rgba(37, 99, 235, 0.25);">
                🔐
            </div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--secondary);">Login Pengguna</h2>
            <p style="color: var(--text-muted); font-size: 13px; margin-top: 6px;">
                Masuk untuk mengelola data inventaris dan transaksi persediaan
            </p>
        </div>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group" style="margin-bottom: 18px;">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ old('username') }}" 
                    class="form-control @error('username') is-invalid @enderror" 
                    placeholder="Masukkan username Anda..."
                    required 
                    autofocus
                >
                @error('username')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    placeholder="Masukkan password Anda..."
                    required
                >
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px;">
                Masuk ke Dashboard ➔
            </button>
        </form>

        <div style="margin-top: 28px; padding-top: 20px; border-top: 1px dashed var(--border); font-size: 13px; color: var(--text-muted);">
            <p style="font-weight: 700; color: var(--secondary); margin-bottom: 8px;">🔑 Kredensial Akun Praktikum (Demo):</p>
            <div style="background: var(--bg); padding: 12px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Administrator:</span>
                    <code><b>admin</b> / <b>admin</b></code>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Staff Gudang:</span>
                    <code><b>staff</b> / <b>staff</b></code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
