<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\FeatureCategory;
use App\Models\Feature;

/**
 * Proje Özellikleri Seeder
 * 
 * Proje özelliklerini (Proje Tipi, Site Özellikleri, Bina Özellikleri, Konut Özellikleri, İnşaat Teknikleri) oluşturur.
 */
class ProjeOzellikleriSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏗️ Proje Özellikleri oluşturuluyor...');

        // Context7: Schema kontrolü
        if (!Schema::hasTable('feature_categories') || !Schema::hasTable('features')) {
            $this->command->warn('⚠️ Feature tabloları bulunamadı!');
            return;
        }

        $hasStatusColumn = Schema::hasColumn('feature_categories', 'status');
        $hasEnabledColumn = Schema::hasColumn('feature_categories', 'enabled');

        // 1. PROJE TİPİ KATEGORİSİ
        $projeTipiKategori = $this->createFeatureCategory('Proje Tipi', 'proje-tipi', 'checkbox', [
            'Daire', 'Dükkan', 'Villa', 'Residence', 'Müstakil Ev'
        ]);

        // 2. SİTE ÖZELLİKLERİ KATEGORİSİ
        $siteOzellikleriKategori = $this->createFeatureCategory('Site Özellikleri', 'site-ozellikleri', 'checkbox', [
            'Kapalı otopark', 'Çocuk oyun alanları', 'Fitness merkezi', 'Açık yüzme havuzu',
            'Güvenlik', 'Kameralı güvenlik', 'Basketbol sahası'
        ]);

        // 3. BİNA ÖZELLİKLERİ KATEGORİSİ
        $binaOzellikleriKategori = $this->createFeatureCategory('Bina Özellikleri', 'bina-ozellikleri', 'checkbox', [
            'Asansör', 'Jeneratör', 'Su Deposu', 'Hidrofor', 'Yangın merdiveni'
        ]);

        // 4. KONUT ÖZELLİKLERİ KATEGORİSİ
        $konutOzellikleriKategori = $this->createFeatureCategory('Konut Özellikleri', 'konut-ozellikleri', 'checkbox', [
            'Ankastre beyaz eşya', 'Ebeveyn banyosu', 'Duşakabin', 'Balkon',
            'Depo/kiler', 'Bahçe Kullanımlı', 'İntercom sistemi', 'Ebeveyn giyinme odası'
        ]);

        // 5. İNŞAAT TEKNİKLERİ KATEGORİSİ
        $insaatTeknikleriKategori = $this->createFeatureCategory('İnşaat Teknikleri', 'insaat-teknikleri', 'checkbox', [
            'Yapı denetimi yapılmış', 'Zemin etüdü yapılmış', 'Deprem yönetmeliğine uygun',
            'Yalıtım yönetmeliğine uygun', 'Radye temel', 'Tünel Kalıp'
        ]);

        $this->command->info('✅ Proje özellikleri oluşturuldu!');
    }

    private function createFeatureCategory(string $name, string $slug, string $fieldType, array $features): FeatureCategory
    {
        $hasStatusColumn = Schema::hasColumn('feature_categories', 'status');
        $hasEnabledColumn = Schema::hasColumn('feature_categories', 'enabled');

        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => "{$name} özellikleri",
            'icon' => '🏗️',
            'order' => (FeatureCategory::max('order') ?? 0) + 1,
        ];

        // Context7: applies_to kolonu varsa ekle
        if (Schema::hasColumn('feature_categories', 'applies_to')) {
            $data['applies_to'] = 'proje';
        }

        if ($hasStatusColumn) {
            $data['status'] = true;
        } elseif ($hasEnabledColumn) {
            $data['enabled'] = true;
        }

        $category = FeatureCategory::firstOrCreate(
            ['slug' => $slug],
            $data
        );

        $this->command->info("  ✓ {$name} kategorisi oluşturuldu");

        // Feature'ları oluştur
        $order = 1;
        foreach ($features as $featureName) {
            $this->createFeature($category->id, $featureName, $fieldType, $order);
            $order++;
        }

        return $category;
    }

    private function createFeature(int $categoryId, string $name, string $fieldType, int $order): void
    {
        $hasStatusColumn = Schema::hasColumn('features', 'status');
        $hasEnabledColumn = Schema::hasColumn('features', 'enabled');
        $hasTypeColumn = Schema::hasColumn('features', 'type');

        // Slug'a kategori ID'si ekle (unique constraint için)
        $baseSlug = \Illuminate\Support\Str::slug($name);
        $slug = $baseSlug . '-' . $categoryId;

        $data = [
            'feature_category_id' => $categoryId,
            'name' => $name,
            'slug' => $slug,
            'order' => $order,
        ];

        // Context7: type kolonu varsa ekle (field_type yerine type)
        if ($hasTypeColumn) {
            $data['type'] = $fieldType;
        }

        // Diğer kolonlar varsa ekle
        if (Schema::hasColumn('features', 'is_required')) {
            $data['is_required'] = false;
        }
        if (Schema::hasColumn('features', 'is_filterable')) {
            $data['is_filterable'] = true;
        }
        if (Schema::hasColumn('features', 'is_searchable')) {
            $data['is_searchable'] = false;
        }
        if (Schema::hasColumn('features', 'show_in_listing')) {
            $data['show_in_listing'] = true;
        }
        if (Schema::hasColumn('features', 'show_in_detail')) {
            $data['show_in_detail'] = true;
        }
        if (Schema::hasColumn('features', 'show_in_filter')) {
            $data['show_in_filter'] = true;
        }

        if ($hasStatusColumn) {
            $data['status'] = true;
        } elseif ($hasEnabledColumn) {
            $data['enabled'] = true;
        }

        // Önce mevcut özelliği kontrol et (aynı kategori ve isim)
        $existing = Feature::where('feature_category_id', $categoryId)
            ->where('name', $name)
            ->first();

        if ($existing) {
            // Mevcut özelliği güncelle
            $existing->update($data);
        } else {
            // Yeni özellik oluştur
            Feature::create($data);
        }
    }
}

