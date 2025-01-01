<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;


    protected $guarded = [];

    // Method to check if coupon is valid
    public function isValid()
{

    $currentDate = now();


    $isActive = $this->is_active;


    $isUnderUsageLimit = $this->usage_count < $this->usage_limit;


    $isWithinValidityPeriod = $currentDate->between($this->start_date, $this->end_date);


    return $isActive && $isUnderUsageLimit &&$isWithinValidityPeriod ;
}
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
