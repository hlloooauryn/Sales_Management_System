@extends('layouts.admin')

@section('title', 'Tambah Produk Bunga Baru')
@section('header_title', '🌹 Tambah Produk Bunga')

@section('content')
<div style="max-width: 820px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h3>Formulir Data Bunga Baru</h3>
            <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm">
                ← Kembali ke Katalog
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Produk Bunga <span class="req">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Mawar Merah Holland Velvet" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category_id">Kategori Bunga <span class="req">*</span></label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi Bunga & Karakteristik</label>
                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Jelaskan aroma, daya tahan segar, paduan warna, dsb...">{{ old('description') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label" for="buy_price">Harga Beli / Pokok (Rp) <span class="req">*</span></label>
                        <input type="number" name="buy_price" id="buy_price" class="form-control" value="{{ old('buy_price', 0) }}" min="0" step="500" required>
                        <small style="color: var(--color-text-muted); font-size: 12px;">Harga kulak/modal per tangkai atau item.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sell_price">Harga Jual Konsumen (Rp) <span class="req">*</span></label>
                        <input type="number" name="sell_price" id="sell_price" class="form-control" value="{{ old('sell_price', 0) }}" min="0" step="500" required>
                        <small style="color: var(--color-text-muted); font-size: 12px;">Harga tampil pada katalog publik & kasir.</small>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label" for="initial_stock">Stok Awal Fisik <span class="req">*</span></label>
                        <input type="number" name="initial_stock" id="initial_stock" class="form-control" value="{{ old('initial_stock', 0) }}" min="0" required>
                        <small style="color: var(--color-text-muted); font-size: 12px;">Jumlah fisik yang tersedia saat ini di toko.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="min_stock">Ambang Stok Minimum (Peringatan) <span class="req">*</span></label>
                        <input type="number" name="min_stock" id="min_stock" class="form-control" value="{{ old('min_stock', 5) }}" min="0" required>
                        <small style="color: var(--color-text-muted); font-size: 12px;">Sistem akan memberi alert jika stok &le; angka ini.</small>
                    </div>
                </div>

                <!-- Bagian Unggah Foto Produk dengan Live Preview -->
                <div class="form-group" style="background-color: var(--color-warm-ivory); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 18px; margin-top: 10px;">
                    <label class="form-label" style="margin-bottom: 4px; display: flex; align-items: center; justify-content: space-between;">
                        <span>📸 Unggah Foto Bunga</span>
                        <span style="font-size: 12px; font-weight: normal; color: var(--color-text-muted);">Mendukung JPG, PNG, WEBP, SVG (Maks 3MB)</span>
                    </label>
                    
                    <div style="display: flex; gap: 18px; align-items: flex-start; margin-top: 12px; flex-wrap: wrap;">
                        <!-- Dropzone / Input File -->
                        <div style="flex: 1; min-width: 250px;">
                            <input type="file" name="image" id="product_image_input" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml" style="padding: 10px; cursor: pointer;">
                            <small style="display: block; color: var(--color-text-muted); font-size: 12px; margin-top: 6px;">
                                Pilih foto bunga berkualitas baik untuk ditampilkan di etalase toko dan kasir.
                            </small>
                        </div>

                        <!-- Live Preview Box -->
                        <div id="image_preview_wrapper" style="display: none; width: 130px; text-align: center;">
                            <div style="width: 120px; height: 120px; border-radius: var(--radius-md); overflow: hidden; border: 2px solid var(--color-sage-green); box-shadow: var(--shadow-sm); background-color: #FFFFFF; margin: 0 auto 6px;">
                                <img id="image_preview_img" src="" alt="Pratinjau Foto" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <span class="badge badge-success" style="font-size: 11px;">Pratinjau Foto</span>
                            <button type="button" id="btn_cancel_preview" style="background: none; border: none; color: var(--color-danger); font-size: 11.5px; cursor: pointer; display: block; margin: 4px auto 0; text-decoration: underline;">
                                ✕ Batalkan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 16px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--color-sage-green);">
                        <span style="font-weight: 600; color: var(--color-dark-brown);">Tampilkan produk ini di etalase publik (Katalog Bloom Moment)</span>
                    </label>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 28px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 13px;">
                        💾 Simpan & Terbitkan Bunga
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline" style="padding: 13px 24px;">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('product_image_input');
        const previewWrap = document.getElementById('image_preview_wrapper');
        const previewImg = document.getElementById('image_preview_img');
        const cancelBtn = document.getElementById('btn_cancel_preview');

        input.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewWrap.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewWrap.style.display = 'none';
            }
        });

        cancelBtn.addEventListener('click', function () {
            input.value = '';
            previewImg.src = '';
            previewWrap.style.display = 'none';
        });
    });
</script>
@endsection
