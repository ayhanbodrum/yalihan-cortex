<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ilce;

class DidimMahallelerSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏖️ Didim mahalleleri ekleniyor...');

        $didim = Ilce::where('ilce_adi', 'Didim')->first();

        if (!$didim) {
            $this->command->error('❌ Didim ilçesi bulunamadı! Önce AydinIlceleriSeeder çalıştırın.');
            return;
        }

        $mahalleler = [
            // Merkez Mahalleler
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Cumhuriyet'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Efeler'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Fevzipaşa'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Yeni'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Çamlık'],

            // Sahil Mahalleleri (Turizm Bölgeleri)
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Altınkum'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Akbük'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Sarımsaklı'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Mavişehir'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Yeşilköy'],

            // Kırsal Mahalleler
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Akköy'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Balat'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Çamlık'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Denizköy'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Evciler'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Hacıveliler'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Hisar'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Kızılcakuyu'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Kurşunlu'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Mavisehir'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Yenihisar'],
            ['ilce_id' => $didim->id, 'mahalle_adi' => 'Yıldız'],
        ];

        foreach ($mahalleler as $mahalle) {
            $mahalle['created_at'] = now();
            $mahalle['updated_at'] = now();

            DB::table('mahalleler')->updateOrInsert(
                ['ilce_id' => $mahalle['ilce_id'], 'mahalle_adi' => $mahalle['mahalle_adi']],
                $mahalle
            );
        }

        $this->command->info('✅ ' . count($mahalleler) . ' mahalle eklendi (Didim)');
    }
}
