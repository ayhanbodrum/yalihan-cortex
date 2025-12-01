<?php

namespace Database\Seeders;

use App\Models\Il;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AydinIlceleriSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏘️ Aydın ilçeleri ekleniyor...');

        $aydin = Il::where('plaka_kodu', '09')->first();

        if (! $aydin) {
            $this->command->error('❌ Aydın ili bulunamadı!');

            return;
        }

        $districts = [
            ['il_id' => $aydin->id, 'ilce_adi' => 'Merkez', 'ilce_kodu' => '0901'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Didim', 'ilce_kodu' => '0902'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Kuşadası', 'ilce_kodu' => '0903'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Söke', 'ilce_kodu' => '0904'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Nazilli', 'ilce_kodu' => '0905'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Efeler', 'ilce_kodu' => '0906'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Germencik', 'ilce_kodu' => '0907'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Bozdoğan', 'ilce_kodu' => '0908'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'İncirliova', 'ilce_kodu' => '0909'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Köşk', 'ilce_kodu' => '0910'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Kuyucak', 'ilce_kodu' => '0911'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Çine', 'ilce_kodu' => '0912'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Sultanhisar', 'ilce_kodu' => '0913'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Yenipazar', 'ilce_kodu' => '0914'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Karacasu', 'ilce_kodu' => '0915'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Karpuzlu', 'ilce_kodu' => '0916'],
            ['il_id' => $aydin->id, 'ilce_adi' => 'Koçarlı', 'ilce_kodu' => '0917'],
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

        $this->command->info('✅ '.count($districts).' ilçe eklendi (Aydın)');
    }
}
