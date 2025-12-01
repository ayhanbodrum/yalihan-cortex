<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MusteriNot Model - DEPRECATED
 *
 * Context7 Compliance: Use KisiNot instead
 *
 * @deprecated Use App\Models\KisiNot instead (Context7 compliance)
 * @see App\Models\KisiNot
 *
 * This is an alias for backward compatibility.
 * New code should use KisiNot.
 *
 * Migration Guide:
 * 1. Replace: use App\Models\MusteriNot → use App\Models\KisiNot
 * 2. Replace: MusteriNot::class → KisiNot::class
 * 3. Database: RENAME TABLE musteri_notlar TO kisi_notlar (if not already)
 */
class MusteriNot extends KisiNot
{
    use HasFactory;

    // ✅ Context7: Inherits everything from KisiNot
    // This model now points to kisi_notlar table (via parent)

    /**
     * Override table name to maintain backward compatibility
     * Context7: Table renamed to kisi_notlar via migration (2025_11_06_230100)
     *
     * @var string
     */
    protected $table = 'kisi_notlar'; // Context7: Table renamed from old name to kisi_notlar (migration completed)
}
