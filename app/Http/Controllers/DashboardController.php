<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display dashboard overview.
     */
    public function index(): View
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();

        // Calculate total inventory financial value
        $totalValue = (float) Product::select(DB::raw('SUM(price * stock) as total_value'))
            ->value('total_value') ?? 0;

        // Products with stock at or below minimum threshold
        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock', 'asc')
            ->get();

        $countStockIn = StockMovement::where('type', 'in')->count();
        $countStockOut = StockMovement::where('type', 'out')->count();

        // Latest stock movements
        $recentMovements = StockMovement::with('product')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'totalValue',
            'lowStockProducts',
            'countStockIn',
            'countStockOut',
            'recentMovements'
        ));
    }
}
