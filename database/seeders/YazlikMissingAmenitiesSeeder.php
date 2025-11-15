<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Database\Seeder;

class YazlikMissingAmenitiesSeeder extends Seeder
{
    /**
     * Yazlık Kiralama için eksik özellikleri ekle
     * Kaynak: EtsTur.com, TatildeKirala.com analizi
     * Tarih: 4 Kasım 2025
     */
    public function run(): void
    {
        $this->command->info('🏖️ Yazlık Missing Amenities ekleniyor...');

        // Kategorileri bul veya oluştur
        $wellnessCategory = FeatureCategory::firstOrCreate(
            ['name' => 'Wellness & Spa'],
            ['description' => 'Wellness ve spa özellikleri', 'display_order' => 40]
        );

        $cocukCategory = FeatureCategory::firstOrCreate(
            ['name' => 'Çocuk Dostu'],
            ['description' => 'Çocuk dostu özellikler', 'display_order' => 50]
        );

        $disMekanCategory = FeatureCategory::firstOrCreate(
            ['name' => 'Dış Mekan'],
            ['description' => 'Dış mekan özellikleri', 'display_order' => 60]
        );

        $mutfakCategory = FeatureCategory::firstOrCreate(
            ['name' => 'Mutfak Ekipmanları'],
            ['description' => 'Mutfak ekipmanları', 'display_order' => 70]
        );

        $banyoCategory = FeatureCategory::firstOrCreate(
            ['name' => 'Banyo'],
            ['description' => 'Banyo ekipmanları', 'display_order' => 80]
        );

        $eglenceCategory = FeatureCategory::firstOrCreate(
            ['name' => 'Eğlence'],
            ['description' => 'Eğlence özellikleri', 'display_order' => 90]
        );

        $manzaraCategory = FeatureCategory::firstOrCreate(
            ['name' => 'Manzara'],
            ['description' => 'Manzara özellikleri', 'display_order' => 100]
        );

        $konumCategory = FeatureCategory::firstOrCreate(
            ['name' => 'Konum Özellikleri'],
            ['description' => 'Konum vurguları', 'display_order' => 110]
        );

        // 1. WELLNESS & SPA
        $wellnessFeatures = [
            ['name' => 'Sauna', 'icon' => '🧖', 'description' => 'Özel sauna mevcut'],
            ['name' => 'Hamam (Türk Hamamı)', 'icon' => '🛁', 'description' => 'Türk hamamı mevcut'],
            ['name' => 'Spa', 'icon' => '💆', 'description' => 'Spa ve masaj alanı'],
            ['name' => 'Masaj Odası', 'icon' => '💆‍♀️', 'description' => 'Profesyonel masaj odası'],
        ];

        foreach ($wellnessFeatures as $index => $feature) {
            Feature::firstOrCreate(
                ['name' => $feature['name']],
                [
                    'category_id' => $wellnessCategory->id,
                    'field_icon' => $feature['icon'],
                    'description' => $feature['description'],
                    'field_type' => 'boolean',
                    'display_order' => $index + 1,
                ]
            );
        }
        $this->command->info("   🧖 Wellness: 4 özellik eklendi");

        // 2. ÇOCUK DOSTU
        $cocukFeatures = [
            ['name' => 'Çocuk Oyun Alanı', 'icon' => '🎪', 'description' => 'Çocuklar için oyun alanı'],
            ['name' => 'Çocuk Parkı', 'icon' => '🛝', 'description' => 'Kaydırak, salıncak var'],
            ['name' => 'Bebek Yatağı', 'icon' => '🍼', 'description' => 'Bebek yatağı talep üzerine'],
            ['name' => 'Mama Sandalyesi', 'icon' => '🪑', 'description' => 'Mama sandalyesi mevcut'],
            ['name' => 'Çocuk Güvenlik Kapısı', 'icon' => '🚪', 'description' => 'Merdiven ve havuz güvenlik'],
            ['name' => 'Oyuncaklar', 'icon' => '🧸', 'description' => 'Çocuk oyuncakları mevcut'],
        ];

        foreach ($cocukFeatures as $index => $feature) {
            Feature::firstOrCreate(
                ['name' => $feature['name']],
                [
                    'category_id' => $cocukCategory->id,
                    'field_icon' => $feature['icon'],
                    'field_type' => 'boolean',
                    'description' => $feature['description'],
                    'display_order' => $index + 1,
                ]
            );
        }
        $this->command->info("   👶 Çocuk Dostu: 6 özellik eklendi");

        // 3. DIŞ MEKAN
        $disFeatures = [
            ['name' => 'Şezlong', 'icon' => '🏖️', 'description' => 'Havuz başı şezlong'],
            ['name' => 'Bahçe Masası', 'icon' => '🪑', 'description' => 'Bahçe masa takımı'],
            ['name' => 'Bahçe Şemsiyesi', 'icon' => '⛱️', 'description' => 'Gölgelik alan'],
            ['name' => 'Dış Aydınlatma', 'icon' => '💡', 'description' => 'Dekoratif dış aydınlatma'],
            ['name' => 'Dış Duş', 'icon' => '🚿', 'description' => 'Havuz kenarı dış duş'],
        ];

        foreach ($disFeatures as $index => $feature) {
            Feature::firstOrCreate(
                ['name' => $feature['name']],
                [
                    'category_id' => $disMekanCategory->id,
                    'field_icon' => $feature['icon'],
                    'field_type' => 'boolean',
                    'description' => $feature['description'],
                    'display_order' => $index + 1,
                ]
            );
        }
        $this->command->info("   🏖️ Dış Mekan: 5 özellik eklendi");

        // 4. MUTFAK EKİPMANLARI
        $mutfakFeatures = [
            ['name' => 'Buzdolabı', 'icon' => '🧊', 'description' => 'Buzdolabı ve dondurucu'],
            ['name' => 'Kahve Makinesi', 'icon' => '☕', 'description' => 'Kahve/espresso makinesi'],
            ['name' => 'Su Isıtıcı (Kettle)', 'icon' => '🫖', 'description' => 'Elektrikli su ısıtıcı'],
            ['name' => 'Mikrodalga Fırın', 'icon' => '📟', 'description' => 'Mikrodalga fırın'],
            ['name' => 'Çay Makinesi', 'icon' => '🍵', 'description' => 'Çay makinesi'],
            ['name' => 'Tost Makinesi', 'icon' => '🍞', 'description' => 'Tost/sandwich makinesi'],
            ['name' => 'Blender/Mikser', 'icon' => '🥤', 'description' => 'Blender ve mikser'],
            ['name' => 'Fırın', 'icon' => '🔥', 'description' => 'Ankastre fırın'],
            ['name' => 'Ocak', 'icon' => '🔥', 'description' => 'Ankastre ocak'],
        ];

        foreach ($mutfakFeatures as $index => $feature) {
            Feature::firstOrCreate(
                ['name' => $feature['name']],
                [
                    'category_id' => $mutfakCategory->id,
                    'field_icon' => $feature['icon'],
                    'field_type' => 'boolean',
                    'description' => $feature['description'],
                    'display_order' => $index + 1,
                ]
            );
        }
        $this->command->info("   🍳 Mutfak: 9 özellik eklendi");

        // 5. BANYO
        $banyoFeatures = [
            ['name' => 'Saç Kurutma Makinesi', 'icon' => '💨', 'description' => 'Saç kurutma makinesi'],
            ['name' => 'Havlu Seti', 'icon' => '🧺', 'description' => 'Kaliteli havlu seti dahil'],
            ['name' => 'Banyo Malzemeleri', 'icon' => '🧴', 'description' => 'Şampuan, sabun, duş jeli'],
            ['name' => 'Bornoz', 'icon' => '🥋', 'description' => 'Bornoz takımı'],
        ];

        foreach ($banyoFeatures as $index => $feature) {
            Feature::firstOrCreate(
                ['name' => $feature['name']],
                [
                    'category_id' => $banyoCategory->id,
                    'field_icon' => $feature['icon'],
                    'field_type' => 'boolean',
                    'description' => $feature['description'],
                    'display_order' => $index + 1,
                ]
            );
        }
        $this->command->info("   🛁 Banyo: 4 özellik eklendi");

        // 6. EĞLENCE
        $eglenceFeatures = [
            ['name' => 'Oyun Konsolu (PS5/Xbox)', 'icon' => '🎮', 'description' => 'Oyun konsolu mevcut'],
            ['name' => 'Netflix/Streaming', 'icon' => '📺', 'description' => 'Netflix, Disney+ dahil'],
            ['name' => 'Bluetooth Hoparlör', 'icon' => '🔊', 'description' => 'Kablosuz hoparlör sistemi'],
            ['name' => 'Kitaplık', 'icon' => '📚', 'description' => 'Kitap koleksiyonu'],
            ['name' => 'Board Games (Masa Oyunları)', 'icon' => '🎲', 'description' => 'Kutu oyunları mevcut'],
            ['name' => 'Projeksiyon', 'icon' => '🎬', 'description' => 'Sinema sistemi'],
        ];

        foreach ($eglenceFeatures as $index => $feature) {
            Feature::firstOrCreate(
                ['name' => $feature['name']],
                [
                    'category_id' => $eglenceCategory->id,
                    'field_icon' => $feature['icon'],
                    'field_type' => 'boolean',
                    'description' => $feature['description'],
                    'display_order' => $index + 1,
                ]
            );
        }
        $this->command->info("   🎮 Eğlence: 6 özellik eklendi");

        // 7. MANZARA (Ayrı Özellikler)
        $manzaraFeatures = [
            ['name' => 'Doğa Manzaralı', 'icon' => '🌲', 'description' => 'Doğa/orman manzaralı'],
            ['name' => 'Dağ Manzaralı', 'icon' => '⛰️', 'description' => 'Dağ manzarası'],
            ['name' => 'Göl Manzaralı', 'icon' => '🏞️', 'description' => 'Göl manzarası'],
            ['name' => 'Şehir Manzaralı', 'icon' => '🏙️', 'description' => 'Şehir manzarası'],
            ['name' => 'Panoramik Manzara', 'icon' => '🌅', 'description' => '360 derece manzara'],
        ];

        foreach ($manzaraFeatures as $index => $feature) {
            Feature::firstOrCreate(
                ['name' => $feature['name']],
                [
                    'category_id' => $manzaraCategory->id,
                    'field_icon' => $feature['icon'],
                    'field_type' => 'boolean',
                    'description' => $feature['description'],
                    'display_order' => $index + 1,
                ]
            );
        }
        $this->command->info("   🌅 Manzara: 5 özellik eklendi");

        // 8. KONUM VURGULARı (TatildeKirala/EtsTur tarzı)
        $konumFeatures = [
            ['name' => 'Sakin Konumda', 'icon' => '🤫', 'description' => 'Sakin ve huzurlu konum'],
            ['name' => 'Huzurlu Çevrede', 'icon' => '🕊️', 'description' => 'Huzur ve sessizlik'],
            ['name' => 'Sessizlik İçinde', 'icon' => '🔇', 'description' => 'Gürültüden uzak'],
            ['name' => 'Merkezi Konumda', 'icon' => '📍', 'description' => 'Her yere yakın'],
            ['name' => 'Denize Sıfır', 'icon' => '🌊', 'description' => 'Denize yürüme mesafesi'],
            ['name' => 'Deniz Kenarında', 'icon' => '🏖️', 'description' => 'Sahil üzerinde'],
            ['name' => 'Orman İçinde', 'icon' => '🌲', 'description' => 'Orman içinde doğal'],
        ];

        foreach ($konumFeatures as $index => $feature) {
            Feature::firstOrCreate(
                ['name' => $feature['name']],
                [
                    'category_id' => $konumCategory->id,
                    'field_icon' => $feature['icon'],
                    'field_type' => 'boolean',
                    'description' => $feature['description'],
                    'display_order' => $index + 1,
                ]
            );
        }
        $this->command->info("   📍 Konum: 7 özellik eklendi");

        // ÖZET
        $this->command->info('');
        $this->command->info('✅ TAMAMLANDI: Yazlık Missing Amenities');
        $this->command->info('');
        $this->command->info('📊 Eklenen Kategoriler:');
        $this->command->info('   🧖 Wellness & Spa: 4');
        $this->command->info('   👶 Çocuk Dostu: 6');
        $this->command->info('   🏖️ Dış Mekan: 5');
        $this->command->info('   🍳 Mutfak: 9');
        $this->command->info('   🛁 Banyo: 4');
        $this->command->info('   🎮 Eğlence: 6');
        $this->command->info('   🌅 Manzara: 5');
        $this->command->info('   📍 Konum: 7');
        $this->command->info('');
        $this->command->info('TOPLAM: 46 yeni özellik eklendi! 🎊');
        $this->command->info('');
        $this->command->info('🎯 Sonraki Adım:');
        $this->command->info('   property-type-manager/4 sayfasından özellikleri yayın tiplerine ata');
    }
}
