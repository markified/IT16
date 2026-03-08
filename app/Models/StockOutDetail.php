<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOutDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_out_order_id',
        'product_id',
        'product_name',
        'quantity_issued',
        'unit_cost',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    /**
     * Get the stock out order for this detail.
     */
    public function stockOutOrder()
    {
        return $this->belongsTo(StockOutOrder::class);
    }

    /**
     * Get the product for this detail.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the subtotal for this line item.
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity_issued * $this->unit_cost;
    }
}
