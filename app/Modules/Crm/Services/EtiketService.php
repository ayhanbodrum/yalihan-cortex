<?php

namespace App\Modules\Crm\Services;

use App\Models\Kisi;
use App\Modules\Crm\Models\Etiket;
use Illuminate\Support\Facades\Log;

class EtiketService
{
    /**
     * Yeni bir etiket oluşturur.
     */
    public function createEtiket(array $data): Etiket
    {
        Log::info('Yeni etiket oluşturuluyor.', $data);

        return Etiket::create($data);
    }

    /**
     * Mevcut bir etiketi günceller.
     */
    public function updateEtiket(Etiket $etiket, array $data): Etiket
    {
        Log::info("{$etiket->id} ID'li etiket güncelleniyor.", $data);
        $etiket->update($data);

        return $etiket;
    }

    /**
     * Bir etiketi siler.
     */
    public function deleteEtiket(Etiket $etiket): ?bool
    {
        Log::info("{$etiket->id} ID'li etiket siliniyor.");

        return $etiket->delete();
    }

    /**
     * ID ile bir etiketi bulur.
     */
    public function getEtiketById(int $id): ?Etiket
    {
        return Etiket::find($id);
    }

    /**
     * Tüm etiketleri listeler.
     *
     * @return \Illuminate\Database\Eloquent\Collection|Etiket[]
     */
    public function getAllEtiketler()
    {
        return Etiket::all();
    }

    /**
     * Bir kişiye etiket atar.
     */
    public function attachEtiketToKisi(Kisi $kisi, Etiket $etiket): void
    {
        Log::info("{$kisi->id} ID'li kişiye {$etiket->id} ID'li etiket atanıyor.");
        $kisi->etiketler()->attach($etiket->id);
    }

    /**
     * Bir kişiden etiketi kaldırır.
     */
    public function detachEtiketFromKisi(Kisi $kisi, Etiket $etiket): void
    {
        Log::info("{$kisi->id} ID'li kişiden {$etiket->id} ID'li etiket kaldırılıyor.");
        $kisi->etiketler()->detach($etiket->id);
    }

    /**
     * Bir kişinin etiketlerini senkronize eder.
     */
    public function syncEtiketlerForKisi(Kisi $kisi, array $etiketIds): void
    {
        Log::info("{$kisi->id} ID'li kişinin etiketleri senkronize ediliyor.", ['etiket_ids' => $etiketIds]);
        $kisi->etiketler()->sync($etiketIds);
    }
}
