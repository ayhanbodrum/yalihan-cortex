<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->completeRemainingCoordinates();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bu migration geri alınamaz - koordinat verileri kalıcı
    }

    /**
     * Kalan koordinat verilerini tamamla
     */
    private function completeRemainingCoordinates(): void
    {
        // Eksik il koordinatları
        $missingIlCoordinates = [
            'Adana' => ['lat' => 37.0000, 'lng' => 35.3213],
            'Bursa' => ['lat' => 40.1826, 'lng' => 29.0665],
            'Mersin' => ['lat' => 36.8000, 'lng' => 34.6333],
            'Kocaeli' => ['lat' => 40.8533, 'lng' => 29.8815],
        ];

        foreach ($missingIlCoordinates as $ilAdi => $coords) {
            DB::table('iller')
                ->where('il_adi', $ilAdi)
                ->whereNull('lat')
                ->orWhereNull('lng')
                ->update([
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng'],
                    'updated_at' => now(),
                ]);
        }

        // Eksik ilçe koordinatları (Muğla ilçeleri)
        $missingIlceCoordinates = [
            'Datça' => ['lat' => 36.7281, 'lng' => 27.6869],
            'Fethiye' => ['lat' => 36.6219, 'lng' => 29.1164],
            'Köyceğiz' => ['lat' => 36.9711, 'lng' => 28.6839],
            'Milas' => ['lat' => 37.3164, 'lng' => 27.7839],
            'Yatağan' => ['lat' => 37.3408, 'lng' => 28.1864],
            'Menteşe' => ['lat' => 37.2153, 'lng' => 28.3636],
            'Ula' => ['lat' => 37.1019, 'lng' => 28.4164],
            'Kavaklıdere' => ['lat' => 37.4408, 'lng' => 28.3664],
            'Seydikemer' => ['lat' => 36.6219, 'lng' => 29.1164],
            'Ortaca' => ['lat' => 36.8392, 'lng' => 28.7647],
            'Dalaman' => ['lat' => 36.7658, 'lng' => 28.8028],
        ];

        foreach ($missingIlceCoordinates as $ilceAdi => $coords) {
            DB::table('ilceler')
                ->where('ilce_adi', $ilceAdi)
                ->whereNull('lat')
                ->orWhereNull('lng')
                ->update([
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng'],
                    'updated_at' => now(),
                ]);
        }

        // Eksik mahalle koordinatları (Bodrum bölgesi)
        $missingMahalleCoordinates = [
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
            'Torba' => ['enlem' => 37.0500, 'boylam' => 27.3000],
            'Gündoğan' => ['enlem' => 37.0669, 'boylam' => 27.2669],
            'Göltürkbükü' => ['enlem' => 37.0169, 'boylam' => 27.2500],
            'Akyarlar' => ['enlem' => 36.9669, 'boylam' => 27.3000],
            'Karaada' => ['enlem' => 37.0169, 'boylam' => 27.4000],
        ];

        foreach ($missingMahalleCoordinates as $mahalleAdi => $coords) {
            DB::table('mahalleler')
                ->where('mahalle_adi', $mahalleAdi)
                ->whereNull('enlem')
                ->orWhereNull('boylam')
                ->update([
                    'enlem' => $coords['enlem'],
                    'boylam' => $coords['boylam'],
                    'updated_at' => now(),
                ]);
        }

        // Rastgele koordinatlar için kalan mahalleler
        $remainingMahalleler = DB::table('mahalleler')
            ->whereNull('enlem')
            ->orWhereNull('boylam')
            ->get();

        foreach ($remainingMahalleler as $mahalle) {
            // İlçe merkezine yakın rastgele koordinat
            $ilce = DB::table('ilceler')->find($mahalle->ilce_id);
            if ($ilce && $ilce->lat && $ilce->lng) {
                $randomLat = $ilce->lat + (rand(-100, 100) / 10000); // ±0.01 derece
                $randomLng = $ilce->lng + (rand(-100, 100) / 10000);

                DB::table('mahalleler')
                    ->where('id', $mahalle->id)
                    ->update([
                        'enlem' => $randomLat,
                        'boylam' => $randomLng,
                        'updated_at' => now(),
                    ]);
            }
        }
    }
};
