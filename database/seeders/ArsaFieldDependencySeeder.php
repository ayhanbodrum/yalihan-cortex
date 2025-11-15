<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriYayinTipiFieldDependency;
use Illuminate\Support\Facades\DB;

class ArsaFieldDependencySeeder extends Seeder
{
    /**
     * Arsa kategorisi için alan ilişkileri
     *
     * Run with: php artisan db:seed --class=ArsaFieldDependencySeeder
     */
    public function run(): void
    {
        $fields = [
            // SATILIK - Arsa Özellikleri
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'ada_no',
                'field_name' => 'Ada No',
                'field_type' => 'text',
                'field_category' => 'arsa',
                'field_icon' => '🗺️',
                'enabled' => true,
                'required' => false,
                'display_order' => 1,
                'searchable' => true,
                'show_in_card' => true,
            ],
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'parsel_no',
                'field_name' => 'Parsel No',
                'field_type' => 'text',
                'field_category' => 'arsa',
                'field_icon' => '📍',
                'enabled' => true,
                'required' => false,
                'display_order' => 2,
                'searchable' => true,
                'show_in_card' => true,
            ],
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'imar_statusu',
                'field_name' => 'İmar Durumu',
                'field_type' => 'select',
                'field_category' => 'arsa',
                'field_options' => json_encode(['İmarlı', 'İmarsız', 'Tarla', 'Bahçe']),
                'field_icon' => '🏗️',
                'enabled' => true,
                'required' => false,
                'display_order' => 3,
                'searchable' => true,
                'show_in_card' => true,
            ],
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'kaks',
                'field_name' => 'KAKS',
                'field_type' => 'number',
                'field_category' => 'arsa',
                'field_icon' => '📊',
                'enabled' => true,
                'required' => false,
                'display_order' => 4,
                'searchable' => false,
                'show_in_card' => false,
            ],
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'taks',
                'field_name' => 'TAKS',
                'field_type' => 'number',
                'field_category' => 'arsa',
                'field_icon' => '📈',
                'enabled' => true,
                'required' => false,
                'display_order' => 5,
                'searchable' => false,
                'show_in_card' => false,
            ],
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'gabari',
                'field_name' => 'Gabari',
                'field_type' => 'number',
                'field_category' => 'arsa',
                'field_unit' => 'm',
                'field_icon' => '📏',
                'enabled' => true,
                'required' => false,
                'display_order' => 6,
                'searchable' => false,
                'show_in_card' => false,
            ],
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'alan_m2',
                'field_name' => 'Alan',
                'field_type' => 'number',
                'field_category' => 'arsa',
                'field_unit' => 'm²',
                'field_icon' => '📐',
                'enabled' => true,
                'required' => true,
                'display_order' => 7,
                'searchable' => true,
                'show_in_card' => true,
            ],
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'satis_fiyati',
                'field_name' => 'Satış Fiyatı',
                'field_type' => 'price',
                'field_category' => 'fiyat',
                'field_icon' => '💰',
                'enabled' => true,
                'required' => true,
                'display_order' => 8,
                'searchable' => true,
                'show_in_card' => true,
                'ai_suggestion' => true,
            ],

            // KİRALIK - Arsa Özellikleri (benzer alanlar)
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Kiralık',
                'field_slug' => 'ada_no',
                'field_name' => 'Ada No',
                'field_type' => 'text',
                'field_category' => 'arsa',
                'field_icon' => '🗺️',
                'enabled' => true,
                'required' => false,
                'display_order' => 1,
                'searchable' => true,
                'show_in_card' => true,
            ],
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Kiralık',
                'field_slug' => 'parsel_no',
                'field_name' => 'Parsel No',
                'field_type' => 'text',
                'field_category' => 'arsa',
                'field_icon' => '📍',
                'enabled' => true,
                'required' => false,
                'display_order' => 2,
                'searchable' => true,
                'show_in_card' => true,
            ],
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Kiralık',
                'field_slug' => 'alan_m2',
                'field_name' => 'Alan',
                'field_type' => 'number',
                'field_category' => 'arsa',
                'field_unit' => 'm²',
                'field_icon' => '📐',
                'enabled' => true,
                'required' => true,
                'display_order' => 3,
                'searchable' => true,
                'show_in_card' => true,
            ],
            [
                'kategori_slug' => 'arsa',
                'yayin_tipi' => 'Kiralık',
                'field_slug' => 'kira_bedeli',
                'field_name' => 'Kira Bedeli',
                'field_type' => 'price',
                'field_category' => 'fiyat',
                'field_icon' => '💵',
                'enabled' => true,
                'required' => true,
                'display_order' => 4,
                'searchable' => true,
                'show_in_card' => true,
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($fields as $field) {
                KategoriYayinTipiFieldDependency::create($field);
            }

            DB::commit();
            $this->command->info('✅ Arsa alan ilişkileri başarıyla eklendi! (' . count($fields) . ' alan)');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Hata: ' . $e->getMessage());
        }
    }
}
