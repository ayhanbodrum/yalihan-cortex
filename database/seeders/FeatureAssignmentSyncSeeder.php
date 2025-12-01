<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\IlanKategoriYayinTipi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FeatureAssignmentSyncSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('ilan_kategori_yayin_tipleri') || ! Schema::hasTable('features') || ! Schema::hasTable('feature_categories')) {
            return;
        }

        $map = [
            'arsa' => ['Arsa Özellikleri', 'Genel Özellikler'],
            'konut' => ['Konut Özellikleri', 'Genel Özellikler'],
            'yazlik' => ['Yazlık Özellikleri', 'Genel Özellikler'],
            'isyeri' => ['Ticari Özellikler', 'Genel Özellikler'],
            'turistik-tesis' => ['Turistik Tesis Özellikleri', 'Genel Özellikler'],
        ];

        IlanKategoriYayinTipi::with('kategori')
            ->where('status', true)
            ->get()
            ->each(function (IlanKategoriYayinTipi $pt) use ($map) {
                $slug = $pt->kategori?->slug ?? null;
                $allowed = $map[$slug] ?? ['Genel Özellikler'];

                $featureIds = Feature::enabled()
                    ->with('category')
                    ->whereHas('category', function ($q) use ($allowed, $slug) {
                        $q->whereIn('name', $allowed)
                            ->orWhere('applies_to', $slug);
                    })
                    ->pluck('id')
                    ->toArray();

                // Fallback: Yazlık Kiralık özel set (slug bazlı)
                if ($pt->yayin_tipi === 'Yazlık Kiralık') {
                    $fallbackSlugs = ['havuz', 'jakuzi', 'barbeku', 'klima', 'genis-teras', 'deniz-manzarasi', 'plaja-yakin'];
                    $fallbackIds = Feature::enabled()->whereIn('slug', $fallbackSlugs)->pluck('id')->toArray();
                    $featureIds = array_values(array_unique(array_merge($featureIds, $fallbackIds)));
                }

                if (! empty($featureIds)) {
                    $pt->assignFeatures($featureIds, [
                        'is_visible' => true,
                    ]);
                }
            });
    }
}
