<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'reference_number',
        'adjustment_type',
        'quantity_before',
        'quantity_adjusted',
        'quantity_after',
        'reason',
        'notes',
        'adjusted_by',
        'adjustment_date',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function adjustedByUser()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    public static function generateReferenceNumber()
    {
        $prefix = 'ADJ';
        $date = now()->format('Ymd');
        $lastAdjustment = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastAdjustment ? (int) substr($lastAdjustment->reference_number, -4) + 1 : 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public static function getReasonOptions()
    {
        return [
            'damaged' => 'Damaged/Defective',
            'expired' => 'Expired',
            'lost' => 'Lost/Missing',
            'theft' => 'Theft',
            'found' => 'Found/Recovered',
            'counting_error' => 'Counting Error',
            'return' => 'Returned to Stock',
            'other' => 'Other',
        ];
    }

    public function getReasonLabelAttribute()
    {
        return self::getReasonOptions()[$this->reason] ?? $this->reason;
    }
}
