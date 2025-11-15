<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NearbyPlacesController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Nearby Places endpoint - to be implemented']);
    }
}
