<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteOzellik;
use Illuminate\Support\Str;

class SiteOzellikleriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ozellikleri = [
            // Security Features
            ['name' => 'Güvenlik', 'type' => 'security', 'description' => '7/24 Güvenlik hizmeti', 'display_order' => 1],
            ['name' => 'Kamera Sistemi', 'type' => 'security', 'description' => 'CCTV güvenlik kameraları', 'display_order' => 2],

            // Parking
            ['name' => 'Otopark', 'type' => 'amenity', 'description' => 'Kapalı otopark', 'display_order' => 3],
            ['name' => 'Açık Otopark', 'type' => 'amenity', 'description' => 'Açık otopark alanı', 'display_order' => 4],

            // Sports & Recreation
            ['name' => 'Havuz', 'type' => 'facility', 'description' => 'Yüzme havuzu', 'display_order' => 5],
            ['name' => 'Spor Salonu', 'type' => 'facility', 'description' => 'Fitness merkezi', 'display_order' => 6],
            ['name' => 'Sauna', 'type' => 'facility', 'description' => 'Sauna ve spa', 'display_order' => 7],
            ['name' => 'Oyun Alanı', 'type' => 'facility', 'description' => 'Çocuk oyun alanı', 'display_order' => 8],

            // Building Features
            ['name' => 'Asansör', 'type' => 'amenity', 'description' => 'Asansör sistemi', 'display_order' => 9],
            ['name' => 'Jeneratör', 'type' => 'amenity', 'description' => 'Yedek jeneratör', 'display_order' => 10],

            // Social Areas
            ['name' => 'Sosyal Tesis', 'type' => 'facility', 'description' => 'Sosyal tesis alanı', 'display_order' => 11],
            ['name' => 'Peyzaj', 'type' => 'amenity', 'description' => 'Peyzaj bahçe', 'display_order' => 12],
            ['name' => 'Çamaşırhane', 'type' => 'amenity', 'description' => 'Ortak çamaşırhane', 'display_order' => 13],
        ];

        foreach ($ozellikleri as $ozellik) {
            SiteOzellik::updateOrCreate(
                ['slug' => Str::slug($ozellik['name'])],
                array_merge($ozellik, [
                    'slug' => Str::slug($ozellik['name']),
                    'status' => true
                ])
            );
        }
    }
}
