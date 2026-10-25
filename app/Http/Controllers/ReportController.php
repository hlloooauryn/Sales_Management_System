<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Tampilkan laporan berkala penjualan dan pembelian dengan filter tanggal dan pencarian
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $search = $request->input('search');

        // Query Penjualan dalam rentang tanggal
        $salesQuery = Sale::with(['user', 'items.product'])
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);

        if ($request->filled('search')) {
            $salesQuery->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items.product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Summary statistik penjualan dihitung dari seluruh hasil filter
        $summarySales = (clone $salesQuery)->get();
        $totalSales = $summarySales->sum('total_amount');
        $totalItemsSold = $summarySales->sum(function ($s) {
            return $s->items->sum('quantity');
        });
        $totalSalesCount = $summarySales->count();

        // Paginate hasil penjualan dengan query string dipertahankan
        $sales = $salesQuery->latest('sale_date')->latest('id')->paginate(10)->withQueryString();

        // Query Pembelian dalam rentang tanggal
        $purchasesQuery = Purchase::with(['supplier', 'user', 'items.product'])
            ->whereDate('purchase_date', '>=', $startDate)
            ->whereDate('purchase_date', '<=', $endDate);

        if ($request->filled('search')) {
            $purchasesQuery->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items.product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $summaryPurchases = (clone $purchasesQuery)->get();
        $totalPurchases = $summaryPurchases->sum('total_amount');
        $totalItemsBought = $summaryPurchases->sum(function ($p) {
            return $p->items->sum('quantity');
        });
        $totalPurchasesCount = $summaryPurchases->count();

        $purchases = $purchasesQuery->latest('purchase_date')->latest('id')->get();

        return view('admin.reports.index', compact(
            'sales',
            'purchases',
            'startDate',
            'endDate',
            'search',
            'totalSales',
            'totalItemsSold',
            'totalSalesCount',
            'totalPurchases',
            'totalItemsBought',
            'totalPurchasesCount'
        ));
    }
}
