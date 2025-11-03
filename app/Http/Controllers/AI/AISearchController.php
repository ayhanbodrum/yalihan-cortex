<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AISearchController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'AI Search endpoint - to be implemented']);
    }
}
