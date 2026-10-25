<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Backoffice') — {{ $siteName ?? 'Bloom Moment' }} Management</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <!-- Brand -->
            <a href="{{ route('dashboard') }}" class="admin-brand" style="text-decoration: none;">
                @if(!empty($siteLogo))
                    <div style="background: rgba(255,255,255,0.95); padding: 6px 12px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm); width: 100%;">
                        <img src="{{ $siteLogo }}" alt="{{ $siteName ?? 'Bloom Moment' }}" style="max-height: 38px; max-width: 160px; object-fit: contain;">
                    </div>
                @else
                    <div class="admin-brand-icon">🌸</div>
                    <div>
                        <h2>{{ $siteName ?? 'Bloom Moment' }}</h2>
                        <small>Inventory & Sales System</small>
                    </div>
                @endif
            </a>

            <!-- Logged-in User Profile -->
            <div class="admin-user-box">
                <div class="admin-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="admin-user-info">
                    <div class="admin-user-name">{{ Auth::user()->name }}</div>
                    <span class="admin-role-badge {{ Auth::user()->isAdmin() ? 'role-admin' : 'role-staff' }}">
                        {{ Auth::user()->role }}
                    </span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="admin-nav">
                <div class="admin-nav-group-title">Menu Utama</div>
                <ul class="admin-nav-list">
                    <li class="admin-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <span class="nav-icon">📊</span>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>

                <div class="admin-nav-group-title">Master Data</div>
                <ul class="admin-nav-list">
                    @if(Auth::user()->isAdmin())
                        <li class="admin-nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <a href="{{ route('categories.index') }}">
                                <span class="nav-icon">🏷️</span>
                                <span>Kategori Bunga</span>
                            </a>
                        </li>
                    @endif

                    <li class="admin-nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <a href="{{ route('products.index') }}">
                            <span class="nav-icon">🌹</span>
                            <span>Data Produk & Stok</span>
                        </a>
                    </li>

                    <li class="admin-nav-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <a href="{{ route('suppliers.index') }}">
                            <span class="nav-icon">🏢</span>
                            <span>Pemasok / Supplier</span>
                        </a>
                    </li>
                </ul>

                <div class="admin-nav-group-title">Transaksi Inventaris</div>
                <ul class="admin-nav-list">
                    <li class="admin-nav-item {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                        <a href="{{ route('purchases.index') }}">
                            <span class="nav-icon">📥</span>
                            <span>Pembelian (Masuk)</span>
                        </a>
                    </li>
                    <li class="admin-nav-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <a href="{{ route('sales.index') }}">
                            <span class="nav-icon">🛒</span>
                            <span>Penjualan (Kasir)</span>
                        </a>
                    </li>
                </ul>

                @if(Auth::user()->isAdmin())
                    <div class="admin-nav-group-title">Laporan & Pengaturan</div>
                    <ul class="admin-nav-list">
                        <li class="admin-nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <a href="{{ route('reports.index') }}">
                                <span class="nav-icon">📈</span>
                                <span>Laporan Penjualan</span>
                            </a>
                        </li>
                        <li class="admin-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}">
                                <span class="nav-icon">👥</span>
                                <span>Kelola Pengguna</span>
                            </a>
                        </li>
                        <li class="admin-nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <a href="{{ route('settings.index') }}">
                                <span class="nav-icon">⚙️</span>
                                <span>Pengaturan Toko & Logo</span>
                            </a>
                        </li>
                    </ul>
                @endif
            </nav>

            <!-- Sidebar Footer -->
            <div class="admin-sidebar-footer">
                <div style="display: flex; gap: 8px; flex-direction: column;">
                    <a href="{{ url('/') }}" target="_blank" class="btn btn-outline btn-sm" style="color: #D2C3B7; border-color: #4A3C33; width: 100%;">
                        🌐 Lihat Web Publik ↗
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" style="width: 100%; border-radius: var(--radius-sm);">
                            🚪 Keluar (Logout)
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="admin-content">
            <!-- Topbar Header -->
            <header class="admin-topbar">
                <div class="admin-topbar-title">
                    @yield('header_title', 'Dashboard')
                </div>

                <div style="display: flex; align-items: center; gap: 16px; font-size: 13.5px; color: var(--color-text-muted);">
                    <div>📅 {{ now()->translatedFormat('l, d F Y') }}</div>
                    <div style="border-left: 1px solid var(--color-border); height: 24px;"></div>
                    <div>Role: <strong style="color: var(--color-warm-brown);">{{ ucfirst(Auth::user()->role) }}</strong></div>
                </div>
            </header>

            <!-- Main Page Body -->
            <main class="admin-main">
                @if(session('success'))
                    <div class="alert alert-success">
                        <span style="font-size: 18px;">✅</span>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <span style="font-size: 18px;">⚠️</span>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <span style="font-size: 18px;">⚠️</span>
                        <div>
                            <strong>Terdapat kesalahan pada input formulir:</strong>
                            <ul style="margin-top: 6px; padding-left: 20px;">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
