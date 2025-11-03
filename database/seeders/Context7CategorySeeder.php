<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Context7 Category Seeder
 *
 * Context7 standartlarına uygun kategori sistemi.
 * Tüm eski kategori seeder'larından verileri birleştirir.
 *
 * Context7 Standardı: C7-CATEGORY-SEEDER-2025-09-13
 * Versiyon: 4.0.0
 */
class Context7CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📂 Context7 Category Seeder - Atlandı (çünkü kategoriler zaten mevcut)');

        // Kategoriler zaten mevcut olduğu için seeder atlanıyor
        $existingCount = \App\Models\IlanKategori::count();

        if ($existingCount > 0) {
            $this->command->info("\u2139\ufe0f Zaten {$existingCount} kategori mevcut. Seeder atlanıyor.");
            return;
        }

        $this->command->warn('⚠️ Bu seeder eski format kullanıyor ve güncellenmeli!');
        // Seeder çalıştırılmayacak
    }

    /**
     * Ana kategoriler oluştur
     */
    private function createMainCategories(): void
    {
        $this->command->info('📁 Ana kategoriler oluşturuluyor...');

        $mainCategories = [
            [
                'name' => 'Konut',
                'slug' => 'konut',
                'description' => 'Daire, villa, müstakil ev gibi konut türleri',
                'icon' => '🏠',
                'seviye' => 0,
                'parent_id' => null,
                'order' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_adi' => 'İş Yeri',
                'slug' => 'is-yeri',
                'aciklama' => 'Ofis, dükkan, mağaza, depo gibi ticari alanlar',
                'icon' => 'building',
                'renk' => '#10b981',
                'sira' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_adi' => 'Arsa',
                'slug' => 'arsa',
                'aciklama' => 'İmarlı arsa, tarla, yatırım arazisi',
                'icon' => 'map',
                'renk' => '#f59e0b',
                'sira' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_adi' => 'Turistik Tesis',
                'slug' => 'turistik-tesis',
                'aciklama' => 'Otel, pansiyon, tatil köyü, yazlık',
                'icon' => 'sun',
                'renk' => '#ef4444',
                'sira' => 4,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_adi' => 'Proje',
                'slug' => 'proje',
                'aciklama' => 'Yapım aşamasındaki projeler',
                'icon' => 'construction',
                'renk' => '#8b5cf6',
                'sira' => 5,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($mainCategories as $category) {
            DB::table('ilan_kategorileri')->updateOrInsert(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('✅ ' . count($mainCategories) . ' ana kategori oluşturuldu');
    }

    /**
     * Alt kategoriler oluştur
     */
    private function createSubCategories(): void
    {
        $this->command->info('📂 Alt kategoriler oluşturuluyor...');

        $subCategories = [
            // Konut Alt Kategorileri
            [
                'id' => 11,
                'kategori_adi' => 'Daire',
                'slug' => 'daire',
                'aciklama' => 'Apartman dairesi',
                'icon' => 'building-2',
                'renk' => '#3b82f6',
                'seviye' => 1,
                'sira' => 1,
                'parent_id' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'kategori_adi' => 'Villa',
                'slug' => 'villa',
                'aciklama' => 'Müstakil villa',
                'icon' => 'home-2',
                'renk' => '#3b82f6',
                'seviye' => 1,
                'sira' => 2,
                'parent_id' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'kategori_adi' => 'Müstakil Ev',
                'slug' => 'mustakil-ev',
                'aciklama' => 'Tek katlı müstakil ev',
                'icon' => 'home-3',
                'renk' => '#3b82f6',
                'seviye' => 1,
                'sira' => 3,
                'parent_id' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 14,
                'kategori_adi' => 'Rezidans',
                'slug' => 'rezidans',
                'aciklama' => 'Lüks rezidans dairesi',
                'icon' => 'crown',
                'renk' => '#8b5cf6',
                'seviye' => 1,
                'sira' => 4,
                'parent_id' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // İş Yeri Alt Kategorileri
            [
                'id' => 21,
                'kategori_adi' => 'Ofis',
                'slug' => 'ofis',
                'aciklama' => 'Büro ofisi',
                'icon' => 'briefcase',
                'renk' => '#10b981',
                'seviye' => 1,
                'sira' => 1,
                'parent_id' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 22,
                'kategori_adi' => 'Dükkan',
                'slug' => 'dukkan',
                'aciklama' => 'Ticari dükkan',
                'icon' => 'store',
                'renk' => '#10b981',
                'seviye' => 1,
                'sira' => 2,
                'parent_id' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 23,
                'kategori_adi' => 'Mağaza',
                'slug' => 'magaza',
                'aciklama' => 'Perakende mağazası',
                'icon' => 'shopping-bag',
                'renk' => '#10b981',
                'seviye' => 1,
                'sira' => 3,
                'parent_id' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 24,
                'kategori_adi' => 'Depo',
                'slug' => 'depo',
                'aciklama' => 'Depo ve antrepo',
                'icon' => 'warehouse',
                'renk' => '#10b981',
                'seviye' => 1,
                'sira' => 4,
                'parent_id' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Arsa Alt Kategorileri
            [
                'id' => 31,
                'kategori_adi' => 'İmarlı Arsa',
                'slug' => 'imarli-arsa',
                'aciklama' => 'İmar planında yer alan arsa',
                'icon' => 'map-pin',
                'renk' => '#f59e0b',
                'seviye' => 1,
                'sira' => 1,
                'parent_id' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 32,
                'kategori_adi' => 'Tarla',
                'slug' => 'tarla',
                'aciklama' => 'Tarım arazisi',
                'icon' => 'tractor',
                'renk' => '#f59e0b',
                'seviye' => 1,
                'sira' => 2,
                'parent_id' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 33,
                'kategori_adi' => 'Yatırım Arazisi',
                'slug' => 'yatirim-arazisi',
                'aciklama' => 'Yatırım amaçlı arazi',
                'icon' => 'trending-up',
                'renk' => '#f59e0b',
                'seviye' => 1,
                'sira' => 3,
                'parent_id' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Turistik Tesis Alt Kategorileri
            [
                'id' => 41,
                'kategori_adi' => 'Otel',
                'slug' => 'otel',
                'aciklama' => 'Otel ve pansiyon',
                'icon' => 'bed',
                'renk' => '#ef4444',
                'seviye' => 1,
                'sira' => 1,
                'parent_id' => 4,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 42,
                'kategori_adi' => 'Yazlık',
                'slug' => 'yazlik',
                'aciklama' => 'Yazlık ev ve villa',
                'icon' => 'sun',
                'renk' => '#ef4444',
                'seviye' => 1,
                'sira' => 2,
                'parent_id' => 4,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 43,
                'kategori_adi' => 'Tatil Köyü',
                'slug' => 'tatil-koyu',
                'aciklama' => 'Tatil köyü ve resort',
                'icon' => 'palm-tree',
                'renk' => '#ef4444',
                'seviye' => 1,
                'sira' => 3,
                'parent_id' => 4,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($subCategories as $category) {
            DB::table('ilan_kategorileri')->updateOrInsert(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('✅ ' . count($subCategories) . ' alt kategori oluşturuldu');
    }

    /**
     * Özellik kategorileri oluştur
     */
    private function createFeatureCategories(): void
    {
        $this->command->info('⚙️ Özellik kategorileri oluşturuluyor...');

        $featureCategories = [
            [
                'id' => 1,
                'slug' => 'konut',
                'display_order' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'slug' => 'is-yeri',
                'display_order' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'slug' => 'arsa',
                'display_order' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'slug' => 'turistik-tesis',
                'display_order' => 4,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($featureCategories as $category) {
            DB::table('feature_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                $category
            );

            // Kategori çevirisini ekle
            $categoryId = DB::table('feature_categories')->where('slug', $category['slug'])->value('id');
            if ($categoryId) {
                $categoryNames = [
                    'konut' => 'Konut',
                    'is-yeri' => 'İş Yeri',
                    'arsa' => 'Arsa',
                    'turistik-tesis' => 'Turistik Tesis',
                ];

                DB::table('feature_category_translations')->updateOrInsert(
                    [
                        'feature_category_id' => $categoryId,
                        'locale' => 'tr',
                    ],
                    [
                        'name' => $categoryNames[$category['slug']],
                        'kategori_adi' => $categoryNames[$category['slug']],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('✅ ' . count($featureCategories) . ' özellik kategorisi oluşturuldu');
    }
}
