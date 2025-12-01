<?php

namespace Database\Seeders;

use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Arsa, İşyeri, Yazlık ve Projeler Kategorileri için Mantıklı Yayın Tipi İlişkileri
 *
 * Bu seeder, alt kategorilere mantıklı yayın tipleri atar.
 */
class ArsaIsyeriYayinTipiSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏢 Kategori Yayın Tipi İlişkileri oluşturuluyor...');

        // ARSA KATEGORİSİ (ID: 2)
        $this->seedArsaKategorisi();

        // İŞYERİ KATEGORİSİ (ID: 3) - Sadece Satılık
        $this->seedIsyeriKategorisi();

        // YAZLIK KATEGORİSİ - Kiralık ekle
        $this->seedYazlikKategorisi();

        // PROJELER KATEGORİSİ (ID: 5)
        $this->seedProjelerKategorisi();

        $this->command->info('✅ Yayın tipi ilişkileri tamamlandı!');
    }

    private function seedArsaKategorisi(): void
    {
        $this->command->info('  📍 Arsa kategorisi işleniyor...');

        $kategoriId = 2; // Arsa

        // Yayın tipleri oluştur/güncelle
        $satilik = $this->getOrCreateYayinTipi($kategoriId, 'Satılık', 1);
        $ticari = $this->getOrCreateYayinTipi($kategoriId, 'Ticari', 3);
        $katKarsiligi = $this->getOrCreateYayinTipi($kategoriId, 'Kat Karşılığı', 4);

        // Alt kategoriler
        $turistikArsa = IlanKategori::where('parent_id', $kategoriId)->where('name', 'Turistik Arsa')->first();
        $digerAltKategoriler = IlanKategori::where('parent_id', $kategoriId)
            ->where('seviye', 1)
            ->where('id', '!=', $turistikArsa?->id)
            ->get();

        // Turistik Arsa → Satılık, Ticari, Kat Karşılığı
        if ($turistikArsa) {
            $this->createAltKategoriYayinTipi($turistikArsa->id, $satilik->id, 1);
            $this->createAltKategoriYayinTipi($turistikArsa->id, $ticari->id, 2);
            $this->createAltKategoriYayinTipi($turistikArsa->id, $katKarsiligi->id, 3);
            $this->command->info('    ✓ Turistik Arsa → Satılık, Ticari, Kat Karşılığı');
        }

        // Diğer alt kategoriler → Sadece Satılık
        foreach ($digerAltKategoriler as $altKat) {
            $this->createAltKategoriYayinTipi($altKat->id, $satilik->id, 1);
            $this->command->info("    ✓ {$altKat->name} → Satılık");
        }
    }

    private function seedIsyeriKategorisi(): void
    {
        $this->command->info('  🏢 İşyeri kategorisi işleniyor...');

        $kategoriId = 3; // İşyeri

        // Yayın tipleri oluştur/güncelle - SADECE SATILIK
        $satilik = $this->getOrCreateYayinTipi($kategoriId, 'Satılık', 1);

        // Alt kategoriler
        $altKategoriler = IlanKategori::where('parent_id', $kategoriId)
            ->where('seviye', 1)
            ->get();

        // Tüm alt kategoriler için SADECE Satılık
        foreach ($altKategoriler as $altKat) {
            $this->createAltKategoriYayinTipi($altKat->id, $satilik->id, 1);
            $this->command->info("    ✓ {$altKat->name} → Satılık");
        }
    }

    private function seedYazlikKategorisi(): void
    {
        $this->command->info('  🏖️ Yazlık kategorisi işleniyor...');

        // Yazlık alt kategori olarak bul
        $yazlik = IlanKategori::where('name', 'Yazlık')->where('seviye', 1)->first();

        if (! $yazlik) {
            $this->command->warn('    ⚠️ Yazlık kategorisi bulunamadı');

            return;
        }

        // Yazlık'ın parent'ı (Konut - ID: 1)
        $konutKategoriId = $yazlik->parent_id;

        // Kiralık yayın tipini oluştur/güncelle
        $kiralik = $this->getOrCreateYayinTipi($konutKategoriId, 'Kiralık', 2);

        // Yazlık alt kategori için Kiralık ekle
        $this->createAltKategoriYayinTipi($yazlik->id, $kiralik->id, 1);
        $this->command->info('    ✓ Yazlık → Kiralık');
    }

    private function seedProjelerKategorisi(): void
    {
        $this->command->info('  🏗️ Projeler kategorisi işleniyor...');

        $kategoriId = 5; // Projeler

        // Yayın tipleri oluştur/güncelle - SADECE SATILIK
        $satilik = $this->getOrCreateYayinTipi($kategoriId, 'Satılık', 1);

        // Alt kategoriler
        $altKategoriler = IlanKategori::where('parent_id', $kategoriId)
            ->where('seviye', 1)
            ->get();

        // Tüm alt kategoriler için SADECE Satılık (Projeler genellikle satılık olur)
        foreach ($altKategoriler as $altKat) {
            $this->createAltKategoriYayinTipi($altKat->id, $satilik->id, 1);
            $this->command->info("    ✓ {$altKat->name} → Satılık");
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
