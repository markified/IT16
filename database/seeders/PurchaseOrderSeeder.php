<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all suppliers and products
        $suppliers = Supplier::all();
        $products = Product::all();

        // Create purchase orders with different statuses
        $statuses = ['pending', 'approved', 'partial', 'received', 'completed', 'cancelled'];
        $currentDate = Carbon::now();

        // Create 20 purchase orders
        for ($i = 0; $i < 20; $i++) {
            // Get a random supplier
            $supplier = $suppliers->random();

            // Determine status and dates based on the status
            $status = $statuses[array_rand($statuses)];
            $createdDate = $currentDate->copy()->subDays(rand(1, 90));

            // Create the purchase order
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $supplier->id,
                'status' => $status,
                'created_at' => $createdDate,
                'updated_at' => $createdDate->copy()->addDays(rand(1, 5)),
            ]);

            // Get products from the supplier (or if none, use random products)
            $supplierProductIds = $supplier->products()->pluck('products.id')->toArray();
            $productPool = !empty($supplierProductIds) ?
                Product::whereIn('id', $supplierProductIds)->get() :
                $products->random(min(5, $products->count()));

            // Select 1-5 products for this order
            $orderProducts = $productPool->random(rand(1, min(5, $productPool->count())));

            $totalAmount = 0;

            // Add order details for each product
            foreach ($orderProducts as $product) {
                $quantity = rand(1, 10);
                $price = $product->price_per_item;
                $orderDate = $createdDate->copy()->format('Y-m-d');

                // Create order detail
                $orderDetail = DB::table('order_details')->insert([
                    'product_id' => $product->id,
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_name' => $product->name,
                    'quantity_ordered' => $quantity,
                    'price_per_item' => $price,
                    'order_date' => $orderDate,
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate,
                ]);

                $totalAmount += ($quantity * $price);

                // If the order is partially or completely received, create receiving records
                if (in_array($status, ['partial', 'received', 'completed'])) {
                    $receivedQuantity = $status === 'partial' ?
                        rand(1, $quantity - 1) : $quantity;

                    $receivedDate = $createdDate->copy()->addDays(rand(2, 10))->format('Y-m-d');

                    // Get the order detail ID
                    $orderDetailId = DB::table('order_details')
                        ->where('purchase_order_id', $purchaseOrder->id)
                        ->where('product_id', $product->id)
                        ->value('id');

                    // Create receiving record
                    DB::table('purchase_order_receivings')->insert([
                        'order_detail_id' => $orderDetailId,
                        'received_date' => $receivedDate,
                        'quantity_received' => $receivedQuantity,
                        'received_by' => ['James Wilson', 'Sarah Chen', 'Michael Rodriguez', 'Emily Johnson'][rand(0, 3)],
                        'notes' => rand(0, 1) ? 'Items received in good condition' : null,
                        'created_at' => $createdDate->copy()->addDays(rand(2, 10)),
                        'updated_at' => $createdDate->copy()->addDays(rand(2, 10)),
                    ]);

                    // Update product quantity if completed or received
                    if (in_array($status, ['received', 'completed'])) {
                        $product->quantity += $receivedQuantity;
                        $product->save();
                    }
                }
            }

            // Update the total amount for the purchase order
            $purchaseOrder->total_amount = $totalAmount;
            $purchaseOrder->save();
        }
    }
}
