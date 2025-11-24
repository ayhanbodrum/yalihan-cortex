<?php

namespace App\Listeners;

use App\Events\PublicationTypeReassigned;
use App\Services\CrmBridge;
use Illuminate\Support\Facades\Cache;

class LogPublicationTypeReassigned
{
    public function handle(PublicationTypeReassigned $event): void
    {
        app(CrmBridge::class)->recordPublicationTypeReassigned(
            $event->kategoriId,
            $event->fromYayinTipiId,
            $event->toYayinTipiId,
            $event->affectedCount
        );

        $key = 'crm:publication_type_changes';
        $list = Cache::get($key, []);
        $list[] = [
            'type' => 'reassigned',
            'kategori_id' => $event->kategoriId,
            'from_id' => $event->fromYayinTipiId,
            'to_id' => $event->toYayinTipiId,
            'affected' => $event->affectedCount,
            'ts' => now()->toIso8601String(),
        ];
        if (count($list) > 100) {
            $list = array_slice($list, -100);
        }
        Cache::put($key, $list, now()->addHours(12));
    }
}