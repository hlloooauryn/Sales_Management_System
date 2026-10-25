@extends('layouts.admin')

@section('title', 'Kategori Bunga')
@section('header_title', '🏷️ Manajemen Kategori Bunga')

@section('content')
<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px; align-items: start;">
    
    <!-- Table List Categories -->
    <div class="card">
        <div class="card-header">
            <h3>Daftar Kategori Bunga</h3>
            <span class="badge badge-neutral">{{ $categories->count() }} Kategori</span>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Produk</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index => $cat)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $cat->name }}</strong>
                                    <div style="font-size: 11px; color: var(--color-text-muted);">Slug: {{ $cat->slug }}</div>
                                </td>
                                <td>{{ $cat->description ?: '—' }}</td>
                                <td>
                                    <span class="badge badge-neutral">{{ $cat->products_count }} Bunga</span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="{{ route('categories.edit', $cat->id) }}" class="btn btn-outline btn-sm" style="padding: 4px 10px;">
                                        ✏️ Edit
                                    </a>
                                    
                                    <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 10px;" {{ $cat->products_count > 0 ? 'title="Tidak dapat dihapus karena memiliki produk" disabled style=opacity:0.4;cursor:not-allowed;' : '' }}>
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: var(--color-text-muted);">
                                    Belum ada data kategori. Silakan tambahkan pada form di samping.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Form Add Category -->
    <div class="card">
        <div class="card-header">
            <h3>Tambah Kategori Baru</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="name">Nama Kategori <span class="req">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Fresh Cut Flowers" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi Singkat</label>
                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Jelaskan jenis bunga dalam kategori ini...">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    💾 Simpan Kategori
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
