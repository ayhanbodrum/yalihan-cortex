<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class YazlikKiralamaControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test YazlikKiralamaController index page
     */
    public function test_yazlik_kiralama_controller_index(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/yazlik-kiralama');

        $response->assertStatus(200);
    }

    /**
     * Test YazlikKiralamaController requires authentication
     */
    public function test_yazlik_kiralama_controller_requires_authentication(): void
    {
        $response = $this->get('/admin/yazlik-kiralama');

        // Should redirect to login
        $response->assertStatus(302);
    }

    /**
     * Test YazlikKiralamaController with filters
     */
    public function test_yazlik_kiralama_controller_with_filters(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/yazlik-kiralama?status=Aktif&il_id=1');

        $response->assertStatus(200);
    }

    /**
     * Test YazlikKiralamaController price range filter
     */
    public function test_yazlik_kiralama_controller_price_range(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/yazlik-kiralama?min_fiyat=1000&max_fiyat=5000');

        $response->assertStatus(200);
    }

    /**
     * Test YazlikKiralamaController search functionality
     */
    public function test_yazlik_kiralama_controller_search(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/yazlik-kiralama?search=test');

        $response->assertStatus(200);
    }
}
