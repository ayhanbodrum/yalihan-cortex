<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Etiket extends Model
{
    use SoftDeletes;

    protected $table = 'etiketler';

    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
        'status',
        'display_order', // Context7: order → display_order (migration: 2025_11_11_103353)
        'type',
        'icon',
        'bg_color',
        'badge_text',
        'is_badge',
        'target_url',
    ];

    protected $casts = [
        'status' => 'boolean',
        'display_order' => 'integer', // Context7: order → display_order
        'is_badge' => 'boolean',
    ];

    // ✅ Context7: Eski Türkçe kolon isimleri için accessor/mutator
    // Veritabanında: ad, renk, aciklama, sira
    // Context7 Standard: name, color, description, order

    protected $appends = [];

    // name → ad mapping
    public function getNameAttribute($value)
    {
        return $this->attributes['ad'] ?? $value;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['ad'] = $value;
    }

    // color → renk mapping
    public function getColorAttribute($value)
    {
        return $this->attributes['renk'] ?? $value;
    }

    public function setColorAttribute($value)
    {
        $this->attributes['renk'] = $value;
    }

    // description → aciklama mapping
    public function getDescriptionAttribute($value)
    {
        return $this->attributes['aciklama'] ?? $value;
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['aciklama'] = $value;
    }

    // Context7: display_order accessor/mutator (backward compatibility için sira mapping korunuyor)
    public function getDisplayOrderAttribute($value)
    {
        return $this->attributes['display_order'] ?? $this->attributes['sira'] ?? $value;
    }

    public function setDisplayOrderAttribute($value)
    {
        $this->attributes['display_order'] = $value;
        // Backward compatibility: sira'yı da güncelle
        if (isset($this->attributes['sira'])) {
            $this->attributes['sira'] = $value;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($etiket) {
            if (empty($etiket->slug)) {
                $baseSlug = \Illuminate\Support\Str::slug($etiket->name);
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$counter;
                    $counter++;
                }

                $etiket->slug = $slug;
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('ad'); // Context7: order → display_order
    }

    public function scopeBadges($query)
    {
        return $query->where('is_badge', true)
            ->where('status', true)
            ->orderBy('display_order'); // Context7: order → display_order
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function ilanlar()
    {
        return $this->belongsToMany(Ilan::class, 'ilan_etiketler')
            ->withPivot(['display_order', 'is_featured'])
            ->withTimestamps();
    }
}
