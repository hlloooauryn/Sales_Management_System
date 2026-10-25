@extends('layouts.admin')

@section('title', 'Edit Produk — ' . $product->name)
@section('header_title', '✏️ Edit Data Bunga')

@section('content')
<div style="max-width: 820px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h3>Edit: {{ $product->name }}</h3>
            <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm">
                ← Kembali ke Katalog
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Physical Stock Notice -->
                <div style="background-color: var(--color-warm-ivory); border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 14px 18px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 700; font-size: 13.5px; color: var(--color-dark-brown);">
                            📦 Stok Fisik Saat Ini: <span style="color: var(--color-sage-green); font-size: 16px; font-weight: 800;">{{ $product->stock }} tangkai</span>
                        </div>
                        <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">
                            Sesuai aturan inventaris, penambahan stok dilakukan melalui <strong>Menu Pembelian</strong> dan pengurangan melalui <strong>Menu Penjualan</strong>.
                        </div>
                    </div>

                    @if($product->isLowStock())
                        <span class="badge badge-warning">Stok Menipis</span>
                    @else
                        <span class="badge badge-success">Stok Aman</span>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Produk Bunga <span class="req">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category_id">Kategori Bunga <span class="req">*</span></label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi Bunga</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    @if(Auth::user()->isAdmin())
                        <div class="form-group">
                            <label class="form-label" for="buy_price">Harga Beli / Pokok (Rp) <span class="req">*</span></label>
                            <input type="number" name="buy_price" id="buy_price" class="form-control" value="{{ old('buy_price', $product->buy_price) }}" min="0" step="500" required>
                            <small style="color: var(--color-text-muted); font-size: 12px;">Hak istimewa Admin: Mengoreksi harga pokok.</small>
                        </div>
                    @else
                        <div class="form-group">
                            <label class="form-label">Harga Beli / Pokok (Rp)</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($product->buy_price, 0, ',', '.') }}" disabled style="background-color: var(--color-warm-ivory);">
                            <small style="color: var(--color-text-muted); font-size: 12px;">Hanya dapat diubah oleh Admin/Owner.</small>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label" for="sell_price">Harga Jual Konsumen (Rp) <span class="req">*</span></label>
                        <input type="number" name="sell_price" id="sell_price" class="form-control" value="{{ old('sell_price', $product->sell_price) }}" min="0" step="500" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label" for="min_stock">Ambang Stok Minimum <span class="req">*</span></label>
                        <input type="number" name="min_stock" id="min_stock" class="form-control" value="{{ old('min_stock', $product->min_stock) }}" min="0" required>
                        <small style="color: var(--color-text-muted); font-size: 12px;">Batas peringatan restock bagi petugas.</small>
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; padding-top: 24px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--color-sage-green);">
                            <span style="font-weight: 600; color: var(--color-dark-brown);">Status Aktif (Tampilkan di etalase publik)</span>
                        </label>
                    </div>
                </div>

                <!-- Manajemen Foto Bunga (Foto Lama & Pratinjau Pengganti) -->
                <div class="form-group" style="background-color: var(--color-warm-ivory); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 20px; margin-top: 14px;">
                    <label class="form-label" style="margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between;">
                        <span>📸 Pengelolaan Foto Bunga</span>
                        <span style="font-size: 12px; font-weight: normal; color: var(--color-text-muted);">Mendukung JPG, PNG, WEBP, SVG (Maks 3MB)</span>
                    </label>

                    <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 20px; align-items: center; margin-top: 14px; background: #FFFFFF; padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--color-border-subtle);">
                        <!-- Foto Saat Ini -->
                        <div style="text-align: center;">
                            <div style="width: 96px; height: 96px; border-radius: var(--radius-sm); overflow: hidden; border: 2px solid var(--color-border); margin: 0 auto 6px; background-color: var(--color-warm-ivory);">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <span class="badge badge-neutral" style="font-size: 11px;">Foto Saat Ini</span>
                        </div>

                        <!-- Upload Pengganti -->
                        <div>
                            <label for="edit_product_image_input" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 5px; color: var(--color-dark-brown);">
                                Pilih Foto Pengganti (Opsional):
                            </label>
                            <input type="file" name="image" id="edit_product_image_input" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml" style="padding: 8px; cursor: pointer;">
                            <small style="display: block; color: var(--color-text-muted); font-size: 12px; margin-top: 6px;">
                                ℹ️ <em>Jika Anda tidak memilih foto baru, foto saat ini akan tetap dipertahankan.</em>
                            </small>
                        </div>

                        <!-- Live Preview Pengganti -->
                        <div id="replacement_preview_wrap" style="display: none; text-align: center; border-left: 1px dashed var(--color-border); padding-left: 18px;">
                            <div style="width: 96px; height: 96px; border-radius: var(--radius-sm); overflow: hidden; border: 2px solid var(--color-sage-green); margin: 0 auto 6px; box-shadow: var(--shadow-sm);">
                                <img id="replacement_preview_img" src="" alt="Foto Pengganti" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <span class="badge badge-success" style="font-size: 11px;">Foto Pengganti</span>
                            <button type="button" id="btn_cancel_edit_preview" style="background: none; border: none; color: var(--color-danger); font-size: 11px; cursor: pointer; display: block; margin: 4px auto 0; text-decoration: underline;">
                                ✕ Batal Ganti
                            </button>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 13px;">
                        💾 Perbarui Data Produk
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
        const input = document.getElementById('edit_product_image_input');
        const previewWrap = document.getElementById('replacement_preview_wrap');
        const previewImg = document.getElementById('replacement_preview_img');
        const cancelBtn = document.getElementById('btn_cancel_edit_preview');

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
