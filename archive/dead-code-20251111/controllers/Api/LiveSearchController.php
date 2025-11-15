<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kisi;
use App\Models\User;
use App\Models\SiteApartman;
use Illuminate\Support\Facades\Log;

/**
 * Context7 Uyumlu Live Search Controller
 *
 * Bu controller tüm live search işlemlerini Context7 standartlarına uygun şekilde yönetir.
 *
 * @package App\Http\Controllers\Api
 * @version 2.0.0
 * @since 2025-10-05
 */
class LiveSearchController extends Controller
{
    /**
     * Kişi canlı arama (Context7 uyumlu)
     */
    public function searchKisiler(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
            'musteri_tipi' => 'nullable|string|in:ev_sahibi,satici,alici,kiraci',
            'danisman_id' => 'nullable|integer|exists:users,id',
            'include_inactive' => 'nullable|boolean'
        ]);

        $query = trim($request->input('q'));
        $limit = $request->input('limit', 20);
        $musteriTipi = $request->input('musteri_tipi');
        $danismanId = $request->input('danisman_id');
        $includeInactive = $request->input('include_inactive', false);

        try {
            // Context7 uyumlu sorgu oluşturma
            $kisilerQuery = Kisi::with(['danisman', 'il', 'ilce', 'mahalle']);

            // Context7 uyumlu arama - tam ad, telefon, email
            $kisilerQuery->where(function ($q) use ($query) {
                $q->whereRaw("CONCAT(ad, ' ', soyad) LIKE ?", ["%{$query}%"])
                    ->orWhere('telefon', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });

            // Context7 uyumlu filtreler
            if (!$includeInactive) {
                $kisilerQuery->where('status', true); // Context7 uyumlu
            }

            if ($musteriTipi) {
                $kisilerQuery->byMusteriTipi($musteriTipi); // Model scope kullanımı
            }

            if ($danismanId) {
                $kisilerQuery->byDanisman($danismanId); // Model scope kullanımı
            }

            $kisiler = $kisilerQuery->limit($limit)->get();

            // Context7 uyumlu response formatı
            $results = $kisiler->map(function ($kisi) {
                return [
                    'id' => $kisi->id,
                    'tam_ad' => $kisi->tam_ad,
                    'telefon' => $kisi->telefon,
                    'email' => $kisi->email,
                    'kisi_tipi' => $kisi->kisi_tipi ?? $kisi->musteri_tipi ?? null,
                    'status' => $kisi->status,
                    'crm_score' => $kisi->crm_score,
                    'is_owner_eligible' => $kisi->isOwnerEligible(),
                    'il_adi' => $kisi->il ? $kisi->il->name : null,
                    'ilce_adi' => $kisi->ilce ? $kisi->ilce->name : null,
                    'mahalle_adi' => $kisi->mahalle ? $kisi->mahalle->name : null,
                    'danisman_adi' => $kisi->danisman ? $kisi->danisman->name : null,
                    'display_text' => $kisi->tam_ad . ' (' . $kisi->telefon . ')',
                    'search_hint' => $this->generateSearchHint($kisi)
                ];
            });

            return response()->json([
                'success' => true,
                'count' => $results->count(),
                'data' => $results,
                'search_metadata' => [
                    'query' => $query,
                    'filters_applied' => [
                        'musteri_tipi' => $musteriTipi,
                        'danisman_id' => $danismanId,
                        'include_inactive' => $includeInactive
                    ],
                    'context7_compliant' => true
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Live search kişiler hatası: ' . $e->getMessage(), [
                'query' => $query,
                'filters' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Kişi arama sırasında hata oluştu.',
                'message' => config('app.debug') ? $e->getMessage() : 'Lütfen daha sonra tekrar deneyin.'
            ], 500);
        }
    }

    /**
     * Danışman canlı arama (Context7 uyumlu)
     */
    public function searchDanismanlar(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
            'include_inactive' => 'nullable|boolean'
        ]);

        $query = trim($request->input('q'));
        $limit = $request->input('limit', 20);
        $includeInactive = $request->input('include_inactive', false);

        try {
            // Context7 uyumlu danışman sorgusu - sadece danışman rolüne sahip kullanıcılar
            $danismanlarQuery = User::whereHas('roles', function ($q) {
                $q->where('name', 'danisman');
            })->with(['roles']);

            // Context7 uyumlu arama - name, email
            $danismanlarQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });

            // Context7 uyumlu status filtresi
            if (!$includeInactive) {
                $danismanlarQuery->where('status', true); // boolean status alanı
            }

            $danismanlar = $danismanlarQuery->limit($limit)->get();

            // Context7 uyumlu response formatı
            $results = $danismanlar->map(function ($danisman) {
                return [
                    'id' => $danisman->id,
                    'name' => $danisman->name,
                    'email' => $danisman->email,
                    'status' => $danisman->status,
                    'roles' => $danisman->roles->pluck('name')->toArray(),
                    'display_text' => $danisman->name . ' (' . $danisman->email . ')',
                    'search_hint' => $this->generateDanismanSearchHint($danisman)
                ];
            });

            return response()->json([
                'success' => true,
                'count' => $results->count(),
                'data' => $results,
                'search_metadata' => [
                    'query' => $query,
                    'filters_applied' => [
                        'include_inactive' => $includeInactive
                    ],
                    'context7_compliant' => true
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Live search danışmanlar hatası: ' . $e->getMessage(), [
                'query' => $query,
                'filters' => $request->all(),
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
     * Site/Apartman canlı arama (Context7 uyumlu)
     */
    public function searchSites(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
            'il_id' => 'nullable|integer|exists:iller,id',
            'ilce_id' => 'nullable|integer|exists:ilceler,id',
            'include_inactive' => 'nullable|boolean'
        ]);

        $query = trim($request->input('q'));
        $limit = $request->input('limit', 20);
        $ilId = $request->input('il_id');
        $ilceId = $request->input('ilce_id');
        $includeInactive = $request->input('include_inactive', false);

        try {
            // ✅ Context7 uyumlu site sorgusu - ilişkilerle birlikte
            $sitesQuery = \App\Models\Site::with(['il', 'ilce', 'mahalle']);

            // ✅ Context7 uyumlu arama - name, address, description
            $sitesQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('address', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });

            // ✅ Context7 uyumlu lokasyon filtreleri
            if ($ilId) {
                $sitesQuery->byIl($ilId); // Model scope kullanımı
            }

            if ($ilceId) {
                $sitesQuery->byIlce($ilceId); // Model scope kullanımı
            }

            // ✅ Context7 uyumlu status filtresi
            if (!$includeInactive) {
                $sitesQuery->where('status', true); // Context7 uyumlu
            }

            $sites = $sitesQuery->limit($limit)->get();

            // ✅ Context7 uyumlu response formatı - ilişkili verilerle
            $results = $sites->map(function ($site) {
                return [
                    'id' => $site->id,
                    'name' => $site->name,
                    'address' => $site->address,
                    'description' => $site->description,
                    'status' => $site->active,
                    'il_adi' => $site->il ? $site->il->name : null, // ✅ İlişki kullanımı
                    'ilce_adi' => $site->ilce ? $site->ilce->name : null, // ✅ İlişki kullanımı
                    'mahalle_adi' => $site->mahalle ? $site->mahalle->name : null, // ✅ İlişki kullanımı
                    'location_text' => $site->location_text, // ✅ Accessor kullanımı
                    'display_text' => $site->name . ' - ' . $site->address,
                    'search_hint' => $this->generateSiteSearchHint($site)
                ];
            });

            return response()->json([
                'success' => true,
                'count' => $results->count(),
                'data' => $results,
                'search_metadata' => [
                    'query' => $query,
                    'filters_applied' => [
                        'il_id' => $ilId,
                        'ilce_id' => $ilceId,
                        'include_inactive' => $includeInactive
                    ],
                    'context7_compliant' => true
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Live search sites hatası: ' . $e->getMessage(), [
                'query' => $query,
                'filters' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Site/Apartman arama sırasında hata oluştu.',
                'message' => config('app.debug') ? $e->getMessage() : 'Lütfen daha sonra tekrar deneyin.'
            ], 500);
        }
    }

    /**
     * Birleşik arama (Context7 uyumlu)
     */
    public function unifiedSearch(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'types' => 'nullable|array',
            'types.*' => 'in:kisiler,danismanlar,sites',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $query = trim($request->input('q'));
        $types = $request->input('types', ['kisiler', 'danismanlar', 'sites']);
        $limit = $request->input('limit', 10);

        try {
            $results = [];

            // Kişiler arama
            if (in_array('kisiler', $types)) {
                $kisiRequest = new Request(['q' => $query, 'limit' => $limit]);
                $kisiResponse = $this->searchKisiler($kisiRequest);
                $kisiData = json_decode($kisiResponse->getContent(), true);

                if ($kisiData['success']) {
                    $results['kisiler'] = [
                        'count' => $kisiData['count'],
                        'data' => $kisiData['data']
                    ];
                }
            }

            // Danışmanlar arama
            if (in_array('danismanlar', $types)) {
                $danismanRequest = new Request(['q' => $query, 'limit' => $limit]);
                $danismanResponse = $this->searchDanismanlar($danismanRequest);
                $danismanData = json_decode($danismanResponse->getContent(), true);

                if ($danismanData['success']) {
                    $results['danismanlar'] = [
                        'count' => $danismanData['count'],
                        'data' => $danismanData['data']
                    ];
                }
            }

            // Siteler arama
            if (in_array('sites', $types)) {
                $siteRequest = new Request(['q' => $query, 'limit' => $limit]);
                $siteResponse = $this->searchSites($siteRequest);
                $siteData = json_decode($siteResponse->getContent(), true);

                if ($siteData['success']) {
                    $results['sites'] = [
                        'count' => $siteData['count'],
                        'data' => $siteData['data']
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'query' => $query,
                'results' => $results,
                'total_count' => array_sum(array_column($results, 'count')),
                'search_metadata' => [
                    'types_searched' => $types,
                    'limit_per_type' => $limit,
                    'context7_compliant' => true
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Unified search hatası: ' . $e->getMessage(), [
                'query' => $query,
                'types' => $types,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Birleşik arama sırasında hata oluştu.',
                'message' => config('app.debug') ? $e->getMessage() : 'Lütfen daha sonra tekrar deneyin.'
            ], 500);
        }
    }

    /**
     * Kişi için arama ipucu oluştur (Context7 uyumlu)
     */
    private function generateSearchHint($kisi): string
    {
        $hints = [];

        if ($kisi->telefon) {
            $hints[] = 'Telefon: ' . $kisi->telefon;
        }

        if ($kisi->email) {
            $hints[] = 'Email: ' . $kisi->email;
        }

        if ($kisi->musteri_tipi) {
            $hints[] = 'Tip: ' . ucfirst($kisi->musteri_tipi);
        }

        if ($kisi->il_adi) {
            $hints[] = 'Konum: ' . $kisi->il_adi;
        }

        return implode(' | ', $hints);
    }

    /**
     * Danışman için arama ipucu oluştur (Context7 uyumlu)
     */
    private function generateDanismanSearchHint($danisman): string
    {
        $hints = [];

        if ($danisman->email) {
            $hints[] = 'Email: ' . $danisman->email;
        }

        if ($danisman->status) {
            $hints[] = 'Durum: ' . $danisman->status;
        }

        $roles = $danisman->roles->pluck('name')->implode(', ');
        if ($roles) {
            $hints[] = 'Roller: ' . $roles;
        }

        return implode(' | ', $hints);
    }

    /**
     * Site için arama ipucu oluştur (Context7 uyumlu)
     */
    private function generateSiteSearchHint($site): string
    {
        $hints = [];

        if ($site->address) {
            $hints[] = 'Adres: ' . $site->address;
        }

        if ($site->active !== null) {
            $hints[] = 'Durum: ' . ($site->active ? 'Aktif' : 'Pasif');
        }

        if ($site->description) {
            $hints[] = 'Açıklama: ' . substr($site->description, 0, 50) . '...';
        }

        return implode(' | ', $hints);
    }
}
