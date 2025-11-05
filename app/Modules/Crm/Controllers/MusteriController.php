<?php

namespace App\Modules\Crm\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Crm\Models\Aktivite;
use App\Modules\Crm\Models\Musteri;
use App\Modules\Crm\Models\Randevu;
use App\Modules\Emlak\Models\Ilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MusteriController extends Controller
{
    /**
     * Müşteri listesini görüntüle
     */
    public function index(Request $request)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $query = Musteri::query();

        // Arama
        if ($request->has('q') && ! empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('ad', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('soyad', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('telefon', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('tc_kimlik', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Durum filtresi
        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        // İl filtresi
        if ($request->has('il') && ! empty($request->il)) {
            $query->where('il', $request->il);
        }

        // İlçe filtresi
        if ($request->has('ilce') && ! empty($request->ilce)) {
            $query->where('ilce', $request->ilce);
        }

        // Danışman filtresi (admin için)
        if ($request->has('user_id') && ! empty($request->user_id) && Auth::user()->hasRole(['admin', 'superadmin'])) {
            $query->where('user_id', $request->user_id);
        } elseif (! Auth::user()->hasRole(['admin', 'superadmin'])) {
            // Danışman sadece kendi müşterilerini görebilir
            $query->where('user_id', Auth::id());
        }

        // Sıralama
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';

        $allowedSortFields = ['ad', 'soyad', 'created_at', 'updated_at', 'status'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // İl ve İlçe Listelerini Hazırla
        $iller = Musteri::select('il')->distinct()->whereNotNull('il')->orderBy('il')->pluck('il');
        $ilceler = [];

        if ($request->has('il') && ! empty($request->il)) {
            $ilceler = Musteri::where('il', $request->il)
                ->select('ilce')
                ->distinct()
                ->whereNotNull('ilce')
                ->orderBy('ilce')
                ->pluck('ilce');
        }

        // Pagination
        $musteriler = $query->paginate(15)->appends($request->all());

        // İstatistikler
        $stats = [
            'total' => Musteri::count(),
            'active' => Musteri::where('status', 'Aktif')->count(),
            'pasif' => Musteri::where('status', 'Pasif')->count(),
            'potansiyel' => Musteri::where('status', 'Potansiyel')->count(),
        ];

        return view('crm::musteriler.index', compact('musteriler', 'iller', 'ilceler', 'stats'));
    }

    /**
     * Yeni müşteri ekleme formunu göster
     */
    public function create()
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        return view('crm::musteriler.create');
    }

    /**
     * Yeni müşteriyi kaydet
     */
    public function store(Request $request)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        // Validasyon
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'soyad' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:musteriler,email',
            'adres' => 'nullable|string',
            'il' => 'nullable|string|max:50',
            'ilce' => 'nullable|string|max:50',
            'tc_kimlik' => 'nullable|string|max:11|unique:musteriler,tc_kimlik',
            'dogum_tarihi' => 'nullable|date',
            'meslek' => 'nullable|string|max:100',
            'gelir_duzeyi' => 'nullable|string|max:50',
            'medeni_status' => 'nullable|string|max:20',
            'notlar' => 'nullable|string',
            'status' => 'required|in:Aktif,Pasif,Potansiyel',
            'kaynak' => 'nullable|string|max:100',
            'facebook_profile' => 'nullable|string|max:255',
            'twitter_profile' => 'nullable|string|max:255',
            'instagram_profile' => 'nullable|string|max:255',
            'linkedin_profile' => 'nullable|string|max:255',
        ]);

        // Müşteri oluşturma
        $musteri = new Musteri;
        $musteri->fill($validated);
        $musteri->user_id = Auth::id(); // Müşteriyi ekleyen danışman
        $musteri->save();

        return redirect()->route('admin.musteriler.show', $musteri->id)
            ->with('success', 'Müşteri başarıyla oluşturuldu.');
    }

    /**
     * Müşteri detaylarını göster
     */
    public function show($id)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $musteri = Musteri::with(['danisman', 'talepler', 'ilgilenilenIlanlar', 'randevular'])->findOrFail($id);

        // Sadece kendi müşterilerini görebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin']) && $musteri->user_id != Auth::id()) {
            abort(403, 'Bu müşteriyi görüntüleme yetkiniz bulunmamaktadır.');
        }

        // Son aktiviteler
        $aktiviteler = Aktivite::where('musteri_id', $id)
            ->orderBy('tarih', 'desc')
            ->limit(5)
            ->get();

        // Yaklaşan randevular
        $yaklasanRandevular = Randevu::where('musteri_id', $id)
            ->where('baslangic', '>=', now())
            ->orderBy('baslangic', 'asc')
            ->limit(3)
            ->get();

        // İlgilenilen ilanlar
        $ilgilenilenIlanlar = $musteri->ilgilenilenIlanlar()->get();

        return view('crm::musteriler.show', compact('musteri', 'aktiviteler', 'yaklasanRandevular', 'ilgilenilenIlanlar'));
    }

    /**
     * Müşteri düzenleme formunu göster
     */
    public function edit($id)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $musteri = Musteri::findOrFail($id);

        // Sadece kendi müşterilerini düzenleyebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin']) && $musteri->user_id != Auth::id()) {
            abort(403, 'Bu müşteriyi düzenleme yetkiniz bulunmamaktadır.');
        }

        return view('crm::musteriler.edit', compact('musteri'));
    }

    /**
     * Müşteri bilgilerini güncelle
     */
    public function update(Request $request, $id)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $musteri = Musteri::findOrFail($id);

        // Sadece kendi müşterilerini düzenleyebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin']) && $musteri->user_id != Auth::id()) {
            abort(403, 'Bu müşteriyi düzenleme yetkiniz bulunmamaktadır.');
        }

        // Validasyon
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'soyad' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:musteriler,email,'.$id,
            'adres' => 'nullable|string',
            'il' => 'nullable|string|max:50',
            'ilce' => 'nullable|string|max:50',
            'tc_kimlik' => 'nullable|string|max:11|unique:musteriler,tc_kimlik,'.$id,
            'dogum_tarihi' => 'nullable|date',
            'meslek' => 'nullable|string|max:100',
            'gelir_duzeyi' => 'nullable|string|max:50',
            'medeni_status' => 'nullable|string|max:20',
            'notlar' => 'nullable|string',
            'status' => 'required|in:Aktif,Pasif,Potansiyel',
            'kaynak' => 'nullable|string|max:100',
            'facebook_profile' => 'nullable|string|max:255',
            'twitter_profile' => 'nullable|string|max:255',
            'instagram_profile' => 'nullable|string|max:255',
            'linkedin_profile' => 'nullable|string|max:255',
        ]);

        // Müşteri güncelleme
        $musteri->fill($validated);
        $musteri->save();

        return redirect()->route('admin.musteriler.show', $musteri->id)
            ->with('success', 'Müşteri bilgileri başarıyla güncellendi.');
    }

    /**
     * Müşteriyi sil
     */
    public function destroy($id)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $musteri = Musteri::findOrFail($id);

        // İlişkili kayıtları sil
        Aktivite::where('musteri_id', $id)->delete();
        Randevu::where('musteri_id', $id)->delete();

        // ✅ STANDARDIZED: Using Eloquent query builder instead of DB::table()
        // Müşteri-ilan ilişkilerini sil (pivot table)
        \Illuminate\Support\Facades\DB::table('musteri_ilan')->where('musteri_id', $id)->delete();

        // Müşteriyi sil
        $musteri->delete();

        return redirect()->route('admin.musteriler.index')
            ->with('success', 'Müşteri başarıyla silindi.');
    }

    /**
     * Müşteriye ilan ekle
     */
    public function addIlan(Request $request, $id)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $musteri = Musteri::findOrFail($id);

        // Sadece kendi müşterilerine ilan ekleyebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin']) && $musteri->user_id != Auth::id()) {
            abort(403, 'Bu müşteriye ilan ekleme yetkiniz bulunmamaktadır.');
        }

        $request->validate([
            'ilan_id' => 'required|exists:ilanlar,id',
            'status' => 'required|in:İlgileniyor,Görüntüledi,Randevu Aldı,Vazgeçti,Satın Aldı,Kiraladı',
            'notlar' => 'nullable|string',
        ]);

        // Müşteri-ilan ilişkisi oluştur
        $musteri->ilgilenilenIlanlar()->syncWithoutDetaching([
            $request->ilan_id => [
                'status' => $request->status,
                'notlar' => $request->notlar,
            ],
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'İlan müşteriye başarıyla eklendi.',
            ]);
        }

        return redirect()->back()->with('success', 'İlan müşteriye başarıyla eklendi.');
    }

    /**
     * Müşteriden ilanı kaldır
     */
    public function removeIlan(Request $request, $id, $ilanId)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $musteri = Musteri::findOrFail($id);

        // Sadece kendi müşterilerinden ilan kaldırabilir (admin ve superadmin hariç)
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin']) && $musteri->user_id != Auth::id()) {
            abort(403, 'Bu müşteriden ilan kaldırma yetkiniz bulunmamaktadır.');
        }

        // Müşteri-ilan ilişkisini kaldır
        $musteri->ilgilenilenIlanlar()->detach($ilanId);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'İlan müşteriden başarıyla kaldırıldı.',
            ]);
        }

        return redirect()->back()->with('success', 'İlan müşteriden başarıyla kaldırıldı.');
    }

    /**
     * Müşteri aktivitelerini listele
     */
    public function aktiviteler($id)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $musteri = Musteri::findOrFail($id);

        // Sadece kendi müşterilerinin aktivitelerini görebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin']) && $musteri->user_id != Auth::id()) {
            abort(403, 'Bu müşterinin aktivitelerini görüntüleme yetkiniz bulunmamaktadır.');
        }

        $aktiviteler = Aktivite::where('musteri_id', $id)
            ->orderBy('tarih', 'desc')
            ->paginate(15);

        return view('crm::musteriler.aktiviteler', compact('musteri', 'aktiviteler'));
    }

    /**
     * Müşteri randevularını listele
     */
    public function randevular($id)
    {
        // Yetki kontrolü
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin', 'danisman'])) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        $musteri = Musteri::findOrFail($id);

        // Sadece kendi müşterilerinin randevularını görebilir (admin ve superadmin hariç)
        if (! Auth::user()->hasAnyRole(['admin', 'superadmin']) && $musteri->user_id != Auth::id()) {
            abort(403, 'Bu müşterinin randevularını görüntüleme yetkiniz bulunmamaktadır.');
        }

        $randevular = Randevu::where('musteri_id', $id)
            ->orderBy('baslangic', 'desc')
            ->paginate(15);

        return view('crm::musteriler.randevular', compact('musteri', 'randevular'));
    }
}
