<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DanismanController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Danisman endpoint - to be implemented']);
    }
}
