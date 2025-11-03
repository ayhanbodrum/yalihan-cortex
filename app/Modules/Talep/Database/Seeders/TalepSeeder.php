<?php

namespace App\Modules\Talep\Database\Seeders;

use App\Models\Talep;
use Illuminate\Database\Seeder;

class TalepSeeder extends Seeder
{
    public function run()
    {
        Talep::factory()->count(50)->create([
            'status' => 'active',
            'oncelik' => 'normal',
        ]);
    }
}
