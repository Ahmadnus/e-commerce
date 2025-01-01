<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Requests\AdressRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\DB;

class AdressesController extends Controller
{
    public function index()
    {
        return Address::where('user_id', Auth::id())->get();
    }

    public function store(AdressRequest $request)
    {
        DB::beginTransaction();
        try {
            $lang = app()->getLocale();
            if ($request->default) {
                Address::where('user_id', Auth::id())->update(['default' => false]);
            }

            $address = Address::create([
                'name' => $request->name,
                'address' => $request->address,
                'lang' => $request->lang,
                'lat' => $request->lat,
                'city_id' => $request->city_id,
                'user_id' => Auth::id(),
                'default' => $request->default ?? false,
            ]);

            DB::commit();

            return response()->json(['message' => trans('Order created successfully'), 'data' => $address], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => trans('Failed to create address'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(AdressRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $lang = app()->getLocale();
            $address = Address::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            if ($request->default) {
                Address::where('user_id', Auth::id())->update(['default' => false]);
            }

            $address->update($request->validated());

            DB::commit();

            return response()->json(['message' => trans('Order updated successfully'), 'data' => $address], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => trans('Address not found or unauthorized access'),
                'error' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => trans('Failed to update address'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $lang = app()->getLocale();
            $address = Address::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $address->delete();

            DB::commit();

            return response()->json(['message' => trans('Address deleted successfully')], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => trans('Address not found or unauthorized access'),
                'error' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => trans('Failed to delete address'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
