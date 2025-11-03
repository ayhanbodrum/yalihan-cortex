<?php

namespace App\Modules\CRMSatis\Services;

use App\Modules\CRMSatis\Models\Satis;
use App\Modules\CRMSatis\Models\Sozlesme;
use App\Modules\CRMSatis\Models\SatisRaporu;
use App\Modules\Emlak\Models\Ilan;
use App\Modules\Crm\Models\Musteri;
use App\Modules\TakimYonetimi\Models\Gorev;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SatisService
{
    /**
     * Satış oluştur
     */
    public function createSatis(array $data): Satis
    {
        return DB::transaction(function () use ($data) {
            // Referans numarası oluştur
            if (!isset($data['referans_no'])) {
                $data['referans_no'] = 'SAT-' . date('Y') . '-' . str_pad(Satis::count() + 1, 6, '0', STR_PAD_LEFT);
            }

            $satis = Satis::create($data);

            // Satış oluşturulduğunda varsayılan sözleşme oluştur
            $this->createDefaultSozlesme($satis);

            return $satis;
        });
    }

    /**
     * Satış güncelle
     */
    public function updateSatis(Satis $satis, array $data): Satis
    {
        return DB::transaction(function () use ($satis, $data) {
            $satis->update($data);
            return $satis;
        });
    }

    /**
     * Satış sil
     */
    public function deleteSatis(Satis $satis): bool
    {
        return DB::transaction(function () use ($satis) {
            // İlişkili sözleşmeleri sil
            $satis->sozlesme()->delete();

            // İlişkili raporları sil
            $satis->raporlar()->delete();

            return $satis->delete();
        });
    }

    /**
     * Sözleşme oluştur
     */
    public function createSozlesme(array $data): Sozlesme
    {
        return DB::transaction(function () use ($data) {
            // Sözleşme numarası oluştur
            if (!isset($data['sozlesme_no'])) {
                $data['sozlesme_no'] = 'SOZ-' . date('Y') . '-' . str_pad(Sozlesme::count() + 1, 6, '0', STR_PAD_LEFT);
            }

            return Sozlesme::create($data);
        });
    }

    /**
     * Sözleşme güncelle
     */
    public function updateSozlesme(Sozlesme $sozlesme, array $data): Sozlesme
    {
        return DB::transaction(function () use ($sozlesme, $data) {
            $sozlesme->update($data);
            return $sozlesme;
        });
    }

    /**
     * Satış raporu oluştur
     */
    public function createSatisRaporu(array $data): SatisRaporu
    {
        return DB::transaction(function () use ($data) {
            return SatisRaporu::create($data);
        });
    }

    /**
     * Satış durumunu güncelle
     */
    public function updateSatisDurum(Satis $satis, string $status): bool
    {
        return DB::transaction(function () use ($satis, $status) {
            $satis->updateDurum($status);

            // Durum değişikliğine göre işlemler
            $this->handleStatusChange($satis, $status);

            return true;
        });
    }

    /**
     * Sözleşmeyi onayla
     */
    public function onaylaSozlesme(Sozlesme $sozlesme, $onaylayanId): bool
    {
        return $sozlesme->onayla($onaylayanId);
    }

    /**
     * Sözleşmeyi imzala
     */
    public function imzalaSozlesme(Sozlesme $sozlesme, $imzaTarihi = null, $imzaYeri = null): bool
    {
        return DB::transaction(function () use ($sozlesme, $imzaTarihi, $imzaYeri) {
            $sozlesme->imzala($imzaTarihi, $imzaYeri);

            // Sözleşme imzalandığında satış durumunu güncelle
            if ($sozlesme->satis) {
                $sozlesme->satis->updateDurum('sozlesme');
            }

            return true;
        });
    }

    /**
     * Ödeme durumunu güncelle
     */
    public function updateOdemeDurumu(Satis $satis, string $odemeDurumu, float $odenenTutar = null): bool
    {
        return $satis->updateOdemeDurumu($odemeDurumu, $odenenTutar);
    }

    /**
     * Satış istatistikleri
     */
    public function getSatisStats(): array
    {
        return [
            'toplam_satis' => Satis::count(),
            'baslangic_satis' => Satis::baslangic()->count(),
            'sozlesme_satis' => Satis::sozlesme()->count(),
            'odeme_satis' => Satis::odeme()->count(),
            'teslim_satis' => Satis::teslim()->count(),
            'tamamlanan_satis' => Satis::tamamlanan()->count(),
            'iptal_satis' => Satis::iptal()->count(),
            'toplam_ciro' => Satis::sum('satis_fiyati'),
            'toplam_komisyon' => Satis::sum('komisyon_tutari'),
        ];
    }

    /**
     * Danışman satış raporu
     */
    public function getDanismanSatisRaporu($danismanId, $tarihAraligi = 30): array
    {
        $baslangicTarihi = now()->subDays($tarihAraligi);

        return [
            'toplam_satis' => Satis::where('danisman_id', $danismanId)
                ->where('satis_tarihi', '>=', $baslangicTarihi)
                ->count(),
            'toplam_ciro' => Satis::where('danisman_id', $danismanId)
                ->where('satis_tarihi', '>=', $baslangicTarihi)
                ->sum('satis_fiyati'),
            'toplam_komisyon' => Satis::where('danisman_id', $danismanId)
                ->where('satis_tarihi', '>=', $baslangicTarihi)
                ->sum('komisyon_tutari'),
            'basari_orani' => $this->calculateBasariOrani($danismanId, $baslangicTarihi),
            'ortalama_satis_fiyati' => Satis::where('danisman_id', $danismanId)
                ->where('satis_tarihi', '>=', $baslangicTarihi)
                ->avg('satis_fiyati'),
        ];
    }

    /**
     * Aylık satış raporu
     */
    public function getAylikSatisRaporu($ay, $yil): array
    {
        $baslangicTarihi = "{$yil}-{$ay}-01";
        $bitisTarihi = "{$yil}-{$ay}-31";

        return [
            'toplam_satis' => Satis::whereBetween('satis_tarihi', [$baslangicTarihi, $bitisTarihi])->count(),
            'toplam_ciro' => Satis::whereBetween('satis_tarihi', [$baslangicTarihi, $bitisTarihi])->sum('satis_fiyati'),
            'toplam_komisyon' => Satis::whereBetween('satis_tarihi', [$baslangicTarihi, $bitisTarihi])->sum('komisyon_tutari'),
            'ortalama_satis_fiyati' => Satis::whereBetween('satis_tarihi', [$baslangicTarihi, $bitisTarihi])->avg('satis_fiyati'),
            'satis_tipi_dagilimi' => Satis::whereBetween('satis_tarihi', [$baslangicTarihi, $bitisTarihi])
                ->selectRaw('satis_tipi, COUNT(*) as sayi')
                ->groupBy('satis_tipi')
                ->get()
                ->pluck('sayi', 'satis_tipi')
                ->toArray(),
        ];
    }

    /**
     * Satış arama
     */
    public function searchSatislar($query): Collection
    {
        return Satis::where(function ($q) use ($query) {
                $q->where('referans_no', 'LIKE', "%{$query}%")
                  ->orWhere('sozlesme_no', 'LIKE', "%{$query}%")
                  ->orWhere('fatura_no', 'LIKE', "%{$query}%")
                  ->orWhereHas('musteri', function ($mq) use ($query) {
                      $mq->where('ad', 'LIKE', "%{$query}%")
                         ->orWhere('soyad', 'LIKE', "%{$query}%");
                  })
                  ->orWhereHas('ilan', function ($iq) use ($query) {
                      $iq->where('baslik', 'LIKE', "%{$query}%");
                  });
            })
            ->orderBy('satis_tarihi', 'desc')
            ->get();
    }

    /**
     * Varsayılan sözleşme oluştur
     */
    private function createDefaultSozlesme(Satis $satis): void
    {
        $sozlesmeData = [
            'satis_id' => $satis->id,
            'ilan_id' => $satis->ilan_id,
            'musteri_id' => $satis->musteri_id,
            'danisman_id' => $satis->danisman_id,
            'sozlesme_tipi' => $satis->satis_tipi,
            'sozlesme_tarihi' => now(),
            'gecerlilik_tarihi' => now()->addMonths(6),
            'status' => 'taslak',
            'sozlesme_metni' => $this->generateDefaultSozlesmeMetni($satis),
        ];

        $this->createSozlesme($sozlesmeData);
    }

    /**
     * Varsayılan sözleşme metni oluştur
     */
    private function generateDefaultSozlesmeMetni(Satis $satis): string
    {
        $metin = "SATIŞ SÖZLEŞMESİ\n\n";
        $metin .= "Satış No: {$satis->referans_no}\n";
        $metin .= "Tarih: " . now()->format('d.m.Y') . "\n\n";
        $metin .= "SATICI: Yalıhan Emlak\n";
        $metin .= "ALICI: {$satis->musteri->ad} {$satis->musteri->soyad}\n\n";
        $metin .= "KONU: {$satis->ilan->baslik}\n";
        $metin .= "FİYAT: {$satis->satis_fiyati} {$satis->para_birimi}\n\n";
        $metin .= "Bu sözleşme taraflar arasında imzalanmıştır.\n\n";
        $metin .= "SATICI: _________________\n";
        $metin .= "ALICI: _________________\n";

        return $metin;
    }

    /**
     * Durum değişikliği işlemleri
     */
    private function handleStatusChange(Satis $satis, string $status): void
    {
        switch ($status) {
            case 'sozlesme':
                // Sözleşme durumuna geçildiğinde görev oluştur
                $this->createSozlesmeGorevi($satis);
                break;
            case 'odeme':
                // Ödeme durumuna geçildiğinde görev oluştur
                $this->createOdemeGorevi($satis);
                break;
            case 'teslim':
                // Teslim durumuna geçildiğinde görev oluştur
                $this->createTeslimGorevi($satis);
                break;
            case 'tamamlandi':
                // Tamamlandı durumuna geçildiğinde rapor oluştur
                $this->createTamamlanmaRaporu($satis);
                break;
        }
    }

    /**
     * Sözleşme görevi oluştur
     */
    private function createSozlesmeGorevi(Satis $satis): void
    {
        if ($satis->gorev) {
            $satis->gorev->create([
                'baslik' => 'Sözleşme İmzalanacak',
                'aciklama' => "Satış sözleşmesi imzalanmalı: {$satis->referans_no}",
                'proje_id' => $satis->ilan->proje_id,
                'musteri_id' => $satis->musteri_id,
                'status' => 'bekliyor',
                'oncelik' => 'yuksek',
                'baslangic_tarihi' => now(),
                'bitis_tarihi' => now()->addDays(7),
            ]);
        }
    }

    /**
     * Ödeme görevi oluştur
     */
    private function createOdemeGorevi(Satis $satis): void
    {
        if ($satis->gorev) {
            $satis->gorev->create([
                'baslik' => 'Ödeme Takibi',
                'aciklama' => "Satış ödemesi takip edilmeli: {$satis->referans_no}",
                'proje_id' => $satis->ilan->proje_id,
                'musteri_id' => $satis->musteri_id,
                'status' => 'bekliyor',
                'oncelik' => 'normal',
                'baslangic_tarihi' => now(),
                'bitis_tarihi' => now()->addDays(14),
            ]);
        }
    }

    /**
     * Teslim görevi oluştur
     */
    private function createTeslimGorevi(Satis $satis): void
    {
        if ($satis->gorev) {
            $satis->gorev->create([
                'baslik' => 'Teslim İşlemleri',
                'aciklama' => "Satış teslim işlemleri yapılmalı: {$satis->referans_no}",
                'proje_id' => $satis->ilan->proje_id,
                'musteri_id' => $satis->musteri_id,
                'status' => 'bekliyor',
                'oncelik' => 'yuksek',
                'baslangic_tarihi' => now(),
                'bitis_tarihi' => now()->addDays(3),
            ]);
        }
    }

    /**
     * Tamamlanma raporu oluştur
     */
    private function createTamamlanmaRaporu(Satis $satis): void
    {
        $this->createSatisRaporu([
            'satis_id' => $satis->id,
            'ilan_id' => $satis->ilan_id,
            'musteri_id' => $satis->musteri_id,
            'danisman_id' => $satis->danisman_id,
            'rapor_tipi' => 'ozel',
            'rapor_tarihi' => now(),
            'satis_sayisi' => 1,
            'toplam_ciro' => $satis->satis_fiyati,
            'ortalama_satis_fiyati' => $satis->satis_fiyati,
            'komisyon_toplami' => $satis->komisyon_tutari,
            'basari_orani' => 100,
            'musteri_memnuniyeti' => 5,
            'rapor_metni' => "Satış başarıyla tamamlandı: {$satis->referans_no}",
            'analiz_sonuclari' => "Satış süreci başarıyla tamamlandı.",
            'status' => 'tamamlandi',
            'olusturan_id' => auth()->id(),
        ]);
    }

    /**
     * Başarı oranını hesapla
     */
    private function calculateBasariOrani($danismanId, $baslangicTarihi): float
    {
        $toplamSatis = Satis::where('danisman_id', $danismanId)
            ->where('satis_tarihi', '>=', $baslangicTarihi)
            ->count();

        $tamamlananSatis = Satis::where('danisman_id', $danismanId)
            ->where('satis_tarihi', '>=', $baslangicTarihi)
            ->where('status', 'tamamlandi')
            ->count();

        return $toplamSatis > 0 ? ($tamamlananSatis / $toplamSatis) * 100 : 0;
    }
}
