<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Context7 Listing Seeder
 *
 * Context7 standartlarına uygun ilan verileri.
 * Örnek ilanlar ve Context7 uyumlu veriler oluşturur.
 *
 * Context7 Standardı: C7-LISTING-SEEDER-2025-09-13
 * Versiyon: 4.0.0
 */
class Context7ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏠 Context7 İlan Verileri oluşturuluyor...');

        // 1. Örnek ilanlar oluştur
        $this->createSampleListings();

        $this->command->info('✅ Context7 ilan verileri başarıyla oluşturuldu!');
    }

    /**
     * Örnek ilanlar oluştur
     */
    private function createSampleListings(): void
    {
        $this->command->info('📋 Örnek ilanlar oluşturuluyor...');

        $listings = [
            [
                'ilan_basligi' => 'Bodrum Merkezde Satılık Villa',
                'ilan_aciklamasi' => 'Denize 200 metre mesafede, 3+1 villa. Bahçeli, havuzlu, deniz manzaralı. Yeni bina, asansörlü, güvenlikli.',
                'category_id' => 12, // Villa
                'yayinlama_tipi' => 'Satılık',
                'fiyat' => 2500000,
                'para_birimi' => 'TRY',
                'net_metrekare' => 180,
                'oda_sayisi' => '3+1',
                'banyo_sayisi' => '2',
                'bulundugu_kat' => '1',
                'bina_yasi' => '5',
                'isitma_tipi' => 'Kombi',
                'status' => 'Satılık',
                'enabled' => true,
                'is_published' => true,
                'il_id' => 48, // Muğla
                'ilce_id' => 1, // Bodrum
                'mahalle_id' => 1, // Gümbet
                'user_id' => 3, // Yunus Emre Gök
                'ilan_sahibi_id' => 12, // Ahmet Yılmaz
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(30),
            ],
            [
                'ilan_basligi' => 'Marmaris Marina Yakını Kiralık Daire',
                'ilan_aciklamasi' => 'Marina manzaralı, 2+1 daire. Mobilyalı, klimalı, güvenlikli site içinde. Deniz manzaralı, güneş alan.',
                'category_id' => 11, // Daire
                'yayinlama_tipi' => 'Kiralık',
                'fiyat' => 15000,
                'para_birimi' => 'TRY',
                'net_metrekare' => 120,
                'oda_sayisi' => '2+1',
                'banyo_sayisi' => '1',
                'bulundugu_kat' => '3',
                'bina_yasi' => '10',
                'isitma_tipi' => 'Merkezi',
                'status' => 'Kiralık',
                'enabled' => true,
                'is_published' => true,
                'il_id' => 48, // Muğla
                'ilce_id' => 2, // Marmaris
                'mahalle_id' => 11, // Armutalan
                'user_id' => 4, // Atılay Önen
                'ilan_sahibi_id' => 13, // Fatma Demir
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25),
            ],
            [
                'ilan_basligi' => 'Fethiye Çalış Plajı Yazlık Villa',
                'ilan_aciklamasi' => 'Denize sıfır, 4+1 yazlık villa. Özel plaj, tekne bağlama yeri, bahçeli. Yaz sezonu için ideal.',
                'category_id' => 42, // Yazlık
                'yayinlama_tipi' => 'Sezonluk Kiralık',
                'fiyat' => 18000,
                'para_birimi' => 'TRY',
                'net_metrekare' => 200,
                'oda_sayisi' => '4+1',
                'banyo_sayisi' => '3',
                'bulundugu_kat' => '1',
                'bina_yasi' => '3',
                'isitma_tipi' => 'Klima',
                'status' => 'Sezonluk Kiralık',
                'enabled' => true,
                'is_published' => true,
                'il_id' => 48, // Muğla
                'ilce_id' => 3, // Fethiye
                'mahalle_id' => 16, // Çalış
                'user_id' => 3, // Yunus Emre Gök
                'ilan_sahibi_id' => 43, // Mehmet Kaya
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
            [
                'ilan_basligi' => 'Datça Merkezde Satılık Arsa',
                'ilan_aciklamasi' => 'İmarlı arsa, 1000 m2. Deniz manzaralı, elektrik ve su bağlantısı mevcut. Yatırım için ideal.',
                'category_id' => 31, // İmarlı Arsa
                'yayinlama_tipi' => 'Satılık',
                'fiyat' => 800000,
                'para_birimi' => 'TRY',
                'net_metrekare' => 1000,
                'status' => 'Satılık',
                'enabled' => true,
                'is_published' => true,
                'il_id' => 48, // Muğla
                'ilce_id' => 4, // Datça
                'mahalle_id' => 1, // Datça Merkez
                'user_id' => 4, // Atılay Önen
                'ilan_sahibi_id' => 44, // Ayşe Özkan
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'ilan_basligi' => 'Bodrum Turgutreis İş Yeri',
                'ilan_aciklamasi' => 'Ana caddede, 150 m2 dükkan. Vitrinli, klimalı, güvenlikli. Ticari faaliyet için uygun.',
                'category_id' => 22, // Dükkan
                'yayinlama_tipi' => 'Kiralık',
                'fiyat' => 25000,
                'para_birimi' => 'TRY',
                'net_metrekare' => 150,
                'status' => 'Kiralık',
                'enabled' => true,
                'is_published' => true,
                'il_id' => 48, // Muğla
                'ilce_id' => 1, // Bodrum
                'mahalle_id' => 4, // Turgutreis
                'user_id' => 3, // Yunus Emre Gök
                'ilan_sahibi_id' => 45, // Ali Çelik
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'ilan_basligi' => 'Çeşme Alaçatı Lüks Rezidans',
                'ilan_aciklamasi' => 'Alaçatı merkezde, 5+1 lüks rezidans. Deniz manzaralı, havuzlu, güvenlikli site içinde.',
                'category_id' => 14, // Rezidans
                'yayinlama_tipi' => 'Satılık',
                'fiyat' => 3500000,
                'para_birimi' => 'TRY',
                'net_metrekare' => 250,
                'oda_sayisi' => '5+1',
                'banyo_sayisi' => '3',
                'bulundugu_kat' => '2',
                'bina_yasi' => '2',
                'isitma_tipi' => 'Merkezi',
                'status' => 'Satılık',
                'enabled' => true,
                'is_published' => true,
                'il_id' => 35, // İzmir
                'ilce_id' => 11, // Çeşme
                'mahalle_id' => 21, // Alaçatı
                'user_id' => 4, // Atılay Önen
                'ilan_sahibi_id' => 15, // Zeynep Arslan
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ];

        foreach ($listings as $listing) {
            $listingId = DB::table('ilanlar')->insertGetId($listing);
            $this->command->info("✅ İlan eklendi: {$listing['ilan_basligi']} (ID: {$listingId})");
        }

        $this->command->info('✅ ' . count($listings) . ' örnek ilan oluşturuldu');
    }
}
