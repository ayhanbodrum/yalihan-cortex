<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class SettingsController extends AdminController
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Settings endpoint - to be implemented']);
    }
}
