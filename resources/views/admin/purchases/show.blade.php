@extends('layouts.admin')

@section('title', 'Faktur Pembelian ' . $purchase->reference_number)
@section('header_title', '📄 Faktur Pembelian / Barang Masuk')

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="{{ route('purchases.index') }}" class="btn btn-outline btn-sm">
            ← Kembali ke Riwayat Pembelian
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            🖨️ Cetak Faktur / Bukti Masuk
        </button>
    </div>

    <div class="card" style="box-shadow: var(--shadow-md);">
        <div class="card-body" style="padding: 40px;">
            
            <!-- Invoice Header -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 24px; border-bottom: 2px solid var(--color-border); margin-bottom: 30px;">
                <div style="display: flex; gap: 14px; align-items: center;">
                    @if(!empty($siteLogo))
                        <div>
                            <img src="{{ $siteLogo }}" alt="{{ $siteName ?? 'Bloom Moment' }}" style="max-height: 48px; max-width: 170px; object-fit: contain;">
                            <div style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">Bukti Penerimaan Barang Masuk (Inventaris)</div>
                        </div>
                    @else
                        <div class="pub-brand-icon" style="width: 50px; height: 50px; font-size: 26px;">🌸</div>
                        <div>
                            <h2 style="font-size: 24px; margin: 0;">{{ $siteName ?? 'Bloom Moment' }}</h2>
                            <div style="font-size: 13px; color: var(--color-text-muted);">Bukti Penerimaan Barang Masuk (Inventaris)</div>
                        </div>
                    @endif
                </div>

                <div style="text-align: right;">
                    <div style="font-size: 12px; text-transform: uppercase; color: var(--color-text-muted); letter-spacing: 1px;">No. Referensi</div>
                    <div style="font-size: 20px; font-weight: 700; color: var(--color-warm-brown-dark); font-family: monospace;">
                        {{ $purchase->reference_number }}
                    </div>
                    <div style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                        Tanggal: <strong>{{ $purchase->purchase_date->format('d F Y') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Supplier & Staff Metadata -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; background-color: var(--color-warm-ivory); padding: 20px; border-radius: var(--radius-md);">
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; color: var(--color-muted-olive-dark); margin-bottom: 6px;">
                        Diterima dari Pemasok:
                    </div>
                    <div style="font-size: 16px; font-weight: 700; color: var(--color-warm-brown-dark);">
                        {{ $purchase->supplier->name }}
                    </div>
                    <div style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
                        PIC: {{ $purchase->supplier->contact_person ?: '—' }} | Telp: {{ $purchase->supplier->phone ?: '—' }}
                    </div>
                    <div style="font-size: 13px; color: var(--color-text-muted);">
                        {{ $purchase->supplier->address ?: '—' }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; color: var(--color-muted-olive-dark); margin-bottom: 6px;">
                        Dicatat Oleh Petugas:
                    </div>
                    <div style="font-size: 16px; font-weight: 700; color: var(--color-warm-brown-dark);">
                        {{ $purchase->user->name }}
                    </div>
                    <div style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
                        Peran: <span class="badge {{ $purchase->user->isAdmin() ? 'role-admin' : 'role-staff' }}">{{ $purchase->user->role }}</span>
                    </div>
                    <div style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                        Waktu Sistem: {{ $purchase->created_at->format('d/m/Y H:i') }} WIB
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive" style="margin-bottom: 30px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Bunga & Kategori</th>
                            <th style="text-align: right;">Kuantitas Masuk</th>
                            <th style="text-align: right;">Harga Satuan Beli</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                    <div style="font-size: 11px; color: var(--color-text-muted);">
                                        Kategori: {{ $item->product->category->name }}
                                    </div>
                                </td>
                                <td style="text-align: right; font-weight: 600;">
                                    +{{ $item->quantity }} tangkai
                                </td>
                                <td style="text-align: right;">
                                    Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                                </td>
                                <td style="text-align: right; font-weight: 600; color: var(--color-warm-brown-dark);">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: var(--color-warm-ivory);">
                            <td colspan="4" style="text-align: right; font-weight: 700; font-size: 15px; padding: 16px;">
                                TOTAL PEMBELIAN:
                            </td>
                            <td style="text-align: right; font-weight: 700; font-size: 18px; color: var(--color-muted-olive-dark); padding: 16px;">
                                Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes -->
            @if($purchase->notes)
                <div style="background-color: #FAF6F0; border-left: 4px solid var(--color-muted-olive); padding: 14px 18px; border-radius: var(--radius-sm); font-size: 13.5px;">
                    <strong>Catatan Transaksi:</strong>
                    <p style="margin-top: 4px; color: var(--color-text-muted);">{{ $purchase->notes }}</p>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
