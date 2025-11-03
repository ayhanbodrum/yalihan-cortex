<?php

namespace App\Modules\Finans\Services;

use App\Modules\Finans\Models\FinansalIslem;
use App\Modules\Finans\Models\Komisyon;
use App\Modules\Emlak\Models\Ilan;
use App\Modules\Crm\Models\Musteri;
use App\Modules\TakimYonetimi\Models\Gorev;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FinansalIslemService
{
    /**
     * Finansal işlem oluştur
     */
    public function createFinansalIslem(array $data): FinansalIslem
    {
        return DB::transaction(function () use ($data) {
            // Referans numarası oluştur
            if (!isset($data['referans_no'])) {
                $data['referans_no'] = 'FIN-' . date('Y') . '-' . str_pad(FinansalIslem::count() + 1, 6, '0', STR_PAD_LEFT);
            }

            return FinansalIslem::create($data);
        });
    }

    /**
     * Komisyon oluştur
     */
    public function createKomisyon(array $data): Komisyon
    {
        return DB::transaction(function () use ($data) {
            $komisyon = Komisyon::create($data);
            $komisyon->hesaplaKomisyon();
            return $komisyon;
        });
    }

    /**
     * İlan satışı için komisyon hesapla
     */
    public function hesaplaIlanSatisKomisyonu(Ilan $ilan, Musteri $musteri, $danismanId): Komisyon
    {
        return $this->createKomisyon([
            'ilan_id' => $ilan->id,
            'musteri_id' => $musteri->id,
            'danisman_id' => $danismanId,
            'komisyon_tipi' => 'satis',
            'ilan_fiyati' => $ilan->fiyat,
            'para_birimi' => $ilan->para_birimi,
        ]);
    }

    /**
     * İlan kiralama için komisyon hesapla
     */
    public function hesaplaIlanKiralamaKomisyonu(Ilan $ilan, Musteri $musteri, $danismanId): Komisyon
    {
        return $this->createKomisyon([
            'ilan_id' => $ilan->id,
            'musteri_id' => $musteri->id,
            'danisman_id' => $danismanId,
            'komisyon_tipi' => 'kiralama',
            'ilan_fiyati' => $ilan->fiyat,
            'para_birimi' => $ilan->para_birimi,
        ]);
    }

    /**
     * Danışmanlık komisyonu hesapla
     */
    public function hesaplaDanismanlikKomisyonu(Gorev $gorev, $danismanId, $tutar): Komisyon
    {
        return $this->createKomisyon([
            'gorev_id' => $gorev->id,
            'danisman_id' => $danismanId,
            'komisyon_tipi' => 'danismanlik',
            'ilan_fiyati' => $tutar,
            'para_birimi' => 'TRY',
        ]);
    }

    /**
     * Finansal işlem onayla
     */
    public function onaylaFinansalIslem(FinansalIslem $islem, $onaylayanId): bool
    {
        return $islem->onayla($onaylayanId);
    }

    /**
     * Finansal işlem reddet
     */
    public function reddetFinansalIslem(FinansalIslem $islem, $onaylayanId, $not = null): bool
    {
        return $islem->reddet($onaylayanId, $not);
    }

    /**
     * Komisyon onayla
     */
    public function onaylaKomisyon(Komisyon $komisyon): bool
    {
        return $komisyon->onayla();
    }

    /**
     * Komisyon öde
     */
    public function odeKomisyon(Komisyon $komisyon): bool
    {
        return $komisyon->ode();
    }

    /**
     * Finansal işlem istatistikleri
     */
    public function getFinansalIslemStats(): array
    {
        return [
            'toplam_islem' => FinansalIslem::count(),
            'bekleyen_islem' => FinansalIslem::bekleyen()->count(),
            'onaylanan_islem' => FinansalIslem::onaylanan()->count(),
            'tamamlanan_islem' => FinansalIslem::tamamlanan()->count(),
            'toplam_komisyon' => Komisyon::sum('komisyon_tutari'),
            'odenmeyen_komisyon' => Komisyon::where('status', '!=', 'odendi')->sum('komisyon_tutari'),
        ];
    }

    /**
     * Danışman komisyon raporu
     */
    public function getDanismanKomisyonRaporu($danismanId, $tarihAraligi = 30): array
    {
        $baslangicTarihi = now()->subDays($tarihAraligi);

        return [
            'toplam_komisyon' => Komisyon::where('danisman_id', $danismanId)
                ->where('hesaplama_tarihi', '>=', $baslangicTarihi)
                ->sum('komisyon_tutari'),
            'odenen_komisyon' => Komisyon::where('danisman_id', $danismanId)
                ->where('status', 'odendi')
                ->where('hesaplama_tarihi', '>=', $baslangicTarihi)
                ->sum('komisyon_tutari'),
            'bekleyen_komisyon' => Komisyon::where('danisman_id', $danismanId)
                ->whereIn('status', ['hesaplandi', 'onaylandi'])
                ->where('hesaplama_tarihi', '>=', $baslangicTarihi)
                ->sum('komisyon_tutari'),
            'komisyon_sayisi' => Komisyon::where('danisman_id', $danismanId)
                ->where('hesaplama_tarihi', '>=', $baslangicTarihi)
                ->count(),
        ];
    }

    /**
     * Aylık finansal rapor
     */
    public function getAylikFinansalRapor($ay, $yil): array
    {
        $baslangicTarihi = "{$yil}-{$ay}-01";
        $bitisTarihi = "{$yil}-{$ay}-31";

        return [
            'toplam_gelir' => FinansalIslem::where('islem_tipi', 'gelir')
                ->whereBetween('tarih', [$baslangicTarihi, $bitisTarihi])
                ->sum('miktar'),
            'toplam_gider' => FinansalIslem::where('islem_tipi', 'gider')
                ->whereBetween('tarih', [$baslangicTarihi, $bitisTarihi])
                ->sum('miktar'),
            'toplam_komisyon' => Komisyon::whereBetween('hesaplama_tarihi', [$baslangicTarihi, $bitisTarihi])
                ->sum('komisyon_tutari'),
            'net_kar' => 0, // Hesaplanacak
        ];
    }
}
