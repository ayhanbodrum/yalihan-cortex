<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $yazlikKiralama = DB::table('ilan_kategorileri')->where('slug', 'yazlik-kiralama')->first();
        $konut = DB::table('ilan_kategorileri')->where('slug', 'konut')->first();

        if (!$yazlikKiralama) {
            echo "Yazlık Kiralama kategorisi bulunamadı!\n";
            return;
        }

        DB::table('ilan_kategorileri')
            ->whereIn('slug', ['villa', 'daire'])
            ->update([
                'parent_id' => $yazlikKiralama->id,
                'seviye' => 1,
            ]);

        DB::table('ilan_kategorileri')
            ->whereIn('slug', ['gunluk-kiralama', 'haftalik-kiralama', 'aylik-kiralama'])
            ->update([
                'seviye' => 2,
            ]);

        $mustakil = DB::table('ilan_kategorileri')->where('slug', 'mustakil')->where('parent_id', $yazlikKiralama->id)->first();
        if (!$mustakil) {
            DB::table('ilan_kategorileri')->insert([
                'name' => 'Müstakil',
                'slug' => 'mustakil-yazlik',
                'parent_id' => $yazlikKiralama->id,
                'seviye' => 1,
                'status' => 1,
                'order' => 3,
                'icon' => 'home',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $bungalov = DB::table('ilan_kategorileri')->where('slug', 'bungalov')->where('parent_id', $yazlikKiralama->id)->first();
        if (!$bungalov) {
            DB::table('ilan_kategorileri')->insert([
                'name' => 'Bungalov',
                'slug' => 'bungalov',
                'parent_id' => $yazlikKiralama->id,
                'seviye' => 1,
                'status' => 1,
                'order' => 4,
                'icon' => 'home',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Yayın Tipleri (seviye=2)
        $gunluk = DB::table('ilan_kategorileri')->where('slug', 'gunluk-kiralama')->first();
        if (!$gunluk) {
            DB::table('ilan_kategorileri')->insert([
                'name' => 'Günlük Kiralama',
                'slug' => 'gunluk-kiralama',
                'parent_id' => $yazlikKiralama->id,
                'seviye' => 2,
                'status' => 1,
                'order' => 1,
                'icon' => 'calendar-day',
                'aciklama' => 'Günlük kiralık yazlık ilanları',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $haftalik = DB::table('ilan_kategorileri')->where('slug', 'haftalik-kiralama')->first();
        if (!$haftalik) {
            DB::table('ilan_kategorileri')->insert([
                'name' => 'Haftalık Kiralama',
                'slug' => 'haftalik-kiralama',
                'parent_id' => $yazlikKiralama->id,
                'seviye' => 2,
                'status' => 1,
                'order' => 2,
                'icon' => 'calendar-week',
                'aciklama' => 'Haftalık kiralık yazlık ilanları',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $aylik = DB::table('ilan_kategorileri')->where('slug', 'aylik-kiralama')->first();
        if (!$aylik) {
            DB::table('ilan_kategorileri')->insert([
                'name' => 'Aylık Kiralama',
                'slug' => 'aylik-kiralama',
                'parent_id' => $yazlikKiralama->id,
                'seviye' => 2,
                'status' => 1,
                'order' => 3,
                'icon' => 'calendar-alt',
                'aciklama' => 'Aylık kiralık yazlık ilanları',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $sezonluk = DB::table('ilan_kategorileri')->where('slug', 'sezonluk-kiralama')->first();
        if (!$sezonluk) {
            DB::table('ilan_kategorileri')->insert([
                'name' => 'Sezonluk Kiralama',
                'slug' => 'sezonluk-kiralama',
                'parent_id' => $yazlikKiralama->id,
                'seviye' => 2,
                'status' => 1,
                'order' => 4,
                'icon' => 'calendar',
                'aciklama' => 'Sezonluk kiralık yazlık ilanları',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "✅ Yazlık Kiralama kategori yapısı düzeltildi!\n";
        echo "✅ Villa ve Daire, Yazlık Kiralama'nın altına taşındı\n";
        echo "✅ Müstakil ve Bungalov eklendi\n";
        echo "✅ Yayın tipleri (Günlük, Haftalık, Aylık, Sezonluk) eklendi\n";
    }

    public function down(): void
    {
        $konut = DB::table('ilan_kategorileri')->where('slug', 'konut')->first();

        if ($konut) {
            DB::table('ilan_kategorileri')
                ->whereIn('slug', ['villa', 'daire'])
                ->update([
                    'parent_id' => $konut->id,
                    'seviye' => 1,
                ]);
        }

        DB::table('ilan_kategorileri')
            ->whereIn('slug', ['gunluk-kiralama', 'haftalik-kiralama', 'aylik-kiralama'])
            ->update([
                'seviye' => 1,
            ]);

        DB::table('ilan_kategorileri')
            ->whereIn('slug', ['mustakil-yazlik', 'bungalov', 'sezonluk-kiralama'])
            ->delete();
    }
};
