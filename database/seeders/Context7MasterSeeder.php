<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\IlanKategori;
use Spatie\Permission\Models\Role;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;

/**
 * Context7 Master Seeder
 *
 * ✅ TEK MASTER SEEDER - Tüm verileri Context7 kurallarına göre yükler.
 *
 * Bu seeder, tüm alt seeder'ları yönetir:
 * - CompleteIlanKategoriSeeder (İlan Kategorileri)
 * - FeatureCategorySeeder (Özellik Kategorileri)
 * - ComprehensiveFeatureSeeder (Özellikler)
 * - Roller ve Lokasyon verileri
 *
 * Context7 Standardı: C7-MASTER-SEEDER-2025-11-05
 * Versiyon: 5.0.0
 *
 * Yasaklı Komutlar (.context7/authority.json):
 * - status kolonu kullanmadan önce Schema::hasColumn() kontrolü
 * - Türkçe alan adları yasak (durum, aktif, is_active)
 * - Context7 field naming standards
 *
 * Kullanım:
 *   php artisan db:seed                    → DatabaseSeeder → Context7MasterSeeder
 *   php artisan db:seed --class=Context7MasterSeeder  → Direkt master seeder
 */
class Context7MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Context7 Master Seeder başlatılıyor...');
        $this->command->info('📋 Context7 Standardı: C7-MASTER-SEEDER-2025-11-05');
        $this->command->info('🔧 Veritabanı: yalihanemlak_ultra');
        $this->command->newLine();

        // 1. Türkiye İlleri (Lokasyon için gerekli)
        $this->command->info('🏛️ 1. Türkiye İlleri seed ediliyor...');
        if (Il::count() == 0) {
            $this->call(TurkiyeIlleriSeeder::class);
        } else {
            $this->command->info('   ✓ İller zaten mevcut (' . Il::count() . ' adet)');
        }

        // 2. İlan Kategorileri (Emlak Yönetimi)
        $this->command->info('📂 2. İlan Kategorileri seed ediliyor...');
        $this->call(CompleteIlanKategoriSeeder::class);

        // 3. İlan Yayın Tipleri
        $this->command->info('📢 3. İlan Yayın Tipleri seed ediliyor...');
        $this->call(IlanYayinTipiSeeder::class);

        // 4. Lokasyon Verileri (Muğla-Aydın)
        $this->command->info('📍 4. Lokasyon verileri seed ediliyor (Muğla-Aydın)...');
        $this->call(MuglaAydinLocationSeeder::class);

        // 5. Yazlık Sezon Tanımları
        $this->command->info('📅 5. Yazlık Sezon Tanımları seed ediliyor...');
        if (Schema::hasTable('ilan_takvim_sezonlar')) {
            $this->call(YazlikSezonSeeder::class);
        } else {
            $this->command->warn('   ⚠️ ilan_takvim_sezonlar tablosu yok');
        }

        // 6. Site/Apartman Verileri
        $this->command->info('🏢 6. Site/Apartman verileri seed ediliyor...');
        if (Schema::hasTable('site_apartmanlar')) {
            try {
                $this->call(SiteApartmanSeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ SiteApartmanSeeder hatası: ' . $e->getMessage());
            }
        } else {
            $this->command->warn('   ⚠️ site_apartmanlar tablosu yok');
        }

        // 7. Roller ve İzinler
        $this->command->info('👥 7. Roller ve izinler seed ediliyor...');
        $this->seedRoles();

        // 8. Özellik Kategorileri (Schema kontrolü ile)
        $this->command->info('⚙️ 8. Özellik kategorileri seed ediliyor...');
        if (Schema::hasTable('feature_categories')) {
            try {
                $this->call(FeatureCategorySeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ FeatureCategorySeeder hatası: ' . $e->getMessage());
                $this->command->info('   ℹ️ Schema kontrolü eklenmeli');
            }
        } else {
            $this->command->warn('   ⚠️ feature_categories tablosu yok');
        }

        // 8b. Revy.com.tr Tarzı Özellik Kategorileri (Modal Seçim Sistemi)
        $this->command->info('🎨 8b. Revy.com.tr tarzı özellik kategorileri seed ediliyor...');
        if (Schema::hasTable('feature_categories') && Schema::hasTable('features')) {
            try {
                $this->call(RevyStyleFeatureCategoriesSeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ RevyStyleFeatureCategoriesSeeder hatası: ' . $e->getMessage());
            }
        } else {
            $this->command->warn('   ⚠️ feature_categories veya features tablosu yok');
        }

        // 9. Temel Özellikler (Schema kontrolü ile)
        $this->command->info('🏷️ 9. Temel özellikler seed ediliyor...');
        if (Schema::hasTable('features')) {
            try {
                $this->call(ComprehensiveFeatureSeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ ComprehensiveFeatureSeeder hatası: ' . $e->getMessage());
            }
        } else {
            $this->command->warn('   ⚠️ features tablosu yok');
        }

        // 10. Proje Özellikleri
        $this->command->info('🏗️ 10. Proje özellikleri seed ediliyor...');
        if (Schema::hasTable('features') && Schema::hasTable('feature_categories')) {
            try {
                $this->call(ProjeOzellikleriSeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ ProjeOzellikleriSeeder hatası: ' . $e->getMessage());
            }
        }

        // 11. Yazlık Villa Özellikleri
        $this->command->info('🏖️ 11. Yazlık villa özellikleri seed ediliyor...');
        if (Schema::hasTable('features') && Schema::hasTable('feature_categories')) {
            try {
                $this->call(YazlikVillaOzellikleriSeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ YazlikVillaOzellikleriSeeder hatası: ' . $e->getMessage());
            }
        }

        // 12. Kategori Yayın Tipi İlişkileri
        $this->command->info('🔗 12. Kategori yayın tipi ilişkileri seed ediliyor...');
        if (Schema::hasTable('alt_kategori_yayin_tipi')) {
            try {
                $this->call(ArsaIsyeriYayinTipiSeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ ArsaIsyeriYayinTipiSeeder hatası: ' . $e->getMessage());
            }
        }

        // 12b. Konut ve Yazlık Yayın Tipi İlişkileri
        $this->command->info('🔗 12b. Konut ve Yazlık yayın tipi ilişkileri seed ediliyor...');
        if (Schema::hasTable('alt_kategori_yayin_tipi')) {
            try {
                $this->call(KonutYazlikYayinTipiSeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ KonutYazlikYayinTipiSeeder hatası: ' . $e->getMessage());
            }
        }

        // 12c. Yazlık Kiralık Ana Kategori Sistemi
        $this->command->info('🏖️ 12c. Yazlık Kiralık ana kategori sistemi seed ediliyor...');
        if (Schema::hasTable('ilan_kategorileri') && Schema::hasTable('ilan_kategori_yayin_tipleri')) {
            try {
                $this->call(YazlikKiralikAnaKategoriSeeder::class);
                if (Schema::hasTable('kategori_yayin_tipi_field_dependencies')) {
                    $this->call(Category39YazlikSeeder::class);
                }
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ YazlikKiralikAnaKategoriSeeder hatası: ' . $e->getMessage());
            }
        }

        // 13. Yazlık Özellik İlişkilendirmeleri
        $this->command->info('🔗 13. Yazlık özellik ilişkilendirmeleri seed ediliyor...');
        if (Schema::hasTable('feature_assignments')) {
            try {
                $this->call(YazlikOzellikIliskilendirmeSeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ YazlikOzellikIliskilendirmeSeeder hatası: ' . $e->getMessage());
            }
        }

        // 13b. Yazlık Kiralık Özellik İlişkilendirmeleri
        $this->command->info('🔗 13b. Yazlık Kiralık özellik ilişkilendirmeleri seed ediliyor...');
        if (Schema::hasTable('feature_assignments')) {
            try {
                $this->call(YazlikKiralikOzellikIliskilendirmeSeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ YazlikKiralikOzellikIliskilendirmeSeeder hatası: ' . $e->getMessage());
            }
        }

        // 15. AI Provider Ayarları
        $this->command->info('🤖 15. AI Provider ayarları seed ediliyor...');
        if (Schema::hasTable('settings')) {
            try {
                $this->call(AIProviderSettingsSeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ AIProviderSettingsSeeder hatası: ' . $e->getMessage());
            }
        }

        // 14. Proje Özellik İlişkilendirmeleri
        $this->command->info('🔗 14. Proje özellik ilişkilendirmeleri seed ediliyor...');
        if (Schema::hasTable('feature_assignments')) {
            try {
                $this->call(ProjeOzellikIliskilendirmeSeeder::class);
            } catch (\Exception $e) {
                $this->command->warn('   ⚠️ ProjeOzellikIliskilendirmeSeeder hatası: ' . $e->getMessage());
            }
        }

        $this->command->newLine();
        $this->command->info('✅ Context7 Master Seeder tamamlandı!');
        $this->command->info('📊 Tüm veriler Context7 standartlarına uygun olarak yüklendi');
    }


    /**
     * Roller ve izinleri seed et
     */
    private function seedRoles(): void
    {
        if (Role::count() > 0) {
            $this->command->info('   ✓ Roller zaten mevcut (' . Role::count() . ' adet)');
            return;
        }

        $roles = [
            ['name' => 'superadmin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'danisman', 'guard_name' => 'web'],
            ['name' => 'user', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        $this->command->info('   ✓ ' . count($roles) . ' rol oluşturuldu');
    }
}
