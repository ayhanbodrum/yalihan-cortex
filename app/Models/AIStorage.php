<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AI Storage Modeli
 *
 * Flexible storage sistemi için AI verilerini saklar
 * Context7 Compliant
 */
class AIStorage extends Model
{
    use HasFactory;

    protected $table = 'ai_storage';

    protected $fillable = [
        'storage_key',
        'data',
        'type',
        'context',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public $timestamps = true;

    /**
     * Storage key'e göre bul
     */
    public static function findByKey($key)
    {
        return static::where('storage_key', $key)->first();
    }

    /**
     * Belirli prefix ile başlayan kayıtlar
     */
    public function scopeByPrefix($query, $prefix)
    {
        return $query->where('storage_key', 'like', $prefix.'%');
    }

    /**
     * Belirli type için kayıtlar
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
