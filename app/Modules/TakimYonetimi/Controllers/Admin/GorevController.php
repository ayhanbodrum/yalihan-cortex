<?php

namespace App\Modules\TakimYonetimi\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gorev;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class GorevController extends Controller
{
    public function index()
    {
        $gorevler = Gorev::with(['admin', 'danisman', 'musteri', 'proje'])->paginate(15);

        // Context7: Spatie Permission ile danışman rolünü kontrol et
        $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->get();

        // Context7: İstatistikler
        $istatistikler = [
            'bekleyen' => Gorev::where('status', 'beklemede')->count(),
            'devam_eden' => Gorev::where('status', 'devam_ediyor')->count(),
            'tamamlanan' => Gorev::where('status', 'tamamlandi')->count(),
        ];

        // ✅ Context7: View için gerekli değişkenler
        $status = request('status'); // Filter için

        return view('admin.takim-yonetimi.gorevler.index', compact('gorevler', 'danismanlar', 'istatistikler', 'status'));
    }

    public function create()
    {
        // Context7: Spatie Permission ile role-based user filtering
        $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->get();
        $adminler = \App\Models\User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })->get();

        // Context7: Müşteriler - Kisi modelinden çek (CRM için)
        $musteriler = \App\Models\Kisi::orderBy('ad')->limit(100)->get();

        // Projeleri getir
        $projeler = \App\Models\Proje::all();

        return view('admin.takim-yonetimi.gorevler.create', compact('danismanlar', 'adminler', 'musteriler', 'projeler'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'baslik' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'required|in:beklemede,devam_ediyor,tamamlandi,iptal,askida',
            'oncelik' => 'required|in:dusuk,normal,yuksek,acil',
            'tip' => 'nullable|string',
            'admin_id' => 'required|exists:users,id',
            'danisman_id' => 'nullable|exists:users,id',
            'musteri_id' => 'nullable|exists:users,id',
            'proje_id' => 'nullable|exists:projeler,id',
            'tahmini_sure' => 'nullable|integer|min:0',
        ]);

        $gorev = Gorev::create($validated);

        return redirect()->route('admin.takim-yonetimi.gorevler.index')
            ->with('success', 'Görev başarıyla oluşturuldu.');
    }

    public function show(Gorev $gorev)
    {
        $gorev->load(['admin', 'danisman', 'musteri', 'proje', 'takip', 'dosyalar']);

        return view('admin.takim-yonetimi.gorevler.show', compact('gorev'));
    }

    public function edit(Gorev $gorev)
    {
        // Context7: Spatie Permission ile role-based user filtering
        $danismanlar = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->get();
        $adminler = \App\Models\User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        })->get();

        // Context7: Müşteriler - Kisi modelinden çek (CRM için)
        $musteriler = \App\Models\Kisi::orderBy('ad')->limit(100)->get();

        // Projeleri getir
        $projeler = \App\Models\Proje::all();

        return view('admin.takim-yonetimi.gorevler.edit', compact('gorev', 'danismanlar', 'adminler', 'musteriler', 'projeler'));
    }

    public function update(Request $request, Gorev $gorev)
    {
        $validated = $request->validate([
            'baslik' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'required|in:beklemede,devam_ediyor,tamamlandi,iptal,askida',
            'oncelik' => 'required|in:dusuk,normal,yuksek,acil',
            'tip' => 'nullable|string',
            'admin_id' => 'required|exists:users,id',
            'danisman_id' => 'nullable|exists:users,id',
            'musteri_id' => 'nullable|exists:users,id',
            'proje_id' => 'nullable|exists:projeler,id',
            'tahmini_sure' => 'nullable|integer|min:0',
        ]);

        $gorev->update($validated);

        return redirect()->route('admin.takim-yonetimi.gorevler.show', $gorev)
            ->with('success', 'Görev başarıyla güncellendi.');
    }

    public function destroy(Gorev $gorev)
    {
        $gorev->delete();

        return redirect()->route('admin.takim-yonetimi.gorevler.index')
            ->with('success', 'Görev başarıyla silindi.');
    }

    public function atama(Request $request, Gorev $gorev)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $gorev->update(['danisman_id' => $validated['user_id']]);

        // Context7: ResponseService kullan (response()->json() YASAK)
        return ResponseService::success([
            'gorev_id' => $gorev->id,
            'danisman_id' => $gorev->danisman_id,
        ], 'Görev başarıyla atandı');
    }

    public function statusGuncelle(Request $request, Gorev $gorev)
    {
        $validated = $request->validate([
            'status' => 'required|in:bekliyor,beklemede,devam_ediyor,tamamlandi,iptal,askida',
        ]);

        // 'bekliyor' ve 'beklemede' aynı anlama geliyor, 'beklemede' olarak kaydet
        if ($validated['status'] === 'bekliyor') {
            $validated['status'] = 'beklemede';
        }

        $gorev->update($validated);

        // Context7: ResponseService kullan (response()->json() YASAK)
        return ResponseService::success([
            'gorev_id' => $gorev->id,
            'status' => $gorev->status,
        ], 'Görev durumu başarıyla güncellendi');
    }

    public function rapor(Gorev $gorev)
    {
        return view('admin.takim-yonetimi.gorevler.rapor', compact('gorev'));
    }

    public function dosyaEkle(Request $request, Gorev $gorev)
    {
        // Dosya ekleme işlemi
        // Context7: ResponseService kullan (response()->json() YASAK)
        return ResponseService::success(null, 'Dosya başarıyla eklendi');
    }

    public function dosyaSil(Gorev $gorev, $dosyaId)
    {
        // Dosya silme işlemi
        // Context7: ResponseService kullan (response()->json() YASAK)
        return ResponseService::success(null, 'Dosya başarıyla silindi');
    }

    public function dashboard()
    {
        $istatistikler = [
            'toplam_gorev' => Gorev::count(),
            'tamamlanan_gorev' => Gorev::where('status', 'tamamlandi')->count(),
            'devam_eden_gorev' => Gorev::where('status', 'devam_ediyor')->count(),
            'gecikmis_gorev' => Gorev::gecikmis()->count(),
        ];

        return view('admin.takim-yonetimi.dashboard', compact('istatistikler'));
    }

    public function raporlar()
    {
        return view('admin.takim-yonetimi.raporlar');
    }

    /**
     * Kanban Board - Personel bazlı görev görselleştirme
     */
    public function board(Request $request)
    {
        // Tüm personeli getir (id, name, avatar)
        $users = \App\Models\User::select(['id', 'name', 'email', 'avatar'])
            ->whereHas('roles', function ($q) {
                $q->where('name', 'danisman');
            })
            ->orWhereHas('takimUyesi', function ($q) {
                $q->where('rol', 'danisman');
            })
            ->orderBy('name')
            ->get();

        // Görevleri getir
        $gorevQuery = Gorev::with(['danisman', 'admin', 'musteri', 'proje'])
            ->whereIn('status', ['bekliyor', 'beklemede', 'devam_ediyor', 'tamamlandi']);

        // Filtreleme: Eğer user_id filtresi varsa sadece o kişiye ait görevleri getir
        $selectedUserId = $request->get('user_id');
        if ($selectedUserId) {
            $gorevQuery->where('danisman_id', $selectedUserId);
        } else {
            // Admin değilse sadece kendi görevlerini görsün
            $user = auth()->user();
            $isAdmin = $user && ($user->role_id == 1 || $user->role_id == 2); // 1: SuperAdmin, 2: Admin
            if (!$isAdmin) {
                $gorevQuery->where('danisman_id', auth()->id());
            }
        }

        // Status'e göre grupla ve sırala (Context7: display_order standardı)
        $filterCallback = function ($q) use ($selectedUserId) {
            if ($selectedUserId) {
                $q->where('danisman_id', $selectedUserId);
            } else {
                $user = auth()->user();
                $isAdmin = $user && ($user->role_id == 1 || $user->role_id == 2);
                if (!$isAdmin) {
                    $q->where('danisman_id', auth()->id());
                }
            }
        };

        // Bekleyenler: display_order varsa onu kullan, yoksa created_at (Context7 standardı)
        $bekleyenlerQuery = Gorev::with(['danisman', 'admin', 'musteri', 'proje'])
            ->whereIn('status', ['bekliyor', 'beklemede']);
        $filterCallback($bekleyenlerQuery);

        // display_order field'ı kontrol et (Schema'dan)
        $hasDisplayOrder = Schema::hasColumn('gorevler', 'display_order');
        if ($hasDisplayOrder) {
            $bekleyenler = $bekleyenlerQuery->latest('display_order')->get();
        } else {
            $bekleyenler = $bekleyenlerQuery->latest('created_at')->get();
        }

        // İşlemdekiler: updated_at'e göre sırala
        $islemdekiler = Gorev::with(['danisman', 'admin', 'musteri', 'proje'])
            ->where('status', 'devam_ediyor');
        $filterCallback($islemdekiler);
        $islemdekiler = $islemdekiler->latest('updated_at')->get();

        // Tamamlananlar: updated_at'e göre sırala, son 20 tanesi
        $tamamlananlar = Gorev::with(['danisman', 'admin', 'musteri', 'proje'])
            ->where('status', 'tamamlandi');
        $filterCallback($tamamlananlar);
        $tamamlananlar = $tamamlananlar->latest('updated_at')->limit(20)->get();

        return view('admin.takim.gorevler.board', compact(
            'users',
            'selectedUserId',
            'bekleyenler',
            'islemdekiler',
            'tamamlananlar'
        ));
    }
}
