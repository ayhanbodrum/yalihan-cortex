<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IlanKategori;
use Illuminate\Support\Str;

class ArsaKategorileriSeeder extends Seeder
{
    /**
     * Arsa Alt Kategorileri - Mantıklı Yapılandırma
     * Context7 Compliant - 2025-10-23
     */
    public function run(): void
    {
        // Ana kategori: Arsa
        $arsa = IlanKategori::where('name', 'Arsa')->first();

        if (!$arsa) {
            $this->command->error('❌ Arsa ana kategorisi bulunamadı!');
            return;
        }

        // ✅ 11 Mantıklı Arsa Alt Kategorisi
        $arsaAltKategoriler = [
            [
                'name' => 'İmarlı Arsa',
                'slug' => 'imarli-arsa',
                'aciklama' => 'İmar planında yapılaşmaya uygun arsa. Konut, villa, ticari veya turizm amaçlı kullanılabilir.',
                'parent_id' => $arsa->id,
                'seviye' => 1, // Alt kategori
                'icon' => 'building-circle-check',
                'display_order' => 1,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Tarla',
                'slug' => 'tarla',
                'aciklama' => 'Tarımsal üretim için kullanılan arazi. Ekim, sulama ve hasat için uygun.',
                'parent_id' => $arsa->id,
                'seviye' => 1,
                'icon' => 'tractor',
                'display_order' => 2,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Zeytinlik',
                'slug' => 'zeytinlik',
                'aciklama' => 'Zeytin ağaçları bulunan tarımsal arazi. Verim, sulama ve bakım bilgileri önemli.',
                'parent_id' => $arsa->id,
                'seviye' => 1,
                'icon' => 'tree',
                'display_order' => 3,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Bağ',
                'slug' => 'bag',
                'aciklama' => 'Üzüm bağı, asma alanı. Şaraplık veya sofralık üzüm üretimi için.',
                'parent_id' => $arsa->id,
                'seviye' => 1,
                'icon' => 'grape',
                'display_order' => 4,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Bahçe',
                'slug' => 'bahce',
                'aciklama' => 'Meyve ve sebze bahçesi. Organik tarım veya hobi bahçecilik için uygun.',
                'parent_id' => $arsa->id,
                'seviye' => 1,
                'icon' => 'seedling',
                'display_order' => 5,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Çiftlik',
                'slug' => 'ciftlik',
                'aciklama' => 'Hayvancılık ve tarım çiftliği. Ahır, ağıl ve sulama sistemi içerebilir.',
                'parent_id' => $arsa->id,
                'seviye' => 1,
                'icon' => 'cow',
                'display_order' => 6,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Turistik Arsa',
                'slug' => 'turistik-arsa',
                'aciklama' => 'Turizm tesisleri (otel, apart, tatil köyü) için tahsisli arsa. Deniz veya doğa manzaralı.',
                'parent_id' => $arsa->id,
                'seviye' => 1,
                'icon' => 'umbrella-beach',
                'display_order' => 7,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Sanayi Arsası',
                'slug' => 'sanayi-arsasi',
                'aciklama' => 'Sanayi tesisleri için tahsisli arsa. OSB içinde veya dışında olabilir.',
                'parent_id' => $arsa->id,
                'seviye' => 1,
                'icon' => 'industry',
                'display_order' => 8,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Ticari Arsa',
                'slug' => 'ticari-arsa',
                'aciklama' => 'AVM, plaza, işyeri gibi ticari yapılar için uygun arsa. Ana cadde cepheli tercih edilir.',
                'parent_id' => $arsa->id,
                'seviye' => 1,
                'icon' => 'store',
                'display_order' => 9,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Karma Alan',
                'slug' => 'karma-alan',
                'aciklama' => 'Konut + Ticaret karma kullanım alanı. Zemin ticari, üst katlar konut olabilir.',
                'parent_id' => $arsa->id,
                'seviye' => 1,
                'icon' => 'building-user',
                'display_order' => 10,
                'status' => 'Aktif',
            ],
            [
                'name' => 'Mesire Alanı',
                'slug' => 'mesire-alani',
                'aciklama' => 'Dinlenme, mesire ve rekreasyon alanı. Ağaçlık, su kaynağı içerebilir.',
                'parent_id' => $arsa->id,
                'seviye' => 1,
                'icon' => 'tree-city',
                'display_order' => 11,
                'status' => 'Aktif',
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($arsaAltKategoriler as $kategoriData) {
            $existing = IlanKategori::where('slug', $kategoriData['slug'])
                ->where('parent_id', $arsa->id)
                ->first();

            if ($existing) {
                $existing->update($kategoriData);
                $updated++;
                $this->command->info("✏️  Güncellendi: {$kategoriData['name']}");
            } else {
                IlanKategori::create($kategoriData);
                $created++;
                $this->command->info("✅ Oluşturuldu: {$kategoriData['name']}");
            }
        }

        $this->command->info("\n📊 ARSA ALT KATEGORİLERİ:");
        $this->command->info("   ✅ Yeni: {$created}");
        $this->command->info("   ✏️  Güncel: {$updated}");
        $this->command->info("   📦 Toplam: " . ($created + $updated));
    }
}
