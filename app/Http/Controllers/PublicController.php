<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Halaman Beranda Publik
     */
    public function index()
    {
        $categories = Category::withCount('products')->get();
        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(6)
            ->get();

        return view('public.home', compact('categories', 'featuredProducts'));
    }

    /**
     * Halaman Katalog Bunga Lengkap (Pencarian & Filter)
     */
    public function catalog(Request $request)
    {
        $categories = Category::all();

        $query = Product::with('category')->where('is_active', true);

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Pencarian nama atau deskripsi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Urutan harga
        if ($request->sort === 'price_low') {
            $query->orderBy('sell_price', 'asc');
        } elseif ($request->sort === 'price_high') {
            $query->orderBy('sell_price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        return view('public.catalog', compact('products', 'categories'));
    }

    /**
     * Halaman Detail Produk Bunga
     */
    public function productDetail($slug)
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(3)
            ->get();

        return view('public.detail', compact('product', 'relatedProducts'));
    }

    /**
     * Halaman Tentang Kami
     */
    public function about()
    {
        return view('public.about');
    }

    /**
     * Halaman Kontak Toko Bunga
     */
    public function contact()
    {
        return view('public.contact');
    }
}
