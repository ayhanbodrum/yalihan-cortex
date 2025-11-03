<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LanguageSetting extends Model
{
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'status',
        'is_default',
        'direction',
        'date_format',
        'custom_settings',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_default' => 'boolean',
        'custom_settings' => 'json',
    ];

    public static function getActive()
    {
        return Cache::remember('active_languages', 3600, function () {
            return static::where('status', true)->get();
        });
    }

    public static function getDefault()
    {
        return Cache::remember('default_language', 3600, function () {
            return static::where('is_default', true)->first();
        });
    }

    public static function setDefault($code)
    {
        static::where('is_default', true)->update(['is_default' => false]);
        static::where('code', $code)->update(['is_default' => true]);

        Cache::forget('default_language');
        Cache::forget('active_languages');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
