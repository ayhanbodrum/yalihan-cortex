<?php

namespace Database\Seeders;

use App\Models\KategoriYayinTipiFieldDependency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KonutFieldDependencySeeder extends Seeder
{
    /**
     * Konut kategorisi için alan ilişkileri
     *
     * Run with: php artisan db:seed --class=KonutFieldDependencySeeder
     */
    public function run(): void
    {
        // Önce mevcut konut alan ilişkilerini temizle (opsiyonel)
        // KategoriYayinTipiFieldDependency::where('kategori_slug', 'konut')->delete();

        $fields = [
            // SATILIK için alanlar
            [
                'kategori_slug' => 'konut',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'satis_fiyati',
                'field_name' => 'Satış Fiyatı',
                'field_type' => 'price',
                'field_category' => 'fiyat',
                'field_icon' => '💰',
                'enabled' => true,
                'required' => true,
                'display_order' => 1,
                'searchable' => true,
                'show_in_card' => true,
            ],
            [
                'kategori_slug' => 'konut',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'oda_sayisi',
                'field_name' => 'Oda Sayısı',
                'field_type' => 'select',
                'field_category' => 'ozellik',
                'field_options' => json_encode(['1+0', '1+1', '2+1', '3+1', '4+1', '5+1']),
                'field_icon' => '🛏️',
                'enabled' => true,
                'required' => false,
                'display_order' => 2,
                'searchable' => true,
                'show_in_card' => true,
            ],
            [
                'kategori_slug' => 'konut',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'banyo_sayisi',
                'field_name' => 'Banyo Sayısı',
                'field_type' => 'number',
                'field_category' => 'ozellik',
                'field_icon' => '🚿',
                'enabled' => true,
                'required' => false,
                'display_order' => 3,
                'searchable' => false,
                'show_in_card' => false,
            ],
            [
                'kategori_slug' => 'konut',
                'yayin_tipi' => 'Satılık',
                'field_slug' => 'metrekare',
                'field_name' => 'Metrekare',
                'field_type' => 'number',
                'field_category' => 'ozellik',
                'field_unit' => 'm²',
                'field_icon' => '📐',
                'enabled' => true,
                'required' => true,
                'display_order' => 4,
                'searchable' => true,
                'show_in_card' => true,
            ],

            // KİRALIK için alanlar
            [
                'kategori_slug' => 'konut',
                'yayin_tipi' => 'Kiralık',
                'field_slug' => 'kira_bedeli',
                'field_name' => 'Kira Bedeli',
                'field_type' => 'price',
                'field_category' => 'fiyat',
                'field_icon' => '🏠',
                'enabled' => true,
                'required' => true,
                'display_order' => 1,
                'searchable' => true,
                'show_in_card' => true,
            ],
            [
                'kategori_slug' => 'konut',
                'yayin_tipi' => 'Kiralık',
                'field_slug' => 'depozito',
                'field_name' => 'Depozito',
                'field_type' => 'price',
                'field_category' => 'fiyat',
                'field_icon' => '💰',
                'enabled' => true,
                'required' => false,
                'display_order' => 2,
                'searchable' => false,
                'show_in_card' => false,
            ],
            [
                'kategori_slug' => 'konut',
                'yayin_tipi' => 'Kiralık',
                'field_slug' => 'oda_sayisi',
                'field_name' => 'Oda Sayısı',
                'field_type' => 'select',
                'field_category' => 'ozellik',
                'field_options' => json_encode(['1+0', '1+1', '2+1', '3+1', '4+1', '5+1']),
                'field_icon' => '🛏️',
                'enabled' => true,
                'required' => false,
                'display_order' => 3,
                'searchable' => true,
                'show_in_card' => true,
            ],
            [
                'kategori_slug' => 'konut',
                'yayin_tipi' => 'Kiralık',
                'field_slug' => 'metrekare',
                'field_name' => 'Metrekare',
                'field_type' => 'number',
                'field_category' => 'ozellik',
                'field_unit' => 'm²',
                'field_icon' => '📐',
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
            $this->command->info('✅ Konut alan ilişkileri başarıyla eklendi!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Hata: '.$e->getMessage());
        }
    }
}
