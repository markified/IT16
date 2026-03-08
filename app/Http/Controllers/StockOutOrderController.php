<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOutDetail;
use App\Models\StockOutOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOutOrderController extends Controller
{
    /**
     * Display a listing of stock out orders.
     */
    public function index()
    {
        $stockOutOrders = StockOutOrder::with('issuedByUser')
            ->latest()
            ->paginate(10);

        return view('stock-out-orders.index', compact('stockOutOrders'));
    }

    /**
     * Show the form for creating a new stock out order.
     */
    public function create()
    {
        $products = Product::where('quantity', '>', 0)->orderBy('name')->get();

        return view('stock-out-orders.create', compact('products'));
    }

    /**
     * Store a newly created stock out order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,approved,issued',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Validate stock availability for all products
            foreach ($request->products as $item) {
                $product = Product::find($item['id']);
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->quantity}");
                }
            }

            // Calculate total amount
            $totalAmount = 0;
            foreach ($request->products as $item) {
                $product = Product::find($item['id']);
                $totalAmount += $item['quantity'] * ($product->cost_price ?? 0);
            }

            // Create stock out order
            $stockOutOrder = StockOutOrder::create([
                'order_number' => StockOutOrder::generateOrderNumber(),
                'recipient' => $request->recipient,
                'issue_date' => $request->issue_date,
                'total_amount' => $totalAmount,
                'status' => $request->status,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'issued_by' => Auth::id(),
                'approved_by' => $request->status === 'approved' ? Auth::id() : null,
            ]);

            // Create order details
            foreach ($request->products as $item) {
                $product = Product::find($item['id']);

                StockOutDetail::create([
                    'stock_out_order_id' => $stockOutOrder->id,
                    'product_id' => $item['id'],
                    'product_name' => $product->name,
                    'quantity_issued' => $item['quantity'],
                    'unit_cost' => $product->cost_price ?? 0,
                ]);

                // Only deduct stock if status is 'issued'
                if ($request->status === 'issued') {
                    $product->quantity -= $item['quantity'];
                    $product->save();
                }
            }

            DB::commit();

            $message = 'Stock out order created successfully.';
            if ($request->status === 'issued') {
                $message .= ' Stock has been deducted.';
            }

            return redirect()->route('stock-out-orders.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified stock out order.
     */
    public function show($id)
    {
        $stockOutOrder = StockOutOrder::with(['details.product', 'issuedByUser', 'approvedByUser'])->findOrFail($id);

        return view('stock-out-orders.show', compact('stockOutOrder'));
    }

    /**
     * Update the status of a stock out order.
     */
    public function updateStatus(Request $request, StockOutOrder $stockOutOrder)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,issued,cancelled',
        ]);

        // Prevent changes if already issued
        if ($stockOutOrder->status === 'issued') {
            return back()->with('error', 'Cannot modify an issued order. The order is already final.');
        }

        // Prevent changes if already cancelled
        if ($stockOutOrder->status === 'cancelled') {
            return back()->with('error', 'Cannot modify a cancelled order.');
        }

        try {
            DB::beginTransaction();

            $oldStatus = $stockOutOrder->status;
            $newStatus = $request->status;
            $updateData = ['status' => $newStatus];

            if ($newStatus === 'approved') {
                $updateData['approved_by'] = Auth::id();
            }

            // Handle stock deduction/restoration based on status change
            if ($newStatus === 'issued' && in_array($oldStatus, ['pending', 'approved'])) {
                // Deduct stock when changing to 'issued'
                foreach ($stockOutOrder->details as $detail) {
                    $product = Product::find($detail->product_id);
                    if ($product) {
                        if ($product->quantity < $detail->quantity_issued) {
                            throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->quantity}");
                        }
                        $product->quantity -= $detail->quantity_issued;
                        $product->save();
                    }
                }
            } elseif ($newStatus === 'cancelled' && in_array($oldStatus, ['pending', 'approved'])) {
                // No stock to restore since it was never deducted
                // Just update status
            }

            $stockOutOrder->update($updateData);

            DB::commit();

            $message = 'Stock out order status updated successfully.';
            if ($newStatus === 'issued') {
                $message .= ' Stock has been deducted.';
            } elseif ($newStatus === 'cancelled') {
                $message .= ' Order has been cancelled.';
            }

            return redirect()->route('stock-out-orders.show', $stockOutOrder)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified stock out order.
     */
    public function destroy(StockOutOrder $stockOutOrder)
    {
        try {
            // Only allow deletion of pending or approved orders
            if ($stockOutOrder->status === 'issued') {
                return back()->with('error', 'Cannot delete an issued order. Stock has already been deducted.');
            }

            if ($stockOutOrder->status === 'cancelled') {
                return back()->with('error', 'Cannot delete a cancelled order.');
            }

            DB::beginTransaction();

            // No need to restore stock since it was never deducted for pending/approved orders
            $stockOutOrder->delete();

            DB::commit();

            return redirect()->route('stock-out-orders.index')
                ->with('success', 'Stock out order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to delete order. Please try again.');
        }
    }
}
