@extends('layouts.public')

@section('title', $product->name . ' — Detail Bunga')

@section('content')
<!-- Breadcrumbs -->
<div style="background-color: var(--color-warm-ivory); border-bottom: 1px solid var(--color-border); padding: 14px 0; font-size: 13px;">
    <div class="pub-container">
        <a href="{{ url('/') }}">Beranda</a>
        <span style="margin: 0 8px; color: var(--color-text-muted);">/</span>
        <a href="{{ route('catalog') }}">Katalog</a>
        <span style="margin: 0 8px; color: var(--color-text-muted);">/</span>
        <a href="{{ route('catalog', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
        <span style="margin: 0 8px; color: var(--color-text-muted);">/</span>
        <span style="color: var(--color-warm-brown-dark); font-weight: 600;">{{ $product->name }}</span>
    </div>
</div>

<div class="pub-container" style="padding: 50px 24px 80px;">
    <!-- Product Detail Layout -->
    <div style="display: grid; grid-template-columns: 1fr 1.1fr; gap: 50px; align-items: start; margin-bottom: 70px;">
        
        <!-- Left: Product Image -->
        <div>
            <div style="border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--color-border); box-shadow: var(--shadow-md); aspect-ratio: 1/1; background-color: var(--color-warm-ivory);">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        </div>

        <!-- Right: Information & Actions -->
        <div>
            <div style="display: flex; gap: 10px; margin-bottom: 12px; align-items: center;">
                <span class="badge badge-neutral" style="font-size: 13px; padding: 5px 12px;">
                    🏷️ {{ $product->category->name }}
                </span>

                @if($product->stock <= 0)
                    <span class="badge badge-danger">Habis</span>
                @elseif($product->isLowStock())
                    <span class="badge badge-warning">Stok Menipis (Tersisa {{ $product->stock }})</span>
                @else
                    <span class="badge badge-success">Stok Siap ({{ $product->stock }} Tersedia)</span>
                @endif
            </div>

            <h1 style="font-size: 36px; margin-bottom: 14px;">{{ $product->name }}</h1>

            <div style="font-size: 28px; font-weight: 700; color: var(--color-muted-olive-dark); font-family: var(--font-heading); margin-bottom: 24px;">
                Rp {{ number_format($product->sell_price, 0, ',', '.') }}
            </div>

            <div style="padding: 20px 0; border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border); margin-bottom: 24px;">
                <h4 style="font-size: 15px; margin-bottom: 8px;">Deskripsi Bunga</h4>
                <p style="color: var(--color-text); line-height: 1.7; font-size: 14.5px;">
                    {{ $product->description }}
                </p>
            </div>

            <!-- Online Purchase & Cart Actions -->
            <div style="margin-bottom: 28px;">
                @if($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST" style="margin-bottom: 16px;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 18px;">
                            <label for="quantity" style="font-weight: 600; font-size: 14px; color: var(--color-dark-brown);">
                                Jumlah Tangkai / Item:
                            </label>
                            <div style="display: inline-flex; align-items: center; border: 1px solid var(--color-border); border-radius: var(--radius-sm); background: #FFFFFF; overflow: hidden;">
                                <button type="button" onclick="changeQty(-1)" style="border: none; background: transparent; padding: 8px 14px; cursor: pointer; font-size: 16px; font-weight: 700; color: var(--color-dark-brown);">-</button>
                                <input type="number" name="quantity" id="detailQty" value="1" min="1" max="{{ $product->stock }}" style="width: 50px; text-align: center; border: none; font-weight: 700; font-size: 15px; outline: none;" readonly>
                                <button type="button" onclick="changeQty(1)" style="border: none; background: transparent; padding: 8px 14px; cursor: pointer; font-size: 16px; font-weight: 700; color: var(--color-dark-brown);">+</button>
                            </div>
                            <span style="font-size: 13px; color: var(--color-text-muted);">
                                (Maksimal {{ $product->stock }} tangkai)
                            </span>
                        </div>

                        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                            <!-- Tambah ke Keranjang -->
                            <button type="submit" name="buy_now" value="0" class="btn btn-secondary" style="padding: 13px 22px; font-size: 15px; display: inline-flex; align-items: center; gap: 8px;">
                                <span>🛒</span>
                                <span>Tambah ke Keranjang</span>
                            </button>

                            <!-- Beli Langsung (Checkout) -->
                            <button type="submit" name="buy_now" value="1" class="btn btn-primary" style="padding: 13px 26px; font-size: 15px; display: inline-flex; align-items: center; gap: 8px;">
                                <span>⚡</span>
                                <span>Beli Sekarang</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div style="margin-bottom: 18px;">
                        <button class="btn btn-secondary" disabled style="opacity: 0.6; cursor: not-allowed; padding: 14px 26px;">
                            ⚠️ Maaf, Stok Sedang Kosong
                        </button>
                    </div>
                @endif

                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <a href="https://wa.me/6281293038852?text=Halo%20Bloom%20Moment,%20saya%20tertarik%20dengan%20bunga%20{{ urlencode($product->name) }}%20(Harga%20Rp%20{{ number_format($product->sell_price, 0, ',', '.') }})." target="_blank" class="btn btn-outline btn-sm" style="padding: 10px 16px;">
                        💬 Tanya Florist via WhatsApp
                    </a>

                    <a href="{{ route('catalog') }}" class="btn btn-outline btn-sm" style="padding: 10px 16px;">
                        ← Lihat Bunga Lainnya
                    </a>
                </div>
            </div>

            <script>
                function changeQty(delta) {
                    var input = document.getElementById('detailQty');
                    var max = parseInt(input.getAttribute('max')) || 1;
                    var val = parseInt(input.value) || 1;
                    var newVal = val + delta;
                    if (newVal >= 1 && newVal <= max) {
                        input.value = newVal;
                    }
                }
            </script>

            <!-- Florist Tips Box -->
            <div style="background-color: var(--color-sage-green-light); border: 1px solid #C9DCBE; border-radius: var(--radius-md); padding: 18px 20px;">
                <div style="font-weight: 700; color: var(--color-muted-olive-dark); font-size: 14px; margin-bottom: 6px;">
                    🌿 Tips Perawatan dari Florist Kami:
                </div>
                <ul style="font-size: 13px; color: var(--color-text); padding-left: 18px; line-height: 1.6;">
                    <li>Potong tangkai secara miring (45 derajat) sepanjang 1-2 cm sebelum dimasukkan ke dalam vas.</li>
                    <li>Ganti air vas setiap 2 hari sekali dengan air bersih suhu ruangan.</li>
                    <li>Jauhkan dari paparan sinar matahari terik langsung dan hembusan pendingin ruangan (AC).</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div style="border-top: 1px solid var(--color-border); padding-top: 50px;">
            <div style="margin-bottom: 28px;">
                <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--color-muted-olive-dark);">Rekomendasi Terkait</span>
                <h3 style="font-size: 26px;">Bunga Serupa dalam Kategori {{ $product->category->name }}</h3>
            </div>

            <div class="product-grid">
                @foreach($relatedProducts as $related)
                    <div class="product-card">
                        <div class="product-thumb">
                            <img src="{{ $related->image_url }}" alt="{{ $related->name }}">
                            <span class="product-category-badge">{{ $related->category->name }}</span>
                        </div>
                        <div class="product-info">
                            <h4 class="product-title">
                                <a href="{{ route('product.detail', $related->slug) }}">{{ $related->name }}</a>
                            </h4>
                            <div class="product-meta">
                                <span class="product-price">Rp {{ number_format($related->sell_price, 0, ',', '.') }}</span>
                                <a href="{{ route('product.detail', $related->slug) }}" class="btn btn-outline btn-sm">Lihat →</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
