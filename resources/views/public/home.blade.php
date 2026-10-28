@extends('layouts.public')

@section('title', 'Toko Bunga Segar & Hand Bouquet Premium')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="pub-container">
        <div class="hero-grid">
            <div>
                <span class="hero-tag">
                    <span>✨</span> Koleksi Spesial Musim Ini
                </span>
                <h1 class="hero-title">
                    Ungkapkan Rasa Lewat Keindahan Bunga Segar Pilihan
                </h1>
                <p class="hero-desc">
                    {{ $siteName ?? 'Bloom Moment' }} merangkai setiap tangkai bunga segar dengan ketulusan dan sentuhan artistik untuk ulang tahun, wisuda, perayaan cinta, hingga pemanis ruangan Anda.
                </p>
                <div class="hero-buttons">
                    <a href="{{ route('catalog') }}" class="btn btn-primary" style="padding: 14px 28px; font-size: 15px;">
                        🌸 Jelajahi Katalog Bunga
                    </a>
                    <a href="{{ route('about') }}" class="btn btn-outline" style="padding: 14px 26px; font-size: 15px;">
                        Tentang Kami
                    </a>
                </div>
            </div>

            <div class="hero-image-wrap">
                <div class="hero-image-card">
                    <img src="{{ asset('images/bloom_moment.jpeg') }}" alt="{{ $siteName ?? 'Bloom Moment' }} Hand Bouquet">
                </div>
                <div class="hero-badge-float">
                    <div class="hero-badge-icon">🌿</div>
                    <div>
                        <div style="font-weight: 700; color: var(--color-warm-brown-dark); font-size: 14px;">100% Segar & Alami</div>
                        <div style="font-size: 12px; color: var(--color-text-muted);">Dipetik dari perkebunan pilihan harian</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features / Keunggulan Toko -->
<section style="padding: 60px 0; background-color: #FFFFFF; border-bottom: 1px solid var(--color-border);">
    <div class="pub-container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 30px;">
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 34px; margin-bottom: 12px;">🌸</div>
                <h4 style="font-size: 17px; margin-bottom: 8px;">Bunga Potong Segar</h4>
                <p style="font-size: 13.5px; color: var(--color-text-muted);">Diambil langsung dari petani bunga Lembang dan Batu Malang setiap pagi.</p>
            </div>
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 34px; margin-bottom: 12px;">🎨</div>
                <h4 style="font-size: 17px; margin-bottom: 8px;">Rangkaian Artistik</h4>
                <p style="font-size: 13.5px; color: var(--color-text-muted);">Didesain oleh florist berpengalaman dengan palet warna natural dan estetik.</p>
            </div>
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 34px; margin-bottom: 12px;">💌</div>
                <h4 style="font-size: 17px; margin-bottom: 8px;">Gratis Kartu Ucapan</h4>
                <p style="font-size: 13.5px; color: var(--color-text-muted);">Sertakan pesan tulus Anda pada kartu ucapan elegan berpita satin.</p>
            </div>
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 34px; margin-bottom: 12px;">🚚</div>
                <h4 style="font-size: 17px; margin-bottom: 8px;">Pengiriman Hati-hati</h4>
                <p style="font-size: 13.5px; color: var(--color-text-muted);">Kemasan khusus tahan guncangan menjaga kelopak tetap utuh dan segar.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section style="padding: 80px 0;">
    <div class="pub-container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 36px;">
            <div>
                <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: var(--color-muted-olive-dark);">Pilihan Terfavorit</span>
                <h2 style="font-size: 32px; margin-top: 4px;">Koleksi Bunga Unggulan</h2>
            </div>
            <a href="{{ route('catalog') }}" class="btn btn-outline btn-sm">
                Lihat Semua Bunga →
            </a>
        </div>

        <div class="product-grid">
            @forelse($featuredProducts as $product)
                <div class="product-card">
                    <div class="product-thumb">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        <span class="product-category-badge">{{ $product->category->name }}</span>
                        
                        @if($product->stock <= 0)
                            <span class="product-stock-tag stock-out">Stok Habis</span>
                        @elseif($product->isLowStock())
                            <span class="product-stock-tag stock-low">Sisa {{ $product->stock }} tangkai</span>
                        @else
                            <span class="product-stock-tag stock-in">Tersedia ({{ $product->stock }})</span>
                        @endif
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">
                            <a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a>
                        </h3>
                        <p class="product-desc">{{ $product->description }}</p>
                        <div class="product-meta">
                            <div>
                                <span style="font-size: 11px; text-transform: uppercase; color: var(--color-text-muted); display: block;">Harga</span>
                                <span class="product-price">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</span>
                            </div>
                            <div style="display: flex; gap: 6px; align-items: center;">
                                @if($product->stock > 0)
                                    <form action="{{ route('cart.add') }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button type="submit" class="btn btn-secondary btn-sm" style="padding: 6px 11px; font-size: 13px;" title="Masukkan ke Keranjang">
                                            🛒 +
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('product.detail', $product->slug) }}" class="btn btn-outline btn-sm">
                                    Detail →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p style="grid-column: 1/-1; text-align: center; color: var(--color-text-muted); padding: 40px;">
                    Belum ada koleksi bunga yang ditampilkan.
                </p>
            @endforelse
        </div>
    </div>
</section>

<!-- Categories Showcase -->
<section style="padding: 70px 0 90px; background-color: var(--color-warm-ivory); border-top: 1px solid var(--color-border);">
    <div class="pub-container">
        <div style="text-align: center; max-width: 600px; margin: 0 auto 46px;">
            <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: var(--color-muted-olive-dark);">Kategori Rangkaian</span>
            <h2 style="font-size: 32px; margin-top: 6px;">Temukan Bunga Sesuai Kebutuhan</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 20px;">
            @foreach($categories as $category)
                <a href="{{ route('catalog', ['category' => $category->slug]) }}" style="background: #FFFFFF; border-radius: var(--radius-md); padding: 24px 20px; border: 1px solid var(--color-border); text-align: center; transition: var(--transition); display: block; box-shadow: var(--shadow-sm);" onmouseover="this.style.borderColor='var(--color-muted-olive)'; this.style.transform='translateY(-4px)';" onmouseout="this.style.borderColor='var(--color-border)'; this.style.transform='none';">
                    <div style="font-size: 32px; margin-bottom: 12px;">💐</div>
                    <h4 style="font-size: 17px; margin-bottom: 6px; color: var(--color-warm-brown-dark);">{{ $category->name }}</h4>
                    <p style="font-size: 12.5px; color: var(--color-text-muted); margin-bottom: 10px;">{{ Str::limit($category->description, 50) }}</p>
                    <span style="font-size: 12px; font-weight: 600; color: var(--color-muted-olive-dark);">{{ $category->products_count }} Koleksi Produk →</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
