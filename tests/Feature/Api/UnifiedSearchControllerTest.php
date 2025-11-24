<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UnifiedSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /**
     * Test search endpoint - success
     */
    public function test_search_success(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/unified-search?q=test&types[]=all');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total',
                    'ilanlar',
                    'kategoriler',
                    'kisiler',
                    'lokasyonlar'
                ]
            ]);
    }

    /**
     * Test search endpoint - with cache
     */
    public function test_search_with_cache(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $cacheKey = 'unified_search_' . md5('test' . serialize([]) . serialize(['all']));
        $cachedData = [
            'total' => 10,
            'ilanlar' => ['items' => [], 'total' => 5]
        ];
        Cache::put($cacheKey, $cachedData, 300);

        $response = $this->actingAs($user)
            ->getJson('/api/unified-search?q=test&types[]=all');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test search endpoint - with filters
     */
    public function test_search_with_filters(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/search/unified', [
                'q' => 'test',
                'filters' => ['kategori' => 'Konut'],
                'types' => ['ilanlar']
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test suggestions endpoint - success
     */
    public function test_suggestions_success(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/search/suggestions?q=test');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'text',
                        'type'
                    ]
                ]
            ]);
    }

    /**
     * Test analytics endpoint - success
     */
    public function test_analytics_success(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/search/analytics');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total_searches',
                    'popular_queries',
                    'search_success_rate'
                ]
            ]);
    }

    /**
     * Test update cache endpoint
     */
    public function test_update_cache(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Put some cache data
        Cache::put('test_key', 'test_value', 300);

        $response = $this->actingAs($user)
            ->postJson('/api/search/cache');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Verify cache is cleared
        $this->assertNull(Cache::get('test_key'));
    }

    /**
     * Test endpoints require authentication
     */
    public function test_endpoints_require_authentication(): void
    {
        $response = $this->getJson('/api/search/unified?q=test');

        $response->assertStatus(401);
    }
}

