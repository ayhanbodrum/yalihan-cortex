<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class UserSettingsController extends AdminController
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'User Settings endpoint - to be implemented']);
    }
}
