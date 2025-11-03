<?php

namespace App\Modules\Crm\Services;

use App\Modules\Auth\Models\User;
use App\Modules\Crm\Models\Aktivite;
use App\Modules\Crm\Models\Kisi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AktiviteService
{
    /**
     * Yeni bir aktivite oluşturur.
     */
    public function createAktivite(array $data): Aktivite
    {
        Log::info('Yeni aktivite oluşturuluyor.', $data);

        return Aktivite::create($data);
    }

    /**
     * Mevcut bir aktiviteyi günceller.
     */
    public function updateAktivite(Aktivite $aktivite, array $data): Aktivite
    {
        Log::info("{$aktivite->id} ID'li aktivite güncelleniyor.", $data);
        $aktivite->update($data);

        return $aktivite;
    }

    /**
     * Bir aktiviteyi siler.
     */
    public function deleteAktivite(Aktivite $aktivite): ?bool
    {
        Log::info("{$aktivite->id} ID'li aktivite siliniyor.");

        return $aktivite->delete();
    }

    /**
     * ID ile bir aktiviteyi bulur.
     */
    public function getAktiviteById(int $id): ?Aktivite
    {
        return Aktivite::find($id);
    }

    /**
     * Tüm aktiviteleri listeler.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllAktiviteler(array $filters = [], int $paginate = 15)
    {
        $query = Aktivite::with(['kisi', 'danisman']);

        if (isset($filters['kisi_id'])) {
            $query->where('kisi_id', $filters['kisi_id']);
        }

        if (isset($filters['danisman_id'])) {
            $query->where('danisman_id', $filters['danisman_id']);
        }

        if (isset($filters['tip'])) {
            $query->where('tip', $filters['tip']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['baslangic_tarihi_baslangic'])) {
            $query->where('baslangic_tarihi', '>=', Carbon::parse($filters['baslangic_tarihi_baslangic']));
        }

        if (isset($filters['baslangic_tarihi_bitis'])) {
            $query->where('baslangic_tarihi', '<=', Carbon::parse($filters['baslangic_tarihi_bitis']));
        }

        return $query->orderBy('baslangic_tarihi', 'desc')->paginate($paginate);
    }

    /**
     * Bir kişiye ait aktiviteleri listeler.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAktivitelerByKisi(Kisi $kisi, int $paginate = 15)
    {
        return $this->getAllAktiviteler(['kisi_id' => $kisi->id], $paginate);
    }

    /**
     * Bir danışmana ait aktiviteleri listeler.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAktivitelerByDanisman(User $danisman, int $paginate = 15)
    {
        return $this->getAllAktiviteler(['danisman_id' => $danisman->id], $paginate);
    }

    /**
     * Aktivite tiplerini döndürür.
     */
    public static function getAktiviteTipleri(): array
    {
        return [
            Aktivite::TIP_GORUSME => 'Görüşme',
            Aktivite::TIP_RANDEVU => 'Randevu',
            Aktivite::TIP_ARAMA => 'Arama',
            Aktivite::TIP_EMAIL => 'E-posta',
            Aktivite::TIP_DIGER => 'Diğer',
        ];
    }

    /**
     * Aktivite statuslarını döndürür.
     */
    public static function getAktiviteDurumlari(): array
    {
        return [
            Aktivite::DURUM_BEKLEMEDE => 'Beklemede',
            Aktivite::DURUM_TAMAMLANDI => 'Tamamlandı',
            Aktivite::DURUM_IPTAL => 'İptal',
        ];
    }
}
