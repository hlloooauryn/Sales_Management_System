@extends('layouts.admin')

@section('title', 'Struk Kasir ' . $sale->invoice_number)
@section('header_title', '🧾 Nota Transaksi Kasir')

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="{{ route('sales.index') }}" class="btn btn-outline btn-sm">
            ← Kembali ke Riwayat Penjualan
        </a>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('sales.create') }}" class="btn btn-outline btn-sm">
                ➕ Transaksi Baru
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                🖨️ Cetak Struk Penjualan
            </button>
        </div>
    </div>

    <!-- Printable Receipt Card -->
    <div class="card" style="box-shadow: var(--shadow-md);" id="printableReceipt">
        <div class="card-body" style="padding: 40px;">
            
            <!-- Store Header -->
            <div style="text-align: center; border-bottom: 2px dashed var(--color-border); padding-bottom: 24px; margin-bottom: 24px;">
                @if(!empty($siteLogo))
                    <div style="margin-bottom: 10px;">
                        <img src="{{ $siteLogo }}" alt="{{ $siteName ?? 'Bloom Moment' }}" style="max-height: 48px; max-width: 180px; object-fit: contain; margin: 0 auto;">
                    </div>
                @else
                    <div class="pub-brand-icon" style="margin: 0 auto 10px; width: 48px; height: 48px; font-size: 24px;">🌸</div>
                    <h2 style="font-size: 24px; margin-bottom: 4px;">{{ $siteName ?? 'Bloom Moment' }}</h2>
                @endif
                <p style="font-size: 13px; color: var(--color-text-muted);">
                    {{ $siteTagline ?? 'Butik Bunga Segar & Hand Bouquet Artistik' }}<br>
                    {{ $siteAddress ?? 'Jl. Cendana No. 18, Kebayoran Baru, Jakarta Selatan' }} | WhatsApp: {{ $sitePhone ?? '+62 812-9988-7766' }}
                </p>
            </div>

            <!-- Transaction Details -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; font-size: 13.5px;">
                <div>
                    <div>No. Invoice: <strong style="font-family: monospace; font-size: 14.5px;">{{ $sale->invoice_number }}</strong></div>
                    <div style="color: var(--color-text-muted); margin-top: 2px;">Tanggal: {{ $sale->sale_date->format('d F Y') }}</div>
                    <div style="color: var(--color-text-muted); margin-top: 2px;">Waktu: {{ $sale->created_at->format('H:i') }} WIB</div>
                </div>

                <div style="text-align: right;">
                    <div>Pelanggan: <strong>{{ $sale->customer_name }}</strong></div>
                    <div style="color: var(--color-text-muted); margin-top: 2px;">Kasir: {{ $sale->user->name }}</div>
                    <div style="color: var(--color-text-muted); margin-top: 2px;">Pembayaran: <span class="badge badge-neutral">{{ $sale->payment_method }}</span></div>
                </div>
            </div>

            <!-- Items Purchased Table -->
            <div class="table-responsive" style="margin-bottom: 24px;">
                <table class="data-table">
                    <thead>
                        <tr style="border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
                            <th>Item Bunga</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: right;">Harga Satuan</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                    <div style="font-size: 11px; color: var(--color-text-muted);">{{ $item->product->category->name }}</div>
                                </td>
                                <td style="text-align: center;">{{ $item->quantity }}x</td>
                                <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td style="text-align: right; font-weight: 600;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="border-top: 2px solid var(--color-warm-brown-dark); font-weight: 700;">
                            <td colspan="3" style="text-align: right; font-size: 15px; padding: 14px 18px;">
                                TOTAL PEMBAYARAN:
                            </td>
                            <td style="text-align: right; font-size: 18px; color: var(--color-muted-olive-dark); padding: 14px 18px;">
                                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($sale->notes)
                <div style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 24px; padding: 10px; background-color: var(--color-warm-ivory); border-radius: var(--radius-sm);">
                    <strong>Catatan:</strong> {{ $sale->notes }}
                </div>
            @endif

            <!-- Footer Receipt -->
            <div style="text-align: center; border-top: 2px dashed var(--color-border); padding-top: 20px; font-size: 12.5px; color: var(--color-text-muted);">
                <p style="font-weight: 600; color: var(--color-warm-brown-dark); margin-bottom: 4px;">
                    Terima kasih telah berbelanja di {{ $siteName ?? 'Bloom Moment' }}! 🌸
                </p>
                <p>Bunga segar dirangkai dengan cinta. Harap simpan struk ini sebagai bukti pembayaran yang sah.</p>
            </div>

        </div>
    </div>
</div>
@endsection
