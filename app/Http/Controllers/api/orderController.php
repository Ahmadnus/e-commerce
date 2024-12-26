<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class orderController extends Controller
{

    public function addOrder(Request $request)
    {
        try {
            $user = Auth::user()->id;
            $total = 0;

            $cartItems = Cart::userId()->with('product')->get();

            foreach ($cartItems as $item) {
                $total += $item->quantity * $item->product->price;
            }

            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $user,
                'total' => $total,
            ]);

            foreach ($cartItems as $item) {

                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

            }

            Cart::userId()->delete();
            $lang=app()->getLocale();
            DB::commit();
            Log::info("Cart items retrieved successfully for user " . Auth::id());
            return response()->json([
                "success" => true,
                "msg" => trans("Order created successfully"),
                "data" => $order
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                "success" => false,
                "msg" => trans("Error creating order") . ": " . $e->getMessage(),
                "data" => []
            ], 500);
        }
    }

    public function getOrders()
    {
        $user = Auth::user()->id;
        $orders = Order::where('user_id', $user)
            ->with('orderProducts.product')
            ->get()->map(function ($order) {

                return [
                    'order_id' => $order->id,
                    'order_total' => $order->total,
                    'order_date' => $order->created_at->toFormattedDateString(),
                    'products' => $order->orderProducts->map(function ($orderProduct) {
                        return [
                            'product_id' => $orderProduct->product->id,
                            'product_name' => $orderProduct->product->name,
                            'product_price' => $orderProduct->product->price,
                            'quantity' => $orderProduct->quantity,
                            'total_price' => $orderProduct->quantity * $orderProduct->product->price,
                        ];
                    }),
                ];
            });
            $lang=app()->getLocale();
        return response()->json([
            "success" => true,
            "msg" => trans("Orders retrieved successfully."),
            "data" => $orders
        ], 200);
    }
}
