<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'recipient',
        'quantity_issued',
        'issue_date',
        'reason',
        'notes',
        'issued_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    /**
     * Get the product associated with the inventory issue.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
