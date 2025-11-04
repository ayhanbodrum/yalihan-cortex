<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * TurkiyeAPI Service
 * 
 * Türkiye'nin idari bölümleri (İl, İlçe, Mahalle, Belde, Köy)
 * API: https://api.turkiyeapi.dev/docs
 * 
 * Context7: Enhanced location data with towns (beldeler) and villages (köyler)
 */
class TurkiyeAPIService
{
    protected string $baseUrl = 'https://api.turkiyeapi.dev/api/v1';
    protected int $cacheTtl = 86400; // 24 saat
    
    /**
     * Get all provinces (İller)
     * 
     * @return array
     */
    public function getProvinces()
    {
        return Cache::remember('turkiyeapi.provinces', $this->cacheTtl, function () {
            try {
                $response = Http::get("{$this->baseUrl}/provinces");
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data'] ?? [];
                }
                
                Log::warning('TurkiyeAPI provinces error', ['status' => $response->status()]);
                return [];
            } catch (\Exception $e) {
                Log::error('TurkiyeAPI provinces exception', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }
    
    /**
     * Get districts by province (İlçeler)
     * 
     * @param int $provinceId Province ID
     * @return array
     */
    public function getDistricts($provinceId)
    {
        return Cache::remember("turkiyeapi.districts.{$provinceId}", $this->cacheTtl, function () use ($provinceId) {
            try {
                $response = Http::get("{$this->baseUrl}/districts", [
                    'provinceId' => $provinceId
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data'] ?? [];
                }
                
                return [];
            } catch (\Exception $e) {
                Log::error('TurkiyeAPI districts exception', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }
    
    /**
     * Get neighborhoods by district (Mahalleler)
     * 
     * @param int $districtId District ID
     * @return array
     */
    public function getNeighborhoods($districtId)
    {
        return Cache::remember("turkiyeapi.neighborhoods.{$districtId}", $this->cacheTtl, function () use ($districtId) {
            try {
                $response = Http::get("{$this->baseUrl}/neighborhoods", [
                    'districtId' => $districtId
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data'] ?? [];
                }
                
                return [];
            } catch (\Exception $e) {
                Log::error('TurkiyeAPI neighborhoods exception', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }
    
    /**
     * Get towns by district (Beldeler) - TATİL BÖLGELERİ!
     * 
     * @param int $districtId District ID
     * @return array
     */
    public function getTowns($districtId)
    {
        return Cache::remember("turkiyeapi.towns.{$districtId}", $this->cacheTtl, function () use ($districtId) {
            try {
                $response = Http::get("{$this->baseUrl}/towns", [
                    'districtId' => $districtId
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data'] ?? [];
                }
                
                return [];
            } catch (\Exception $e) {
                Log::error('TurkiyeAPI towns exception', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }
    
    /**
     * Get villages by district (Köyler) - KIRSAL EMLAK!
     * 
     * @param int $districtId District ID
     * @param int $limit Limit results
     * @return array
     */
    public function getVillages($districtId, $limit = 100)
    {
        return Cache::remember("turkiyeapi.villages.{$districtId}.{$limit}", $this->cacheTtl, function () use ($districtId, $limit) {
            try {
                $response = Http::get("{$this->baseUrl}/villages", [
                    'districtId' => $districtId,
                    'limit' => $limit,
                    'offset' => 0
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data'] ?? [];
                }
                
                return [];
            } catch (\Exception $e) {
                Log::error('TurkiyeAPI villages exception', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }
    
    /**
     * Get all location types for a district (Unified)
     * Mahalle + Belde + Köy birlikte
     * 
     * @param int $districtId District ID
     * @return array
     */
    public function getAllLocations($districtId)
    {
        return Cache::remember("turkiyeapi.all_locations.{$districtId}", $this->cacheTtl, function () use ($districtId) {
            $locations = [
                'neighborhoods' => [],
                'towns' => [],
                'villages' => []
            ];
            
            try {
                // Mahalleler
                $neighborhoods = $this->getNeighborhoods($districtId);
                foreach ($neighborhoods as $n) {
                    $locations['neighborhoods'][] = [
                        'id' => $n['id'],
                        'name' => $n['name'],
                        'type' => 'mahalle',
                        'type_label' => 'Mahalle',
                        'icon' => '📍',
                        'population' => $n['population'] ?? null,
                        'postcode' => $n['postcode'] ?? null,
                    ];
                }
                
                // Beldeler (TATİL BÖLGELERİ!)
                $towns = $this->getTowns($districtId);
                foreach ($towns as $t) {
                    $locations['towns'][] = [
                        'id' => $t['id'],
                        'name' => $t['name'],
                        'type' => 'belde',
                        'type_label' => 'Belde',
                        'icon' => '🏖️',
                        'population' => $t['population'] ?? null,
                        'postcode' => $t['postcode'] ?? null,
                        'is_coastal' => $t['isCoastal'] ?? false,
                        'area' => $t['area'] ?? null,
                    ];
                }
                
                // Köyler (KIRSAL EMLAK!)
                $villages = $this->getVillages($districtId, 50); // İlk 50 köy
                foreach ($villages as $v) {
                    $locations['villages'][] = [
                        'id' => $v['id'],
                        'name' => $v['name'],
                        'type' => 'koy',
                        'type_label' => 'Köy',
                        'icon' => '🌾',
                        'population' => $v['population'] ?? null,
                        'postcode' => $v['postcode'] ?? null,
                    ];
                }
                
                return $locations;
            } catch (\Exception $e) {
                Log::error('TurkiyeAPI getAllLocations exception', ['error' => $e->getMessage()]);
                return $locations;
            }
        });
    }
    
    /**
     * Search locations by query
     * 
     * @param string $query Search term
     * @param string $type Location type filter (mahalle, belde, koy, all)
     * @return array
     */
    public function searchLocations($query, $type = 'all')
    {
        $cacheKey = "turkiyeapi.search.{$query}.{$type}";
        
        return Cache::remember($cacheKey, 3600, function () use ($query, $type) {
            $results = [];
            
            try {
                // API'de search endpoint yoksa, tüm verilerde ara
                // Provinces
                if ($type === 'all' || $type === 'province') {
                    $provinces = $this->getProvinces();
                    foreach ($provinces as $p) {
                        if (stripos($p['name'], $query) !== false) {
                            $results[] = [
                                'id' => $p['id'],
                                'name' => $p['name'],
                                'type' => 'province',
                                'type_label' => 'İl',
                                'icon' => '🏙️'
                            ];
                        }
                    }
                }
                
                return $results;
            } catch (\Exception $e) {
                Log::error('TurkiyeAPI search exception', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }
    
    /**
     * Get location details with WikiMapia enhancement
     * 
     * @param string $type Location type
     * @param int $id Location ID
     * @return array|null
     */
    public function getLocationDetails($type, $id)
    {
        $cacheKey = "turkiyeapi.location.{$type}.{$id}";
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($type, $id) {
            try {
                $endpoint = match($type) {
                    'province' => 'provinces',
                    'district' => 'districts',
                    'neighborhood' => 'neighborhoods',
                    'town' => 'towns',
                    'village' => 'villages',
                    default => null
                };
                
                if (!$endpoint) return null;
                
                $response = Http::get("{$this->baseUrl}/{$endpoint}/{$id}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data'] ?? null;
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error('TurkiyeAPI location details exception', ['error' => $e->getMessage()]);
                return null;
            }
        });
    }
    
    /**
     * Clear all TurkiyeAPI caches
     */
    public function clearCache()
    {
        Cache::tags(['turkiyeapi'])->flush();
    }
}

