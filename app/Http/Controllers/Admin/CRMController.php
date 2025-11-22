<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CRMController extends AdminController
{
    /**
     * CRM Dashboard
     * Context7: admin.crm.dashboard route
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Context7: CRM dashboard stats
        $stats = [
            'total_customers' => \App\Models\Kisi::count(),
            'active_customers' => \App\Models\Kisi::where('status', 'Aktif')->count(),
            'pending_followups' => 0, // TODO: Implement followup tracking
            'recent_activities' => [],
        ];

        return view('admin.crm.dashboard', compact('stats'));
    }

    /**
     * Customers Index
     * Context7: admin.crm.customers.index route
     * 
     * @return \Illuminate\View\View
     */
    public function customers()
    {
        return redirect()->route('admin.kisiler.index');
    }

    /**
     * Create Customer
     * Context7: admin.crm.customers.create route
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return redirect()->route('admin.kisiler.create');
    }

    /**
     * Store Customer
     * Context7: admin.crm.customers.store route
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.kisiler.store');
    }

    /**
     * Show Customer
     * Context7: admin.crm.customers.show route
     * 
     * @param mixed $customer
     * @return \Illuminate\View\View
     */
    public function show($customer)
    {
        return redirect()->route('admin.kisiler.show', $customer);
    }

    /**
     * Edit Customer
     * Context7: admin.crm.customers.edit route
     * 
     * @param mixed $customer
     * @return \Illuminate\View\View
     */
    public function edit($customer)
    {
        return redirect()->route('admin.kisiler.edit', $customer);
    }

    /**
     * Update Customer
     * Context7: admin.crm.customers.update route
     * 
     * @param Request $request
     * @param mixed $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $customer)
    {
        return redirect()->route('admin.kisiler.update', $customer);
    }

    /**
     * Add Note to Customer
     * Context7: admin.crm.customers.notes.add route
     * 
     * @param Request $request
     * @param mixed $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function addNote(Request $request, $customer)
    {
        // TODO: Implement note adding
        return response()->json(['success' => false, 'message' => 'Not implemented yet'], 501);
    }

    /**
     * Add Activity to Customer
     * Context7: admin.crm.customers.activities.add route
     * 
     * @param Request $request
     * @param mixed $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function addActivity(Request $request, $customer)
    {
        // TODO: Implement activity adding
        return response()->json(['success' => false, 'message' => 'Not implemented yet'], 501);
    }

    /**
     * Update Follow Up
     * Context7: admin.crm.customers.followup.update route
     * 
     * @param Request $request
     * @param mixed $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFollowUp(Request $request, $customer)
    {
        // TODO: Implement followup update
        return response()->json(['success' => false, 'message' => 'Not implemented yet'], 501);
    }

    /**
     * AI Analysis for Customer
     * Context7: admin.crm.customers.ai-analysis route
     * 
     * @param Request $request
     * @param mixed $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function aiAnalysis(Request $request, $customer)
    {
        // TODO: Implement AI analysis
        return response()->json(['success' => false, 'message' => 'Not implemented yet'], 501);
    }

    /**
     * Purchase Prediction for Customer
     * Context7: admin.crm.customers.purchase-prediction route
     * 
     * @param Request $request
     * @param mixed $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchasePrediction(Request $request, $customer)
    {
        // TODO: Implement purchase prediction
        return response()->json(['success' => false, 'message' => 'Not implemented yet'], 501);
    }

    /**
     * AI Analyze
     * Context7: admin.crm.ai-analyze route
     * 
     * @return \Illuminate\View\View
     */
    public function aiAnalyze()
    {
        // TODO: Implement AI analyze page
        return view('admin.crm.ai-analyze');
    }

    /**
     * Fix Duplicates
     * Context7: admin.crm.fix-duplicates route
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fixDuplicates(Request $request)
    {
        // TODO: Implement duplicate fixing
        return response()->json(['success' => false, 'message' => 'Not implemented yet'], 501);
    }

    /**
     * Generate Report
     * Context7: admin.crm.generate-report route
     * 
     * @return \Illuminate\Http\Response
     */
    public function generateReport()
    {
        // TODO: Implement report generation
        return response('Not implemented yet', 501);
    }

    /**
     * Risk Score for Customer
     * Context7: admin.crm.customers.risk-score route
     * 
     * @param Request $request
     * @param mixed $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function riskScore(Request $request, $customer)
    {
        // TODO: Implement risk score calculation
        return response()->json(['success' => false, 'message' => 'Not implemented yet'], 501);
    }

    /**
     * Follow Up Suggestions for Customer
     * Context7: admin.crm.customers.followup-suggestions route
     * 
     * @param Request $request
     * @param mixed $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function followUpSuggestions(Request $request, $customer)
    {
        // TODO: Implement followup suggestions
        return response()->json(['success' => false, 'message' => 'Not implemented yet'], 501);
    }

    /**
     * Suggest Tags for Customer
     * Context7: admin.crm.customers.suggest-tags route
     * 
     * @param Request $request
     * @param mixed $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestTags(Request $request, $customer)
    {
        // TODO: Implement tag suggestions
        return response()->json(['success' => false, 'message' => 'Not implemented yet'], 501);
    }

    public function publicationTypeChanges(Request $request)
    {
        $data = Cache::get('crm:publication_type_changes', []);
        $kategoriId = (int) $request->query('kategoriId', 0);
        $from = $request->query('from');
        $to = $request->query('to');
        $filtered = collect($data)->filter(function ($x) use ($kategoriId, $from, $to) {
            if ($kategoriId && (int) ($x['kategori_id'] ?? 0) !== $kategoriId) {
                return false;
            }
            if ($from) {
                try {
                    $f = Carbon::parse($from);
                    $xts = isset($x['ts']) ? Carbon::parse($x['ts']) : null;
                    if ($xts && $xts->lt($f)) {
                        return false;
                    }
                } catch (\Throwable $e) {
                }
            }
            if ($to) {
                try {
                    $t = Carbon::parse($to);
                    $xts = isset($x['ts']) ? Carbon::parse($x['ts']) : null;
                    if ($xts && $xts->gt($t)) {
                        return false;
                    }
                } catch (\Throwable $e) {
                }
            }
            return true;
        })->values();
        $payload = ['data' => $filtered];
        $etag = sha1(json_encode($payload));
        if ($request->headers->get('If-None-Match') === $etag) {
            return response('', 304)->header('ETag', $etag);
        }
        return response()->json($payload, 200)->header('ETag', $etag);
    }

    public function publicationTypeChangesPage(Request $request)
    {
        return view('admin.crm.publication-type-changes');
    }

    public function publicationTypeChangesCsv(Request $request)
    {
        $data = Cache::get('crm:publication_type_changes', []);
        $kategoriId = (int) $request->query('kategoriId', 0);
        $from = $request->query('from');
        $to = $request->query('to');
        $filtered = collect($data)->filter(function ($x) use ($kategoriId, $from, $to) {
            if ($kategoriId && (int) ($x['kategori_id'] ?? 0) !== $kategoriId) {
                return false;
            }
            if ($from) {
                try {
                    $f = Carbon::parse($from);
                    $xts = isset($x['ts']) ? Carbon::parse($x['ts']) : null;
                    if ($xts && $xts->lt($f)) {
                        return false;
                    }
                } catch (\Throwable $e) {
                }
            }
            if ($to) {
                try {
                    $t = Carbon::parse($to);
                    $xts = isset($x['ts']) ? Carbon::parse($x['ts']) : null;
                    if ($xts && $xts->gt($t)) {
                        return false;
                    }
                } catch (\Throwable $e) {
                }
            }
            return true;
        })->values();
        $rows = [
            ['type','kategori_id','from_id','to_id','deleted_id','affected','ts'],
        ];
        foreach ($filtered as $x) {
            $rows[] = [
                $x['type'] ?? '',
                $x['kategori_id'] ?? '',
                $x['from_id'] ?? '',
                $x['to_id'] ?? '',
                $x['deleted_id'] ?? '',
                $x['affected'] ?? '',
                $x['ts'] ?? '',
            ];
        }
        $csv = '';
        foreach ($rows as $r) {
            $csv .= implode(',', array_map(function ($v) { return str_replace(["\n","\r",','],' ', (string) $v); }, $r)) . "\n";
        }
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="publication-type-changes.csv"',
        ]);
    }

    public function ilanlarExportCsv(Request $request)
    {
        $kategoriId = (int) $request->query('kategoriId', 0);
        $yayinTipiId = (int) $request->query('yayinTipiId', 0);
        $q = DB::table('ilanlar');
        if ($kategoriId && Schema::hasColumn('ilanlar', 'kategori_id')) {
            $q->where('kategori_id', $kategoriId);
        }
        if ($yayinTipiId && Schema::hasColumn('ilanlar', 'yayin_tipi_id')) {
            $q->where('yayin_tipi_id', $yayinTipiId);
        }
        $cols = [];
        foreach (['id','baslik','fiyat','para_birimi','status','kategori_id','yayin_tipi_id','created_at'] as $c) {
            if (Schema::hasColumn('ilanlar', $c)) {
                $cols[] = $c;
            }
        }
        if (!empty($cols)) {
            $q->select($cols);
        }
        $rows = $q->limit(10000)->get();
        $header = !empty($cols) ? $cols : array_keys((array) ($rows->first() ?? []));
        $csvRows = [$header];
        foreach ($rows as $r) {
            $line = [];
            foreach ($header as $h) {
                $line[] = str_replace(["\n","\r",','],' ', (string) ($r->$h ?? ''));
            }
            $csvRows[] = $line;
        }
        $csv = '';
        foreach ($csvRows as $r) {
            $csv .= implode(',', $r) . "\n";
        }
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ilanlar.csv"',
        ]);
    }

    public function ilanlarJson(Request $request)
    {
        $id = (int) $request->query('id', 0);
        $kategoriId = (int) $request->query('kategoriId', 0);
        $yayinTipiId = (int) $request->query('yayinTipiId', 0);
        $status = (string) $request->query('status', '');
        $q = (string) $request->query('q', '');
        $minFiyat = $request->query('minFiyat');
        $maxFiyat = $request->query('maxFiyat');
        $sort = (string) $request->query('sort', 'id:desc');
        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('perPage', 25)));
        $cacheKey = 'crm:ilanlar_json:' . md5(http_build_query($request->query()));
        $payload = Cache::remember($cacheKey, now()->addSeconds(15), function () use ($id, $kategoriId, $yayinTipiId, $status, $q, $minFiyat, $maxFiyat, $sort, $page, $perPage) {
            $builder = DB::table('ilanlar');
            if ($id) {
                $builder->where('id', $id);
            }
            if ($kategoriId && Schema::hasColumn('ilanlar', 'kategori_id')) {
                $builder->where('kategori_id', $kategoriId);
            }
            if ($yayinTipiId && Schema::hasColumn('ilanlar', 'yayin_tipi_id')) {
                $builder->where('yayin_tipi_id', $yayinTipiId);
            }
        if ($status && Schema::hasColumn('ilanlar', 'status')) {
            $builder->where('status', $status);
        }
        if ($q) {
            if (Schema::hasColumn('ilanlar', 'baslik')) {
                $builder->where('baslik', 'like', '%'.$q.'%');
            }
        }
        if (Schema::hasColumn('ilanlar', 'fiyat')) {
            if ($minFiyat !== null && $minFiyat !== '') {
                $builder->where('fiyat', '>=', (float) $minFiyat);
            }
            if ($maxFiyat !== null && $maxFiyat !== '') {
                $builder->where('fiyat', '<=', (float) $maxFiyat);
            }
        }
        $allowedSorts = ['id','fiyat','created_at'];
        $parts = explode(':', $sort);
        $sortCol = $parts[0] ?? 'id';
        $sortDir = strtolower($parts[1] ?? 'desc');
        if (!in_array($sortCol, $allowedSorts, true)) {
            $sortCol = 'id';
        }
        if (!in_array($sortDir, ['asc','desc'], true)) {
            $sortDir = 'desc';
        }
            $total = (clone $builder)->count();
            $allowed = ['id','baslik','fiyat','para_birimi','status','kategori_id','yayin_tipi_id','created_at'];
            $select = [];
            foreach ($allowed as $c) {
                if (Schema::hasColumn('ilanlar', $c)) {
                    $select[] = $c;
                }
            }
            if (!empty($select)) {
                $builder->select($select);
            }
            $items = $builder->orderBy($sortCol, $sortDir)->forPage($page, $perPage)->get();
            return [
                'data' => $items,
                'meta' => [
                    'page' => $page,
                    'perPage' => $perPage,
                    'total' => $total,
                    'totalPages' => $perPage ? (int) ceil($total / $perPage) : 1,
                ],
            ];
        });
        $etag = sha1(json_encode($payload));
        if ($request->headers->get('If-None-Match') === $etag) {
            return response('', 304)->header('ETag', $etag);
        }
        return response()->json($payload, 200)->header('ETag', $etag);
    }

    public function ilanlarPage(Request $request)
    {
        return view('admin.crm.ilanlar-index');
    }

    public function ilanDetayPage($id)
    {
        return view('admin.crm.ilan-detay');
    }
}
