<?php

namespace App\Modules\TakimYonetimi\Services;

use App\Modules\TakimYonetimi\Models\Gorev;
use App\Modules\TakimYonetimi\Models\GorevTakip;
use App\Modules\TakimYonetimi\Models\TakimUyesi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GorevYonetimService
{
    /**
     * Yeni görev oluştur
     */
    public function gorevOlustur(array $data): Gorev
    {
        try {
            DB::beginTransaction();

            $gorev = Gorev::create($data);

            // Eğer danışman atanmışsa, görev takibini başlat
            if (isset($data['danisman_id']) && $data['danisman_id']) {
                $this->gorevTakipBaslat($gorev->id, $data['danisman_id']);
            }

            DB::commit();

            Log::info("Yeni görev oluşturuldu: {$gorev->id} - {$gorev->baslik}");

            return $gorev;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Görev oluşturma hatası: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Görevi danışmana ata
     */
    public function gorevAta(int $gorevId, int $danismanId): bool
    {
        try {
            $gorev = Gorev::findOrFail($gorevId);
            $danisman = TakimUyesi::where('user_id', $danismanId)->first();

            if (! $danisman) {
                throw new \Exception("Danışman bulunamadı: {$danismanId}");
            }

            if (! $gorev->atanabilirMi()) {
                throw new \Exception("Görev atanamaz statusda: {$gorev->status}");
            }

            DB::beginTransaction();

            $gorev->update([
                'danisman_id' => $danismanId,
                'status' => 'devam_ediyor',
            ]);

            // Görev takibini başlat
            $this->gorevTakipBaslat($gorevId, $danismanId);

            DB::commit();

            Log::info("Görev atandı: {$gorevId} -> Danışman: {$danismanId}");

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Görev atama hatası: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Görev statusunu güncelle
     */
    public function gorevDurumGuncelle(int $gorevId, string $status): bool
    {
        try {
            $gorev = Gorev::findOrFail($gorevId);

            if (! in_array($status, Gorev::getDurumlar())) {
                throw new \Exception("Geçersiz status: {$status}");
            }

            $gorev->update(['status' => $status]);

            Log::info("Görev statusu güncellendi: {$gorevId} -> {$status}");

            return true;
        } catch (\Exception $e) {
            Log::error('Görev status güncelleme hatası: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Görev takibini başlat
     */
    public function gorevTakipBaslat(int $gorevId, int $userId): bool
    {
        try {
            // Aktif takip var mı kontrol et
            $statusTakip = GorevTakip::where('gorev_id', $gorevId)
                ->where('user_id', $userId)
                ->whereIn('status', ['basladi', 'devam_ediyor'])
                ->first();

            if ($statusTakip) {
                return true; // Zaten status takip var
            }

            GorevTakip::create([
                'gorev_id' => $gorevId,
                'user_id' => $userId,
                'status' => 'basladi',
                'baslangic_zamani' => now(),
            ]);

            Log::info("Görev takibi başlatıldı: {$gorevId} -> User: {$userId}");

            return true;
        } catch (\Exception $e) {
            Log::error('Görev takip başlatma hatası: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Görevi başlat
     */
    public function gorevBaslat(int $gorevId, int $userId): bool
    {
        try {
            $gorev = Gorev::findOrFail($gorevId);

            if ($gorev->status !== 'bekliyor') {
                throw new \Exception("Görev başlatılamaz statusda: {$gorev->status}");
            }

            DB::beginTransaction();

            $gorev->update(['status' => 'devam_ediyor']);

            // Görev takibini başlat
            $this->gorevTakipBaslat($gorevId, $userId);

            DB::commit();

            Log::info("Görev başlatıldı: {$gorevId} -> User: {$userId}");

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Görev başlatma hatası: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Görevi tamamla
     */
    public function gorevTamamla(int $gorevId, int $userId, array $data = []): bool
    {
        try {
            $gorev = Gorev::findOrFail($gorevId);

            if (! $gorev->tamamlanabilirMi()) {
                throw new \Exception("Görev tamamlanamaz statusda: {$gorev->status}");
            }

            DB::beginTransaction();

            $gorev->update(['status' => 'tamamlandi']);

            // Görev takibini tamamla
            $statusTakip = GorevTakip::where('gorev_id', $gorevId)
                ->where('user_id', $userId)
                ->whereIn('status', ['basladi', 'devam_ediyor'])
                ->first();

            if ($statusTakip) {
                $statusTakip->tamamla($data['notlar'] ?? null);
            }

            // Danışman performansını güncelle
            if ($gorev->danisman_id) {
                $this->danismanPerformansGuncelle($gorev->danisman_id);
            }

            DB::commit();

            Log::info("Görev tamamlandı: {$gorevId} -> User: {$userId}");

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Görev tamamlama hatası: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Görevi durdur
     */
    public function gorevDurdur(int $gorevId, int $userId, ?string $sebep = null): bool
    {
        try {
            $gorev = Gorev::findOrFail($gorevId);

            if (! $gorev->tamamlanabilirMi()) {
                throw new \Exception("Görev durdurulamaz statusda: {$gorev->status}");
            }

            DB::beginTransaction();

            $gorev->update(['status' => 'beklemede']);

            // Aktif görev takibini durdur
            $statusTakip = GorevTakip::where('gorev_id', $gorevId)
                ->where('user_id', $userId)
                ->whereIn('status', ['basladi', 'devam_ediyor'])
                ->first();

            if ($statusTakip) {
                $statusTakip->durdur($sebep);
            }

            DB::commit();

            Log::info("Görev durduruldu: {$gorevId} -> User: {$userId} - Sebep: {$sebep}");

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Görev durdurma hatası: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Danışman performansını güncelle
     */
    public function danismanPerformansGuncelle(int $danismanId): void
    {
        try {
            $takimUyesi = TakimUyesi::where('user_id', $danismanId)->first();

            if ($takimUyesi) {
                $takimUyesi->performansGuncelle();
            }
        } catch (\Exception $e) {
            Log::error('Danışman performans güncelleme hatası: '.$e->getMessage());
        }
    }

    /**
     * Danışman performans raporu
     */
    public function danismanPerformans(int $danismanId, string $tarihAraligi = '30'): array
    {
        try {
            $baslangicTarihi = now()->subDays($tarihAraligi);

            $gorevler = Gorev::where('danisman_id', $danismanId)
                ->where('created_at', '>=', $baslangicTarihi)
                ->get();

            $tamamlananGorevler = $gorevler->where('status', 'tamamlandi');
            $gecikenGorevler = $gorevler->where('status', '!=', 'tamamlandi')
                ->filter(function ($gorev) {
                    return $gorev->geciktiMi();
                });

            $ortalamaSure = $tamamlananGorevler->avg('tahmini_sure') ?? 0;

            return [
                'toplam_gorev' => $gorevler->count(),
                'tamamlanan_gorev' => $tamamlananGorevler->count(),
                'devam_eden_gorev' => $gorevler->where('status', 'devam_ediyor')->count(),
                'geciken_gorev' => $gecikenGorevler->count(),
                'basari_orani' => $gorevler->count() > 0 ? round(($tamamlananGorevler->count() / $gorevler->count()) * 100, 2) : 0,
                'ortalama_sure' => round($ortalamaSure / 60, 2), // Saat cinsinden
                'tarih_araligi' => $tarihAraligi.' gün',
            ];
        } catch (\Exception $e) {
            Log::error('Danışman performans raporu hatası: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Takım performans raporu
     */
    public function takimPerformans(string $tarihAraligi = '30'): array
    {
        try {
            $baslangicTarihi = now()->subDays($tarihAraligi);

            $gorevler = Gorev::where('created_at', '>=', $baslangicTarihi)->get();
            $danismanlar = TakimUyesi::where('rol', 'danisman')->get();

            $danismanPerformanslari = [];
            foreach ($danismanlar as $danisman) {
                $danismanPerformanslari[] = [
                    'danisman_id' => $danisman->user_id,
                    'danisman_adi' => $danisman->user->name ?? 'Bilinmeyen',
                    'performans' => $this->danismanPerformans($danisman->user_id, $tarihAraligi),
                ];
            }

            return [
                'toplam_gorev' => $gorevler->count(),
                'tamamlanan_gorev' => $gorevler->where('status', 'tamamlandi')->count(),
                'devam_eden_gorev' => $gorevler->where('status', 'devam_ediyor')->count(),
                'geciken_gorev' => $gorevler->where('status', '!=', 'tamamlandi')->filter(function ($gorev) {
                    return $gorev->geciktiMi();
                })->count(),
                'danisman_performanslari' => $danismanPerformanslari,
                'tarih_araligi' => $tarihAraligi.' gün',
            ];
        } catch (\Exception $e) {
            Log::error('Takım performans raporu hatası: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Görev istatistikleri
     */
    public function gorevIstatistikleri(): array
    {
        try {
            $bugun = now()->startOfDay();
            $buHafta = now()->startOfWeek();
            $buAy = now()->startOfMonth();

            return [
                'bugun' => [
                    'yeni_gorev' => Gorev::whereDate('created_at', $bugun)->count(),
                    'tamamlanan_gorev' => Gorev::whereDate('updated_at', $bugun)->where('status', 'tamamlandi')->count(),
                    'geciken_gorev' => Gorev::where('deadline', '<', now())->where('status', '!=', 'tamamlandi')->count(),
                ],
                'bu_hafta' => [
                    'yeni_gorev' => Gorev::where('created_at', '>=', $buHafta)->count(),
                    'tamamlanan_gorev' => Gorev::where('updated_at', '>=', $buHafta)->where('status', 'tamamlandi')->count(),
                    'geciken_gorev' => Gorev::where('deadline', '<', now())->where('status', '!=', 'tamamlandi')->count(),
                ],
                'bu_ay' => [
                    'yeni_gorev' => Gorev::where('created_at', '>=', $buAy)->count(),
                    'tamamlanan_gorev' => Gorev::where('updated_at', '>=', $buAy)->where('status', 'tamamlandi')->count(),
                    'geciken_gorev' => Gorev::where('deadline', '<', now())->where('status', '!=', 'tamamlandi')->count(),
                ],
                'genel' => [
                    'toplam_gorev' => Gorev::count(),
                    'status_gorev' => Gorev::whereIn('status', ['bekliyor', 'devam_ediyor'])->count(),
                    'tamamlanan_gorev' => Gorev::where('status', 'tamamlandi')->count(),
                    'iptal_gorev' => Gorev::where('status', 'iptal')->count(),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Görev istatistikleri hatası: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Deadline yaklaşan görevleri getir
     */
    public function deadlineYaklasanGorevler(int $gun = 1): array
    {
        try {
            return Gorev::deadlineYaklasan($gun)
                ->with(['danisman', 'musteri', 'proje'])
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Deadline yaklaşan görevler hatası: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Geciken görevleri getir
     */
    public function gecikenGorevler(): array
    {
        try {
            return Gorev::geciken()
                ->with(['danisman', 'musteri', 'proje'])
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Geciken görevler hatası: '.$e->getMessage());

            return [];
        }
    }
}
