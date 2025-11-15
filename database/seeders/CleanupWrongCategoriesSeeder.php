<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IlanKategori;
use Illuminate\Support\Facades\DB;

class CleanupWrongCategoriesSeeder extends Seeder
{
    /**
     * Yanlış Seviye Kayıtlarını Temizle
     * Context7 Compliant - 2025-10-23
     *
     * SORUN:
     * - "Satılık", "Kiralık" seviye=1 (YANLIŞ! Bunlar yayın tipi)
     * - Bunlar ilan_kategorileri'nde OLMAMALI
     * - Sadece ilan_kategori_yayin_tipleri'nde olmalı
     */
    public function run(): void
    {
        $this->command->info("🧹 Yanlış kategori kayıtları temizleniyor...\n");

        // ❌ YANLIŞ: ilan_kategorileri'nde Yayın Tipi olarak kayıtlı olanlar
        $yanlisYayinTipleri = [
            'Satılık',
            'Kiralık',
            'Günlük Kiralık',
            'Sezonluk Kiralık',
            'Devren',
            'Kat Karşılığı',
        ];

        $silinecekler = IlanKategori::whereIn('name', $yanlisYayinTipleri)
            ->where('parent_id', '!=', null) // Sadece alt kategorilerdeki
            ->get();

        if ($silinecekler->count() > 0) {
            $this->command->warn("⚠️  Yanlış kayıtlar bulundu: " . $silinecekler->count());

            foreach ($silinecekler as $kategori) {
                // İlan var mı kontrol et
                $ilanSayisi = DB::table('ilanlar')
                    ->where(function($q) use ($kategori) {
                        $q->where('ana_kategori_id', $kategori->id)
                          ->orWhere('alt_kategori_id', $kategori->id)
                          ->orWhere('yayin_tipi_id', $kategori->id);
                    })
                    ->count();

                if ($ilanSayisi > 0) {
                    $this->command->error("   ❌ Silinemedi: {$kategori->name} ({$ilanSayisi} ilan var)");
                    $this->command->info("      → Manuel olarak ilanları başka kategoriye taşıyın");
                } else {
                    $kategori->delete();
                    $this->command->info("   ✅ Silindi: {$kategori->name} (ID: {$kategori->id})");
                }
            }
        } else {
            $this->command->info("✅ Yanlış kayıt bulunamadı!");
        }

        // ✅ Arsa alt kategorilerinin seviye kontrolü
        $this->command->info("\n🔍 Arsa alt kategorileri kontrol ediliyor...");

        $arsa = IlanKategori::where('name', 'Arsa')->first();

        if ($arsa) {
            $arsaAltlari = IlanKategori::where('parent_id', $arsa->id)->get();

            $yanlisSeviye = $arsaAltlari->where('seviye', '!=', 1);

            if ($yanlisSeviye->count() > 0) {
                $this->command->warn("⚠️  Yanlış seviye bulundu: " . $yanlisSeviye->count());

                foreach ($yanlisSeviye as $kategori) {
                    $kategori->seviye = 1; // Düzelt
                    $kategori->save();
                    $this->command->info("   ✏️  Düzeltildi: {$kategori->name} (seviye → 1)");
                }
            } else {
                $this->command->info("   ✅ Tüm arsa altları doğru seviyede!");
            }
        }

        $this->command->info("\n📊 TEMİZLİK RAPORU:");
        $this->command->info("   Ana Kategori (seviye=0): " . IlanKategori::whereNull('parent_id')->count());
        $this->command->info("   Alt Kategori (seviye=1): " . IlanKategori::where('seviye', 1)->whereNotNull('parent_id')->count());
        $this->command->info("   Yayın Tipi (seviye=2): " . IlanKategori::where('seviye', 2)->count());
        $this->command->info("\n   ⚠️  Seviye=2 kayıtlar varsa bunlar HATA! (Yayın tipleri buraya ait değil)");
    }
}
