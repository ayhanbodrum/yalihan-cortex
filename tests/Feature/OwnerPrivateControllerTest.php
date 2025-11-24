<?php

namespace Tests\Feature;

use App\Models\Ilan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerPrivateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_private_update_requires_authorization(): void
    {
        $user = User::factory()->create();
        $ilan = Ilan::create(['baslik' => 'Test', 'slug' => 'test-ilan', 'para_birimi' => 'TRY', 'danisman_id' => $user->id]);

        $payload = [
            'owner_private_desired_price_min' => 100,
            'owner_private_desired_price_max' => 200,
            'owner_private_notes' => 'Not'
        ];

        $this->actingAs($user)
            ->post(route('admin.ilanlar.owner-private', $ilan), $payload)
            ->assertRedirect();

        $fresh = $ilan->fresh();
        $priv = $fresh->owner_private_data;
        $this->assertSame(100, $priv['desired_price_min']);
        $this->assertSame(200, $priv['desired_price_max']);
        $this->assertSame('Not', $priv['notes']);
    }
}