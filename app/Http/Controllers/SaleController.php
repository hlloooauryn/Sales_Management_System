<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Tampilkan riwayat transaksi penjualan kasir
     */
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'items.product'])->latest('sale_date')->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        $sales = $query->paginate(10)->withQueryString();

        return view('admin.sales.index', compact('sales'));
    }

    /**
     * Tampilkan formulir transaksi penjualan kasir / POS
     */
    public function create()
    {
        // Hanya tampilkan produk aktif yang memiliki stok > 0
        $products = Product::where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get();

        if ($products->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Tidak ada produk bunga yang memiliki stok tersedia untuk dijual. Silakan catat pembelian / restock terlebih dahulu.');
        }

        // Generate nomor invoice penjualan otomatis: INV-YYYYMM-XXXX
        $datePrefix = 'INV-' . date('Ym') . '-';
        $maxSeq = Sale::where('invoice_number', 'like', "{$datePrefix}%")
            ->pluck('invoice_number')
            ->map(fn($inv) => (int) substr($inv, -4))
            ->max() ?? 0;
        $nextNumber = $maxSeq + 1;
        $defaultInvoice = $datePrefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('admin.sales.create', compact('products', 'defaultInvoice'));
    }

    /**
     * Simpan transaksi penjualan kasir dengan validasi stok ketat & atomik
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:sales,invoice_number',
            'sale_date' => 'required|date',
            'customer_name' => 'nullable|string|max:100',
            'payment_method' => 'required|string|in:Cash,Transfer,QRIS,Debit Card',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'invoice_number.required' => 'Nomor invoice wajib diisi.',
            'invoice_number.unique' => 'Nomor invoice ini sudah digunakan.',
            'items.required' => 'Minimal pilih 1 item bunga yang dibeli pelanggan.',
            'items.*.quantity.min' => 'Jumlah pembelian bunga minimal 1 tangkai.',
            'payment_method.required' => 'Pilih metode pembayaran pelanggan.',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $itemsToProcess = [];

            // 1. Verifikasi stok dan hitung total server-side
            foreach ($validated['items'] as $itemData) {
                $qty = (int) $itemData['quantity'];
                
                // Kunci baris produk database (lockForUpdate) untuk mencegah race condition
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);

                // Pengecekan stok fisik: STOK TIDAK BOLEH NEGATIF
                if ($product->stock < $qty) {
                    throw new \Exception("Stok untuk bunga '{$product->name}' tidak mencukupi! Stok fisik tersedia hanya {$product->stock} tangkai, namun Anda meminta {$qty} tangkai.");
                }

                $unitPrice = (float) $product->sell_price;
                $subtotal = $qty * $unitPrice;
                $totalAmount += $subtotal;

                $itemsToProcess[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            }

            // 2. Simpan header penjualan
            $sale = Sale::create([
                'invoice_number' => $validated['invoice_number'],
                'user_id' => Auth::id(),
                'sale_date' => $validated['sale_date'],
                'customer_name' => $validated['customer_name'] ?: 'Pelanggan Umum',
                'payment_method' => $validated['payment_method'],
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            // 3. Simpan item penjualan dan kurangi stok fisik
            foreach ($itemsToProcess as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Kurangi stok fisik produk
                $item['product']->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Transaksi penjualan kasir ' . $sale->invoice_number . ' berhasil! Stok fisik otomatis berkurang.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan nota kasir / struk belanja
     */
    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product.category']);
        return view('admin.sales.show', compact('sale'));
    }

    /**
     * Batalkan transaksi penjualan & kembalikan stok secara aman (Khusus Admin/Owner)
     */
    public function cancel(Sale $sale)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin/Owner yang dapat membatalkan transaksi penjualan.');
        }

        try {
            DB::beginTransaction();

            // Kembalikan seluruh stok fisik produk
            foreach ($sale->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            $invNumber = $sale->invoice_number;
            $sale->delete(); // Otomatis cascade hapus sale_items karena FK cascade

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', "Transaksi penjualan {$invNumber} telah berhasil dibatalkan. Seluruh stok fisik bunga telah dikembalikan ke sistem secara aman.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
