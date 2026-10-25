<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    /**
     * Tampilkan riwayat transaksi pembelian / barang masuk (Staff & Admin)
     */
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'user', 'items.product'])->latest('purchase_date')->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }

        $purchases = $query->paginate(10)->withQueryString();

        return view('admin.purchases.index', compact('purchases'));
    }

    /**
     * Tampilkan formulir pencatatan barang masuk (pembelian baru)
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        if ($suppliers->isEmpty()) {
            return redirect()->route('suppliers.index')->with('error', 'Silakan daftarkan data pemasok terlebih dahulu.');
        }

        if ($products->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Silakan buat data produk bunga terlebih dahulu.');
        }

        // Generate nomor referensi pembelian otomatis: PB-IN-202610-XXXX
        $datePrefix = 'PB-IN-' . date('Ym') . '-';
        $maxSeq = Purchase::where('reference_number', 'like', "{$datePrefix}%")
            ->pluck('reference_number')
            ->map(fn($ref) => (int) substr($ref, -4))
            ->max() ?? 0;
        $nextNumber = $maxSeq + 1;
        $defaultRef = $datePrefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('admin.purchases.create', compact('suppliers', 'products', 'defaultRef'));
    }

    /**
     * Simpan transaksi pembelian barang masuk dengan DB::transaction
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|string|max:50|unique:purchases,reference_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ], [
            'reference_number.required' => 'Nomor referensi pembelian wajib diisi.',
            'reference_number.unique' => 'Nomor referensi ini sudah ada di sistem.',
            'supplier_id.required' => 'Pilih pemasok / supplier bunga.',
            'items.required' => 'Minimal masukkan 1 item bunga yang dibeli.',
            'items.*.quantity.min' => 'Jumlah pembelian barang harus lebih besar dari 0.',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $itemsToInsert = [];

            // 1. Hitung total secara aman di sisi server dan siapkan data item
            foreach ($validated['items'] as $itemData) {
                $qty = (int) $itemData['quantity'];
                $cost = (float) $itemData['unit_cost'];
                $subtotal = $qty * $cost;
                $totalAmount += $subtotal;

                $itemsToInsert[] = [
                    'product_id' => $itemData['product_id'],
                    'quantity' => $qty,
                    'unit_cost' => $cost,
                    'subtotal' => $subtotal,
                ];
            }

            // 2. Simpan header pembelian
            $purchase = Purchase::create([
                'reference_number' => $validated['reference_number'],
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            // 3. Simpan rincian item & tambah stok produk secara atomik
            foreach ($itemsToInsert as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Kunci baris database dan tambahkan stok fisik produk
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $product->increment('stock', $item['quantity']);

                // Perbarui harga beli terbaru dari pemasok
                $product->update(['buy_price' => $item['unit_cost']]);
            }

            DB::commit();

            return redirect()->route('purchases.show', $purchase->id)
                ->with('success', 'Transaksi pembelian ' . $purchase->reference_number . ' berhasil disimpan! Stok produk otomatis bertambah.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses pembelian: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail nota transaksi pembelian
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);
        return view('admin.purchases.show', compact('purchase'));
    }
}
