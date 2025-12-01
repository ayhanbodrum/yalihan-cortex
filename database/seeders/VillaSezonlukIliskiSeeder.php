<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VillaSezonlukIliskiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏖️ Villa + Sezonluk ilişkileri güncelleniyor...');

        // Villa kategorisi ID
        $villaKategoriId = 10;

        // Yazlık Kiralık özellik kategorileri (yeni ID'ler)
        $yazlikOzellikIds = [40, 41, 42, 43, 44, 7, 45]; // Max Kişi, Min Konaklama, Havuz, Bahçe, Denize Uzaklık, Belge No, Dahil Hizmetler

        // Eski ilişkileri temizle
        DB::table('ilan_kategori_ozellik_baglanti')
            ->where('category_id', $villaKategoriId)
            ->where('baglanti_tipi', 'yayin')
            ->delete();

        $this->command->info('🗑️ Eski ilişkiler temizlendi.');

        // Yeni ilişkileri ekle
        foreach ($yazlikOzellikIds as $index => $ozellikId) {
            DB::table('ilan_kategori_ozellik_baglanti')->insert([
                'category_id' => $villaKategoriId,
                'ozellik_kategori_id' => $ozellikId,
                'ozellik_id' => null,
                'baglanti_tipi' => 'yayin',
                'zorunlu' => 0,
                'filtrelemede_goster' => 1,
                'ilan_kartinda_goster' => 1,
                'siralama' => $index + 1,
                'varsayilan_deger' => null,
                'validasyon_kurallari' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ '.count($yazlikOzellikIds).' yeni Villa + Sezonluk ilişkisi eklendi.');

        // Sonucu kontrol et
        $baglantiSayisi = DB::table('ilan_kategori_ozellik_baglanti')
            ->where('category_id', $villaKategoriId)
            ->where('baglanti_tipi', 'yayin')
            ->count();

        $this->command->info('📊 Toplam Villa + Sezonluk ilişkisi: '.$baglantiSayisi);
    }
}
