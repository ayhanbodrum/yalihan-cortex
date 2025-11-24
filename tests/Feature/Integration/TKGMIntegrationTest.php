<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use App\Services\TKGMService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

/**
 * TKGM Integration Test
 * 
 * Tests the complete TKGM workflow from request to response
 */
class TKGMIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test complete TKGM parsel sorgulama workflow
     */
    public function test_complete_tkgm_parsel_workflow(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mock TKGMService
        $mockService = Mockery::mock(TKGMService::class);
        $mockService->shouldReceive('parselSorgula')
            ->once()
            ->andReturn([
                'success' => true,
                'parsel_bilgileri' => [
                    'ada' => '123',
                    'parsel' => '456',
                    'il' => 'İstanbul',
                    'ilce' => 'Kadıköy',
                    'alan' => 500,
                    'imar_durumu' => 'İmar var'
                ]
            ]);

        $this->app->instance(TKGMService::class, $mockService);

        // Step 1: Request parsel sorgulama
        $response = $this->actingAs($user)
            ->postJson('/api/tkgm/parsel-sorgu', [
                'ada' => '123',
                'parsel' => '456',
                'il' => 'İstanbul',
                'ilce' => 'Kadıköy'
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

        // Step 3: Verify data structure
        $data = $response->json('data');
        $this->assertArrayHasKey('ada', $data);
        $this->assertArrayHasKey('parsel', $data);
    }

    /**
     * Test TKGM yatırım analizi workflow
     */
    public function test_tkgm_yatirim_analizi_workflow(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mock TKGMService
        $mockService = Mockery::mock(TKGMService::class);
        $mockService->shouldReceive('parselSorgula')
            ->once()
            ->andReturn([
                'success' => true,
                'parsel_bilgileri' => [
                    'ada' => '123',
                    'parsel' => '456',
                    'alan' => 500
                ]
            ]);
        $mockService->shouldReceive('yatirimAnalizi')
            ->once()
            ->andReturn([
                'potansiyel' => 'Yüksek',
                'risk' => 'Düşük'
            ]);

        $this->app->instance(TKGMService::class, $mockService);

        $response = $this->actingAs($user)
            ->postJson('/api/tkgm/yatirim-analizi', [
                'ada' => '123',
                'parsel' => '456',
                'il' => 'İstanbul',
                'ilce' => 'Kadıköy'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'parsel_bilgileri',
                    'yatirim_analizi'
                ]
            ]);
    }
}

