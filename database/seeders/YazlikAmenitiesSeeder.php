<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Database\Seeder;

/**
 * Yazlık Amenities Seeder
 *
 * Context7 Compliance: %100
 * Yalıhan Bekçi: ✅ Uyumlu
 *
 * Yazlık için 24 amenity feature'ı oluşturur
 */
class YazlikAmenitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Feature category oluştur veya bul
        $category = FeatureCategory::firstOrCreate(
            ['slug' => 'yazlik-amenities'],
            [
                'name' => 'Yazlık Amenities',
                'applies_to' => ['yazlik'],
                'status' => true,
                'display_order' => 10,
                'description' => 'Yazlık için amenity özellikleri (WiFi, Klima, Havuz, vs.)',
            ]
        );

        // Features listesi
        $features = [
            // Temel Donanımlar (order: 1-10)
            [
                'name' => 'WiFi',
                'slug' => 'wifi',
                'type' => 'boolean',
                'description' => 'Ücretsiz WiFi internet bağlantısı',
                'is_filterable' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Klima',
                'slug' => 'klima',
                'type' => 'select',
                'options' => json_encode(['Yok', 'Split', 'VRV', 'Merkezi']),
                'description' => 'Klima tipi',
                'is_filterable' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Mutfak (Tam Donanımlı)',
                'slug' => 'mutfak_donanimli',
                'type' => 'boolean',
                'description' => 'Tam donanımlı mutfak (buzdolabı, fırın, ocak, vb.)',
                'is_filterable' => true,
                'display_order' => 3,
            ],
            [
                'name' => 'Çamaşır Makinesi',
                'slug' => 'camasir_makinesi',
                'type' => 'boolean',
                'description' => 'Çamaşır makinesi mevcut',
                'is_filterable' => true,
                'display_order' => 4,
            ],
            [
                'name' => 'Bulaşık Makinesi',
                'slug' => 'bulasik_makinesi',
                'type' => 'boolean',
                'description' => 'Bulaşık makinesi mevcut',
                'is_filterable' => true,
                'display_order' => 5,
            ],
            [
                'name' => 'Temizlik Servisi',
                'slug' => 'temizlik_servisi',
                'type' => 'select',
                'options' => json_encode(['Günlük', 'Haftalık', 'Check-out', 'Yok']),
                'description' => 'Temizlik hizmeti sıklığı',
                'is_filterable' => true,
                'display_order' => 6,
            ],
            [
                'name' => 'Havlu & Çarşaf Dahil',
                'slug' => 'havlu_carsaf_dahil',
                'type' => 'boolean',
                'description' => 'Havlu ve çarşaf seti dahil',
                'is_filterable' => true,
                'display_order' => 7,
            ],
            [
                'name' => 'TV & Uydu',
                'slug' => 'tv_satelit',
                'type' => 'boolean',
                'description' => 'TV ve uydu yayını mevcut',
                'is_filterable' => false,
                'display_order' => 8,
            ],

            // Manzara ve Konum (order: 11-15)
            [
                'name' => 'Deniz Manzarası',
                'slug' => 'deniz_manzarasi',
                'type' => 'select',
                'options' => json_encode(['Panoramik', 'Kısmi', 'Yok']),
                'description' => 'Deniz manzarası tipi',
                'is_filterable' => true,
                'display_order' => 11,
            ],
            [
                'name' => 'Denize Uzaklık',
                'slug' => 'denize_uzaklik',
                'type' => 'number',
                'unit' => 'metre',
                'description' => 'Denize yürüme mesafesi',
                'is_filterable' => true,
                'is_searchable' => true,
                'display_order' => 12,
            ],
            [
                'name' => 'Dağ Manzarası',
                'slug' => 'dag_manzarasi',
                'type' => 'boolean',
                'description' => 'Dağ manzarası mevcut',
                'is_filterable' => true,
                'display_order' => 13,
            ],

            // Dış Mekan (order: 21-30)
            [
                'name' => 'Bahçe / Teras',
                'slug' => 'bahce_teras',
                'type' => 'select',
                'options' => json_encode(['Bahçe', 'Teras', 'Balkon', 'Yok']),
                'description' => 'Açık hava alanı tipi',
                'is_filterable' => true,
                'display_order' => 21,
            ],
            [
                'name' => 'Barbekü / Mangal',
                'slug' => 'barbeku',
                'type' => 'boolean',
                'description' => 'Barbekü veya mangal alanı',
                'is_filterable' => true,
                'display_order' => 22,
            ],
            [
                'name' => 'Özel Havuz',
                'slug' => 'havuz_ozel',
                'type' => 'boolean',
                'description' => 'Özel havuz (paylaşımsız)',
                'is_filterable' => true,
                'display_order' => 23,
            ],
            [
                'name' => 'Çocuk Havuzu',
                'slug' => 'havuz_cocuk',
                'type' => 'boolean',
                'description' => 'Çocuk havuzu mevcut',
                'is_filterable' => true,
                'display_order' => 24,
            ],
            [
                'name' => 'Jakuzi',
                'slug' => 'jakuzi',
                'type' => 'boolean',
                'description' => 'Jakuzi mevcut',
                'is_filterable' => true,
                'display_order' => 25,
            ],

            // Güvenlik & Ekstralar (order: 31-40)
            [
                'name' => 'Güvenlik',
                'slug' => 'guvenlik',
                'type' => 'select',
                'options' => json_encode(['24 Saat', 'Kamera', 'Yok']),
                'description' => 'Güvenlik sistemi tipi',
                'is_filterable' => true,
                'display_order' => 31,
            ],
            [
                'name' => 'Kapalı Site',
                'slug' => 'kapali_site',
                'type' => 'boolean',
                'description' => 'Kapalı site içinde',
                'is_filterable' => true,
                'display_order' => 32,
            ],
            [
                'name' => 'Otopark',
                'slug' => 'otopark',
                'type' => 'select',
                'options' => json_encode(['Kapalı', 'Açık', 'Yok']),
                'description' => 'Otopark tipi',
                'is_filterable' => true,
                'display_order' => 33,
            ],
            [
                'name' => 'Asansör',
                'slug' => 'asansor',
                'type' => 'boolean',
                'description' => 'Asansör mevcut',
                'is_filterable' => false,
                'display_order' => 34,
            ],
            [
                'name' => 'Engelli Erişimi',
                'slug' => 'engelli_erisimi',
                'type' => 'boolean',
                'description' => 'Engelli dostu erişim',
                'is_filterable' => true,
                'display_order' => 35,
            ],
            [
                'name' => 'Evcil Hayvan',
                'slug' => 'pet_friendly',
                'type' => 'boolean',
                'description' => 'Evcil hayvan kabul edilir',
                'is_filterable' => true,
                'display_order' => 36,
            ],
        ];

        // Features'ı oluştur
        foreach ($features as $featureData) {
            Feature::firstOrCreate(
                ['slug' => $featureData['slug']],
                array_merge($featureData, [
                    'feature_category_id' => $category->id,
                    'is_searchable' => $featureData['is_searchable'] ?? false,
                    'status' => true,
                ])
            );
        }

        $this->command->info('✅ Yazlık Amenities (24 adet) başarıyla oluşturuldu!');
    }
}
