<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Yayın Tipi Yöneticisi Controller
 *
 * Tek sayfada kategori, yayın tipi ve ilişki yönetimi.
 * Context7 Standardı: C7-YAYIN-TIPI-YONETICI-2025-11-05
 */
class YayinTipiYoneticisiController extends Controller
{
    /**
     * Ana sayfa - Tüm kategoriler ve yayın tipleri
     */
    public function index()
    {
        // Ana kategorileri getir (seviye 1 veya parent_id null)
        $anaKategoriler = IlanKategori::whereNull('parent_id')
            ->orWhere('seviye', 1)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        // Her kategori için yayın tiplerini yükle
        $kategoriler = $anaKategoriler->map(function ($kategori) {
            $yayinTipleri = IlanKategoriYayinTipi::where('kategori_id', $kategori->id)
                ->orderBy('order')
                ->orderBy('yayin_tipi')
                ->get();

            return [
                'kategori' => $kategori,
                'yayin_tipleri' => $yayinTipleri,
                'yayin_tipi_count' => $yayinTipleri->count(),
            ];
        });

        // İstatistikler
        $istatistikler = [
            'toplam_kategori' => $anaKategoriler->count(),
            'toplam_yayin_tipi' => IlanKategoriYayinTipi::count(),
            'aktif_yayin_tipi' => IlanKategoriYayinTipi::where('status', true)->count(),
        ];

        return view('admin.yayin-tipi-yoneticisi.index', compact('kategoriler', 'istatistikler'));
    }

    /**
     * Yayın tipi ekle (AJAX)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:ilan_kategorileri,id',
            'yayin_tipi' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        // Aynı kategori için aynı yayın tipi var mı kontrol et
        $existing = IlanKategoriYayinTipi::where('kategori_id', $request->kategori_id)
            ->where('yayin_tipi', $request->yayin_tipi)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Bu yayın tipi zaten mevcut!',
            ], 422);
        }

        // Context7: Schema kontrolü
        $hasStatusColumn = Schema::hasColumn('ilan_kategori_yayin_tipleri', 'status');

        $data = [
            'kategori_id' => $request->kategori_id,
            'yayin_tipi' => $request->yayin_tipi,
            'order' => $request->order ?? IlanKategoriYayinTipi::where('kategori_id', $request->kategori_id)->max('order') + 1,
        ];

        if ($hasStatusColumn) {
            $data['status'] = true;
        }

        $yayinTipi = IlanKategoriYayinTipi::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Yayın tipi başarıyla eklendi!',
            'data' => [
                'id' => $yayinTipi->id,
                'yayin_tipi' => $yayinTipi->yayin_tipi,
                'order' => $yayinTipi->order,
                'status' => $yayinTipi->status ?? true,
            ],
        ]);
    }

    /**
     * Yayın tipi güncelle (AJAX)
     */
    public function update(Request $request, $id)
    {
        $yayinTipi = IlanKategoriYayinTipi::findOrFail($id);

        $request->validate([
            'yayin_tipi' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        // Aynı kategori için başka bir yayın tipi aynı isme sahip mi kontrol et
        if ($request->yayin_tipi !== $yayinTipi->yayin_tipi) {
            $existing = IlanKategoriYayinTipi::where('kategori_id', $yayinTipi->kategori_id)
                ->where('yayin_tipi', $request->yayin_tipi)
                ->where('id', '!=', $id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu yayın tipi zaten mevcut!',
                ], 422);
            }
        }

        $data = [
            'yayin_tipi' => $request->yayin_tipi,
            'order' => $request->order ?? $yayinTipi->order,
        ];

        // Context7: Schema kontrolü
        $hasStatusColumn = Schema::hasColumn('ilan_kategori_yayin_tipleri', 'status');
        if ($hasStatusColumn && $request->has('status')) {
            $data['status'] = $request->status;
        }

        $yayinTipi->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Yayın tipi başarıyla güncellendi!',
            'data' => [
                'id' => $yayinTipi->id,
                'yayin_tipi' => $yayinTipi->yayin_tipi,
                'order' => $yayinTipi->order,
                'status' => $yayinTipi->status ?? true,
            ],
        ]);
    }

    /**
     * Yayın tipi sil (AJAX)
     */
    public function destroy($id)
    {
        $yayinTipi = IlanKategoriYayinTipi::findOrFail($id);

        // İlişkili ilan var mı kontrol et
        $ilanCount = $yayinTipi->ilanlar()->count();

        if ($ilanCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Bu yayın tipine ait {$ilanCount} ilan bulunuyor. Silinemez!",
            ], 422);
        }

        $yayinTipi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Yayın tipi başarıyla silindi!',
        ]);
    }

    /**
     * Yayın tipi durumunu değiştir (AJAX)
     */
    public function toggleStatus($id)
    {
        $yayinTipi = IlanKategoriYayinTipi::findOrFail($id);

        // Context7: Schema kontrolü
        $hasStatusColumn = Schema::hasColumn('ilan_kategori_yayin_tipleri', 'status');

        if (!$hasStatusColumn) {
            return response()->json([
                'success' => false,
                'message' => 'Status kolonu bulunamadı!',
            ], 422);
        }

        $yayinTipi->status = !$yayinTipi->status;
        $yayinTipi->save();

        return response()->json([
            'success' => true,
            'message' => 'Durum başarıyla güncellendi!',
            'data' => [
                'id' => $yayinTipi->id,
                'status' => $yayinTipi->status,
            ],
        ]);
    }

    /**
     * Sıralama güncelle (AJAX)
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:ilan_kategori_yayin_tipleri,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->items as $item) {
                IlanKategoriYayinTipi::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Sıralama başarıyla güncellendi!',
        ]);
    }
}

