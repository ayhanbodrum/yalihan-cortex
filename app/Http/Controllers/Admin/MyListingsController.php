<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\IlanInternalResource;
use App\Models\Ilan;
use App\Models\IlanKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MyListingsController extends AdminController
{
    /**
     * Display user's own listings (İlanlarım sayfası)
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get current user
        $user = Auth::user();
        
        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login first');
        }
        
        // Query user's listings with eager loading
        $query = Ilan::query()
            ->where('danisman_id', $user->id)
            ->orderBy('updated_at', 'desc');
        
        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
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
        
        // Paginate first, then eager load (more efficient)
        $listings = $query->paginate(20);
        
        // Eager load relationships after pagination
        $listings->load([
            'altKategori' => function($query) {
                $query->select('id', 'name', 'icon');
            },
            'anaKategori' => function($query) {
                $query->select('id', 'name');
            },
            'il' => function($query) {
                $query->select('id', 'il_adi');
            },
            'ilce' => function($query) {
                $query->select('id', 'ilce_adi');
            }
        ]);
        
        // Calculate statistics
        $stats = [
            'total_listings' => Ilan::where('danisman_id', $user->id)->count(),
            'active_listings' => Ilan::where('danisman_id', $user->id)
                                     ->where('status', 'active')
                                     ->count(),
            'pending_listings' => Ilan::where('danisman_id', $user->id)
                                      ->where('status', 'pending')
                                      ->count(),
            'total_views' => Ilan::where('danisman_id', $user->id)
                                 ->sum('goruntulenme') ?? 0,
        ];
        
        // Get categories for filter (subcategories only)
        $categories = IlanKategori::select('id', 'name', 'icon')
                                  ->whereNotNull('parent_id')
                                  ->orderBy('name')
                                  ->get();
        
        return view('admin.ilanlar.my-listings', compact('listings', 'stats', 'categories'));
    }
    
    /**
     * Search listings via AJAX
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        
        $query = Ilan::with(['altKategori:id,name', 'il:id,il_adi'])
                     ->where('danisman_id', $user->id);
        
        if ($request->has('status')) {
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
        
        $listings = $query->orderBy('updated_at', 'desc')
                         ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => IlanInternalResource::collection($listings)
        ]);
    }
    
    /**
     * Bulk actions (delete, activate, deactivate)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate,draft',
            'ids' => 'required|array',
            'ids.*' => 'exists:ilanlar,id'
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
                'message' => 'Unauthorized: Some listings do not belong to you'
            ], 403);
        }
        
        // Perform action
        switch ($action) {
            case 'delete':
                Ilan::whereIn('id', $ids)->delete();
                $message = count($ids) . ' listings deleted successfully';
                break;
                
            case 'activate':
                Ilan::whereIn('id', $ids)->update(['status' => 'active']);
                $message = count($ids) . ' listings activated successfully';
                break;
                
            case 'deactivate':
                Ilan::whereIn('id', $ids)->update(['status' => 'inactive']);
                $message = count($ids) . ' listings deactivated successfully';
                break;
                
            case 'draft':
                Ilan::whereIn('id', $ids)->update(['status' => 'draft']);
                $message = count($ids) . ' listings moved to draft';
                break;
                
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action'
                ], 400);
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
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
        
        $stats = [
            'total_listings' => Ilan::where('danisman_id', $user->id)->count(),
            'active_listings' => Ilan::where('danisman_id', $user->id)
                                     ->where('status', 'active')
                                     ->count(),
            'pending_listings' => Ilan::where('danisman_id', $user->id)
                                      ->where('status', 'pending')
                                      ->count(),
            'draft_listings' => Ilan::where('danisman_id', $user->id)
                                    ->where('status', 'draft')
                                    ->count(),
            'inactive_listings' => Ilan::where('danisman_id', $user->id)
                                       ->where('status', 'inactive')
                                       ->count(),
            'total_views' => Ilan::where('danisman_id', $user->id)
                                 ->sum('goruntulenme') ?? 0,
            'this_month' => Ilan::where('danisman_id', $user->id)
                                ->whereMonth('created_at', now()->month)
                                ->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Export listings to Excel/PDF
     * 
     * Context7 Standardı: C7-MYLISTINGS-EXPORT-2025-11-05
     * 
     * GET /admin/my-listings/export?format=excel|pdf
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'nullable|in:excel,pdf'
        ]);

        $user = Auth::user();
        $format = $request->input('format', 'excel');
        
        $listings = Ilan::with(['altKategori:id,name', 'anaKategori:id,name', 'il:id,il_adi', 'ilce:id,ilce_adi'])
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

        $dosyaAdi = "Ilanlarim_" . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
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
        
        $dosyaAdi = "Ilanlarim_" . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($dosyaAdi);
    }
}
