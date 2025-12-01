<?php

namespace App\Modules\Crm\Services;

use App\Models\Kisi;
use Illuminate\Support\Facades\Log;

class KisiService
{
    /**
     * Yeni bir kişi oluşturur.
     * ✅ Context7: kisi_tipi required field kontrolü eklendi
     */
    public function createKisi(array $data): Kisi
    {
        Log::info('Yeni kişi oluşturuluyor.', $data);

        // ✅ Context7: kisi_tipi default değer ataması (eğer null ise)
        if (empty($data['kisi_tipi'])) {
            $data['kisi_tipi'] = 'Müşteri'; // Default değer
            Log::warning('kisi_tipi boş, default değer atandı: Müşteri', $data);
        }

        return Kisi::create($data);
    }

    /**
     * Mevcut bir kişiyi günceller.
     */
    public function updateKisi(Kisi $kisi, array $data): Kisi
    {
        Log::info("{$kisi->id} ID'li kişi güncelleniyor.", $data);
        $kisi->update($data);

        return $kisi;
    }

    /**
     * Bir kişiyi siler.
     */
    public function deleteKisi(Kisi $kisi): ?bool
    {
        Log::info("{$kisi->id} ID'li kişi siliniyor.");

        return $kisi->delete();
    }

    /**
     * ID ile bir kişiyi bulur.
     */
    public function getKisiById(int $id): ?Kisi
    {
        return Kisi::find($id);
    }

    /**
     * Tüm kişileri listeler.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllKisiler(array $filters = [], int $paginate = 15)
    {
        $query = Kisi::query();

        // Search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(ad, ' ', soyad) LIKE ?", ["%{$search}%"])
                    ->orWhere('telefon', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Danisman filter
        if (! empty($filters['danisman_id'])) {
            $query->where('danisman_id', $filters['danisman_id']);
        }

        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Limit for API responses
        if (! empty($filters['limit'])) {
            return $query->orderBy('created_at', 'desc')
                ->limit($filters['limit'])
                ->get();
        }

        return $query->orderBy('created_at', 'desc')->paginate($paginate);
    }

    /**
     * Kişi arama - API için optimize edilmiş
     * Context7 & Yalıhan Bekçi: Standart kişi arama metodu
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function search(string $searchTerm, int $limit = 10)
    {
        // ✅ Context7 & Yalıhan Bekçi: Standart kişi sorgusu
        // 1. Status kontrolü: Sadece aktif kişiler (status = 'Aktif')
        // 2. Select optimization: Sadece gerekli kolonlar
        // 3. Sıralama: İsim sırasına göre (orderBy tam_ad)
        return Kisi::select(['id', 'ad', 'soyad', 'telefon', 'email', 'kisi_tipi', 'status'])
            ->where('status', 'Aktif') // ✅ Context7: Sadece aktif kişiler
            ->where(function ($query) use ($searchTerm) {
                $query->whereRaw("CONCAT(ad, ' ', soyad) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('telefon', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            })
            ->orderByRaw("CONCAT(ad, ' ', soyad)") // ✅ İsim sırasına göre
            ->limit($limit)
            ->get()
            ->map(function ($kisi) {
                // ✅ Context7: Response formatı standartlaştırıldı
                $tamAd = trim($kisi->ad.' '.$kisi->soyad);

                return [
                    'id' => $kisi->id,
                    'ad' => $kisi->ad,
                    'soyad' => $kisi->soyad,
                    'tam_ad' => $tamAd,
                    'telefon' => $kisi->telefon,
                    'email' => $kisi->email,
                    'kisi_tipi' => $kisi->kisi_tipi ?? null,
                    'text' => $tamAd.($kisi->telefon ? ' - '.$kisi->telefon : ''), // Context7 Live Search için
                ];
            })
            ->values() // ✅ Collection index'lerini sıfırla (array uyumluluğu)
            ->toArray(); // ✅ Context7: Array döndür (JavaScript uyumluluğu için)
    }

    /**
     * İlan sahibi olarak uygun kişileri getir
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPotentialOwners(?string $searchTerm = null, int $limit = 10)
    {
        $query = Kisi::where('status', 'Aktif');

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw("CONCAT(ad, ' ', soyad) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('telefon', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($kisi) {
                // İlan sahibi olarak uygunluk skoru hesapla
                $kisi->owner_score = self::calculateOwnerScore($kisi);

                return $kisi;
            })
            ->sortByDesc('owner_score');
    }

    /**
     * Kişinin ilan sahibi olarak uygunluk skorunu hesapla
     */
    public static function calculateOwnerScore(\App\Models\Kisi $kisi): int
    {
        $score = 0;

        // Temel bilgiler
        if ($kisi->ad && $kisi->soyad) {
            $score += 10;
        }
        if ($kisi->telefon) {
            $score += 5;
        }
        if ($kisi->email) {
            $score += 5;
        }

        // Kişi tipi (Context7: kisi_tipi preferred)
        $kisiTipi = $kisi->kisi_tipi ?? $kisi->musteri_tipi ?? null;
        if ($kisiTipi === 'Ev Sahibi' || $kisiTipi === 'ev_sahibi') {
            $score += 15;
        } elseif ($kisiTipi === 'Satıcı' || $kisiTipi === 'satici') {
            $score += 10;
        } elseif ($kisiTipi === 'Alıcı' || $kisiTipi === 'alici') {
            $score += 5;
        }

        // Aktiflik statusu
        if ($kisi->status === 'Aktif') {
            $score += 10;
        }

        // İletişim bilgileri
        if ($kisi->telefon && $kisi->email) {
            $score += 5;
        }

        return $score;
    }

    /**
     * Kişiyi ilan sahibi olarak işaretle
     */
    public static function markAsOwner(int $kisiId): bool
    {
        $kisi = Kisi::find($kisiId);
        if (! $kisi) {
            return false;
        }

        $kisi->update([
            'kisi_tipi' => 'Ev Sahibi',
            'status' => 'Aktif',
        ]);

        return true;
    }

    /**
     * Kişinin ilan sahibi geçmişini getir
     */
    public static function getOwnerHistory(int $kisiId): array
    {
        $kisi = Kisi::find($kisiId);
        if (! $kisi) {
            return [];
        }

        $ilanlar = \App\Models\Ilan::where('owner_id', $kisiId)
            ->with(['kategori', 'danisman'])
            ->get();

        return [
            'kisi' => $kisi,
            'ilanlar' => $ilanlar,
            'statistics' => [
                'total_listings' => $ilanlar->count(),
                'active_listings' => $ilanlar->where('status', 'active')->count(),
                'sold_listings' => $ilanlar->where('status', 'satildi')->count(),
                'total_value' => $ilanlar->sum('fiyat'),
                'average_price' => $ilanlar->avg('fiyat'),
                'categories' => $ilanlar->groupBy('kategori.name')->map->count(),
            ],
        ];
    }
}
