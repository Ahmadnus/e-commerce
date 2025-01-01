<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized: You must be logged in to perform this action',
            ], 401);
        }

        if ($user->is_admin === 1) {
            $brands = Brand::all();
            return response()->json($brands);
        } else {
            return response()->json(['message' => 'no bro u cant']);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized: You must be logged in to perform this action',
                ], 401);
            }

            if ($user->is_admin === 1) {
                $brand = Brand::create([
                    'name' => $request->name,
                    'description' => $request->description,
                ]);

                $fileAdders = $brand
                    ->addMultipleMediaFromRequest(['image'])
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection();
                    });

                DB::commit();
                Log::info('Brand created successfully', ['brand_id' => $brand->id, 'user_id' => $user->id]);

                return response()->json(['message' => 'Brand created successfully', 'data' => $brand], 201);
            } else {
                return response()->json([
                    'message' => 'Unauthorized: Only admins can create cities',
                ], 403);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create brand', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to create brand',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Brand $brand)
    {
        return $brand;
    }

    public function update(Request $request, Brand $brand)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized: You must be logged in to perform this action',
                ], 401);
            }

            if ($user->is_admin === 1) {
                $brand->update([
                    'name' => $request->name,
                    'description' => $request->description,
                ]);

                DB::commit();
                Log::info('Brand updated successfully', ['brand_id' => $brand->id, 'user_id' => $user->id]);

                return response()->json(['message' => 'Brand updated successfully', 'data' => $brand], 200);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update brand', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to update brand',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Brand $brand)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized: You must be logged in to perform this action',
                ], 401);
            }

            if ($user->is_admin == 1) {
                $brand->delete();

                DB::commit();
                Log::info('Brand deleted successfully', ['brand_id' => $brand->id, 'user_id' => $user->id]);

                return response()->json(['message' => 'Brand deleted successfully'], 200);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete brand', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to delete brand',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
