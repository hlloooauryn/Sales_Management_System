<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas Internal — Bloom Moment</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="login-page-wrap">
        <div class="login-card">
            <div style="text-align: center; margin-bottom: 24px;">
                @if(!empty($siteLogo))
                    <div style="margin-bottom: 14px;">
                        <img src="{{ $siteLogo }}" alt="Bloom Moment" style="max-height: 52px; max-width: 190px; object-fit: contain; margin: 0 auto;">
                    </div>
                @else
                    <div class="pub-brand-icon" style="margin: 0 auto 12px; width: 48px; height: 48px; font-size: 24px;">🌸</div>
                    <h2 style="font-size: 24px; margin-bottom: 4px;">Bloom Moment</h2>
                @endif
                <p style="font-size: 13.5px; color: var(--color-text-muted);">Portal Masuk Pengelolaan Inventaris & Kasir</p>
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
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="nama@bloommoment.com" required autofocus>
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
                    Masuk ke Backoffice
                </button>
            </form>

            <!-- Akun Demo untuk Pengujian Cepat -->
            <div class="login-demo-box">
                <div class="login-demo-title">Akun Demo Pengujian (Klik untuk Isi Cepat):</div>
                
                <div class="demo-account-pill" onclick="fillAccount('admin@bloommoment.com')">
                    <div>
                        <strong>Owner / Admin</strong><br>
                        <span style="color: var(--color-text-muted);">admin@bloommoment.com</span>
                    </div>
                    <span class="badge role-admin">Admin</span>
                </div>

                <div class="demo-account-pill" onclick="fillAccount('staff@bloommoment.com')">
                    <div>
                        <strong>Staff Florist</strong><br>
                        <span style="color: var(--color-text-muted);">staff@bloommoment.com</span>
                    </div>
                    <span class="badge role-staff">Staff</span>
                </div>
            </div>

            <div style="text-align: center; margin-top: 24px; font-size: 13.5px;">
                <a href="{{ url('/') }}" style="color: var(--color-sage-green-hover, #728266); font-weight: 600;">
                    ← Kembali ke Katalog Publik
                </a>
            </div>
        </div>
    </div>

    <script>
        function fillAccount(email) {
            document.getElementById('email').value = email;
            var passwordField = document.getElementById('password');
            passwordField.value = '';
            passwordField.focus();
        }
    </script>
</body>
</html>
