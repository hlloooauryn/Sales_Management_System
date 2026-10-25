@extends('layouts.admin')

@section('title', 'Kelola Pengguna Sistem')
@section('header_title', '👥 Manajemen Pengguna & Hak Akses')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>Daftar Pengguna Internal</h3>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                Kelola akun petugas (Staff) dan administrator toko bunga (Admin/Owner).
            </p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            ➕ Tambah Pengguna Baru
        </a>
    </div>

    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pengguna</th>
                        <th>Alamat Email</th>
                        <th>Peran (Role)</th>
                        <th>Aktivitas Transaksi</th>
                        <th>Terdaftar Sejak</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $u)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $u->name }}</strong>
                                @if($u->id === Auth::id())
                                    <span class="badge badge-success" style="font-size: 10px; margin-left: 6px;">Akun Anda</span>
                                @endif
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>
                                <span class="badge {{ $u->isAdmin() ? 'role-admin' : 'role-staff' }}">
                                    {{ strtoupper($u->role) }}
                                </span>
                            </td>
                            <td style="font-size: 12.5px; color: var(--color-text-muted);">
                                {{ $u->purchases()->count() }} Pembelian • {{ $u->sales()->count() }} Penjualan
                            </td>
                            <td>{{ $u->created_at->format('d M Y') }}</td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('users.edit', $u->id) }}" class="btn btn-outline btn-sm" style="padding: 4px 10px;">
                                    ✏️ Edit
                                </a>

                                @if($u->id !== Auth::id())
                                    @php
                                        $txCount = $u->purchases()->count() + $u->sales()->count();
                                    @endphp
                                    <form action="{{ route('users.destroy', $u->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 10px;" {{ $txCount > 0 ? 'title="Akun memiliki riwayat transaksi" disabled style=opacity:0.4;cursor:not-allowed;' : '' }}>
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
