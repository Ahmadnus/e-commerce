<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ggResource;
use App\Models\Product;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;
use LogicException;
use Illuminate\Support\Facades\Mail;
use App\Mail\gg;
use App\Mail\mass;
use Illuminate\Support\Facades\Log;
;
class productController extends Controller
{
    public function index()
    {
        try {


            $productPage = Product::with('category','media')->paginate(10);
            $pagination = [

                            "current_page" => $productPage->currentPage(),
                            "last_page" => $productPage->lastPage(),
                            "per_page" => $productPage->perPage(),
            ];

                    return response()->json([
                        "success" => true,
                        "msg" => "Products displayed successfully",
                        "data" => $productPage->items(),

                        $pagination

                    ], 200);

        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "msg" =>  $e->getMessage(),
                "data" => [],

            ], 500);
        }
    }

    public function insert(Request $request)
    {


            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
 'images' => 'required|file|mimes:jpg,jpeg,png,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "msg" => "Validation errors",
                    "errors" => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {

                $product = Product::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'stock' => $request->stock,
                    'category_id' => $request->category_id,
                    'user_id' => Auth::user()->id,

                ]);
                $fileAdders = $product
    ->addMultipleMediaFromRequest(['image1', 'image2'])
    ->each(function ($fileAdder) {
        $fileAdder->toMediaCollection();
    });




                $favorites = Favorite::where('favoritable_type', 'App\Models\Category')
                    ->where('favoritable_id', $request->category_id)
                    ->with('user')
                    ->get();

                foreach ($favorites as $favorite) {
                    $user = $favorite->user;
                    if ($user) {
                        Mail::to($user->email)->send(new gg());
                        Log::info("Email sent to user: " . $user->email . " for new product: " . $product->name);
                    }
                }

                DB::commit();

                return response()->json([
                    "success" => true,
                    "msg" => "Product created successfully, and emails sent to relevant users.",
                    "data" => $product,
                    'media' => $product->getMedia('images'),
                ], 201);
            } catch (Exception $e) {
                DB::rollBack();

                Log::error("Error creating product or sending emails: " . $e->getMessage());

                return response()->json([
                    "success" => false,
                    "msg" => $e->getMessage(),
                    "data" => []
                ], 500);

        }
    }


    public function showProduct($id)
    {
        try {
            $product = Product::with('category')->where('id',$id)->get();

                $cat=ggResource::collection($product);
                return $cat;
if($product){
            return response()->json([
                "success" => true,
                "msg" => "Product details retrieved successfully",
                "data" => $product
            ], 200);
        }
        else{
            return response()->json([
                "success" => false,
                "msg" => "not found",
                "data" => $product
            ], 200);
        }
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "msg" => 'Product not found:',
                "data" => [],
                'media' => $product->getMedia('images'),
            ], 404);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            Product::where('user_id',Auth::user()->id)->
            findOrFail($id)        ->delete();



            DB::commit();

            return response()->json([
                "success" => true,
                "msg" => "Product deleted successfully",
                "data" => []
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                "success" => false,
                "msg" => 'Error deleting product' ,
                "data" => []
            ], 500);
        }
    }
    public function update(Request $request, $id)

{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'category_id' => 'required|exists:categories,id',
    ]);

    if ($validator->fails()) {
        Log::warning("Validation errors while updating product: ", $validator->errors()->toArray());
        return response()->json([
            "success" => false,
            "msg" => "Validation errors",
            "data" => $validator->errors(),
        ], 422);
    }
{
    $product = Product::where('user_id', Auth::user()->id)->findOrFail($id);

    try {
        $product->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
            'category_id' => $request->input('category_id'),
        ]);

        Log::info("Product updated successfully for user " . Auth::id() );
        return response()->json([
            "success" => true,
            "msg" => "Product updated successfully",
            "data" => $product,
        ], 200);
    } catch (Exception $e) {
        Log::error("Error updating product for user " . Auth::id() . ", Product ID: " . $id . ". Error: " . $e->getMessage());
        return response()->json([
            "success" => false,
            "msg" => "Error updating product: " . $e->getMessage(),
            "data" => [],
        ], 500);
    }
}





}}
