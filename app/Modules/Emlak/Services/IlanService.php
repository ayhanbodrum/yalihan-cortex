<?php

namespace App\Modules\Emlak\Services;

use App\Models\Talep;
use App\Modules\Crm\Models\Musteri;
use App\Modules\Emlak\Models\Ilan;
use App\Modules\TakimYonetimi\Models\Gorev;
use Illuminate\Support\Facades\DB;

class IlanService
{
    /**
     * İlan oluştur
     */
    public function createIlan(array $data): Ilan
    {
        return DB::transaction(function () use ($data) {
            $ilan = Ilan::create($data);

            // İlan oluşturulduğunda aktivite kaydı
            $ilan->aktiviteler()->create([
                'tip' => 'ilan_olusturuldu',
                'aciklama' => 'İlan oluşturuldu',
                'user_id' => auth()->id(),
                'tarih' => now(),
            ]);

            return $ilan;
        });
    }

    /**
     * İlan güncelle
     */
    public function updateIlan(Ilan $ilan, array $data): Ilan
    {
        return DB::transaction(function () use ($ilan, $data) {
            $ilan->update($data);

            // İlan güncellendiğinde aktivite kaydı
            $ilan->aktiviteler()->create([
                'tip' => 'ilan_guncellendi',
                'aciklama' => 'İlan güncellendi',
                'user_id' => auth()->id(),
                'tarih' => now(),
            ]);

            return $ilan;
        });
    }

    /**
     * İlan sil
     */
    public function deleteIlan(Ilan $ilan): bool
    {
        return DB::transaction(function () use ($ilan) {
            // İlişkili aktiviteleri sil
            $ilan->aktiviteler()->delete();

            // İlanı sil
            return $ilan->delete();
        });
    }

    /**
     * Müşteri-ilan eşleştirmesi
     */
    public function attachMusteri(Ilan $ilan, Musteri $musteri, array $pivotData = []): void
    {
        $ilan->kisiler()->syncWithoutDetaching([
            $musteri->id => array_merge([
                'status' => 'ilgileniyor',
                'created_at' => now(),
            ], $pivotData),
        ]);
    }

    /**
     * Müşteri-ilan eşleştirmesini kaldır
     */
    public function detachMusteri(Ilan $ilan, Musteri $musteri): void
    {
        $ilan->kisiler()->detach($musteri->id);
    }

    /**
     * Talep-ilan eşleştirmesi
     */
    public function attachTalep(Ilan $ilan, Talep $talep, array $pivotData = []): void
    {
        $ilan->talepler()->syncWithoutDetaching([
            $talep->id => array_merge([
                'skor' => $this->calculateMatchingScore($ilan, $talep),
                'status' => 'eslesme',
                'created_at' => now(),
            ], $pivotData),
        ]);
    }

    /**
     * Talep-ilan eşleştirmesini kaldır
     */
    public function detachTalep(Ilan $ilan, Talep $talep): void
    {
        $ilan->talepler()->detach($talep->id);
    }

    /**
     * Görev-ilan eşleştirmesi
     */
    public function attachGorev(Ilan $ilan, Gorev $gorev, array $pivotData = []): void
    {
        $ilan->gorevler()->syncWithoutDetaching([
            $gorev->id => array_merge([
                'gorev_tipi' => 'ilan_islemi',
                'created_at' => now(),
            ], $pivotData),
        ]);
    }

    /**
     * Görev-ilan eşleştirmesini kaldır
     */
    public function detachGorev(Ilan $ilan, Gorev $gorev): void
    {
        $ilan->gorevler()->detach($gorev->id);
    }

    /**
     * Eşleştirme skoru hesapla
     */
    public function calculateMatchingScore(Ilan $ilan, Talep $talep): float
    {
        $score = 0;
        $totalCriteria = 0;

        // Fiyat eşleştirmesi
        if ($talep->min_fiyat && $talep->max_fiyat) {
            $totalCriteria++;
            if ($ilan->fiyat >= $talep->min_fiyat && $ilan->fiyat <= $talep->max_fiyat) {
                $score += 1;
            } elseif ($ilan->fiyat >= $talep->min_fiyat * 0.9 && $ilan->fiyat <= $talep->max_fiyat * 1.1) {
                $score += 0.7;
            }
        }

        // Lokasyon eşleştirmesi
        if ($talep->il && $ilan->adres_il) {
            $totalCriteria++;
            if ($talep->il === $ilan->adres_il) {
                $score += 1;
            }
        }

        if ($talep->ilce && $ilan->adres_ilce) {
            $totalCriteria++;
            if ($talep->ilce === $ilan->adres_ilce) {
                $score += 1;
            }
        }

        // Alan eşleştirmesi
        if ($talep->min_alan && $talep->max_alan && $ilan->metrekare) {
            $totalCriteria++;
            if ($ilan->metrekare >= $talep->min_alan && $ilan->metrekare <= $talep->max_alan) {
                $score += 1;
            } elseif ($ilan->metrekare >= $talep->min_alan * 0.9 && $ilan->metrekare <= $talep->max_alan * 1.1) {
                $score += 0.7;
            }
        }

        return $totalCriteria > 0 ? ($score / $totalCriteria) * 100 : 0;
    }

    /**
     * İlan istatistikleri
     */
    public function getIlanStats(): array
    {
        return [
            'toplam_ilan' => Ilan::count(),
            'aktif_ilan' => Ilan::where('status', 'status')->count(),
            'satilan_ilan' => Ilan::where('status', 'sold')->count(),
            'kiralanan_ilan' => Ilan::where('status', 'rented')->count(),
            'beklemede_ilan' => Ilan::where('status', 'pending')->count(),
        ];
    }

    /**
     * İlan performans analizi
     */
    public function getIlanPerformance(Ilan $ilan): array
    {
        return [
            'goruntulenme_sayisi' => $ilan->view_count,
            'musteri_sayisi' => $ilan->kisiler()->count(),
            'talep_sayisi' => $ilan->talepler()->count(),
            'gorev_sayisi' => $ilan->gorevler()->count(),
            'aktivite_sayisi' => $ilan->aktiviteler()->count(),
            'ortalama_eslesme_skoru' => $ilan->talepler()->avg('pivot.skor') ?? 0,
        ];
    }
}
