<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class CrmBridge
{
    public function recordPublicationTypeReassigned(int $kategoriId, int $fromId, int $toId, int $affected): void
    {
        Log::channel('crm')->info('publication_type_reassigned', [
            'kategori_id' => $kategoriId,
            'from_yayin_tipi_id' => $fromId,
            'to_yayin_tipi_id' => $toId,
            'affected_ilan_count' => $affected,
        ]);
    }

    public function recordPublicationTypeDeleted(int $kategoriId, int $yayinTipiId): void
    {
        Log::channel('crm')->info('publication_type_deleted', [
            'kategori_id' => $kategoriId,
            'yayin_tipi_id' => $yayinTipiId,
        ]);
    }
}
