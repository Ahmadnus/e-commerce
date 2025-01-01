<?php

namespace App\Http\Controllers;

use App\Models\Cities;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCitiesRequest;

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
    }
    else{
        return response()->json(['massege'=>'no bro u cant']);
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

        // Check if the authenticated user is an admin
        if ($user->is_admin === 1) {
            // Validate the incoming request data
            $request->validate([
                'name' => 'required|string|unique:cities',
            ]);

            // Create the city
            $city = Cities::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'City created successfully',
                'data' => $city,
            ], 201);
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


        $city->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'City updated successfully',
            'data' => $city,
        ], 200);
    } else {
        return response()->json([
            'message' => 'Unauthorized: Only admins can update cities',
        ], 403);
    }
}




    public function destroy(Cities $city)
    {
        $city->delete();
        return response()->json(['message' => 'City deleted successfully'], 200);
    }
}
