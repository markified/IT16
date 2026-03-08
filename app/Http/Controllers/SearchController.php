<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockIn;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Validate search input
        $request->validate([
            'query' => 'nullable|string|max:255',
        ]);

        $query = $request->input('query');

        // Return empty results if no query provided
        if (empty($query)) {
            return view('search.results', [
                'products' => collect(),
                'suppliers' => collect(),
                'purchaseOrders' => collect(),
                'stocks' => collect(),
                'query' => '',
            ]);
        }

        try {
            // Sanitize the query for LIKE search
            $searchTerm = '%' . $query . '%';

            // Search in products
            $products = Product::where('name', 'LIKE', $searchTerm)
                ->orWhere('type', 'LIKE', $searchTerm)
                ->get();

            // Search in suppliers
            $suppliers = Supplier::where('name', 'LIKE', $searchTerm)
                ->orWhere('contact_number', 'LIKE', $searchTerm)
                ->orWhere('email', 'LIKE', $searchTerm)
                ->get();

            // Search in purchase orders via order details
            $purchaseOrders = PurchaseOrder::whereHas('orderDetails', function ($q) use ($searchTerm) {
                $q->where('product_name', 'LIKE', $searchTerm);
            })
                ->orWhereHas('supplier', function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', $searchTerm);
                })
                ->get();

            // Search in stock ins
            $stocks = StockIn::where('supplier_name', 'LIKE', $searchTerm)
                ->orWhere('reference_number', 'LIKE', $searchTerm)
                ->get();

            return view('search.results', compact('products', 'suppliers', 'purchaseOrders', 'stocks', 'query'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Search failed. Please try again.');
        }
    }
}
