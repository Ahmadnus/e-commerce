<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'order_id' => $this->id,
            'order_total' => $this->total,
            'order_date' => $this->created_at->toFormattedDateString(),
            'products' => $this->orderProducts->map(function ($orderProduct) {
                return [
                    'product_id' => $orderProduct->product->id,
                    'product_name' => $orderProduct->product->name,
                    'product_price' => $orderProduct->product->price,
                    'quantity' => $orderProduct->quantity,
                    'total_price' => $orderProduct->quantity * $orderProduct->product->price,
                ];
            }),
        ];
    }
}
