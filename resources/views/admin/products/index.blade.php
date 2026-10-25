@extends('layouts.admin')

@section('title', 'Data Produk & Stok Bunga')
@section('header_title', '🌹 Master Produk & Stok Bunga')

@section('content')
<div class="card">
    <!-- Filter and Header Section -->
    <div class="card-header" style="flex-wrap: wrap; gap: 14px;">
        <div>
            <h3>Katalog Produk Internal</h3>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                Kelola informasi bunga, harga jual, stok fisik, dan batas minimum stok operasional.
            </p>
        </div>

        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
            ➕ Tambah Produk Bunga Baru
        </a>
    </div>

    <!-- Filter Form -->
    <div style="padding: 16px 24px; background-color: var(--color-warm-ivory); border-bottom: 1px solid var(--color-border);">
        <form action="{{ route('products.index') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 2; min-width: 200px;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama bunga..." style="background-color: #FFFFFF;">
            </div>

            <div style="flex: 1.5; min-width: 180px;">
                <select name="category_id" class="form-control" style="background-color: #FFFFFF;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1.5; min-width: 170px;">
                <select name="stock_status" class="form-control" style="background-color: #FFFFFF;">
                    <option value="">Semua Kondisi Stok</option>
                    <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>✅ Stok Cukup / Aman</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>⚠️ Stok Menipis (&le; Min)</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>⛔ Stok Kosong (0)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm" style="padding: 9px 16px;">
                🔍 Filter
            </button>
            @if(request()->hasAny(['search', 'category_id', 'stock_status']))
                <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm" style="padding: 9px 12px;" title="Reset Filter">
                    ✕ Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table of Products -->
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Foto</th>
                        <th>Nama Produk & Kategori</th>
                        @if(Auth::user()->isAdmin())
                            <th>Harga Beli</th>
                        @endif
                        <th>Harga Jual</th>
                        <th>Stok Fisik</th>
                        <th>Min. Stok</th>
                        <th>Status Publik</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $prod)
                        <tr style="{{ $prod->isLowStock() ? 'background-color: #FFFDF5;' : '' }}">
                            <td>
                                <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--color-border); background-color: var(--color-warm-ivory);">
                                    <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </td>
                            <td>
                                <strong style="font-size: 14.5px;">{{ $prod->name }}</strong>
                                <div>
                                    <span class="badge badge-neutral" style="font-size: 11px; padding: 2px 8px;">
                                        {{ $prod->category->name }}
                                    </span>
                                </div>
                            </td>
                            @if(Auth::user()->isAdmin())
                                <td style="color: var(--color-text-muted); font-size: 13.5px;">
                                    Rp {{ number_format($prod->buy_price, 0, ',', '.') }}
                                </td>
                            @endif
                            <td>
                                <strong style="color: var(--color-muted-olive-dark); font-size: 14.5px;">
                                    Rp {{ number_format($prod->sell_price, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                @if($prod->stock <= 0)
                                    <span class="badge badge-danger">
                                        ⛔ Kosong (0)
                                    </span>
                                @elseif($prod->isLowStock())
                                    <span class="badge badge-warning" title="Stok di bawah atau sama dengan batas minimum!">
                                        ⚠️ {{ $prod->stock }} tangkai
                                    </span>
                                @else
                                    <span class="badge badge-success">
                                        ✓ {{ $prod->stock }} tangkai
                                    </span>
                                @endif
                            </td>
                            <td style="color: var(--color-text-muted);">
                                {{ $prod->min_stock }} tangkai
                            </td>
                            <td>
                                @if($prod->is_active)
                                    <span style="color: var(--color-success); font-weight: 600; font-size: 13px;">● Aktif</span>
                                @else
                                    <span style="color: var(--color-text-muted); font-size: 13px;">○ Non-aktif</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('products.edit', $prod->id) }}" class="btn btn-outline btn-sm" style="padding: 4px 10px;">
                                    ✏️ Edit
                                </a>

                                @if(Auth::user()->isAdmin())
                                    @php
                                        $hasTx = ($prod->purchase_items_count ?? $prod->purchaseItems()->count()) + ($prod->sale_items_count ?? $prod->saleItems()->count()) > 0;
                                    @endphp
                                    <form action="{{ route('products.destroy', $prod->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 10px;" {{ $hasTx ? 'title="Produk telah memiliki riwayat transaksi" disabled style=opacity:0.4;cursor:not-allowed;' : '' }}>
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->isAdmin() ? 8 : 7 }}" style="text-align: center; padding: 40px; color: var(--color-text-muted);">
                                Tidak ditemukan data produk bunga yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="padding: 16px 24px; border-top: 1px solid var(--color-border);">
        {{ $products->links() }}
    </div>
</div>
@endsection
