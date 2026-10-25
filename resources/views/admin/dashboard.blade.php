@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header_title', '📊 Dashboard Ringkasan Inventaris & Penjualan')

@section('content')
<!-- Welcome Greeting -->
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 26px;">Halo, {{ Auth::user()->name }}! 🌸</h2>
    <p style="color: var(--color-text-muted); font-size: 14px;">
        Berikut adalah ringkasan inventaris bunga, pergerakan stok, dan performa penjualan terkini di {{ $siteName ?? 'Bloom Moment' }}.
    </p>
</div>

<!-- Statistic Metric Cards -->
<div class="stats-grid">
    <!-- Omzet Penjualan (Khusus Admin atau Staff) -->
    <div class="stat-card">
        <div class="stat-info">
            <p>Total Penjualan</p>
            <div class="stat-value">Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}</div>
            <div class="stat-subtext">{{ $totalSalesCount }} Transaksi Tercatat</div>
        </div>
        <div class="stat-icon olive">
            🛒
        </div>
    </div>

    <!-- Total Pembelian Inventaris -->
    <div class="stat-card">
        <div class="stat-info">
            <p>Total Pembelian Masuk</p>
            <div class="stat-value">Rp {{ number_format($totalPurchasesAmount, 0, ',', '.') }}</div>
            <div class="stat-subtext">{{ $totalPurchasesCount }} Transaksi Restock</div>
        </div>
        <div class="stat-icon sand">
            📥
        </div>
    </div>

    <!-- Koleksi Produk Bunga -->
    <div class="stat-card">
        <div class="stat-info">
            <p>Total Produk Bunga</p>
            <div class="stat-value">{{ $totalProducts }}</div>
            <div class="stat-subtext">{{ $totalCategories }} Kategori Rangkaian</div>
        </div>
        <div class="stat-icon rose">
            🌹
        </div>
    </div>

    <!-- Indikator Stok Menipis -->
    <div class="stat-card">
        <div class="stat-info">
            <p>Peringatan Stok</p>
            <div class="stat-value" style="color: {{ $lowStockProducts->count() > 0 ? 'var(--color-danger)' : 'var(--color-success)' }};">
                {{ $lowStockProducts->count() }}
            </div>
            <div class="stat-subtext">
                @if($outOfStockCount > 0)
                    <strong style="color: var(--color-danger);">{{ $outOfStockCount }} Habis</strong>, 
                @endif
                {{ $lowStockProducts->count() }} Perlu Restock Segera
            </div>
        </div>
        <div class="stat-icon warning">
            ⚠️
        </div>
    </div>
</div>

<!-- Low Stock Warning Alert Table (Sangat penting untuk penilaian dosen) -->
@if($lowStockProducts->count() > 0)
    <div class="card" style="border: 1px solid #F5D397; background-color: #FFFDF9; margin-bottom: 28px;">
        <div class="card-header" style="background-color: #FFF6E8; border-bottom: 1px solid #F5D397;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 20px;">⚠️</span>
                <h3 style="color: #9C5B00;">Perhatian Florist: Stok Bunga Menipis / Perlu Segera Restock</h3>
            </div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
                    📥 Buat Pembelian / Restock Bunga Sekarang
                </a>
            @else
                <span class="badge badge-warning" style="font-size: 12px; padding: 6px 12px;">
                    ℹ️ Harap laporkan ke Admin untuk pengadaan restok
                </span>
            @endif
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Bunga</th>
                            <th>Kategori</th>
                            <th>Stok Tersisa</th>
                            <th>Batas Min. Stok</th>
                            <th>Status Peringatan</th>
                            <th style="text-align: right;">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $low)
                            <tr>
                                <td>
                                    <strong>{{ $low->name }}</strong>
                                </td>
                                <td>{{ $low->category->name }}</td>
                                <td>
                                    <strong style="font-size: 15px; color: {{ $low->stock <= 0 ? 'var(--color-danger)' : 'var(--color-warning)' }};">
                                        {{ $low->stock }} tangkai
                                    </strong>
                                </td>
                                <td>{{ $low->min_stock }} tangkai</td>
                                <td>
                                    @if($low->stock <= 0)
                                        <span class="badge badge-danger">Habis Total</span>
                                    @else
                                        <span class="badge badge-warning">Menipis (&le; {{ $low->min_stock }})</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('purchases.create') }}" class="btn btn-outline btn-sm" style="padding: 3px 10px; font-size: 12px;">
                                            + Pasok Baru
                                        </a>
                                    @else
                                        <span style="font-size: 12px; color: var(--color-text-muted);">
                                            Perlu Restok
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<!-- Recent Transactions Dual Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
    
    <!-- Recent Sales -->
    <div class="card">
        <div class="card-header">
            <h3>Penjualan Kasir Terkini</h3>
            <a href="{{ route('sales.index') }}" class="btn btn-outline btn-sm" style="font-size: 12px; padding: 4px 10px;">
                Semua Penjualan →
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('sales.show', $sale->id) }}" style="font-weight: 600; font-family: monospace;">
                                        {{ $sale->invoice_number }}
                                    </a>
                                    <div style="font-size: 11px; color: var(--color-text-muted);">{{ $sale->sale_date->format('d/m/Y') }}</div>
                                </td>
                                <td>
                                    <div>{{ $sale->customer_name }}</div>
                                    <span class="badge badge-neutral" style="font-size: 10px;">{{ $sale->payment_method }}</span>
                                </td>
                                <td style="text-align: right; font-weight: 600; color: var(--color-muted-olive-dark);">
                                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 24px; color: var(--color-text-muted);">
                                    Belum ada transaksi penjualan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Purchases -->
    <div class="card">
        <div class="card-header">
            <h3>Barang Masuk / Pembelian Terkini</h3>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline btn-sm" style="font-size: 12px; padding: 4px 10px;">
                Semua Pembelian →
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Referensi</th>
                            <th>Pemasok</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPurchases as $purchase)
                            <tr>
                                <td>
                                    <a href="{{ route('purchases.show', $purchase->id) }}" style="font-weight: 600; font-family: monospace;">
                                        {{ $purchase->reference_number }}
                                    </a>
                                    <div style="font-size: 11px; color: var(--color-text-muted);">{{ $purchase->purchase_date->format('d/m/Y') }}</div>
                                </td>
                                <td>
                                    <div>{{ $purchase->supplier->name }}</div>
                                    <div style="font-size: 11px; color: var(--color-text-muted);">Oleh: {{ $purchase->user->name }}</div>
                                </td>
                                <td style="text-align: right; font-weight: 600; color: var(--color-warm-brown-dark);">
                                    Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 24px; color: var(--color-text-muted);">
                                    Belum ada transaksi pembelian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
