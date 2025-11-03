<?php

namespace App\Modules\Emlak\Services;

use App\Modules\Emlak\Models\Feature;
use App\Modules\Emlak\Models\Ilan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FeatureService
{
    /**
     * Feature oluştur
     */
    public function createFeature(array $data): Feature
    {
        return DB::transaction(function () use ($data) {
            return Feature::create($data);
        });
    }

    /**
     * Feature güncelle
     */
    public function updateFeature(Feature $feature, array $data): Feature
    {
        return DB::transaction(function () use ($feature, $data) {
            $feature->update($data);
            return $feature;
        });
    }

    /**
     * Feature sil
     */
    public function deleteFeature(Feature $feature): bool
    {
        return DB::transaction(function () use ($feature) {
            // İlişkili ilanları kontrol et
            if ($feature->ilanlar()->count() > 0) {
                throw new \Exception('Bu özellik kullanılan ilanlar var, silinemez.');
            }
            
            return $feature->delete();
        });
    }

    /**
     * İlan'a özellik ekle
     */
    public function attachFeatureToIlan(Ilan $ilan, Feature $feature, $deger = null): void
    {
        $ilan->ozellikler()->syncWithoutDetaching([
            $feature->id => [
                'deger' => $deger,
                'created_at' => now(),
            ]
        ]);
    }

    /**
     * İlan'dan özellik kaldır
     */
    public function detachFeatureFromIlan(Ilan $ilan, Feature $feature): void
    {
        $ilan->ozellikler()->detach($feature->id);
    }

    /**
     * İlan özelliklerini güncelle
     */
    public function updateIlanFeatures(Ilan $ilan, array $features): void
    {
        DB::transaction(function () use ($ilan, $features) {
            $syncData = [];
            
            foreach ($features as $featureId => $deger) {
                $syncData[$featureId] = [
                    'deger' => $deger,
                    'updated_at' => now(),
                ];
            }
            
            $ilan->ozellikler()->sync($syncData);
        });
    }

    /**
     * Kategoriye göre özellikleri getir
     */
    public function getFeaturesByCategory($kategori): Collection
    {
        return Feature::where('kategori', $kategori)
            ->where('status', 'status')
            ->orderBy('sira_no')
            ->get();
    }

    /**
     * İlan tipine göre özellikleri getir
     */
    public function getFeaturesByIlanType($ilanTipi): Collection
    {
        return Feature::where('ilan_tipi', $ilanTipi)
            ->where('status', 'status')
            ->orderBy('sira_no')
            ->get();
    }

    /**
     * Popüler özellikleri getir
     */
    public function getPopularFeatures($limit = 10): Collection
    {
        return Feature::withCount('ilanlar')
            ->where('status', 'status')
            ->orderBy('ilanlar_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Özellik istatistikleri
     */
    public function getFeatureStats(): array
    {
        return [
            'toplam_ozellik' => Feature::count(),
            'aktif_ozellik' => Feature::where('status', 'status')->count(),
            'pasif_ozellik' => Feature::where('status', 'inactive')->count(),
            'en_populer_ozellik' => Feature::withCount('ilanlar')
                ->orderBy('ilanlar_count', 'desc')
                ->first(),
            'kategori_dagilimi' => Feature::selectRaw('kategori, COUNT(*) as sayi')
                ->groupBy('kategori')
                ->get()
                ->pluck('sayi', 'kategori')
                ->toArray(),
        ];
    }

    /**
     * Özellik arama
     */
    public function searchFeatures($query): Collection
    {
        return Feature::where('status', 'status')
            ->where(function ($q) use ($query) {
                $q->where('ad', 'LIKE', "%{$query}%")
                  ->orWhere('aciklama', 'LIKE', "%{$query}%")
                  ->orWhere('kategori', 'LIKE', "%{$query}%");
            })
            ->orderBy('ad')
            ->get();
    }

    /**
     * Özellik gruplarını getir
     */
    public function getFeatureGroups(): array
    {
        return Feature::selectRaw('kategori, COUNT(*) as sayi')
            ->where('status', 'status')
            ->groupBy('kategori')
            ->orderBy('kategori')
            ->get()
            ->map(function ($item) {
                return [
                    'kategori' => $item->kategori,
                    'sayi' => $item->sayi,
                    'ozellikler' => Feature::where('kategori', $item->kategori)
                        ->where('status', 'status')
                        ->orderBy('sira_no')
                        ->get()
                ];
            })
            ->toArray();
    }

    /**
     * İlan özellik analizi
     */
    public function analyzeIlanFeatures(Ilan $ilan): array
    {
        $ozellikler = $ilan->ozellikler()->get();
        
        return [
            'toplam_ozellik' => $ozellikler->count(),
            'kategori_dagilimi' => $ozellikler->groupBy('kategori')->map->count(),
            'eksik_ozellikler' => $this->getMissingFeaturesForIlan($ilan),
            'fazla_ozellikler' => $this->getExcessiveFeaturesForIlan($ilan),
        ];
    }

    /**
     * İlan için eksik özellikleri getir
     */
    private function getMissingFeaturesForIlan(Ilan $ilan): Collection
    {
        $mevcutOzellikler = $ilan->ozellikler()->pluck('features.id');
        
        return Feature::where('ilan_tipi', $ilan->emlak_tipi)
            ->where('status', 'status')
            ->where('zorunlu', true)
            ->whereNotIn('id', $mevcutOzellikler)
            ->get();
    }

    /**
     * İlan için fazla özellikleri getir
     */
    private function getExcessiveFeaturesForIlan(Ilan $ilan): Collection
    {
        $mevcutOzellikler = $ilan->ozellikler()->pluck('features.id');
        
        return Feature::whereIn('id', $mevcutOzellikler)
            ->where('ilan_tipi', '!=', $ilan->emlak_tipi)
            ->where('status', 'status')
            ->get();
    }
}
