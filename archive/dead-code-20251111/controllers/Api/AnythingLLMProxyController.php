<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnythingLLMService;
use Illuminate\Http\Request;

class AnythingLLMProxyController extends Controller
{
    public function health(AnythingLLMService $svc)
    {
        $h = $svc->health();
        return response()->json(['success' => $h['ok'], 'message' => $h['message'] ?? null]);
    }

    public function completions(Request $request, AnythingLLMService $svc)
    {
        $prompt = (string) $request->input('prompt', '');
        if ($prompt === '') {
            return response()->json(['success' => false, 'message' => 'prompt required'], 422);
        }

        $options = $request->only(['max_tokens', 'temperature', 'top_p', 'model']);
        $res = $svc->completions($prompt, $options);
        return response()->json(['success' => $res['ok'], 'data' => $res['data'] ?? null, 'message' => $res['message'] ?? null], $res['ok'] ? 200 : 502);
    }

    public function embeddings(Request $request, AnythingLLMService $svc)
    {
        $text = (string) $request->input('text', '');
        if ($text === '') {
            return response()->json(['success' => false, 'message' => 'text required'], 422);
        }

        $options = $request->only(['model']);
        $res = $svc->embeddings($text, $options);
        return response()->json(['success' => $res['ok'], 'data' => $res['data'] ?? null, 'message' => $res['message'] ?? null], $res['ok'] ? 200 : 502);
    }
}
