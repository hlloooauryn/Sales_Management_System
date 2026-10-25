@extends('layouts.admin')

@section('title', 'Pengaturan Toko & Identitas Brand')
@section('header_title', '⚙️ Pengaturan Toko & Brand')

@section('content')
<div style="max-width: 820px; margin: 0 auto;">
    
    <!-- Admin Security Banner -->
    <div style="background-color: var(--color-butter-yellow-light, #FFF8E7); border: 1px solid var(--color-butter-yellow, #F9E7A8); border-radius: var(--radius-md); padding: 14px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 14px;">
        <span style="font-size: 26px;">🛡️</span>
        <div>
            <div style="font-weight: 700; color: var(--color-dark-brown); font-size: 14px;">
                Hak Akses Khusus: Admin / Pemilik Toko
            </div>
            <div style="font-size: 12.5px; color: var(--color-text-muted); margin-top: 2px;">
                Pengaturan logo dan identitas merek toko dilindungi oleh otorisasi server. Pengguna dengan peran Staff tidak memiliki izin untuk mengubah aset merek.
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Identitas Merek & Logo Toko</h3>
            <span class="badge badge-success">Sistem Bloom Moment</span>
        </div>
        <div class="card-body">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Section: Logo Toko -->
                <div style="background-color: var(--color-surface-soft); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 22px; margin-bottom: 26px;">
                    <h4 style="font-size: 16px; margin-bottom: 4px; color: var(--color-dark-brown);">
                        🖼️ Logo Toko Bloom Moment
                    </h4>
                    <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 18px;">
                        Logo ini akan ditampilkan secara seragam di seluruh website (Navbar Publik, Footer Publik, Halaman Login, dan Sidebar Admin).
                    </p>

                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 24px; align-items: center; background: #FFFFFF; padding: 20px; border-radius: var(--radius-sm); border: 1px solid var(--color-border-subtle);">
                        <!-- Current Logo Display -->
                        <div style="text-align: center;">
                            <div style="min-width: 130px; height: 110px; border-radius: var(--radius-md); border: 2px solid var(--color-border); display: flex; align-items: center; justify-content: center; padding: 12px; background-color: var(--color-butter-cream, #FFF8E7); box-shadow: var(--shadow-sm); margin-bottom: 8px;">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="Logo Toko" style="max-height: 85px; max-width: 120px; object-fit: contain;">
                                @else
                                    <!-- Default Bloom Moment Brandmark Badge -->
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;">
                                        <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, var(--color-dusty-pink, #EBA7AD), var(--color-soft-lavender, #D8B5F0)); display: flex; align-items: center; justify-content: center; font-size: 22px; box-shadow: var(--shadow-sm);">
                                            🌸
                                        </div>
                                        <span style="font-family: var(--font-heading); font-size: 13px; font-weight: 700; color: var(--color-dark-brown);">Bloom Moment</span>
                                    </div>
                                @endif
                            </div>

                            @if($logoUrl)
                                <span class="badge badge-success" style="font-size: 11px;">Logo Kustom Aktif</span>
                            @else
                                <span class="badge badge-neutral" style="font-size: 11px;">Logo Default</span>
                            @endif
                        </div>

                        <!-- Upload New Logo & Controls -->
                        <div>
                            <label for="logo_input" style="font-weight: 600; font-size: 13.5px; display: block; margin-bottom: 6px; color: var(--color-dark-brown);">
                                Unggah File Logo Baru
                            </label>
                            <input type="file" name="logo" id="logo_input" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml" style="padding: 9px; cursor: pointer;">
                            <small style="display: block; color: var(--color-text-muted); font-size: 12px; margin-top: 6px;">
                                Format yang didukung: PNG (transparan disarankan), SVG, JPG, atau WEBP. Maksimal 2MB.
                            </small>

                            <!-- Live Preview Box -->
                            <div id="logo_preview_wrapper" style="display: none; margin-top: 14px; background-color: var(--color-butter-yellow-light, #FFF8E7); border: 1px dashed var(--color-sage-green); border-radius: var(--radius-sm); padding: 12px; display: none; align-items: center; gap: 16px;">
                                <div style="width: 70px; height: 60px; background: #FFFFFF; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px; border: 1px solid var(--color-border);">
                                    <img id="logo_preview_img" src="" alt="Pratinjau Logo" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; font-size: 13px; color: var(--color-dark-brown);">Pratinjau Logo Baru Terpilih</div>
                                    <div style="font-size: 11.5px; color: var(--color-text-muted);">Klik 'Simpan Pengaturan' untuk menerapkan ke seluruh situs.</div>
                                </div>
                                <button type="button" id="btn_cancel_logo_preview" style="background: none; border: none; color: var(--color-danger); font-size: 12px; cursor: pointer; text-decoration: underline;">
                                    ✕ Batal
                                </button>
                            </div>

                            @if($logoUrl)
                                <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--color-border-subtle);">
                                    <button type="button" onclick="document.getElementById('form_delete_logo').submit();" class="btn btn-outline btn-sm" style="color: var(--color-danger); border-color: #EBA7AD;">
                                        🗑️ Hapus Logo Kustom (Kembali ke Default)
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Section: Informasi Toko -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label" for="store_name">Nama Toko / Brand <span class="req">*</span></label>
                        <input type="text" name="store_name" id="store_name" class="form-control" value="{{ old('store_name', $storeName) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tagline">Tagline / Slogan</label>
                        <input type="text" name="tagline" id="tagline" class="form-control" value="{{ old('tagline', $tagline) }}" placeholder="Contoh: Artisan Florist & Botanicals">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label" for="phone">Nomor Telepon / WhatsApp Toko</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $phone) }}" placeholder="Contoh: 0812-9988-7766">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Alamat Fisik Butik Bunga</label>
                        <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $address) }}" placeholder="Contoh: Jl. Cendana No. 18, Jakarta Selatan">
                    </div>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 13px;">
                        💾 Simpan Pengaturan Identitas & Logo
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline" style="padding: 13px 24px;">
                        Kembali ke Dashboard
                    </a>
                </div>
            </form>

            @if($logoUrl)
                <form id="form_delete_logo" action="{{ route('settings.destroyLogo') }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logoInput = document.getElementById('logo_input');
        const previewWrap = document.getElementById('logo_preview_wrapper');
        const previewImg = document.getElementById('logo_preview_img');
        const cancelBtn = document.getElementById('btn_cancel_logo_preview');

        if (logoInput) {
            logoInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImg.src = e.target.result;
                        previewWrap.style.display = 'flex';
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewWrap.style.display = 'none';
                }
            });

            cancelBtn.addEventListener('click', function () {
                logoInput.value = '';
                previewImg.src = '';
                previewWrap.style.display = 'none';
            });
        }
    });
</script>
@endsection
