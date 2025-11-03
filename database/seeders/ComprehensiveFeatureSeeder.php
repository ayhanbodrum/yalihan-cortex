<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\FeatureCategory;
use App\Models\FeatureTranslation;
use App\Models\OzellikKategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComprehensiveFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gerçek Emlak Özellikleri - Türkiye Emlak Sektörüne Uygun
        $this->seedGenelOzellikler();
        $this->seedKonutOzellikleri();
        $this->seedArsaOzellikleri();
        $this->seedIsyeriOzellikleri();
        $this->seedYazlikOzellikleri();

        $this->command->info('✅ Comprehensive Feature Seeding completed successfully!');
        $this->command->info('📊 Total Features Created: ' . Feature::count());
    }

    /**
     * Genel Özellikler (Tüm emlak türleri için)
     */
    private function seedGenelOzellikler()
    {
        $category = FeatureCategory::where('slug', 'genel-ozellikler')->first();
        $ozellikKategori = OzellikKategori::where('ad', 'Temel Bilgiler')->first();

        $genelOzellikler = [
            // Temel Alan Bilgileri
            [
                'name' => 'Brüt Alan',
                'description' => 'Gayrimenkulün toplam brüt alanı (m²)',
                'type' => 'number',
                'options' => null,
                'slug' => 'brut-alan',
                'applies_to' => 'konut,arsa,isyeri,yazlik',
                'is_required' => true,
                'display_order' => 1
            ],
            [
                'name' => 'Net Alan',
                'description' => 'Gayrimenkulün kullanılabilir net alanı (m²)',
                'type' => 'number',
                'options' => null,
                'slug' => 'net-alan',
                'applies_to' => 'konut,arsa,isyeri,yazlik',
                'is_required' => true,
                'display_order' => 2
            ],

            // Yapım & Yaş Bilgileri
            [
                'name' => 'Yapım Yılı',
                'description' => 'Gayrimenkulün inşa edildiği yıl',
                'type' => 'number',
                'options' => null,
                'slug' => 'yapim-yili',
                'applies_to' => 'konut,isyeri,yazlik',
                'is_required' => false,
                'display_order' => 3
            ],
            [
                'name' => 'Bina Yaşı',
                'description' => 'Binanın yaşı (yıl)',
                'type' => 'select',
                'options' => '0 (Sıfır Bina),1-5,6-10,11-15,16-20,21-25,26-30,30+',
                'slug' => 'bina-yasi',
                'applies_to' => 'konut,isyeri,yazlik',
                'is_required' => false,
                'display_order' => 4
            ],

            // Konum & Yön
            [
                'name' => 'Cephe Yönü',
                'description' => 'Gayrimenkulün cephe yönü',
                'type' => 'select',
                'options' => 'Kuzey,Güney,Doğu,Batı,Güneydoğu,Güneybatı,Kuzeydoğu,Kuzeybatı',
                'slug' => 'cephe-yonu',
                'applies_to' => 'konut,isyeri,yazlik',
                'is_required' => false,
                'display_order' => 5
            ],
            [
                'name' => 'Kat Sayısı',
                'description' => 'Binanın toplam kat sayısı',
                'type' => 'select',
                'options' => '1,2,3,4,5,6,7,8,9,10,11-15,16-20,21-30,30+',
                'slug' => 'kat-sayisi',
                'applies_to' => 'konut,isyeri',
                'is_required' => false,
                'display_order' => 6
            ],
            [
                'name' => 'Bulunduğu Kat',
                'description' => 'Gayrimenkulün bulunduğu kat',
                'type' => 'select',
                'options' => 'Bodrum 3,Bodrum 2,Bodrum 1,Zemin,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21+,Çatı Katı',
                'slug' => 'bulundugu-kat',
                'applies_to' => 'konut,isyeri',
                'is_required' => false,
                'display_order' => 7
            ],

            // Tapu & Hukuki Durum
            [
                'name' => 'Tapu Durumu',
                'description' => 'Gayrimenkulün tapu statusu',
                'type' => 'select',
                'options' => 'Kat Mülkiyetli,Kat İrtifaklı,Arsa Paylı,Müstakil Tapulu,Tarla,Bahçe',
                'slug' => 'tapu-statusu',
                'applies_to' => 'konut,arsa,isyeri,yazlik',
                'is_required' => true,
                'display_order' => 8
            ],
            [
                'name' => 'Takas',
                'description' => 'Takas kabul edilip edilmeyeceği',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'takas',
                'applies_to' => 'konut,arsa,isyeri,yazlik',
                'is_required' => false,
                'display_order' => 9
            ],
            [
                'name' => 'Kredi Uygunluğu',
                'description' => 'Bankadan kredi çekilebilir mi?',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'kredi-uygunlugu',
                'applies_to' => 'konut,arsa,isyeri,yazlik',
                'is_required' => false,
                'display_order' => 10
            ]
        ];

        $this->createFeaturesWithTranslations($genelOzellikler, $category, $ozellikKategori);
        $this->command->info('✅ Genel Özellikler eklendi: ' . count($genelOzellikler));
    }

    /**
     * Konut Özellikleri
     */
    private function seedKonutOzellikleri()
    {
        $category = FeatureCategory::where('slug', 'konut-ozellikleri')->first();
        $ozellikKategoriOda = OzellikKategori::where('ad', 'Oda ve Alan')->first();
        $ozellikKategoriEk = OzellikKategori::where('ad', 'Ek Özellikler')->first();

        $konutOzellikleri = [
            // Oda & Mekan Bilgileri
            [
                'name' => 'Oda Sayısı',
                'description' => 'Konutun oda sayısı',
                'type' => 'select',
                'options' => 'Stüdyo (1+0),1+1,1.5+1,2+1,2.5+1,3+1,3.5+1,4+1,4.5+1,5+1,5.5+1,6+1,6+2,7+1,7+2,8+1,8+2,9+1,10+1',
                'slug' => 'oda-sayisi',
                'applies_to' => 'konut',
                'is_required' => true,
                'display_order' => 1
            ],
            [
                'name' => 'Banyo Sayısı',
                'description' => 'Konutun banyo sayısı',
                'type' => 'select',
                'options' => '1,2,3,4,5,6+',
                'slug' => 'banyo-sayisi',
                'applies_to' => 'konut',
                'is_required' => true,
                'display_order' => 2
            ],
            [
                'name' => 'Salon Sayısı',
                'description' => 'Konutun salon sayısı',
                'type' => 'select',
                'options' => '1,2,3,4+',
                'slug' => 'salon-sayisi',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 3
            ],
            [
                'name' => 'Balkon Sayısı',
                'description' => 'Konutun balkon sayısı',
                'type' => 'select',
                'options' => '0,1,2,3,4+',
                'slug' => 'balkon-sayisi',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 4
            ],

            // Isitma & Soğutma
            [
                'name' => 'Isıtma Tipi',
                'description' => 'Konutun ısıtma sistemi',
                'type' => 'select',
                'options' => 'Yok,Soba,Doğalgaz (Kombi),Doğalgaz (Merkezi),Elektrikli Radyatör,Klima,Yerden Isıtma,Güneş Enerjisi,Jeotermal,Şömine',
                'slug' => 'isitma-tipi',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 5
            ],
            [
                'name' => 'Yakıt Tipi',
                'description' => 'Kullanılan yakıt türü',
                'type' => 'select',
                'options' => 'Doğalgaz,Elektrik,Kömür,Odun,LPG,Güneş Enerjisi,Jeotermal',
                'slug' => 'yakit-tipi',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 6
            ],

            // Güvenlik & Konfor
            [
                'name' => 'Güvenlik',
                'description' => 'Güvenlik sistemleri',
                'type' => 'select',
                'options' => 'Yok,7/24 Güvenlik,Güvenlik Kamerası,Diafon,Kapıcı',
                'slug' => 'guvenlik',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 7
            ],
            [
                'name' => 'Asansör',
                'description' => 'Asansör bulunup bulunmadığı',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'asansor',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 8
            ],
            [
                'name' => 'Otopark',
                'description' => 'Otopark statusu',
                'type' => 'select',
                'options' => 'Yok,Açık Otopark,Kapalı Otopark,Sokak Üstü Park,Mekanik Otopark',
                'slug' => 'otopark',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 9
            ],

            // Mutfak & Donanım
            [
                'name' => 'Mutfak',
                'description' => 'Mutfak statusu',
                'type' => 'select',
                'options' => 'Yok,Açık Mutfak,Kapalı Mutfak,Amerikan Mutfak,Ankastre Mutfak',
                'slug' => 'mutfak',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 10
            ],
            [
                'name' => 'Beyaz Eşya',
                'description' => 'Beyaz eşya statusu',
                'type' => 'select',
                'options' => 'Yok,Kısmi,Tam Set,Ankastre Set',
                'slug' => 'beyaz-esya',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 11
            ],
            [
                'name' => 'Eşyalı',
                'description' => 'Mobilyalı mı?',
                'type' => 'select',
                'options' => 'Hayır,Kısmen,Tamamen',
                'slug' => 'esyali',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 12
            ],

            // Site & Yapısal Özellikler
            [
                'name' => 'Site İçerisinde',
                'description' => 'Site içinde mi?',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'site-icerisinde',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 13
            ],
            [
                'name' => 'Havuz',
                'description' => 'Havuz bulunup bulunmadığı',
                'type' => 'select',
                'options' => 'Yok,Site Havuzu,Özel Havuz,Kapalı Havuz,Çocuk Havuzu',
                'slug' => 'havuz',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 14
            ],
            [
                'name' => 'Aidat',
                'description' => 'Aylık aidat miktarı (TL)',
                'type' => 'number',
                'options' => null,
                'slug' => 'aidat',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 15
            ],
            [
                'name' => 'Kullanım Durumu',
                'description' => 'Konutun kullanım statusu',
                'type' => 'select',
                'options' => 'Boş,Kiracılı,Mülk Sahibi,Yatırım Amaçlı',
                'slug' => 'kullanim-statusu',
                'applies_to' => 'konut',
                'is_required' => false,
                'display_order' => 16
            ]
        ];

        // İlk yarı Oda ve Alan kategorisine
        $this->createFeaturesWithTranslations(
            array_slice($konutOzellikleri, 0, 8),
            $category,
            $ozellikKategoriOda
        );

        // İkinci yarı Ek Özellikler kategorisine
        $this->createFeaturesWithTranslations(
            array_slice($konutOzellikleri, 8),
            $category,
            $ozellikKategoriEk
        );

        $this->command->info('✅ Konut Özellikleri eklendi: ' . count($konutOzellikleri));
    }

    /**
     * Arsa Özellikleri
     */
    private function seedArsaOzellikleri()
    {
        $category = FeatureCategory::where('slug', 'arsa-ozellikleri')->first();
        $ozellikKategori = OzellikKategori::where('ad', 'Konum ve Çevre')->first();

        $arsaOzellikleri = [
            // İmar & Zoning
            [
                'name' => 'İmar Durumu',
                'description' => 'Arsanın imar statusu',
                'type' => 'select',
                'options' => 'İmarlı,İmarsız,Ticari İmar,Konut İmarı,Sanayi İmarı,Turizm İmarı,Tarla,Bahçe,Orman,Zeytinlik',
                'slug' => 'imar-statusu',
                'applies_to' => 'arsa',
                'is_required' => true,
                'display_order' => 1
            ],
            [
                'name' => 'İfraz / Tevhit',
                'description' => 'İfraz veya tevhit statusu',
                'type' => 'select',
                'options' => 'Mevcut,İfraz Edilebilir,Tevhit Edilebilir,Her İkisi de',
                'slug' => 'ifraz-tevhit',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 2
            ],
            [
                'name' => 'Gabari',
                'description' => 'Maksimum bina yüksekliği',
                'type' => 'select',
                'options' => '2.5,3.5,4.5,6.5,9.5,12.5,15.5,18.5,21.5,25.5,30.5,Sınırsız',
                'slug' => 'gabari',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 3
            ],
            [
                'name' => 'Emsal',
                'description' => 'Kat alanları toplamı oranı',
                'type' => 'select',
                'options' => '0.15,0.20,0.25,0.30,0.35,0.40,0.50,0.60,0.80,1.00,1.25,1.50,1.80,2.00,2.50,3.00,3.50,4.00',
                'slug' => 'emsal',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 4
            ],
            [
                'name' => 'TAKS',
                'description' => 'Taban alanı katsayısı',
                'type' => 'select',
                'options' => '0.10,0.15,0.20,0.25,0.30,0.35,0.40,0.45,0.50,0.60,0.70',
                'slug' => 'taks',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 5
            ],

            // Altyapı & Hizmetler
            [
                'name' => 'Elektrik',
                'description' => 'Elektrik altyapısı',
                'type' => 'select',
                'options' => 'Yok,Arsanın Yanında,Arsanın İçinden Geçiyor',
                'slug' => 'elektrik',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 6
            ],
            [
                'name' => 'Su',
                'description' => 'Su altyapısı',
                'type' => 'select',
                'options' => 'Yok,Şebeke Suyu,Kuyu,Kaynak Suyu,Keson',
                'slug' => 'su',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 7
            ],
            [
                'name' => 'Doğalgaz',
                'description' => 'Doğalgaz altyapısı',
                'type' => 'select',
                'options' => 'Yok,Arsanın Yanında,Arsanın İçinden Geçiyor',
                'slug' => 'dogalgaz',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 8
            ],
            [
                'name' => 'Telefon',
                'description' => 'Telefon altyapısı',
                'type' => 'select',
                'options' => 'Yok,Var,Fiber Optik',
                'slug' => 'telefon',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 9
            ],
            [
                'name' => 'İnternet',
                'description' => 'İnternet altyapısı',
                'type' => 'select',
                'options' => 'Yok,ADSL,Fiber,Kablolu,Uydu',
                'slug' => 'internet',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 10
            ],

            // Coğrafi & Çevresel
            [
                'name' => 'Arsa Eğimi',
                'description' => 'Arsanın eğim statusu',
                'type' => 'select',
                'options' => 'Düz,Az Eğimli,Orta Eğimli,Çok Eğimli,Meyilli,Teraslı',
                'slug' => 'arsa-egimi',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 11
            ],
            [
                'name' => 'Arsa Manzarası',
                'description' => 'Arsanın manzara statusu',
                'type' => 'select',
                'options' => 'Yok,Şehir Manzarası,Doğa Manzarası,Deniz Manzarası,Göl Manzarası,Dağ Manzarası,Boğaz Manzarası',
                'slug' => 'arsa-manzarasi',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 12
            ],
            [
                'name' => 'Yol Bağlantısı',
                'description' => 'Yol statusu',
                'type' => 'select',
                'options' => 'Asfalt Yol,Parke Yol,Stabilize Yol,Toprak Yol,Ana Yol Üzeri',
                'slug' => 'yol-baglantisi',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 13
            ],
            [
                'name' => 'Arsa İçi Durum',
                'description' => 'Arsanın içindeki statusu',
                'type' => 'select',
                'options' => 'Boş Arsa,Meyveli Ağaçlar,Zeytinlik,Bağ,Bahçe,Çiftlik Binası,Harabe,Yıkıntı',
                'slug' => 'arsa-ici-status',
                'applies_to' => 'arsa',
                'is_required' => false,
                'display_order' => 14
            ]
        ];

        $this->createFeaturesWithTranslations($arsaOzellikleri, $category, $ozellikKategori);
        $this->command->info('✅ Arsa Özellikleri eklendi: ' . count($arsaOzellikleri));
    }

    /**
     * İşyeri Özellikleri
     */
    private function seedIsyeriOzellikleri()
    {
        $category = FeatureCategory::where('slug', 'isyeri-ozellikleri')->first();
        $ozellikKategoriEk = OzellikKategori::where('ad', 'Ek Özellikler')->first();
        $ozellikKategoriFiyat = OzellikKategori::where('ad', 'Fiyat ve Ödeme')->first();

        $isyeriOzellikleri = [
            // İşyeri Türü & Kullanım
            [
                'name' => 'İşyeri Türü',
                'description' => 'İşyerinin kullanım türü',
                'type' => 'select',
                'options' => 'Dükkan,Mağaza,Ofis,Büro,Atölye,Depo,Fabrika,İmalathane,Showroom,Kafe,Restoran,Otel,Hastane,Okul,Spor Salonu,Kuaför,Berber',
                'slug' => 'isyeri-turu',
                'applies_to' => 'isyeri',
                'is_required' => true,
                'display_order' => 1
            ],
            [
                'name' => 'Ticaret Unvanı',
                'description' => 'İşletme türü açıklaması',
                'type' => 'text',
                'options' => null,
                'slug' => 'ticaret-unvani',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 2
            ],
            [
                'name' => 'Kira Getirisi',
                'description' => 'Aylık kira geliri (TL)',
                'type' => 'number',
                'options' => null,
                'slug' => 'kira-getirisi',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 3
            ],

            // Teknik Özellikler
            [
                'name' => 'Tavan Yüksekliği',
                'description' => 'Tavan yüksekliği (metre)',
                'type' => 'select',
                'options' => '2.5,2.7,3.0,3.2,3.5,4.0,4.5,5.0,5.5,6.0,6.5,7.0+',
                'slug' => 'tavan-yuksekligi',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 4
            ],
            [
                'name' => 'Kalorifer Tipi',
                'description' => 'Isıtma sistemi',
                'type' => 'select',
                'options' => 'Yok,Merkezi,Kombi,Klima,Yerden Isıtma,Fancoil,VRV',
                'slug' => 'kalorifer-tipi',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 5
            ],
            [
                'name' => 'Klima',
                'description' => 'Klima sistemi',
                'type' => 'select',
                'options' => 'Yok,Split Klima,VRV Sistem,Merkezi Klima,Chiller Sistem',
                'slug' => 'klima',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 6
            ],
            [
                'name' => 'Jeneratör',
                'description' => 'Jeneratör statusu',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'jenerator',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 7
            ],

            // Erişim & Konum
            [
                'name' => 'Vitrin',
                'description' => 'Vitrin statusu',
                'type' => 'select',
                'options' => 'Yok,Köşe Başı,Cadde Üzeri,Ana Yol Üzeri,İç Sokak',
                'slug' => 'vitrin',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 8
            ],
            [
                'name' => 'Giriş Yüksekliği',
                'description' => 'Giriş kapısı yüksekliği (metre)',
                'type' => 'select',
                'options' => '2.0,2.2,2.5,2.7,3.0,3.5,4.0,4.5+',
                'slug' => 'giris-yuksekligi',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 9
            ],
            [
                'name' => 'WC Sayısı',
                'description' => 'WC-Tuvalet sayısı',
                'type' => 'select',
                'options' => '0,1,2,3,4,5+',
                'slug' => 'wc-sayisi',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 10
            ],

            // Güvenlik & Sistem
            [
                'name' => 'Yangın Çıkışı',
                'description' => 'Yangın çıkışı bulunup bulunmadığı',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'yangin-cikisi',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 11
            ],
            [
                'name' => 'Engelli Erişimi',
                'description' => 'Engelli erişimi uygunluğu',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'engelli-erisimi',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 12
            ],
            [
                'name' => 'İşletme Ruhsatı',
                'description' => 'İşletme ruhsatı statusu',
                'type' => 'select',
                'options' => 'Var,Yok,Alınabilir,Sorunlu',
                'slug' => 'isletme-ruhsati',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 13
            ],
            [
                'name' => 'Devren Satış',
                'description' => 'Devren satış mı?',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'devren-satis',
                'applies_to' => 'isyeri',
                'is_required' => false,
                'display_order' => 14
            ]
        ];

        // İlk yarı Ek Özellikler kategorisine
        $this->createFeaturesWithTranslations(
            array_slice($isyeriOzellikleri, 0, 7),
            $category,
            $ozellikKategoriEk
        );

        // İkinci yarı Fiyat ve Ödeme kategorisine
        $this->createFeaturesWithTranslations(
            array_slice($isyeriOzellikleri, 7),
            $category,
            $ozellikKategoriFiyat
        );

        $this->command->info('✅ İşyeri Özellikleri eklendi: ' . count($isyeriOzellikleri));
    }

    /**
     * Yazlık Özellikleri
     */
    private function seedYazlikOzellikleri()
    {
        $category = FeatureCategory::where('slug', 'yazlik-ozellikleri')->first();
        $ozellikKategori = OzellikKategori::where('ad', 'Konum ve Çevre')->first();

        $yazlikOzellikleri = [
            // Lokasyon & Çevre
            [
                'name' => 'Denize Mesafe',
                'description' => 'Denize olan mesafe',
                'type' => 'select',
                'options' => 'Deniz Kenarı,50m İçinde,100m İçinde,200m İçinde,500m İçinde,1km İçinde,1-5km Arası,5km+',
                'slug' => 'denize-mesafe',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 1
            ],
            [
                'name' => 'Manzara',
                'description' => 'Manzara türü',
                'type' => 'select',
                'options' => 'Deniz Manzarası,Göl Manzarası,Doğa Manzarası,Dağ Manzarası,Bahçe Manzarası,Havuz Manzarası',
                'slug' => 'manzara',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 2
            ],
            [
                'name' => 'Plaj',
                'description' => 'Plaj erişimi',
                'type' => 'select',
                'options' => 'Özel Plaj,Halk Plajı,Plaja Yakın,Plajdan Uzak',
                'slug' => 'plaj',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 3
            ],
            [
                'name' => 'Bahçe',
                'description' => 'Bahçe statusu',
                'type' => 'select',
                'options' => 'Yok,Özel Bahçe,Ortak Bahçe,Peyzajlı Bahçe,Meyve Bahçesi',
                'slug' => 'bahce',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 4
            ],

            // Rekreasyon & Eğlence
            [
                'name' => 'Özel Havuz',
                'description' => 'Özel havuz statusu',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'ozel-havuz',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 5
            ],
            [
                'name' => 'Jakuzi',
                'description' => 'Jakuzi bulunup bulunmadığı',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'jakuzi',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 6
            ],
            [
                'name' => 'Sauna',
                'description' => 'Sauna bulunup bulunmadığı',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'sauna',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 7
            ],
            [
                'name' => 'Tenis Kortu',
                'description' => 'Tenis kortu erişimi',
                'type' => 'select',
                'options' => 'Yok,Ortak Kort,Özel Kort',
                'slug' => 'tenis-kortu',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 8
            ],
            [
                'name' => 'Spor Salonu',
                'description' => 'Spor salonu-fitness erişimi',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'spor-salonu',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 9
            ],

            // Konfor & Hizmet
            [
                'name' => 'Kamelyallı',
                'description' => 'Kamelya bulunup bulunmadığı',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'kamelyali',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 10
            ],
            [
                'name' => 'Barbekü',
                'description' => 'Barbekü alanı',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'barbeku',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 11
            ],
            [
                'name' => 'Çamaşırhane',
                'description' => 'Çamaşırhane bulunup bulunmadığı',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'camasirhane',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 12
            ],
            [
                'name' => 'Hizmetli Odası',
                'description' => 'Hizmetli odası bulunup bulunmadığı',
                'type' => 'boolean',
                'options' => null,
                'slug' => 'hizmetli-odasi',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 13
            ],
            [
                'name' => 'Kamelya',
                'description' => 'Kamelya adet sayısı',
                'type' => 'select',
                'options' => '0,1,2,3,4+',
                'slug' => 'kamelya',
                'applies_to' => 'yazlik',
                'is_required' => false,
                'display_order' => 14
            ]
        ];

        $this->createFeaturesWithTranslations($yazlikOzellikleri, $category, $ozellikKategori);
        $this->command->info('✅ Yazlık Özellikleri eklendi: ' . count($yazlikOzellikleri));
    }

    /**
     * Helper method to create features with translations
     */
    private function createFeaturesWithTranslations($features, $category, $ozellikKategori)
    {
        foreach ($features as $featureData) {
            // Slug üniqueness check - if exists, skip
            $existingFeature = Feature::withTrashed()->where('slug', $featureData['slug'])->first();
            if ($existingFeature) {
                $this->command->warn("Skipping duplicate feature: {$featureData['name']} (slug: {$featureData['slug']})");
                continue;
            }

            // Feature oluştur
            $feature = Feature::create([
                'category_id' => $category->id,
                'kategori_id' => $ozellikKategori->id,
                'type' => $featureData['type'],
                'options' => $featureData['options'] ? explode(',', $featureData['options']) : null,
                'slug' => $featureData['slug'],
                'applies_to' => $featureData['applies_to'],
                'status' => true,
                'is_required' => $featureData['is_required'],
                'display_order' => $featureData['display_order'],
            ]);

            // Feature Translation oluştur
            FeatureTranslation::create([
                'feature_id' => $feature->id,
                'locale' => 'tr',
                'name' => $featureData['name'],
                'description' => $featureData['description'],
            ]);
        }
    }
}
