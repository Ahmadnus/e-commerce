<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;
    protected $table="cities";
    protected $guarded = [];
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

}
