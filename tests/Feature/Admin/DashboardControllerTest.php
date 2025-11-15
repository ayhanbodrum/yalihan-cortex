<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test DashboardController index page
     */
    public function test_dashboard_controller_index(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test DashboardController requires authentication
     */
    public function test_dashboard_controller_requires_authentication(): void
    {
        $response = $this->get('/admin/dashboard');

        // Should redirect to login
        $response->assertStatus(302);
    }

    /**
     * Test DashboardController stats endpoint
     */
    public function test_dashboard_controller_stats(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/admin/dashboard/stats');

        // Should return JSON response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    /**
     * Test DashboardController recent activities endpoint
     */
    public function test_dashboard_controller_recent_activities(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/admin/dashboard/recent-activities');

        // Should return JSON response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    /**
     * Test DashboardController with filters
     */
    public function test_dashboard_controller_with_filters(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/dashboard?period=week');

        $response->assertStatus(200);
    }
}

