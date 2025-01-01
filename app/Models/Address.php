<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $table="addresses";
    protected $guarded = [];

    public function city()
    {
        return $this->belongsTo(Cities::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
