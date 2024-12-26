<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Cart extends Model
{
    use HasFactory;


    protected $table="carts";
    protected $guarded = [];


    public function scopeUserId($query)
    {

        return $query->where('user_id', Auth::user()->id);
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
