<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\newProd;
use Illuminate\Support\Facades\Log;
use Exception;

class FavoriteController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'favoritable_id' => 'required|integer',
            'favoritable_type' => 'required|string|in:App\Models\Product,App\Models\Category',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "msg" => trans("Validation errors"),
                "data" => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {

            $exFavorite = Favorite::where('user_id', Auth::id())
                ->where('favoritable_id', $request->favoritable_id)
                ->where('favoritable_type', $request->favoritable_type)
                ->first();

            if ($exFavorite) {
                return response()->json([
                    "success" => false,
                    "msg" => trans("This product is already in your favorites."),
                    "data" => [],
                ], 409);
            }


            $favorite = Favorite::create([
                'user_id' => Auth::id(),
                'favoritable_id' => $request->favoritable_id,
                'favoritable_type' => $request->favoritable_type,
            ]);


            if ($request->favoritable_type == 'App\Models\Product') {
                $this->sendEmailNotifications($request->favoritable_id);
            }

            DB::commit();

            Log::info("Favorite created successfully for user " . Auth::id());

            return response()->json([
                "success" => true,
                "msg" => trans("Product added to favorites successfully"),
                "data" => $favorite,
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            Log::error("Error occurred while creating favorite: " . $e->getMessage());

            return response()->json([
                "success" => false,
                "msg" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }

    private function sendEmailNotifications($favoritableId)
    {
        $product = Product::find($favoritableId);

        if ($product) {
            // Get all products in the same category
            $products = Product::where('category_id', $product->category_id)
                ->with('user')
                ->get();

            foreach ($products as $n_prod) {
                if ($n_prod->user) {
                    Mail::to($n_prod->user->email)->send(new newProd);
                    Log::info("Email sent to user: " . $n_prod->user->email);
                }
            }
        }
    }

    public function show()
    {
        $user = Auth::user();
        $favorites = Favorite::where('user_id', $user->id)->get();

        return response()->json($favorites, 200);
    }

    public function delete($id)
    {
        $favorite = Favorite::findOrFail($id);
        $favorite->delete();

        return response()->json(['message' => trans('Favorite removed successfully.')], 200);
    }
}
