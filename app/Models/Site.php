<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SoftDeletes;

    protected $table = 'sites';

    // ✅ Context7 uyumlu fillable alanlar (tablo yapısına göre)
    protected $fillable = [
        'name',
        'address',
        'description',
        'status',
        'il_id',
        'ilce_id',
        'mahalle_id',
        'lat',
        'lng'
    ];

    // ✅ Context7 uyumlu casts
    protected $casts = [
        'lat' => 'string',
        'lng' => 'string',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ✅ Context7 uyumlu ilişkiler
    public function il()
    {
        return $this->belongsTo(Il::class, 'il_id');
    }

    public function ilce()
    {
        return $this->belongsTo(Ilce::class, 'ilce_id');
    }

    public function mahalle()
    {
        return $this->belongsTo(Mahalle::class, 'mahalle_id');
    }

    // ✅ Context7 uyumlu scopes
    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }

    public function scopePasif($query)
    {
        return $query->where('status', false);
    }

    public function scopeByIl($query, $ilId)
    {
        return $query->where('il_id', $ilId);
    }

    public function scopeByIlce($query, $ilceId)
    {
        return $query->where('ilce_id', $ilceId);
    }

    // ✅ Context7 uyumlu accessor
    public function getTamAdAttribute(): string
    {
        return $this->name . ($this->address ? ' - ' . $this->address : '');
    }

    // ✅ Context7 uyumlu helper methods
    public function getLocationTextAttribute(): string
    {
        $parts = [];

        if ($this->il) {
            $parts[] = $this->il->name;
        }
        if ($this->ilce) {
            $parts[] = $this->ilce->name;
        }
        if ($this->mahalle) {
            $parts[] = $this->mahalle->name;
        }

        return implode(' / ', $parts);
    }

    public function isActive(): bool
    {
        return (bool) $this->status;
    }
}
