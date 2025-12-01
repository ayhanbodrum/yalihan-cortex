<?php

namespace Database\Seeders;

use App\Models\Il;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MuglaIlceleriSeeder extends Seeder
{
    /**
     * Muğla ilçeleri seeder
     *
     * Run with: php artisan db:seed --class=MuglaIlceleriSeeder
     */
    public function run(): void
    {
        $this->command->info('🏘️ Muğla ilçeleri ekleniyor...');

        // Muğla'yı bul
        $mugla = Il::where('plaka_kodu', '48')->first();

        if (! $mugla) {
            $this->command->error('❌ Muğla ili bulunamadı! Önce TurkiyeIlleriSeeder çalıştırın.');

            return;
        }

        $districts = [
            ['il_id' => $mugla->id, 'ilce_adi' => 'Bodrum', 'ilce_kodu' => '4801'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Milas', 'ilce_kodu' => '4802'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Fethiye', 'ilce_kodu' => '4803'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Marmaris', 'ilce_kodu' => '4804'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Datça', 'ilce_kodu' => '4805'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Köyceğiz', 'ilce_kodu' => '4806'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Ula', 'ilce_kodu' => '4807'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Yatağan', 'ilce_kodu' => '4808'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Ortaca', 'ilce_kodu' => '4809'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Dalaman', 'ilce_kodu' => '4810'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Seydikemer', 'ilce_kodu' => '4811'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Kavaklıdere', 'ilce_kodu' => '4812'],
            ['il_id' => $mugla->id, 'ilce_adi' => 'Merkez', 'ilce_kodu' => '4813'],
        ];

        foreach ($districts as $district) {
            $district['created_at'] = now();
            $district['updated_at'] = now();
            $district['status'] = true;

            DB::table('ilceler')->updateOrInsert(
                ['il_id' => $district['il_id'], 'ilce_adi' => $district['ilce_adi']],
                $district
            );
        }

        $this->command->info('✅ '.count($districts).' ilçe eklendi (Muğla)');
    }
}
