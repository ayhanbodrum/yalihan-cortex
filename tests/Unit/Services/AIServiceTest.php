<?php

namespace Tests\Unit\Services;

use App\Services\AIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AIServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AIService $aiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->aiService = new AIService();
    }

    /**
     * Test AIService analyze method
     */
    public function test_ai_service_analyze(): void
    {
        $data = [
            'baslik' => 'Test İlan',
            'tip' => 'Satılık',
            'kategori_id' => 1,
        ];

        $result = $this->aiService->analyze($data, []);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('priority', $result);
    }

    /**
     * Test AIService suggest method
     */
    public function test_ai_service_suggest(): void
    {
        $context = [
            'category' => 'test',
            'type' => 'general',
        ];

        $result = $this->aiService->suggest($context, 'general');

        $this->assertIsArray($result);
    }

    /**
     * Test AIService generate method
     */
    public function test_ai_service_generate(): void
    {
        $prompt = 'Test prompt';
        $options = ['test' => 'options'];

        $result = $this->aiService->generate($prompt, $options);

        $this->assertIsString($result);
    }

    /**
     * Test AIService healthCheck method
     */
    public function test_ai_service_health_check(): void
    {
        $result = $this->aiService->healthCheck();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
    }

    /**
     * Test AIService with empty data
     */
    public function test_ai_service_analyze_with_empty_data(): void
    {
        $result = $this->aiService->analyze([], []);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('category', $result);
    }

    /**
     * Test AIService with invalid context
     */
    public function test_ai_service_suggest_with_invalid_context(): void
    {
        $result = $this->aiService->suggest([], 'invalid');

        $this->assertIsArray($result);
    }
}

