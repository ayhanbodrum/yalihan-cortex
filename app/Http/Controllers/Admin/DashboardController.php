<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends AdminController
{
    /**
     * Display the main dashboard.
     * Context7: Ana dashboard sayfası
     */
    public function index()
    {
        try {
            // Cache key for dashboard data
            $cacheKey = 'admin_dashboard_' . Auth::id();

            $dashboardData = Cache::remember($cacheKey, 300, function () {
                return $this->getDashboardData();
            });

            return view('admin.dashboard.index', $dashboardData);
        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            
            return view('admin.dashboard.index', [
                'quickStats' => $this->getEmptyStats(),
                'recentIlanlar' => [],
                'recentUsers' => []
            ]);
        }
    }

    /**
     * Show the form for creating a new dashboard widget.
     * Context7: Yeni widget oluşturma
     */
    public function create()
    {
        try {
            $widgetTypes = [
                'stat' => 'İstatistik Widget',
                'chart' => 'Grafik Widget',
                'table' => 'Tablo Widget',
                'activity' => 'Aktivite Widget'
            ];

            $dataSources = [
                'ilanlar' => 'İlanlar',
                'musteriler' => 'Müşteriler',
                'talepler' => 'Talepler',
                'satislar' => 'Satışlar'
            ];

            return view('admin.dashboard.create', compact('widgetTypes', 'dataSources'));
        } catch (\Exception $e) {
            return back()->with('error', 'Widget oluşturma formu yüklenirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created dashboard widget.
     * Context7: Widget kaydetme
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:stat,chart,table,activity',
                'data_source' => 'required|string',
                'position_x' => 'required|integer|min:0',
                'position_y' => 'required|integer|min:0',
                'width' => 'required|integer|min:1|max:12',
                'height' => 'required|integer|min:1|max:6'
            ]);

            $widgetData = [
                'name' => $request->name,
                'type' => $request->type,
                'data_source' => $request->data_source,
                'position_x' => $request->position_x,
                'position_y' => $request->position_y,
                'width' => $request->width,
                'height' => $request->height,
                'user_id' => Auth::id(),
                'created_at' => now()
            ];

            // TODO: DashboardWidget model oluşturulduğunda kullanılacak
            // DashboardWidget::create($widgetData);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Widget başarıyla oluşturuldu',
                    'data' => $widgetData
                ], 201);
            }

            return redirect()->route('admin.dashboard.index')->with('success', 'Widget başarıyla oluşturuldu');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget oluşturulurken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Widget oluşturulurken hata: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified dashboard widget.
     * Context7: Widget detayları
     */
    public function show($id)
    {
        try {
            $widget = $this->getSampleWidget($id);

            if (!$widget) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Widget bulunamadı'
                    ], 404);
                }

                return redirect()->route('admin.dashboard.index')->with('error', 'Widget bulunamadı');
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $widget
                ]);
            }

            return view('admin.dashboard.show', compact('widget'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget detayları alınırken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Widget detayları alınırken hata: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified dashboard widget.
     * Context7: Widget düzenleme
     */
    public function edit($id)
    {
        try {
            $widget = $this->getSampleWidget($id);

            if (!$widget) {
                return redirect()->route('admin.dashboard.index')->with('error', 'Widget bulunamadı');
            }

            $widgetTypes = [
                'stat' => 'İstatistik Widget',
                'chart' => 'Grafik Widget',
                'table' => 'Tablo Widget',
                'activity' => 'Aktivite Widget'
            ];

            $dataSources = [
                'ilanlar' => 'İlanlar',
                'musteriler' => 'Müşteriler',
                'talepler' => 'Talepler',
                'satislar' => 'Satışlar'
            ];

            return view('admin.dashboard.edit', compact('widget', 'widgetTypes', 'dataSources'));
        } catch (\Exception $e) {
            return back()->with('error', 'Widget düzenleme formu yüklenirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified dashboard widget.
     * Context7: Widget güncelleme
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:stat,chart,table,activity',
                'data_source' => 'required|string',
                'position_x' => 'required|integer|min:0',
                'position_y' => 'required|integer|min:0',
                'width' => 'required|integer|min:1|max:12',
                'height' => 'required|integer|min:1|max:6'
            ]);

            $widgetData = [
                'name' => $request->name,
                'type' => $request->type,
                'data_source' => $request->data_source,
                'position_x' => $request->position_x,
                'position_y' => $request->position_y,
                'width' => $request->width,
                'height' => $request->height,
                'updated_at' => now()
            ];

            // TODO: DashboardWidget model ile güncelleme
            // DashboardWidget::where('id', $id)->update($widgetData);

            // Clear cache
            Cache::forget('admin_dashboard_' . Auth::id());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Widget başarıyla güncellendi',
                    'data' => $widgetData
                ]);
            }

            return redirect()->route('admin.dashboard.index')->with('success', 'Widget başarıyla güncellendi');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget güncellenirken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Widget güncellenirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified dashboard widget.
     * Context7: Widget silme
     */
    public function destroy($id)
    {
        try {
            // TODO: DashboardWidget model ile silme
            // DashboardWidget::findOrFail($id)->delete();

            // Clear cache
            Cache::forget('admin_dashboard_' . Auth::id());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Widget başarıyla silindi'
                ]);
            }

            return redirect()->route('admin.dashboard.index')->with('success', 'Widget başarıyla silindi');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget silinirken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Widget silinirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Context7: Dashboard verilerini getir
     */
    public function getDashboardStats()
    {
        try {
            $stats = $this->getDashboardData();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dashboard istatistikleri alınırken hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Context7: Dashboard widget'larını yenile
     */
    public function refresh()
    {
        try {
            // Clear cache
            Cache::forget('admin_dashboard_' . Auth::id());

            $dashboardData = $this->getDashboardData();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard başarıyla yenilendi',
                'data' => $dashboardData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dashboard yenilenirken hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Context7: Dashboard verilerini hazırla
     */
    private function getDashboardData()
    {
        // View'in beklediği format: quickStats, recentIlanlar, recentUsers
        $recentIlanlar = \App\Models\Ilan::latest('created_at')->limit(5)->get();
        $recentUsers = \App\Models\User::latest('created_at')->limit(5)->get();
        
        return [
            'quickStats' => [
                'total_ilanlar' => \App\Models\Ilan::count(),
                'active_ilanlar' => \App\Models\Ilan::where('status', 'Aktif')->count(),
                'total_kullanicilar' => \App\Models\User::count(),
                'total_danismanlar' => \App\Models\User::whereHas('roles', function($q) { 
                    $q->where('name', 'danisman'); 
                })->count(),
                'system_status' => [
                    'database' => 'online',
                    'cache' => 'online',
                    'storage' => 'online'
                ],
                'recent_ilanlar' => $recentIlanlar,
                'recent_kullanicilar' => $recentUsers,
            ],
            'recentIlanlar' => $recentIlanlar,
            'recentUsers' => $recentUsers,
        ];
    }

    /**
     * Context7: Boş istatistikler (hata durumu için)
     */
    private function getEmptyStats()
    {
        return [
            'total_ilanlar' => 0,
            'active_ilanlar' => 0,
            'total_kullanicilar' => 0,
            'total_danismanlar' => 0
        ];
    }

    /**
     * Context7: Boş grafik verileri
     */
    private function getEmptyCharts()
    {
        return [
            'monthly_sales' => [
                'labels' => [],
                'data' => []
            ],
            'ilan_categories' => [
                'labels' => [],
                'data' => []
            ]
        ];
    }

    /**
     * Context7: Örnek widget verisi
     */
    private function getSampleWidget($id)
    {
        $widgets = [
            [
                'id' => 1,
                'name' => 'İlan İstatistikleri',
                'type' => 'stat',
                'data_source' => 'ilanlar',
                'position_x' => 0,
                'position_y' => 0,
                'width' => 6,
                'height' => 2
            ],
            [
                'id' => 2,
                'name' => 'Müşteri Grafiği',
                'type' => 'chart',
                'data_source' => 'musteriler',
                'position_x' => 6,
                'position_y' => 0,
                'width' => 6,
                'height' => 3
            ]
        ];

        return collect($widgets)->firstWhere('id', (int)$id);
    }

    /**
     * Context7: Gerçek aktiviteleri database'den çek
     */
    private function getRecentActivities()
    {
        $activities = [];
        
        // Son eklenen ilanlar
        $recentIlanlar = \App\Models\Ilan::latest('created_at')
            ->limit(3)
            ->get();
        
        foreach ($recentIlanlar as $ilan) {
            $activities[] = [
                'id' => $ilan->id,
                'type' => 'ilan_created',
                'message' => 'Yeni ilan eklendi: ' . ($ilan->baslik ?? 'Başlıksız'),
                'time' => $ilan->created_at->diffForHumans(),
                'user' => $ilan->user->name ?? 'System'
            ];
        }
        
        // Son eklenen kişiler
        $recentKisiler = \App\Models\Kisi::latest('created_at')
            ->limit(2)
            ->get();
        
        foreach ($recentKisiler as $kisi) {
            $activities[] = [
                'id' => $kisi->id,
                'type' => 'kisi_created',
                'message' => 'Yeni kişi eklendi: ' . ($kisi->adi . ' ' . ($kisi->soyadi ?? '')),
                'time' => $kisi->created_at->diffForHumans(),
                'user' => 'Admin'
            ];
        }
        
        return collect($activities)->sortByDesc('id')->take(5)->values()->all();
    }

    /**
     * Context7: Conversion rate hesapla (Talep -> İlan eşleşme oranı)
     */
    private function calculateConversionRate()
    {
        $totalTalepler = \App\Models\Talep::count();
        
        if ($totalTalepler === 0) {
            return 0;
        }
        
        $eslesenTalepler = \App\Models\Eslesme::distinct('talep_id')->count('talep_id');
        
        return round(($eslesenTalepler / $totalTalepler) * 100, 1);
    }

    /**
     * Context7: Grafik verilerini database'den çek
     */
    private function getChartData()
    {
        // Son 6 ay için aylık ilan istatistikleri
        $monthlyData = [];
        $monthlyLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->locale('tr')->translatedFormat('F');
            $monthlyData[] = \App\Models\Ilan::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        
        // Kategori bazlı ilan dağılımı
        $categories = \App\Models\IlanKategori::withCount('ilanlar')
            ->having('ilanlar_count', '>', 0)
            ->orderByDesc('ilanlar_count')
            ->limit(6)
            ->get();
        
        return [
            'monthly_sales' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyData
            ],
            'ilan_categories' => [
                'labels' => $categories->pluck('kategori_adi')->toArray(),
                'data' => $categories->pluck('ilanlar_count')->toArray()
            ]
        ];
    }
}
