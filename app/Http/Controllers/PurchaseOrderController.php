<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the purchase orders.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')
            ->latest()
            ->paginate(10);

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created purchase order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total amount based on products and their prices
            $totalAmount = 0;
            foreach ($request->products as $product) {
                $productModel = Product::find($product['id']);
                $totalAmount += $product['quantity'] * $productModel->price_per_item;
            }

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // Create order details
            foreach ($request->products as $product) {
                $productModel = Product::find($product['id']);
                OrderDetail::create([
                    'product_id' => $product['id'],
                    'product_name' => $productModel->name, // Added product_name field
                    'purchase_order_id' => $purchaseOrder->id,
                    'quantity_ordered' => $product['quantity'],
                    'price_per_item' => $productModel->price_per_item,
                    'order_date' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create purchase order. ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified purchase order.
     */
    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with(['products', 'supplier'])->findOrFail($id);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Update the status of a purchase order.
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,received,cancelled',
        ]);

        $purchaseOrder->update([
            'status' => $request->status,
        ]);

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase order status updated successfully.');
    }

    /**
     * Remove the specified purchase order from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        // Only allow deletion of pending purchase orders
        if ($purchaseOrder->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending purchase orders can be deleted.']);
        }

        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order deleted successfully.');
    }

    /**
     * Show form for receiving items from a purchase order.
     */
    public function showReceiveForm(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'approved') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->withErrors(['error' => 'Only approved purchase orders can receive items.']);
        }

        $purchaseOrder->load(['supplier', 'orderDetails.product', 'orderDetails.receivings']);
        return view('receivings.receive', compact('purchaseOrder'));
    }

    /**
     * Process receiving items for a purchase order.
     */
    public function processReceive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'receivings' => 'required|array',
            'receivings.*.order_detail_id' => 'required|exists:order_details,id',
            'receivings.*.quantity_received' => 'required|integer|min:1',
            'received_by' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $allReceived = true;
            $anyReceived = false;
            $totalReceived = 0;

            foreach ($request->receivings as $receiving) {
                if ($receiving['quantity_received'] > 0) {
                    // Create receiving record
                    $orderDetail = OrderDetail::findOrFail($receiving['order_detail_id']);

                    $orderDetail->receivings()->create([
                        'received_date' => now(),
                        'quantity_received' => $receiving['quantity_received'],
                        'received_by' => $request->received_by,
                        'notes' => $receiving['notes'] ?? null,
                    ]);

                    // Update inventory quantity
                    $product = $orderDetail->product;
                    $product->quantity += $receiving['quantity_received'];
                    $product->save();

                    $totalReceived += $receiving['quantity_received'];
                }
            }

            // Check status for all order details
            foreach ($purchaseOrder->orderDetails as $detail) {
                $totalReceivedForDetail = $detail->receivings()->sum('quantity_received');
                if ($totalReceivedForDetail < $detail->quantity_ordered) {
                    $allReceived = false;
                }
                if ($totalReceivedForDetail > 0) {
                    $anyReceived = true;
                }
            }

            if ($allReceived && $totalReceived > 0) {
                $purchaseOrder->update(['status' => 'received']);
            } elseif ($anyReceived) {
                $purchaseOrder->update(['status' => 'partial']);
            }

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Items received successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process receiving. ' . $e->getMessage()]);
        }
    }

    /**
     * Generate purchase orders for low stock items.
     */
    public function generateForLowStock()
    {
        $lowStockParts = Product::whereRaw('quantity <= min_stock_level')->get();
        $suppliers = Supplier::all();

        return view('purchase-orders.generate-low-stock', compact('lowStockParts', 'suppliers'));
    }
}
