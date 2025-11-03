<?php

namespace App\Jobs;

use App\Modules\Emlak\Models\Ilan;
use App\Services\Location\NearbyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class FetchNearbyForIlanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $ilanId;

    public int $radius;

    public ?array $categories;

    public function __construct(int $ilanId, int $radius = 1000, ?array $categories = null)
    {
        $this->ilanId = $ilanId;
        $this->radius = $radius;
        $this->categories = $categories;
    }

    public function handle(): void
    {
        $ilan = Ilan::find($this->ilanId);
        if (! $ilan) {
            return;
        }
        $lat = $ilan->latitude ?? $ilan->konum_lat;
        $lng = $ilan->longitude ?? $ilan->konum_lng;
        if (! $lat || ! $lng) {
            return;
        }

        /** @var NearbyService $service */
        $service = app(NearbyService::class);
        $data = $service->fetchNearby((float) $lat, (float) $lng, $this->radius, $this->categories);

        DB::table('ilan_nearby')->updateOrInsert(
            ['ilan_id' => $ilan->id],
            [
                'summary' => json_encode($data['summary'] ?? []),
                'items' => json_encode($data['items'] ?? []),
                'fetched_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
