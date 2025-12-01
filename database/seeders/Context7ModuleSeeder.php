<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Context7 Module Seeder
 *
 * Context7 standartlarına uygun modül verileri.
 * Arsa, yazlık, turistik tesis modüllerinin verilerini oluşturur.
 *
 * Context7 Standardı: C7-MODULE-SEEDER-2025-09-13
 * Versiyon: 4.0.0
 */
class Context7ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Context7 Modül Verileri oluşturuluyor...');

        // 1. Komisyon oranları oluştur
        $this->createCommissionRates();

        // 2. Vergi oranları oluştur
        $this->createTaxRates();

        // 3. Para birimleri oluştur
        $this->createCurrencies();

        // 4. Site ayarları oluştur
        $this->createSiteSettings();

        // 5. Başlangıç ayarları oluştur
        $this->createInitialSettings();

        $this->command->info('✅ Context7 modül verileri başarıyla oluşturuldu!');
    }

    /**
     * Komisyon oranları oluştur
     */
    private function createCommissionRates(): void
    {
        $this->command->info('💰 Komisyon oranları oluşturuluyor...');

        $commissionRates = [
            [
                'kategori' => 'konut',
                'alt_kategori' => 'daire',
                'satis_orani' => 3.0,
                'kiralama_orani' => 1.0,
                'min_komisyon' => 1000.00,
                'max_komisyon' => 50000.00,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'konut',
                'alt_kategori' => 'villa',
                'satis_orani' => 4.0,
                'kiralama_orani' => 1.5,
                'min_komisyon' => 5000.00,
                'max_komisyon' => 100000.00,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'is-yeri',
                'alt_kategori' => 'ofis',
                'satis_orani' => 5.0,
                'kiralama_orani' => 2.0,
                'min_komisyon' => 2000.00,
                'max_komisyon' => 75000.00,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'arsa',
                'alt_kategori' => 'imarli-arsa',
                'satis_orani' => 6.0,
                'kiralama_orani' => 0.0,
                'min_komisyon' => 3000.00,
                'max_komisyon' => 100000.00,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'turistik-tesis',
                'alt_kategori' => 'yazlik',
                'satis_orani' => 4.0,
                'kiralama_orani' => 3.0,
                'min_komisyon' => 2000.00,
                'max_komisyon' => 50000.00,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($commissionRates as $rate) {
            DB::table('komisyon_oranlari')->updateOrInsert(
                [
                    'kategori' => $rate['kategori'],
                    'alt_kategori' => $rate['alt_kategori'],
                ],
                $rate
            );
        }

        $this->command->info('✅ '.count($commissionRates).' komisyon oranı oluşturuldu');
    }

    /**
     * Vergi oranları oluştur
     */
    private function createTaxRates(): void
    {
        $this->command->info('📊 Vergi oranları oluşturuluyor...');

        $taxRates = [
            [
                'name' => 'KDV',
                'code' => 'KDV',
                'rate' => 18.0,
                'description' => 'Katma Değer Vergisi',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emlak Vergisi',
                'code' => 'EMLAK',
                'rate' => 0.1,
                'description' => 'Emlak Vergisi',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tapu Harcı',
                'code' => 'TAPU',
                'rate' => 4.0,
                'description' => 'Tapu Harcı',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($taxRates as $rate) {
            DB::table('tax_rates')->updateOrInsert(
                ['name' => $rate['name']],
                $rate
            );
        }

        $this->command->info('✅ '.count($taxRates).' vergi oranı oluşturuldu');
    }

    /**
     * Para birimleri oluştur
     */
    private function createCurrencies(): void
    {
        $this->command->info('💱 Para birimleri oluşturuluyor...');

        $currencies = [
            [
                'kod' => 'TRY',
                'ad' => 'Türk Lirası',
                'sembol' => '₺',
                'kur' => 1.0,
                'status' => true,
                'varsayilan' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kod' => 'USD',
                'ad' => 'Amerikan Doları',
                'sembol' => '$',
                'kur' => 30.5,
                'status' => true,
                'varsayilan' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kod' => 'EUR',
                'ad' => 'Euro',
                'sembol' => '€',
                'kur' => 33.2,
                'status' => true,
                'varsayilan' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kod' => 'GBP',
                'ad' => 'İngiliz Sterlini',
                'sembol' => '£',
                'kur' => 38.7,
                'status' => true,
                'varsayilan' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($currencies as $currency) {
            DB::table('para_birimleri')->updateOrInsert(
                ['kod' => $currency['kod']],
                $currency
            );
        }

        $this->command->info('✅ '.count($currencies).' para birimi oluşturuldu');
    }

    /**
     * Site ayarları oluştur
     */
    private function createSiteSettings(): void
    {
        $this->command->info('⚙️ Site ayarları oluşturuluyor...');

        $siteSettings = [
            [
                'key' => 'site_name',
                'value' => 'Yalıhan Emlak',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Site adı',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_description',
                'value' => 'Bodrum ve çevresinde emlak danışmanlığı',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Site açıklaması',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_phone',
                'value' => '+90 533 209 03 02',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'İletişim telefonu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@yalihanemlak.com',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'İletişim e-postası',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_address',
                'value' => 'Kadıköy, İstanbul',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'İletişim adresi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_currency',
                'value' => 'TRY',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Varsayılan para birimi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'items_per_page',
                'value' => '12',
                'type' => 'number',
                'group' => 'general',
                'description' => 'Sayfa başına ilan sayısı',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'general',
                'description' => 'Bakım modu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($siteSettings as $setting) {
            // ✅ STANDARDIZED: Using Setting model instead of DB::table('site_settings')
            Setting::set(
                $setting['key'],
                $setting['value'],
                $setting['group'] ?? 'general',
                $setting['type'],
                $setting['description'] ?? null
            );
        }

        $this->command->info('✅ '.count($siteSettings).' site ayarı oluşturuldu');
    }

    /**
     * Başlangıç ayarları oluştur
     */
    private function createInitialSettings(): void
    {
        $this->command->info('🚀 Başlangıç ayarları oluşturuluyor...');

        $initialSettings = [
            [
                'key' => 'app_version',
                'value' => '4.0.0',
                'type' => 'text',
                'description' => 'Uygulama versiyonu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'context7_version',
                'value' => '4.0.0',
                'type' => 'text',
                'description' => 'Context7 versiyonu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'database_version',
                'value' => '2025-09-13',
                'type' => 'text',
                'description' => 'Veritabanı versiyonu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'last_migration',
                'value' => '2025_09_13_000000_context7_master_seeder',
                'type' => 'text',
                'description' => 'Son migration',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'seeder_status',
                'value' => 'completed',
                'type' => 'text',
                'description' => 'Seeder statusu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($initialSettings as $setting) {
            DB::table('initial_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('✅ '.count($initialSettings).' başlangıç ayarı oluşturuldu');
    }
}
