<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersonSearchController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Person Search endpoint - to be implemented']);
    }
}
