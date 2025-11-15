<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;

class IlanKategoriYayinTipiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Context7: Kategori bazlı yayın tipleri
     */
    public function run(): void
    {
        // ✅ Tüm yayın tipleri
        $yayinTipleri = [
            'Satılık',
            'Kiralık',
            'Günlük Kiralık',
            'Devren Satılık',
            'Devren Kiralık',
        ];

        // ✅ Hangi kategoriler hangi yayın tiplerini kullanabilir
        $kategoriYayinMap = [
            // Konut kategorileri
            'daire' => ['Satılık', 'Kiralık'],
            'villa' => ['Satılık', 'Kiralık', 'Günlük Kiralık'],
            'residence' => ['Satılık', 'Kiralık'],
            'mustakil-ev' => ['Satılık', 'Kiralık'],
            'ciftlik-evi' => ['Satılık', 'Kiralık'],
            'kosk' => ['Satılık', 'Kiralık'],
            'yazlik' => ['Satılık', 'Kiralık', 'Günlük Kiralık'],
            'apart' => ['Kiralık', 'Günlük Kiralık'],

            // Arsa kategorileri
            'imarli-arsa' => ['Satılık'],
            'tarla' => ['Satılık', 'Kiralık'],
            'bag' => ['Satılık', 'Kiralık'],
            'bahce' => ['Satılık', 'Kiralık'],
            'zeytinlik' => ['Satılık', 'Kiralık'],
            'turistik-arsa' => ['Satılık'],

            // İşyeri kategorileri
            'dukkan' => ['Satılık', 'Kiralık', 'Devren Satılık', 'Devren Kiralık'],
            'magaza' => ['Satılık', 'Kiralık', 'Devren Kiralık'],
            'plaza-avm' => ['Satılık', 'Kiralık'],
            'ofis' => ['Satılık', 'Kiralık'],
            'depo' => ['Satılık', 'Kiralık'],
            'fabrika' => ['Satılık', 'Kiralık'],
            'imalathane' => ['Satılık', 'Kiralık'],
            'atolye' => ['Satılık', 'Kiralık'],
            'restaurant-cafe' => ['Kiralık', 'Devren Kiralık', 'Devren Satılık'],

            // Turistik tesis
            'otel' => ['Satılık', 'Kiralık'],
            'pansiyon' => ['Satılık', 'Kiralık'],
            'apart-otel' => ['Satılık', 'Kiralık'],
            'butik-otel' => ['Satılık', 'Kiralık'],
            'tatil-koyu' => ['Satılık', 'Kiralık'],
            'motel' => ['Satılık', 'Kiralık'],

            // Projeler
            'konut-projesi' => ['Satılık'],
            'villa-projesi' => ['Satılık'],
            'residence-projesi' => ['Satılık'],
            'ticari-proje' => ['Satılık', 'Kiralık'],
        ];

        $order = 0;
        $totalAdded = 0;

        foreach ($kategoriYayinMap as $kategoriSlug => $yayinTipleriList) {
            $kategori = IlanKategori::where('slug', $kategoriSlug)->first();

            if (!$kategori) {
                $this->command->warn("⚠️ Kategori bulunamadı: {$kategoriSlug}");
                continue;
            }

            foreach ($yayinTipleriList as $index => $yayinTipi) {
                // ✅ Context7: slug column yok, sadece mevcut alanları kullan
                $created = IlanKategoriYayinTipi::updateOrCreate(
                    [
                        'kategori_id' => $kategori->id,
                        'yayin_tipi' => $yayinTipi,
                    ],
                    [
                        'status' => 'Aktif',
                        'display_order' => $index + 1,
                        // slug column yok - otomatik oluşturulmaz
                    ]
                );

                if ($created->wasRecentlyCreated) {
                    $totalAdded++;
                }
            }
        }

        $this->command->info("✅ Yayın tipleri eklendi!");
        $this->command->info("   Toplam: " . IlanKategoriYayinTipi::count());
        $this->command->info("   Yeni eklenen: {$totalAdded}");

        // ✅ Kategori bazında dağılımı göster
        $this->command->info("\n📊 Kategori Bazında Dağılım:");
        $distribution = IlanKategoriYayinTipi::selectRaw('kategori_id, COUNT(*) as count')
            ->groupBy('kategori_id')
            ->get();

        foreach ($distribution as $dist) {
            $kategori = IlanKategori::find($dist->kategori_id);
            if ($kategori) {
                $this->command->info("   {$kategori->name}: {$dist->count} yayın tipi");
            }
        }
    }
}
