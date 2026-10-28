@extends('layouts.public')

@section('title', 'Katalog Bunga Lengkap')

@section('content')
<!-- Page Header -->
<div style="background: linear-gradient(135deg, var(--color-warm-ivory) 0%, #F3ECE6 100%); padding: 50px 0; border-bottom: 1px solid var(--color-border);">
    <div class="pub-container">
        <h1 style="font-size: 34px; margin-bottom: 10px;">Katalog Bunga & Rangkaian</h1>
        <p style="color: var(--color-text-muted); max-width: 600px; font-size: 15px;">
            Pilih beragam tangkai mawar segar, hand bouquet manis, box bunga mewah, hingga dried flower awet untuk setiap suasana.
        </p>
    </div>
</div>

<div class="pub-container" style="padding: 40px 24px 80px;">
    <!-- Filter and Search Bar -->
    <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 36px; box-shadow: var(--shadow-sm);">
        <form action="{{ route('catalog') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1.2fr 1fr auto; gap: 16px; align-items: end;">
            
            <!-- Search -->
            <div>
                <label class="form-label" for="search">Cari Nama / Jenis Bunga</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Contoh: Mawar Merah, Lily, Tulip...">
            </div>

            <!-- Category Filter -->
            <div>
                <label class="form-label" for="category">Kategori Bunga</label>
                <select name="category" id="category" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label class="form-label" for="sort">Urutkan Harga</label>
                <select name="sort" id="sort" class="form-control">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>

            <!-- Submit & Reset -->
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 18px;">
                    🔍 Cari
                </button>
                @if(request()->hasAny(['search', 'category', 'sort']))
                    <a href="{{ route('catalog') }}" class="btn btn-outline" style="padding: 10px 14px;" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Active Filter Tags -->
    @if(request('category') || request('search'))
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <span style="font-size: 13px; color: var(--color-text-muted);">Filter Aktif:</span>
            @if(request('category'))
                <span class="badge badge-neutral">
                    Kategori: {{ $categories->firstWhere('slug', request('category'))->name ?? request('category') }}
                </span>
            @endif
            @if(request('search'))
                <span class="badge badge-neutral">
                    Kata Kunci: "{{ request('search') }}"
                </span>
            @endif
            <a href="{{ route('catalog') }}" style="font-size: 12.5px; color: var(--color-danger); text-decoration: underline;">
                Hapus Semua Filter
            </a>
        </div>
    @endif

    <!-- Product Grid -->
    <div class="product-grid">
        @forelse($products as $product)
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
            <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; background: #FFFFFF; border-radius: var(--radius-md); border: 1px dashed var(--color-border);">
                <div style="font-size: 40px; margin-bottom: 12px;">🥀</div>
                <h3 style="font-size: 20px; margin-bottom: 6px;">Bunga Tidak Ditemukan</h3>
                <p style="color: var(--color-text-muted); max-width: 460px; margin: 0 auto 20px;">
                    Maaf, tidak ada produk bunga yang cocok dengan kata kunci atau filter yang Anda pilih. Coba gunakan kata kunci lain atau reset filter pencarian.
                </p>
                <a href="{{ route('catalog') }}" class="btn btn-primary btn-sm">
                    Kembali ke Semua Koleksi Bunga
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top: 40px;">
        {{ $products->links() }}
    </div>
</div>
@endsection
