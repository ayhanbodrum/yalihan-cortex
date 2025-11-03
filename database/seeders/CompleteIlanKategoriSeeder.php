<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IlanKategori;
use Illuminate\Support\Str;

class CompleteIlanKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Context7: Tam kategori hiyerarşisi
     */
    public function run(): void
    {
        // ✅ Ana Kategoriler
        $anaKategoriler = [
            ['name' => 'Konut', 'slug' => 'konut', 'status' => true],
            ['name' => 'Arsa', 'slug' => 'arsa', 'status' => true],
            ['name' => 'İşyeri', 'slug' => 'isyeri', 'status' => true],
            ['name' => 'Turistik Tesis', 'slug' => 'turistik-tesis', 'status' => true],
            ['name' => 'Projeler', 'slug' => 'projeler', 'status' => true],
        ];

        foreach ($anaKategoriler as $kategoriData) {
            IlanKategori::updateOrCreate(
                ['slug' => $kategoriData['slug']],
                [
                    'name' => $kategoriData['name'],
                    'parent_id' => null,
                    'status' => $kategoriData['status'],
                    'seviye' => 1, // Ana kategori
                ]
            );
        }

        // ✅ Alt Kategoriler
        $altKategoriler = [
            // Konut altındakiler
            ['name' => 'Daire', 'slug' => 'daire', 'parent_slug' => 'konut'],
            ['name' => 'Villa', 'slug' => 'villa', 'parent_slug' => 'konut'],
            ['name' => 'Residence', 'slug' => 'residence', 'parent_slug' => 'konut'],
            ['name' => 'Müstakil Ev', 'slug' => 'mustakil-ev', 'parent_slug' => 'konut'],
            ['name' => 'Çiftlik Evi', 'slug' => 'ciftlik-evi', 'parent_slug' => 'konut'],
            ['name' => 'Köşk', 'slug' => 'kosk', 'parent_slug' => 'konut'],
            ['name' => 'Yazlık', 'slug' => 'yazlik', 'parent_slug' => 'konut'],
            ['name' => 'Apart', 'slug' => 'apart', 'parent_slug' => 'konut'],

            // Arsa altındakiler
            ['name' => 'İmarlı Arsa', 'slug' => 'imarli-arsa', 'parent_slug' => 'arsa'],
            ['name' => 'Tarla', 'slug' => 'tarla', 'parent_slug' => 'arsa'],
            ['name' => 'Bağ', 'slug' => 'bag', 'parent_slug' => 'arsa'],
            ['name' => 'Bahçe', 'slug' => 'bahce', 'parent_slug' => 'arsa'],
            ['name' => 'Zeytinlik', 'slug' => 'zeytinlik', 'parent_slug' => 'arsa'],
            ['name' => 'Turistik Arsa', 'slug' => 'turistik-arsa', 'parent_slug' => 'arsa'],

            // İşyeri altındakiler
            ['name' => 'Dükkan', 'slug' => 'dukkan', 'parent_slug' => 'isyeri'],
            ['name' => 'Mağaza', 'slug' => 'magaza', 'parent_slug' => 'isyeri'],
            ['name' => 'Plaza / AVM', 'slug' => 'plaza-avm', 'parent_slug' => 'isyeri'],
            ['name' => 'Ofis', 'slug' => 'ofis', 'parent_slug' => 'isyeri'],
            ['name' => 'Depo', 'slug' => 'depo', 'parent_slug' => 'isyeri'],
            ['name' => 'Fabrika', 'slug' => 'fabrika', 'parent_slug' => 'isyeri'],
            ['name' => 'İmalathane', 'slug' => 'imalathane', 'parent_slug' => 'isyeri'],
            ['name' => 'Atölye', 'slug' => 'atolye', 'parent_slug' => 'isyeri'],
            ['name' => 'Restaurant / Cafe', 'slug' => 'restaurant-cafe', 'parent_slug' => 'isyeri'],

            // Turistik Tesis altındakiler
            ['name' => 'Otel', 'slug' => 'otel', 'parent_slug' => 'turistik-tesis'],
            ['name' => 'Pansiyon', 'slug' => 'pansiyon', 'parent_slug' => 'turistik-tesis'],
            ['name' => 'Apart Otel', 'slug' => 'apart-otel', 'parent_slug' => 'turistik-tesis'],
            ['name' => 'Butik Otel', 'slug' => 'butik-otel', 'parent_slug' => 'turistik-tesis'],
            ['name' => 'Tatil Köyü', 'slug' => 'tatil-koyu', 'parent_slug' => 'turistik-tesis'],
            ['name' => 'Motel', 'slug' => 'motel', 'parent_slug' => 'turistik-tesis'],

            // Projeler altındakiler
            ['name' => 'Konut Projesi', 'slug' => 'konut-projesi', 'parent_slug' => 'projeler'],
            ['name' => 'Villa Projesi', 'slug' => 'villa-projesi', 'parent_slug' => 'projeler'],
            ['name' => 'Residence Projesi', 'slug' => 'residence-projesi', 'parent_slug' => 'projeler'],
            ['name' => 'Ticari Proje', 'slug' => 'ticari-proje', 'parent_slug' => 'projeler'],
        ];

        foreach ($altKategoriler as $kategoriData) {
            $parent = IlanKategori::where('slug', $kategoriData['parent_slug'])->first();

            if ($parent) {
                IlanKategori::updateOrCreate(
                    ['slug' => $kategoriData['slug']],
                    [
                        'name' => $kategoriData['name'],
                        'parent_id' => $parent->id,
                        'status' => true,
                        'seviye' => 2, // Alt kategori
                    ]
                );
            }
        }

        $this->command->info('✅ Kategori hiyerarşisi tamamlandı!');
        $this->command->info('   Ana Kategoriler: ' . IlanKategori::whereNull('parent_id')->count());
        $this->command->info('   Alt Kategoriler: ' . IlanKategori::whereNotNull('parent_id')->count());
    }
}
