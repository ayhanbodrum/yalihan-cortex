<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class CustomerProfileController extends AdminController
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Customer Profile endpoint - to be implemented']);
    }
}
