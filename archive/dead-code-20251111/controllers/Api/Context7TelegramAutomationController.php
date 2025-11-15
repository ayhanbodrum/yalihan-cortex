<?php

/**
 * @deprecated Bu controller eski Context7 API sisteminin parçasıdır.
 * Yeni API endpoint'leri routes/api-admin.php ve routes/api.php içinde tanımlıdır.
 * Context7 Standard: C7-DEPRECATED-CONTEXT7-API-2025-11-05
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Context7TelegramAutomationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Context7 Telegram Automation endpoint - to be implemented']);
    }
}
