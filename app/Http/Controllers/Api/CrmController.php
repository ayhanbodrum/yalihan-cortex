<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'CRM endpoint - to be implemented']);
    }
}
