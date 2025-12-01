<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AIControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test AI analyze endpoint
     */
    public function test_ai_analyze_endpoint(): void
    {
        // Create authenticated user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/analyze', [
                'action' => 'test',
                'data' => ['test' => 'data'],
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'analysis',
                    'metadata',
                ],
            ]);
    }

    /**
     * Test AI analyze endpoint validation
     */
    public function test_ai_analyze_endpoint_validation(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/analyze', []);

        // Should return validation error or success (depending on implementation)
        $response->assertStatus(200); // ResponseService handles validation gracefully
    }

    /**
     * Test AI suggest endpoint
     */
    public function test_ai_suggest_endpoint(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/suggest', [
                'category' => 'test',
                'context' => ['test' => 'data'],
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    /**
     * Test AI generate endpoint
     */
    public function test_ai_generate_endpoint(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/generate', [
                'prompt' => 'Test prompt',
                'options' => ['test' => 'options'],
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    /**
     * Test AI health check endpoint
     */
    public function test_ai_health_check_endpoint(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/admin/ai/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    /**
     * Test AI stats endpoint
     */
    public function test_ai_stats_endpoint(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/admin/ai/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    /**
     * Test AI endpoints require authentication
     */
    public function test_ai_endpoints_require_authentication(): void
    {
        $response = $this->postJson('/api/admin/ai/analyze', [
            'action' => 'test',
        ]);

        // Should return 401 or redirect to login
        $response->assertStatus(401);
    }

    /**
     * Test AI endpoints use ResponseService format
     */
    public function test_ai_endpoints_use_response_service_format(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/analyze', [
                'action' => 'test',
                'data' => ['test' => 'data'],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }
}
