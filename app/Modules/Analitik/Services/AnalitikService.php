<?php

namespace App\Modules\Analitik\Services;

use App\Models\Ilan;
use App\Models\CRMSatis;
use App\Models\FinansTransaction;
use App\Models\Musteri;
use App\Models\Bildirim;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalitikService
{
    /**
     * Genel dashboard verilerini al
     */
    public function getDashboardData(): array
    {
        return [
            'genel_istatistikler' => $this->getGenelIstatistikler(),
            'ilan_istatistikleri' => $this->getIlanIstatistikleri(),
            'satis_istatistikleri' => $this->getSatisIstatistikleri(),
            'finans_istatistikleri' => $this->getFinansIstatistikleri(),
            'musteri_istatistikleri' => $this->getMusteriIstatistikleri(),
            'son_aktiviteler' => $this->getSonAktiviteler()
        ];
    }

    /**
     * Genel istatistikler
     */
    public function getGenelIstatistikler(): array
    {
        $bugun = now()->format('Y-m-d');
        $buAy = now()->format('Y-m');
        $buYil = now()->format('Y');

        return [
            'toplam_ilan' => Ilan::count(),
            'status_ilan' => Ilan::where('status', 'active')->count(),
            'bugun_ilan' => Ilan::whereDate('created_at', $bugun)->count(),
            'bu_ay_ilan' => Ilan::where('created_at', 'like', $buAy . '%')->count(),
            'bu_yil_ilan' => Ilan::where('created_at', 'like', $buYil . '%')->count(),

            'toplam_satis' => CRMSatis::count(),
            'bu_ay_satis' => CRMSatis::where('created_at', 'like', $buAy . '%')->count(),
            'bu_yil_satis' => CRMSatis::where('created_at', 'like', $buYil . '%')->count(),

            'toplam_musteri' => Musteri::count(),
            'status_musteri' => Musteri::where('status', true)->count(),

            'toplam_gelir' => FinansTransaction::gelir()->onaylanmis()->sum('tl_tutar'),
            'bu_ay_gelir' => FinansTransaction::gelir()->onaylanmis()->buAy()->sum('tl_tutar'),
            'bu_yil_gelir' => FinansTransaction::gelir()->onaylanmis()->buYil()->sum('tl_tutar')
        ];
    }

    /**
     * İlan istatistikleri
     */
    public function getIlanIstatistikleri(): array
    {
        $son12Ay = [];
        for ($i = 11; $i >= 0; $i--) {
            $tarih = now()->subMonths($i);
            $son12Ay[] = [
                'ay' => $tarih->format('M Y'),
                'ilan_sayisi' => Ilan::whereYear('created_at', $tarih->year)
                    ->whereMonth('created_at', $tarih->month)
                    ->count()
            ];
        }

        return [
            'status_dagilimi' => Ilan::selectRaw('status, count(*) as sayi')
                ->groupBy('status')
                ->get()
                ->pluck('sayi', 'status')
                ->toArray(),
            'kategori_dagilimi' => Ilan::selectRaw('kategori, count(*) as sayi')
                ->groupBy('kategori')
                ->get()
                ->pluck('sayi', 'kategori')
                ->toArray(),
            'aylik_trend' => $son12Ay,
            'en_populer_ilanlar' => Ilan::with('user')
                ->orderBy('goruntulenme_sayisi', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($ilan) {
                    return [
                        'id' => $ilan->id,
                        'baslik' => $ilan->baslik,
                        'fiyat' => $ilan->fiyat,
                        'goruntulenme' => $ilan->goruntulenme_sayisi,
                        'user' => $ilan->user->name ?? 'Bilinmiyor'
                    ];
                })
        ];
    }

    /**
     * Satış istatistikleri
     */
    public function getSatisIstatistikleri(): array
    {
        $son12Ay = [];
        for ($i = 11; $i >= 0; $i--) {
            $tarih = now()->subMonths($i);
            $son12Ay[] = [
                'ay' => $tarih->format('M Y'),
                'satis_sayisi' => CRMSatis::whereYear('created_at', $tarih->year)
                    ->whereMonth('created_at', $tarih->month)
                    ->count(),
                'satis_tutari' => CRMSatis::whereYear('created_at', $tarih->year)
                    ->whereMonth('created_at', $tarih->month)
                    ->sum('satis_tutari')
            ];
        }

        return [
            'status_dagilimi' => CRMSatis::selectRaw('status, count(*) as sayi')
                ->groupBy('status')
                ->get()
                ->pluck('sayi', 'status')
                ->toArray(),
            'odeme_statusu' => CRMSatis::selectRaw('odeme_statusu, count(*) as sayi')
                ->groupBy('odeme_statusu')
                ->get()
                ->pluck('sayi', 'odeme_statusu')
                ->toArray(),
            'aylik_trend' => $son12Ay,
            'toplam_satis_tutari' => CRMSatis::sum('satis_tutari'),
            'ortalama_satis_tutari' => CRMSatis::avg('satis_tutari'),
            'en_yuksek_satis' => CRMSatis::max('satis_tutari'),
            'en_dusuk_satis' => CRMSatis::min('satis_tutari')
        ];
    }

    /**
     * Finans istatistikleri
     */
    public function getFinansIstatistikleri(): array
    {
        $son12Ay = [];
        for ($i = 11; $i >= 0; $i--) {
            $tarih = now()->subMonths($i);
            $son12Ay[] = [
                'ay' => $tarih->format('M Y'),
                'gelir' => FinansTransaction::gelir()->onaylanmis()
                    ->whereYear('islem_tarihi', $tarih->year)
                    ->whereMonth('islem_tarihi', $tarih->month)
                    ->sum('tl_tutar'),
                'gider' => FinansTransaction::gider()->onaylanmis()
                    ->whereYear('islem_tarihi', $tarih->year)
                    ->whereMonth('islem_tarihi', $tarih->month)
                    ->sum('tl_tutar')
            ];
        }

        return [
            'kategori_dagilimi' => FinansTransaction::selectRaw('kategori, sum(tl_tutar) as toplam')
                ->onaylanmis()
                ->groupBy('kategori')
                ->get()
                ->pluck('toplam', 'kategori')
                ->toArray(),
            'aylik_trend' => $son12Ay,
            'toplam_gelir' => FinansTransaction::gelir()->onaylanmis()->sum('tl_tutar'),
            'toplam_gider' => FinansTransaction::gider()->onaylanmis()->sum('tl_tutar'),
            'net_kar' => FinansTransaction::gelir()->onaylanmis()->sum('tl_tutar') -
                        FinansTransaction::gider()->onaylanmis()->sum('tl_tutar'),
            'bekleyen_odeme' => FinansTransaction::where('status', 'beklemede')->sum('tl_tutar')
        ];
    }

    /**
     * Müşteri istatistikleri
     */
    public function getMusteriIstatistikleri(): array
    {
        return [
            'toplam_musteri' => Musteri::count(),
            'status_musteri' => Musteri::where('status', true)->count(),
            'yeni_musteri' => Musteri::whereMonth('created_at', now()->month)->count(),
            'en_status_musteriler' => Musteri::withCount('ilanlar')
                ->orderBy('ilanlar_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($musteri) {
                    return [
                        'id' => $musteri->id,
                        'ad' => $musteri->tam_ad,
                        'ilan_sayisi' => $musteri->ilanlar_count,
                        'email' => $musteri->email
                    ];
                })
        ];
    }

    /**
     * Son aktiviteler
     */
    public function getSonAktiviteler(): array
    {
        $aktiviteler = collect();

        // Son ilanlar
        $ilanlar = Ilan::with('user')->latest()->limit(5)->get();
        foreach ($ilanlar as $ilan) {
            $aktiviteler->push([
                'tip' => 'ilan',
                'mesaj' => 'Yeni ilan eklendi: ' . $ilan->baslik,
                'tarih' => $ilan->created_at,
                'user' => $ilan->user->name ?? 'Bilinmiyor'
            ]);
        }

        // Son satışlar
        $satislar = CRMSatis::with('user', 'musteri')->latest()->limit(5)->get();
        foreach ($satislar as $satis) {
            $aktiviteler->push([
                'tip' => 'satis',
                'mesaj' => 'Yeni satış: ' . $satis->satis_no,
                'tarih' => $satis->created_at,
                'user' => $satis->user->name ?? 'Bilinmiyor'
            ]);
        }

        // Son finans işlemleri
        $finans = FinansTransaction::with('user')->latest()->limit(5)->get();
        foreach ($finans as $transaction) {
            $aktiviteler->push([
                'tip' => 'finans',
                'mesaj' => 'Finans işlemi: ' . $transaction->transaction_no,
                'tarih' => $transaction->created_at,
                'user' => $transaction->user->name ?? 'Bilinmiyor'
            ]);
        }

        return $aktiviteler->sortByDesc('tarih')->take(10)->values()->toArray();
    }

    /**
     * Performans raporu
     */
    public function getPerformansRaporu(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->subMonth()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->format('Y-m-d');

        return [
            'tarih_araligi' => [
                'baslangic' => $startDate,
                'bitis' => $endDate
            ],
            'ilan_performansi' => $this->getIlanPerformansi($startDate, $endDate),
            'satis_performansi' => $this->getSatisPerformansi($startDate, $endDate),
            'finans_performansi' => $this->getFinansPerformansi($startDate, $endDate),
            'musteri_performansi' => $this->getMusteriPerformansi($startDate, $endDate)
        ];
    }

    /**
     * İlan performansı
     */
    protected function getIlanPerformansi(string $startDate, string $endDate): array
    {
        return [
            'toplam_ilan' => Ilan::whereBetween('created_at', [$startDate, $endDate])->count(),
            'status_ilan' => Ilan::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'active')->count(),
            'ortalama_fiyat' => Ilan::whereBetween('created_at', [$startDate, $endDate])->avg('fiyat'),
            'toplam_goruntulenme' => Ilan::whereBetween('created_at', [$startDate, $endDate])->sum('goruntulenme_sayisi')
        ];
    }

    /**
     * Satış performansı
     */
    protected function getSatisPerformansi(string $startDate, string $endDate): array
    {
        return [
            'toplam_satis' => CRMSatis::whereBetween('created_at', [$startDate, $endDate])->count(),
            'toplam_tutar' => CRMSatis::whereBetween('created_at', [$startDate, $endDate])->sum('satis_tutari'),
            'ortalama_tutar' => CRMSatis::whereBetween('created_at', [$startDate, $endDate])->avg('satis_tutari'),
            'tamamlanan_satis' => CRMSatis::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'tamamlandi')->count()
        ];
    }

    /**
     * Finans performansı
     */
    protected function getFinansPerformansi(string $startDate, string $endDate): array
    {
        return [
            'toplam_gelir' => FinansTransaction::gelir()->onaylanmis()
                ->whereBetween('islem_tarihi', [$startDate, $endDate])->sum('tl_tutar'),
            'toplam_gider' => FinansTransaction::gider()->onaylanmis()
                ->whereBetween('islem_tarihi', [$startDate, $endDate])->sum('tl_tutar'),
            'net_kar' => FinansTransaction::gelir()->onaylanmis()
                ->whereBetween('islem_tarihi', [$startDate, $endDate])->sum('tl_tutar') -
                FinansTransaction::gider()->onaylanmis()
                ->whereBetween('islem_tarihi', [$startDate, $endDate])->sum('tl_tutar')
        ];
    }

    /**
     * Müşteri performansı
     */
    protected function getMusteriPerformansi(string $startDate, string $endDate): array
    {
        return [
            'yeni_musteri' => Musteri::whereBetween('created_at', [$startDate, $endDate])->count(),
            'status_musteri' => Musteri::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', true)->count()
        ];
    }
}
