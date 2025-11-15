<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\DashboardWidgetRequest;
use App\Models\DashboardWidget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\Cache\CacheHelper;
use App\Services\Response\ResponseService;
use App\Services\Logging\LogService;
use Illuminate\Support\Facades\DB;

class DashboardController extends AdminController
{
    /**
     * Display the main dashboard.
     * Context7: Ana dashboard sayfası
     *
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(): \Illuminate\View\View
    {
        try {
            // ✅ STANDARDIZED: Using CacheHelper with user-specific cache
            $dashboardData = CacheHelper::remember('dashboard', 'data', 'short', function () {
                return $this->getDashboardData();
            });

            return view('admin.dashboard.index', $dashboardData);
        } catch (\Exception $e) {
            // ✅ STANDARDIZED: Using LogService
            LogService::error('Dashboard error', [], $e);

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
     *
     * @return \Illuminate\View\View
     * @throws \Exception
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
     *
     * @param DashboardWidgetRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(DashboardWidgetRequest $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            // ✅ STANDARDIZED: Using Form Request
            $validated = $request->validated();

            // ✅ DashboardWidget model kullanımı
            $widget = DashboardWidget::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'data_source' => $validated['data_source'],
                'position_x' => $validated['position_x'],
                'position_y' => $validated['position_y'],
                'width' => $validated['width'],
                'height' => $validated['height'],
                'user_id' => Auth::id(),
                'settings' => $validated['settings'] ?? null,
            ]);

            // Clear cache
            CacheHelper::forget('dashboard', 'data', ['user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Widget başarıyla oluşturuldu',
                    'data' => $widget
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
     *
     * @param int|string $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function show($id)
    {
        try {
            // ✅ DashboardWidget model kullanımı
            $widget = DashboardWidget::forUser(Auth::id())->findOrFail($id);

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
     *
     * @param int|string $id
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit($id)
    {
        try {
            // ✅ DashboardWidget model kullanımı
            $widget = DashboardWidget::forUser(Auth::id())->findOrFail($id);

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
     *
     * @param DashboardWidgetRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(DashboardWidgetRequest $request, int $id): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            // ✅ STANDARDIZED: Using Form Request
            $validated = $request->validated();

            // ✅ DashboardWidget model kullanımı
            $widget = DashboardWidget::forUser(Auth::id())->findOrFail($id);

            $widget->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'data_source' => $validated['data_source'],
                'position_x' => $validated['position_x'],
                'position_y' => $validated['position_y'],
                'width' => $validated['width'],
                'height' => $validated['height'],
                'settings' => $validated['settings'] ?? $widget->settings,
            ]);

            // Clear cache
            // ✅ STANDARDIZED: Using CacheHelper
            CacheHelper::forget('dashboard', 'data', ['user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Widget başarıyla güncellendi',
                    'data' => $widget
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
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            // ✅ DashboardWidget model kullanımı
            $widget = DashboardWidget::forUser(Auth::id())->findOrFail($id);
            $widget->delete();

            // Clear cache
            // ✅ STANDARDIZED: Using CacheHelper
            CacheHelper::forget('dashboard', 'data', ['user_id' => Auth::id()]);

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
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
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
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function refresh()
    {
        try {
            // Clear cache
            // ✅ STANDARDIZED: Using CacheHelper
            CacheHelper::forget('dashboard', 'data', ['user_id' => Auth::id()]);

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
     *
     * @return array
     */
    private function getDashboardData()
    {
        // View'in beklediği format: quickStats, recentIlanlar, recentUsers
        // ✅ EAGER LOADING: Prevent N+1 queries
        $recentIlanlar = \App\Models\Ilan::with([
            'ilanSahibi:id,ad,soyad',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'kategori:id,name'
        ])
        ->select(['id', 'baslik', 'ilan_basligi', 'fiyat', 'para_birimi', 'status',
                  'ilan_sahibi_id', 'il_id', 'ilce_id', 'kategori_id', 'created_at'])
        ->latest('created_at')
        ->limit(5)
        ->get();

        // ✅ EAGER LOADING: User relationships
        $recentUsers = \App\Models\User::with(['roles:id,name'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        return [
            'quickStats' => [
                'total_ilanlar' => \App\Models\Ilan::count(),
                'active_ilanlar' => \App\Models\Ilan::where('status', true)->count(),
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
     *
     * @return array
     */
    private function getEmptyStats()
    {
        return [
            'total_ilanlar' => 0,
            'active_ilanlar' => 0,
            'total_kullanicilar' => 0,
            'total_danismanlar' => 0,
            'system_status' => [
                'database' => 'unknown',
                'cache' => 'unknown',
                'storage' => 'unknown'
            ],
            'recent_ilanlar' => collect([]),
            'recent_kullanicilar' => collect([]),
        ];
    }

    /**
     * Context7: Boş grafik verileri
     *
     * @return array
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
     *
     * @param int|string $id
     * @return array|null
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
     *
     * @return array
     */
    private function getRecentActivities()
    {
        $activities = [];

        // Son eklenen ilanlar
        // ✅ PERFORMANCE FIX: N+1 query önlendi - Eager loading eklendi
        $recentIlanlar = \App\Models\Ilan::with([
            'danisman:id,name',
            'ilanSahibi:id,ad,soyad'
        ])
        ->select(['id', 'baslik', 'danisman_id', 'created_at'])
        ->latest('created_at')
        ->limit(3)
        ->get();

        foreach ($recentIlanlar as $ilan) {
            $activities[] = [
                'id' => $ilan->id,
                'type' => 'ilan_created',
                'message' => 'Yeni ilan eklendi: ' . ($ilan->baslik ?? 'Başlıksız'),
                'time' => $ilan->created_at->diffForHumans(),
                'user' => $ilan->danisman->name ?? 'System'
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
     *
     * @return float
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
     *
     * @return array
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
