<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminAIControllerTest extends TestCase
{
    public function test_chat_endpoint_requires_auth()
    {
        $response = $this->post('/api/admin/ai/chat', ['user_msg' => 'test']);
        $response->assertStatus(302);
    }

    public function test_price_predict_basic_flow()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $payload = ['property_data' => ['area' => 100, 'city' => 'İzmir']];
        $response = $this->post('/api/admin/ai/price/predict', $payload);
        $response->assertStatus(200)->assertJsonStructure(['success', 'data' => ['predicted_price', 'currency', 'confidence']]);
    }

    public function test_analytics_endpoint()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/api/admin/ai/analytics');
        $response->assertStatus(200)->assertJsonStructure(['success', 'data' => ['average_response_time', 'success_rate', 'error_rate', 'provider_usage', 'total_requests']]);
    }
}
