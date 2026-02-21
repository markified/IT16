<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'department_id',
        'employee_id',
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

    /**
     * Get the department associated with the inventory issue.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the employee associated with the inventory issue.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}