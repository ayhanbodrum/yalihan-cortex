<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BulkKisiController extends AdminController
{
    /**
     * Display the bulk operations dashboard
     */
    public function index()
    {
        $stats = [
            'total_kisiler' => Kisi::count(),
            'active_kisiler' => Kisi::where('status', 'active')->count(),
            'inactive_kisiler' => Kisi::where('status', 'inactive')->count(),
            'recent_additions' => Kisi::whereBetween('created_at', [
                now()->subDays(7),
                now(),
            ])->count(),
        ];

        return view('admin.bulk-kisi.index', compact('stats'));
    }

    /**
     * Show the form for creating multiple kisiler
     */
    public function create()
    {
        return view('admin.bulk-kisi.create');
    }

    /**
     * Store multiple kisiler in bulk
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kisiler' => 'required|array|min:1',
            'kisiler.*.ad' => 'required|string|max:255',
            'kisiler.*.soyad' => 'required|string|max:255',
            'kisiler.*.email' => 'nullable|email|unique:kisiler,email',
            'kisiler.*.telefon' => 'nullable|string|max:20',
            'kisiler.*.tc_kimlik' => 'nullable|string|size:11|unique:kisiler,tc_kimlik',
            'kisiler.*.tip' => 'required|in:musteri,mal_sahibi,danismani',
            'kisiler.*.status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $created = [];
            $errors = [];

            // ✅ PERFORMANCE FIX: N+1 query önlendi - Tüm email ve TC'leri tek query'de kontrol et
            $emails = array_filter(array_column($request->kisiler, 'email'));
            $tcKimlikler = array_filter(array_column($request->kisiler, 'tc_kimlik'));

            $existingByEmail = Kisi::whereIn('email', $emails)
                ->pluck('email', 'id')
                ->toArray();
            $existingByTc = Kisi::whereIn('tc_kimlik', $tcKimlikler)
                ->pluck('tc_kimlik', 'id')
                ->toArray();

            foreach ($request->kisiler as $index => $kisiData) {
                try {
                    // ✅ PERFORMANCE FIX: Array kontrolü kullan (database query yerine)
                    $existingKisi = null;
                    if (! empty($kisiData['email']) && in_array($kisiData['email'], $existingByEmail)) {
                        $existingKisi = Kisi::find(array_search($kisiData['email'], $existingByEmail));
                    }
                    if (! $existingKisi && ! empty($kisiData['tc_kimlik']) && in_array($kisiData['tc_kimlik'], $existingByTc)) {
                        $existingKisi = Kisi::find(array_search($kisiData['tc_kimlik'], $existingByTc));
                    }

                    if ($existingKisi) {
                        $errors[] = [
                            'index' => $index,
                            'message' => "Kişi zaten mevcut (Email: {$kisiData['email']}, TC: {$kisiData['tc_kimlik']})",
                        ];

                        continue;
                    }

                    $kisi = Kisi::create([
                        'ad' => $kisiData['ad'],
                        'soyad' => $kisiData['soyad'],
                        'email' => $kisiData['email'] ?? null,
                        'telefon' => $kisiData['telefon'] ?? null,
                        'tc_kimlik' => $kisiData['tc_kimlik'] ?? null,
                        'tip' => $kisiData['tip'],
                        'status' => $kisiData['status'],
                        'created_by' => auth()->id(),
                    ]);

                    $created[] = $kisi;
                } catch (\Exception $e) {
                    $errors[] = [
                        'index' => $index,
                        'message' => 'Kayıt hatası: '.$e->getMessage(),
                    ];
                }
            }

            DB::commit();

            Log::info('Bulk kisi creation completed', [
                'created_count' => count($created),
                'error_count' => count($errors),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => count($created).' kişi başarıyla oluşturuldu.',
                'data' => [
                    'created_count' => count($created),
                    'error_count' => count($errors),
                    'errors' => $errors,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Bulk kisi creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Toplu kayıt işlemi başarısız: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for bulk editing
     */
    public function edit()
    {
        return view('admin.bulk-kisi.edit');
    }

    /**
     * Update multiple kisiler in bulk
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kisi_ids' => 'required|array|min:1',
            'kisi_ids.*' => 'exists:kisiler,id',
            'updates' => 'required|array',
            'updates.status' => 'sometimes|in:active,inactive',
            'updates.tip' => 'sometimes|in:musteri,mal_sahibi,danismani',
            'updates.danismanId' => 'sometimes|nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updatedCount = Kisi::whereIn('id', $request->kisi_ids)
                ->update(array_filter($request->updates, function ($value) {
                    return $value !== null;
                }));

            DB::commit();

            Log::info('Bulk kisi update completed', [
                'updated_count' => $updatedCount,
                'kisi_ids' => $request->kisi_ids,
                'updates' => $request->updates,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => $updatedCount.' kişi başarıyla güncellendi.',
                'updated_count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Bulk kisi update failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Toplu güncelleme işlemi başarısız: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove multiple kisiler in bulk
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kisi_ids' => 'required|array|min:1',
            'kisi_ids.*' => 'exists:kisiler,id',
            'force_delete' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            if ($request->force_delete) {
                $deletedCount = Kisi::whereIn('id', $request->kisi_ids)->forceDelete();
            } else {
                $deletedCount = Kisi::whereIn('id', $request->kisi_ids)->delete();
            }

            DB::commit();

            Log::info('Bulk kisi deletion completed', [
                'deleted_count' => $deletedCount,
                'kisi_ids' => $request->kisi_ids,
                'force_delete' => $request->force_delete,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => $deletedCount.' kişi başarıyla silindi.',
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Bulk kisi deletion failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Toplu silme işlemi başarısız: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export kisiler data
     */
    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'format' => 'required|in:csv,xlsx,json',
            'filters' => 'sometimes|array',
            'filters.tip' => 'sometimes|in:musteri,mal_sahibi,danismani',
            'filters.status' => 'sometimes|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $query = Kisi::select(['id', 'ad', 'soyad', 'email', 'telefon', 'tc_kimlik', 'tip', 'status', 'created_at']);

            // Apply filters
            if (isset($request->filters['tip'])) {
                $query->where('tip', $request->filters['tip']);
            }
            if (isset($request->filters['status'])) {
                $query->where('status', $request->filters['status']);
            }

            $kisiler = $query->get();

            switch ($request->format) {
                case 'json':
                    return response()->json([
                        'success' => true,
                        'data' => $kisiler,
                        'count' => $kisiler->count(),
                    ]);

                case 'csv':
                    $filename = 'kisiler_export_'.date('Y-m-d_H-i-s').'.csv';

                    return response()->streamDownload(function () use ($kisiler) {
                        $file = fopen('php://output', 'w');

                        // CSV header
                        fputcsv($file, ['ID', 'Ad', 'Soyad', 'Email', 'Telefon', 'TC Kimlik', 'Tip', 'Status', 'Oluşturma Tarihi']);

                        // CSV rows
                        foreach ($kisiler as $kisi) {
                            fputcsv($file, [
                                $kisi->id,
                                $kisi->ad,
                                $kisi->soyad,
                                $kisi->email,
                                $kisi->telefon,
                                $kisi->tc_kimlik,
                                $kisi->tip,
                                $kisi->status,
                                $kisi->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }

                        fclose($file);
                    }, $filename, [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                    ]);

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Desteklenmeyen format',
                    ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Bulk kisi export failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Export işlemi başarısız: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import kisiler from file
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
            'has_header' => 'boolean',
            'delimiter' => 'sometimes|string|max:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $file = $request->file('file');
            $hasHeader = $request->get('has_header', true);
            $delimiter = $request->get('delimiter', ',');

            $csvData = array_map('str_getcsv', file($file->getPathname()));

            if ($hasHeader) {
                array_shift($csvData); // Remove header row
            }

            DB::beginTransaction();

            $created = [];
            $errors = [];

            foreach ($csvData as $index => $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Map CSV columns to fields
                    $kisiData = [
                        'ad' => $row[0] ?? '',
                        'soyad' => $row[1] ?? '',
                        'email' => ! empty($row[2]) ? $row[2] : null,
                        'telefon' => ! empty($row[3]) ? $row[3] : null,
                        'tc_kimlik' => ! empty($row[4]) ? $row[4] : null,
                        'tip' => $row[5] ?? 'musteri',
                        'status' => $row[6] ?? 'active',
                    ];

                    // Validate data
                    if (empty($kisiData['ad']) || empty($kisiData['soyad'])) {
                        $errors[] = [
                            'row' => $index + 1,
                            'message' => 'Ad ve Soyad alanları zorunludur',
                        ];

                        continue;
                    }

                    // Check for duplicates
                    $existingKisi = null;
                    if (! empty($kisiData['email'])) {
                        $existingKisi = Kisi::where('email', $kisiData['email'])->first();
                    }
                    if (! $existingKisi && ! empty($kisiData['tc_kimlik'])) {
                        $existingKisi = Kisi::where('tc_kimlik', $kisiData['tc_kimlik'])->first();
                    }

                    if ($existingKisi) {
                        $errors[] = [
                            'row' => $index + 1,
                            'message' => 'Kişi zaten mevcut: '.$kisiData['email'],
                        ];

                        continue;
                    }

                    $kisi = Kisi::create([
                        ...$kisiData,
                        'created_by' => auth()->id(),
                    ]);

                    $created[] = $kisi;
                } catch (\Exception $e) {
                    $errors[] = [
                        'row' => $index + 1,
                        'message' => 'Import hatası: '.$e->getMessage(),
                    ];
                }
            }

            DB::commit();

            Log::info('Bulk kisi import completed', [
                'file_name' => $file->getClientOriginalName(),
                'created_count' => count($created),
                'error_count' => count($errors),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => count($created).' kişi başarıyla import edildi.',
                'data' => [
                    'created_count' => count($created),
                    'error_count' => count($errors),
                    'errors' => $errors,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Bulk kisi import failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Import işlemi başarısız: '.$e->getMessage(),
            ], 500);
        }
    }
}
