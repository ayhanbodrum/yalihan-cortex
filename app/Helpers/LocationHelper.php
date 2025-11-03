<?php

namespace App\Helpers;

class LocationHelper
{
    /**
     * Get all provinces for a country
     */
    public static function getProvinces($country = 'turkey')
    {
        return array_keys(config('locations.' . $country, []));
    }

    /**
     * Get all districts for a province
     */
    public static function getDistricts($province, $country = 'turkey')
    {
        return array_keys(config('locations.' . $country . '.' . $province, []));
    }

    /**
     * Get all neighborhoods for a district in a province
     */
    public static function getNeighborhoods($province, $district, $country = 'turkey')
    {
        return config('locations.' . $country . '.' . $province . '.' . $district, []);
    }

    /**
     * Get full location data as JSON
     */
    public static function getAllLocations($country = 'turkey')
    {
        return config('locations.' . $country, []);
    }

    /**
     * Get formatted province name
     */
    public static function formatProvinceName($province)
    {
        return ucwords(str_replace('_', ' ', $province));
    }

    /**
     * Get formatted district name
     */
    public static function formatDistrictName($district)
    {
        return ucwords(str_replace('_', ' ', $district));
    }

    /**
     * Get formatted neighborhood name
     */
    public static function formatNeighborhoodName($neighborhood)
    {
        return ucwords(str_replace('_', ' ', $neighborhood));
    }
}
