<?php

namespace App\Modules\Crm\Models;

use App\Modules\Emlak\Models\Ilan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Musteri Model - DEPRECATED
 *
 * Context7: This model is an alias for Kisi
 * Use App\Models\Kisi or App\Modules\Crm\Models\Kisi instead
 *
 * @deprecated Use Kisi model instead (Context7 compliance)
 * @see App\Modules\Crm\Models\Kisi
 * @see App\Models\Kisi
 */
class Musteri extends Kisi
{
    use HasFactory;

    // ✅ Context7: Inherits everything from Kisi
    // This model now points to 'kisiler' table (via parent)

    /**
     * Override table name for CRM module compatibility
     * Points to 'kisiler' table through parent Kisi model
     *
     * @var string
     */
    protected $table = 'kisiler'; // ✅ Context7: Using kisiler table
}
