<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_number',
        'email'
    ];



    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function suppliedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_supplier');
    }
}