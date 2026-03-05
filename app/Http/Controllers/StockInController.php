<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    /**
     * Display a listing of stock in records.
     */
    public function index()
    {
        $stockIns = StockIn::with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('stock-in.index', compact('stockIns'));
    }

    /**
     * Show the form for creating a new stock in record.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('stock-in.create', compact('products'));
    }

    /**
     * Store a newly created stock in record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            'supplier_name' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['received_by'] = Auth::user()->name;

        DB::beginTransaction();

        try {
            // Create stock in record
            StockIn::create($validated);

            // Update product quantity
            $product = Product::findOrFail($validated['product_id']);
            $product->quantity += $validated['quantity'];
            $product->save();

            DB::commit();

            return redirect()->route('stock-in.index')
                ->with('success', 'Stock added successfully. ' . $validated['quantity'] . ' units added to ' . $product->name);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to add stock: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified stock in record.
     */
    public function show($id)
    {
        $stockIn = StockIn::with('product')->findOrFail($id);
        return view('stock-in.show', compact('stockIn'));
    }

    /**
     * Remove the specified stock in record.
     */
    public function destroy($id)
    {
        $stockIn = StockIn::findOrFail($id);
        
        DB::beginTransaction();

        try {
            // Reverse the stock addition
            $product = $stockIn->product;
            if ($product->quantity >= $stockIn->quantity) {
                $product->quantity -= $stockIn->quantity;
                $product->save();
            }

            $stockIn->delete();

            DB::commit();

            return redirect()->route('stock-in.index')
                ->with('success', 'Stock in record deleted and inventory adjusted.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete record: ' . $e->getMessage()]);
        }
    }
}
