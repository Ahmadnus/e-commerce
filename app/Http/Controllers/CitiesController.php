<?php

namespace App\Http\Controllers;

use App\Models\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreCitiesRequest;
use Exception;

class CitiesController extends Controller
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
            $cities = Cities::all();
            return response()->json($cities);
        } else {
            return response()->json(['massege' => 'no bro u cant']);
        }
    }

    public function store(StoreCitiesRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized: You must be logged in to perform this action',
            ], 401);
        }

        if ($user->is_admin === 1) {
            $request->validate([
                'name' => 'required|string|unique:cities',
            ]);

            DB::beginTransaction();

            try {
                $city = Cities::create([
                    'name' => $request->name,
                ]);

                DB::commit();
                Log::info("City created successfully: " . $city->name);

                return response()->json([
                    'message' => 'City created successfully',
                    'data' => $city,
                ], 201);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error("Error creating city: " . $e->getMessage());
                return response()->json([
                    'message' => 'An error occurred while creating the city',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized: Only admins can create cities',
            ], 403);
        }
    }

    public function update(Request $request, Cities $city)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized: You must be logged in to perform this action',
            ], 401);
        }

        if ($user->is_admin === 1) {
            $request->validate([
                'name' => 'required|string|unique:cities,name,' . $city->id,
            ]);

            DB::beginTransaction();

            try {
                $city->update([
                    'name' => $request->name,
                ]);

                DB::commit();
                Log::info("City updated successfully: " . $city->name);

                return response()->json([
                    'message' => 'City updated successfully',
                    'data' => $city,
                ], 200);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error("Error updating city: " . $e->getMessage());
                return response()->json([
                    'message' => 'An error occurred while updating the city',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized: Only admins can update cities',
            ], 403);
        }
    }

    public function destroy(Cities $city)
    {
        DB::beginTransaction();

        try {
            $city->delete();
            DB::commit();
            Log::info("City deleted successfully: " . $city->name);

            return response()->json(['message' => 'City deleted successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error deleting city: " . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while deleting the city',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
