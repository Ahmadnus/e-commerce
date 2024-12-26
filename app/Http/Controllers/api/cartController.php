<?php

namespace App\Http\Controllers\api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Log;

class cartController extends Controller
{
    // Show cart items
    public function show(Request $request)
    {

        try {

            $lang=app()->getLocale();
            $items = Cart::with('product')->userId()->first();
if(!$items){return response()->json([
    "success" => false,
    "msg" => 'no items',
    "data" => []
], 400);}
else{
    $lang=app()->getLocale();
            return response()->json([
                "success" => true,
                "msg" => "Cart items retrieved successfully",
                "data" => $items
            ], 200);
        }
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "msg" => 'cart cant unavilbele',
                "data" => []
            ], 500);
        }
    }


    public function addItems(Request $request)
    {
        DB::beginTransaction();

        try {
          $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $lang=app()->getLocale();
        if ($validator->invalid()) {
            return response()->json([
                "success" => false,
                "msg" => "Validation errors",
                "errors" => $validator->errors()
            ], 422);
        }
        $product = Product::find($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                "success" => false,
                "msg" => "sorry we don't have this amount",
            ], 400);
        }

            $cartItem = Cart::userId()
                ->where('product_id', $request->product_id)
                ->first();


            if ($cartItem) {
                $cartItem->quantity += $request->quantity;
                $cartItem->save();
            }
             else {
                Cart::create([
                    'user_id' => Auth::user()->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity
                ]);

            }
            $product->stock-= $request->quantity;
            $product -> save();


            DB::commit();

            return response()->json([
                "success" => true,
                "msg" => trans("Product added successfully"),
                "data" => []
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "msg" => $e->getMessage(),
                "data" => []
            ], 500);
        }
    }


    public function remove(Request $request ,$id)
    {
        DB::beginTransaction();

        try {
            $lang=app()->getLocale();


            $cartItem = Cart::userId()
                ->where('product_id', $id)
                ->first();

            if (!$cartItem) {
                Log::warning("No item found in cart to remove for user " . Auth::id());
                return response()->json([
                    "success" => false,
                    "msg" => 'Nothing to delete',
                    "data" => []
                ], 500);;
            }
else{
            $cartItem->delete();
            Log::info("Product " . $id . " removed from cart for user " . Auth::id());


}
            DB::commit();

            return response()->json([
                "success" => true,
                "msg" => "Product removed from cart successfully",
                "data" => []
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "msg" => $e->getMessage(),
                "data" => []
            ], 500);
        }
    }
}
