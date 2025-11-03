<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Context7TelegramWebhookController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Context7 Telegram Webhook endpoint - to be implemented']);
    }
}
