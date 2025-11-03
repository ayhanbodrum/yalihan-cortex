<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Önce mevcut verileri temizle (eğer varsa)
        try {
            DB::table('kisiler')->where('id', '>', 0)->delete();
        } catch (\Exception $e) {
            // Tablo yoksa devam et
        }

        try {
            DB::table('people')->where('id', '>', 0)->delete();
        } catch (\Exception $e) {
            // Tablo yoksa devam et
        }

        // Test müşteri verileri
        $customers = [
            [
                'id' => 100,
                'ad' => 'Ahmet',
                'soyad' => 'Yılmaz',
                'email' => 'ahmet.yilmaz@example.com',
                'telefon' => '05321234567',
                'tc_kimlik' => '12345678901',
                'musteri_tipi' => 'ev_sahibi',
                'status' => 'Aktif',
                'kaynak' => 'Web Sitesi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 101,
                'ad' => 'Fatma',
                'soyad' => 'Demir',
                'email' => 'fatma.demir@example.com',
                'telefon' => '05329876543',
                'tc_kimlik' => '98765432109',
                'musteri_tipi' => 'alici',
                'status' => 'Aktif',
                'kaynak' => 'Telefon',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 102,
                'ad' => 'Mehmet',
                'soyad' => 'Kaya',
                'email' => 'mehmet.kaya@example.com',
                'telefon' => '05335554433',
                'tc_kimlik' => '11223344556',
                'musteri_tipi' => 'satici',
                'status' => 'Potansiyel',
                'kaynak' => 'Referans',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 103,
                'ad' => 'Ayşe',
                'soyad' => 'Özkan',
                'email' => 'ayse.ozkan@example.com',
                'telefon' => '05337778899',
                'tc_kimlik' => '55667788990',
                'musteri_tipi' => 'kiraci',
                'status' => 'Aktif',
                'kaynak' => 'Web Sitesi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 104,
                'ad' => 'Ali',
                'soyad' => 'Çelik',
                'email' => 'ali.celik@example.com',
                'telefon' => '05339998877',
                'tc_kimlik' => '99887766554',
                'musteri_tipi' => 'yatirimci',
                'status' => 'Aktif',
                'kaynak' => 'Telefon',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($customers as $customer) {
            DB::table('kisiler')->insert($customer);
        }

        // People tablosuna da aynı verileri ekle (tutarlılık için)
        foreach ($customers as $customer) {
            DB::table('people')->insert([
                'id' => $customer['id'],
                'ad' => $customer['ad'],
                'soyad' => $customer['soyad'],
                'tam_ad' => $customer['ad'] . ' ' . $customer['soyad'],
                'email' => $customer['email'],
                'telefon' => $customer['telefon'],
                'identity_number' => $customer['tc_kimlik'],
                'type' => $customer['musteri_tipi'],
                'status' => $customer['status'] === 'Aktif' ? 1 : 0,
                'notes' => 'Kaynak: ' . $customer['kaynak'],
                'created_at' => $customer['created_at'],
                'updated_at' => $customer['updated_at'],
            ]);
        }

        $this->command->info('Customer data seeded successfully!');
    }
}
