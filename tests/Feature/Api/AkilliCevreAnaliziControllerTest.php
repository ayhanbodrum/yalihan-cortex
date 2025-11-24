<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Services\AkilliCevreAnaliziService;
use App\Services\AIAkilliOnerilerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AkilliCevreAnaliziControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test analyze environment endpoint - success
     */
    public function test_analyze_environment_success(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mock services
        $mockCevreService = Mockery::mock(AkilliCevreAnaliziService::class);
        $mockCevreService->shouldReceive('analyzeNearbyEnvironment')
            ->once()
            ->with(41.0082, 28.9784, 'arsa')
            ->andReturn([
                'pois' => [
                    ['name' => 'Okul', 'distance' => 500],
                    ['name' => 'Market', 'distance' => 300]
                ],
                'transport' => [
                    'bus_stops' => 2,
                    'metro_stations' => 1
                ]
            ]);

        $this->app->instance(AkilliCevreAnaliziService::class, $mockCevreService);
        $this->app->instance(AIAkilliOnerilerService::class, Mockery::mock(AIAkilliOnerilerService::class));

        $response = $this->actingAs($user)
            ->postJson('/api/akilli-cevre-analizi/analyze', [
                'latitude' => 41.0082,
                'longitude' => 28.9784,
                'property_type' => 'arsa'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'pois',
                    'transport'
                ]
            ]);
    }

    /**
     * Test analyze environment endpoint - validation error
     */
    public function test_analyze_environment_validation_error(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/cevre-analizi', [
                'latitude' => 200, // Invalid latitude
                'longitude' => 28.9784,
                'property_type' => 'arsa'
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
     * Test get smart recommendations endpoint - success
     */
    public function test_get_smart_recommendations_success(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mock services
        $mockAIService = Mockery::mock(AIAkilliOnerilerService::class);
        $mockAIService->shouldReceive('getSmartRecommendations')
            ->once()
            ->andReturn([
                'recommendations' => [
                    'fiyat' => 'Fiyat piyasa ortalamasının üzerinde',
                    'lokasyon' => 'Merkezi konumda'
                ]
            ]);

        $this->app->instance(AkilliCevreAnaliziService::class, Mockery::mock(AkilliCevreAnaliziService::class));
        $this->app->instance(AIAkilliOnerilerService::class, $mockAIService);

        $response = $this->actingAs($user)
            ->getJson('/api/admin/cevre-analizi/smart-recommendations', [
                'ilan_data' => json_encode([
                    'baslik' => 'Test İlan',
                    'fiyat' => 1000000
                ]),
                'context' => 'create'
            ]);

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
     * Test get smart recommendations endpoint - validation error
     */
    public function test_get_smart_recommendations_validation_error(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/admin/cevre-analizi/smart-recommendations', [
                'ilan_data' => 'not-an-array', // Invalid
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test calculate distance endpoint - success
     */
    public function test_calculate_distance_success(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->app->instance(AkilliCevreAnaliziService::class, Mockery::mock(AkilliCevreAnaliziService::class));
        $this->app->instance(AIAkilliOnerilerService::class, Mockery::mock(AIAkilliOnerilerService::class));

        $response = $this->actingAs($user)
            ->postJson('/api/akilli-cevre-analizi/distance', [
                'from_lat' => 41.0082,
                'from_lon' => 28.9784,
                'to_lat' => 41.0182,
                'to_lon' => 28.9884,
                'mode' => 'walking'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'distance',
                    'duration',
                    'mode',
                    'from',
                    'to'
                ]
            ]);
    }

    /**
     * Test calculate distance endpoint - validation error
     */
    public function test_calculate_distance_validation_error(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/cevre-analizi/calculate-distance', [
                'from_lat' => 200, // Invalid
                'from_lon' => 28.9784,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test search POI endpoint - success
     */
    public function test_search_poi_success(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->app->instance(AkilliCevreAnaliziService::class, Mockery::mock(AkilliCevreAnaliziService::class));
        $this->app->instance(AIAkilliOnerilerService::class, Mockery::mock(AIAkilliOnerilerService::class));

        $response = $this->actingAs($user)
            ->postJson('/api/akilli-cevre-analizi/poi', [
                'latitude' => 41.0082,
                'longitude' => 28.9784,
                'query' => 'okul',
                'radius' => 1000
            ]);

        // Response might be empty if Nominatim API is not available, but should return 200
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);
    }

    /**
     * Test search POI endpoint - validation error
     */
    public function test_search_poi_validation_error(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/cevre-analizi/search-poi', [
                'latitude' => 41.0082,
                'longitude' => 28.9784,
                // Missing query
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test endpoints require authentication
     */
    public function test_endpoints_require_authentication(): void
    {
        $response = $this->postJson('/api/admin/cevre-analizi', [
            'latitude' => 41.0082,
            'longitude' => 28.9784,
            'property_type' => 'arsa'
        ]);

        $response->assertStatus(401);
    }
}

