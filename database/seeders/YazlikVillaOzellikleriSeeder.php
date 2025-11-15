<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\FeatureCategory;
use App\Models\Feature;

/**
 * Yazlık Villa Özellikleri Seeder
 *
 * Yazlık kiralama için villa özelliklerini oluşturur.
 * Etstur.com ve benzeri sitelerden çıkarılan özellikler.
 */
class YazlikVillaOzellikleriSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏖️ Yazlık Villa Özellikleri oluşturuluyor...');

        // Context7: Schema kontrolü
        if (!Schema::hasTable('feature_categories') || !Schema::hasTable('features')) {
            $this->command->warn('⚠️ Feature tabloları bulunamadı!');
            return;
        }

        // 1. DIŞ MEKAN ÖZELLİKLERİ
        $disMekanKategori = $this->createFeatureCategory('Dış Mekan Özellikleri', 'dis-mekan-ozellikleri', 'checkbox', [
            'Özel Havuz', 'Genel Havuz', 'Havuz Bariyeri', 'Havuz Jakuzi', 'Isıtmalı Havuz',
            'Tuzlu Su Havuzu', 'Infinity Havuz', 'Çocuk Havuzu', 'Havuz Barı', 'Havuz Şezlongu',
            'Teras', 'Bahçe', 'Bahçe Mobilyası', 'Bahçe Çardak', 'Bahçe Şemsiyesi',
            'Barbekü', 'Mangal Alanı', 'Açık Mutfak', 'Dış Mekan Duşu', 'Çocuk Oyun Alanı',
            'Açık Otopark', 'Kapalı Otopark', 'Deniz Manzarası', 'Dağ Manzarası',
            'Havuz Manzarası', 'Güvenlik Sistemi', 'Kamera Güvenliği', 'Bahçe Aydınlatması',
            'Denize Sıfır', 'Deniz Erişimi', 'Özel Plaj', 'Plaj Şemsiyesi', 'Plaj Şezlongu'
        ]);

        // 2. İÇ MEKAN ÖZELLİKLERİ
        $icMekanKategori = $this->createFeatureCategory('İç Mekan Özellikleri', 'ic-mekan-ozellikleri', 'checkbox', [
            'Klima', 'Merkezi Isıtma', 'Şömine', 'Elektrikli Şömine', 'TV', 'Smart TV',
            'Uydu Yayını', 'Netflix', 'Amazon Prime', 'YouTube', 'WiFi', 'Yüksek Hızlı İnternet',
            'Fiber İnternet', 'Çamaşır Makinesi', 'Kurutma Makinesi', 'Bulaşık Makinesi',
            'Buzdolabı', 'Derin Dondurucu', 'Fırın', 'Mikrodalga', 'Kahve Makinesi',
            'Espresso Makinesi', 'Su Sebili', 'Ankastre Mutfak', 'Tam Donanımlı Mutfak',
            'Mutfak Gereçleri', 'Bardak Takımı', 'Tabak Takımı', 'Çatal Bıçak Takımı',
            'Balkon', 'Balkon Mobilyası', 'Çatı Katı', 'Depo', 'Kiler', 'Köşe Dolapları'
        ]);

        // 3. YATAK ODASI ÖZELLİKLERİ
        $yatakOdasiKategori = $this->createFeatureCategory('Yatak Odası Özellikleri', 'yatak-odasi-ozellikleri', 'checkbox', [
            'Ebeveyn Yatak Odası', 'Ebeveyn Banyosu', 'Ebeveyn Balkonu', 'Giyinme Odası',
            'Yatak Odası Kliması', 'Yatak Odası TV', 'Gardırop', 'Karyola', 'Yatak Takımı'
        ]);

        // 4. BANYO ÖZELLİKLERİ
        $banyoKategori = $this->createFeatureCategory('Banyo Özellikleri', 'banyo-ozellikleri', 'checkbox', [
            'Jakuzi', 'Duşakabin', 'Banyo Penceresi', 'Saç Kurutma Makinesi',
            'Banyo Havlusu', 'Banyo Aynası', 'Banyo Dolabı', 'Bebek Banyosu'
        ]);

        // 5. EK HİZMETLER
        $ekHizmetlerKategori = $this->createFeatureCategory('Ek Hizmetler', 'ek-hizmetler', 'checkbox', [
            'Temizlik Hizmeti', 'Günlük Temizlik', 'Haftalık Temizlik', 'Çıkış Temizliği',
            'Çamaşır Yıkama Hizmeti', 'Ütü Hizmeti', 'Oda Servisi', 'Kahvaltı Servisi',
            'Akşam Yemeği Servisi', 'Barbekü Hazırlama', 'Transfer Hizmeti', 'Havaalanı Transferi',
            'Otopark Hizmeti', 'Bebek Bakıcısı', 'Çocuk Bakıcısı', 'Şoför Hizmeti',
            'Yemek Servisi', 'Market Alışverişi', 'Rezervasyon Yardımı', 'Tur Organizasyonu'
        ]);

        // 6. ULAŞIM VE KONUM
        $ulasimKonumKategori = $this->createFeatureCategory('Ulaşım ve Konum', 'ulasim-konum', 'checkbox', [
            'Merkeze Yakın', 'Plaja Yakın', 'Denize Sıfır', 'Market Yakın', 'Restoran Yakın',
            'Havaalanına Yakın', 'Şehir Merkezine Yakın', 'Alışveriş Merkezine Yakın',
            'Sahil Yolu', 'Ana Yola Yakın', 'Toplu Taşıma Erişimi'
        ]);

        // 7. EĞLENCE VE AKTİVİTE
        $eglenceKategori = $this->createFeatureCategory('Eğlence ve Aktivite', 'eglence-aktivite', 'checkbox', [
            'Masa Tenisi', 'Bilardo', 'Tavla', 'Okey', 'PlayStation', 'Xbox',
            'Oyun Konsolu', 'Müzik Sistemi', 'Ses Sistemi', 'Projeksiyon',
            'Sinema Odası', 'Fitness Ekipmanları', 'Spa', 'Hamam', 'Sauna'
        ]);

        // 8. GÜVENLİK VE ERİŞİM
        $guvenlikKategori = $this->createFeatureCategory('Güvenlik ve Erişim', 'guvenlik-erisim', 'checkbox', [
            '24 Saat Güvenlik', 'Güvenlik Kamerası', 'Alarm Sistemi', 'Kasa', 'Şifreli Kasa',
            'Güvenli Otopark', 'Kapıcı', 'Ziyaretçi Kontrolü', 'Kartlı Erişim',
            'Yangın Tüpü', 'İlk Yardım Çantası', 'Duman Dedektörü', 'Karbon Monoksit Dedektörü'
        ]);

        // 9. ÇOCUK VE BEBEK DOSTU
        $cocukBebekKategori = $this->createFeatureCategory('Çocuk ve Bebek Dostu', 'cocuk-bebek-dostu', 'checkbox', [
            'Bebek Yatağı', 'Bebek Sandalyesi', 'Bebek Beşiği', 'Bebek Arabası',
            'Çocuk Yatağı', 'Çocuk Sandalyesi', 'Çocuk Güvenlik Kapısı', 'Çocuk Oyun Alanı',
            'Çocuk Havuzu', 'Bebek Bakım Seti', 'Bebek Banyosu', 'Çocuk Dostu Mutfak'
        ]);

        // 10. EVCİL HAYVAN
        $evcilHayvanKategori = $this->createFeatureCategory('Evcil Hayvan', 'evcil-hayvan', 'checkbox', [
            'Evcil Hayvan Kabul', 'Evcil Hayvan Yatağı', 'Evcil Hayvan Maması Kasesi',
            'Evcil Hayvan Oyun Alanı', 'Evcil Hayvan Temizlik Malzemeleri'
        ]);

        // 11. ÖZEL HAVUZ DETAYLARI
        $havuzDetayKategori = $this->createFeatureCategory('Havuz Detayları', 'havuz-detaylari', 'checkbox', [
            'Havuz Isıtma Sistemi', 'Havuz Temizleme Sistemi', 'Havuz Aydınlatması',
            'Havuz Müzik Sistemi', 'Havuz Barı', 'Havuz Şezlongu', 'Havuz Şemsiyesi',
            'Havuz Çevresi Döşeme', 'Havuz Güvenlik Bariyeri', 'Havuz Bakım Hizmeti'
        ]);

        $this->command->info('✅ Yazlık villa özellikleri oluşturuldu!');
    }

    private function createFeatureCategory(string $name, string $slug, string $fieldType, array $features): FeatureCategory
    {
        $hasStatusColumn = Schema::hasColumn('feature_categories', 'status');
        $hasEnabledColumn = Schema::hasColumn('feature_categories', 'enabled');

        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => "Yazlık villa {$name}",
            'icon' => '🏖️',
            'display_order' => (FeatureCategory::max('order') ?? 0) + 1,
        ];

        // Context7: applies_to kolonu varsa ekle
        if (Schema::hasColumn('feature_categories', 'applies_to')) {
            $data['applies_to'] = 'yazlik,villa';
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
        $slug = $baseSlug . '-yazlik-' . $categoryId;

        $data = [
            'feature_category_id' => $categoryId,
            'name' => $name,
            'slug' => $slug,
            'display_order' => $order,
        ];

        // Context7: type kolonu varsa ekle
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
