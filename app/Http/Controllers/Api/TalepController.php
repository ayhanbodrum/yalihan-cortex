<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TalepController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Talep endpoint - to be implemented']);
    }
}
