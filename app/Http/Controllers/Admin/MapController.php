<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class MapController extends AdminController
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Map endpoint - to be implemented']);
    }
}
