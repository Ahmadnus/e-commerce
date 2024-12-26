<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class authController extends Controller
{
    // Signup Method
    public function signup(Request $request)
    {
        try {
            $lang = app()->getLocale();

            $validatedData = Validator::make($request->all(), [
                'name' => 'required|regex:/^[a-zA-Z\s]+$/',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6'
            ]);

            if ($validatedData->invalid()) {
                return response()->json([
                    "success" => false,
                    "msg" => trans("Validation errors"),
                    "data" => $validatedData->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                "success" => true,
                "msg" => trans("User created successfully"),
                "data" => ["user" => $user]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "msg" => $e->getMessage(),
                "data" => []
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validatedData->invalid()) {
                return response()->json([
                    "success" => false,
                    "msg" => trans("Validation errors"),
                    "data" => $validatedData->errors()
                ], 422);
            }

            $data = $request->only(["email", "password"]);
            $user = User::where('email', $request->email)->first();

            if (!Hash::check($data['password'], $user->password)) {
                return response()->json([
                    "success" => false,
                    "msg" => trans("invalde"),
                    "data" => []
                ], 401);
            }

            $token = $user->createToken("login")->plainTextToken;

            return response()->json([
                "success" => true,
                "msg" => trans("Login successful"),
                "data" => ["user" => $user, "token" => $token]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "msg" => $e->getMessage(),
                "data" => []
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();

                return response()->json([
                    "success" => true,
                    "msg" => trans("Logout successful"),
                    "data" => ["user" => $user]
                ], 200);
            }

            return response()->json([
                "success" => false,
                "msg" => trans("User not authenticated"),
                "data" => []
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "msg" => $e->getMessage(),
                "data" => []
            ], 500);
        }
    }
}
