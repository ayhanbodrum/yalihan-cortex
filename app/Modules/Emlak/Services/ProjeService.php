<?php

namespace App\Modules\Emlak\Services;

use App\Modules\Emlak\Models\Proje;
use App\Modules\Emlak\Models\Ilan;
use App\Modules\TakimYonetimi\Models\Gorev;
use App\Modules\Crm\Models\Musteri;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProjeService
{
    /**
     * Proje oluştur
     */
    public function createProje(array $data): Proje
    {
        return DB::transaction(function () use ($data) {
            $proje = Proje::create($data);
            
            // Proje oluşturulduğunda varsayılan görevleri oluştur
            $this->createDefaultTasks($proje);
            
            return $proje;
        });
    }

    /**
     * Proje güncelle
     */
    public function updateProje(Proje $proje, array $data): Proje
    {
        return DB::transaction(function () use ($proje, $data) {
            $proje->update($data);
            return $proje;
        });
    }

    /**
     * Proje sil
     */
    public function deleteProje(Proje $proje): bool
    {
        return DB::transaction(function () use ($proje) {
            // İlişkili ilanları kontrol et
            if ($proje->ilanlar()->count() > 0) {
                throw new \Exception('Bu projede ilanlar var, silinemez.');
            }
            
            // İlişkili görevleri sil
            $proje->gorevler()->delete();
            
            return $proje->delete();
        });
    }

    /**
     * Proje'ye ilan ekle
     */
    public function attachIlanToProje(Proje $proje, Ilan $ilan): void
    {
        $ilan->update(['proje_id' => $proje->id]);
    }

    /**
     * Proje'den ilan kaldır
     */
    public function detachIlanFromProje(Proje $proje, Ilan $ilan): void
    {
        $ilan->update(['proje_id' => null]);
    }

    /**
     * Proje'ye müşteri ekle
     */
    public function attachMusteriToProje(Proje $proje, Musteri $musteri): void
    {
        $proje->kisiler()->syncWithoutDetaching([
            $musteri->id => [
                'rol' => 'musteri',
                'created_at' => now(),
            ]
        ]);
    }

    /**
     * Proje'den müşteri kaldır
     */
    public function detachMusteriFromProje(Proje $proje, Musteri $musteri): void
    {
        $proje->kisiler()->detach($musteri->id);
    }

    /**
     * Proje durumunu güncelle
     */
    public function updateProjeStatus(Proje $proje, string $status): bool
    {
        return DB::transaction(function () use ($proje, $status) {
            $proje->update(['proje_statusu' => $status]);
            
            // Durum değişikliğine göre görevler oluştur
            $this->createStatusChangeTasks($proje, $status);
            
            return true;
        });
    }

    /**
     * Proje ilerlemesini hesapla
     */
    public function calculateProjeProgress(Proje $proje): float
    {
        $toplamGorev = $proje->gorevler()->count();
        
        if ($toplamGorev === 0) {
            return 0;
        }
        
        $tamamlananGorev = $proje->gorevler()->where('status', 'tamamlandi')->count();
        
        return ($tamamlananGorev / $toplamGorev) * 100;
    }

    /**
     * Proje istatistikleri
     */
    public function getProjeStats(): array
    {
        return [
            'toplam_proje' => Proje::count(),
            'aktif_proje' => Proje::where('proje_statusu', 'status')->count(),
            'tamamlanan_proje' => Proje::where('proje_statusu', 'tamamlandi')->count(),
            'beklemede_proje' => Proje::where('proje_statusu', 'beklemede')->count(),
            'iptal_proje' => Proje::where('proje_statusu', 'iptal')->count(),
            'ortalama_ilerleme' => Proje::withCount(['gorevler as tamamlanan_gorev' => function ($query) {
                $query->where('status', 'tamamlandi');
            }])
            ->withCount('gorevler')
            ->get()
            ->map(function ($proje) {
                return $proje->gorevler_count > 0 ? ($proje->tamamlanan_gorev / $proje->gorevler_count) * 100 : 0;
            })
            ->avg(),
        ];
    }

    /**
     * Proje performans analizi
     */
    public function getProjePerformance(Proje $proje): array
    {
        $gorevler = $proje->gorevler();
        $ilanlar = $proje->ilanlar();
        
        return [
            'toplam_gorev' => $gorevler->count(),
            'tamamlanan_gorev' => $gorevler->where('status', 'tamamlandi')->count(),
            'bekleyen_gorev' => $gorevler->where('status', 'bekliyor')->count(),
            'devam_eden_gorev' => $gorevler->where('status', 'devam_ediyor')->count(),
            'toplam_ilan' => $ilanlar->count(),
            'satilan_ilan' => $ilanlar->where('status', 'sold')->count(),
            'kiralanan_ilan' => $ilanlar->where('status', 'rented')->count(),
            'aktif_ilan' => $ilanlar->where('status', 'status')->count(),
            'ilerleme_yuzdesi' => $this->calculateProjeProgress($proje),
            'ortalama_gorev_suresi' => $this->calculateAverageTaskDuration($proje),
        ];
    }

    /**
     * Proje arama
     */
    public function searchProjeler($query): Collection
    {
        return Proje::where(function ($q) use ($query) {
                $q->where('proje_adi', 'LIKE', "%{$query}%")
                  ->orWhere('aciklama', 'LIKE', "%{$query}%")
                  ->orWhere('lokasyon', 'LIKE', "%{$query}%");
            })
            ->orderBy('proje_adi')
            ->get();
    }

    /**
     * Durum değişikliği görevleri oluştur
     */
    private function createStatusChangeTasks(Proje $proje, string $status): void
    {
        $gorevler = match($status) {
            'status' => [
                ['baslik' => 'Proje başlatıldı', 'aciklama' => 'Proje aktif duruma geçirildi', 'oncelik' => 'yuksek'],
                ['baslik' => 'İlanlar hazırlanacak', 'aciklama' => 'Proje ilanları hazırlanmalı', 'oncelik' => 'normal'],
            ],
            'tamamlandi' => [
                ['baslik' => 'Proje tamamlandı', 'aciklama' => 'Proje başarıyla tamamlandı', 'oncelik' => 'yuksek'],
                ['baslik' => 'Final raporu hazırla', 'aciklama' => 'Proje final raporu hazırlanmalı', 'oncelik' => 'normal'],
            ],
            'iptal' => [
                ['baslik' => 'Proje iptal edildi', 'aciklama' => 'Proje iptal edildi, gerekli işlemler yapılmalı', 'oncelik' => 'yuksek'],
            ],
            default => [],
        };

        foreach ($gorevler as $gorevData) {
            $proje->gorevler()->create(array_merge($gorevData, [
                'proje_id' => $proje->id,
                'status' => 'bekliyor',
                'baslangic_tarihi' => now(),
                'bitis_tarihi' => now()->addDays(7),
            ]));
        }
    }

    /**
     * Varsayılan görevleri oluştur
     */
    private function createDefaultTasks(Proje $proje): void
    {
        $varsayilanGorevler = [
            [
                'baslik' => 'Proje planlaması',
                'aciklama' => 'Proje detayları planlanmalı',
                'oncelik' => 'yuksek',
                'status' => 'bekliyor',
            ],
            [
                'baslik' => 'İlan hazırlığı',
                'aciklama' => 'Proje ilanları hazırlanmalı',
                'oncelik' => 'normal',
                'status' => 'bekliyor',
            ],
            [
                'baslik' => 'Müşteri iletişimi',
                'aciklama' => 'Müşteri ile iletişim kurulmalı',
                'oncelik' => 'normal',
                'status' => 'bekliyor',
            ],
        ];

        foreach ($varsayilanGorevler as $gorevData) {
            $proje->gorevler()->create(array_merge($gorevData, [
                'proje_id' => $proje->id,
                'baslangic_tarihi' => now(),
                'bitis_tarihi' => now()->addDays(14),
            ]));
        }
    }

    /**
     * Ortalama görev süresini hesapla
     */
    private function calculateAverageTaskDuration(Proje $proje): float
    {
        $tamamlananGorevler = $proje->gorevler()
            ->where('status', 'tamamlandi')
            ->whereNotNull('tamamlanma_tarihi')
            ->get();

        if ($tamamlananGorevler->isEmpty()) {
            return 0;
        }

        $toplamSure = $tamamlananGorevler->sum(function ($gorev) {
            return $gorev->baslangic_tarihi->diffInDays($gorev->tamamlanma_tarihi);
        });

        return $toplamSure / $tamamlananGorevler->count();
    }
}
