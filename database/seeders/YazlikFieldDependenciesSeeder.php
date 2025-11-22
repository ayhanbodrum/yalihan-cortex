<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\IlanKategoriYayinTipi;
use App\Models\KategoriYayinTipiFieldDependency;

class YazlikFieldDependenciesSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('ilan_kategori_yayin_tipleri') || !Schema::hasTable('kategori_yayin_tipi_field_dependencies')) {
            return;
        }

        $base = [
            ['field_slug' => 'havuz', 'field_name' => 'Havuz', 'field_type' => 'boolean', 'field_category' => 'Yazlık Özellikleri', 'display_order' => 16],
            ['field_slug' => 'jakuzi', 'field_name' => 'Jakuzi', 'field_type' => 'boolean', 'field_category' => 'Yazlık Özellikleri', 'display_order' => 17],
            ['field_slug' => 'klima', 'field_name' => 'Klima', 'field_type' => 'boolean', 'field_category' => 'Genel Özellikler', 'display_order' => 18],
            ['field_slug' => 'barbeku', 'field_name' => 'Barbekü', 'field_type' => 'boolean', 'field_category' => 'Yazlık Özellikleri', 'display_order' => 19],
            ['field_slug' => 'genis-teras', 'field_name' => 'Geniş Teras', 'field_type' => 'boolean', 'field_category' => 'Genel Özellikler', 'display_order' => 20],
            ['field_slug' => 'deniz-manzarasi', 'field_name' => 'Deniz Manzarası', 'field_type' => 'boolean', 'field_category' => 'Genel Özellikler', 'display_order' => 21],
            ['field_slug' => 'plaja-yakin', 'field_name' => 'Plaja Yakın', 'field_type' => 'boolean', 'field_category' => 'Genel Özellikler', 'display_order' => 22],
        ];

        IlanKategoriYayinTipi::with('kategori')
            ->where('yayin_tipi', 'Yazlık Kiralık')
            ->get()
            ->each(function (IlanKategoriYayinTipi $pt) use ($base) {
                $kategoriSlug = $pt->kategori?->slug;
                if (!$kategoriSlug) return;

                foreach ($base as $f) {
                    KategoriYayinTipiFieldDependency::updateOrCreate(
                        [
                            'kategori_slug' => $kategoriSlug,
                            'yayin_tipi' => 'Yazlık Kiralık',
                            'field_slug' => $f['field_slug'],
                        ],
                        [
                            'field_name' => $f['field_name'],
                            'field_type' => $f['field_type'],
                            'field_category' => $f['field_category'],
                            'status' => true,
                            'required' => false,
                            'display_order' => $f['display_order'],
                            'ai_auto_fill' => false,
                            'ai_suggestion' => false,
                            'searchable' => false,
                            'show_in_card' => true,
                        ]
                    );
                }
            });
    }
}