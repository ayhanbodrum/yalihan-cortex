<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AIChatController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'AI Chat endpoint - to be implemented']);
    }
}
