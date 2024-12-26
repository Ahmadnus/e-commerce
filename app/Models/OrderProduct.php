<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected $table="order_products";
    protected $guarded = [];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship with the product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
