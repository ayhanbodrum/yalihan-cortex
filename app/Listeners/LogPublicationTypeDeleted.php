<?php

namespace App\Listeners;

use App\Events\PublicationTypeDeleted;
use App\Services\CrmBridge;
use Illuminate\Support\Facades\Cache;

class LogPublicationTypeDeleted
{
    public function handle(PublicationTypeDeleted $event): void
    {
        app(CrmBridge::class)->recordPublicationTypeDeleted(
            $event->kategoriId,
            $event->yayinTipiId
        );

        $key = 'crm:publication_type_changes';
        $list = Cache::get($key, []);
        $list[] = [
            'type' => 'deleted',
            'kategori_id' => $event->kategoriId,
            'deleted_id' => $event->yayinTipiId,
            'ts' => now()->toIso8601String(),
        ];
        if (count($list) > 100) {
            $list = array_slice($list, -100);
        }
        Cache::put($key, $list, now()->addHours(12));
    }
}