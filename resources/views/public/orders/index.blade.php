@extends('layouts.public')

@section('title', 'Pesanan Saya — Riwayat Pembelian Online')

@section('content')
<!-- Breadcrumbs -->
<div style="background-color: var(--color-warm-ivory); border-bottom: 1px solid var(--color-border); padding: 14px 0; font-size: 13px;">
    <div class="pub-container">
        <a href="{{ url('/') }}">Beranda</a>
        <span style="margin: 0 8px; color: var(--color-text-muted);">/</span>
        <span style="color: var(--color-warm-brown-dark); font-weight: 600;">Pesanan Saya</span>
    </div>
</div>

<div class="pub-container" style="padding: 40px 24px 80px;">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--color-muted-olive-dark);">Aktivitas Belanja</span>
            <h1 style="font-size: 32px; margin-top: 4px;">🛍️ Riwayat Pesanan Saya</h1>
            <p style="color: var(--color-text-muted); font-size: 14px; margin-top: 2px;">
                Pantau status pembayaran, proses rangkaian, dan pengiriman pesanan bunga Anda.
            </p>
        </div>

        <a href="{{ route('catalog') }}" class="btn btn-primary btn-sm">
            🌸 Pesan Bunga Baru
        </a>
    </div>

    <!-- Filter Status Tabs -->
    <div style="display: flex; gap: 8px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 6px;">
        <a href="{{ route('orders.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline' }}">
            Semua Pesanan
        </a>
        <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-primary' : 'btn-outline' }}">
            ⏳ Menunggu Bayar
        </a>
        <a href="{{ route('orders.index', ['status' => 'paid']) }}" class="btn btn-sm {{ request('status') === 'paid' ? 'btn-primary' : 'btn-outline' }}">
            ✓ Sudah Bayar
        </a>
        <a href="{{ route('orders.index', ['status' => 'processing']) }}" class="btn btn-sm {{ request('status') === 'processing' ? 'btn-primary' : 'btn-outline' }}">
            🌿 Sedang Dirangkai
        </a>
        <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="btn btn-sm {{ request('status') === 'completed' ? 'btn-primary' : 'btn-outline' }}">
            ✅ Selesai
        </a>
    </div>

    @if($orders->isEmpty())
        <!-- Empty Orders State -->
        <div style="text-align: center; padding: 60px 20px; background: #FFFFFF; border-radius: var(--radius-lg); border: 1px dashed var(--color-border); max-width: 580px; margin: 0 auto; box-shadow: var(--shadow-sm);">
            <div style="font-size: 48px; margin-bottom: 14px;">🛍️🥀</div>
            <h3 style="font-size: 20px; margin-bottom: 6px;">Belum Ada Riwayat Pesanan</h3>
            <p style="color: var(--color-text-muted); margin-bottom: 20px; font-size: 14px;">
                Anda belum memiliki pesanan dengan filter ini. Mari pilih buket atau bunga segar favorit untuk momen spesial Anda.
            </p>
            <a href="{{ route('catalog') }}" class="btn btn-primary">
                Katalog Bunga Segar →
            </a>
        </div>
    @else
        <!-- Order Cards List -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            @foreach($orders as $order)
                <div class="card" style="box-shadow: var(--shadow-sm); transition: var(--transition);">
                    <!-- Order Header -->
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; background: var(--color-warm-ivory); flex-wrap: wrap; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <strong style="font-family: monospace; font-size: 15px; color: var(--color-dark-brown);">
                                {{ $order->invoice_number }}
                            </strong>
                            <span style="font-size: 12.5px; color: var(--color-text-muted);">
                                📅 {{ $order->created_at->format('d M Y, H:i') }} WIB
                            </span>
                            <span class="badge badge-neutral" style="font-size: 11.5px;">
                                {{ $order->payment_method }}
                            </span>
                        </div>

                        <span class="badge {{ $order->status_badge_class }}" style="font-size: 12.5px; padding: 5px 12px;">
                            ● {{ $order->status_label }}
                        </span>
                    </div>

                    <!-- Order Body -->
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: center;">
                            <!-- Items Mini List -->
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                @foreach($order->items as $item)
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 42px; height: 42px; border-radius: var(--radius-sm); overflow: hidden; background: var(--color-warm-ivory); border: 1px solid var(--color-border); flex-shrink: 0;">
                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div>
                                            <div style="font-size: 13.5px; font-weight: 700; color: var(--color-dark-brown);">
                                                {{ $item->product->name }}
                                            </div>
                                            <div style="font-size: 12px; color: var(--color-text-muted);">
                                                {{ $item->quantity }} tangkai @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if($order->shipping_address)
                                    <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 6px;">
                                        📍 Alamat: {{ Str::limit($order->shipping_address, 65) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Right: Total & Action -->
                            <div style="text-align: right; border-left: 1px dashed var(--color-border); padding-left: 24px;">
                                <div style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 2px;">Total Pembayaran:</div>
                                <div style="font-size: 20px; font-weight: 800; color: var(--color-muted-olive-dark); font-family: var(--font-heading); margin-bottom: 12px;">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </div>

                                <a href="{{ route('orders.show', $order->invoice_number) }}" class="btn btn-primary btn-sm" style="padding: 8px 18px;">
                                    Lihat Detail & Pembayaran →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 30px;">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection
