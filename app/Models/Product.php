<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'barcode',
        'brand',
        'model_number',
        'type',
        'description',
        'quantity',
        'min_stock_level',
        'serial_number',
        'specifications',
        'location',
        'status',
        'price_per_item',
        'cost_price',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    /**
     * Scope to filter out archived products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope to get only archived products.
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = self::generateSku($product);
            }
        });
    }

    public static function generateSku($product)
    {
        $prefix = 'PC';
        if ($product->category_id) {
            $category = Category::find($product->category_id);
            if ($category) {
                $prefix = strtoupper(substr($category->code, 0, 3));
            }
        }
        $sequence = self::max('id') + 1;

        return $prefix . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    public static function generateBarcode()
    {
        return '89' . str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventoryIssues()
    {
        return $this->hasMany(InventoryIssue::class, 'product_id');
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'part_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier');
    }

    // Accessors
    public function isLowStock()
    {
        return $this->quantity <= $this->min_stock_level;
    }

    public function getStockValueAttribute()
    {
        return $this->quantity * $this->price_per_item;
    }

    public function getCostValueAttribute()
    {
        return $this->quantity * $this->cost_price;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->cost_price == 0) {
            return 0;
        }

        return (($this->price_per_item - $this->cost_price) / $this->cost_price) * 100;
    }

    public function getStockStatusAttribute()
    {
        if ($this->quantity == 0) {
            return 'out_of_stock';
        }
        if ($this->quantity <= $this->min_stock_level) {
            return 'low_stock';
        }
        if ($this->quantity <= $this->min_stock_level * 2) {
            return 'moderate';
        }

        return 'in_stock';
    }

    public function getStockBadgeAttribute()
    {
        return match ($this->stock_status) {
            'out_of_stock' => '<span class="badge bg-danger">Out of Stock</span>',
            'low_stock' => '<span class="badge bg-warning">Low Stock</span>',
            'moderate' => '<span class="badge bg-info">Moderate</span>',
            'in_stock' => '<span class="badge bg-success">In Stock</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
