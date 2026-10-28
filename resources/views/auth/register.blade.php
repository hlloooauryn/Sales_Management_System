<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pelanggan — {{ $siteName ?? 'Bloom Moment' }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="login-page-wrap">
        <div class="login-card" style="max-width: 520px;">
            <div style="text-align: center; margin-bottom: 24px;">
                @if(!empty($siteLogo))
                    <div style="margin-bottom: 14px;">
                        <img src="{{ $siteLogo }}" alt="{{ $siteName ?? 'Bloom Moment' }}" style="max-height: 52px; max-width: 190px; object-fit: contain; margin: 0 auto;">
                    </div>
                @else
                    <div class="pub-brand-icon" style="margin: 0 auto 12px; width: 48px; height: 48px; font-size: 24px;">🌸</div>
                    <h2 style="font-size: 24px; margin-bottom: 4px;">{{ $siteName ?? 'Bloom Moment' }}</h2>
                @endif
                <h3 style="font-size: 18px; margin-bottom: 4px; color: var(--color-dark-brown);">Pendaftaran Akun Pelanggan</h3>
                <p style="font-size: 13.5px; color: var(--color-text-muted);">Buat akun untuk memesan bunga online, lacak pengiriman, dan nikmati promo spesial.</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    <span>⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <span>⚠️</span>
                    <div>
                        <strong>Mohon periksa kesalahan input:</strong>
                        <ul style="margin-top: 4px; padding-left: 18px; font-size: 13px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Amanda Putri" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email <span class="req">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="amanda@gmail.com" required>
                    <small style="font-size: 11.5px; color: var(--color-text-muted);">Digunakan untuk masuk dan menerima invoice pesanan.</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Nomor WhatsApp / Telepon <span class="req">*</span></label>
                    <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="0812-xxxx-xxxx" required>
                    <small style="font-size: 11.5px; color: var(--color-text-muted);">Untuk konfirmasi pesanan dan koordinasi kurir florist.</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Alamat Pengiriman (Opsional)</label>
                    <textarea name="address" id="address" class="form-control" rows="2" placeholder="Nama jalan, nomor rumah, kelurahan, kecamatan, kota">{{ old('address') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label class="form-label" for="password">Kata Sandi <span class="req">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Min. 6 karakter" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Sandi <span class="req">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi sandi" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 13px; font-size: 15px; margin-top: 8px;">
                    🌸 Daftarkan Akun Saya
                </button>
            </form>

            <div style="text-align: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--color-border); font-size: 13.5px;">
                <span style="color: var(--color-text-muted);">Sudah memiliki akun?</span>
                <a href="{{ route('login') }}" style="color: var(--color-sage-green-hover, #728266); font-weight: 700; margin-left: 4px;">
                    Masuk di sini →
                </a>
            </div>

            <div style="text-align: center; margin-top: 16px; font-size: 13px;">
                <a href="{{ url('/') }}" style="color: var(--color-text-muted);">
                    ← Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</body>
</html>
