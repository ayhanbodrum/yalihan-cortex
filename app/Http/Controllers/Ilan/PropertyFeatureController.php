<?php

namespace App\Http\Controllers\Ilan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PropertyFeatureController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Property Feature endpoint - to be implemented']);
    }
}
