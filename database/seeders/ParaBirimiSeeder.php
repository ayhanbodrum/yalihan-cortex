<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParaBirimiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paraBirimleri = [
            [
                'kod' => 'TRY',
                'ad' => 'Türk Lirası',
                'sembol' => '₺',
                'varsayilan' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kod' => 'USD',
                'ad' => 'Amerikan Doları',
                'sembol' => '$',
                'varsayilan' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kod' => 'EUR',
                'ad' => 'Euro',
                'sembol' => '€',
                'varsayilan' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kod' => 'GBP',
                'ad' => 'İngiliz Sterlini',
                'sembol' => '£',
                'varsayilan' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('para_birimleri')->insert($paraBirimleri);
    }
}
