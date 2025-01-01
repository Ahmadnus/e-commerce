<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\OrderResource;

class orderController extends Controller
{

    public function addOrder(Request $request,)
    {
        try {


            // Check if the coupon is valid


            $user = Auth::user()->id;
            $total = 0;

            $cartItems = Cart::userId()->with('product')->get();

            foreach ($cartItems as $item) {
                $total += $item->quantity * $item->product->price;
            }

            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $user,
                'total_price' => $total,
                'address_id' => $request->add,
            'discount' => 0,
            'status' => 'pending',
            'coupon_id' =>$request->copon,
            ]);

            foreach ($cartItems as $item) {

                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,

                    'quantity' => $item->quantity,
                    'price' => $item->product->price,

                ]);

            }


            $coupon = Coupon::where('id',$request->copon)->first();

if($coupon){

    if ($coupon->isValid()) {



        $discountAmount = $coupon->discount_type == 'percentage'
            ? $total * ($coupon->discount_value / 100)
            : $coupon->discount_value;


        $discountAmount = min($discountAmount, $total);


        $order->update([
            'coupon_id' => $coupon->id,
            'discount' => $discountAmount,
            'total_price' => $total - $discountAmount,
        ]);

   $coupon->usage_count += 1;
   $coupon->usage_limit -= 1;
   $coupon->save();

    }
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
    $userId = Auth::id();

    if (!$userId) {
        return response()->json([
            "success" => false,
            "msg" => trans("Unauthorized: You must be logged in."),
            "data" => null
        ], 401);
    }

    $user = Auth::user()->id;
    $orders = Order::where('user_id', $user)
        ->with('orderProducts.product')
        ->get();
    $lang = app()->getLocale();

    return response()->json([
        "success" => true,
        "msg" => trans("Orders retrieved successfully.", [], $lang),
        "data" => OrderResource::collection($orders),
    ], 200);

}
public function updateOrder(Request $request, $id)
{
    $userId = Auth::id();

    $order = Order::where('id', $id)->where('user_id', $userId)->first();

    if (!$order) {
        return response()->json([
            "success" => false,
            "msg" => trans("Order not found or unauthorized."),
            "data" => null
        ], 404);
    }

    try {

        $order->update($request->only(['address_id', 'coupon_id']));

        return response()->json([
            "success" => true,
            "msg" => trans("Order updated successfully."),
            "data" => $order
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            "success" => false,
            "msg" => trans("Error updating order") . ": " . $e->getMessage(),
            "data" => []
        ], 500);
    }
}

public function deleteOrder($id)
{
    $userId = Auth::id();

    $order = Order::where('id', $id)->where('user_id', $userId)->first();

    if (!$order) {
        return response()->json([
            "success" => false,
            "msg" => trans("Order not found or unauthorized."),
            "data" => null
        ], 404);
    }

    try {
        $order->delete();

        return response()->json([
            "success" => true,
            "msg" => trans("Order deleted successfully."),
            "data" => null
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            "success" => false,
            "msg" => trans("Error deleting order") . ": " . $e->getMessage(),
            "data" => []
        ], 500);
    }
}

}
