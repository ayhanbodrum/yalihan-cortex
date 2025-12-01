<?php

namespace App\Services\CRM;

use App\Models\Kisi;
use Carbon\Carbon;

class KisiScoringService
{
    /**
     * Kişi için lead score hesapla (0-100)
     */
    public function calculateScore(Kisi $kisi): int
    {
        $score = 0;

        // Son etkileşim (0-20 puan)
        $score += $this->sonEtkilesimSkoru($kisi);

        // İlan sayısı (0-20 puan)
        $score += $this->ilanSayisiSkoru($kisi);

        // Talep sayısı (0-20 puan)
        $score += $this->talepSayisiSkoru($kisi);

        // Pipeline stage (0-20 puan)
        $score += $this->pipelineSkoru($kisi);

        // Referans (0-10 puan)
        $score += $this->referansSkoru($kisi);

        // VIP segment bonus (0-10 puan)
        $score += $this->segmentSkoru($kisi);

        return min(100, max(0, $score));
    }

    private function sonEtkilesimSkoru(Kisi $kisi): int
    {
        if (! $kisi->son_etkilesim) {
            return 0;
        }

        $gunFarki = Carbon::now()->diffInDays($kisi->son_etkilesim);

        return match (true) {
            $gunFarki <= 7 => 20,
            $gunFarki <= 14 => 15,
            $gunFarki <= 30 => 10,
            $gunFarki <= 60 => 5,
            default => 0,
        };
    }

    private function ilanSayisiSkoru(Kisi $kisi): int
    {
        $ilanSayisi = $kisi->ilanlar()->count();

        return match (true) {
            $ilanSayisi >= 10 => 20,
            $ilanSayisi >= 5 => 15,
            $ilanSayisi >= 3 => 10,
            $ilanSayisi >= 1 => 5,
            default => 0,
        };
    }

    private function talepSayisiSkoru(Kisi $kisi): int
    {
        $talepSayisi = $kisi->talepler()->count();

        return match (true) {
            $talepSayisi >= 5 => 20,
            $talepSayisi >= 3 => 15,
            $talepSayisi >= 1 => 10,
            default => 0,
        };
    }

    private function pipelineSkoru(Kisi $kisi): int
    {
        return match ($kisi->pipeline_stage) {
            5 => 20, // Kazanıldı
            4 => 15, // Görüşme yapıldı
            3 => 10, // Teklif verildi
            2 => 5,  // İletişim kuruldu
            default => 0,
        };
    }

    private function referansSkoru(Kisi $kisi): int
    {
        return $kisi->referans_kisi_id ? 10 : 0;
    }

    private function segmentSkoru(Kisi $kisi): int
    {
        return match ($kisi->segment) {
            'vip' => 10,
            'aktif' => 5,
            default => 0,
        };
    }

    /**
     * Tüm kişilerin skorlarını yeniden hesapla
     */
    public function recalculateAllScores(): void
    {
        Kisi::chunk(100, function ($kisiler) {
            foreach ($kisiler as $kisi) {
                $kisi->update(['skor' => $this->calculateScore($kisi)]);
            }
        });
    }
}
