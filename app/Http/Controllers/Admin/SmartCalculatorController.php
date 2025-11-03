<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class SmartCalculatorController extends AdminController
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Smart Calculator endpoint - to be implemented']);
    }
}
