<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class GeoProxyController extends Controller
{
    protected string $ua = 'YalihanEmlak/1.0 (server-proxy)';

    protected function ttl(): int
    {
        return (int) config('context7.cache.ttl', 3600);
    }

    public function geocode(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'limit' => 'nullable|integer|min:1|max:10',
        ]);

        $query = trim($request->input('query'));
        $limit = (int) ($request->input('limit', 5));

        $cacheKey = 'geo:geocode:' . md5($query . '|' . $limit);
        $data = Cache::remember($cacheKey, $this->ttl(), function () use ($query, $limit) {
            $url = 'https://nominatim.openstreetmap.org/search';
            $resp = Http::withHeaders([
                'User-Agent' => $this->ua,
                'Accept' => 'application/json',
            ])->retry(3, 1000)->get($url, [
                'format' => 'json',
                'q' => $query,
                'limit' => $limit,
                'accept-language' => 'tr',
                'countrycodes' => 'tr',
                'addressdetails' => 1,
            ]);

            if (!$resp->ok()) {
                abort($resp->status(), 'Geocode upstream error');
            }

            return $resp->json();
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');

        $cacheKey = 'geo:reverse:' . md5($lat . '|' . $lng);
        $data = Cache::remember($cacheKey, $this->ttl(), function () use ($lat, $lng) {
            $url = 'https://nominatim.openstreetmap.org/reverse';
            $resp = Http::withHeaders([
                'User-Agent' => $this->ua,
                'Accept' => 'application/json',
            ])->retry(3, 1000)->get($url, [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lng,
                'zoom' => 18,
                'addressdetails' => 1,
                'accept-language' => 'tr',
            ]);

            if (!$resp->ok()) {
                abort($resp->status(), 'Reverse geocode upstream error');
            }

            return $resp->json();
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function nearby(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'type' => 'nullable|string',
            'radius' => 'nullable|integer|min:100|max:5000',
        ]);

        $lat = (float) $request->query('lat');
        $lng = (float) $request->query('lng');
        $type = $request->query('type', 'restaurant');
        $radius = (int) $request->query('radius', 2000);

        // Map UI category to Overpass amenity if needed
        $amenityMap = [ 'gas_station' => 'fuel' ];
        $amenity = $amenityMap[$type] ?? $type;

        $cacheKey = 'geo:nearby:' . md5($lat . '|' . $lng . '|' . $amenity . '|' . $radius);
        $data = Cache::remember($cacheKey, $this->ttl(), function () use ($lat, $lng, $amenity, $radius) {
            $query = <<<OVERPASS
[out:json][timeout:25];
(
  node["amenity"="{$amenity}"](around:{$radius},{$lat},{$lng});
  way["amenity"="{$amenity}"](around:{$radius},{$lat},{$lng});
  relation["amenity"="{$amenity}"](around:{$radius},{$lat},{$lng});
);
out center;
OVERPASS;

            $resp = Http::withHeaders([
                'User-Agent' => $this->ua,
                'Accept' => 'application/json',
            ])->retry(3, 1500)->get('https://overpass-api.de/api/interpreter', [
                'data' => $query,
            ]);

            if (!$resp->ok()) {
                abort($resp->status(), 'Nearby upstream error');
            }

            return $resp->json();
        });

        // Normalize: return elements as data
        $elements = $data['elements'] ?? [];

        return response()->json([
            'success' => true,
            'data' => $elements,
        ]);
    }
}
