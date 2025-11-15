<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class AdminCreatePageTest extends TestCase
{
    public function test_admin_create_page_is_accessible()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/ilanlar/create');

        $response->assertStatus(200);
        $response->assertSee('Yeni İlan Oluştur');
        $response->assertSee('ilan-create-form');
    }
}