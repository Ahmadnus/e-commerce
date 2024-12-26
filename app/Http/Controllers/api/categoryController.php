<?php

namespace App\Http\Controllers\api;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class categoryController extends Controller
{
    public function showCategory()
{
    $categories = Category::all();

    return response()->json([
        "success" => true,
        "msg" => "Categories retrieved successfully",
        "data" => $categories
    ]);
}
}
