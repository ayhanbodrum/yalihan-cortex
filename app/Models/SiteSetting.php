<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'one_cikan', // Context7: status → one_cikan
        'description',
    ];

    protected $casts = [
        'one_cikan' => 'boolean', // Context7: status → one_cikan
    ];

    public static function getValue($key, $default = null)
    {
        return Cache::remember('setting.'.$key, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }

    public static function setValue($key, $value, $group = 'general')
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => is_bool($value) ? 'boolean' : (is_numeric($value) ? 'number' : 'text'),
            ]
        );

        Cache::forget('setting.'.$key);

        return $setting;
    }

    public static function getGroup($group)
    {
        return static::where('group', $group)->get();
    }

    public static function getPublicSettings()
    {
        return Cache::remember('public_settings', 3600, function () {
            return static::where('is_public', true)->get();
        });
    }
}
