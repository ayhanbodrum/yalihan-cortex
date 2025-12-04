<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\IlanInternalResource;
use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Services\AI\YalihanCortex;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

/**
 * My Listings Controller
 *
 * Context7: İlanlarım - Kullanıcının kendi ilanlarını yönetir
 * ✅ REFACTORED: YalihanCortex merkezi AI sistemi kullanılıyor
 */
class MyListingsController extends AdminController
{
    protected YalihanCortex $cortex;

    public function __construct(YalihanCortex $cortex)
    {
        $this->cortex = $cortex;
    }
    /**
     * Display user's own listings (İlanlarım sayfası)
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get current user
        $user = Auth::user();

        // If user is not authenticated, redirect to login
        if (! $user) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Query user's listings with eager loading
        $query = Ilan::query()
            ->where('danisman_id', $user->id);

        // ✅ REFACTORED: Sort (Filterable trait)
        $query->sort($request->sort_by, $request->sort_order ?? 'desc', 'updated_at');

        // ✅ REFACTORED: Status filter (Context7: Doğrudan database değerlerini kullan)
        if ($request->has('status') && $request->status) {
            // ✅ Context7: Frontend'den doğrudan database değerleri geliyor ('Aktif', 'Pasif', 'Taslak', 'Beklemede')
            $query->where('status', $request->status);
        }

        // ✅ REFACTORED: Category filter
        if ($request->has('category') && $request->category) {
            $query->where('alt_kategori_id', $request->category);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $referansService = app(\App\Services\IlanReferansService::class);

            // İlanReferansService'in searchQuery metodunu kullan
            $searchQuery = $referansService->searchQuery($search);

            // Kullanıcıya ait ilanları filtrele
            $query->whereIn('id', $searchQuery->where('danisman_id', $user->id)->pluck('id'));
        }

        // ✅ N+1 FIX: Select optimization + Eager loading
        $listings = $query->select([
            'id',
            'baslik',
            'fiyat',
            'para_birimi',
            'status',
            'goruntulenme', // ✅ Context7: goruntulenme_sayisi → goruntulenme (database column name)
            'alt_kategori_id',
            'ana_kategori_id',
            'il_id',
            'ilce_id',
            'referans_no',
            'dosya_adi',
            'created_at',
            'updated_at',
        ])
            ->with([
                'altKategori:id,name,icon',
                'anaKategori:id,name',
                'il:id,il_adi',
                'ilce:id,ilce_adi',
                'fotograflar:id,ilan_id,dosya_yolu,sira' => function ($query) {
                    $query->orderBy('sira')->limit(1);
                },
            ])
            ->paginate(20);

        // Calculate statistics - Context7: Status değerleri düzeltildi
        $stats = [
            'total_listings' => Ilan::where('danisman_id', $user->id)->count(),
            'active_listings' => Ilan::where('danisman_id', $user->id)
                ->where('status', 'Aktif')
                ->count(),
            'pending_listings' => Ilan::where('danisman_id', $user->id)
                ->where('status', 'Beklemede')
                ->count(),
            'total_views' => Ilan::where('danisman_id', $user->id)
                ->sum('goruntulenme') ?? 0, // ✅ Context7: goruntulenme_sayisi → goruntulenme (database column name)
        ];

        // ✅ CACHE: Kategoriler dropdown için cache ekle
        $categories = \Illuminate\Support\Facades\Cache::remember('my_listings_categories_'.$user->id, 3600, function () {
            return IlanKategori::select('id', 'name', 'icon')
                ->whereNotNull('parent_id')
                ->where('status', true)
                ->orderBy('name')
                ->get();
        });

        // ✅ NEW: AI analiz sonuçları (opsiyonel, lazy load)
        $aiAnalysis = null;
        if ($request->has('ai_analysis') && $request->ai_analysis) {
            $aiAnalysis = $this->cortex->analyzeMyListings($user->id);
        }

        return view('admin.ilanlar.my-listings', compact('listings', 'stats', 'categories', 'aiAnalysis'));
    }

    /**
     * Search listings via AJAX
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $user = Auth::user();

        $query = Ilan::with(['altKategori:id,name', 'il:id,il_adi'])
            ->where('danisman_id', $user->id);

        // Context7: Status mapping (active→Aktif, pending→Beklemede, vb.)
        // ✅ Context7: Status filter (Doğrudan database değerlerini kullan)
        if ($request->has('status') && $request->status) {
            // ✅ Context7: Frontend'den doğrudan database değerleri geliyor ('Aktif', 'Pasif', 'Taslak', 'Beklemede')
            $query->where('status', $request->status);
        }

        if ($request->has('category')) {
            $query->where('alt_kategori_id', $request->category);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $referansService = app(\App\Services\IlanReferansService::class);

            // İlanReferansService'in searchQuery metodunu kullan
            $searchQuery = $referansService->searchQuery($search);

            // Kullanıcıya ait ilanları filtrele
            $query->whereIn('id', $searchQuery->where('danisman_id', $user->id)->pluck('id'));
        }

        // ✅ N+1 FIX: Select optimization + Eager loading
        $listings = $query->select([
            'id',
            'baslik',
            'fiyat',
            'para_birimi',
            'status',
            'goruntulenme', // ✅ Context7: goruntulenme_sayisi → goruntulenme (database column name)
            'alt_kategori_id',
            'ana_kategori_id',
            'il_id',
            'ilce_id',
            'referans_no',
            'created_at',
            'updated_at',
        ])
            ->with([
                'altKategori:id,name',
                'anaKategori:id,name',
                'il:id,il_adi',
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => IlanInternalResource::collection($listings),
        ]);
    }

    /**
     * Bulk actions (delete, activate, deactivate)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate,draft',
            'ids' => 'required|array',
            'ids.*' => 'exists:ilanlar,id',
        ]);

        $user = Auth::user();
        $action = $request->action;
        $ids = $request->ids;

        // Verify all listings belong to current user
        $listings = Ilan::whereIn('id', $ids)
            ->where('danisman_id', $user->id)
            ->get();

        if ($listings->count() !== count($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Some listings do not belong to you',
            ], 403);
        }

        // Perform action
        switch ($action) {
            case 'delete':
                Ilan::whereIn('id', $ids)->delete();
                $message = count($ids).' listings deleted successfully';
                break;

            case 'activate':
                Ilan::whereIn('id', $ids)->update(['status' => 'Aktif']); // Context7: Database değeri
                $message = count($ids).' listings activated successfully';
                break;

            case 'deactivate':
                Ilan::whereIn('id', $ids)->update(['status' => 'Pasif']); // Context7: Database değeri
                $message = count($ids).' listings deactivated successfully';
                break;

            case 'draft':
                Ilan::whereIn('id', $ids)->update(['status' => 'Taslak']); // Context7: Database değeri
                $message = count($ids).' listings moved to draft';
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action',
                ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Get statistics via AJAX
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        $user = Auth::user();

        // Context7: Status değerleri düzeltildi (active→Aktif, vb.)
        $stats = [
            'total_listings' => Ilan::where('danisman_id', $user->id)->count(),
            'active_listings' => Ilan::where('danisman_id', $user->id)
                ->where('status', 'Aktif')
                ->count(),
            'pending_listings' => Ilan::where('danisman_id', $user->id)
                ->where('status', 'Beklemede')
                ->count(),
            'draft_listings' => Ilan::where('danisman_id', $user->id)
                ->where('status', 'Taslak')
                ->count(),
            'inactive_listings' => Ilan::where('danisman_id', $user->id)
                ->where('status', 'Pasif')
                ->count(),
            'total_views' => Ilan::where('danisman_id', $user->id)
                ->sum('goruntulenme') ?? 0, // ✅ Context7: goruntulenme_sayisi → goruntulenme (database column name)
            'this_month' => Ilan::where('danisman_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Export listings to Excel/PDF
     *
     * Context7 Standardı: C7-MYLISTINGS-EXPORT-2025-11-05
     *
     * GET /admin/my-listings/export?format=excel|pdf
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'nullable|in:excel,pdf',
        ]);

        $user = Auth::user();
        $format = $request->input('format', 'excel');

        // ✅ N+1 FIX: Select optimization + Eager loading
        $listings = Ilan::select([
            'id',
            'baslik',
            'fiyat',
            'para_birimi',
            'status',
            'goruntulenme', // ✅ Context7: goruntulenme_sayisi → goruntulenme (database column name)
            'alt_kategori_id',
            'ana_kategori_id',
            'il_id',
            'ilce_id',
            'referans_no',
            'created_at',
            'updated_at',
        ])
            ->with([
                'altKategori:id,name',
                'anaKategori:id,name',
                'il:id,il_adi',
                'ilce:id,ilce_adi',
            ])
            ->where('danisman_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($format === 'pdf') {
            return $this->exportPdf($listings, $user);
        }

        return $this->exportExcel($listings, $user);
    }

    /**
     * Export to Excel
     */
    protected function exportExcel($listings, $user)
    {
        $data = [
            ['İlanlarım - Excel Raporu'],
            ['Danışman', $user->name],
            ['Email', $user->email],
            ['Tarih', now()->format('d.m.Y H:i')],
            ['Toplam İlan', $listings->count()],
            [''],
            ['ID', 'Referans No', 'Başlık', 'Kategori', 'İl', 'İlçe', 'Fiyat', 'Para Birimi', 'Durum', 'Görüntülenme', 'Oluşturulma Tarihi'],
        ];

        foreach ($listings as $listing) {
            $data[] = [
                $listing->id,
                $listing->referans_no ?? '-',
                $listing->baslik ?? 'Başlıksız',
                $listing->altKategori?->name ?? $listing->anaKategori?->name ?? '-',
                $listing->il?->il_adi ?? '-',
                $listing->ilce?->ilce_adi ?? '-',
                $listing->fiyat ?? 0,
                $listing->para_birimi ?? 'TL',
                $listing->status ?? 'Aktif',
                $listing->goruntulenme ?? 0,
                $listing->created_at?->format('d.m.Y H:i') ?? '-',
            ];
        }

        $dosyaAdi = 'Ilanlarim_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray
        {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }
        }, $dosyaAdi);
    }

    /**
     * Export to PDF
     */
    protected function exportPdf($listings, $user)
    {
        $data = [
            'listings' => $listings,
            'user' => $user,
            'tarih' => now()->format('d.m.Y H:i'),
        ];

        $pdf = Pdf::loadView('admin.ilanlar.exports.my-listings-pdf', $data);

        $dosyaAdi = 'Ilanlarim_'.now()->format('Ymd_His').'.pdf';

        return $pdf->download($dosyaAdi);
    }

    /**
     * API: İlanlarım AI Analizi
     *
     * GET /api/admin/my-listings/ai-analysis
     * ✅ REFACTORED: YalihanCortex kullanılıyor
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiAnalysis(Request $request)
    {
        try {
            $user = Auth::user();
            $options = [
                'days' => $request->input('days', 30),
                'include_recommendations' => $request->input('include_recommendations', true),
            ];

            $result = $this->cortex->analyzeMyListings($user->id, $options);

            return response()->json([
                'success' => $result['success'] ?? false,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'AI analizi başarısız oldu: ' . $e->getMessage(),
            ], 500);
        }
    }
}
