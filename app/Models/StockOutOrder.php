<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOutOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'recipient',
        'issue_date',
        'total_amount',
        'status',
        'reason',
        'notes',
        'issued_by',
        'approved_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the details for this stock out order.
     */
    public function details()
    {
        return $this->hasMany(StockOutDetail::class);
    }

    /**
     * Get the products for this stock out order.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'stock_out_details')
            ->withPivot('quantity_issued', 'unit_cost', 'product_name');
    }

    /**
     * Get the user who issued this order.
     */
    public function issuedByUser()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the user who approved this order.
     */
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber()
    {
        $prefix = 'SO-' . date('Ymd') . '-';
        $lastOrder = self::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }
}
