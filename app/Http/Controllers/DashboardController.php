<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan ringkasan dashboard operasional dan metrik eksekutif
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Statistik Kunci
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();

        // Metrik Penjualan & Pembelian
        $totalSalesCount = Sale::count();
        $totalSalesAmount = Sale::sum('total_amount');

        $totalPurchasesCount = Purchase::count();
        $totalPurchasesAmount = Purchase::sum('total_amount');

        // Metrik Stok
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock', 'asc')
            ->get();

        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        // 2. Transaksi Terkini
        $recentSales = Sale::with(['user', 'items.product'])->latest('sale_date')->latest('id')->take(5)->get();
        $recentPurchases = Purchase::with(['supplier', 'user'])->latest('purchase_date')->latest('id')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'totalSalesCount',
            'totalSalesAmount',
            'totalPurchasesCount',
            'totalPurchasesAmount',
            'lowStockProducts',
            'outOfStockCount',
            'recentSales',
            'recentPurchases'
        ));
    }
}
