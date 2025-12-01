<?php

namespace App\Modules\Analitik\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnalitikDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('maintenance_alerts')->insert([
            ['component' => 'cache', 'status' => 'ok', 'payload' => json_encode(['driver' => 'file']), 'threshold' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['component' => 'database', 'status' => 'ok', 'payload' => json_encode(['connection' => 'mysql']), 'threshold' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
