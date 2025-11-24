<?php

namespace Tests\Feature;

use App\Models\Ilan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerPrivateUnauthorizedTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_user_cannot_update_owner_private(): void
    {
        $danisman = User::factory()->create();
        $other = User::factory()->create();
        $ilan = Ilan::create(['baslik' => 'Test', 'slug' => 'test-ilan', 'para_birimi' => 'TRY', 'danisman_id' => $danisman->id]);
        $payload = [
            'owner_private_desired_price_min' => 100,
            'owner_private_desired_price_max' => 200,
            'owner_private_notes' => 'Not'
        ];
        $this->actingAs($other)
            ->post(route('admin.ilanlar.owner-private', $ilan), $payload)
            ->assertStatus(403);
    }
}