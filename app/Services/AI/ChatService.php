<?php

namespace App\Services\AI;

use App\Services\AIService;

class ChatService
{
    public function __construct(private AIService $ai)
    {
    }

    public function chat(array $payload): array
    {
        $prompt = $payload['prompt'] ?? '';
        if (!empty($payload['prompt_preset'])) {
            $lib = app(\App\Services\AI\PromptLibrary::class);
            $preset = $lib->get($payload['prompt_preset']);
            if ($preset) { $prompt = ($preset['content'] ?? '').$prompt; }
        }
        $options = $payload['options'] ?? [];
        $result = $this->ai->generate($prompt, $options);
        return [
            'success' => true,
            'data' => [
                'text' => $result['data']['text'] ?? ($result['text'] ?? ''),
                'confidence' => $result['data']['confidence'] ?? ($result['confidence'] ?? null),
            ],
            'message' => 'Chat tamamlandı',
        ];
    }
}