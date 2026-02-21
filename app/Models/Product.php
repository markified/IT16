<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'quantity',
        'min_stock_level',
        'serial_number',
        'specifications',
        'status',
        'price_per_item',
    ];

    // Define relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventoryIssues()
    {
        return $this->hasMany(InventoryIssue::class, 'part_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'part_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier');
    }

    // Check if product is low on stock
    public function isLowStock()
    {
        return $this->quantity <= $this->min_stock_level;
    }
}
