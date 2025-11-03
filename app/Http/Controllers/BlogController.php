<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Blog endpoint - to be implemented']);
    }
}
