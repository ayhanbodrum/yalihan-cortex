<?php

namespace Tests\Feature;

use App\Models\Ilan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IlanSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_by_referans_success(): void
    {
        $user = User::factory()->create();
        $ilan = Ilan::create(['baslik' => 'Test', 'slug' => 'test-ilan', 'para_birimi' => 'TRY', 'danisman_id' => $user->id, 'referans_no' => 'REF-001']);
        $this->actingAs($user)
            ->get('/admin/ilanlar/by-referans/REF-001')
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_find_by_portal_id_not_found(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get('/admin/ilanlar/by-portal?portal=sahibinden&id=000000')
            ->assertStatus(404);
    }
}