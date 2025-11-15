<?php

namespace App\Modules\TakimYonetimi\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gorev;
use Illuminate\Http\Request;

class GorevController extends Controller
{
    public function index()
    {
        $gorevler = Gorev::with(['admin', 'danisman', 'musteri', 'proje'])->paginate(15);

        // Context7: Spatie Permission ile danışman rolünü kontrol et
        $danismanlar = \App\Models\User::whereHas('roles', function($q) { $q->where('name', 'danisman'); })->get();

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
        $danismanlar = \App\Models\User::whereHas('roles', function($q) { $q->where('name', 'danisman'); })->get();
        $adminler = \App\Models\User::whereHas('roles', function($q) {
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
        $danismanlar = \App\Models\User::whereHas('roles', function($q) { $q->where('name', 'danisman'); })->get();
        $adminler = \App\Models\User::whereHas('roles', function($q) {
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

        $gorev->update(['user_id' => $validated['user_id']]);

        return response()->json(['success' => true]);
    }

    public function statusGuncelle(Request $request, Gorev $gorev)
    {
        $validated = $request->validate([
            'status' => 'required|in:beklemede,devam_ediyor,tamamlandi,iptal,askida',
        ]);

        $gorev->update($validated);

        return response()->json(['success' => true]);
    }

    public function rapor(Gorev $gorev)
    {
        return view('admin.takim-yonetimi.gorevler.rapor', compact('gorev'));
    }

    public function dosyaEkle(Request $request, Gorev $gorev)
    {
        // Dosya ekleme işlemi
        return response()->json(['success' => true]);
    }

    public function dosyaSil(Gorev $gorev, $dosyaId)
    {
        // Dosya silme işlemi
        return response()->json(['success' => true]);
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
}
