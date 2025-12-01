<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kisi;
use App\Models\KisiEtkilesim;
use App\Models\KisiTask;
use App\Services\CRM\KisiScoringService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CRMDashboardController extends Controller
{
    protected $scoringService;

    public function __construct(KisiScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * CRM Ana Dashboard
     */
    public function index()
    {
        // Bugünün task'ları
        $bugunTasks = KisiTask::with(['kisi', 'kullanici'])
            ->bugun()
            ->bekleyen()
            ->orderBy('oncelik', 'desc')
            ->limit(10)
            ->get();

        // Geciken task'lar
        $gecikenTasks = KisiTask::with(['kisi', 'kullanici'])
            ->gecmis()
            ->orderBy('tarih', 'asc')
            ->limit(5)
            ->get();

        // Pipeline özeti
        $pipelineOzet = Kisi::selectRaw('pipeline_stage, count(*) as total')
            ->where('status', 1)
            ->groupBy('pipeline_stage')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->pipeline_stage => $item->total];
            });

        // Segment özeti
        $segmentOzet = Kisi::selectRaw('segment, count(*) as total')
            ->where('status', 1)
            ->groupBy('segment')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->segment => $item->total];
            });

        // En yüksek skorlu lead'ler
        $topLeads = Kisi::where('status', 1)
            ->where('pipeline_stage', '<', 5)
            ->orderBy('skor', 'desc')
            ->limit(10)
            ->get();

        // Son etkileşimler
        $sonEtkilesimler = KisiEtkilesim::with(['kisi', 'kullanici'])
            ->aktif()
            ->sonEtkilesimler(15)
            ->get();

        // Bu hafta eklenen yeni lead'ler
        $yeniLeadler = Kisi::where('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();

        // Bu ay kazanılan müşteriler
        $kazanilanMusteriler = Kisi::where('pipeline_stage', 5)
            ->whereMonth('updated_at', Carbon::now()->month)
            ->count();

        // Conversion oranı (bu ay)
        $totalLeads = Kisi::whereMonth('created_at', Carbon::now()->month)->count();
        $conversionRate = $totalLeads > 0 ? ($kazanilanMusteriler / $totalLeads) * 100 : 0;

        return view('admin.crm.dashboard', compact(
            'bugunTasks',
            'gecikenTasks',
            'pipelineOzet',
            'segmentOzet',
            'topLeads',
            'sonEtkilesimler',
            'yeniLeadler',
            'kazanilanMusteriler',
            'conversionRate'
        ));
    }

    /**
     * Pipeline Kanban View
     */
    public function pipeline()
    {
        $stages = [
            1 => Kisi::where('pipeline_stage', 1)->where('status', 1)->get(),
            2 => Kisi::where('pipeline_stage', 2)->where('status', 1)->get(),
            3 => Kisi::where('pipeline_stage', 3)->where('status', 1)->get(),
            4 => Kisi::where('pipeline_stage', 4)->where('status', 1)->get(),
            5 => Kisi::where('pipeline_stage', 5)->where('status', 1)->get(),
        ];

        $kaybedilenler = Kisi::where('pipeline_stage', 0)
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->count();

        return view('admin.crm.pipeline', compact('stages', 'kaybedilenler'));
    }

    /**
     * Pipeline stage değiştir (AJAX)
     */
    public function updatePipelineStage(Request $request, Kisi $kisi)
    {
        $validated = $request->validate([
            'stage' => 'required|integer|in:0,1,2,3,4,5',
        ]);

        $kisi->update([
            'pipeline_stage' => $validated['stage'],
            'son_etkilesim' => now(),
        ]);

        // Skorları yeniden hesapla
        $kisi->update(['skor' => $this->scoringService->calculateScore($kisi)]);

        return response()->json([
            'success' => true,
            'message' => 'Pipeline güncellendi',
            'kisi' => $kisi->fresh(),
        ]);
    }

    /**
     * Segment değiştir (AJAX)
     */
    public function updateSegment(Request $request, Kisi $kisi)
    {
        $validated = $request->validate([
            'segment' => 'required|in:potansiyel,aktif,eski,vip',
        ]);

        $kisi->update([
            'segment' => $validated['segment'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Segment güncellendi',
        ]);
    }

    /**
     * Tüm skorları yeniden hesapla
     */
    public function recalculateScores()
    {
        $this->scoringService->recalculateAllScores();

        return redirect()->back()->with('success', 'Tüm skorlar yeniden hesaplandı');
    }

    /**
     * Lead source analizi
     */
    public function leadSourceAnalytics()
    {
        $leadSources = Kisi::selectRaw('lead_source, count(*) as total, avg(skor) as avg_score')
            ->where('status', 1)
            ->whereNotNull('lead_source')
            ->groupBy('lead_source')
            ->get();

        return view('admin.crm.lead-sources', compact('leadSources'));
    }
}
