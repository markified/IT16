<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockIn;
use App\Models\StockOutDetail;
use App\Models\StockOutOrder;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Security role should not access dashboard
        if (auth()->user()->isSecurity()) {
            return redirect()->route('security.index')
                ->with('error', 'Access denied. Security role does not have dashboard access.');
        }

        try {
            // Get total counts
            $totalProducts = Product::count();
            $totalSuppliers = Supplier::count();
            $totalCategories = Category::where('is_active', true)->count();
            $totalStockIn = StockIn::count();
            $totalStockOut = StockOutOrder::count();
            $totalStockOutIssued = StockOutOrder::where('status', 'issued')->count();

            // Stock status counts
            $lowStockItems = Product::whereRaw('quantity <= min_stock_level AND quantity > 0')->count();
            $outOfStockItems = Product::where('quantity', 0)->count();

            // Inventory value
            $totalInventoryValue = Product::where('status', '!=', 'retired')
                ->selectRaw('SUM(quantity * price_per_item) as total')
                ->value('total') ?? 0;

            $totalCostValue = Product::where('status', '!=', 'retired')
                ->selectRaw('SUM(quantity * cost_price) as total')
                ->value('total') ?? 0;

            // Get items by status for chart
            $itemsByStatus = Product::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();

            // Get stock by category for chart
            $stockByCategory = Category::withCount('products')
                ->with(['products' => function ($q) {
                    $q->where('status', '!=', 'retired');
                }])
                ->where('is_active', true)
                ->get()
                ->map(function ($cat) {
                    return [
                        'name' => $cat->name,
                        'products' => $cat->products_count,
                        'stock' => $cat->products->sum('quantity'),
                        'value' => $cat->products->sum(fn ($p) => $p->quantity * $p->price_per_item),
                    ];
                });

            // Get recent stock out activities (show issued orders with details)
            $recentStockOut = StockOutOrder::with(['details.product', 'issuedByUser'])
                ->where('status', 'issued')
                ->orderBy('issue_date', 'desc')
                ->limit(5)
                ->get();

            // Get recent stock in activities
            $recentStockIn = StockIn::with(['product'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Get recent adjustments
            $recentAdjustments = StockAdjustment::with(['product', 'adjustedByUser'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Monthly stock movement (last 6 months)
            $monthlyMovement = collect();
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $stockIn = StockIn::whereYear('received_date', $month->year)
                    ->whereMonth('received_date', $month->month)
                    ->sum('quantity');
                $stockOut = StockOutDetail::whereHas('stockOutOrder', function ($q) use ($month) {
                    $q->whereYear('issue_date', $month->year)
                        ->whereMonth('issue_date', $month->month);
                })
                    ->sum('quantity_issued');
                $monthlyMovement->push([
                    'month' => $month->format('M Y'),
                    'in' => $stockIn,
                    'out' => $stockOut,
                ]);
            }

            // Top low stock items
            $topLowStock = Product::whereRaw('quantity <= min_stock_level')
                ->where('status', '!=', 'retired')
                ->orderByRaw('quantity - min_stock_level ASC')
                ->limit(5)
                ->get();

            return view('dashboard', compact(
                'totalProducts',
                'totalSuppliers',
                'totalCategories',
                'lowStockItems',
                'outOfStockItems',
                'totalInventoryValue',
                'totalCostValue',
                'itemsByStatus',
                'stockByCategory',
                'recentStockOut',
                'recentStockIn',
                'recentAdjustments',
                'monthlyMovement',
                'topLowStock',
                'totalStockIn',
                'totalStockOut',
                'totalStockOutIssued'
            ));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Dashboard error: ' . $e->getMessage());

            // Return view with empty/default data to prevent complete failure
            return view('dashboard', [
                'totalProducts' => 0,
                'totalSuppliers' => 0,
                'totalCategories' => 0,
                'lowStockItems' => 0,
                'outOfStockItems' => 0,
                'totalInventoryValue' => 0,
                'totalCostValue' => 0,
                'itemsByStatus' => collect(),
                'stockByCategory' => collect(),
                'recentStockOut' => collect(),
                'recentStockIn' => collect(),
                'recentAdjustments' => collect(),
                'monthlyMovement' => collect(),
                'topLowStock' => collect(),
                'totalStockIn' => 0,
                'totalStockOut' => 0,
                'totalStockOutIssued' => 0,
            ])->with('error', 'Some dashboard data could not be loaded. Please refresh the page.');
        }
    }
}
