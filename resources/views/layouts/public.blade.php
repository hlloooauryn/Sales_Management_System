<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Katalog Bunga Cantik') — {{ $siteName ?? 'Bloom Moment' }}</title>
    <meta name="description" content="@yield('meta_description', 'Toko bunga segar, buket pernikahan, dan kado bunga elegan ' . ($siteName ?? 'Bloom Moment') . '.')">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body>

    <!-- Top Announcement Bar -->
    <div class="pub-announcement-bar">
        🌸 Bunga Segar Pilihan Tiba Setiap Pagi • Buka Setiap Hari 08.00 - 21.00 WIB • Pengiriman Seluruh Kota
    </div>

    <!-- Public Navigation Bar -->
    <header class="pub-navbar">
        <div class="pub-container">
            <div class="pub-nav-inner">
                <!-- Brand -->
                <a href="{{ url('/') }}" class="pub-brand">
                    @if(!empty($siteLogo))
                        <img src="{{ $siteLogo }}" alt="{{ $siteName ?? 'Bloom Moment' }}" style="max-height: 48px; max-width: 180px; object-fit: contain;">
                    @else
                        <div class="pub-brand-icon">🌸</div>
                        <div class="pub-brand-text">
                            <h1>{{ $siteName ?? 'Bloom Moment' }}</h1>
                            <span>{{ $siteTagline ?? 'Artisan Florist & Botanicals' }}</span>
                        </div>
                    @endif
                </a>

                <!-- Nav Links -->
                <nav>
                    <ul class="pub-nav-links">
                        <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Beranda</a></li>
                        <li><a href="{{ route('catalog') }}" class="{{ request()->is('catalog*') ? 'active' : '' }}">Katalog Bunga</a></li>
                        <li><a href="{{ route('about') }}" class="{{ request()->is('about') ? 'active' : '' }}">Tentang Kami</a></li>
                        <li><a href="{{ route('contact') }}" class="{{ request()->is('contact') ? 'active' : '' }}">Kontak</a></li>
                    </ul>
                </nav>

                <!-- Cart & Auth Actions -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <!-- Shopping Cart Button -->
                    <a href="{{ route('cart.index') }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; position: relative;" title="Lihat Keranjang Belanja">
                        <span style="font-size: 16px;">🛒</span>
                        <span style="font-weight: 600;">Keranjang</span>
                        @if(($cartCount ?? 0) > 0)
                            <span class="badge" style="background-color: var(--color-dusty-pink-dark); color: #FFFFFF; font-size: 11px; padding: 1px 7px; border-radius: 999px; font-weight: 700;">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm" style="padding: 7px 14px;">
                                ⚙️ Dashboard Backoffice
                            </a>
                        @else
                            <!-- Menu Khusus Pelanggan / Role User -->
                            <a href="{{ route('orders.index') }}" class="btn btn-outline btn-sm {{ request()->routeIs('orders.*') ? 'active' : '' }}" style="padding: 7px 12px;">
                                🛍️ Pesanan Saya
                            </a>
                            <div style="font-size: 13px; font-weight: 600; color: var(--color-warm-brown); background: var(--color-butter-yellow-light); border: 1px solid var(--color-butter-yellow); padding: 5px 12px; border-radius: var(--radius-sm);">
                                👤 {{ Str::limit(Auth::user()->name, 14) }}
                            </div>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm" style="background: none; border: 1px solid var(--color-border); color: var(--color-text-muted); padding: 7px 10px;" title="Keluar dari akun">
                                🚪 Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline btn-sm" style="padding: 7px 12px;">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm" style="padding: 7px 14px;">
                            🌸 Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="pub-container" style="margin-top: 18px;">
            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom: 20px;">
                    <span style="font-size: 18px;">🌸</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="margin-bottom: 20px;">
                    <span style="font-size: 18px;">⚠️</span>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Public Footer -->
    <footer class="pub-footer">
        <div class="pub-container">
            <div class="pub-footer-grid">
                <div class="pub-footer-col">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        @if(!empty($siteLogo))
                            <div style="background: rgba(255,255,255,0.9); padding: 6px 12px; border-radius: var(--radius-sm); display: inline-flex;">
                                <img src="{{ $siteLogo }}" alt="{{ $siteName ?? 'Bloom Moment' }}" style="max-height: 40px; max-width: 160px; object-fit: contain;">
                            </div>
                        @else
                            <div class="pub-brand-icon" style="width: 38px; height: 38px; font-size: 20px;">🌸</div>
                            <h3 style="font-size: 22px; margin: 0;">{{ $siteName ?? 'Bloom Moment' }}</h3>
                        @endif
                    </div>
                    <p>
                        Menghadirkan keindahan bunga potong segar lokal dan import, hand bouquet artistik, serta flower box elegan untuk merayakan setiap momen berharga Anda.
                    </p>
                    <p style="font-size: 13px; color: var(--color-sage-green-hover, #728266); font-weight: 600;">
                        📍 {{ $siteAddress ?? 'Jl. Cendana No. 18, Kebayoran Baru, Jakarta Selatan' }}
                    </p>
                </div>

                <div class="pub-footer-col">
                    <h4>Navigasi</h4>
                    <ul>
                        <li><a href="{{ url('/') }}">Beranda</a></li>
                        <li><a href="{{ route('catalog') }}">Katalog Lengkap</a></li>
                        <li><a href="{{ route('about') }}">Tentang Kami</a></li>
                        <li><a href="{{ route('contact') }}">Hubungi Florist</a></li>
                    </ul>
                </div>

                <div class="pub-footer-col">
                    <h4>Kategori Favorit</h4>
                    <ul>
                        <li><a href="{{ route('catalog', ['category' => 'hand-bouquet']) }}">Hand Bouquet</a></li>
                        <li><a href="{{ route('catalog', ['category' => 'fresh-cut-flowers']) }}">Fresh Cut Flowers</a></li>
                        <li><a href="{{ route('catalog', ['category' => 'flower-box']) }}">Flower Box</a></li>
                        <li><a href="{{ route('catalog', ['category' => 'dried-preserved']) }}">Dried & Preserved</a></li>
                    </ul>
                </div>

                <div class="pub-footer-col">
                    <h4>Layanan & Jam Buka</h4>
                    <p>Senin – Minggu: 08.00 – 21.00 WIB</p>
                    <p>WhatsApp Pemesanan: <strong>{{ $sitePhone ?? '+62 812-9988-7766' }}</strong></p>
                    <p style="font-size: 12.5px; margin-top: 10px;">
                        <em>Website ini adalah butik bunga terintegrasi sistem inventaris dan kasir Bloom Moment.</em>
                    </p>
                </div>
            </div>

            <div class="pub-footer-bottom">
                <div>
                    &copy; {{ date('Y') }} <strong>{{ $siteName ?? 'Bloom Moment' }}</strong>. Seluruh hak cipta dilindungi.
                </div>
                <div>
                    Toko Bunga & Sistem Inventaris Berbasis Laravel MVC
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
