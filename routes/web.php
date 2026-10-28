<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Bloom Moment Florist
|--------------------------------------------------------------------------
|
| 1. Website Publik (Katalog, Detail, Keranjang Belanja)
| 2. Autentikasi Pengguna (User / Pelanggan & Petugas Backoffice)
| 3. Pembelian Online & Pembayaran (Role User / Pelanggan Wajib Login)
| 4. Backoffice Internal (Staff & Admin Wajib Login)
|
*/

// ==========================================
// 1. WEBSITE PUBLIK & KATALOG BUNGA
// ==========================================
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/catalog', [PublicController::class, 'catalog'])->name('catalog');
Route::get('/product/{slug}', [PublicController::class, 'productDetail'])->name('product.detail');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');

// ==========================================
// 2. KERANJANG BELANJA (CART)
// ==========================================
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// ==========================================
// 3. AUTENTIKASI PENGGUNA (LOGIN & REGISTER)
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 4. PEMBELIAN ONLINE & PESANAN (ROLE USER WAJIB LOGIN)
// ==========================================
Route::middleware(['auth'])->group(function () {
    // Checkout & Pembayaran
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Riwayat & Detail Pesanan Pelanggan
    Route::get('/my-orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{invoice}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{invoice}/proof', [OrderController::class, 'uploadProof'])->name('orders.uploadProof');
    Route::get('/orders/{invoice}/receipt', [OrderController::class, 'printReceipt'])->name('orders.receipt');
});

// ==========================================
// 5. BACKOFFICE INTERNAL (STAFF & ADMIN)
// ==========================================
Route::middleware(['auth', 'role:admin,staff'])->prefix('admin')->group(function () {
    
    // Dashboard Operasional & Metrik Bisnis
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Produk & Stok (Staff & Admin)
    Route::resource('products', ProductController::class);

    // Manajemen Pemasok / Supplier (Staff: Lihat, Admin: CRUD)
    Route::resource('suppliers', SupplierController::class);

    // Transaksi Pembelian / Restok Barang Masuk (Staff & Admin)
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

    // Transaksi Penjualan / Barang Keluar Kasir & Online (Staff & Admin)
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::post('/sales/{sale}/status', [SaleController::class, 'updateStatus'])->name('sales.status');

    // ==========================================
    // KHUSUS ADMIN / OWNER (ROLE: ADMIN)
    // ==========================================
    Route::middleware(['role:admin'])->group(function () {
        // Pembatalan Transaksi Penjualan Aman (Kembalikan Stok)
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');

        // CRUD Kategori Bunga
        Route::resource('categories', CategoryController::class);

        // CRUD Pengguna & Peran (Role)
        Route::resource('users', UserController::class);

        // Laporan Rekapitulasi Penjualan & Pembelian
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        // Pengaturan Toko & Logo (Hanya Admin / Owner)
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::delete('/settings/logo', [SettingController::class, 'destroyLogo'])->name('settings.destroyLogo');
    });

});
