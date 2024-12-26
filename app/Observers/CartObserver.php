<?php

namespace App\Observers;

use App\Models\Cart;
use App\Models\Product;

class CartObserver
{
    /**
     * Handle the Cart "created" event.
     */
    public function created(Cart $cart)
    {


    }

    /**
     * Handle the Cart "updated" event.
     */
    public function updated(Cart $cart): void
    {
        //
    }

    /**
     * Handle the Cart "deleted" event.
     */
    public function deleted(Cart $cart)
    {
        $product = Product::find($cart->product_id);
        if ($product) {
            $product->stock += $cart->quantity;
            $product->save();
        }
    }

    /**
     * Handle the Cart "restored" event.
     */
    public function restored(Cart $cart): void
    {
        //
    }

    /**
     * Handle the Cart "force deleted" event.
     */
    public function forceDeleted(Cart $cart): void
    {
        //
    }
}
