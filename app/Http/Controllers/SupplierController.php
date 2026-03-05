<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('created_at', 'DESC')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'email' => 'required|email|max:255'
        ], [
            'name.required' => 'Supplier name is required.',
            'contact_number.required' => 'Contact number is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        try {
            Supplier::create($validated);
            return redirect()->route('suppliers')->with('success', 'Supplier added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create supplier. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::with('suppliedProducts')->findOrFail($id);
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'email' => 'required|email|max:255'
        ], [
            'name.required' => 'Supplier name is required.',
            'contact_number.required' => 'Contact number is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        try {
            $supplier->update($validated);
            return redirect()->route('suppliers')->with('success', 'Supplier updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update supplier. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);

            // Check if supplier is used in any purchase order
            if ($supplier->purchaseOrders()->count() > 0) {
                return redirect()->route('suppliers')
                    ->with('error', 'Cannot delete supplier because it is used in one or more purchase orders.');
            }

            $supplier->delete();
            return redirect()->route('suppliers')->with('success', 'Supplier deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('suppliers')->with('error', 'Failed to delete supplier. Please try again.');
        }
    }

    // Add this method to support AJAX supplier filtering by product
    public function suppliersForProduct(Request $request)
    {
        $productId = $request->input('product_id');
        if (!$productId) {
            return response()->json([]);
        }
        $product = \App\Models\Product::with('suppliers')->find($productId);
        return response()->json($product ? $product->suppliers : []);
    }
}