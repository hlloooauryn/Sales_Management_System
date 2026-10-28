<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan {{ $sale->invoice_number }} — {{ $siteName ?? 'Bloom Moment' }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            background-color: #F8F5F0;
            padding: 30px 15px;
        }
        @media print {
            body {
                background: #FFFFFF;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt-box {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>
<body>
    <div style="max-width: 680px; margin: 0 auto;">
        
        <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <a href="{{ route('orders.show', $sale->invoice_number) }}" class="btn btn-outline btn-sm">
                ← Kembali ke Pesanan
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                🖨️ Cetak Struk Ini
            </button>
        </div>

        <div class="card receipt-box" style="padding: 36px; background: #FFFFFF; box-shadow: var(--shadow-md); border-radius: var(--radius-md);">
            <!-- Store Header -->
            <div style="text-align: center; border-bottom: 2px dashed var(--color-border); padding-bottom: 20px; margin-bottom: 24px;">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="{{ $siteName ?? 'Bloom Moment' }}" style="max-height: 44px; max-width: 170px; object-fit: contain; margin: 0 auto 8px;">
                @else
                    <div style="font-size: 28px; margin-bottom: 4px;">🌸</div>
                    <h2 style="font-size: 22px; margin: 0 0 4px;">{{ $siteName ?? 'Bloom Moment' }}</h2>
                @endif
                <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
                    {{ $siteTagline ?? 'Artisan Florist & Botanicals' }}<br>
                    {{ $siteAddress ?? 'Jl. Cendana No. 18, Kebayoran Baru, Jakarta Selatan' }}<br>
                    WhatsApp: {{ $sitePhone ?? '0812-9988-7766' }}
                </p>
            </div>

            <!-- Transaction Details -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; font-size: 13px;">
                <div>
                    <div>No. Invoice: <strong style="font-family: monospace; font-size: 14px;">{{ $sale->invoice_number }}</strong></div>
                    <div style="color: var(--color-text-muted); margin-top: 2px;">Tanggal Pesan: {{ $sale->created_at->format('d/m/Y H:i') }} WIB</div>
                    <div style="color: var(--color-text-muted); margin-top: 2px;">Kanal: <strong>Pesanan Web Online</strong></div>
                </div>
                <div style="text-align: right;">
                    <div>Penerima: <strong>{{ $sale->customer_name }}</strong></div>
                    <div style="color: var(--color-text-muted); margin-top: 2px;">Telepon: {{ $sale->customer_phone ?? '-' }}</div>
                    <div style="color: var(--color-text-muted); margin-top: 2px;">Metode: <strong>{{ $sale->payment_method }}</strong></div>
                </div>
            </div>

            @if($sale->shipping_address)
                <div style="font-size: 12.5px; background: var(--color-warm-ivory); padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--color-border); margin-bottom: 20px;">
                    <strong>Alamat Kirim:</strong> {{ $sale->shipping_address }}
                </div>
            @endif

            <!-- Items Table -->
            <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; margin-bottom: 24px;">
                <thead>
                    <tr style="border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border); background: var(--color-surface-soft);">
                        <th style="text-align: left; padding: 10px 8px;">Rangkaian Bunga</th>
                        <th style="text-align: center; padding: 10px 8px;">Qty</th>
                        <th style="text-align: right; padding: 10px 8px;">Harga</th>
                        <th style="text-align: right; padding: 10px 8px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                        <tr style="border-bottom: 1px dashed var(--color-border);">
                            <td style="padding: 10px 8px;">
                                <strong>{{ $item->product->name }}</strong>
                            </td>
                            <td style="text-align: center; padding: 10px 8px;">{{ $item->quantity }}x</td>
                            <td style="text-align: right; padding: 10px 8px;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td style="text-align: right; padding: 10px 8px; font-weight: 600;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top: 2px solid var(--color-dark-brown);">
                        <td colspan="3" style="text-align: right; padding: 12px 8px; font-weight: 700; font-size: 14px;">
                            TOTAL PEMBAYARAN:
                        </td>
                        <td style="text-align: right; padding: 12px 8px; font-weight: 800; font-size: 17px; color: var(--color-muted-olive-dark);">
                            Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            @if($sale->notes)
                <div style="font-size: 12.5px; background: var(--color-dusty-pink-light); border: 1px solid var(--color-dusty-pink); padding: 10px 14px; border-radius: var(--radius-sm); margin-bottom: 20px;">
                    <strong>Pesan Kartu Ucapan:</strong> " {{ $sale->notes }} "
                </div>
            @endif

            <!-- Footer Message -->
            <div style="text-align: center; border-top: 2px dashed var(--color-border); padding-top: 18px; font-size: 12px; color: var(--color-text-muted);">
                <p style="font-weight: 700; color: var(--color-dark-brown); margin-bottom: 4px;">
                    Terima kasih telah mempercayakan momen spesial Anda kepada {{ $siteName ?? 'Bloom Moment' }}! 🌸
                </p>
                <p>Bunga segar dirangkai dengan kehangatan & cinta. Hubungi kami jika ada pertanyaan atau permintaan khusus.</p>
            </div>
        </div>
    </div>
</body>
</html>
