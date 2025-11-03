<?php

namespace App\Modules\Crm\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Crm\Models\Musteri;
use App\Modules\Crm\Models\Randevu;
use App\Modules\Emlak\Models\Ilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RandevuController extends Controller
{
    /**
     * Randevu listesini görüntüle
     */
    public function index(Request $request)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $query = Randevu::with(['musteri', 'danisman', 'ilan']);

        // Arama
        if ($request->has('q') && ! empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('baslik', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('aciklama', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('konum', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('musteri', function ($q) use ($searchTerm) {
                        $q->where('ad', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('soyad', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        // Durum filtresi
        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Tarih filtresi
        if ($request->has('tarih_baslangic') && ! empty($request->tarih_baslangic)) {
            $query->whereDate('baslangic', '>=', $request->tarih_baslangic);
        }
        if ($request->has('tarih_bitis') && ! empty($request->tarih_bitis)) {
            $query->whereDate('baslangic', '<=', $request->tarih_bitis);
        }

        // Müşteri filtresi
        if ($request->has('musteri_id') && ! empty($request->musteri_id)) {
            $query->where('musteri_id', $request->musteri_id);
        }

        // Kullanıcı filtresi (admin için)
        if ($request->has('user_id') && ! empty($request->user_id) && Auth::user()->hasRole(['admin', 'superadmin'])) {
            $query->where('user_id', $request->user_id);
        } elseif (! Auth::user()->hasRole(['admin', 'superadmin'])) {
            // Danışman sadece kendi randevularını görebilir
            $query->where('user_id', Auth::id());
        }

        // Sıralama
        $sortField = $request->sort_by ?? 'baslangic';
        $sortDirection = $request->sort_direction ?? 'asc';

        $allowedSortFields = ['baslangic', 'baslik', 'status', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('baslangic', 'asc');
        }

        // Pagination
        $randevular = $query->paginate(15)->appends($request->all());

        // İstatistikler
        $stats = [
            'total' => Randevu::count(),
            'bugun' => Randevu::whereDate('baslangic', now()->toDateString())->count(),
            'planlandi' => Randevu::where('status', 'Planlandı')->count(),
            'tamamlandi' => Randevu::where('status', 'Tamamlandı')->count(),
        ];

        return view('crm::randevular.index', compact('randevular', 'stats'));
    }

    /**
     * Yeni randevu ekleme formunu göster
     */
    public function create(Request $request)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        // Eğer müşteri ID'si varsa, o müşteri için randevu oluşturulacak
        $musteri = null;
        if ($request->has('musteri_id') && ! empty($request->musteri_id)) {
            $musteri = Musteri::findOrFail($request->musteri_id);

            // Sadece kendi müşterileri için randevu ekleyebilir (admin ve superadmin hariç)
            if (! Auth::user()->hasRole(['admin', 'superadmin']) && $musteri->user_id != Auth::id()) {
                abort(403, 'Bu müşteri için randevu ekleme yetkiniz bulunmamaktadır.');
            }
        }

        // Eğer ilan ID'si varsa, o ilan için randevu oluşturulacak
        $ilan = null;
        if ($request->has('ilan_id') && ! empty($request->ilan_id)) {
            $ilan = Ilan::findOrFail($request->ilan_id);
        }

        // Müşteri listesi (danışman sadece kendi müşterilerini görebilir)
        $musteriler = [];
        if (Auth::user()->hasRole(['admin', 'superadmin'])) {
            $musteriler = Musteri::orderBy('ad')->orderBy('soyad')->get();
        } else {
            $musteriler = Musteri::where('user_id', Auth::id())->orderBy('ad')->orderBy('soyad')->get();
        }

        return view('crm::randevular.create', compact('musteri', 'ilan', 'musteriler'));
    }

    /**
     * Yeni randevuyu kaydet
     */
    public function store(Request $request)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        // Validasyon
        $validated = $request->validate([
            'musteri_id' => 'required|exists:musteriler,id',
            'baslangic' => 'required|date',
            'bitis' => 'nullable|date|after_or_equal:baslangic',
            'baslik' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'konum' => 'nullable|string|max:255',
            'status' => 'required|in:Planlandı,Tamamlandı,İptal Edildi,Ertelendi',
            'notlar' => 'nullable|string',
            'hatirlatma_gonder' => 'boolean',
            'hatirlatma_dakika' => 'nullable|integer|min:0',
            'ilan_id' => 'nullable|exists:ilanlar,id',
        ]);

        // Müşteri kontrolü
        $musteri = Musteri::findOrFail($request->musteri_id);

        // Sadece kendi müşterileri için randevu ekleyebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasRole(['admin', 'superadmin']) && $musteri->user_id != Auth::id()) {
            abort(403, 'Bu müşteri için randevu ekleme yetkiniz bulunmamaktadır.');
        }

        // Randevu oluşturma
        $randevu = new Randevu;
        $randevu->fill($validated);
        $randevu->user_id = Auth::id(); // Randevuyu ekleyen kullanıcı
        $randevu->hatirlatma_gonder = $request->has('hatirlatma_gonder');
        $randevu->save();

        // Müşteri-ilan ilişkisi güncelleme
        if ($request->ilan_id) {
            $musteri->ilgilenilenIlanlar()->syncWithoutDetaching([
                $request->ilan_id => [
                    'status' => 'Randevu Aldı',
                    'notlar' => 'Randevu: '.$randevu->baslik.' - '.$randevu->baslangic->format('d.m.Y H:i'),
                ],
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Randevu başarıyla oluşturuldu.',
                'randevu' => $randevu,
            ]);
        }

        return redirect()->route('admin.randevular.show', $randevu->id)
            ->with('success', 'Randevu başarıyla oluşturuldu.');
    }

    /**
     * Randevu detaylarını göster
     */
    public function show($id)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $randevu = Randevu::with(['musteri', 'danisman', 'ilan'])->findOrFail($id);

        // Sadece kendi randevularını görebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasRole(['admin', 'superadmin']) && $randevu->user_id != Auth::id()) {
            abort(403, 'Bu randevuyu görüntüleme yetkiniz bulunmamaktadır.');
        }

        return view('crm::randevular.show', compact('randevu'));
    }

    /**
     * Randevu düzenleme formunu göster
     */
    public function edit($id)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $randevu = Randevu::with(['musteri', 'ilan'])->findOrFail($id);

        // Sadece kendi randevularını düzenleyebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasRole(['admin', 'superadmin']) && $randevu->user_id != Auth::id()) {
            abort(403, 'Bu randevuyu düzenleme yetkiniz bulunmamaktadır.');
        }

        // Müşteri listesi (danışman sadece kendi müşterilerini görebilir)
        $musteriler = [];
        if (Auth::user()->hasRole(['admin', 'superadmin'])) {
            $musteriler = Musteri::orderBy('ad')->orderBy('soyad')->get();
        } else {
            $musteriler = Musteri::where('user_id', Auth::id())->orderBy('ad')->orderBy('soyad')->get();
        }

        return view('crm::randevular.edit', compact('randevu', 'musteriler'));
    }

    /**
     * Randevu bilgilerini güncelle
     */
    public function update(Request $request, $id)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $randevu = Randevu::findOrFail($id);

        // Sadece kendi randevularını düzenleyebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasRole(['admin', 'superadmin']) && $randevu->user_id != Auth::id()) {
            abort(403, 'Bu randevuyu düzenleme yetkiniz bulunmamaktadır.');
        }

        // Validasyon
        $validated = $request->validate([
            'musteri_id' => 'required|exists:musteriler,id',
            'baslangic' => 'required|date',
            'bitis' => 'nullable|date|after_or_equal:baslangic',
            'baslik' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'konum' => 'nullable|string|max:255',
            'status' => 'required|in:Planlandı,Tamamlandı,İptal Edildi,Ertelendi',
            'notlar' => 'nullable|string',
            'hatirlatma_gonder' => 'boolean',
            'hatirlatma_dakika' => 'nullable|integer|min:0',
            'ilan_id' => 'nullable|exists:ilanlar,id',
        ]);

        // Müşteri kontrolü
        $musteri = Musteri::findOrFail($request->musteri_id);

        // Sadece kendi müşterileri için randevu düzenleyebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasRole(['admin', 'superadmin']) && $musteri->user_id != Auth::id()) {
            abort(403, 'Bu müşteri için randevu düzenleme yetkiniz bulunmamaktadır.');
        }

        // Randevu güncelleme
        $randevu->fill($validated);
        $randevu->hatirlatma_gonder = $request->has('hatirlatma_gonder');
        $randevu->save();

        // Müşteri-ilan ilişkisi güncelleme (eğer ilan değiştiyse)
        if ($request->ilan_id && $randevu->ilan_id != $request->ilan_id) {
            $musteri->ilgilenilenIlanlar()->syncWithoutDetaching([
                $request->ilan_id => [
                    'status' => 'Randevu Aldı',
                    'notlar' => 'Randevu: '.$randevu->baslik.' - '.$randevu->baslangic->format('d.m.Y H:i'),
                ],
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Randevu başarıyla güncellendi.',
                'randevu' => $randevu,
            ]);
        }

        return redirect()->route('admin.randevular.show', $randevu->id)
            ->with('success', 'Randevu başarıyla güncellendi.');
    }

    /**
     * Randevuyu sil
     */
    public function destroy($id)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $randevu = Randevu::findOrFail($id);

        // Sadece kendi randevularını silebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasRole(['admin', 'superadmin']) && $randevu->user_id != Auth::id()) {
            abort(403, 'Bu randevuyu silme yetkiniz bulunmamaktadır.');
        }

        // Randevuyu sil
        $randevu->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Randevu başarıyla silindi.',
            ]);
        }

        return redirect()->route('admin.randevular.index')
            ->with('success', 'Randevu başarıyla silindi.');
    }

    /**
     * Takvim görünümü
     */
    public function calendar(Request $request)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        // Danışman filtresi (admin için)
        $danismanlar = [];
        if (Auth::user()->hasRole(['admin', 'superadmin'])) {
            $danismanlar = \App\Models\User::role(['danisman', 'admin', 'superadmin'])->get();
        }

        return view('crm::randevular.calendar', compact('danismanlar'));
    }

    /**
     * Takvim için randevu verilerini getir (AJAX)
     */
    public function getCalendarData(Request $request)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $start = $request->start;
        $end = $request->end;

        $query = Randevu::with(['musteri', 'danisman'])
            ->whereBetween('baslangic', [$start, $end]);

        // Danışman filtresi (admin için)
        if ($request->has('user_id') && ! empty($request->user_id) && Auth::user()->hasRole(['admin', 'superadmin'])) {
            $query->where('user_id', $request->user_id);
        } elseif (! Auth::user()->hasRole(['admin', 'superadmin'])) {
            // Danışman sadece kendi randevularını görebilir
            $query->where('user_id', Auth::id());
        }

        $randevular = $query->get();

        $events = [];
        foreach ($randevular as $randevu) {
            // Durum renklerini belirle
            $color = '#3788d8'; // Varsayılan mavi
            switch ($randevu->status) {
                case 'Planlandı':
                    $color = '#3788d8'; // Mavi
                    break;
                case 'Tamamlandı':
                    $color = '#28a745'; // Yeşil
                    break;
                case 'İptal Edildi':
                    $color = '#dc3545'; // Kırmızı
                    break;
                case 'Ertelendi':
                    $color = '#ffc107'; // Sarı
                    break;
            }

            $events[] = [
                'id' => $randevu->id,
                'title' => $randevu->musteri->name.' - '.$randevu->baslik,
                'start' => $randevu->baslangic->toIso8601String(),
                'end' => $randevu->bitis ? $randevu->bitis->toIso8601String() : null,
                'color' => $color,
                'extendedProps' => [
                    'musteri_id' => $randevu->musteri_id,
                    'musteri_adi' => $randevu->musteri->name,
                    'status' => $randevu->status,
                    'konum' => $randevu->konum,
                    'aciklama' => $randevu->aciklama,
                ],
            ];
        }

        return response()->json($events);
    }
}
