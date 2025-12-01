<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MusteriEtiket Model - DEPRECATED
 *
 * Context7 Compliance: Use KisiEtiket instead
 *
 * @deprecated Use App\Models\KisiEtiket instead (Context7 compliance)
 * @see App\Models\KisiEtiket
 *
 * This is an alias for backward compatibility.
 * New code should use KisiEtiket.
 *
 * Migration Guide:
 * 1. Replace: use App\Models\MusteriEtiket → use App\Models\KisiEtiket
 * 2. Replace: MusteriEtiket::class → KisiEtiket::class
 * 3. Database: RENAME TABLE musteri_etiketler TO kisi_etiketler (if not already renamed)
 */
class MusteriEtiket extends KisiEtiket
{
    use HasFactory;

    // ✅ Context7: Inherits everything from KisiEtiket
    // This model now points to etiketler table (via parent)

    /**
     * Override table name to maintain backward compatibility
     * Until migration is complete
     *
     * @var string
     */
    protected $table = 'musteri_etiketler'; // ⚠️ Deprecated table, use 'etiketler' (KisiEtiket)
}
