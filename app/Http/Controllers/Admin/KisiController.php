<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Modules\Crm\Models\Kisi;

class KisiController extends AdminController
{
    public function index(Request $request)
    {
        // Context7 uyumlu sorgu oluşturma
        $query = Kisi::with(['danisman', 'il', 'ilce', 'mahalle']);

        // Context7 uyumlu arama
        $search = trim((string) $request->get('q'));
        if ($search !== '') {
            $query->search($search); // Model scope kullanımı
        }

        // Context7 uyumlu status filtreleme
        $status = $request->get('status');
        if ($status === 'Aktif') {
            $query->where('status', true); // Context7 uyumlu
        } elseif ($status === 'Pasif') {
            $query->pasif(); // Model scope kullanımı
        }

        // Danışman filtresi (Context7 uyumlu)
        $danismanId = $request->get('danisman_id');
        if ($danismanId) {
            $query->byDanisman($danismanId); // Model scope kullanımı
        }

        // Kişi tipi filtresi (Context7: kisi_tipi, not musteri_tipi)
        $kisiTipi = $request->get('kisi_tipi');
        if ($kisiTipi) {
            $query->where('kisi_tipi', $kisiTipi);
        }

        // Context7 uyumlu sıralama
        $sort = $request->get('sort');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('ad')->orderBy('soyad');
                break;
            case 'name_desc':
                $query->orderByDesc('ad')->orderByDesc('soyad');
                break;
            case 'created_asc':
                $query->orderBy('created_at');
                break;
            case 'crm_score_desc':
                // CRM skoruna göre sıralama (Context7 özelliği)
                $query->orderByRaw('(
                    CASE WHEN ad IS NOT NULL AND soyad IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN telefon IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN email IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN tc_kimlik IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN il_id IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN ilce_id IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN mahalle_id IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN musteri_tipi IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN meslek IS NOT NULL THEN 10 ELSE 0 END +
                    CASE WHEN gelir_duzeyi IS NOT NULL THEN 10 ELSE 0 END
                ) DESC');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        $kisiler = $query->paginate(20)->withQueryString();

        // Context7 uyumlu istatistikler (view'da $stats olarak kullanılıyor)
        $stats = [
            'total' => Kisi::count(),
            'active' => Kisi::where('status', true)->count(), // Context7 uyumlu
            'pasif' => Kisi::pasif()->count(),
            'taslak' => Kisi::where('status', false)->count(), // Pasif kişi sayısı
            'ev_sahibi' => Kisi::byMusteriTipi('ev_sahibi')->count(),
            'satici' => Kisi::byMusteriTipi('satici')->count(),
            'alici' => Kisi::byMusteriTipi('alici')->count(),
            'kiraci' => Kisi::byMusteriTipi('kiraci')->count(),
        ];

        // Backward compatibility
        $istatistikler = $stats;
        $taslak = $stats['taslak'];

        // Context7 uyumlu mükerrer kontrolü
        $olasiKopyalar = [];
        if ($search === '') {
            $olasiKopyalar = Kisi::select('email')
                ->whereNotNull('email')
                ->groupBy('email')
                ->havingRaw('COUNT(*) > 1')
                ->limit(5)
                ->pluck('email')
                ->toArray();
        }

        $filters = ['q' => $search, 'status' => $status, 'sort' => $sort, 'danisman_id' => $danismanId, 'kisi_tipi' => $kisiTipi]; // Context7: kisi_tipi

        // Context7: View için değişkenler
        $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->get();

        if (view()->exists('admin.kisiler.index')) {
            return response()->view('admin.kisiler.index', compact('kisiler', 'filters', 'stats', 'istatistikler', 'olasiKopyalar', 'taslak', 'danismanlar'));
        }
        return $this->renderAny(['Crm::musteriler.index'], compact('kisiler', 'filters', 'istatistikler'));
    }

    public function create()
    {
        $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->get();
        $iller = \App\Models\Il::orderBy('il_adi')->get();
        $musteriTipleri = ['ev_sahibi', 'satici', 'alici', 'kiraci'];
        $kaynaklar = ['Web', 'Telefon', 'Referans', 'Sosyal Medya', 'Diğer'];

        return $this->renderAny(['admin.kisiler.create', 'Crm::kisiler.create'], compact('danismanlar', 'iller', 'musteriTipleri', 'kaynaklar'));
    }

    public function store(Request $request)
    {
        // Context7 uyumlu validasyon (mahalle_id added - 2025-10-31, adres_detay fixed - 2025-11-01)
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'soyad' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tc_kimlik' => 'nullable|string|max:11|min:11',
            'status' => 'required|string|in:Aktif,Pasif,Beklemede',
            'kisi_tipi' => 'nullable|string|in:Müşteri,Potansiyel,Tedarikçi,Yatırımcı,Ev Sahibi,Alıcı,Kiracı,Satıcı',
            'danisman_id' => 'nullable|exists:users,id',
            'il_id' => 'nullable|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'adres_detay' => 'nullable|string|max:500', // Context7: adres → adres_detay (database column name)
            'notlar' => 'nullable|string',
        ]);

        try {
            // Context7 uyumlu kişi oluşturma
            $kisi = Kisi::create($validated);

            return redirect()
                ->route('admin.kisiler.index')
                ->with('success', $kisi->ad . ' ' . $kisi->soyad . ' başarıyla eklendi! ✅');
        } catch (\Exception $e) {
            \Log::error('Kişi oluşturma hatası:', [
                'error' => $e->getMessage(),
                'validated' => $validated ?? null
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Kişi eklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function show($kisi)
    {
        return $this->renderAny(['admin.kisiler.show', 'Crm::musteriler.show'], ['kisi' => $this->resolve($kisi)]);
    }

    public function edit($kisi)
    {
        $kisi = $this->resolve($kisi);
        $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->get();
        $iller = \App\Models\Il::orderBy('il_adi')->get();
        $ilceler = $kisi->il_id ? \App\Models\Ilce::where('il_id', $kisi->il_id)->orderBy('ilce_adi')->get() : [];
        $mahalleler = $kisi->ilce_id ? \App\Models\Mahalle::where('ilce_id', $kisi->ilce_id)->orderBy('mahalle_adi')->get() : [];
        $musteriTipleri = ['ev_sahibi', 'satici', 'alici', 'kiraci'];
        $etiketler = \App\Models\Etiket::orderBy('name')->get(); // Context7: Etiketler eklendi
        $kaynaklar = ['Web', 'Telefon', 'Referans', 'Sosyal Medya', 'Diğer'];

        // Kişinin mevcut etiket ID'lerini al
        $kisiEtiketIds = $kisi->etiketler ? $kisi->etiketler->pluck('id')->toArray() : [];

        return $this->renderAny(['admin.kisiler.edit', 'Crm::musteriler.edit'], [
            'kisi' => $kisi,
            'danismanlar' => $danismanlar,
            'iller' => $iller,
            'ilceler' => $ilceler,
            'mahalleler' => $mahalleler,
            'musteriTipleri' => $musteriTipleri,
            'etiketler' => $etiketler,
            'kaynaklar' => $kaynaklar,
            'kisiEtiketIds' => $kisiEtiketIds
        ]);
    }

    public function update(Request $request, Kisi $kisi)
    {
        // Context7: Validation (mahalle_id added - 2025-10-31, adres_detay fixed - 2025-11-01)
        $validated = $request->validate([
            'ad' => 'required|string|max:100',
            'soyad' => 'required|string|max:100',
            'telefon' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'kisi_tipi' => 'required|in:Müşteri,Potansiyel,Tedarikçi,Yatırımcı,Ev Sahibi,Alıcı,Kiracı,Satıcı',
            'danisman_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Aktif,Pasif,Beklemede',
            'il_id' => 'nullable|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'adres_detay' => 'nullable|string|max:500', // Context7: adres → adres_detay (database column name)
            'notlar' => 'nullable|string',
            'etiketler_ids' => 'nullable|array',
            'etiketler_ids.*' => 'exists:etiketler,id',
        ]);

        try {
            // Remove non-existent fields
            $updateData = collect($validated)->except(['etiketler_ids'])->toArray();
            
            // Update kişi
            $kisi->update($updateData);
            
            // Sync etiketler (tags) if provided
            if ($request->has('etiketler_ids')) {
                $kisi->etiketler()->sync($request->etiketler_ids);
            }

            return redirect()
                ->route('admin.kisiler.edit', $kisi->id)
                ->with('success', $kisi->ad . ' ' . $kisi->soyad . ' başarıyla güncellendi! ✅');
        } catch (\Exception $e) {
            \Log::error('Kişi güncelleme hatası:', [
                'kisi_id' => $kisi->id,
                'error' => $e->getMessage(),
                'validated' => $validated ?? null
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Kişi güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function destroy(Kisi $kisi)
    {
        try {
            // Kişiyi sil
            $kisiAdi = $kisi->ad . ' ' . $kisi->soyad;
            $kisi->delete();

            // JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $kisiAdi . ' başarıyla silindi.'
                ]);
            }

            // Redirect for form submissions
            return redirect()
                ->route('admin.kisiler.index')
                ->with('success', $kisiAdi . ' başarıyla silindi.');
        } catch (\Exception $e) {
            // JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kişi silinirken bir hata oluştu: ' . $e->getMessage()
                ], 500);
            }

            // Redirect for form submissions
            return redirect()
                ->route('admin.kisiler.index')
                ->with('error', 'Kişi silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        // Context7 uyumlu kişi arama
        $search = $request->get('q', '');
        $limit = $request->get('limit', 10);

        if (empty($search)) {
            return response()->json(['items' => []]);
        }

        $kisiler = Kisi::search($search)
            ->where('status', true) // Context7 uyumlu
            ->with(['il', 'ilce'])
            ->limit($limit)
            ->get()
            ->map(function ($kisi) {
                return [
                    'id' => $kisi->id,
                    'text' => $kisi->tam_ad . ' - ' . ($kisi->telefon ?? 'Tel yok') . ' - ' . ($kisi->il->il_adi ?? ''),
                    'tam_ad' => $kisi->tam_ad,
                    'telefon' => $kisi->telefon,
                    'email' => $kisi->email,
                    'il' => $kisi->il->il_adi ?? '',
                    'crm_score' => $kisi->crm_score,
                    'is_owner_eligible' => $kisi->isOwnerEligible(),
                ];
            });

        return response()->json(['items' => $kisiler]);
    }

    public function checkDuplicate(Request $request)
    {
        // Context7 uyumlu mükerrer kontrolü
        $email = $request->get('email');
        $telefon = $request->get('telefon');
        $tc_kimlik = $request->get('tc_kimlik');

        $duplicates = [];

        if ($email) {
            $emailDuplicate = Kisi::where('email', $email)->first();
            if ($emailDuplicate) {
                $duplicates[] = [
                    'type' => 'email',
                    'kisi' => $emailDuplicate->tam_ad,
                    'value' => $email
                ];
            }
        }

        if ($telefon) {
            $telefonDuplicate = Kisi::where('telefon', $telefon)->first();
            if ($telefonDuplicate) {
                $duplicates[] = [
                    'type' => 'telefon',
                    'kisi' => $telefonDuplicate->tam_ad,
                    'value' => $telefon
                ];
            }
        }

        if ($tc_kimlik) {
            $tcDuplicate = Kisi::where('tc_kimlik', $tc_kimlik)->first();
            if ($tcDuplicate) {
                $duplicates[] = [
                    'type' => 'tc_kimlik',
                    'kisi' => $tcDuplicate->tam_ad,
                    'value' => $tc_kimlik
                ];
            }
        }

        return response()->json([
            'duplicate' => !empty($duplicates),
            'duplicates' => $duplicates
        ]);
    }

    public function bulkAction(Request $request)
    {
        // Context7 uyumlu toplu işlemler
        $action = $request->get('action');
        $ids = $request->get('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Hiç kişi seçilmedi']);
        }

        $query = Kisi::whereIn('id', $ids);

        switch ($action) {
            case 'activate':
                $query->update(['status' => true]); // Context7 uyumlu
                $message = count($ids) . ' kişi etkinleştirildi';
                break;

            case 'pasif_yap':
                $query->update(['status' => false]); // Context7 uyumlu
                $message = count($ids) . ' kişi pasif yapıldı';
                break;

            case 'sil':
            case 'delete':
                $query->delete();
                $message = count($ids) . ' kişi silindi';
                break;

            default:
                return response()->json(['success' => false, 'message' => 'Geçersiz işlem']);
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function aiAnalyze(Request $request)
    {
        // Context7 uyumlu AI analiz önerileri
        $kisiId = $request->get('kisi_id');
        $kisi = Kisi::find($kisiId);

        if (!$kisi) {
            return response()->json(['success' => false, 'message' => 'Kişi bulunamadı']);
        }

        $suggestions = [];

        // CRM skoruna göre öneriler
        if ($kisi->crm_score < 50) {
            $suggestions[] = [
                'type' => 'crm_score',
                'priority' => 'high',
                'message' => 'CRM skoru düşük (' . $kisi->crm_score . '/100). Eksik bilgileri tamamlayın.',
                'actions' => [
                    'tc_kimlik' => !$kisi->tc_kimlik ? 'TC Kimlik No ekleyin' : null,
                    'telefon' => !$kisi->telefon ? 'Telefon numarası ekleyin' : null,
                    'email' => !$kisi->email ? 'E-posta adresi ekleyin' : null,
                    'adres' => !$kisi->il_id ? 'Adres bilgilerini tamamlayın' : null,
                ]
            ];
        }

        // İlan sahibi uygunluğu
        if (!$kisi->isOwnerEligible()) {
            $suggestions[] = [
                'type' => 'owner_eligibility',
                'priority' => 'medium',
                'message' => 'Bu kişi ilan sahibi olarak uygun değil.',
                'actions' => [
                    'tc_kimlik' => !$kisi->tc_kimlik ? 'TC Kimlik No gerekli' : null,
                    'telefon' => !$kisi->telefon ? 'Telefon numarası gerekli' : null,
                    'adres' => !$kisi->il_id ? 'Adres bilgileri gerekli' : null,
                ]
            ];
        }

        // Kişi tipi önerileri (Context7: kisi_tipi)
        if (!$kisi->kisi_tipi) {
            $suggestions[] = [
                'type' => 'kisi_tipi',
                'priority' => 'medium',
                'message' => 'Kişi tipi belirtilmemiş.',
                'actions' => [
                    'kisi_tipi' => 'Kişi tipini seçin'
                ]
            ];
        }

        return response()->json([
            'success' => true,
            'suggestions' => array_filter($suggestions),
            'crm_score' => $kisi->crm_score,
            'is_owner_eligible' => $kisi->isOwnerEligible(),
        ]);
    }

    public function takip(Request $request)
    {
        return $this->renderAny(['admin.kisiler.takip', 'Crm::musteriler.index']);
    }

    private function resolve($kisi): Kisi
    {
        if ($kisi instanceof Kisi) return $kisi;
        return Kisi::query()->findOrFail($kisi);
    }

    private function renderAny(array $views, array $data = []): Response|\Illuminate\Contracts\View\View
    {
        foreach ($views as $view) {
            if (view()->exists($view)) return response()->view($view, $data);
        }
        return response('Kişiler sayfaları hazır değil', 200);
    }
}
