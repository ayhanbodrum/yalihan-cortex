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
            ['name' => 'Güvenlik', 'type' => 'security', 'description' => '7/24 Güvenlik hizmeti', 'order' => 1],
            ['name' => 'Kamera Sistemi', 'type' => 'security', 'description' => 'CCTV güvenlik kameraları', 'order' => 2],
            
            // Parking
            ['name' => 'Otopark', 'type' => 'amenity', 'description' => 'Kapalı otopark', 'order' => 3],
            ['name' => 'Açık Otopark', 'type' => 'amenity', 'description' => 'Açık otopark alanı', 'order' => 4],
            
            // Sports & Recreation
            ['name' => 'Havuz', 'type' => 'facility', 'description' => 'Yüzme havuzu', 'order' => 5],
            ['name' => 'Spor Salonu', 'type' => 'facility', 'description' => 'Fitness merkezi', 'order' => 6],
            ['name' => 'Sauna', 'type' => 'facility', 'description' => 'Sauna ve spa', 'order' => 7],
            ['name' => 'Oyun Alanı', 'type' => 'facility', 'description' => 'Çocuk oyun alanı', 'order' => 8],
            
            // Building Features
            ['name' => 'Asansör', 'type' => 'amenity', 'description' => 'Asansör sistemi', 'order' => 9],
            ['name' => 'Jeneratör', 'type' => 'amenity', 'description' => 'Yedek jeneratör', 'order' => 10],
            
            // Social Areas
            ['name' => 'Sosyal Tesis', 'type' => 'facility', 'description' => 'Sosyal tesis alanı', 'order' => 11],
            ['name' => 'Peyzaj', 'type' => 'amenity', 'description' => 'Peyzaj bahçe', 'order' => 12],
            ['name' => 'Çamaşırhane', 'type' => 'amenity', 'description' => 'Ortak çamaşırhane', 'order' => 13],
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
