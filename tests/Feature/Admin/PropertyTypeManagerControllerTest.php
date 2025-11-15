<?php

namespace Tests\Feature\Admin;

use App\Models\IlanKategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PropertyTypeManagerControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test PropertyTypeManagerController index page
     */
    public function test_property_type_manager_controller_index(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/property-type-manager');

        $response->assertStatus(200);
    }

    /**
     * Test PropertyTypeManagerController show method
     */
    public function test_property_type_manager_controller_show(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $kategori = IlanKategori::create([
            'name' => 'Test Kategori',
            'slug' => 'test-kategori',
            'status' => true,
            'display_order' => 0,
        ]);

        $response = $this->actingAs($user)
            ->get("/admin/property-type-manager/{$kategori->id}");

        $response->assertStatus(200);
    }

    /**
     * Test PropertyTypeManagerController updateFieldOrder method
     */
    public function test_property_type_manager_controller_update_field_order(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $kategori = IlanKategori::create([
            'name' => 'Test Kategori',
            'slug' => 'test-kategori',
            'status' => true,
            'display_order' => 0,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/admin/property-type-manager/{$kategori->id}/update-field-order", [
                'fields' => [
                    ['id' => 1, 'display_order' => 1],
                    ['id' => 2, 'display_order' => 2],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);
    }

    /**
     * Test PropertyTypeManagerController bulkSave method
     */
    public function test_property_type_manager_controller_bulk_save(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $kategori = IlanKategori::create([
            'name' => 'Test Kategori',
            'slug' => 'test-kategori',
            'status' => true,
            'display_order' => 0,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/admin/property-type-manager/{$kategori->id}/bulk-save", [
                'features' => [
                    ['id' => 1, 'enabled' => true],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);
    }

    /**
     * Test PropertyTypeManagerController requires authentication
     */
    public function test_property_type_manager_controller_requires_authentication(): void
    {
        $response = $this->get('/admin/property-type-manager');

        // Should redirect to login
        $response->assertStatus(302);
    }

    /**
     * Test PropertyTypeManagerController with invalid category
     */
    public function test_property_type_manager_controller_with_invalid_category(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/property-type-manager/99999');

        // Should return 404 or redirect
        $response->assertStatus(404);
    }
}
