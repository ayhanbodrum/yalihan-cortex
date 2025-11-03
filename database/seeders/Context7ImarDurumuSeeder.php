<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Context7ImarDurumuSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            'Konut İmarlı Arsa',
            'Ticari İmarlı Arsa',
            'Tarla / Bağ / Bahçe',
            'Sanayi İmarlı Arsa',
            'Turizm İmarlı Arsa',
            'İmarlı Ticari + Konut',
        ];

        $categoryId = DB::table('feature_categories')->where('name', 'Hukuki')->value('id');
        if (! $categoryId) {
            $payload = ['name' => 'Hukuki', 'created_at' => now(), 'updated_at' => now()];
            if (Schema::hasColumn('feature_categories', 'slug')) {
                $payload['slug'] = Str::slug('Hukuki');
            }
            if (Schema::hasColumn('feature_categories', 'active')) {
                $payload['active'] = true;
            }
            $categoryId = DB::table('feature_categories')->insertGetId($payload);
        }

        $exists = DB::table('features')->where('name', 'İmar Durumu')->exists();
        if (! $exists) {
            $insert = [
                'name' => 'İmar Durumu',
                'type' => 'select',
                'options' => json_encode($options, JSON_UNESCAPED_UNICODE),
                'category_id' => $categoryId,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('features', 'order')) {
                $insert['order'] = 9;
            }
            if (Schema::hasColumn('features', 'slug')) {
                $insert['slug'] = Str::slug('İmar Durumu');
            }
            DB::table('features')->insert($insert);
        } else {
            DB::table('features')->where('name', 'İmar Durumu')->update([
                'type' => 'select',
                'options' => json_encode($options, JSON_UNESCAPED_UNICODE),
                'status' => true,
                'updated_at' => now(),
            ]);
        }
    }
}
