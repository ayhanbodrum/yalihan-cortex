<?php

namespace Database\Seeders;

use App\Modules\Emlak\Models\Feature;
use App\Modules\Emlak\Models\FeatureTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturesTableSeeder extends Seeder
{
    public function run()
    {
        $features = [
            [
                'category_id' => DB::table('feature_categories')->where('slug', 'konut')->first()->id,
                'slug' => 'oda-sayisi',
                'is_filterable' => 1,
                'show_on_card' => 1,
                'display_order' => 1,
                'name' => 'Oda Sayısı',
                'description' => 'Konutun oda sayısı',
            ],
            [
                'category_id' => DB::table('feature_categories')->where('slug', 'konut')->first()->id,
                'slug' => 'bina-yasi',
                'is_filterable' => 1,
                'show_on_card' => 1,
                'display_order' => 2,
                'name' => 'Bina Yaşı',
                'description' => 'Binanın yaşı',
            ],
            [
                'category_id' => DB::table('feature_categories')->where('slug', 'konut')->first()->id,
                'slug' => 'isitma-tipi',
                'is_filterable' => 1,
                'show_on_card' => 1,
                'display_order' => 3,
                'name' => 'Isıtma Tipi',
                'description' => 'Konutun ısıtma sistemi',
            ],
            [
                'category_id' => DB::table('feature_categories')->where('slug', 'is-yeri')->first()->id,
                'slug' => 'kat-sayisi',
                'is_filterable' => 1,
                'show_on_card' => 0,
                'display_order' => 1,
                'name' => 'Kat Sayısı',
                'description' => 'İş yerinin kat sayısı',
            ],
            [
                'category_id' => DB::table('feature_categories')->where('slug', 'arsa')->first()->id,
                'slug' => 'imar-statusu',
                'is_filterable' => 1,
                'show_on_card' => 1,
                'display_order' => 1,
                'name' => 'İmar Durumu',
                'description' => 'Arsanın imar statusu',
            ],
        ];

        foreach ($features as $featureData) {
            $name = $featureData['name'];
            $description = $featureData['description'];
            $slug = $featureData['slug'];
            unset($featureData['name'], $featureData['description']);

            // Özellik zaten var mı kontrol et - SoftDeletes olmadan
            $existingFeature = DB::table('features')->where('slug', $slug)->first();

            if (! $existingFeature) {
                $feature = Feature::create($featureData);

                // Özellik çevirisini ekle
                FeatureTranslation::create([
                    'feature_id' => $feature->id,
                    'locale' => 'tr',
                    'name' => $name,
                ]);

                $this->command->info("Özellik eklendi: {$name}");
            } else {
                $this->command->info("Özellik zaten mevcut: {$name}");
            }
        }
    }
}
