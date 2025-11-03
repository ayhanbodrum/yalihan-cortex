<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // İller tablosuna eksik alanları ekle
        Schema::table('iller', function (Blueprint $table) {
            // ulke_id alanı eksikse ekle
            if (!Schema::hasColumn('iller', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('id')->comment('Ülke ID');
                $table->foreign('country_id')->references('id')->on('ulkeler')->onDelete('set null');
            }

            // plaka_kodu alanı eksikse ekle (il_kodu yerine)
            if (!Schema::hasColumn('iller', 'plaka_kodu')) {
                $table->string('plaka_kodu', 3)->nullable()->after('country_id')->comment('Plaka kodu');
            }

            // telefon_kodu alanı eksikse ekle
            if (!Schema::hasColumn('iller', 'telefon_kodu')) {
                $table->string('telefon_kodu', 4)->nullable()->after('plaka_kodu')->comment('Telefon kodu');
            }
        });

        // İlçeler tablosuna eksik alanları ekle
        Schema::table('ilceler', function (Blueprint $table) {
            // ilce_kodu alanı eksikse ekle
            if (!Schema::hasColumn('ilceler', 'ilce_kodu')) {
                $table->string('ilce_kodu', 10)->nullable()->after('il_id')->comment('İlçe kodu');
            }
        });

        // Mahalleler tablosuna eksik alanları ekle
        Schema::table('mahalleler', function (Blueprint $table) {
            // mahalle_kodu alanı eksikse ekle
            if (!Schema::hasColumn('mahalleler', 'mahalle_kodu')) {
                $table->string('mahalle_kodu', 20)->nullable()->after('ilce_id')->comment('Mahalle kodu');
            }
        });

        // Koordinat verilerini güncelle
        $this->updateCoordinates();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iller', function (Blueprint $table) {
            if (Schema::hasColumn('iller', 'country_id')) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            }
            if (Schema::hasColumn('iller', 'plaka_kodu')) {
                $table->dropColumn('plaka_kodu');
            }
            if (Schema::hasColumn('iller', 'telefon_kodu')) {
                $table->dropColumn('telefon_kodu');
            }
        });

        Schema::table('ilceler', function (Blueprint $table) {
            if (Schema::hasColumn('ilceler', 'ilce_kodu')) {
                $table->dropColumn('ilce_kodu');
            }
        });

        Schema::table('mahalleler', function (Blueprint $table) {
            if (Schema::hasColumn('mahalleler', 'mahalle_kodu')) {
                $table->dropColumn('mahalle_kodu');
            }
        });
    }

    /**
     * Koordinat verilerini güncelle
     */
    private function updateCoordinates(): void
    {
        // Türkiye koordinatları
        $turkiyeCoordinates = [
            'Muğla' => ['lat' => 37.2153, 'lng' => 28.3636],
            'İstanbul' => ['lat' => 41.0082, 'lng' => 28.9784],
            'Ankara' => ['lat' => 39.9334, 'lng' => 32.8597],
            'İzmir' => ['lat' => 38.4192, 'lng' => 27.1287],
            'Antalya' => ['lat' => 36.8969, 'lng' => 30.7133],
            'Aydın' => ['lat' => 37.8560, 'lng' => 27.8416],
            'Denizli' => ['lat' => 37.7765, 'lng' => 29.0864],
            'Balıkesir' => ['lat' => 39.6484, 'lng' => 27.8826],
            'Çanakkale' => ['lat' => 40.1553, 'lng' => 26.4142],
        ];

        // İlçe koordinatları
        $ilceCoordinates = [
            'Bodrum' => ['lat' => 37.0344, 'lng' => 27.4305],
            'Marmaris' => ['lat' => 36.8550, 'lng' => 28.2742],
            'Datça' => ['lat' => 36.7281, 'lng' => 27.6869],
            'Köyceğiz' => ['lat' => 36.9711, 'lng' => 28.6839],
            'Ortaca' => ['lat' => 36.8392, 'lng' => 28.7647],
            'Dalaman' => ['lat' => 36.7658, 'lng' => 28.8028],
            'Fethiye' => ['lat' => 36.6219, 'lng' => 29.1164],
            'Menteşe' => ['lat' => 37.2153, 'lng' => 28.3636],
            'Ula' => ['lat' => 37.1019, 'lng' => 28.4164],
            'Yatağan' => ['lat' => 37.3408, 'lng' => 28.1864],
            'Kavaklıdere' => ['lat' => 37.4408, 'lng' => 28.3664],
            'Milas' => ['lat' => 37.3164, 'lng' => 27.7839],
            'Seydikemer' => ['lat' => 36.6219, 'lng' => 29.1164],
        ];

        // Mahalle koordinatları (Bodrum bölgesi)
        $mahalleCoordinates = [
            'Yalıkavak' => ['enlem' => 37.1019, 'boylam' => 27.2839],
            'Gümüşlük' => ['enlem' => 37.0339, 'boylam' => 27.2339],
            'Türkbükü' => ['enlem' => 37.0169, 'boylam' => 27.2500],
            'Bitez' => ['enlem' => 37.0339, 'boylam' => 27.3169],
            'Ortakent' => ['enlem' => 37.0169, 'boylam' => 27.3500],
            'Konacık' => ['enlem' => 37.0000, 'boylam' => 27.3839],
            'Bodrum Merkez' => ['enlem' => 37.0344, 'boylam' => 27.4305],
            'Gümbet' => ['enlem' => 37.0169, 'boylam' => 27.4500],
            'Turgutreis' => ['enlem' => 37.0000, 'boylam' => 27.2669],
            'Kadıkalesi' => ['enlem' => 36.9839, 'boylam' => 27.2839],
        ];

        // İl koordinatlarını güncelle
        foreach ($turkiyeCoordinates as $ilAdi => $coords) {
            DB::table('iller')
                ->where('il_adi', $ilAdi)
                ->update([
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng'],
                    'updated_at' => now()
                ]);
        }

        // İlçe koordinatlarını güncelle
        foreach ($ilceCoordinates as $ilceAdi => $coords) {
            DB::table('ilceler')
                ->where('ilce_adi', $ilceAdi)
                ->update([
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng'],
                    'updated_at' => now()
                ]);
        }

        // Mahalle koordinatlarını güncelle
        foreach ($mahalleCoordinates as $mahalleAdi => $coords) {
            DB::table('mahalleler')
                ->where('mahalle_adi', $mahalleAdi)
                ->update([
                    'enlem' => $coords['enlem'],
                    'boylam' => $coords['boylam'],
                    'updated_at' => now()
                ]);
        }

        // Türkiye'yi ülke olarak ekle (yoksa)
        $turkiye = DB::table('ulkeler')->where('ulke_kodu', 'TR')->first();
        if (!$turkiye) {
            $turkiyeId = DB::table('ulkeler')->insertGetId([
                'ulke_adi' => 'Türkiye',
                'ulke_kodu' => 'TR',
                'telefon_kodu' => '+90',
                'para_birimi' => 'TRY',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $turkiyeId = $turkiye->id;
        }

        // Tüm illeri Türkiye'ye bağla
        DB::table('iller')->update(['country_id' => $turkiyeId]);

        // Plaka kodlarını güncelle
        $plakaKodlari = [
            'Muğla' => '48',
            'İstanbul' => '34',
            'Ankara' => '06',
            'İzmir' => '35',
            'Antalya' => '07',
            'Aydın' => '09',
            'Denizli' => '20',
            'Balıkesir' => '10',
            'Çanakkale' => '17',
        ];

        foreach ($plakaKodlari as $ilAdi => $plaka) {
            DB::table('iller')
                ->where('il_adi', $ilAdi)
                ->update(['plaka_kodu' => $plaka]);
        }

        // Telefon kodlarını güncelle
        $telefonKodlari = [
            'Muğla' => '252',
            'İstanbul' => '212',
            'Ankara' => '312',
            'İzmir' => '232',
            'Antalya' => '242',
            'Aydın' => '256',
            'Denizli' => '258',
            'Balıkesir' => '266',
            'Çanakkale' => '286',
        ];

        foreach ($telefonKodlari as $ilAdi => $kod) {
            DB::table('iller')
                ->where('il_adi', $ilAdi)
                ->update(['telefon_kodu' => $kod]);
        }
    }
};
