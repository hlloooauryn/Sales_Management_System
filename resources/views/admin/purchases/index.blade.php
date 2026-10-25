@extends('layouts.admin')

@section('title', 'Riwayat Pembelian')
@section('header_title', '📥 Transaksi Pembelian / Barang Masuk')

@section('content')
<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 14px;">
        <div>
            <h3>Riwayat Barang Masuk dari Pemasok</h3>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                Catatan pembelian bunga segar dari perkebunan yang otomatis menambah stok fisik toko.
            </p>
        </div>

        <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
            ➕ Catat Pembelian / Restock Bunga
        </a>
    </div>

    <!-- Filter/Search -->
    <div style="padding: 18px 24px; background-color: var(--color-warm-ivory); border-bottom: 1px solid var(--color-border);">
        <form action="{{ route('purchases.index') }}" method="GET" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 220px;">
                <label class="form-label" for="search" style="font-size: 12px; margin-bottom: 4px;">Kata Kunci Pencarian</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nomor referensi atau nama pemasok..." style="background: #FFFFFF;">
            </div>

            <div style="min-width: 150px;">
                <label class="form-label" for="start_date" style="font-size: 12px; margin-bottom: 4px;">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-control" style="background: #FFFFFF;">
            </div>

            <div style="min-width: 150px;">
                <label class="form-label" for="end_date" style="font-size: 12px; margin-bottom: 4px;">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-control" style="background: #FFFFFF;">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary btn-sm" style="padding: 9px 18px;">
                    🔍 Filter
                </button>
                @if(request()->hasAny(['search', 'start_date', 'end_date']))
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline btn-sm" style="padding: 9px 14px;" title="Reset Filter">
                        ✕ Reset
                    </a>
                @endif
            </div>
        </form>

        @if(request()->hasAny(['search', 'start_date', 'end_date']))
            <div style="margin-top: 12px; font-size: 12.5px; color: var(--color-text-muted); display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span>Filter Aktif:</span>
                @if(request('search'))
                    <span class="badge badge-neutral">Kata Kunci: "{{ request('search') }}"</span>
                @endif
                @if(request('start_date'))
                    <span class="badge badge-neutral">Dari: {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}</span>
                @endif
                @if(request('end_date'))
                    <span class="badge badge-neutral">Sampai: {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</span>
                @endif
            </div>
        @endif
    </div>

    <!-- Purchases Table -->
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Referensi</th>
                        <th>Tanggal Pembelian</th>
                        <th>Pemasok / Kebun</th>
                        <th>Petugas Penerima</th>
                        <th>Jumlah Item</th>
                        <th>Total Pembelian</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $p)
                        <tr>
                            <td>
                                <strong style="color: var(--color-warm-brown-dark); font-family: monospace; font-size: 14px;">
                                    {{ $p->reference_number }}
                                </strong>
                            </td>
                            <td>{{ $p->purchase_date->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $p->supplier->name }}</strong>
                            </td>
                            <td>
                                <div>{{ $p->user->name }}</div>
                                <span class="badge {{ $p->user->isAdmin() ? 'role-admin' : 'role-staff' }}" style="font-size: 10px;">
                                    {{ $p->user->role }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-neutral">
                                    {{ $p->items->sum('quantity') }} Tangkai / {{ $p->items->count() }} Jenis
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--color-muted-olive-dark);">
                                    Rp {{ number_format($p->total_amount, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('purchases.show', $p->id) }}" class="btn btn-outline btn-sm">
                                    👁️ Lihat Rincian
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--color-text-muted);">
                                Belum ada riwayat transaksi pembelian bunga.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="padding: 16px 24px; border-top: 1px solid var(--color-border);">
        {{ $purchases->links() }}
    </div>
</div>
@endsection
