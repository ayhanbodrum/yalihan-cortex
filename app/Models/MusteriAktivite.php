<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MusteriAktivite Model - DEPRECATED
 *
 * Context7 Compliance: Use KisiAktivite instead
 *
 * @deprecated Use App\Models\KisiAktivite instead (Context7 compliance)
 * @see App\Models\KisiAktivite
 *
 * This is an alias for backward compatibility.
 * New code should use KisiAktivite.
 *
 * Migration Guide:
 * 1. Replace: use App\Models\MusteriAktivite → use App\Models\KisiAktivite
 * 2. Replace: MusteriAktivite::class → KisiAktivite::class
 * 3. Database: RENAME TABLE musteri_aktiviteler TO kisi_aktiviteler
 */
class MusteriAktivite extends KisiAktivite
{
    use HasFactory;

    // ✅ Context7: Inherits everything from KisiAktivite
    // This model now points to 'kisi_aktiviteler' table (via parent)

    /**
     * Override table name to maintain backward compatibility
     * Until migration is complete
     *
     * @var string
     */
    protected $table = 'musteri_aktiviteler'; // ⚠️ Will be renamed to kisi_aktiviteler
}
