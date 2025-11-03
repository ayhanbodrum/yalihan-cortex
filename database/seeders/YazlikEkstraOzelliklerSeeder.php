<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OzellikKategori;
use App\Models\Ozellik;

class YazlikEkstraOzelliklerSeeder extends Seeder
{
    /**
     * Yazlık Kiralık EKSTRA Özellikler
     * Context7 Compliant - 2025-10-23
     *
     * T.C. Kültür ve Turizm Bakanlığı Belge + Airbnb Premium Özellikler
     */
    public function run(): void
    {
        $this->command->info("🏖️ Yazlık EKSTRA özellikleri oluşturuluyor...\n");

        // ✅ 1. Resmi Belgeler ve Lisanslar
        $lisansKategori = OzellikKategori::updateOrCreate(
            ['slug' => 'lisans-belgeler'],
            [
                'name' => 'Lisans ve Belgeler',
                'slug' => 'lisans-belgeler',
                'aciklama' => 'T.C. Kültür ve Turizm Bakanlığı belgesi ve diğer resmi lisanslar',
                'icon' => 'certificate',
                'order' => 26,
                'status' => 'Aktif',
            ]
        );

        $lisansOzellikleri = [
            [
                'name' => 'Turizm İşletme Belgesi',
                'slug' => 'turizm-isletme-belgesi',
                'kategori_id' => $lisansKategori->id,
                'veri_tipi' => 'text',
                'birim' => 'Belge No',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'aciklama' => 'T.C. Kültür ve Turizm Bakanlığı İşletme Belgesi (Format: 07-1776)',
                'order' => 1,
            ],
            [
                'name' => 'Turizm Belgeli',
                'slug' => 'turizm-belgeli',
                'kategori_id' => $lisansKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'aciklama' => 'T.C. Kültür ve Turizm Bakanlığı belgeli tesis',
                'order' => 2,
            ],
            [
                'name' => 'Airbnb Süper Ev Sahibi',
                'slug' => 'airbnb-super-host',
                'kategori_id' => $lisansKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'aciklama' => 'Airbnb Superhost statüsü',
                'order' => 3,
            ],
        ];

        foreach ($lisansOzellikleri as $ozellik) {
            Ozellik::updateOrCreate(
                ['slug' => $ozellik['slug']],
                $ozellik
            );
        }
        $this->command->info("✅ Lisans ve Belgeler: 3 özellik");

        // ✅ 2. Premium Lokasyon Özellikleri
        $premiumLokasyonKategori = OzellikKategori::updateOrCreate(
            ['slug' => 'premium-lokasyon'],
            [
                'name' => 'Premium Lokasyon Özellikleri',
                'slug' => 'premium-lokasyon',
                'aciklama' => 'Denize sıfır, özel plaj, korunaklı koy gibi özel lokasyon özellikleri',
                'icon' => 'location-dot',
                'order' => 27,
                'status' => 'Aktif',
            ]
        );

        $premiumLokasyonOzellikleri = [
            [
                'name' => 'Denize Sıfır',
                'slug' => 'denize-sifir',
                'kategori_id' => $premiumLokasyonKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'aciklama' => 'Denize doğrudan erişim (0-10m)',
                'order' => 1,
            ],
            [
                'name' => 'Özel Plajlı',
                'slug' => 'ozel-plajli',
                'kategori_id' => $premiumLokasyonKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'aciklama' => 'Özel plaj kullanım hakkı',
                'order' => 2,
            ],
            [
                'name' => 'Korunaklı Koy',
                'slug' => 'korunakli-koy',
                'kategori_id' => $premiumLokasyonKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'aciklama' => 'Korunaklı/sakin koy içinde',
                'order' => 3,
            ],
            [
                'name' => 'Doğa İçinde',
                'slug' => 'doga-icinde',
                'kategori_id' => $premiumLokasyonKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'aciklama' => 'Doğa içinde/orman kenarı/sessiz lokasyon',
                'order' => 4,
            ],
            [
                'name' => 'Marina Yakını',
                'slug' => 'marina-yakini',
                'kategori_id' => $premiumLokasyonKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Marina/yat limanı yakınında',
                'order' => 5,
            ],
        ];

        foreach ($premiumLokasyonOzellikleri as $ozellik) {
            Ozellik::updateOrCreate(
                ['slug' => $ozellik['slug']],
                $ozellik
            );
        }
        $this->command->info("✅ Premium Lokasyon: 5 özellik");

        // ✅ 3. Site İçi Özellikler
        $siteOzellikleriKategori = OzellikKategori::updateOrCreate(
            ['slug' => 'site-ici-ozellikler'],
            [
                'name' => 'Site İçi Özellikler',
                'slug' => 'site-ici-ozellikler',
                'aciklama' => 'Sitede bulunan ortak kullanım alanları ve hizmetler',
                'icon' => 'building',
                'order' => 28,
                'status' => 'Aktif',
            ]
        );

        $siteOzellikleri = [
            [
                'name' => 'Ortak Havuz',
                'slug' => 'ortak-havuz',
                'kategori_id' => $siteOzellikleriKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'aciklama' => 'Sitede ortak kullanımlı havuz',
                'order' => 1,
            ],
            [
                'name' => 'Fitness Center',
                'slug' => 'fitness-center',
                'kategori_id' => $siteOzellikleriKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Site içi fitness salonu',
                'order' => 2,
            ],
            [
                'name' => 'Spor Alanları',
                'slug' => 'spor-alanlari',
                'kategori_id' => $siteOzellikleriKategori->id,
                'veri_tipi' => 'multiselect',
                'veri_secenekleri' => json_encode(['Tenis Kortu', 'Basketbol', 'Voleybol', 'Futbol Sahası', 'Mini Golf']),
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Site içi spor tesisleri',
                'order' => 3,
            ],
            [
                'name' => 'Restoran/Kafe',
                'slug' => 'restoran-kafe',
                'kategori_id' => $siteOzellikleriKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Site içi restoran veya kafe',
                'order' => 4,
            ],
            [
                'name' => 'Market/Bakkal',
                'slug' => 'market-bakkal',
                'kategori_id' => $siteOzellikleriKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Site içi market veya bakkal',
                'order' => 5,
            ],
        ];

        foreach ($siteOzellikleri as $ozellik) {
            Ozellik::updateOrCreate(
                ['slug' => $ozellik['slug']],
                $ozellik
            );
        }
        $this->command->info("✅ Site İçi Özellikler: 5 özellik");

        // ✅ 4. Özel Hedef Kitle Özellikleri
        $hedefKitleKategori = OzellikKategori::updateOrCreate(
            ['slug' => 'hedef-kitle'],
            [
                'name' => 'Özel Hedef Kitle',
                'slug' => 'hedef-kitle',
                'aciklama' => 'Tesettürlü, aileler, çocuklu, engelli gibi özel kitle özellikleri',
                'icon' => 'users',
                'order' => 29,
                'status' => 'Aktif',
            ]
        );

        $hedefKitleOzellikleri = [
            [
                'name' => 'Tesettüre Uygun',
                'slug' => 'tesetture-uygun',
                'kategori_id' => $hedefKitleKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'aciklama' => 'Yüksek duvar/çit, korunaklı havuz, mahremiyete uygun',
                'order' => 1,
            ],
            [
                'name' => 'Çocuk Dostu',
                'slug' => 'cocuk-dostu',
                'kategori_id' => $hedefKitleKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => true,
                'aciklama' => 'Çocuk havuzu, oyun alanı, güvenli ortam',
                'order' => 2,
            ],
            [
                'name' => 'Engelli Erişimi',
                'slug' => 'engelli-erisimi',
                'kategori_id' => $hedefKitleKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Tekerlekli sandalye erişimi, rampa, geniş koridor',
                'order' => 3,
            ],
            [
                'name' => 'Yaşlı Dostu',
                'slug' => 'yasli-dostu',
                'kategori_id' => $hedefKitleKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => true,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Tek kat, asansör, güvenli ortam',
                'order' => 4,
            ],
        ];

        foreach ($hedefKitleOzellikleri as $ozellik) {
            Ozellik::updateOrCreate(
                ['slug' => $ozellik['slug']],
                $ozellik
            );
        }
        $this->command->info("✅ Özel Hedef Kitle: 4 özellik");

        // ✅ 5. Fiyata Dahil/Dahil Değil Özellikler
        $fiyatDahilKategori = OzellikKategori::updateOrCreate(
            ['slug' => 'fiyata-dahil'],
            [
                'name' => 'Fiyata Dahil Olanlar',
                'slug' => 'fiyata-dahil',
                'aciklama' => 'Kiralama fiyatına dahil olan hizmetler',
                'icon' => 'check-circle',
                'order' => 30,
                'status' => 'Aktif',
            ]
        );

        $fiyatDahilOzellikleri = [
            [
                'name' => 'Çarşaf/Havlu Dahil',
                'slug' => 'carsaf-havlu-dahil',
                'kategori_id' => $fiyatDahilKategori->id,
                'veri_tipi' => 'boolean',
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => true,
                'aciklama' => 'Yatak çarşafı ve havlu fiyata dahil',
                'order' => 1,
            ],
            [
                'name' => 'Elektrik/Su Dahil',
                'slug' => 'elektrik-su-dahil',
                'kategori_id' => $fiyatDahilKategori->id,
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode(['Hepsi Dahil', 'Elektrik Dahil', 'Su Dahil', 'Ayrı Ödenecek']),
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Elektrik ve su faturası durumu',
                'order' => 2,
            ],
            [
                'name' => 'Klima Kullanımı',
                'slug' => 'klima-kullanimi',
                'kategori_id' => $fiyatDahilKategori->id,
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode(['Dahil', 'Sınırsız Dahil', 'Günlük 6 Saat', 'Ek Ücretli']),
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Klima kullanım limiti',
                'order' => 3,
            ],
            [
                'name' => 'Havuz Bakımı',
                'slug' => 'havuz-bakimi',
                'kategori_id' => $fiyatDahilKategori->id,
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode(['Günlük', 'Haftalık', 'Gerektiğinde', 'Misafir Sorumlu']),
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Havuz temizlik/bakım sıklığı',
                'order' => 4,
            ],
            [
                'name' => 'Bahçe Bakımı',
                'slug' => 'bahce-bakimi',
                'kategori_id' => $fiyatDahilKategori->id,
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode(['Dahil', 'Haftalık', 'Aylık', 'Yok']),
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Bahçe/peyzaj bakım hizmeti',
                'order' => 5,
            ],
        ];

        foreach ($fiyatDahilOzellikleri as $ozellik) {
            Ozellik::updateOrCreate(
                ['slug' => $ozellik['slug']],
                $ozellik
            );
        }
        $this->command->info("✅ Fiyata Dahil Olanlar: 5 özellik");

        // ✅ 6. Ekstra Hizmetler (Fiyata Dahil Değil)
        $ekstraHizmetKategori = OzellikKategori::updateOrCreate(
            ['slug' => 'ekstra-hizmetler'],
            [
                'name' => 'Ekstra Hizmetler',
                'slug' => 'ekstra-hizmetler',
                'aciklama' => 'Ek ücret karşılığı sunulan hizmetler',
                'icon' => 'plus-circle',
                'order' => 31,
                'status' => 'Aktif',
            ]
        );

        $ekstraHizmetOzellikleri = [
            [
                'name' => 'Havaalanı Transferi',
                'slug' => 'havalimani-transferi',
                'kategori_id' => $ekstraHizmetKategori->id,
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode(['Yok', 'Ücretli', 'Ücretsiz']),
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Havaalanı transfer hizmeti',
                'order' => 1,
            ],
            [
                'name' => 'Günlük Temizlik',
                'slug' => 'gunluk-temizlik',
                'kategori_id' => $ekstraHizmetKategori->id,
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode(['Dahil', 'Ek Ücretli', 'Yok']),
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Günlük temizlik hizmeti',
                'order' => 2,
            ],
            [
                'name' => 'Yemek Servisi',
                'slug' => 'yemek-servisi',
                'kategori_id' => $ekstraHizmetKategori->id,
                'veri_tipi' => 'multiselect',
                'veri_secenekleri' => json_encode(['Kahvaltı', 'Öğle Yemeği', 'Akşam Yemeği', 'All Inclusive']),
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Yemek hizmeti seçenekleri',
                'order' => 3,
            ],
            [
                'name' => 'Çamaşır Yıkama',
                'slug' => 'camasir-yikama',
                'kategori_id' => $ekstraHizmetKategori->id,
                'veri_tipi' => 'select',
                'veri_secenekleri' => json_encode(['Dahil', 'Ek Ücretli', 'Self-Service', 'Yok']),
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Çamaşır yıkama hizmeti',
                'order' => 4,
            ],
            [
                'name' => 'Bebek Ekipmanı',
                'slug' => 'bebek-ekipmani',
                'kategori_id' => $ekstraHizmetKategori->id,
                'veri_tipi' => 'multiselect',
                'veri_secenekleri' => json_encode(['Bebek Beşiği', 'Mama Sandalyesi', 'Bebek Küveti', 'Oyun Parkı']),
                'zorunlu' => false,
                'arama_filtresi' => false,
                'ilan_kartinda_goster' => false,
                'aciklama' => 'Bebek ekipmanları (ücretsiz/ücretli)',
                'order' => 5,
            ],
        ];

        foreach ($ekstraHizmetOzellikleri as $ozellik) {
            Ozellik::updateOrCreate(
                ['slug' => $ozellik['slug']],
                $ozellik
            );
        }
        $this->command->info("✅ Ekstra Hizmetler: 5 özellik");

        $this->command->info("\n📊 YAZLIK EKSTRA ÖZELLİKLER RAPORU:");
        $this->command->info("   ✅ Yeni Kategori: 4");
        $this->command->info("   ✅ Yeni Özellik: 22");
        $this->command->info("   📜 Lisans/Belge: 3");
        $this->command->info("   🌟 Premium Lokasyon: 5");
        $this->command->info("   🏢 Site İçi: 5");
        $this->command->info("   👥 Hedef Kitle: 4");
        $this->command->info("   ✅ Fiyata Dahil: 5");

        $this->command->info("\n🎯 ÖNE ÇIKAN ÖZELLİKLER:");
        $this->command->info("   🏖️ Denize Sıfır");
        $this->command->info("   🏝️  Özel Plajlı");
        $this->command->info("   🏊 Özel Havuzlu");
        $this->command->info("   🧕 Tesettüre Uygun");
        $this->command->info("   🌲 Korunaklı/Doğa İçinde");
        $this->command->info("   📜 Turizm Belgeli");
    }
}

