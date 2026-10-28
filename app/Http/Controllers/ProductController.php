<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Tampilkan data seluruh produk dengan pencarian dan filter stok
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category');

        // Filter kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Pencarian nama produk
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter status stok
        if ($request->stock_status === 'low') {
            $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
        } elseif ($request->stock_status === 'out') {
            $query->where('stock', '<=', 0);
        } elseif ($request->stock_status === 'available') {
            $query->whereColumn('stock', '>', 'min_stock');
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Form tambah produk baru
     */
    public function create()
    {
        $categories = Category::all();
        if ($categories->isEmpty()) {
            return redirect()->route('categories.index')->with('error', 'Silakan buat kategori bunga terlebih dahulu sebelum menambahkan produk.');
        }

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Simpan produk baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150|unique:products,name',
            'description' => 'nullable|string|max:1000',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0|gte:buy_price',
            'initial_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'is_active' => 'boolean',
        ], [
            'category_id.required' => 'Pilih kategori bunga.',
            'name.required' => 'Nama produk bunga wajib diisi.',
            'name.unique' => 'Nama produk ini sudah digunakan.',
            'buy_price.required' => 'Harga beli wajib diisi.',
            'sell_price.gte' => 'Harga jual tidak boleh lebih rendah dari harga beli.',
            'initial_stock.required' => 'Stok awal wajib diisi (minimal 0).',
            'min_stock.required' => 'Batas stok minimum wajib diisi.',
            'image.image' => 'File foto bunga harus berupa gambar yang valid.',
            'image.mimes' => 'Format foto yang didukung: JPG, JPEG, PNG, WEBP, atau SVG.',
            'image.max' => 'Ukuran foto maksimal 3MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'buy_price' => $validated['buy_price'],
            'sell_price' => $validated['sell_price'],
            'stock' => $validated['initial_stock'],
            'min_stock' => $validated['min_stock'],
            'image' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('products.index')->with('success', 'Produk bunga "' . $product->name . '" berhasil ditambahkan.');
    }

    /**
     * Form edit produk
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update data produk
     */
    public function update(Request $request, Product $product)
    {
        $user = Auth::user();

        // Validasi umum untuk Staff dan Admin
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150|unique:products,name,' . $product->id,
            'description' => 'nullable|string|max:1000',
            'sell_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'is_active' => 'boolean',
        ];

        // Khusus Admin diperbolehkan mengoreksi harga beli langsung
        if ($user->isAdmin()) {
            $rules['buy_price'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama produk bunga wajib diisi.',
            'name.unique' => 'Nama produk ini sudah ada.',
            'sell_price.required' => 'Harga jual wajib diisi.',
            'min_stock.required' => 'Stok minimum wajib diisi.',
            'image.image' => 'File foto bunga harus berupa gambar yang valid.',
            'image.mimes' => 'Format foto yang didukung: JPG, JPEG, PNG, WEBP, atau SVG.',
            'image.max' => 'Ukuran foto maksimal 3MB.',
        ]);

        $updateData = [
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'sell_price' => $validated['sell_price'],
            'min_stock' => $validated['min_stock'],
            'is_active' => $request->has('is_active'),
        ];

        if ($user->isAdmin() && isset($validated['buy_price'])) {
            $updateData['buy_price'] = $validated['buy_price'];
        }

        // Jika ada unggahan foto baru, simpan dan hapus foto lama (jika dari storage)
        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $updateData['image'] = $request->file('image')->store('products', 'public');
        }
        // Jika tidak memilih file foto baru, $product->image lama tetap dipertahankan

        $product->update($updateData);

        return redirect()->route('products.index')->with('success', 'Data produk "' . $product->name . '" berhasil diperbarui.');
    }

    /**
     * Hapus produk (Khusus Admin/Owner dengan Soft Deletes aman)
     */
    public function destroy(Product $product)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin/Owner yang memiliki kewenangan menghapus produk.');
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk bunga "' . $productName . '" berhasil dihapus dari sistem.');
    }
}
