<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AiMonitorEndpointsTest extends TestCase
{
    use WithFaker;

    protected function actingAsAdmin()
    {
        $user = User::query()->first();
        if (!$user) {
            $user = User::factory()->create();
        }
        // DB bağımlılığını azaltmak için kullanıcıyı oluşturup kaydetmeden oturum açıyoruz
        $user = User::factory()->make(['id' => 1]);
        return $this->actingAs($user);
    }

    public function test_code_health_endpoint_returns_json()
    {
        $this->actingAsAdmin()
            ->get('/admin/ai-monitor/code-health')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_issues',
                    'health_score',
                    'issues',
                ],
            ]);
    }

    public function test_duplicates_endpoint_returns_json()
    {
        $this->actingAsAdmin()
            ->get('/admin/ai-monitor/duplicates')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_conflicts_endpoint_returns_json()
    {
        $this->actingAsAdmin()
            ->get('/admin/ai-monitor/conflicts')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_pages_health_endpoint_returns_json()
    {
        $this->actingAsAdmin()
            ->get('/admin/ai-monitor/pages-health')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_run_context7_fix_returns_suggestions()
    {
        $this->actingAsAdmin()
            ->post('/admin/ai-monitor/run-context7-fix')
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'suggestions',
                'action',
            ]);
    }

    public function test_apply_suggestion_requires_manual()
    {
        $this->actingAsAdmin()
            ->postJson('/admin/ai-monitor/apply-suggestion', [
                'suggestion' => 'Context7 kontrol çalıştırın',
                'index' => 0,
            ])
            ->assertStatus(200)
            ->assertJson([
                'manual_required' => true,
            ]);
    }
}
