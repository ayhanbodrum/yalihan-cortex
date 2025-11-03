<?php

namespace Database\Seeders;

use App\Models\CommissionRate;
use Illuminate\Database\Seeder;

class CommissionRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commissionRates = [
            // Satış Komisyonları
            [
                'commission_type' => 'sales',
                'commission_name' => 'Satış Komisyonu - Standart',
                'rate' => 3.00,
                'min_amount' => 0,
                'max_amount' => 1000000,
                'description' => 'Standart emlak satış komisyonu',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
            [
                'commission_type' => 'sales',
                'commission_name' => 'Satış Komisyonu - Premium',
                'rate' => 4.00,
                'min_amount' => 1000000,
                'max_amount' => 5000000,
                'description' => 'Premium emlak satış komisyonu',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
            [
                'commission_type' => 'sales',
                'commission_name' => 'Satış Komisyonu - Lüks',
                'rate' => 5.00,
                'min_amount' => 5000000,
                'max_amount' => null,
                'description' => 'Lüks emlak satış komisyonu',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],

            // Kiralama Komisyonları
            [
                'commission_type' => 'rental',
                'commission_name' => 'Kiralama Komisyonu - Aylık',
                'rate' => 50.00,
                'min_amount' => 0,
                'max_amount' => null,
                'description' => 'Aylık kira komisyonu (sabit tutar)',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
            [
                'commission_type' => 'rental',
                'commission_name' => 'Kiralama Komisyonu - Yıllık',
                'rate' => 1.00,
                'min_amount' => 0,
                'max_amount' => null,
                'description' => 'Yıllık kira komisyonu (yüzde)',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],

            // Danışmanlık Komisyonları
            [
                'commission_type' => 'consultation',
                'commission_name' => 'Danışmanlık Komisyonu - Saatlik',
                'rate' => 200.00,
                'min_amount' => 0,
                'max_amount' => null,
                'description' => 'Saatlik danışmanlık komisyonu',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
            [
                'commission_type' => 'consultation',
                'commission_name' => 'Danışmanlık Komisyonu - Proje',
                'rate' => 2.00,
                'min_amount' => 0,
                'max_amount' => null,
                'description' => 'Proje bazlı danışmanlık komisyonu',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],

            // Yönetim Komisyonları
            [
                'commission_type' => 'management',
                'commission_name' => 'Yönetim Komisyonu - Aylık',
                'rate' => 5.00,
                'min_amount' => 0,
                'max_amount' => null,
                'description' => 'Aylık emlak yönetim komisyonu',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],

            // Acentelik Komisyonları
            [
                'commission_type' => 'brokerage',
                'commission_name' => 'Acentelik Komisyonu - Satış',
                'rate' => 1.50,
                'min_amount' => 0,
                'max_amount' => null,
                'description' => 'Acentelik satış komisyonu',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
            [
                'commission_type' => 'brokerage',
                'commission_name' => 'Acentelik Komisyonu - Kiralama',
                'rate' => 0.50,
                'min_amount' => 0,
                'max_amount' => null,
                'description' => 'Acentelik kiralama komisyonu',
                'status' => true,
                'effective_date' => '2024-01-01',
            ],
        ];

        foreach ($commissionRates as $commissionRate) {
            CommissionRate::create($commissionRate);
        }
    }
}
