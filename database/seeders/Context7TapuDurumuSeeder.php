<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Context7TapuDurumuSeeder extends Seeder
{
    public function run(): void
    {
        $categoryId = DB::table('feature_categories')->where('name', 'Hukuki')->value('id');
        if (! $categoryId) {
            $payload = [
                'name' => 'Hukuki',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('feature_categories', 'slug')) {
                $payload['slug'] = Str::slug('Hukuki');
            }
            if (Schema::hasColumn('feature_categories', 'active')) {
                $payload['active'] = true;
            }
            $categoryId = DB::table('feature_categories')->insertGetId($payload);
        }

        $options = [
            'Hisseli Tapu',
            'Müstakil Parsel',
            'Tahsis',
            'Zilliyet',
            'Belirtilmemiş',
            'Yabancıdan',
            'Tapu yok',
            'Kıbrıs Tapulu',
            'Kooperatiften Hisseli Tapu',
            'Müstakil Tapulu',
            'İntifa Hakkı Tesisli',
        ];

        $exists = DB::table('features')->where('name', 'Tapu Durumu')->exists();
        if (! $exists) {
            $insert = [
                'name' => 'Tapu Durumu',
                'type' => 'select',
                'options' => json_encode($options, JSON_UNESCAPED_UNICODE),
                'category_id' => $categoryId,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('features', 'slug')) {
                $insert['slug'] = Str::slug('Tapu Durumu');
            }
            DB::table('features')->insert($insert);
        } else {
            DB::table('features')->where('name', 'Tapu Durumu')->update([
                'type' => 'select',
                'options' => json_encode($options, JSON_UNESCAPED_UNICODE),
                'status' => true,
                'updated_at' => now(),
            ]);
        }
    }
}
