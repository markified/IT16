<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['product', 'adjustedByUser'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }
        if ($request->filled('adjustment_type')) {
            $query->where('adjustment_type', $request->adjustment_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('adjustment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('adjustment_date', '<=', $request->date_to);
        }

        $adjustments = $query->paginate(20);
        $products = Product::orderBy('name')->get();
        $reasons = StockAdjustment::getReasonOptions();

        return view('stock-adjustments.index', compact('adjustments', 'products', 'reasons'));
    }

    public function create()
    {
        $products = Product::where('status', '!=', 'retired')
            ->orderBy('name')
            ->get();
        $reasons = StockAdjustment::getReasonOptions();

        return view('stock-adjustments.create', compact('products', 'reasons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment_type' => 'required|in:add,remove',
            'quantity_adjusted' => 'required|integer|min:1',
            'reason' => 'required|in:damaged,expired,lost,theft,found,counting_error,return,other',
            'notes' => 'nullable|string|max:1000',
            'adjustment_date' => 'required|date',
        ]);

        try {
            $product = Product::findOrFail($validated['product_id']);
            $quantityBefore = $product->quantity;

            // Calculate new quantity
            if ($validated['adjustment_type'] === 'add') {
                $quantityAfter = $quantityBefore + $validated['quantity_adjusted'];
            } else {
                // Remove stock
                $quantityAfter = max(0, $quantityBefore - $validated['quantity_adjusted']);
            }

            DB::transaction(function () use ($validated, $product, $quantityBefore, $quantityAfter) {
                // Create adjustment record
                $adjustment = StockAdjustment::create([
                    'product_id' => $validated['product_id'],
                    'reference_number' => StockAdjustment::generateReferenceNumber(),
                    'adjustment_type' => $validated['adjustment_type'],
                    'quantity_before' => $quantityBefore,
                    'quantity_adjusted' => $validated['quantity_adjusted'],
                    'quantity_after' => $quantityAfter,
                    'reason' => $validated['reason'],
                    'notes' => $validated['notes'],
                    'adjusted_by' => Auth::id(),
                    'adjustment_date' => $validated['adjustment_date'],
                ]);

                // Update product quantity
                $product->update(['quantity' => $quantityAfter]);

                // Log audit trail
                AuditLog::logAdjustment($adjustment);
            });

            return redirect()->route('stock-adjustments.index')
                ->with('success', 'Stock adjustment recorded successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Product not found.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Failed to record stock adjustment. Please try again.');
        }
    }

    public function show(string $id)
    {
        $adjustment = StockAdjustment::with(['product', 'adjustedByUser'])->findOrFail($id);

        return view('stock-adjustments.show', compact('adjustment'));
    }

    public function destroy(string $id)
    {
        try {
            $adjustment = StockAdjustment::findOrFail($id);

            // Reverse the adjustment
            $product = $adjustment->product;
            $product->update(['quantity' => $adjustment->quantity_before]);

            // Log the reversal
            AuditLog::log(
                'adjustment_reversed',
                $adjustment,
                ['quantity' => $adjustment->quantity_after],
                ['quantity' => $adjustment->quantity_before],
                "Reversed adjustment: {$product->name} restored to {$adjustment->quantity_before}"
            );

            $adjustment->delete();

            return redirect()->route('stock-adjustments.index')
                ->with('success', 'Stock adjustment reversed and deleted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('stock-adjustments.index')
                ->with('error', 'Stock adjustment record not found.');
        } catch (\Exception $e) {
            return redirect()->route('stock-adjustments.index')
                ->with('error', 'Failed to reverse stock adjustment. Please try again.');
        }
    }
}
