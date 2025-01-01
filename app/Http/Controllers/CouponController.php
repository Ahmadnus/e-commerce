<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Resources\CouponResource;

class CouponController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized: You must be logged in to perform this action'], 401);
        }

        if ($user->is_admin === 1) {
            $coupons = Coupon::all();

            return response()->json(CouponResource::collection($coupons));
        } else {
            return response()->json(['message' => 'Unauthorized: Only admins can view coupons'], 403);
        }
    }

    public function store(StoreCouponRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized: You must be logged in to perform this action'], 401);
        }

        if ($user->is_admin !== 1) {
            return response()->json(['message' => 'Unauthorized: Only admins can create coupons'], 403);
        }

        DB::beginTransaction();

        try {
            $coupon = Coupon::create([
                'code' => $request->code,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'usage_limit' => $request->usage_limit,
                'is_active' => $request->is_active,
            ]);

            DB::commit();

            return response()->json(['message' => 'Coupon created successfully', 'data' => $coupon], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'An error occurred while creating the coupon', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(StoreCouponRequest $request, Coupon $coupon)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized: You must be logged in to perform this action'], 401);
        }

        if ($user->is_admin !== 1) {
            return response()->json(['message' => 'Unauthorized: Only admins can update coupons'], 403);
        }

        DB::beginTransaction();

        try {
            $coupon->update([
                'code' => $request->code,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'usage_limit' => $request->usage_limit,
                'is_active' => $request->is_active,
            ]);

            DB::commit();

            return response()->json(['message' => 'Coupon updated successfully', 'data' => $coupon], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'An error occurred while updating the coupon', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Coupon $coupon)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized: You must be logged in to perform this action'], 401);
        }

        if ($user->is_admin !== 1) {
            return response()->json(['message' => 'Unauthorized: Only admins can delete coupons'], 403);
        }

        DB::beginTransaction();

        try {
            $coupon->delete();

            DB::commit();

            return response()->json(['message' => 'Coupon deleted successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'An error occurred while deleting the coupon', 'error' => $e->getMessage()], 500);
        }
    }
}
