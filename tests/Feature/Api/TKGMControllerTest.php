<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Services\TKGMService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class TKGMControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test parsel sorgula endpoint - success
     */
    public function test_parsel_sorgula_success(): void
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
            ->with('123', '456', 'İstanbul', 'Kadıköy')
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

        $response = $this->actingAs($user)
            ->postJson('/api/tkgm/parsel-sorgu', [
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
                    'ada',
                    'parsel',
                    'il',
                    'ilce'
                ]
            ]);
    }

    /**
     * Test parsel sorgula endpoint - validation error
     */
    public function test_parsel_sorgula_validation_error(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/tkgm/parsel-sorgu', [
                'ada' => '',
                'parsel' => '456',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'errors'
            ]);
    }

    /**
     * Test parsel sorgula endpoint - service error
     */
    public function test_parsel_sorgula_service_error(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mock TKGMService with error
        $mockService = Mockery::mock(TKGMService::class);
        $mockService->shouldReceive('parselSorgula')
            ->once()
            ->andReturn([
                'success' => false,
                'message' => 'Parsel bulunamadı'
            ]);

        $this->app->instance(TKGMService::class, $mockService);

        $response = $this->actingAs($user)
            ->postJson('/api/tkgm/parsel-sorgu', [
                'ada' => '123',
                'parsel' => '456',
                'il' => 'İstanbul',
                'ilce' => 'Kadıköy'
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test yatirim analizi endpoint - success
     */
    public function test_yatirim_analizi_success(): void
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

    /**
     * Test yatirim analizi endpoint - parsel not found
     */
    public function test_yatirim_analizi_parsel_not_found(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mock TKGMService with error
        $mockService = Mockery::mock(TKGMService::class);
        $mockService->shouldReceive('parselSorgula')
            ->once()
            ->andReturn([
                'success' => false,
                'message' => 'Parsel bulunamadı'
            ]);

        $this->app->instance(TKGMService::class, $mockService);

        $response = $this->actingAs($user)
            ->postJson('/api/tkgm/yatirim-analizi', [
                'ada' => '123',
                'parsel' => '456',
                'il' => 'İstanbul',
                'ilce' => 'Kadıköy'
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test health check endpoint - success
     */
    public function test_health_check_success(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mock TKGMService
        $mockService = Mockery::mock(TKGMService::class);
        $mockService->shouldReceive('healthCheck')
            ->once()
            ->andReturn([
                'status' => 'ok',
                'message' => 'Service is healthy'
            ]);

        $this->app->instance(TKGMService::class, $mockService);

        $response = $this->actingAs($user)
            ->getJson('/api/tkgm/health');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);
    }

    /**
     * Test health check endpoint - service unhealthy
     */
    public function test_health_check_unhealthy(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mock TKGMService
        $mockService = Mockery::mock(TKGMService::class);
        $mockService->shouldReceive('healthCheck')
            ->once()
            ->andReturn([
                'status' => 'error',
                'message' => 'Service is down'
            ]);

        $this->app->instance(TKGMService::class, $mockService);

        $response = $this->actingAs($user)
            ->getJson('/api/tkgm/health');

        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test clear cache endpoint
     */
    public function test_clear_cache(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mock TKGMService
        $mockService = Mockery::mock(TKGMService::class);
        $mockService->shouldReceive('clearCache')
            ->once()
            ->with('123', '456', 'İstanbul', 'Kadıköy')
            ->andReturn(true);

        $this->app->instance(TKGMService::class, $mockService);

        $response = $this->actingAs($user)
            ->postJson('/api/tkgm/clear-cache', [
                'ada' => '123',
                'parsel' => '456',
                'il' => 'İstanbul',
                'ilce' => 'Kadıköy'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test endpoints require authentication
     */
    public function test_endpoints_require_authentication(): void
    {
        $response = $this->postJson('/api/tkgm/parsel-sorgu', [
            'ada' => '123',
            'parsel' => '456',
            'il' => 'İstanbul',
            'ilce' => 'Kadıköy'
        ]);

        // Should return 401 or redirect to login
        $response->assertStatus(401);
    }
}

