@extends('layouts.admin')

@section('title', 'Laporan Penjualan & Pembelian')
@section('header_title', '📈 Laporan Transaksi & Inventaris Bunga')

@section('content')
<!-- Filter Period Card -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div>
            <h3>Filter Periode Laporan</h3>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                Tentukan rentang tanggal untuk melihat rekapitulasi penjualan kasir dan pembelian barang masuk.
            </p>
        </div>

        <button onclick="window.print()" class="btn btn-outline btn-sm">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.index') }}" method="GET" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 220px;">
                <label class="form-label" for="search">Cari Kata Kunci (Invoice / Pelanggan / Kasir / Bunga)</label>
                <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Contoh: INV, Amanda, Mawar..." style="background: #FFFFFF;">
            </div>

            <div style="min-width: 150px;">
                <label class="form-label" for="start_date">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}" required style="background: #FFFFFF;">
            </div>

            <div style="min-width: 150px;">
                <label class="form-label" for="end_date">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}" required style="background: #FFFFFF;">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                    📊 Tampilkan Laporan
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('reports.index', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline" style="padding: 10px 14px;" title="Reset Pencarian">
                        ✕ Reset Cari
                    </a>
                @endif
            </div>
        </form>

        @if(request('search'))
            <div style="margin-top: 14px; font-size: 12.5px; color: var(--color-text-muted); display: flex; align-items: center; gap: 8px;">
                <span>Filter Pencarian Aktif:</span>
                <span class="badge badge-neutral">Kata Kunci: "{{ request('search') }}"</span>
            </div>
        @endif
    </div>
</div>

<!-- Summary Period Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <p>Total Penjualan</p>
            <div class="stat-value" style="color: var(--color-muted-olive-dark);">
                Rp {{ number_format($totalSales, 0, ',', '.') }}
            </div>
            <div class="stat-subtext">{{ $totalSalesCount }} Invoice • {{ $totalItemsSold }} Tangkai Keluar</div>
        </div>
        <div class="stat-icon olive">🛒</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <p>Total Pembelian / Pasokan</p>
            <div class="stat-value" style="color: var(--color-warm-brown-dark);">
                Rp {{ number_format($totalPurchases, 0, ',', '.') }}
            </div>
            <div class="stat-subtext">{{ $totalPurchasesCount }} Pengiriman • {{ $totalItemsBought }} Tangkai Masuk</div>
        </div>
        <div class="stat-icon sand">📥</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <p>Selisih Kas Operasional</p>
            @php $netCash = $totalSales - $totalPurchases; @endphp
            <div class="stat-value" style="color: {{ $netCash >= 0 ? 'var(--color-success)' : 'var(--color-danger)' }};">
                Rp {{ number_format($netCash, 0, ',', '.') }}
            </div>
            <div class="stat-subtext">Arus Kas Masuk Bersih Periode Ini</div>
        </div>
        <div class="stat-icon {{ $netCash >= 0 ? 'olive' : 'rose' }}">💵</div>
    </div>
</div>

<!-- Detailed Sales in Range -->
<div class="card" style="margin-bottom: 28px;">
    <div class="card-header">
        <h3>Rekap Penjualan ({{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})</h3>
        <span class="badge badge-neutral">{{ $totalSalesCount }} Transaksi</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Metode</th>
                        <th>Kasir</th>
                        <th>Rincian Item</th>
                        <th style="text-align: right;">Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('sales.show', $sale->id) }}" style="font-weight: 600; font-family: monospace;">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                            <td>{{ $sale->customer_name }}</td>
                            <td><span class="badge badge-neutral">{{ $sale->payment_method }}</span></td>
                            <td>{{ $sale->user->name }}</td>
                            <td>
                                <ul style="list-style: none; font-size: 12px; color: var(--color-text-muted);">
                                    @foreach($sale->items as $it)
                                        <li>• {{ $it->product->name }} ({{ $it->quantity }}x)</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: var(--color-muted-olive-dark);">
                                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: var(--color-text-muted);">
                                Tidak ada transaksi penjualan pada rentang tanggal dan pencarian ini.
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

<!-- Detailed Purchases in Range -->
<div class="card">
    <div class="card-header">
        <h3>Rekap Pembelian / Barang Masuk ({{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})</h3>
        <span class="badge badge-neutral">{{ $totalPurchasesCount }} Transaksi</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Referensi</th>
                        <th>Tanggal</th>
                        <th>Pemasok</th>
                        <th>Petugas</th>
                        <th>Rincian Pasokan</th>
                        <th style="text-align: right;">Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $pur)
                        <tr>
                            <td>
                                <a href="{{ route('purchases.show', $pur->id) }}" style="font-weight: 600; font-family: monospace;">
                                    {{ $pur->reference_number }}
                                </a>
                            </td>
                            <td>{{ $pur->purchase_date->format('d/m/Y') }}</td>
                            <td>{{ $pur->supplier->name }}</td>
                            <td>{{ $pur->user->name }}</td>
                            <td>
                                <ul style="list-style: none; font-size: 12px; color: var(--color-text-muted);">
                                    @foreach($pur->items as $it)
                                        <li>• {{ $it->product->name }} (+{{ $it->quantity }} tangkai)</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: var(--color-warm-brown-dark);">
                                Rp {{ number_format($pur->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--color-text-muted);">
                                Tidak ada transaksi pembelian pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
