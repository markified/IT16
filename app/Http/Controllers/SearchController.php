<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\Stock;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');

        // Search in products
        $products = Product::where('name', 'LIKE', "%$query%")
            ->orWhere('type', 'LIKE', "%$query%")
            ->get();

        // Search in suppliers
        $suppliers = Supplier::where('name', 'LIKE', "%$query%")
            ->orWhere('contact_number', 'LIKE', "%$query%")
            ->orWhere('email', 'LIKE', "%$query%")
            ->get();

        // Search in purchase orders via order details
        $purchaseOrders = PurchaseOrder::whereHas('orderDetails', function ($q) use ($query) {
            $q->where('product_name', 'LIKE', "%$query%");
        })
            ->orWhereHas('supplier', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%");
            })
            ->get();

        // Search in stock
        $stocks = Stock::where('product_name', 'LIKE', "%$query%")
            ->orWhere('supplier', 'LIKE', "%$query%")
            ->get();

        return view('search.results', compact('products', 'suppliers', 'purchaseOrders', 'stocks', 'query'));
    }
}
