<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class ReportingController extends AdminController
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Reporting endpoint - to be implemented']);
    }
}
