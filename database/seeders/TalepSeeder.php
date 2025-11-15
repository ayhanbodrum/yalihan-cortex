<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\Ilce;
use App\Models\Kisi;
use App\Models\Talep;
use App\Models\User;
use Illuminate\Database\Seeder;

class TalepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // İlk kişiyi al (veya varsayılan kişi oluştur)
        $kisi = Kisi::first() ?? Kisi::create([
            'ad' => 'Test',
            'soyad' => 'Müşteri',
            'telefon' => '05001234567',
            'email' => 'test@example.com',
            'status' => 'Aktif',
        ]);

        // İlk kullanıcıyı al (danışman)
        $danisman = User::first();

        // Ankara ve Muğla il'lerini al
        $ankara = Il::where('il_adi', 'Ankara')->first();
        $mugla = Il::where('il_adi', 'Muğla')->first();

        // Ankara'nın ilk ilçesi
        $ankaraIlce = $ankara ? Ilce::where('il_id', $ankara->id)->first() : null;
        // Muğla'nın ilk ilçesi
        $muglaIlce = $mugla ? Ilce::where('il_id', $mugla->id)->first() : null;

        // Context7 Uyumlu Test Talepler
        $talepler = [
            [
                'kisi_id' => $kisi->id,
                'danisman_id' => $danisman?->id,
                'talep_tipi' => 'Al',
                'emlak_tipi' => 'Daire',
                'min_fiyat' => 500000.00,
                'max_fiyat' => 800000.00,
                'il_id' => $ankara?->id,
                'ilce_id' => $ankaraIlce?->id,
                'notlar' => '3+1 daire, balkonlu, asansörlü bina tercih ediliyor.',
                'status' => 'Aktif',
                'oncelik' => 'Yüksek',
            ],
            [
                'kisi_id' => $kisi->id,
                'danisman_id' => $danisman?->id,
                'talep_tipi' => 'Kirala_Al',
                'emlak_tipi' => 'Yazlık',
                'min_fiyat' => 2000000.00,
                'max_fiyat' => 4000000.00,
                'il_id' => $mugla?->id,
                'ilce_id' => $muglaIlce?->id,
                'notlar' => 'Denize yakın, havuzlu, müstakil villa tercih ediliyor. Yaz sezonunda kiralama, sonrasında satın alma düşünülüyor.',
                'status' => 'Aktif',
                'oncelik' => 'Orta',
            ],
            [
                'kisi_id' => $kisi->id,
                'danisman_id' => $danisman?->id,
                'talep_tipi' => 'Kirala',
                'emlak_tipi' => 'İşyeri',
                'min_fiyat' => 15000.00,
                'max_fiyat' => 30000.00,
                'il_id' => $ankara?->id,
                'ilce_id' => $ankaraIlce?->id,
                'notlar' => 'Merkezi konumda, 100-150 m² ofis aranıyor. Otopark önemli.',
                'status' => 'Aktif',
                'oncelik' => 'Yüksek',
            ],
            [
                'kisi_id' => $kisi->id,
                'danisman_id' => $danisman?->id,
                'talep_tipi' => 'Al',
                'emlak_tipi' => 'Arsa',
                'min_fiyat' => 300000.00,
                'max_fiyat' => 600000.00,
                'il_id' => $mugla?->id,
                'ilce_id' => $muglaIlce?->id,
                'notlar' => 'İmar durumu temiz, 500-800 m² arsa aranıyor. Villa yapımı için.',
                'status' => 'Aktif',
                'oncelik' => 'Orta',
            ],
            [
                'kisi_id' => $kisi->id,
                'danisman_id' => $danisman?->id,
                'talep_tipi' => 'Al',
                'emlak_tipi' => 'Villa',
                'min_fiyat' => 3000000.00,
                'max_fiyat' => 5000000.00,
                'il_id' => $mugla?->id,
                'ilce_id' => $muglaIlce?->id,
                'notlar' => 'Deniz manzaralı, 4+1 veya 5+1 villa. Havuz ve bahçe şart.',
                'status' => 'Aktif',
                'oncelik' => 'Yüksek',
            ],
            [
                'kisi_id' => $kisi->id,
                'danisman_id' => $danisman?->id,
                'talep_tipi' => 'Kirala',
                'emlak_tipi' => 'Daire',
                'min_fiyat' => 8000.00,
                'max_fiyat' => 15000.00,
                'il_id' => $ankara?->id,
                'ilce_id' => $ankaraIlce?->id,
                'notlar' => '2+1 kiralık daire. Eşyalı olabilir, metro yakını tercih.',
                'status' => 'Aktif',
                'oncelik' => 'Orta',
            ],
            [
                'kisi_id' => $kisi->id,
                'danisman_id' => $danisman?->id,
                'talep_tipi' => 'Sat',
                'emlak_tipi' => 'Daire',
                'min_fiyat' => 650000.00,
                'max_fiyat' => 750000.00,
                'il_id' => $ankara?->id,
                'ilce_id' => $ankaraIlce?->id,
                'notlar' => 'Elimizdeki dairenin değerlendirmesi. 120 m², 3+1, 5. kat.',
                'status' => 'Pasif',
                'oncelik' => 'Düşük',
            ],
            [
                'kisi_id' => $kisi->id,
                'danisman_id' => $danisman?->id,
                'talep_tipi' => 'Al',
                'emlak_tipi' => 'Daire',
                'min_fiyat' => 1200000.00,
                'max_fiyat' => 1800000.00,
                'il_id' => $ankara?->id,
                'ilce_id' => $ankaraIlce?->id,
                'notlar' => 'Yatırımlık daire. Site içinde, havuz ve sosyal alan olan kompleks tercih.',
                'status' => 'Aktif',
                'oncelik' => 'Yüksek',
            ],
        ];

        // Talepler oluştur
        foreach ($talepler as $talep) {
            Talep::create($talep);
        }

        $this->command->info('✅ ' . count($talepler) . ' adet test talebi başarıyla eklendi!');
        $this->command->info('📊 Context7 uyumlu talep verileri hazır.');
    }
}
