<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Akun — {{ $siteName ?? 'Bloom Moment' }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="login-page-wrap">
        <div class="login-card">
            <div style="text-align: center; margin-bottom: 24px;">
                @if(!empty($siteLogo))
                    <div style="margin-bottom: 14px;">
                        <img src="{{ $siteLogo }}" alt="{{ $siteName ?? 'Bloom Moment' }}" style="max-height: 52px; max-width: 190px; object-fit: contain; margin: 0 auto;">
                    </div>
                @else
                    <div class="pub-brand-icon" style="margin: 0 auto 12px; width: 48px; height: 48px; font-size: 24px;">🌸</div>
                    <h2 style="font-size: 24px; margin-bottom: 4px;">{{ $siteName ?? 'Bloom Moment' }}</h2>
                @endif
                <p style="font-size: 13.5px; color: var(--color-text-muted);">Portal Masuk Pelanggan & Petugas Backoffice</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    <span>⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <span>✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email <span class="req">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi <span class="req">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 13px;">
                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} style="accent-color: var(--color-sage-green);">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px;">
                    🌸 Masuk ke Akun
                </button>
            </form>

            <div style="text-align: center; margin-top: 20px; padding: 12px; background-color: var(--color-butter-yellow-light); border: 1px solid var(--color-butter-yellow); border-radius: var(--radius-sm); font-size: 13.5px;">
                Belum punya akun pembeli? 
                <a href="{{ route('register') }}" style="color: var(--color-dark-brown); font-weight: 700; text-decoration: underline;">
                    Daftar di sini →
                </a>
            </div>

            <div style="text-align: center; margin-top: 24px; font-size: 13.5px;">
                <a href="{{ url('/') }}" style="color: var(--color-sage-green-hover, #728266); font-weight: 600;">
                    ← Kembali ke Katalog Publik
                </a>
            </div>
        </div>
    </div>
</body>
</html>
