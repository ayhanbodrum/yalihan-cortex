<?php

namespace App\Observers;

use App\Models\Kisi;
use App\Models\KisiTask;
use Carbon\Carbon;

class KisiObserver
{
    /**
     * Yeni kişi oluşturulduğunda
     */
    public function created(Kisi $kisi): void
    {
        // 3 gün sonra ilk follow-up task oluştur
        KisiTask::create([
            'kisi_id' => $kisi->id,
            'kullanici_id' => $kisi->danisan_id ?? auth()->id(),
            'baslik' => 'İlk İletişim - '.$kisi->ad.' '.$kisi->soyad,
            'aciklama' => 'Yeni lead ile ilk iletişim kurulmalı. Tanışma ve ihtiyaç analizi yapılmalı.',
            'tarih' => Carbon::now()->addDays(3),
            'oncelik' => 'yuksek',
            'status' => 0,
        ]);
    }

    /**
     * Kişi güncellendiğinde
     */
    public function updated(Kisi $kisi): void
    {
        // Pipeline stage değiştiğinde
        if ($kisi->isDirty('pipeline_stage')) {
            $this->handlePipelineStageChange($kisi);
        }

        // Segment değiştiğinde
        if ($kisi->isDirty('segment')) {
            $this->handleSegmentChange($kisi);
        }
    }

    private function handlePipelineStageChange(Kisi $kisi): void
    {
        // Her stage değişikliğinde otomatik task oluştur
        $taskAciklama = match ($kisi->pipeline_stage) {
            2 => 'İlk iletişim kuruldu. Detaylı ihtiyaç analizi yapılmalı.',
            3 => 'Teklif verildi. Takip edilmeli ve feedback alınmalı.',
            4 => 'Görüşme yapıldı. Karar aşamasında, final takip yapılmalı.',
            5 => 'Müşteri kazanıldı! Onboarding süreci başlatılmalı.',
            0 => 'Lead kaybedildi. Sebep analizi yapılmalı.',
            default => null,
        };

        if ($taskAciklama) {
            KisiTask::create([
                'kisi_id' => $kisi->id,
                'kullanici_id' => $kisi->danisan_id ?? auth()->id(),
                'baslik' => 'Pipeline Güncellemesi - '.$kisi->ad,
                'aciklama' => $taskAciklama,
                'tarih' => Carbon::now()->addDays(1),
                'oncelik' => 'normal',
                'status' => 0,
            ]);
        }
    }

    private function handleSegmentChange(Kisi $kisi): void
    {
        // VIP'e yükseltildiğinde özel task
        if ($kisi->segment === 'vip' && $kisi->getOriginal('segment') !== 'vip') {
            KisiTask::create([
                'kisi_id' => $kisi->id,
                'kullanici_id' => $kisi->danisan_id ?? auth()->id(),
                'baslik' => 'VIP Müşteri - Özel İlgi Gerekli',
                'aciklama' => $kisi->ad.' VIP statüsüne yükseltildi. Kişiselleştirilmiş hizmet sunulmalı.',
                'tarih' => Carbon::now()->addDay(),
                'oncelik' => 'kritik',
                'status' => 0,
            ]);
        }
    }
}
