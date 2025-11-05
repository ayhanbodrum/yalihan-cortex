<?php

namespace Database\Seeders;

use App\Models\LanguageSetting;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class InitialSettingsSeeder extends Seeder
{
    public function run()
    {
        // Site Ayarları
        $siteSettings = [
            [
                'group' => 'general',
                'key' => 'site_name',
                'value' => 'Yalıhan Emlak',
                'type' => 'text',
                'status' => true,
                'description' => 'Site adı',
            ],
            [
                'group' => 'contact',
                'key' => 'company_address',
                'value' => 'Yalıkavak, Şeyhül İslam Ömer Lütfi Cd. No:10 D:C, 48400 Bodrum/Muğla',
                'type' => 'text',
                'status' => true,
                'description' => 'Şirket adresi',
            ],
            [
                'group' => 'contact',
                'key' => 'company_phone',
                'value' => '0533 209 03 02',
                'type' => 'text',
                'status' => true,
                'description' => 'Şirket telefonu',
            ],
            [
                'group' => 'social',
                'key' => 'social_media',
                'value' => json_encode([
                    'facebook' => 'https://facebook.com/yalihanemlak',
                    'instagram' => 'https://instagram.com/yalihanemlak',
                    'twitter' => 'https://twitter.com/yalihanemlak',
                    'linkedin' => 'https://linkedin.com/company/yalihanemlak',
                ]),
                'type' => 'json',
                'status' => true,
                'description' => 'Sosyal medya linkleri',
            ],
            [
                'group' => 'seo',
                'key' => 'meta_description',
                'value' => 'Yalıhan Emlak - Bodrum\'da güvenilir emlak danışmanlığı hizmeti',
                'type' => 'text',
                'status' => true,
                'description' => 'Meta açıklama',
            ],
            [
                'group' => 'currency',
                'key' => 'default_currency',
                'value' => 'TRY',
                'type' => 'text',
                'status' => true,
                'description' => 'Varsayılan para birimi',
            ],
        ];

        foreach ($siteSettings as $setting) {
            // ✅ STANDARDIZED: Using Setting model instead of SiteSetting (merged)
            Setting::set(
                $setting['key'],
                $setting['value'],
                $setting['group'],
                $setting['type'],
                $setting['description'] ?? null
            );
        }

        // Dil Ayarları
        $languages = [
            [
                'code' => 'tr',
                'name' => 'Türkçe',
                'native_name' => 'Türkçe',
                'status' => true,
                'is_default' => true,
                'direction' => 'ltr',
                'date_format' => 'd.m.Y',
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'status' => true,
                'is_default' => false,
                'direction' => 'ltr',
                'date_format' => 'd/m/Y',
            ],
            [
                'code' => 'de',
                'name' => 'German',
                'native_name' => 'Deutsch',
                'status' => true,
                'is_default' => false,
                'direction' => 'ltr',
                'date_format' => 'd.m.Y',
            ],
            [
                'code' => 'ru',
                'name' => 'Russian',
                'native_name' => 'Русский',
                'status' => true,
                'is_default' => false,
                'direction' => 'ltr',
                'date_format' => 'd.m.Y',
            ],
        ];

        foreach ($languages as $language) {
            LanguageSetting::create($language);
        }
    }
}
