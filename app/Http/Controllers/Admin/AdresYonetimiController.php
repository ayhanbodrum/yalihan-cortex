<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ulke;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use Illuminate\Http\Request;

class AdresYonetimiController extends AdminController
{
    public function index()
    {
        $ulkeler = Ulke::orderBy('ulke_adi')->get(['id', 'ulke_adi']);
        $iller = Il::orderBy('il_adi')->get(['id', 'il_adi']);
        return view('admin.adres-yonetimi.index', compact('ulkeler', 'iller'));
    }

    /**
     * Adres öğesi detaylarını göster
     * Context7: Lokasyon sistemi detay sayfası
     */
    public function show($type, $id)
    {
        try {
            switch ($type) {
                case 'ulke':
                    $item = Ulke::findOrFail($id);
                    $relatedData = [
                        'iller_count' => Il::count(), // Context7: ulke_id kolonu olmadığı için tüm illeri say
                        'type' => 'Ülke',
                        'name' => $item->ulke_adi
                    ];
                    break;

                case 'il':
                    $item = Il::findOrFail($id);
                    $relatedData = [
                        'ilceler_count' => Ilce::where('il_id', $id)->count(),
                        'type' => 'İl',
                        'name' => $item->il_adi
                    ];
                    break;

                case 'ilce':
                    $item = Ilce::with('il')->findOrFail($id);
                    $relatedData = [
                        'mahalleler_count' => Mahalle::where('ilce_id', $id)->count(),
                        'parent_name' => $item->il->il_adi ?? 'Bilinmiyor',
                        'type' => 'İlçe',
                        'name' => $item->ilce_adi
                    ];
                    break;

                case 'mahalle':
                    $item = Mahalle::with('ilce.il')->findOrFail($id);
                    $relatedData = [
                        'parent_name' => $item->ilce->ilce_adi ?? 'Bilinmiyor',
                        'grandparent_name' => $item->ilce->il->il_adi ?? 'Bilinmiyor',
                        'type' => 'Mahalle',
                        'name' => $item->mahalle_adi
                    ];
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Geçersiz tür'
                    ], 422);
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'item' => $item,
                    'related_data' => $relatedData
                ]);
            }

            return view('admin.adres-yonetimi.show', compact('item', 'relatedData', 'type'));

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Öğe bulunamadı: ' . $e->getMessage()
                ], 404);
            }

            return redirect()->route('admin.adres-yonetimi.index')
                ->with('error', 'Öğe bulunamadı: ' . $e->getMessage());
        }
    }

    /**
     * Yeni adres öğesi oluşturma formu
     * Context7: Lokasyon sistemi yeni öğe ekleme
     */
    public function create($type)
    {
        try {
            $parentOptions = [];

            switch ($type) {
                case 'ulke':
                    // Ülke için parent yok
                    break;

                case 'il':
                    $parentOptions = Ulke::orderBy('ulke_adi')->get(['id', 'ulke_adi']);
                    break;

                case 'ilce':
                    $parentOptions = Il::orderBy('il_adi')->get(['id', 'il_adi']);
                    break;

                case 'mahalle':
                    $parentOptions = Ilce::with('il')->orderBy('ilce_adi')->get(['id', 'il_id', 'ilce_adi']);
                    break;

                default:
                    return redirect()->route('admin.adres-yonetimi.index')
                        ->with('error', 'Geçersiz tür');
            }

            return view('admin.adres-yonetimi.create', compact('type', 'parentOptions'));

        } catch (\Exception $e) {
            return redirect()->route('admin.adres-yonetimi.index')
                ->with('error', 'Form yüklenirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Adres öğesi düzenleme formu
     * Context7: Lokasyon sistemi öğe düzenleme
     */
    public function edit($type, $id)
    {
        try {
            $parentOptions = [];

            switch ($type) {
                case 'ulke':
                    $item = Ulke::findOrFail($id);
                    break;

                case 'il':
                    $item = Il::findOrFail($id);
                    $parentOptions = Ulke::orderBy('ulke_adi')->get(['id', 'ulke_adi']);
                    break;

                case 'ilce':
                    $item = Ilce::findOrFail($id);
                    $parentOptions = Il::orderBy('il_adi')->get(['id', 'il_adi']);
                    break;

                case 'mahalle':
                    $item = Mahalle::findOrFail($id);
                    $parentOptions = Ilce::with('il')->orderBy('ilce_adi')->get(['id', 'il_id', 'ilce_adi']);
                    break;

                default:
                    return redirect()->route('admin.adres-yonetimi.index')
                        ->with('error', 'Geçersiz tür');
            }

            return view('admin.adres-yonetimi.edit', compact('item', 'type', 'parentOptions'));

        } catch (\Exception $e) {
            return redirect()->route('admin.adres-yonetimi.index')
                ->with('error', 'Öğe bulunamadı: ' . $e->getMessage());
        }
    }

    public function getUlkeler()
    {
        $ulkeler = Ulke::orderBy('ulke_adi')->get(['id', 'ulke_adi']);
        return response()->json(['success' => true, 'ulkeler' => $ulkeler]);
    }

    public function getBolgeler()
    {
        return response()->json(['success' => true, 'bolgeler' => []]);
    }

    public function getIller()
    {
        $iller = Il::orderBy('il_adi')->get(['id', 'il_adi']);
        return response()->json(['success' => true, 'iller' => $iller]);
    }

    public function getIllerByUlke($ulkeId)
    {
        // Context7: iller tablosunda ulke_id kolonu yok - tüm illeri döndür
        // TODO: Eğer ulke filtrelemesi gerekiyorsa, migration ile ulke_id kolonu eklenmeli
        $iller = Il::orderBy('il_adi')->get(['id', 'il_adi']);
        return response()->json(['success' => true, 'iller' => $iller]);
    }

    public function getIlceler()
    {
        $ilceler = Ilce::orderBy('ilce_adi')->get(['id', 'il_id', 'ilce_adi']);
        return response()->json(['success' => true, 'ilceler' => $ilceler]);
    }

    public function getIlcelerByIl($ilId)
    {
        $ilceler = Ilce::where('il_id', $ilId)->orderBy('ilce_adi')->get(['id', 'il_id', 'ilce_adi']);
        return response()->json(['success' => true, 'ilceler' => $ilceler]);
    }

    public function getMahalleler()
    {
        $mahalleler = Mahalle::orderBy('mahalle_adi')->get(['id', 'ilce_id', 'mahalle_adi']);
        return response()->json(['success' => true, 'mahalleler' => $mahalleler]);
    }

    public function getMahallelerByIlce($ilceId)
    {
        $mahalleler = Mahalle::where('ilce_id', $ilceId)->orderBy('mahalle_adi')->get(['id', 'ilce_id', 'mahalle_adi']);
        return response()->json(['success' => true, 'mahalleler' => $mahalleler]);
    }

    public function store(Request $request, $type)
    {
        $name = $request->input('name');
        $parentId = $request->input('parent_id');
        if ($type === 'ulke') {
            $item = Ulke::create(['ulke_adi' => $name]);
            return response()->json(['success' => true, 'item' => $item]);
        }
        if ($type === 'il') {
            // Context7: iller tablosunda ulke_id kolonu yok - sadece il_adi kaydet
            $item = Il::create(['il_adi' => $name]);
            return response()->json(['success' => true, 'item' => $item]);
        }
        if ($type === 'ilce') {
            $item = Ilce::create(['il_id' => $parentId, 'ilce_adi' => $name]);
            return response()->json(['success' => true, 'item' => $item]);
        }
        if ($type === 'mahalle') {
            $item = Mahalle::create(['ilce_id' => $parentId, 'mahalle_adi' => $name]);
            return response()->json(['success' => true, 'item' => $item]);
        }
        return response()->json(['success' => false, 'message' => 'Geçersiz tür'], 422);
    }

    public function update(Request $request, $type, $id)
    {
        $name = $request->input('name');
        if ($type === 'ulke') {
            $item = Ulke::findOrFail($id);
            $item->update(['ulke_adi' => $name]);
            return response()->json(['success' => true]);
        }
        if ($type === 'il') {
            $item = Il::findOrFail($id);
            $item->update(['il_adi' => $name]);
            return response()->json(['success' => true]);
        }
        if ($type === 'ilce') {
            $item = Ilce::findOrFail($id);
            $item->update(['ilce_adi' => $name]);
            return response()->json(['success' => true]);
        }
        if ($type === 'mahalle') {
            $item = Mahalle::findOrFail($id);
            $item->update(['mahalle_adi' => $name]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Geçersiz tür'], 422);
    }

    public function destroy($type, $id)
    {
        if ($type === 'ulke') {
            Ulke::where('id', $id)->delete();
            return response()->json(['success' => true]);
        }
        if ($type === 'il') {
            Il::where('id', $id)->delete();
            return response()->json(['success' => true]);
        }
        if ($type === 'ilce') {
            Ilce::where('id', $id)->delete();
            return response()->json(['success' => true]);
        }
        if ($type === 'mahalle') {
            Mahalle::where('id', $id)->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Geçersiz tür'], 422);
    }
}
