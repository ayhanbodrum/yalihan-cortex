<?php

namespace App\Services\AI;

use App\Services\AIService;

class PriceService
{
    public function __construct(private AIService $ai) {}

    public function predict(array $payload): array
    {
        $context = $payload['context'] ?? $payload;
        $result = $this->ai->analyze($context, ['type' => 'price']);
        $price = $result['price'] ?? ($result['data']['price'] ?? null);

        return [
            'success' => true,
            'data' => [
                'suggested_price' => $price,
                'meta' => $result['meta'] ?? [],
            ],
            'message' => 'Fiyat tahmini tamamlandı',
        ];
    }
}
