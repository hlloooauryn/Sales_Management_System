@extends('layouts.public')

@section('title', 'Keranjang Belanja Bunga')

@section('content')
<!-- Breadcrumbs -->
<div style="background-color: var(--color-warm-ivory); border-bottom: 1px solid var(--color-border); padding: 14px 0; font-size: 13px;">
    <div class="pub-container">
        <a href="{{ url('/') }}">Beranda</a>
        <span style="margin: 0 8px; color: var(--color-text-muted);">/</span>
        <a href="{{ route('catalog') }}">Katalog Bunga</a>
        <span style="margin: 0 8px; color: var(--color-text-muted);">/</span>
        <span style="color: var(--color-warm-brown-dark); font-weight: 600;">Keranjang Belanja</span>
    </div>
</div>

<div class="pub-container" style="padding: 40px 24px 80px;">
    
    <div style="margin-bottom: 30px;">
        <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--color-muted-olive-dark);">Pilihan Bunga Segar</span>
        <h1 style="font-size: 32px; margin-top: 4px;">🛒 Keranjang Belanja Anda</h1>
    </div>

    @if(empty($cart))
        <!-- Empty Cart State -->
        <div style="text-align: center; padding: 70px 20px; background: #FFFFFF; border-radius: var(--radius-lg); border: 1px dashed var(--color-border); max-width: 640px; margin: 0 auto; box-shadow: var(--shadow-sm);">
            <div style="font-size: 54px; margin-bottom: 16px;">🌸🛒</div>
            <h2 style="font-size: 24px; margin-bottom: 8px;">Keranjang Belanja Anda Masih Kosong</h2>
            <p style="color: var(--color-text-muted); max-width: 440px; margin: 0 auto 24px; line-height: 1.6;">
                Belum ada bunga atau buket yang dipilih. Temukan rangkaian bunga potong segar, hand bouquet, dan flower box eksklusif kami.
            </p>
            <a href="{{ route('catalog') }}" class="btn btn-primary" style="padding: 13px 28px; font-size: 15px;">
                💐 Jelajahi Katalog Bunga
            </a>
        </div>
    @else
        <!-- Cart Grid Layout -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 36px; align-items: start;">
            
            <!-- Left: Items Table -->
            <div class="card" style="box-shadow: var(--shadow-sm);">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 18px; margin: 0;">Item Bunga ({{ $totalItems }} Tangkai)</h3>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua bunga dari keranjang?');">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm" style="color: var(--color-danger); border-color: #E6C2C2; padding: 5px 12px;">
                            🗑️ Kosongkan Keranjang
                        </button>
                    </form>
                </div>
                
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Bunga</th>
                                    <th>Harga</th>
                                    <th style="text-align: center;">Jumlah</th>
                                    <th style="text-align: right;">Subtotal</th>
                                    <th style="text-align: center;">Hapus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $item)
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 14px;">
                                                <div style="width: 58px; height: 58px; border-radius: var(--radius-sm); overflow: hidden; background: var(--color-warm-ivory); flex-shrink: 0; border: 1px solid var(--color-border);">
                                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                                <div>
                                                    <a href="{{ route('product.detail', $item['slug']) }}" style="font-weight: 700; color: var(--color-dark-brown); font-size: 14.5px;">
                                                        {{ $item['name'] }}
                                                    </a>
                                                    <div style="font-size: 12px; color: var(--color-text-muted);">
                                                        🏷️ {{ $item['category_name'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                        </td>
                                        <td style="text-align: center;">
                                            <!-- Update Qty Form -->
                                            <form action="{{ route('cart.update') }}" method="POST" style="display: inline-flex; align-items: center; gap: 4px; margin: 0;">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}" class="form-control" style="width: 60px; text-align: center; padding: 6px; font-weight: 700;">
                                                <button type="submit" class="btn btn-outline btn-sm" style="padding: 6px 8px;" title="Perbarui Jumlah">
                                                    ✓
                                                </button>
                                            </form>
                                        </td>
                                        <td style="text-align: right; font-weight: 700; color: var(--color-muted-olive-dark); white-space: nowrap;">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </td>
                                        <td style="text-align: center;">
                                            <form action="{{ route('cart.remove') }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 8px; font-size: 12px;" title="Hapus dari keranjang">
                                                    ✕
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="padding: 16px 24px; border-top: 1px solid var(--color-border); background: var(--color-surface-soft); display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('catalog') }}" class="btn btn-outline btn-sm">
                        ← Tambah Bunga Lain
                    </a>
                    <span style="font-size: 13px; color: var(--color-text-muted);">
                        Bunga dirawat segar dalam wadah sejuk sebelum pengantaran.
                    </span>
                </div>
            </div>

            <!-- Right: Order Summary Card -->
            <div class="card" style="box-shadow: var(--shadow-md); position: sticky; top: 90px;">
                <div class="card-header" style="background: var(--color-warm-ivory);">
                    <h3 style="font-size: 18px; margin: 0;">Ringkasan Pesanan</h3>
                </div>
                
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                        <span style="color: var(--color-text-muted);">Total Item:</span>
                        <strong>{{ $totalItems }} Tangkai</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                        <span style="color: var(--color-text-muted);">Subtotal Bunga:</span>
                        <strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 14px; align-items: center;">
                        <span style="color: var(--color-text-muted);">Ongkos Kirim Kurir:</span>
                        <span class="badge badge-success" style="font-size: 12px;">GRATIS (Promo)</span>
                    </div>

                    <div style="border-top: 2px dashed var(--color-border); padding-top: 16px; margin-top: 16px; display: flex; justify-content: space-between; align-items: baseline;">
                        <span style="font-size: 16px; font-weight: 700; color: var(--color-dark-brown);">Total Tagihan:</span>
                        <span style="font-size: 24px; font-weight: 800; color: var(--color-muted-olive-dark); font-family: var(--font-heading);">
                            Rp {{ number_format($totalAmount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div style="margin-top: 24px;">
                        @auth
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 15px; text-align: center; display: block; border-radius: var(--radius-md);">
                                🌸 Lanjut ke Checkout & Pembayaran →
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 15px; text-align: center; display: block; border-radius: var(--radius-md);">
                                🔐 Masuk / Login untuk Membeli Online →
                            </a>
                            <div style="text-align: center; margin-top: 8px; font-size: 12.5px; color: var(--color-text-muted);">
                                Belum punya akun? <a href="{{ route('register') }}" style="color: var(--color-dark-brown); font-weight: 700;">Daftar gratis</a>
                            </div>
                        @endauth
                    </div>

                    <!-- Trust Points -->
                    <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--color-border); font-size: 12.5px; color: var(--color-text-muted);">
                        <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                            <span>🌿</span>
                            <span>100% Bunga segar pilihan dirangkai hari ini</span>
                        </div>
                        <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                            <span>💳</span>
                            <span>Metode pembayaran lengkap (Transfer Bank BCA/Mandiri/BNI/BRI/BSI, E-Wallet GoPay/OVO/DANA/ShopeePay, QRIS, VA, Indomaret/Alfamart, & COD)</span>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <span>💌</span>
                            <span>Gratis kartu ucapan kustom & pita elegan</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif

</div>
@endsection
