<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['supplier', 'category'])->active()->orderBy('created_at', 'DESC');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Stock status filter
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('quantity <= min_stock_level AND quantity > 0');
            } elseif ($request->stock_status === 'out') {
                $query->where('quantity', 0);
            } elseif ($request->stock_status === 'in') {
                $query->whereRaw('quantity > min_stock_level');
            }
        }

        $products = $query->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('products.create', compact('suppliers', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'specifications' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'price_per_item' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,assigned,maintenance,retired',
            'suppliers' => 'array',
            'suppliers.*' => 'exists:suppliers,id',
        ], [
            'name.required' => 'Product name is required.',
            'description.required' => 'Product description is required.',
            'type.required' => 'Product type is required.',
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity cannot be negative.',
            'min_stock_level.required' => 'Minimum stock level is required.',
            'price_per_item.required' => 'Price per item is required.',
            'price_per_item.min' => 'Price cannot be negative.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
        ]);

        try {
            // Auto-generate serial number and barcode
            $validated['serial_number'] = 'SN-' . strtoupper(uniqid());
            $validated['barcode'] = $request->barcode ?: Product::generateBarcode();
            $validated['cost_price'] = $validated['cost_price'] ?? 0;

            $product = Product::create($validated);
            $product->suppliers()->sync($request->input('suppliers', []));

            return redirect()->route('products')->with('success', 'PC part added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create product. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['supplier', 'category', 'suppliers', 'stockIns', 'stockAdjustments', 'inventoryIssues'])->findOrFail($id);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with('suppliers')->findOrFail($id);
        $suppliers = Supplier::all();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('products.edit', compact('product', 'suppliers', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'serial_number' => 'nullable|string|max:255|unique:products,serial_number,' . $id,
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $id,
            'specifications' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'price_per_item' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,assigned,maintenance,retired',
            'suppliers' => 'array',
            'suppliers.*' => 'exists:suppliers,id',
        ], [
            'name.required' => 'Product name is required.',
            'description.required' => 'Product description is required.',
            'type.required' => 'Product type is required.',
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity cannot be negative.',
            'serial_number.unique' => 'This serial number is already in use.',
            'sku.unique' => 'This SKU is already in use.',
            'barcode.unique' => 'This barcode is already in use.',
            'price_per_item.required' => 'Price per item is required.',
            'price_per_item.min' => 'Price cannot be negative.',
        ]);

        try {
            $validated['cost_price'] = $validated['cost_price'] ?? 0;

            $product->update($validated);
            $product->suppliers()->sync($request->input('suppliers', []));

            return redirect()->route('products')->with('success', 'PC part updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Archive the specified resource.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update(['is_archived' => true]);

            return redirect()->route('products')->with('success', 'PC part archived successfully');
        } catch (\Exception $e) {
            return redirect()->route('products')->with('error', 'Failed to archive product. Please try again.');
        }
    }

    /**
     * Display low stock products.
     */
    public function lowStock()
    {
        $products = Product::whereRaw('quantity <= min_stock_level')->get();

        return view('products.low_stock', compact('products'));
    }

    /**
     * Display archived products.
     */
    public function archived()
    {
        $products = Product::with(['supplier', 'category'])->archived()->orderBy('updated_at', 'DESC')->get();

        return view('products.archived', compact('products'));
    }

    /**
     * Restore an archived product.
     */
    public function restore(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update(['is_archived' => false]);

            return redirect()->back()->with('success', 'PC part restored successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore product. Please try again.');
        }
    }

    /**
     * Permanently delete an archived product.
     */
    public function permanentDelete(string $id)
    {
        try {
            $product = Product::findOrFail($id);

            // Only allow permanent deletion of archived products
            if (! $product->is_archived) {
                return redirect()->back()->with('error', 'Only archived products can be permanently deleted.');
            }

            $productName = $product->name;
            $product->delete();

            return redirect()->back()->with('success', "Product '{$productName}' has been permanently deleted.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete product permanently. Please try again.');
        }
    }
}
