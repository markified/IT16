<?php

namespace App\Http\Controllers;

use App\Helpers\InputSanitizerHelper;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::active()->orderBy('created_at', 'DESC')->get();

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
        // Sanitize inputs before validation
        $sanitizationRules = [
            'name' => 'string',
            'contact_number' => 'phone',
            'email' => 'email',
        ];
        $sanitized = InputSanitizerHelper::sanitizeRequest($request, $sanitizationRules);
        $request->merge($sanitized);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'email' => 'required|email|max:255',
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

        // Sanitize inputs before validation
        $sanitizationRules = [
            'name' => 'string',
            'contact_number' => 'phone',
            'email' => 'email',
        ];
        $sanitized = InputSanitizerHelper::sanitizeRequest($request, $sanitizationRules);
        $request->merge($sanitized);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'email' => 'required|email|max:255',
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
     * Archive the specified resource.
     */
    public function destroy(string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->update(['is_archived' => true]);

            return redirect()->route('suppliers')->with('success', 'Supplier archived successfully');
        } catch (\Exception $e) {
            return redirect()->route('suppliers')->with('error', 'Failed to archive supplier. Please try again.');
        }
    }

    // Add this method to support AJAX supplier filtering by product
    public function suppliersForProduct(Request $request)
    {
        try {
            // Validate product_id
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
            ]);

            $productId = $request->input('product_id');
            $product = \App\Models\Product::with('suppliers')->find($productId);

            return response()->json($product ? $product->suppliers : []);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Invalid product ID'], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch suppliers'], 500);
        }
    }

    /**
     * Display archived suppliers.
     */
    public function archived()
    {
        $suppliers = Supplier::archived()->orderBy('updated_at', 'DESC')->get();

        return view('suppliers.archived', compact('suppliers'));
    }

    /**
     * Restore an archived supplier.
     */
    public function restore(string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->update(['is_archived' => false]);

            return redirect()->back()->with('success', 'Supplier restored successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore supplier. Please try again.');
        }
    }

    /**
     * Permanently delete an archived supplier.
     */
    public function permanentDelete(string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);

            // Only allow permanent deletion of archived suppliers
            if (! $supplier->is_archived) {
                return redirect()->back()->with('error', 'Only archived suppliers can be permanently deleted.');
            }

            $supplierName = $supplier->name;
            $supplier->delete();

            return redirect()->back()->with('success', "Supplier '{$supplierName}' has been permanently deleted.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete supplier permanently. Please try again.');
        }
    }
}
