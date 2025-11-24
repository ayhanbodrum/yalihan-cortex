<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Services\AI\ImageBasedAIDescriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ImageAIControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test analyze image endpoint - success
     */
    public function test_analyze_image_success(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mock ImageBasedAIDescriptionService
        $mockService = Mockery::mock(ImageBasedAIDescriptionService::class);
        $mockService->shouldReceive('analyzeImage')
            ->once()
            ->andReturn([
                'success' => true,
                'analysis' => [
                    'description' => 'Modern bir daire',
                    'objects' => ['koltuk', 'masa'],
                    'colors' => ['beyaz', 'gri']
                ]
            ]);

        $this->app->instance(ImageBasedAIDescriptionService::class, $mockService);

        $file = UploadedFile::fake()->image('test-image.jpg', 800, 600);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/image/analyze', [
                'image' => $file,
                'options' => [
                    'detail' => 'high',
                    'include_objects' => true
                ]
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data',
                    'raw_analysis'
                ]
            ]);
    }

    /**
     * Test analyze image endpoint - validation error
     */
    public function test_analyze_image_validation_error(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/image/analyze', [
                // Missing required 'image' field
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
     * Test analyze image endpoint - invalid file type
     */
    public function test_analyze_image_invalid_file_type(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $file = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/image/analyze', [
                'image' => $file
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test generate tags endpoint - success
     */
    public function test_generate_tags_success(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mock ImageBasedAIDescriptionService
        $mockService = Mockery::mock(ImageBasedAIDescriptionService::class);
        $mockService->shouldReceive('generateTags')
            ->once()
            ->andReturn(['modern', 'daire', 'balkon', 'manzara']);

        $this->app->instance(ImageBasedAIDescriptionService::class, $mockService);

        $file = UploadedFile::fake()->image('test-image.jpg', 800, 600);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/image/tags', [
                'image' => $file
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'tags',
                    'tag_count'
                ]
            ]);
    }

    /**
     * Test generate tags endpoint - validation error
     */
    public function test_generate_tags_validation_error(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/admin/ai/image/tags', [
                // Missing required 'image' field
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
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test-image.jpg');

        $response = $this->postJson('/api/admin/ai/image/analyze', [
            'image' => $file
        ]);

        $response->assertStatus(401);
    }
}

