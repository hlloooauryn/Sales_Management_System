@extends('layouts.admin')

@section('title', 'Edit Pengguna — ' . $user->name)
@section('header_title', '✏️ Edit Pengguna')

@section('content')
<div style="max-width: 620px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h3>Edit Akun: {{ $user->name }}</h3>
            <a href="{{ route('users.index') }}" class="btn btn-outline btn-sm">
                ← Kembali ke Daftar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email <span class="req">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Hak Akses / Peran (Role) <span class="req">*</span></label>
                    <select name="role" id="role" class="form-control" required {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                        <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User (Pelanggan / Pembeli Online)</option>
                        <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin / Owner</option>
                    </select>
                    @if($user->id === Auth::id())
                        <input type="hidden" name="role" value="admin">
                        <small style="color: var(--color-text-muted); font-size: 12px;">Anda tidak dapat mengubah peran akun Anda sendiri saat sedang aktif.</small>
                    @endif
                </div>

                <div class="form-group" style="padding-top: 14px; border-top: 1px dashed var(--color-border);">
                    <label class="form-label" for="password">Ubah Kata Sandi</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti kata sandi">
                    <small style="color: var(--color-text-muted); font-size: 12px;">Isi minimal 6 karakter hanya jika ingin mengganti password pengguna.</small>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 28px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        💾 Simpan Perubahan Akun
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
