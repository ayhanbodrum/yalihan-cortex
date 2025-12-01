<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class PricingSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'pricing.weekend_multiplier', 'value' => '1.15', 'type' => 'string', 'group' => 'currency', 'description' => 'Hafta sonu çarpanı'],
            ['key' => 'pricing.occupancy.high_threshold', 'value' => '0.8', 'type' => 'string', 'group' => 'currency', 'description' => 'Yüksek doluluk eşiği'],
            ['key' => 'pricing.occupancy.med_threshold', 'value' => '0.6', 'type' => 'string', 'group' => 'currency', 'description' => 'Orta doluluk eşiği'],
            ['key' => 'pricing.occupancy.high_multiplier', 'value' => '1.15', 'type' => 'string', 'group' => 'currency', 'description' => 'Yüksek doluluk çarpanı'],
            ['key' => 'pricing.occupancy.med_multiplier', 'value' => '1.08', 'type' => 'string', 'group' => 'currency', 'description' => 'Orta doluluk çarpanı'],
            ['key' => 'pricing.occupancy.low_multiplier', 'value' => '0.95', 'type' => 'string', 'group' => 'currency', 'description' => 'Düşük doluluk çarpanı'],
            ['key' => 'pricing.timing.last_min_days', 'value' => '7', 'type' => 'integer', 'group' => 'currency', 'description' => 'Son dakika gün eşiği'],
            ['key' => 'pricing.timing.last_min_multiplier', 'value' => '0.90', 'type' => 'string', 'group' => 'currency', 'description' => 'Son dakika çarpanı'],
            ['key' => 'pricing.timing.early_days', 'value' => '60', 'type' => 'integer', 'group' => 'currency', 'description' => 'Erken rezervasyon gün eşiği'],
            ['key' => 'pricing.timing.early_multiplier', 'value' => '0.95', 'type' => 'string', 'group' => 'currency', 'description' => 'Erken rezervasyon çarpanı'],
            ['key' => 'pricing.guest.extra_rate', 'value' => '0.02', 'type' => 'string', 'group' => 'currency', 'description' => 'Ekstra misafir oranı'],
            ['key' => 'pricing.cleaning_fee', 'value' => '500', 'type' => 'integer', 'group' => 'currency', 'description' => 'Temizlik ücreti'],
            ['key' => 'pricing.service_fee_rate', 'value' => '0.05', 'type' => 'string', 'group' => 'currency', 'description' => 'Hizmet bedeli oranı'],
            ['key' => 'pricing.floor_daily', 'value' => '0', 'type' => 'integer', 'group' => 'currency', 'description' => 'Gecelik taban fiyat'],
            ['key' => 'pricing.ceiling_daily', 'value' => '0', 'type' => 'integer', 'group' => 'currency', 'description' => 'Gecelik tavan fiyat'],
            ['key' => 'pricing.holiday_multiplier', 'value' => '1.25', 'type' => 'string', 'group' => 'currency', 'description' => 'Resmî tatil/zirve çarpanı'],
            ['key' => 'pricing.holidays_json', 'value' => '[]', 'type' => 'json', 'group' => 'currency', 'description' => 'Resmî tatil/zirve günleri JSON listesi (YYYY-MM-DD[])'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }

        Setting::clearCache();
    }
}
