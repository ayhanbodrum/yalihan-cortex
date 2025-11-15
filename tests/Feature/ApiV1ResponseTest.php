<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiV1ResponseTest extends TestCase
{
    public function test_kisiler_list_returns_standard_response()
    {
        $this->withoutMiddleware();
        $response = $this->getJson('/api/admin/api/v1/kisiler?per_page=2');
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data',
            'timestamp',
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
        ]);
    }

    public function test_danismanlar_list_returns_standard_response()
    {
        $this->withoutMiddleware();
        $response = $this->getJson('/api/admin/api/v1/danismanlar?per_page=2');
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data',
            'timestamp',
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
        ]);
    }

    public function test_talepler_list_returns_standard_response()
    {
        $this->withoutMiddleware();
        $response = $this->getJson('/api/admin/api/v1/talepler?per_page=2');
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data',
            'timestamp',
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
        ]);
    }

    public function test_ilanlar_list_returns_standard_response()
    {
        $this->withoutMiddleware();
        $response = $this->getJson('/api/admin/api/v1/ilanlar?per_page=2');
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'message',
            'data',
            'timestamp',
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
        ]);
    }
}