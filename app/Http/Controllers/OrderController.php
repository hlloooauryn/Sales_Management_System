<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Tampilkan daftar seluruh pesanan online milik user/pelanggan yang sedang login
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Sale::with(['items.product'])
            ->where('user_id', $user->id)
            ->where('order_type', 'online')
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(8)->withQueryString();

        return view('public.orders.index', compact('orders'));
    }

    /**
     * Tampilkan detail pesanan & instruksi pembayaran interaktif
     */
    public function show($invoice)
    {
        $sale = Sale::with(['items.product.category', 'user'])
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        // Keamanan: Hanya pemilik pesanan atau Staf/Admin yang boleh membuka
        if ($sale->user_id !== Auth::id() && !Auth::user()->isAdmin() && !Auth::user()->isStaff()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki akses untuk melihat pesanan ini.');
        }

        // Data rekening dan panduan pembayaran
        $paymentInfo = $this->getPaymentGuide($sale->payment_method);

        return view('public.orders.show', compact('sale', 'paymentInfo'));
    }

    /**
     * Unggah bukti transfer / pembayaran dari pelanggan
     */
    public function uploadProof(Request $request, $invoice)
    {
        $sale = Sale::where('invoice_number', $invoice)->firstOrFail();

        if ($sale->user_id !== Auth::id() && !Auth::user()->isAdmin() && !Auth::user()->isStaff()) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ], [
            'payment_proof.required' => 'Pilih file foto atau tangkapan layar bukti transfer.',
            'payment_proof.image' => 'File harus berupa gambar (JPG, PNG, WebP).',
            'payment_proof.max' => 'Ukuran gambar maksimal 4 MB.',
        ]);

        if ($request->hasFile('payment_proof')) {
            // Hapus bukti lama jika ada
            if ($sale->payment_proof && Storage::disk('public')->exists($sale->payment_proof)) {
                Storage::disk('public')->delete($sale->payment_proof);
            }

            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            
            $sale->update([
                'payment_proof' => $path,
                'status' => 'paid', // Status berubah menjadi sudah bayar / menunggu verifikasi
            ]);

            return back()->with('success', '🎉 Bukti pembayaran berhasil diunggah! Florist kami akan segera memverifikasi dan menyiapkan rangkaian bunga Anda.');
        }

        return back()->with('error', 'Gagal mengunggah file bukti pembayaran.');
    }

    /**
     * Cetak struk pesanan online
     */
    public function printReceipt($invoice)
    {
        $sale = Sale::with(['items.product.category', 'user'])
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        if ($sale->user_id !== Auth::id() && !Auth::user()->isAdmin() && !Auth::user()->isStaff()) {
            abort(403);
        }

        return view('public.orders.receipt', compact('sale'));
    }

    /**
     * Panduan instruksi pembayaran berdasarkan metode bayar
     */
    private function getPaymentGuide(string $method): array
    {
        $methodUpper = strtoupper($method);

        // 1. Virtual Account (Prioritas sebelum pencocokan nama bank manual)
        if (str_contains($methodUpper, 'VIRTUAL') || str_contains($methodUpper, 'VA')) {
            $bankName = 'Virtual Account';
            $prefix = '80777';
            if (str_contains($methodUpper, 'MANDIRI')) {
                $bankName = 'Mandiri Virtual Account';
                $prefix = '89508';
            } elseif (str_contains($methodUpper, 'BNI')) {
                $bankName = 'BNI Virtual Account';
                $prefix = '98812';
            } elseif (str_contains($methodUpper, 'BRI')) {
                $bankName = 'BRI Virtual Account (BRIVA)';
                $prefix = '12800';
            } elseif (str_contains($methodUpper, 'BCA')) {
                $bankName = 'BCA Virtual Account';
                $prefix = '80777';
            }

            return [
                'type' => 'va',
                'title' => $bankName,
                'va_name' => $bankName,
                'va_number' => $prefix . rand(10000000, 99999999),
                'merchant' => 'Bloom Moment Florist',
                'badge' => 'Virtual Account',
                'instructions' => [
                    'Buka aplikasi m-Banking atau kunjungi mesin ATM terdekat.',
                    'Pilih menu Pembayaran / Bayar Tagihan > Virtual Account.',
                    'Masukkan Nomor Virtual Account yang tertera di atas.',
                    'Pastikan nama merchant tertera "Bloom Moment" dan nominal sesuai total tagihan.',
                    'Konfirmasi pembayaran dan masukkan PIN transaksi Anda.',
                    'Sistem akan memverifikasi pembayaran Anda secara instan dalam beberapa saat.',
                ]
            ];
        }

        // 2. Transfer Bank Manual
        if (str_contains($methodUpper, 'BCA')) {
            return [
                'type' => 'bank_transfer',
                'bank_name' => 'Bank Central Asia (BCA)',
                'account_number' => '8820-1234-5678',
                'account_holder' => 'Bloom Moment Florist',
                'badge' => 'BCA Transfer',
                'instructions' => [
                    'Buka aplikasi BCA Mobile, KlikBCA, atau kunjungi ATM BCA terdekat.',
                    'Pilih menu Transfer > Antar Rekening BCA.',
                    'Masukkan Nomor Rekening: 8820-1234-5678 a/n Bloom Moment Florist.',
                    'Pastikan nominal transfer sesuai dengan total tagihan pesanan Anda.',
                    'Simpan bukti transfer dan unggah foto/screenshot pada formulir di bawah ini.',
                ]
            ];
        } elseif (str_contains($methodUpper, 'MANDIRI')) {
            return [
                'type' => 'bank_transfer',
                'bank_name' => 'Bank Mandiri',
                'account_number' => '137-00-9876543-2',
                'account_holder' => 'PT Bloom Moment Indonesia',
                'badge' => 'Mandiri Transfer',
                'instructions' => [
                    'Buka aplikasi Livin\' by Mandiri atau ATM Mandiri.',
                    'Pilih menu Transfer Rupiah > Rekening Mandiri.',
                    'Masukkan Nomor Rekening: 137-00-9876543-2 a/n PT Bloom Moment Indonesia.',
                    'Masukkan nominal tepat sesuai jumlah total pesanan.',
                    'Foto atau screenshot bukti pembayaran lalu unggah pada form di bawah.',
                ]
            ];
        } elseif (str_contains($methodUpper, 'BNI')) {
            return [
                'type' => 'bank_transfer',
                'bank_name' => 'Bank BNI',
                'account_number' => '0987-654-321',
                'account_holder' => 'Bloom Moment Florist',
                'badge' => 'BNI Transfer',
                'instructions' => [
                    'Buka BNI Mobile Banking atau ATM BNI.',
                    'Pilih menu Transfer > Antar BNI.',
                    'Masukkan Nomor Rekening: 0987-654-321 a/n Bloom Moment Florist.',
                    'Ketikkan nominal yang sesuai dengan total belanja Anda.',
                    'Unggah bukti transfer Anda agar tim florist dapat langsung memproses bunga.',
                ]
            ];
        } elseif (str_contains($methodUpper, 'BRI')) {
            return [
                'type' => 'bank_transfer',
                'bank_name' => 'Bank BRI',
                'account_number' => '0341-01-001234-50-8',
                'account_holder' => 'Bloom Moment Florist',
                'badge' => 'BRI Transfer',
                'instructions' => [
                    'Buka aplikasi BRImo atau mesin ATM BRI.',
                    'Pilih menu Transfer > Rekening BRI.',
                    'Masukkan Nomor Rekening: 0341-01-001234-50-8 a/n Bloom Moment Florist.',
                    'Kirim sesuai nominal pesanan bunga.',
                    'Unggah bukti transaksi pada formulir konfirmasi di halaman ini.',
                ]
            ];
        } elseif (str_contains($methodUpper, 'BSI')) {
            return [
                'type' => 'bank_transfer',
                'bank_name' => 'Bank Syariah Indonesia (BSI)',
                'account_number' => '7123-456-789',
                'account_holder' => 'Bloom Moment Florist',
                'badge' => 'BSI Syariah',
                'instructions' => [
                    'Buka aplikasi BSI Mobile atau mesin ATM BSI.',
                    'Pilih menu Transfer > Antar Rekening BSI.',
                    'Masukkan Nomor Rekening: 7123-456-789 a/n Bloom Moment Florist.',
                    'Kirim sesuai nominal tagihan bunga yang tertera.',
                    'Unggah bukti transaksi pada formulir konfirmasi di bawah.',
                ]
            ];
        }

        // 3. E-Wallet (GoPay, OVO, DANA, ShopeePay)
        if (str_contains($methodUpper, 'GOPAY')) {
            return [
                'type' => 'ewallet',
                'wallet_name' => 'GoPay',
                'phone_number' => '0812-9988-7766',
                'account_holder' => 'Bloom Moment Florist',
                'badge' => 'GoPay Instan',
                'icon' => '🟢',
                'instructions' => [
                    'Buka aplikasi Gojek di ponsel Anda.',
                    'Pilih menu Bayar / Kirim di bagian saldo GoPay.',
                    'Ketik nomor HP tujuan: 0812-9988-7766 (Bloom Moment Florist).',
                    'Masukkan nominal tepat sesuai total tagihan pesanan bunga.',
                    'Konfirmasi pengiriman dan masukkan 6 digit PIN GoPay Anda.',
                    'Tangkapan layar (screenshot) bukti transfer sukses lalu unggah di formulir di bawah.',
                ]
            ];
        } elseif (str_contains($methodUpper, 'OVO')) {
            return [
                'type' => 'ewallet',
                'wallet_name' => 'OVO Cash',
                'phone_number' => '0812-9988-7766',
                'account_holder' => 'Bloom Moment Florist',
                'badge' => 'OVO Transfer',
                'icon' => '🟣',
                'instructions' => [
                    'Buka aplikasi OVO di smartphone Anda.',
                    'Pilih menu Transfer > Ke Sesama OVO.',
                    'Masukkan nomor ponsel: 0812-9988-7766 a/n Bloom Moment Florist.',
                    'Masukkan nominal transfer sesuai total tagihan bunga.',
                    'Tekan Lanjutkan dan masukkan Security Code OVO Anda.',
                    'Simpan bukti transfer dan unggah pada formulir konfirmasi di bawah ini.',
                ]
            ];
        } elseif (str_contains($methodUpper, 'DANA')) {
            return [
                'type' => 'ewallet',
                'wallet_name' => 'DANA Dompet Digital',
                'phone_number' => '0812-9988-7766',
                'account_holder' => 'Bloom Moment Florist',
                'badge' => 'DANA Transfer',
                'icon' => '🔵',
                'instructions' => [
                    'Buka aplikasi DANA di smartphone Anda.',
                    'Pilih menu Kirim / Send > Kirim ke Nomor Telepon.',
                    'Masukkan nomor: 0812-9988-7766 (Bloom Moment Florist).',
                    'Masukkan nominal pembayaran pas sesuai total tagihan dan pilih Bayar.',
                    'Masukkan PIN DANA Anda.',
                    'Unggah screenshot bukti pembayaran sukses pada formulir di bawah.',
                ]
            ];
        } elseif (str_contains($methodUpper, 'SHOPEEPAY') || str_contains($methodUpper, 'SHOPEE')) {
            return [
                'type' => 'ewallet',
                'wallet_name' => 'ShopeePay',
                'phone_number' => '0812-9988-7766',
                'account_holder' => 'Bloom Moment Florist',
                'badge' => 'ShopeePay',
                'icon' => '🟠',
                'instructions' => [
                    'Buka aplikasi Shopee dan masuk ke saldo ShopeePay.',
                    'Pilih menu Transfer > Transfer ke Kontak / Nomor HP.',
                    'Masukkan nomor HP: 0812-9988-7766 (Bloom Moment Florist).',
                    'Masukkan nominal transfer pas sesuai total tagihan pesanan.',
                    'Konfirmasi dan verifikasi dengan PIN ShopeePay atau Sidik Jari.',
                    'Unggah screenshot struk sukses transfer pada formulir konfirmasi di bawah.',
                ]
            ];
        }

        // 4. QRIS Real-Time Scan
        if (str_contains($methodUpper, 'QRIS')) {
            return [
                'type' => 'qris',
                'title' => 'QRIS Real-Time Scan',
                'nmid' => 'ID1020038847291',
                'merchant' => 'BLOOM MOMENT STORE',
                'badge' => 'QRIS Nasional',
                'instructions' => [
                    'Buka aplikasi E-Wallet (GoPay, OVO, DANA, ShopeePay, LinkAja) atau Mobile Banking (BCA, Mandiri, BRI, BNI).',
                    'Pilih menu SCAN / BAYAR QRIS.',
                    'Arahkan kamera ke kode QRIS di bawah ini.',
                    'Periksa nama Merchant: BLOOM MOMENT STORE.',
                    'Masukkan nominal tepat sesuai total tagihan lalu masukkan PIN Anda.',
                    'Setelah berhasil, unggah screenshot bukti pembayaran.',
                ]
            ];
        }

        // 5. Gerai Retail Minimarket (Indomaret / Alfamart)
        if (str_contains($methodUpper, 'INDOMARET') || str_contains($methodUpper, 'ALFAMART') || str_contains($methodUpper, 'RETAIL')) {
            $isAlfamart = str_contains($methodUpper, 'ALFA');
            $retailName = $isAlfamart ? 'Alfamart / Alfamidi' : 'Indomaret / Ceriamart';
            $codePrefix = $isAlfamart ? 'BLM-ALFA-' : 'BLM-INDO-';

            return [
                'type' => 'retail',
                'retail_name' => $retailName,
                'payment_code' => $codePrefix . rand(100000, 999999),
                'merchant' => 'Bloom Moment Florist',
                'badge' => $isAlfamart ? 'Alfamart' : 'Indomaret',
                'instructions' => [
                    "Kunjungi kasir gerai {$retailName} terdekat di kota Anda.",
                    "Sampaikan ke kasir bahwa Anda ingin membayar transaksi online *Merchant Bloom Moment*.",
                    "Tunjukkan Kode Pembayaran yang tertera di atas kepada kasir.",
                    "Kasir akan memverifikasi nama pemesan dan nominal tagihan.",
                    "Lakukan pembayaran tunai atau debit kepada kasir minimarket.",
                    "Simpan struk fisik yang diberikan, foto, lalu unggah pada formulir bukti di bawah.",
                ]
            ];
        }

        // 6. Default: Cash on Delivery (COD)
        return [
            'type' => 'cod',
            'title' => 'Cash on Delivery (Bayar di Tempat)',
            'badge' => 'COD Tunai',
            'instructions' => [
                'Pesanan bunga Anda langsung dipersiapkan dan dirangkai oleh tim florist kami.',
                'Kurir kami akan menghubungi nomor WhatsApp Anda sebelum pengantaran ke alamat tujuan.',
                'Siapkan uang tunai pas sejumlah total tagihan saat kurir tiba.',
                'Tidak perlu mengunggah bukti transfer untuk pesanan dengan metode COD.',
            ]
        ];
    }
}
