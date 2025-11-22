<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\FeatureCategory;
use App\Models\Feature;

class YazlikFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('feature_categories') || !Schema::hasTable('features')) {
            return;
        }

        $genel = FeatureCategory::updateOrCreate(
            ['slug' => 'genel-ozellikler'],
            ['name' => 'Genel Özellikler', 'status' => true, 'display_order' => 1]
        );

        $yazlik = FeatureCategory::updateOrCreate(
            ['slug' => 'yazlik-ozellikleri'],
            ['name' => 'Yazlık Özellikleri', 'status' => true, 'display_order' => 2]
        );

        $features = [
            // Yazlık Olanaklar
            ['slug' => 'havuz', 'name' => 'Havuz', 'type' => 'boolean', 'category' => $yazlik, 'display_order' => 16, 'status' => true],
            ['slug' => 'jakuzi', 'name' => 'Jakuzi', 'type' => 'boolean', 'category' => $yazlik, 'display_order' => 17, 'status' => true],
            ['slug' => 'barbeku', 'name' => 'Barbekü', 'type' => 'boolean', 'category' => $yazlik, 'display_order' => 19, 'status' => true],
            // Yazlık Genel
            ['slug' => 'klima', 'name' => 'Klima', 'type' => 'boolean', 'category' => $genel, 'display_order' => 18, 'status' => true],
            ['slug' => 'genis-teras', 'name' => 'Geniş Teras', 'type' => 'boolean', 'category' => $genel, 'display_order' => 20, 'status' => true],
            ['slug' => 'deniz-manzarasi', 'name' => 'Deniz Manzarası', 'type' => 'boolean', 'category' => $genel, 'display_order' => 21, 'status' => true],
            ['slug' => 'plaja-yakin', 'name' => 'Plaja Yakın', 'type' => 'boolean', 'category' => $genel, 'display_order' => 22, 'status' => true],
        ];

        foreach ($features as $f) {
            Feature::updateOrCreate(
                ['slug' => $f['slug']],
                [
                    'feature_category_id' => $f['category']->id,
                    'name' => $f['name'],
                    'type' => $f['type'],
                    'display_order' => $f['display_order'],
                    'status' => $f['status'],
                ]
            );
        }
    }
}