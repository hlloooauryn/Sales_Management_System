<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Tampilkan formulir checkout & pemilihan metode pembayaran online
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('catalog')->with('error', 'Keranjang belanja Anda masih kosong. Silakan pilih bunga terlebih dahulu.');
        }

        $user = Auth::user();
        $totalAmount = 0;
        $totalItems = 0;

        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
            $totalItems += $item['quantity'];
        }

        // Daftar metode pembayaran resmi Bloom Moment
        $paymentMethods = [
            'transfer' => [
                'title' => 'Transfer Bank Otomatis / Manual',
                'description' => 'Transfer aman via m-Banking, ATM, atau Internet Banking dengan konfirmasi cepat',
                'icon' => '🏦',
                'options' => [
                    ['code' => 'Transfer BCA', 'name' => 'BCA (Bank Central Asia)', 'account' => '8820-1234-5678', 'holder' => 'Bloom Moment Florist', 'badge' => 'Terpopuler'],
                    ['code' => 'Transfer Mandiri', 'name' => 'Bank Mandiri', 'account' => '137-00-9876543-2', 'holder' => 'PT Bloom Moment Indonesia', 'badge' => null],
                    ['code' => 'Transfer BNI', 'name' => 'Bank BNI', 'account' => '0987-654-321', 'holder' => 'Bloom Moment Florist', 'badge' => null],
                    ['code' => 'Transfer BRI', 'name' => 'Bank BRI', 'account' => '0341-01-001234-50-8', 'holder' => 'Bloom Moment Florist', 'badge' => null],
                    ['code' => 'Transfer BSI', 'name' => 'BSI (Bank Syariah Indonesia)', 'account' => '7123-456-789', 'holder' => 'Bloom Moment Florist', 'badge' => 'Syariah'],
                ]
            ],
            'ewallet' => [
                'title' => 'Dompet Digital / E-Wallet',
                'description' => 'Transfer instan dan bebas biaya admin antar e-wallet resmi Bloom Moment',
                'icon' => '📱',
                'options' => [
                    ['code' => 'GoPay', 'name' => 'GoPay', 'account' => '0812-9988-7766', 'holder' => 'Bloom Moment Florist', 'badge' => 'Instan'],
                    ['code' => 'OVO', 'name' => 'OVO Cash', 'account' => '0812-9988-7766', 'holder' => 'Bloom Moment Florist', 'badge' => 'Bebas Admin'],
                    ['code' => 'DANA', 'name' => 'DANA Dompet Digital', 'account' => '0812-9988-7766', 'holder' => 'Bloom Moment Florist', 'badge' => 'Favorit'],
                    ['code' => 'ShopeePay', 'name' => 'ShopeePay', 'account' => '0812-9988-7766', 'holder' => 'Bloom Moment Florist', 'badge' => 'Promo'],
                ]
            ],
            'qris' => [
                'title' => 'QRIS Real-Time Scan (Semua Bank & Dompet Digital)',
                'description' => 'Scan 1 kode QR dari aplikasi m-Banking (BCA, Mandiri, BRI, BNI) atau E-Wallet apa saja',
                'icon' => '🔲',
                'options' => [
                    ['code' => 'QRIS', 'name' => 'QRIS Real-Time Scan (Semua Bank & E-Wallet)', 'account' => 'NMID: ID1020038847291', 'holder' => 'BLOOM MOMENT STORE', 'badge' => 'Scan Cepat']
                ]
            ],
            'va' => [
                'title' => 'Virtual Account Instan (VA)',
                'description' => 'Konfirmasi pembayaran instan 24 jam tanpa perlu unggah struk manual',
                'icon' => '⚡',
                'options' => [
                    ['code' => 'Virtual Account BCA', 'name' => 'BCA Virtual Account', 'account' => '80777' . rand(10000000, 99999999), 'holder' => 'Bloom Moment - ' . $user->name, 'badge' => 'Otomatis'],
                    ['code' => 'Virtual Account Mandiri', 'name' => 'Mandiri Virtual Account', 'account' => '89508' . rand(10000000, 99999999), 'holder' => 'Bloom Moment - ' . $user->name, 'badge' => 'Otomatis'],
                    ['code' => 'Virtual Account BNI', 'name' => 'BNI Virtual Account', 'account' => '98812' . rand(10000000, 99999999), 'holder' => 'Bloom Moment - ' . $user->name, 'badge' => 'Otomatis'],
                    ['code' => 'Virtual Account BRI', 'name' => 'BRI Virtual Account (BRIVA)', 'account' => '12800' . rand(10000000, 99999999), 'holder' => 'Bloom Moment - ' . $user->name, 'badge' => 'Otomatis'],
                ]
            ],
            'retail' => [
                'title' => 'Gerai Retail / Minimarket',
                'description' => 'Bayar tunai di kasir minimarket terdekat di seluruh Indonesia tanpa perlu rekening bank',
                'icon' => '🏪',
                'options' => [
                    ['code' => 'Indomaret', 'name' => 'Indomaret / Ceriamart', 'account' => 'Kode Bayar: BLM-' . rand(100000, 999999), 'holder' => 'Merchant: Bloom Moment', 'badge' => 'Kasir Tunai'],
                    ['code' => 'Alfamart', 'name' => 'Alfamart / Alfamidi', 'account' => 'Kode Bayar: BLM-' . rand(100000, 999999), 'holder' => 'Merchant: Bloom Moment', 'badge' => 'Kasir Tunai'],
                ]
            ],
            'cod' => [
                'title' => 'Cash on Delivery (COD)',
                'description' => 'Bayar tunai kepada kurir kami saat rangkaian bunga sampai di tujuan',
                'icon' => '💵',
                'options' => [
                    ['code' => 'COD', 'name' => 'COD / Bayar Tunai di Tempat ke Kurir', 'account' => 'Siapkan uang pas saat bunga tiba', 'holder' => 'Kurir Bloom Moment Express', 'badge' => 'Bayar di Tempat']
                ]
            ],
        ];

        return view('public.checkout', compact('cart', 'user', 'totalAmount', 'totalItems', 'paymentMethods'));
    }

    /**
     * Proses transaksi pembelian online dan kurangi stok fisik produk
     */
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('catalog')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:25',
            'shipping_address' => 'required|string|max:500',
            'delivery_date' => 'nullable|date',
            'payment_method' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500',
        ], [
            'recipient_name.required' => 'Nama penerima bunga wajib diisi.',
            'customer_phone.required' => 'Nomor WhatsApp / telepon wajib diisi untuk konfirmasi kurir.',
            'shipping_address.required' => 'Alamat lengkap pengiriman wajib diisi.',
            'payment_method.required' => 'Silakan pilih metode pembayaran yang diinginkan.',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $itemsToProcess = [];

            // 1. Verifikasi stok setiap item di keranjang dengan DB lock
            foreach ($cart as $item) {
                $qty = (int) $item['quantity'];
                $product = Product::lockForUpdate()->findOrFail($item['id']);

                if ($product->stock < $qty) {
                    throw new \Exception("Maaf, stok bunga '{$product->name}' tidak mencukupi! Tersisa hanya {$product->stock} tangkai.");
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

            // 2. Generate nomor invoice penjualan online unik: INV-ONL-YYYYMM-XXXX
            $datePrefix = 'INV-ONL-' . date('Ym') . '-';
            $maxSeq = Sale::where('invoice_number', 'like', "{$datePrefix}%")
                ->pluck('invoice_number')
                ->map(fn($inv) => (int) substr($inv, -4))
                ->max() ?? 0;
            $nextNumber = $maxSeq + 1;
            $invoiceNumber = $datePrefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Status awal transaksi online
            // Jika COD langsung 'processing' (siap dirangkai & dikirim kurir)
            // Jika Transfer/QRIS/VA berstatus 'pending' (menunggu pembayaran)
            $isCOD = str_contains(strtoupper($validated['payment_method']), 'COD');
            $initialStatus = $isCOD ? 'processing' : 'pending';

            $user = Auth::user();

            // 3. Simpan data penjualan
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $user->id,
                'sale_date' => now()->toDateString(),
                'delivery_date' => $validated['delivery_date'] ?? now()->toDateString(),
                'order_type' => 'online',
                'status' => $initialStatus,
                'customer_name' => $validated['recipient_name'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'payment_method' => $validated['payment_method'],
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            // 4. Simpan item detail penjualan & kurangi stok fisik produk
            foreach ($itemsToProcess as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Kurangi stok fisik bunga di inventaris
                $item['product']->decrement('stock', $item['quantity']);
            }

            // 5. Perbarui telepon dan alamat di akun pengguna jika belum ada
            if (empty($user->phone) || empty($user->address)) {
                $user->update([
                    'phone' => $user->phone ?: $validated['customer_phone'],
                    'address' => $user->address ?: $validated['shipping_address'],
                ]);
            }

            // 6. Kosongkan keranjang belanja
            session()->forget('cart');

            DB::commit();

            return redirect()->route('orders.show', $sale->invoice_number)
                ->with('success', '🎉 Pesanan online bunga berhasil dibuat! Silakan ikuti panduan pembayaran di bawah ini.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
