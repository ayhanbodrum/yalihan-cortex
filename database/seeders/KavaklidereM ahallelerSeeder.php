<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ilce;

class KavaklidereM ahallelerSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏘️ Kavaklıdere mahalleleri ekleniyor...');

        $kavaklidere = Ilce::where('ilce_adi', 'Kavaklıdere')->first();

        if (!$kavaklidere) {
            $this->command->error('❌ Kavaklıdere ilçesi bulunamadı!');
            return;
        }

        $mahalleler = [
            ['ilce_id' => $kavaklidere->id, 'mahalle_adi' => 'Merkez'],
            ['ilce_id' => $kavaklidere->id, 'mahalle_adi' => 'Belen'],
            ['ilce_id' => $kavaklidere->id, 'mahalle_adi' => 'Çamlıbel'],
            ['ilce_id' => $kavaklidere->id, 'mahalle_adi' => 'Çökek'],
            ['ilce_id' => $kavaklidere->id, 'mahalle_adi' => 'Gökova'],
            ['ilce_id' => $kavaklidere->id, 'mahalle_adi' => 'Karacahisar'],
            ['ilce_id' => $kavaklidere->id, 'mahalle_adi' => 'Karaköy'],
            ['ilce_id' => $kavaklidere->id, 'mahalle_adi' => 'Kozağacı'],
            ['ilce_id' => $kavaklidere->id, 'mahalle_adi' => 'Sarıana'],
            ['ilce_id' => $kavaklidere->id, 'mahalle_adi' => 'Yerkesik'],
        ];

        foreach ($mahalleler as $mahalle) {
            $mahalle['created_at'] = now();
            $mahalle['updated_at'] = now();

            DB::table('mahalleler')->updateOrInsert(
                ['ilce_id' => $mahalle['ilce_id'], 'mahalle_adi' => $mahalle['mahalle_adi']],
                $mahalle
            );
        }

        $this->command->info('✅ ' . count($mahalleler) . ' mahalle eklendi (Kavaklıdere)');
    }
}

