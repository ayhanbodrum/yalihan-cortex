<?php

namespace App\Modules\TakimYonetimi\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proje;
use Illuminate\Http\Request;

class ProjeController extends Controller
{
    public function index()
    {
        $projeler = Proje::with(['user', 'takim'])->paginate(15);
        return view('takimyonetimi::admin.projeler.index', compact('projeler'));
    }

    public function create()
    {
        return view('takimyonetimi::admin.projeler.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proje_adi' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'baslangic_tarihi' => 'required|date',
            'bitis_tarihi' => 'nullable|date|after:baslangic_tarihi',
            'status' => 'required|in:planlama,devam_ediyor,tamamlandi,iptal,beklemede',
            'oncelik' => 'required|in:dusuk,orta,yuksek,kritik',
            'user_id' => 'required|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
        ]);

        $proje = Proje::create($validated);

        return redirect()->route('admin.takim.projeler.index')
            ->with('success', 'Proje başarıyla oluşturuldu.');
    }

    public function show(Proje $proje)
    {
        $proje->load(['user', 'takim', 'gorevler']);
        return view('takimyonetimi::admin.projeler.show', compact('proje'));
    }

    public function edit(Proje $proje)
    {
        return view('takimyonetimi::admin.projeler.edit', compact('proje'));
    }

    public function update(Request $request, Proje $proje)
    {
        $validated = $request->validate([
            'proje_adi' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'baslangic_tarihi' => 'required|date',
            'bitis_tarihi' => 'nullable|date|after:baslangic_tarihi',
            'status' => 'required|in:planlama,devam_ediyor,tamamlandi,iptal,beklemede',
            'oncelik' => 'required|in:dusuk,orta,yuksek,kritik',
            'user_id' => 'required|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
        ]);

        $proje->update($validated);

        return redirect()->route('admin.takim.projeler.index')
            ->with('success', 'Proje başarıyla güncellendi.');
    }

    public function destroy(Proje $proje)
    {
        $proje->delete();

        return redirect()->route('admin.takim.projeler.index')
            ->with('success', 'Proje başarıyla silindi.');
    }

    public function gorevEkle(Request $request, Proje $proje)
    {
        // Görev ekleme işlemi
        return response()->json(['success' => true]);
    }

    public function rapor(Proje $proje)
    {
        // Proje raporu
        return view('takimyonetimi::admin.projeler.rapor', compact('proje'));
    }
}
