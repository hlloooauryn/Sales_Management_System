@extends('layouts.admin')

@section('title', 'Riwayat Penjualan Kasir')
@section('header_title', '🛒 Transaksi Penjualan / Barang Keluar')

@section('content')
<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 14px;">
        <div>
            <h3>Riwayat Penjualan Toko</h3>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                Catatan penjualan kasir kepada pelanggan yang otomatis mengurangi stok inventaris fisik.
            </p>
        </div>

        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
            ➕ Kasir Penjualan Baru (POS)
        </a>
    </div>

    <!-- Filter/Search -->
    <div style="padding: 18px 24px; background-color: var(--color-warm-ivory); border-bottom: 1px solid var(--color-border);">
        <form action="{{ route('sales.index') }}" method="GET" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label" for="search" style="font-size: 12px; margin-bottom: 4px;">Kata Kunci Pencarian</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Cari invoice, pelanggan, no. hp..." style="background: #FFFFFF;">
            </div>

            <div style="min-width: 140px;">
                <label class="form-label" for="order_type" style="font-size: 12px; margin-bottom: 4px;">Kanal Penjualan</label>
                <select name="order_type" id="order_type" class="form-control" style="background: #FFFFFF;">
                    <option value="">Semua Kanal</option>
                    <option value="offline" {{ request('order_type') === 'offline' ? 'selected' : '' }}>🛒 Kasir (POS)</option>
                    <option value="online" {{ request('order_type') === 'online' ? 'selected' : '' }}>🌐 Online Web</option>
                </select>
            </div>

            <div style="min-width: 140px;">
                <label class="form-label" for="status" style="font-size: 12px; margin-bottom: 4px;">Status Pesanan</label>
                <select name="status" id="status" class="form-control" style="background: #FFFFFF;">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Menunggu Bayar</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>✓ Sudah Bayar</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>🌿 Dirangkai</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>🚚 Dikirim</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>✕ Dibatalkan</option>
                </select>
            </div>

            <div style="min-width: 130px;">
                <label class="form-label" for="start_date" style="font-size: 12px; margin-bottom: 4px;">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-control" style="background: #FFFFFF;">
            </div>

            <div style="min-width: 130px;">
                <label class="form-label" for="end_date" style="font-size: 12px; margin-bottom: 4px;">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-control" style="background: #FFFFFF;">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary btn-sm" style="padding: 9px 18px;">
                    🔍 Filter
                </button>
                @if(request()->hasAny(['search', 'order_type', 'status', 'start_date', 'end_date']))
                    <a href="{{ route('sales.index') }}" class="btn btn-outline btn-sm" style="padding: 9px 14px;" title="Reset Filter">
                        ✕ Reset
                    </a>
                @endif
            </div>
        </form>

        @if(request()->hasAny(['search', 'order_type', 'status', 'start_date', 'end_date']))
            <div style="margin-top: 12px; font-size: 12.5px; color: var(--color-text-muted); display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span>Filter Aktif:</span>
                @if(request('search'))
                    <span class="badge badge-neutral">Kata Kunci: "{{ request('search') }}"</span>
                @endif
                @if(request('order_type'))
                    <span class="badge badge-neutral">Kanal: {{ request('order_type') === 'online' ? 'Online Web' : 'Kasir POS' }}</span>
                @endif
                @if(request('status'))
                    <span class="badge badge-neutral">Status: {{ ucfirst(request('status')) }}</span>
                @endif
            </div>
        @endif
    </div>

    <!-- Sales Table -->
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Kanal</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th>Kasir / Akun</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $s)
                        <tr>
                            <td>
                                <strong style="color: var(--color-warm-brown-dark); font-family: monospace; font-size: 14px;">
                                    {{ $s->invoice_number }}
                                </strong>
                            </td>
                            <td>
                                @if($s->order_type === 'online')
                                    <span class="badge" style="background: var(--color-soft-lavender-light); color: var(--color-soft-lavender-dark); border: 1px solid var(--color-soft-lavender); font-size: 11px;">
                                        🌐 Online
                                    </span>
                                @else
                                    <span class="badge badge-neutral" style="font-size: 11px;">
                                        🛒 Kasir
                                    </span>
                                @endif
                            </td>
                            <td>{{ $s->sale_date->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $s->customer_name }}</strong>
                                @if($s->customer_phone)
                                    <div style="font-size: 11.5px; color: var(--color-text-muted);">📱 {{ $s->customer_phone }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $s->payment_method }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $s->status_badge_class }}" style="font-size: 11px;">
                                    {{ $s->status_label }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $s->user->name }}</div>
                                <span class="badge {{ $s->user->isAdmin() ? 'role-admin' : ($s->user->isStaff() ? 'role-staff' : 'badge-neutral') }}" style="font-size: 10px;">
                                    {{ $s->user->role }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-neutral">
                                    {{ $s->items->sum('quantity') }} Tangkai
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--color-muted-olive-dark); font-size: 14.5px;">
                                    Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('sales.show', $s->id) }}" class="btn btn-outline btn-sm" style="padding: 4px 10px;">
                                    🧾 Detail
                                </a>

                                @if(Auth::user()->isAdmin())
                                    <form action="{{ route('sales.cancel', $s->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Peringatan Admin: Membatalkan transaksi ini akan mengembalikan seluruh stok bunga ke inventaris. Lanjutkan pembatalan?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 10px;" title="Batalkan transaksi dan kembalikan stok">
                                            ✕ Batal
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--color-text-muted);">
                                Belum ada riwayat transaksi penjualan bunga.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="padding: 16px 24px; border-top: 1px solid var(--color-border);">
        {{ $sales->links() }}
    </div>
</div>
@endsection
