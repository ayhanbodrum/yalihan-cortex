<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Context7Controller extends Controller
{
    public function status(Request $request)
    {
        $apiKey = config('services.context7.api_key') ?? env('CONTEXT7_API_KEY');
        $configured = ! empty($apiKey);

        return response()->json([
            'success' => true,
            'configured' => $configured,
            'connected' => false,
            'transport' => 'http',
        ]);
    }
}
