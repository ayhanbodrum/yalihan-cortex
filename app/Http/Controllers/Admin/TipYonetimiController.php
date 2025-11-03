<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class TipYonetimiController extends AdminController
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Tip Yonetimi endpoint - to be implemented']);
    }
}
