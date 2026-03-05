<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_number'
    ];

    public function inventoryIssues()
    {
        return $this->hasMany(InventoryIssue::class);
    }
}