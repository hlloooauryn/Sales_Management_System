<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
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
| Web Routes - Bloom Moment
|--------------------------------------------------------------------------
|
| Terbagi menjadi 2 bagian sesuai kebutuhan brief:
| 1. Website Publik (Tanpa Login)
| 2. Backoffice Internal (Wajib Login & Berbasis Peran Staff / Admin)
|
*/

// ==========================================
// 1. WEBSITE PUBLIK (TANPA LOGIN)
// ==========================================
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/catalog', [PublicController::class, 'catalog'])->name('catalog');
Route::get('/product/{slug}', [PublicController::class, 'productDetail'])->name('product.detail');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');

// ==========================================
// 2. AUTENTIKASI PENGGUNA INTERNAL
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 3. BACKOFFICE INTERNAL (WAJIB LOGIN)
// ==========================================
Route::middleware(['auth'])->prefix('admin')->group(function () {
    
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

    // Transaksi Penjualan / Barang Keluar Kasir (Staff & Admin)
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

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
