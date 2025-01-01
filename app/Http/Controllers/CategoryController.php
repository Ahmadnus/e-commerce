<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CouponResource;
use Exception;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized: You must be logged in to perform this action'], 401);
            }

            if ($user->is_admin === 1) {

                $categories = Category::all();
                return response()->json(CouponResource::collection($categories));
            }

            return response()->json(['message' => 'Unauthorized: Access denied'], 403);
        } catch (Exception $e) {
            Log::error('Error in CategoryController@index', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch categories'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized: You must be logged in to perform this action'], 401);
            }

            if ($user->is_admin !== 1) {
                return response()->json(['message' => 'Unauthorized: Only admins can create categories'], 403);
            }

            // Validate input
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            ]);

            $category = Category::create([
                'name' => $request->name,
            ]);


            $category->addMediaFromRequest('image')->toMediaCollection();

            return response()->json(['message' => 'Category created successfully', 'data' => $category], 201);
        } catch (Exception $e) {
            Log::error('Error in CategoryController@store', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to create category', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Category $category)
    {
        try {
            return response()->json($category, 200);
        } catch (Exception $e) {
            Log::error('Error in CategoryController@show', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch category details'], 500);
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized: You must be logged in to perform this action'], 401);
            }

            if ($user->is_admin !== 1) {
                return response()->json(['message' => 'Unauthorized: Only admins can update categories'], 403);
            }

            // Validate input
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $category->update(['name' => $request->name]);

            return response()->json(['message' => 'Category updated successfully', 'data' => $category], 200);
        } catch (Exception $e) {
            Log::error('Error in CategoryController@update', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update category', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized: You must be logged in to perform this action'], 401);
            }

            if ($user->is_admin !== 1) {
                return response()->json(['message' => 'Unauthorized: Only admins can delete categories'], 403);
            }

            $category->delete();

            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (Exception $e) {
            Log::error('Error in CategoryController@destroy', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete category', 'error' => $e->getMessage()], 500);
        }
    }
}
