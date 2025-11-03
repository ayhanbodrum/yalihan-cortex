<?php

namespace Database\Seeders;

use App\Models\Mahalle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahalleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table to avoid duplicate entries
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Mahalle::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // İstanbul mahalleleri (mevcut ilçe ID'leri kullanarak)
        $istanbulMahalleleri = [
            ['ilce_id' => 1, 'mahalle_kodu' => '340101', 'mahalle_adi' => 'Kadıköy Merkez'],
            ['ilce_id' => 1, 'mahalle_kodu' => '340102', 'mahalle_adi' => 'Moda'],
            ['ilce_id' => 1, 'mahalle_kodu' => '340103', 'mahalle_adi' => 'Fenerbahçe'],
            ['ilce_id' => 1, 'mahalle_kodu' => '340104', 'mahalle_adi' => 'Bostancı'],
            ['ilce_id' => 1, 'mahalle_kodu' => '340105', 'mahalle_adi' => 'Kozyatağı'],
            ['ilce_id' => 2, 'mahalle_kodu' => '340201', 'mahalle_adi' => 'Beşiktaş Merkez'],
            ['ilce_id' => 2, 'mahalle_kodu' => '340202', 'mahalle_adi' => 'Ortaköy'],
            ['ilce_id' => 2, 'mahalle_kodu' => '340203', 'mahalle_adi' => 'Etiler'],
            ['ilce_id' => 2, 'mahalle_kodu' => '340204', 'mahalle_adi' => 'Levent'],
            ['ilce_id' => 2, 'mahalle_kodu' => '340205', 'mahalle_adi' => 'Bebek'],
        ];

        // Şişli mahalleleri
        $sisliMahalleleri = [
            ['ilce_id' => 3, 'mahalle_kodu' => '340301', 'mahalle_adi' => 'Şişli Merkez'],
            ['ilce_id' => 3, 'mahalle_kodu' => '340302', 'mahalle_adi' => 'Mecidiyeköy'],
            ['ilce_id' => 3, 'mahalle_kodu' => '340303', 'mahalle_adi' => 'Gayrettepe'],
            ['ilce_id' => 3, 'mahalle_kodu' => '340304', 'mahalle_adi' => 'Harbiye'],
            ['ilce_id' => 3, 'mahalle_kodu' => '340305', 'mahalle_adi' => 'Nişantaşı'],
        ];

        // Bakırköy mahalleleri
        $bakirkoyMahalleleri = [
            ['ilce_id' => 4, 'mahalle_kodu' => '340401', 'mahalle_adi' => 'Bakırköy Merkez'],
            ['ilce_id' => 4, 'mahalle_kodu' => '340402', 'mahalle_adi' => 'Yeşilyurt'],
            ['ilce_id' => 4, 'mahalle_kodu' => '340403', 'mahalle_adi' => 'Zeytinlik'],
            ['ilce_id' => 4, 'mahalle_kodu' => '340404', 'mahalle_adi' => 'Kartaltepe'],
            ['ilce_id' => 4, 'mahalle_kodu' => '340405', 'mahalle_adi' => 'Ataköy'],
        ];

        // Üsküdar mahalleleri
        $uskudarMahalleleri = [
            ['ilce_id' => 5, 'mahalle_kodu' => '340501', 'mahalle_adi' => 'Üsküdar Merkez'],
            ['ilce_id' => 5, 'mahalle_kodu' => '340502', 'mahalle_adi' => 'Çengelköy'],
            ['ilce_id' => 5, 'mahalle_kodu' => '340503', 'mahalle_adi' => 'Kuzguncuk'],
            ['ilce_id' => 5, 'mahalle_kodu' => '340504', 'mahalle_adi' => 'Beylerbeyi'],
            ['ilce_id' => 5, 'mahalle_kodu' => '340505', 'mahalle_adi' => 'Kandilli'],
        ];

        // Tüm mahalleleri ekle
        foreach ($istanbulMahalleleri as $mahalle) {
            Mahalle::create($mahalle);
        }

        foreach ($sisliMahalleleri as $mahalle) {
            Mahalle::create($mahalle);
        }

        foreach ($bakirkoyMahalleleri as $mahalle) {
            Mahalle::create($mahalle);
        }

        foreach ($uskudarMahalleleri as $mahalle) {
            Mahalle::create($mahalle);
        }
    }
}
