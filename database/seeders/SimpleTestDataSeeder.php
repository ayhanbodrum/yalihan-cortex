<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎯 Basit Test Verisi Oluşturuluyor...');

        // 1. Kategoriler
        $this->createCategories();

        // 2. Kişiler
        $this->createPersons();

        // 3. İlanlar
        $this->createListings();

        $this->command->info('✅ Test verisi başarıyla oluşturuldu!');
    }

    private function createCategories(): void
    {
        $this->command->info('📂 Kategoriler oluşturuluyor...');

        $categories = [
            ['name' => 'Konut', 'slug' => 'konut', 'seviye' => 1, 'status' => 1],
            ['name' => 'İşyeri', 'slug' => 'isyeri', 'seviye' => 1, 'status' => 1],
            ['name' => 'Arsa', 'slug' => 'arsa', 'seviye' => 1, 'status' => 1],
            ['name' => 'Turistik Tesis', 'slug' => 'turistik', 'seviye' => 1, 'status' => 1],
        ];

        foreach ($categories as $category) {
            $category['created_at'] = now();
            $category['updated_at'] = now();
            DB::table('ilan_kategorileri')->updateOrInsert(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('✅ ' . count($categories) . ' kategori oluşturuldu');
    }

    private function createPersons(): void
    {
        $this->command->info('👥 Kişiler oluşturuluyor...');

        $persons = [
            [
                'ad' => 'Ahmet',
                'soyad' => 'Yılmaz',
                'email' => 'ahmet@example.com',
                'telefon' => '+905551234567',
                'musteri_tipi' => 'ev_sahibi',
                'status' => 'status',
            ],
            [
                'ad' => 'Ayşe',
                'soyad' => 'Kara',
                'email' => 'ayse@example.com',
                'telefon' => '+905551234568',
                'musteri_tipi' => 'alici',
                'status' => 'status',
            ],
            [
                'ad' => 'Mehmet',
                'soyad' => 'Demir',
                'email' => 'mehmet@example.com',
                'telefon' => '+905551234569',
                'musteri_tipi' => 'yatirimci',
                'status' => 'status',
            ]
        ];

        foreach ($persons as $person) {
            $person['created_at'] = now();
            $person['updated_at'] = now();
            DB::table('kisiler')->updateOrInsert(
                ['email' => $person['email']],
                $person
            );
        }

        $this->command->info('✅ ' . count($persons) . ' kişi oluşturuldu');
    }

    private function createListings(): void
    {
        $this->command->info('🏠 İlanlar oluşturuluyor...');

        // İlk kategori ve kişi ID'lerini al
        $category = DB::table('ilan_kategorileri')->first();
        $person = DB::table('kisiler')->first();
        $location = DB::table('mahalleler')->first();

        if (!$category || !$person || !$location) {
            $this->command->warn('⚠️ Kategori, kişi veya lokasyon bulunamadı. İlan oluşturulamıyor.');
            return;
        }

        $listings = [
            [
                'baslik' => 'Deniz Manzaralı 2+1 Daire',
                'aciklama' => 'Bodrum Gümbet\'te deniz manzaralı, balkonlu 2+1 daire. Yüzme havuzu ve güvenlik mevcut.',
                'fiyat' => 2500000.00,
                'para_birimi' => 'TRY',
                'status' => 'status',
                'stage' => 'published',
                'is_draft' => false,
                'completion_percentage' => 100,
                'last_saved_at' => now(),
                'is_published' => 1,
                'enabled' => 1,
                'ana_kategori_id' => $category->id,
                'yayin_tipi_id' => 1, // Satılık
                'danisman_id' => 1,
                'ilan_sahibi_id' => $person->id,
                'il_id' => 48, // Muğla
                'ilce_id' => 1, // Bodrum
                'mahalle_id' => $location->id,
                'adres' => 'Gümbet Mahallesi, Bodrum/Muğla',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'baslik' => 'Merkezi Konumda Ofis',
                'aciklama' => 'İstanbul Kadıköy\'de merkezi konumda, asansörlü binada 120m² ofis.',
                'fiyat' => 8500.00,
                'para_birimi' => 'TRY',
                'status' => 'status',
                'stage' => 'published',
                'is_draft' => false,
                'completion_percentage' => 100,
                'last_saved_at' => now(),
                'is_published' => 1,
                'enabled' => 1,
                'ana_kategori_id' => $category->id,
                'yayin_tipi_id' => 2, // Kiralık
                'danisman_id' => 1,
                'ilan_sahibi_id' => $person->id,
                'il_id' => 34, // İstanbul
                'ilce_id' => 20, // Kadıköy
                'mahalle_id' => $location->id,
                'adres' => 'Kadıköy Merkez, İstanbul',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($listings as $listing) {
            $listing['created_at'] = now();
            $listing['updated_at'] = now();
            DB::table('ilanlar')->insert($listing);
        }

        $this->command->info('✅ ' . count($listings) . ' ilan oluşturuldu');
    }
}
