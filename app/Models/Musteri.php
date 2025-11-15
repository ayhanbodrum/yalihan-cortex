<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Musteri Model - DEPRECATED
 *
 * Context7 Compliance: This is a backward-compatibility alias for Kisi model
 *
 * @deprecated Use App\Models\Kisi instead
 * @see App\Models\Kisi
 *
 * This model extends Kisi and uses 'kisiler' table.
 * All Musteri references should be migrated to Kisi.
 */
class Musteri extends Kisi
{
    /**
     * ⚠️ DEPRECATED MODEL - Context7 Compliance
     *
     * This model is a backward-compatibility facade for Kisi model.
     *
     * Context7 Rule Violation: "musteri" → "kisi"
     * Database: Uses 'kisiler' table (NOT 'musteriler')
     *
     * Migration Guide:
     * - Replace: use App\Models\Musteri; → use App\Models\Kisi;
     * - Replace: Musteri::find() → Kisi::find()
     * - Replace: $musteri → $kisi
     * - Replace: route('admin.musteriler.*') → route('admin.kisiler.*')
     *
     * All functionality is inherited from Kisi model.
     * This class exists only for backward compatibility.
     *
     * @deprecated Since Context7 v3.6.0 (Nov 6, 2025)
     * @see \App\Models\Kisi Use Kisi model instead
     */

    // No additional code needed - everything inherited from Kisi!
    // This is purely a facade/alias for backward compatibility.
}
