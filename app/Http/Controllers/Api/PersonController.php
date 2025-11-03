<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kisi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersonController extends Controller
{
    /**
     * Search persons with advanced filtering
     * Context7: C7-PERSON-SEARCH-2025-10-19
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $limit = min($request->get('limit', 20), 50); // Max 50 results
            $type = $request->get('type', 'all'); // all, customer, owner, agent

            $personsQuery = Kisi::query()
                ->select([
                    'id',
                    'ad',
                    'soyad',
                    'telefon',
                    'email',
                    'kisi_tipi',
                    'il_id',
                    'ilce_id',
                    'created_at'
                ])
                ->where('status', 'Aktif');

            // Search filter
            if (!empty($query)) {
                $personsQuery->where(function ($q) use ($query) {
                    $q->where('ad', 'LIKE', "%{$query}%")
                      ->orWhere('soyad', 'LIKE', "%{$query}%")
                      ->orWhere('telefon', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->orWhereRaw("CONCAT(ad, ' ', soyad) LIKE ?", ["%{$query}%"]);
                });
            }

            // Type filter
            if ($type !== 'all') {
                $personsQuery->where('musteri_tipi', $type);
            }

            // Order by relevance and recency
            $personsQuery->orderByRaw("
                CASE
                    WHEN CONCAT(ad, ' ', soyad) LIKE ? THEN 1
                    WHEN ad LIKE ? THEN 2
                    WHEN soyad LIKE ? THEN 3
                    WHEN telefon LIKE ? THEN 4
                    WHEN email LIKE ? THEN 5
                    ELSE 6
                END,
                created_at DESC
            ", [
                "%{$query}%",
                "%{$query}%",
                "%{$query}%",
                "%{$query}%",
                "%{$query}%"
            ]);

            $persons = $personsQuery->limit($limit)->get();

            // Add formatted display text
            $persons->each(function ($person) {
                $person->display_name = trim("{$person->ad} {$person->soyad}");
                $person->text = $person->display_name; // Context7 Live Search compatibility
                $person->display_contact = collect([
                    $person->telefon,
                    $person->email
                ])->filter()->implode(' • ');

                $person->display_location = collect([
                    $person->il,
                    $person->ulke
                ])->filter()->implode(', ');
            });

            return response()->json([
                'success' => true,
                'data' => $persons,
                'count' => $persons->count(),
                'query' => $query,
                'type' => $type
            ]);

        } catch (\Exception $e) {
            Log::error('Person search error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Arama sırasında hata oluştu',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get person details by ID
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $person = Kisi::select([
                'id',
                'ad',
                'soyad',
                'telefon',
                'email',
                'il',
                'ulke',
                'adres',
                'musteri_tipi',
                'tc_kimlik',
                'dogum_tarihi',
                'meslek',
                'notlar',
                'created_at',
                'updated_at'
            ])->find($id);

            if (!$person) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kişi bulunamadı'
                ], 404);
            }

            // Add formatted fields
            $person->display_name = trim("{$person->ad} {$person->soyad}");
            $person->age = $person->dogum_tarihi ?
                \Carbon\Carbon::parse($person->dogum_tarihi)->age : null;

            return response()->json([
                'success' => true,
                'data' => $person
            ]);

        } catch (\Exception $e) {
            Log::error('Person detail error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Kişi bilgileri alınırken hata oluştu'
            ], 500);
        }
    }

    /**
     * Store new person (for quick add from search)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'ad' => 'required|string|max:100',
                'soyad' => 'required|string|max:100',
                'telefon' => 'required|string|max:20',
                'email' => 'nullable|email|max:150',
                'il' => 'nullable|string|max:100',
                'ulke' => 'nullable|string|max:100',
                'musteri_tipi' => 'nullable|in:customer,owner,agent',
                'adres' => 'nullable|string|max:500',
                'notlar' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for duplicates
            $duplicate = Kisi::where('telefon', $request->telefon)
                ->orWhere(function($q) use ($request) {
                    if ($request->email) {
                        $q->where('email', $request->email);
                    }
                })
                ->first();

            if ($duplicate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu telefon numarası veya email adresi zaten kayıtlı',
                    'existing_person' => [
                        'id' => $duplicate->id,
                        'display_name' => trim("{$duplicate->ad} {$duplicate->soyad}"),
                        'telefon' => $duplicate->telefon,
                        'email' => $duplicate->email
                    ]
                ], 409);
            }

            DB::beginTransaction();

            $person = Kisi::create([
                'ad' => $request->ad,
                'soyad' => $request->soyad,
                'telefon' => $request->telefon,
                'email' => $request->email,
                'il' => $request->il ?? 'Muğla',
                'ulke' => $request->ulke ?? 'Türkiye',
                'musteri_tipi' => $request->musteri_tipi ?? 'customer',
                'adres' => $request->adres,
                'notlar' => $request->notlar,
                'status' => 'active',
                'created_by' => auth()->id() ?? 1 // Default admin user
            ]);

            DB::commit();

            // Format response
            $person->display_name = trim("{$person->ad} {$person->soyad}");
            $person->display_contact = collect([
                $person->telefon,
                $person->email
            ])->filter()->implode(' • ');

            return response()->json([
                'success' => true,
                'message' => 'Kişi başarıyla eklendi',
                'data' => $person
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Person store error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Kişi eklenirken hata oluştu',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get person statistics (for admin)
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_persons' => Kisi::where('status', 'Aktif')->count(),
                'customers' => Kisi::where('status', 'Aktif')->where('musteri_tipi', 'customer')->count(),
                'owners' => Kisi::where('status', 'Aktif')->where('musteri_tipi', 'owner')->count(),
                'agents' => Kisi::where('status', 'Aktif')->where('musteri_tipi', 'agent')->count(),
                'recent_additions' => Kisi::where('status', 'Aktif')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İstatistikler alınırken hata oluştu'
            ], 500);
        }
    }
}
