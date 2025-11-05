<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @deprecated This model is deprecated. Use Setting model instead.
 * SiteSetting has been merged into Setting model for Context7 compliance.
 * 
 * Migration guide:
 * - Replace SiteSetting::getValue() with Setting::get()
 * - Replace SiteSetting::setValue() with Setting::set()
 * - Replace SiteSetting::getGroup() with Setting::getByGroup()
 * 
 * All data should be migrated from site_settings table to settings table.
 * 
 * @see \App\Models\Setting
 */
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

    /**
     * @deprecated Use Setting::get() instead
     */
    public static function getValue($key, $default = null)
    {
        return Setting::get($key, $default);
    }

    /**
     * @deprecated Use Setting::set() instead
     */
    public static function setValue($key, $value, $group = 'general')
    {
        return Setting::set($key, $value, $group);
    }

    /**
     * @deprecated Use Setting::getByGroup() instead
     */
    public static function getGroup($group)
    {
        return Setting::getByGroup($group);
    }

    /**
     * @deprecated Use Setting::getByGroup('public') with appropriate filter instead
     */
    public static function getPublicSettings()
    {
        return Cache::remember('public_settings', 3600, function () {
            return Setting::where('group', 'public')->get();
        });
    }
}
