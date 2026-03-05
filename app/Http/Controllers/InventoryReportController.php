<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockIn;
use App\Models\InventoryIssue;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryReportController extends Controller
{
    public function index()
    {
        return view('reports.inventory.index');
    }

    /**
     * Inventory Valuation Report
     */
    public function valuation(Request $request)
    {
        $query = Product::with('category')
            ->where('status', '!=', 'retired');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name')->get();

        $summary = [
            'total_items' => $products->sum('quantity'),
            'total_retail_value' => $products->sum(fn($p) => $p->quantity * $p->price_per_item),
            'total_cost_value' => $products->sum(fn($p) => $p->quantity * $p->cost_price),
            'potential_profit' => 0,
        ];
        $summary['potential_profit'] = $summary['total_retail_value'] - $summary['total_cost_value'];

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('reports.inventory.valuation', compact('products', 'summary', 'categories'));
    }

    /**
     * Stock Movement Report
     */
    public function movement(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $productId = $request->input('product_id');

        // Get Stock In movements
        $stockInQuery = StockIn::with('product')
            ->whereBetween('received_date', [$startDate, $endDate]);
        if ($productId) {
            $stockInQuery->where('product_id', $productId);
        }
        $stockIns = $stockInQuery->orderBy('received_date')->get();

        // Get Stock Out movements
        $stockOutQuery = InventoryIssue::with('product')
            ->whereBetween('issue_date', [$startDate, $endDate]);
        if ($productId) {
            $stockOutQuery->where('product_id', $productId);
        }
        $stockOuts = $stockOutQuery->orderBy('issue_date')->get();

        // Get Adjustments
        $adjustmentQuery = StockAdjustment::with('product')
            ->whereBetween('adjustment_date', [$startDate, $endDate]);
        if ($productId) {
            $adjustmentQuery->where('product_id', $productId);
        }
        $adjustments = $adjustmentQuery->orderBy('adjustment_date')->get();

        // Combine and sort all movements
        $movements = collect();

        foreach ($stockIns as $item) {
            $movements->push([
                'date' => $item->received_date,
                'type' => 'Stock In',
                'product' => $item->product->name,
                'reference' => $item->reference_number,
                'quantity_in' => $item->quantity,
                'quantity_out' => 0,
                'note' => $item->supplier_name,
            ]);
        }

        foreach ($stockOuts as $item) {
            $movements->push([
                'date' => $item->issue_date,
                'type' => 'Stock Out',
                'product' => $item->product->name,
                'reference' => 'ISS-' . $item->id,
                'quantity_in' => 0,
                'quantity_out' => $item->quantity_issued,
                'note' => $item->recipient,
            ]);
        }

        foreach ($adjustments as $item) {
            $qty = $item->quantity_after - $item->quantity_before;
            $movements->push([
                'date' => $item->adjustment_date,
                'type' => 'Adjustment',
                'product' => $item->product->name,
                'reference' => $item->reference_number,
                'quantity_in' => $qty > 0 ? $qty : 0,
                'quantity_out' => $qty < 0 ? abs($qty) : 0,
                'note' => $item->reason_label,
            ]);
        }

        $movements = $movements->sortBy('date')->values();

        $summary = [
            'total_in' => $movements->sum('quantity_in'),
            'total_out' => $movements->sum('quantity_out'),
            'net_change' => $movements->sum('quantity_in') - $movements->sum('quantity_out'),
        ];

        $products = Product::orderBy('name')->get();

        return view('reports.inventory.movement', compact('movements', 'summary', 'products', 'startDate', 'endDate', 'productId'));
    }

    /**
     * Low Stock Report
     */
    public function lowStock(Request $request)
    {
        $query = Product::with('category')
            ->whereRaw('quantity <= min_stock_level')
            ->where('status', '!=', 'retired');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderByRaw('quantity - min_stock_level')->get();

        $outOfStock = $products->where('quantity', 0)->count();
        $lowStock = $products->where('quantity', '>', 0)->count();

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('reports.inventory.low-stock', compact('products', 'outOfStock', 'lowStock', 'categories'));
    }

    /**
     * Category Summary Report
     */
    public function categoryReport()
    {
        $categories = Category::withCount('products')
            ->with(['products' => function ($q) {
                $q->where('status', '!=', 'retired');
            }])
            ->where('is_active', true)
            ->get()
            ->map(function ($category) {
                $category->total_stock = $category->products->sum('quantity');
                $category->total_value = $category->products->sum(fn($p) => $p->quantity * $p->price_per_item);
                $category->low_stock_count = $category->products->filter(fn($p) => $p->isLowStock())->count();
                return $category;
            });

        $totals = [
            'products' => $categories->sum('products_count'),
            'stock' => $categories->sum('total_stock'),
            'value' => $categories->sum('total_value'),
            'low_stock' => $categories->sum('low_stock_count'),
        ];

        return view('reports.inventory.category', compact('categories', 'totals'));
    }

    /**
     * Export report as CSV
     */
    public function export(Request $request, string $type)
    {
        $filename = "inventory_{$type}_" . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($type, $request) {
            $file = fopen('php://output', 'w');

            switch ($type) {
                case 'valuation':
                    fputcsv($file, ['SKU', 'Name', 'Category', 'Quantity', 'Cost Price', 'Retail Price', 'Cost Value', 'Retail Value']);
                    $products = Product::with('category')->where('status', '!=', 'retired')->get();
                    foreach ($products as $p) {
                        fputcsv($file, [
                            $p->sku,
                            $p->name,
                            $p->category->name ?? 'Uncategorized',
                            $p->quantity,
                            $p->cost_price,
                            $p->price_per_item,
                            $p->quantity * $p->cost_price,
                            $p->quantity * $p->price_per_item,
                        ]);
                    }
                    break;

                case 'low-stock':
                    fputcsv($file, ['SKU', 'Name', 'Category', 'Current Stock', 'Min Level', 'Shortage', 'Status']);
                    $products = Product::with('category')
                        ->whereRaw('quantity <= min_stock_level')
                        ->where('status', '!=', 'retired')
                        ->get();
                    foreach ($products as $p) {
                        fputcsv($file, [
                            $p->sku,
                            $p->name,
                            $p->category->name ?? 'Uncategorized',
                            $p->quantity,
                            $p->min_stock_level,
                            $p->min_stock_level - $p->quantity,
                            $p->quantity == 0 ? 'Out of Stock' : 'Low Stock',
                        ]);
                    }
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
