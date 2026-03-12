<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'serial_number',
        'specifications',
        'quantity',
        'price_per_item',
        'status',
        'report_type',
        'parameters',
        'data',
        'generated_by',
        'report_date',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'parameters' => 'array',
        'data' => 'array',
        'report_date' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the user who generated this report.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Get the inventory issues related to this report.
     */
    public function inventoryIssues()
    {
        return $this->belongsToMany(InventoryIssue::class, 'report_inventory_issues');
    }

    /**
     * Get the suppliers related to this report.
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'report_suppliers');
    }
}
