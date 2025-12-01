<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

class AdminContext7ViewTest extends TestCase
{
    public function test_context7_view_renders_with_array_rules()
    {
        $metrics = [
            'rpm' => 10,
            'avg_ms' => 12,
            'uptime' => 100,
            'p95_ms' => 50,
            'p99_ms' => 80,
            'rpm15' => 30,
            'avg15' => 20,
            'rpm60' => 100,
            'avg60' => 22,
        ];

        $rules = [
            'version' => ['major' => 1, 'minor' => 0],
            'forbidden' => [['op' => 'DROP TABLE'], 'SELECT * FROM secrets'],
            'required' => [['header' => 'Authorization'], 'Content-Type: application/json'],
            'forbidden_count' => 2,
            'required_count' => 2,
        ];

        $html = View::make('admin.context7.index', compact('metrics', 'rules'))->render();
        $this->assertStringContainsString('Context7 Kurallar', $html);
        $this->assertStringContainsString('Forbidden', $html);
        $this->assertStringContainsString('Required', $html);
    }
}
