<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * MusteriTakip Model - DEPRECATED
 *
 * Context7 Compliance: Use KisiTakip instead
 *
 * @deprecated Use App\Models\KisiTakip instead (Context7 compliance)
 * @see App\Models\KisiTakip
 *
 * This is an alias for backward compatibility.
 * New code should use KisiTakip.
 *
 * Migration Guide:
 * 1. Replace: use App\Models\MusteriTakip → use App\Models\KisiTakip
 * 2. Replace: MusteriTakip::class → KisiTakip::class
 * 3. Database: RENAME TABLE musteri_takip TO kisi_takip
 *
 * @property int $id
 * @property int $kisi_id
 * @property int $danisman_id
 * @property string $takip_tipi
 * @property string|null $notlar
 * @property \Carbon\Carbon|null $son_takip_tarihi
 * @property \Carbon\Carbon|null $sonraki_takip_tarihi
 * @property string $oncelik
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class MusteriTakip extends KisiTakip
{
    use HasFactory, SoftDeletes;

    // ✅ Context7: Inherits everything from KisiTakip
    // This model now points to 'kisi_takip' table (via parent)

    /**
     * Override table name to maintain backward compatibility
     * Until migration is complete
     *
     * @var string
     */
    protected $table = 'musteri_takip'; // ⚠️ Will be renamed to kisi_takip
}
