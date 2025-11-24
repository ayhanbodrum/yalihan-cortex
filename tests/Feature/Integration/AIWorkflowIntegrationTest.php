<?php

namespace Tests\Feature\Integration;

use App\Models\Ilan;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * AI Workflow Integration Test
 *
 * Tests the complete AI workflow from request to response
 */
class AIWorkflowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete AI analyze workflow
     */
    public function test_complete_ai_analyze_workflow(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $ilan = Ilan::create([
            'baslik' => 'Test İlan',
            'fiyat' => 1000000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
        ]);

        // Step 1: AI analyze request
        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/analyze', [
                'action' => 'analyze',
                'data' => [
                    'ilan_id' => $ilan->id,
                    'baslik' => $ilan->baslik,
                    'fiyat' => $ilan->fiyat
                ]
            ]);

        // Step 2: Verify response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);

        // Step 3: Verify AI service was called
        $this->assertNotNull($response->json('data'));
    }

    /**
     * Test AI suggest workflow
     */
    public function test_ai_suggest_workflow(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/suggest', [
                'category' => 'Konut',
                'context' => [
                    'il' => 'İstanbul',
                    'ilce' => 'Kadıköy'
                ]
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test AI generate workflow
     */
    public function test_ai_generate_workflow(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/generate', [
                'prompt' => 'Test prompt',
                'options' => [
                    'tone' => 'professional',
                    'length' => 'medium'
                ]
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}

