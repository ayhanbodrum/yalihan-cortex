<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kisi;
use App\Models\Site;
use Illuminate\Support\Facades\Log;

/**
 * Hibrit Arama Controller - Select2, Context7 ve React Select Uyumlu
 *
 * Bu controller tüm frontend teknolojilerinin kullanabileceği
 * standartlaştırılmış API endpoint'leri sağlar.
 *
 * @package App\Http\Controllers\Api
 * @version 1.0.0
 * @since 2025-01-30
 */
class HybridSearchController extends Controller
{
    /**
     * Danışman arama - Hibrit format (Select2, Context7, React Select uyumlu)
     */
    public function searchDanismanlar(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
            'format' => 'nullable|string|in:select2,context7,react-select',
            'include_inactive' => 'nullable|boolean'
        ]);

        $query = trim($request->input('q'));
        $limit = $request->input('limit', 20);
        $format = $request->input('format', 'context7'); // Default Context7
        $includeInactive = $request->input('include_inactive', false);

        try {
            // Context7 uyumlu sorgu
            $danismanlarQuery = User::whereHas('roles', function ($q) {
                $q->where('name', 'danisman');
            })->with(['roles']);

            // Arama kriterleri
            $danismanlarQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });

            // Status filtresi
            if (!$includeInactive) {
                $danismanlarQuery->where('status', true);
            }

            $danismanlar = $danismanlarQuery->limit($limit)->get();

            // Format'a göre response döndür
            return $this->formatResponse($danismanlar, $format, 'danismanlar', $query);
        } catch (\Throwable $e) {
            Log::error('Hibrit danışman arama hatası: ' . $e->getMessage(), [
                'query' => $query,
                'format' => $format,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Danışman arama sırasında hata oluştu.',
                'message' => config('app.debug') ? $e->getMessage() : 'Lütfen daha sonra tekrar deneyin.'
            ], 500);
        }
    }

    /**
     * Kişi arama - Hibrit format
     */
    public function searchKisiler(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
            'format' => 'nullable|string|in:select2,context7,react-select',
            'musteri_tipi' => 'nullable|string|in:ev_sahibi,satici,alici,kiraci',
            'include_inactive' => 'nullable|boolean'
        ]);

        $query = trim($request->input('q'));
        $limit = $request->input('limit', 20);
        $format = $request->input('format', 'context7');
        $musteriTipi = $request->input('musteri_tipi');
        $includeInactive = $request->input('include_inactive', false);

        try {
            // Context7 uyumlu sorgu
            $kisilerQuery = Kisi::with(['il', 'ilce']);

            // Arama kriterleri
            $kisilerQuery->where(function ($q) use ($query) {
                $q->whereRaw("CONCAT(ad, ' ', soyad) LIKE ?", ["%{$query}%"])
                    ->orWhere('telefon', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });

            // Filtreler
            if (!$includeInactive) {
                $kisilerQuery->where('status', 'Aktif'); // kisiler tablosunda status string
            }

            if ($musteriTipi) {
                $kisilerQuery->byMusteriTipi($musteriTipi);
            }

            $kisiler = $kisilerQuery->limit($limit)->get();

            return $this->formatResponse($kisiler, $format, 'kisiler', $query);
        } catch (\Throwable $e) {
            Log::error('Hibrit kişi arama hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Kişi arama sırasında hata oluştu.',
                'message' => config('app.debug') ? $e->getMessage() : 'Lütfen daha sonra tekrar deneyin.'
            ], 500);
        }
    }

    /**
     * Site arama - Hibrit format
     */
    public function searchSites(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
            'format' => 'nullable|string|in:select2,context7,react-select',
            'include_inactive' => 'nullable|boolean'
        ]);

        $query = trim($request->input('q'));
        $limit = $request->input('limit', 20);
        $format = $request->input('format', 'context7');
        $includeInactive = $request->input('include_inactive', false);

        try {
            // Context7 uyumlu sorgu
            $sitesQuery = Site::with(['il', 'ilce', 'mahalle']);

            // Arama kriterleri
            $sitesQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('address', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });

            // Filtreler
            if (!$includeInactive) {
                $sitesQuery->where('status', true);
            }

            $sites = $sitesQuery->limit($limit)->get();

            return $this->formatResponse($sites, $format, 'sites', $query);
        } catch (\Throwable $e) {
            Log::error('Hibrit site arama hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Site arama sırasında hata oluştu.',
                'message' => config('app.debug') ? $e->getMessage() : 'Lütfen daha sonra tekrar deneyin.'
            ], 500);
        }
    }

    /**
     * Format'a göre response döndür
     */
    private function formatResponse($items, $format, $type, $query)
    {
        switch ($format) {
            case 'select2':
                return $this->formatSelect2Response($items, $type);

            case 'react-select':
                return $this->formatReactSelectResponse($items, $type);

            case 'context7':
            default:
                return $this->formatContext7Response($items, $type, $query);
        }
    }

    /**
     * Select2 formatı
     */
    private function formatSelect2Response($items, $type)
    {
        $results = $items->map(function ($item) use ($type) {
            return [
                'id' => $item->id,
                'text' => $this->getDisplayText($item, $type)
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => false // Basit implementasyon
            ]
        ]);
    }

    /**
     * React Select formatı
     */
    private function formatReactSelectResponse($items, $type)
    {
        $options = $items->map(function ($item) use ($type) {
            return [
                'value' => $item->id,
                'label' => $this->getDisplayText($item, $type),
                'data' => $this->getItemData($item, $type)
            ];
        });

        return response()->json($options);
    }

    /**
     * Context7 formatı
     */
    private function formatContext7Response($items, $type, $query)
    {
        $results = $items->map(function ($item) use ($type) {
            return [
                'id' => $item->id,
                'display_text' => $this->getDisplayText($item, $type),
                'search_hint' => $this->getSearchHint($item, $type),
                'data' => $this->getItemData($item, $type)
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $results->count(),
            'data' => $results,
            'search_metadata' => [
                'query' => $query,
                'type' => $type,
                'context7_compliant' => true,
                'hybrid_api' => true
            ]
        ]);
    }

    /**
     * Görüntüleme metni al
     */
    private function getDisplayText($item, $type)
    {
        switch ($type) {
            case 'danismanlar':
                return $item->name . ' (' . $item->email . ')';

            case 'kisiler':
                return $item->ad . ' ' . $item->soyad;

            case 'sites':
                return $item->name . ($item->address ? ' - ' . $item->address : '');

            default:
                return $item->name ?? $item->ad ?? 'Bilinmeyen';
        }
    }

    /**
     * Arama ipucu al
     */
    private function getSearchHint($item, $type)
    {
        switch ($type) {
            case 'danismanlar':
                return 'Danışman • ' . ($item->status ? 'Aktif' : 'Pasif');

            case 'kisiler':
                return 'Kişi • ' . $item->musteri_tipi . ' • ' . ($item->status === 'Aktif' ? 'Aktif' : 'Pasif');

            case 'sites':
                return 'Site/Apartman • ' . ($item->status ? 'Aktif' : 'Pasif');

            default:
                return '';
        }
    }

    /**
     * Item verilerini al
     */
    private function getItemData($item, $type)
    {
        $data = ['id' => $item->id];

        switch ($type) {
            case 'danismanlar':
                $data = array_merge($data, [
                    'name' => $item->name,
                    'email' => $item->email,
                    'status' => $item->status,
                    'roles' => $item->roles->pluck('name')->toArray()
                ]);
                break;

            case 'kisiler':
                $data = array_merge($data, [
                    'ad' => $item->ad,
                    'soyad' => $item->soyad,
                    'telefon' => $item->telefon,
                    'email' => $item->email,
                    'musteri_tipi' => $item->musteri_tipi,
                    'status' => $item->status
                ]);
                break;

            case 'sites':
                $data = array_merge($data, [
                    'name' => $item->name,
                    'address' => $item->address,
                    'description' => $item->description,
                    'status' => $item->status,
                    'location_text' => $item->location_text
                ]);
                break;
        }

        return $data;
    }
}
