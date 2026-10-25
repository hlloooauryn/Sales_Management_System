@extends('layouts.admin')

@section('title', 'Data Pemasok')
@section('header_title', '🏢 Pemasok / Supplier Bunga')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>Daftar Pemasok Resmi</h3>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                Mitra perkebunan dan distributor bunga potong, buket, dan material florist.
            </p>
        </div>
        
        @if(Auth::user()->isAdmin())
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
                ➕ Tambah Pemasok Baru
            </a>
        @else
            <span class="badge badge-neutral">Mode Lihat (Staff)</span>
        @endif
    </div>

    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pemasok / Perusahaan</th>
                        <th>Kontak PIC</th>
                        <th>Telepon & Email</th>
                        <th>Alamat Kebun / Kantor</th>
                        <th>Total Pembelian</th>
                        @if(Auth::user()->isAdmin())
                            <th style="text-align: right;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $index => $sup)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $sup->name }}</strong>
                            </td>
                            <td>{{ $sup->contact_person ?: '—' }}</td>
                            <td>
                                @if($sup->phone)
                                    <div>📞 {{ $sup->phone }}</div>
                                @endif
                                @if($sup->email)
                                    <div style="font-size: 12px; color: var(--color-text-muted);">✉️ {{ $sup->email }}</div>
                                @endif
                                @if(!$sup->phone && !$sup->email)
                                    —
                                @endif
                            </td>
                            <td>{{ $sup->address ?: '—' }}</td>
                            <td>
                                <span class="badge badge-neutral">
                                    {{ $sup->purchases_count }} Transaksi
                                </span>
                            </td>
                            @if(Auth::user()->isAdmin())
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="{{ route('suppliers.edit', $sup->id) }}" class="btn btn-outline btn-sm" style="padding: 4px 10px;">
                                        ✏️ Edit
                                    </a>
                                    
                                    <form action="{{ route('suppliers.destroy', $sup->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemasok ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 10px;" {{ $sup->purchases_count > 0 ? 'title="Tidak dapat dihapus karena tercatat dalam transaksi pembelian" disabled style=opacity:0.4;cursor:not-allowed;' : '' }}>
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->isAdmin() ? 7 : 6 }}" style="text-align: center; padding: 40px; color: var(--color-text-muted);">
                                Belum ada data pemasok bunga.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
