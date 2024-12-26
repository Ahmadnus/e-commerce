<?php

namespace App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Product extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;
    protected $table="products";
    protected $guarded = [];

    public function scopeForFind($query)
    {

        return $query->where('user_id', Auth::id());
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
    public function favorites()
{
    return $this->morphMany(Favorite::class, 'favoritable');
}
}
