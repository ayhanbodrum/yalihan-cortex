<?php

namespace App\Services;

use App\Models\IlanKategori;
use App\Models\Kisi;
use App\Models\User;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use App\Models\Site;
use Illuminate\Support\Collection;

/**
 * İlan Data Provider Service
 *
 * Context7 Standardı: C7-ILAN-DATA-PROVIDER-2025-10-11
 * Amaç: Tüm ilan create/edit sayfaları için standart veri sağlayıcı
 *
 * Kullanım:
 * $dataProvider = app(IlanDataProviderService::class);
 * $data = $dataProvider->getStandardFormData();
 */
class IlanDataProviderService
{
    /**
     * Tüm ilan create/edit sayfaları için standart veri seti
     *
     * @return array
     */
    public function getStandardFormData(): array
    {
        return [
            'anaKategoriler' => $this->getAnaKategoriler(),
            'kisiler' => $this->getAktifKisiler(),
            'danismanlar' => $this->getDanismanlar(),
            'iller' => $this->getIller(),
        ];
    }

    /**
     * Ana kategorileri getir (parent_id = null)
     *
     * @return Collection
     */
    public function getAnaKategoriler(): Collection
    {
        return IlanKategori::whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name', 'icon', 'description']);
    }

    /**
     * Aktif kişileri getir (İlan sahipleri için)
     *
     * @return Collection
     */
    public function getAktifKisiler(): Collection
    {
        return Kisi::where('status', 'Aktif')
            ->orderBy('ad')
            ->orderBy('soyad')
            ->get(['id', 'ad', 'soyad', 'telefon', 'email']);
    }

    /**
     * Danışmanları getir (User modeli, danisman rolü)
     *
     * @return Collection
     */
    public function getDanismanlar(): Collection
    {
        return User::whereHas('roles', function($q) {
            $q->where('name', 'danisman');
        })
        ->where('status', 1)
        ->orderBy('name')
        ->get(['id', 'name', 'email']);
    }

    /**
     * İlleri getir
     *
     * @return Collection
     */
    public function getIller(): Collection
    {
        return Il::orderBy('il_adi')
            ->get(['id', 'il_adi']);
    }

    /**
     * Alt kategorileri getir (AJAX için)
     *
     * @param int $anaKategoriId
     * @return Collection
     */
    public function getAltKategoriler(int $anaKategoriId): Collection
    {
        return IlanKategori::where('parent_id', $anaKategoriId)
            ->where('seviye', 1)
            ->orderBy('order')
            ->orderBy('name')
            ->get(['id', 'name', 'icon', 'description']);
    }

    /**
     * Yayın tiplerini getir (AJAX için)
     *
     * @param int $altKategoriId
     * @return Collection
     */
    public function getYayinTipleri(int $altKategoriId): Collection
    {
        return IlanKategori::where('parent_id', $altKategoriId)
            ->where('seviye', 2)
            ->where('status', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get(['id', 'name', 'icon', 'description']);
    }

    /**
     * İlçeleri getir (AJAX için)
     *
     * @param int $ilId
     * @return Collection
     */
    public function getIlceler(int $ilId): Collection
    {
        return Ilce::where('il_id', $ilId)
            ->orderBy('ilce_adi')
            ->get(['id', 'il_id', 'ilce_adi']);
    }

    /**
     * Mahalleleri getir (AJAX için)
     *
     * @param int $ilceId
     * @return Collection
     */
    public function getMahalleler(int $ilceId): Collection
    {
        return Mahalle::where('ilce_id', $ilceId)
            ->orderBy('mahalle_adi')
            ->get(['id', 'ilce_id', 'mahalle_adi']);
    }

    /**
     * Aktif siteleri getir (Context7 Live Search için)
     *
     * @return Collection
     */
    public function getAktifSiteler(): Collection
    {
        return Site::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'il_id', 'ilce_id', 'mahalle_id']);
    }

    /**
     * İl bazında siteleri getir
     *
     * @param int $ilId
     * @return Collection
     */
    public function getSitelerByIl(int $ilId): Collection
    {
        return Site::where('active', true)
            ->where('il_id', $ilId)
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'il_id', 'ilce_id', 'mahalle_id']);
    }

    /**
     * İlçe bazında siteleri getir
     *
     * @param int $ilceId
     * @return Collection
     */
    public function getSitelerByIlce(int $ilceId): Collection
    {
        return Site::where('active', true)
            ->where('ilce_id', $ilceId)
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'il_id', 'ilce_id', 'mahalle_id']);
    }

    /**
     * Kategori bazlı özellikleri getir
     *
     * @param int|null $altKategoriId
     * @return Collection
     */
    public function getOzelliklerByKategori(?int $altKategoriId = null): Collection
    {
        if ($altKategoriId) {
            // Kategoriye özel özellikler
            return \App\Models\Feature::where('active', true)
                ->whereHas('categories', function($q) use ($altKategoriId) {
                    $q->where('ilan_kategori_id', $altKategoriId);
                })
                ->orderBy('display_order')
                ->get();
        }

        // Tüm özellikler
        return \App\Models\Feature::where('active', true)
            ->orderBy('display_order')
            ->get();
    }

    /**
     * İlan istatistikleri
     *
     * @param int|null $danismanId
     * @return array
     */
    public function getIstatistikler(?int $danismanId = null): array
    {
        $query = \App\Models\Ilan::query();

        if ($danismanId) {
            $query->where('danisman_id', $danismanId);
        }

        return [
            'toplam' => $query->count(),
            'aktif' => $query->where('status', 'Aktif')->count(),
            'taslak' => $query->where('status', 'Taslak')->count(),
            'satildi' => $query->where('status', 'Satıldı')->count(),
        ];
    }

    /**
     * Danışman bazlı ilanlar
     *
     * @param int $danismanId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getDanismanIlanlari(int $danismanId, int $perPage = 20)
    {
        return \App\Models\Ilan::with(['ilanSahibi', 'il', 'ilce', 'anaKategori'])
            ->where('danisman_id', $danismanId)
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Context7 uyumlu field mapping
     *
     * @return array
     */
    public function getFieldMapping(): array
    {
        return [
            'status_field' => 'status', // Context7: status yerine status
            'city_field' => 'il_id', // Context7: il yerine il_id
            'district_field' => 'ilce_id',
            'neighborhood_field' => 'mahalle_id',
            'category_field' => 'kategori_id', // Tek kolon (ana_kategori_id yok)
        ];
    }

    /**
     * Cache'li veri getirme (Performance optimization)
     *
     * @param string $key
     * @param callable $callback
     * @param int $minutes
     * @return mixed
     */
    protected function remember(string $key, callable $callback, int $minutes = 60)
    {
        if (config('cache.default') !== 'array') {
            return cache()->remember($key, now()->addMinutes($minutes), $callback);
        }

        return $callback();
    }

    /**
     * Cache'li ana kategoriler
     *
     * @return Collection
     */
    public function getCachedAnaKategoriler(): Collection
    {
        return $this->remember('ilan_ana_kategoriler', function() {
            return $this->getAnaKategoriler();
        }, 60);
    }

    /**
     * Cache'li iller
     *
     * @return Collection
     */
    public function getCachedIller(): Collection
    {
        return $this->remember('iller', function() {
            return $this->getIller();
        }, 120);
    }
}

