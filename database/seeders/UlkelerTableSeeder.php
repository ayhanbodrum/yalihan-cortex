<?php

namespace Database\Seeders;

use App\Models\Ulke;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UlkelerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Önce tabloyu temizle
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ulke::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ülke verileri
        $ulkeler = [
            ['ulke_adi' => 'Türkiye', 'ulke_kodu' => 'TR', 'telefon_kodu' => '90', 'para_birimi' => 'TRY', 'dil' => 'tr'],
            ['ulke_adi' => 'Almanya', 'ulke_kodu' => 'DE', 'telefon_kodu' => '49', 'para_birimi' => 'EUR', 'dil' => 'de'],
            ['ulke_adi' => 'İngiltere', 'ulke_kodu' => 'GB', 'telefon_kodu' => '44', 'para_birimi' => 'GBP', 'dil' => 'en'],
            ['ulke_adi' => 'Amerika Birleşik Devletleri', 'ulke_kodu' => 'US', 'telefon_kodu' => '1', 'para_birimi' => 'USD', 'dil' => 'en'],
            ['ulke_adi' => 'Rusya', 'ulke_kodu' => 'RU', 'telefon_kodu' => '7', 'para_birimi' => 'RUB', 'dil' => 'ru'],
            ['ulke_adi' => 'Birleşik Arap Emirlikleri', 'ulke_kodu' => 'AE', 'telefon_kodu' => '971', 'para_birimi' => 'AED', 'dil' => 'ar'],
            ['ulke_adi' => 'İspanya', 'ulke_kodu' => 'ES', 'telefon_kodu' => '34', 'para_birimi' => 'EUR', 'dil' => 'es'],
            ['ulke_adi' => 'Fransa', 'ulke_kodu' => 'FR', 'telefon_kodu' => '33', 'para_birimi' => 'EUR', 'dil' => 'fr'],
            ['ulke_adi' => 'İtalya', 'ulke_kodu' => 'IT', 'telefon_kodu' => '39', 'para_birimi' => 'EUR', 'dil' => 'it'],
            ['ulke_adi' => 'Hollanda', 'ulke_kodu' => 'NL', 'telefon_kodu' => '31', 'para_birimi' => 'EUR', 'dil' => 'nl'],
        ];

        // Ülkeleri ekle
        foreach ($ulkeler as $ulke) {
            Ulke::create($ulke);
        }
    }
}
