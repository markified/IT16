<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'total_amount',
        'status',
        // Add any other fields that should be mass assignable
    ];

    /**
     * Get the supplier associated with the purchase order.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the order details for the purchase order.
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Get the products associated with the purchase order.
     */
    public function products()
    {
        // Changed from using 'part_id' to 'product_id'
        return $this->belongsToMany(Product::class, 'order_details', 'purchase_order_id', 'product_id')
            ->withPivot(['quantity_ordered', 'price_per_item']);
    }
}
