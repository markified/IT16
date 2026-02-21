<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderReceiving extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_detail_id',
        'received_date',
        'quantity_received',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'received_date' => 'datetime',
    ];

    /**
     * Get the order detail associated with this receiving record.
     */
    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(OrderDetail::class);
    }
}