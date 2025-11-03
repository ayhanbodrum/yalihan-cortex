<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class NotificationController extends AdminController
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Notification endpoint - to be implemented']);
    }
}
