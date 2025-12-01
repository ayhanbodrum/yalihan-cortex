<?php

namespace App\Http\Controllers\Admin;

use App\Models\AnahtarYonetimi;
use App\Models\Ilan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnahtarYonetimiController extends AdminController
{
    /**
     * Anahtar yönetimi listesi
     */
    public function index()
    {
        $anahtarlar = AnahtarYonetimi::with(['ilan', 'teslimEden', 'teslimAlan', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.anahtar-yonetimi.index', compact('anahtarlar'));
    }

    /**
     * Yeni anahtar oluşturma formu
     */
    public function create()
    {
        $ilanlar = Ilan::where('status', 'Aktif')->get();
        $kullanicilar = User::where('status', 'Aktif')->get();

        return view('admin.anahtar-yonetimi.create', compact('ilanlar', 'kullanicilar'));
    }

    /**
     * Anahtar kaydetme
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ilan_id' => 'required|exists:ilanlar,id',
            'anahtar_durumu' => 'required|in:Beklemede,Hazır,Teslim Edildi,Geri Alındı,Kayıp',
            'teslim_tarihi' => 'nullable|date',
            'teslim_eden_kisi_id' => 'nullable|exists:users,id',
            'teslim_alan_kisi_id' => 'nullable|exists:users,id',
            'anahtar_konumu' => 'nullable|string|max:255',
            'anahtar_notlari' => 'nullable|string',
            'anahtar_tipi' => 'required|in:Ana Anahtar,Yedek Anahtar,Kodlu Anahtar,Kartlı Anahtar,Uzaktan Kumanda',
            'anahtar_sayisi' => 'required|integer|min:1',
            'anahtar_ozellikleri' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $anahtar = AnahtarYonetimi::create([
            'ilan_id' => $request->ilan_id,
            'anahtar_durumu' => $request->anahtar_durumu,
            'teslim_tarihi' => $request->teslim_tarihi,
            'teslim_eden_kisi_id' => $request->teslim_eden_kisi_id,
            'teslim_alan_kisi_id' => $request->teslim_alan_kisi_id,
            'anahtar_konumu' => $request->anahtar_konumu,
            'anahtar_notlari' => $request->anahtar_notlari,
            'anahtar_tipi' => $request->anahtar_tipi,
            'anahtar_sayisi' => $request->anahtar_sayisi,
            'anahtar_ozellikleri' => $request->anahtar_ozellikleri,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.anahtar-yonetimi.index')
            ->with('success', 'Anahtar başarıyla oluşturuldu.');
    }

    /**
     * Anahtar detayları
     */
    public function show($id)
    {
        $anahtar = AnahtarYonetimi::with(['ilan', 'teslimEden', 'teslimAlan', 'creator', 'updater'])
            ->findOrFail($id);

        return view('admin.anahtar-yonetimi.show', compact('anahtar'));
    }

    /**
     * Anahtar düzenleme formu
     */
    public function edit($id)
    {
        $anahtar = AnahtarYonetimi::findOrFail($id);
        $ilanlar = Ilan::where('status', 'Aktif')->get();
        $kullanicilar = User::where('status', 'Aktif')->get();

        return view('admin.anahtar-yonetimi.edit', compact('anahtar', 'ilanlar', 'kullanicilar'));
    }

    /**
     * Anahtar güncelleme
     */
    public function update(Request $request, $id)
    {
        $anahtar = AnahtarYonetimi::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'anahtar_durumu' => 'required|in:Beklemede,Hazır,Teslim Edildi,Geri Alındı,Kayıp',
            'teslim_tarihi' => 'nullable|date',
            'teslim_eden_kisi_id' => 'nullable|exists:users,id',
            'teslim_alan_kisi_id' => 'nullable|exists:users,id',
            'anahtar_konumu' => 'nullable|string|max:255',
            'anahtar_notlari' => 'nullable|string',
            'anahtar_tipi' => 'required|in:Ana Anahtar,Yedek Anahtar,Kodlu Anahtar,Kartlı Anahtar,Uzaktan Kumanda',
            'anahtar_sayisi' => 'required|integer|min:1',
            'anahtar_ozellikleri' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $anahtar->update([
            'anahtar_durumu' => $request->anahtar_durumu,
            'teslim_tarihi' => $request->teslim_tarihi,
            'teslim_eden_kisi_id' => $request->teslim_eden_kisi_id,
            'teslim_alan_kisi_id' => $request->teslim_alan_kisi_id,
            'anahtar_konumu' => $request->anahtar_konumu,
            'anahtar_notlari' => $request->anahtar_notlari,
            'anahtar_tipi' => $request->anahtar_tipi,
            'anahtar_sayisi' => $request->anahtar_sayisi,
            'anahtar_ozellikleri' => $request->anahtar_ozellikleri,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.anahtar-yonetimi.index')
            ->with('success', 'Anahtar başarıyla güncellendi.');
    }

    /**
     * Anahtar silme
     */
    public function destroy($id)
    {
        $anahtar = AnahtarYonetimi::findOrFail($id);
        $anahtar->delete();

        return redirect()->route('admin.anahtar-yonetimi.index')
            ->with('success', 'Anahtar başarıyla silindi.');
    }

    /**
     * Anahtar durumu güncelleme (AJAX)
     */
    public function updateStatus(Request $request, $id)
    {
        $anahtar = AnahtarYonetimi::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'anahtar_durumu' => 'required|in:Beklemede,Hazır,Teslim Edildi,Geri Alındı,Kayıp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $anahtar->update([
            'anahtar_durumu' => $request->anahtar_durumu,
            'teslim_tarihi' => $request->anahtar_durumu === 'Teslim Edildi' ? now() : $anahtar->teslim_tarihi,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anahtar durumu başarıyla güncellendi.',
            'data' => $anahtar,
        ]);
    }

    /**
     * Anahtar teslim etme
     */
    public function deliver(Request $request, $id)
    {
        $anahtar = AnahtarYonetimi::findOrFail($id);

        if (! $anahtar->canBeDelivered()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu anahtar teslim edilemez.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'teslim_alan_kisi_id' => 'required|exists:users,id',
            'anahtar_notlari' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $anahtar->update([
            'anahtar_durumu' => 'Teslim Edildi',
            'teslim_tarihi' => now(),
            'teslim_alan_kisi_id' => $request->teslim_alan_kisi_id,
            'teslim_eden_kisi_id' => auth()->id(),
            'anahtar_notlari' => $request->anahtar_notlari,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anahtar başarıyla teslim edildi.',
            'data' => $anahtar,
        ]);
    }
}
