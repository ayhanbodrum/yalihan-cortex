<?php

namespace Database\Seeders;

use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Konut ve Yazlık Kategorileri için Yayın Tipi İlişkileri
 *
 * Konut alt kategorilerine Satılık ve Kiralık ekler.
 * Yazlık alt kategorisine Kiralık ekler.
 */
class KonutYazlikYayinTipiSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏠 Konut ve Yazlık Yayın Tipi İlişkileri oluşturuluyor...');

        // KONUT KATEGORİSİ (ID: 1)
        $this->seedKonutKategorisi();

        // YAZLIK KATEGORİSİ - Kiralık kontrolü
        $this->seedYazlikKategorisi();

        $this->command->info('✅ Konut ve Yazlık yayın tipi ilişkileri tamamlandı!');
    }

    private function seedKonutKategorisi(): void
    {
        $this->command->info('  🏠 Konut kategorisi işleniyor...');

        $kategoriId = 1; // Konut

        // Yayın tipleri oluştur/güncelle
        $satilik = $this->getOrCreateYayinTipi($kategoriId, 'Satılık', 1);
        $kiralik = $this->getOrCreateYayinTipi($kategoriId, 'Kiralık', 2);

        // Alt kategoriler (Yazlık hariç)
        $altKategoriler = IlanKategori::where('parent_id', $kategoriId)
            ->where('seviye', 1)
            ->where('name', '!=', 'Yazlık')
            ->get();

        // Tüm alt kategoriler için Satılık ve Kiralık
        foreach ($altKategoriler as $altKat) {
            $this->createAltKategoriYayinTipi($altKat->id, $satilik->id, 1);
            $this->createAltKategoriYayinTipi($altKat->id, $kiralik->id, 2);
            $this->command->info("    ✓ {$altKat->name} → Satılık, Kiralık");
        }
    }

    private function seedYazlikKategorisi(): void
    {
        $this->command->info('  🏖️ Yazlık kategorisi kontrol ediliyor...');

        // Yazlık alt kategori olarak bul
        $yazlik = IlanKategori::where('name', 'Yazlık')->where('seviye', 1)->first();

        if (! $yazlik) {
            $this->command->warn('    ⚠️ Yazlık kategorisi bulunamadı');

            return;
        }

        // Yazlık'ın parent'ı (Konut - ID: 1)
        $konutKategoriId = $yazlik->parent_id;

        // Kiralık yayın tipini kontrol et
        $kiralik = $this->getOrCreateYayinTipi($konutKategoriId, 'Kiralık', 2);

        // Yazlık alt kategori için Kiralık kontrolü
        $mevcutIliski = DB::table('alt_kategori_yayin_tipi')
            ->where('alt_kategori_id', $yazlik->id)
            ->where('yayin_tipi_id', $kiralik->id)
            ->exists();

        if (! $mevcutIliski) {
            $this->createAltKategoriYayinTipi($yazlik->id, $kiralik->id, 1);
            $this->command->info('    ✓ Yazlık → Kiralık (eklendi)');
        } else {
            $this->command->info('    ✓ Yazlık → Kiralık (zaten mevcut)');
        }
    }

    private function getOrCreateYayinTipi(int $kategoriId, string $yayinTipi, int $order): IlanKategoriYayinTipi
    {
        return IlanKategoriYayinTipi::firstOrCreate(
            [
                'kategori_id' => $kategoriId,
                'yayin_tipi' => $yayinTipi,
            ],
            [
                'status' => true,
                'display_order' => $order,
            ]
        );
    }

    private function createAltKategoriYayinTipi(int $altKategoriId, int $yayinTipiId, int $order): void
    {
        DB::table('alt_kategori_yayin_tipi')->updateOrInsert(
            [
                'alt_kategori_id' => $altKategoriId,
                'yayin_tipi_id' => $yayinTipiId,
            ],
            [
                'enabled' => true,
                'display_order' => $order,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
