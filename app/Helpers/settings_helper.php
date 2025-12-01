<?php

/**
 * Settings Helper Functions
 * Context7: Global helper functions for easy setting access
 */

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Get setting value
     *
     * @param  string  $key  Setting key
     * @param  mixed  $default  Default value
     * @return mixed
     */
    function setting($key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('setting_set')) {
    /**
     * Set setting value
     *
     * @param  string  $key  Setting key
     * @param  mixed  $value  Setting value
     * @param  string  $group  Setting group
     * @param  string|null  $type  Value type
     * @param  string|null  $description  Description
     * @return Setting
     */
    function setting_set($key, $value, $group = 'general', $type = null, $description = null)
    {
        return Setting::set($key, $value, $group, $type, $description);
    }
}

if (! function_exists('setting_group')) {
    /**
     * Get settings by group
     *
     * @param  string  $group  Group name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function setting_group($group)
    {
        return Setting::getByGroup($group);
    }
}

if (! function_exists('setting_groups')) {
    /**
     * Get all groups with counts
     *
     * @return array
     */
    function setting_groups()
    {
        return Setting::getGroups();
    }
}
