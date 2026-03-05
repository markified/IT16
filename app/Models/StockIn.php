<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'unit_cost',
        'supplier_name',
        'reference_number',
        'received_date',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'received_date' => 'date',
        'unit_cost' => 'decimal:2',
    ];

    /**
     * Get the product (PC part) associated with this stock in.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
