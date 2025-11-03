<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxRates = [
            // KDV Oranları
            [
                'tax_type' => 'vat',
                'tax_name' => 'KDV - Standart Oran',
                'rate' => 18.00,
                'description' => 'Genel mal ve hizmetler için standart KDV oranı',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
            [
                'tax_type' => 'vat',
                'tax_name' => 'KDV - İndirimli Oran',
                'rate' => 8.00,
                'description' => 'Temel gıda maddeleri ve eğitim hizmetleri için indirimli KDV oranı',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
            [
                'tax_type' => 'vat',
                'tax_name' => 'KDV - Özel Oran',
                'rate' => 1.00,
                'description' => 'Temel gıda maddeleri için özel KDV oranı',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
            [
                'tax_type' => 'vat',
                'tax_name' => 'KDV - Sıfır Oran',
                'rate' => 0.00,
                'description' => 'İhracat ve bazı özel statuslar için sıfır KDV oranı',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],

            // Emlak Vergisi
            [
                'tax_type' => 'property_tax',
                'tax_name' => 'Emlak Vergisi - Konut',
                'rate' => 0.20,
                'description' => 'Konut amaçlı emlaklar için emlak vergisi oranı',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
            [
                'tax_type' => 'property_tax',
                'tax_name' => 'Emlak Vergisi - Ticari',
                'rate' => 0.40,
                'description' => 'Ticari amaçlı emlaklar için emlak vergisi oranı',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],

            // Tapu Harcı
            [
                'tax_type' => 'deed_fee',
                'tax_name' => 'Tapu Harcı - Satış',
                'rate' => 4.00,
                'description' => 'Emlak satış işlemleri için tapu harcı oranı',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
            [
                'tax_type' => 'deed_fee',
                'tax_name' => 'Tapu Harcı - Kiralama',
                'rate' => 2.00,
                'description' => 'Emlak kiralama işlemleri için tapu harcı oranı',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],

            // Damga Vergisi
            [
                'tax_type' => 'stamp_duty',
                'tax_name' => 'Damga Vergisi - Sözleşme',
                'rate' => 0.95,
                'description' => 'Emlak sözleşmeleri için damga vergisi oranı',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],

            // Gelir Vergisi
            [
                'tax_type' => 'income_tax',
                'tax_name' => 'Gelir Vergisi - Emlak Satışı',
                'rate' => 15.00,
                'description' => 'Emlak satışından elde edilen gelir için gelir vergisi oranı',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],

            // Kurumlar Vergisi
            [
                'tax_type' => 'corporate_tax',
                'tax_name' => 'Kurumlar Vergisi - Emlak',
                'rate' => 20.00,
                'description' => 'Kurumsal emlak işlemleri için kurumlar vergisi oranı',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
        ];

        foreach ($taxRates as $taxRate) {
            TaxRate::create($taxRate);
        }
    }
}
