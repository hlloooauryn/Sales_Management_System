@extends('layouts.admin')

@section('title', 'Tambah Pengguna Baru')
@section('header_title', '👥 Tambah Akun Pengguna')

@section('content')
<div style="max-width: 620px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h3>Form Akun Petugas Baru</h3>
            <a href="{{ route('users.index') }}" class="btn btn-outline btn-sm">
                ← Kembali ke Daftar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Rina Anggraini" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email <span class="req">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="rina@bloommoment.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Hak Akses / Peran (Role) <span class="req">*</span></label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">Pilih Peran Akun</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff (Kasir, Transaksi, Edit Operasional Produk)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin / Owner (Akses Penuh Seluruh Sistem)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi Awal <span class="req">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 28px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        💾 Daftarkan Pengguna
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
