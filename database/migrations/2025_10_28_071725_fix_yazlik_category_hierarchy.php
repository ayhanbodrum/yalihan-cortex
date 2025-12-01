<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Mevcut yanlış yayın tiplerini sil
        DB::table('ilan_kategorileri')
            ->whereIn('slug', ['gunluk-kiralama', 'haftalik-kiralama', 'aylik-kiralama', 'sezonluk-kiralama'])
            ->where('seviye', 2)
            ->delete();

        // 2. Yazlık Kiralama kategorisini bul
        $yazlikKiralama = DB::table('ilan_kategorileri')
            ->where('slug', 'yazlik-kiralama')
            ->first();

        if (! $yazlikKiralama) {
            // Yazlık Kiralama kategorisi yoksa migration'ı atla
            Log::warning('Yazlık Kiralama kategorisi bulunamadı, migration atlanıyor.');

            return;
        }

        // 3. Alt kategorileri bul
        $daire = DB::table('ilan_kategorileri')
            ->where('slug', 'daire')
            ->where('parent_id', $yazlikKiralama->id)
            ->first();

        $villa = DB::table('ilan_kategorileri')
            ->where('slug', 'villa')
            ->where('parent_id', $yazlikKiralama->id)
            ->first();

        // 4. Yayın tiplerini tanımla
        $yayinTipleri = [
            ['name' => 'Günlük Kiralama', 'slug' => 'gunluk-kiralama', 'display_order' => 1], // Context7: order → display_order
            ['name' => 'Haftalık Kiralama', 'slug' => 'haftalik-kiralama', 'display_order' => 2], // Context7: order → display_order
            ['name' => 'Aylık Kiralama', 'slug' => 'aylik-kiralama', 'display_order' => 3], // Context7: order → display_order
            ['name' => 'Sezonluk Kiralama', 'slug' => 'sezonluk-kiralama', 'display_order' => 4], // Context7: order → display_order
        ];

        // 5. Her alt kategori için yayın tiplerini oluştur
        $altKategoriler = [$daire, $villa];

        foreach ($altKategoriler as $altKategori) {
            if (! $altKategori) {
                continue;
            }

            foreach ($yayinTipleri as $yayin) {
                // Duplicate slug olmaması için slug'a alt kategori ekle
                $slug = $altKategori->slug.'-'.$yayin['slug'];

                DB::table('ilan_kategorileri')->insert([
                    'name' => $yayin['name'],
                    'slug' => $slug,
                    'aciklama' => $altKategori->name.' - '.$yayin['name'],
                    'parent_id' => $altKategori->id,
                    'seviye' => 2,
                    'display_order' => $yayin['display_order'], // Context7: order → display_order
                    'status' => 1,
                    'icon' => 'fa-calendar',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Oluşturulan yayın tiplerini geri al
        DB::table('ilan_kategorileri')
            ->where('seviye', 2)
            ->whereIn('slug', [
                'daire-gunluk-kiralama',
                'daire-haftalik-kiralama',
                'daire-aylik-kiralama',
                'daire-sezonluk-kiralama',
                'villa-gunluk-kiralama',
                'villa-haftalik-kiralama',
                'villa-aylik-kiralama',
                'villa-sezonluk-kiralama',
            ])
            ->delete();
    }
};
