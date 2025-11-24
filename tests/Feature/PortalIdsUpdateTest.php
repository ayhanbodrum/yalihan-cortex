<?php

namespace Tests\Feature;

use App\Models\Ilan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalIdsUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_portal_ids(): void
    {
        $user = User::factory()->create();
        $ilan = Ilan::create(['baslik' => 'Test', 'slug' => 'test-ilan', 'para_birimi' => 'TRY', 'danisman_id' => $user->id]);
        $payload = [
            'sahibinden_id' => '163868-6',
            'emlakjet_id' => 'EJ-123',
        ];
        $this->actingAs($user)
            ->post(route('admin.ilanlar.portal-ids', $ilan), $payload)
            ->assertRedirect();
        $fresh = $ilan->fresh();
        $this->assertSame('163868-6', $fresh->sahibinden_id);
        $this->assertSame('EJ-123', $fresh->emlakjet_id);
    }
}