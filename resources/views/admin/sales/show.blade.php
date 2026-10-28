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
                ➕ Kasir Baru
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                🖨️ Cetak Struk Penjualan
            </button>
        </div>
    </div>

    @if($sale->isOnline())
        <!-- Box Khusus Pesanan Online Toko -->
        <div class="card" style="box-shadow: var(--shadow-sm); margin-bottom: 24px; border-left: 4px solid var(--color-soft-lavender-dark);">
            <div class="card-header" style="background: var(--color-warm-ivory); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 20px;">🌐</span>
                    <div>
                        <h3 style="font-size: 16px; margin: 0;">Kelola Pesanan Online Pelanggan</h3>
                        <small style="color: var(--color-text-muted);">Dipesan melalui website oleh akun: {{ $sale->user->name }} ({{ $sale->user->email }})</small>
                    </div>
                </div>

                <span class="badge {{ $sale->status_badge_class }}" style="font-size: 13px; padding: 6px 14px;">
                    ● {{ $sale->status_label }}
                </span>
            </div>

            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 24px; margin-bottom: 20px;">
                    <!-- Data Pengiriman -->
                    <div>
                        <h4 style="font-size: 14px; margin-bottom: 10px; color: var(--color-dark-brown);">📍 Informasi Pengiriman & Penerima:</h4>
                        <div style="font-size: 13.5px; line-height: 1.7;">
                            <div>Penerima Bunga: <strong>{{ $sale->customer_name }}</strong></div>
                            <div>No. WhatsApp: <strong>{{ $sale->customer_phone ?? '-' }}</strong>
                                @if($sale->customer_phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sale->customer_phone) }}" target="_blank" style="margin-left: 6px; font-size: 12px; color: var(--color-sage-green-dark); font-weight: 700;">
                                        [Chat WA ↗]
                                    </a>
                                @endif
                            </div>
                            <div>Rencana Tanggal Kirim: <strong>{{ $sale->delivery_date ? $sale->delivery_date->format('d F Y') : '-' }}</strong></div>
                            <div style="margin-top: 6px; background: var(--color-surface-soft); padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--color-border);">
                                <strong>Alamat Kirim:</strong><br>
                                {{ $sale->shipping_address ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <!-- Bukti Pembayaran -->
                    <div>
                        <h4 style="font-size: 14px; margin-bottom: 10px; color: var(--color-dark-brown);">📸 Bukti Transfer Pelanggan:</h4>
                        @if($sale->payment_proof)
                            <div style="display: flex; gap: 14px; align-items: center;">
                                <div style="width: 80px; height: 80px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--color-border); background: #FFF; flex-shrink: 0;">
                                    <a href="{{ $sale->payment_proof_url }}" target="_blank">
                                        <img src="{{ $sale->payment_proof_url }}" alt="Bukti Transfer" style="width: 100%; height: 100%; object-fit: cover;">
                                    </a>
                                </div>
                                <div>
                                    <span class="badge badge-success" style="font-size: 11px; margin-bottom: 4px;">Bukti Diunggah</span>
                                    <div>
                                        <a href="{{ $sale->payment_proof_url }}" target="_blank" class="btn btn-outline btn-sm" style="padding: 3px 8px; font-size: 11px;">
                                            🔍 Perbesar Foto
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div style="background: var(--color-warning-bg); border: 1px solid #F5D3A9; padding: 12px; border-radius: var(--radius-sm); font-size: 13px; color: var(--color-warning);">
                                ⏳ Belum ada bukti transfer yang diunggah pelanggan (atau metode COD).
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Update Status Form -->
                <div style="border-top: 1px dashed var(--color-border); padding-top: 16px;">
                    <form action="{{ route('sales.status', $sale->id) }}" method="POST" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                        @csrf
                        <div style="flex: 1; min-width: 220px;">
                            <label class="form-label" for="update_status" style="font-size: 12.5px; margin-bottom: 4px;">Perbarui Status Progres Pesanan Ini:</label>
                            <select name="status" id="update_status" class="form-control" style="background: #FFFFFF;">
                                <option value="pending" {{ $sale->status === 'pending' ? 'selected' : '' }}>⏳ Menunggu Pembayaran</option>
                                <option value="paid" {{ $sale->status === 'paid' ? 'selected' : '' }}>✓ Sudah Bayar (Menunggu Konfirmasi)</option>
                                <option value="processing" {{ $sale->status === 'processing' ? 'selected' : '' }}>🌿 Bunga Sedang Dirangkai Florist</option>
                                <option value="shipped" {{ $sale->status === 'shipped' ? 'selected' : '' }}>🚚 Bunga Sedang Dikirim Kurir</option>
                                <option value="completed" {{ $sale->status === 'completed' ? 'selected' : '' }}>✅ Pesanan Selesai Diterima</option>
                                <option value="cancelled" {{ $sale->status === 'cancelled' ? 'selected' : '' }}>✕ Dibatalkan</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm" style="padding: 9px 18px;">
                            💾 Simpan Perubahan Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

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
