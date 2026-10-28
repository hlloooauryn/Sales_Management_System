<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Tampilkan halaman keranjang belanja bunga
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $totalAmount = 0;
        $totalItems = 0;
        $cleanCart = [];

        foreach ($cart as $id => $item) {
            $product = Product::find($item['id'] ?? $id);
            if ($product && $product->is_active) {
                $item['stock'] = $product->stock;
                $cleanCart[$id] = $item;
                $totalAmount += $item['price'] * $item['quantity'];
                $totalItems += $item['quantity'];
            }
        }

        if (count($cleanCart) !== count($cart)) {
            session()->put('cart', $cleanCart);
            $cart = $cleanCart;
        }

        return view('public.cart', compact('cart', 'totalAmount', 'totalItems'));
    }

    /**
     * Tambahkan bunga ke dalam keranjang
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $qtyToAdd = (int) ($request->input('quantity', 1));

        if (!$product->is_active || $product->stock <= 0) {
            return back()->with('error', "Maaf, stok bunga '{$product->name}' sedang kosong.");
        }

        $cart = session()->get('cart', []);
        $productId = $product->id;
        $currentCartQty = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
        $newQty = $currentCartQty + $qtyToAdd;

        if ($newQty > $product->stock) {
            return back()->with('error', "Stok fisik bunga '{$product->name}' yang tersedia hanya {$product->stock} tangkai (di keranjang sudah ada {$currentCartQty}).");
        }

        $cart[$productId] = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (float) $product->sell_price,
            'quantity' => $newQty,
            'image_url' => $product->image_url,
            'category_name' => $product->category ? $product->category->name : 'Bunga Segar',
            'stock' => $product->stock,
        ];

        session()->put('cart', $cart);

        // Jika tombol "Beli Sekarang" ditekan, langsung arahkan ke checkout
        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', "🌸 '{$product->name}' ({$qtyToAdd} tangkai) berhasil dimasukkan ke keranjang belanja!");
    }

    /**
     * Perbarui jumlah item di keranjang
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->product_id;
        $qty = (int) $request->quantity;
        $product = Product::findOrFail($productId);

        if ($qty > $product->stock) {
            return back()->with('error', "Jumlah melebihi stok yang tersedia ({$product->stock} tangkai).");
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $qty;
            session()->put('cart', $cart);
            return back()->with('success', 'Jumlah pesanan bunga berhasil diperbarui.');
        }

        return back()->with('error', 'Produk tidak ditemukan di keranjang Anda.');
    }

    /**
     * Hapus satu item dari keranjang
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
        ]);

        $productId = $request->product_id;
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $itemName = $cart[$productId]['name'];
            unset($cart[$productId]);
            session()->put('cart', $cart);
            return back()->with('success', "'{$itemName}' telah dihapus dari keranjang belanja.");
        }

        return back()->with('error', 'Item tidak ditemukan di keranjang.');
    }

    /**
     * Kosongkan seluruh keranjang
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('catalog')->with('success', 'Keranjang belanja telah dikosongkan.');
    }
}
