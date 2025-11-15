<?php

namespace App\Modules\Crm\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Crm\Models\Aktivite;
use App\Modules\Crm\Models\Kisi;
use App\Modules\Crm\Services\AktiviteService;
use App\Modules\Crm\Services\KisiService;
use App\Modules\Danisman\Services\DanismanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AktiviteController extends Controller
{
    protected $aktiviteService;

    protected $kisiService;

    protected $danismanService;

    public function __construct(AktiviteService $aktiviteService, KisiService $kisiService, DanismanService $danismanService)
    {
        $this->aktiviteService = $aktiviteService;
        $this->kisiService = $kisiService;
        $this->danismanService = $danismanService;
        $this->middleware('auth');
    }

    /**
     * Aktivite listesini görüntüle
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $user = Auth::user();

        // Danışman kendi aktivitelerini görür, admin herkesi
        if ($user->role && $user->role->name === 'danisman') {
            $filters['danisman_id'] = $user->id;
        }

        $aktiviteler = $this->aktiviteService->getAllAktiviteler($filters);
        $kisiler = $this->kisiService->getAllKisilerForDropdown(); // Form için kişiler
        $danismanlar = $this->danismanService->getAllDanismanlarForDropdown(); // Form için danışmanlar

        return view('Crm::aktiviteler.index', compact('aktiviteler', 'kisiler', 'danismanlar', 'filters'));
    }

    /**
     * Yeni aktivite ekleme formunu göster
     */
    public function create(Request $request)
    {
        $kisiler = $this->kisiService->getAllKisilerForDropdown();
        $danismanlar = $this->danismanService->getAllDanismanlarForDropdown();
        $aktiviteTipleri = Aktivite::getAktiviteTipleri();
        $aktiviteDurumlari = Aktivite::getAktiviteDurumlari();
        $kisi_id = $request->get('kisi_id'); // Kişi sayfasından geliyorsa

        return view('Crm::aktiviteler.create', compact('kisiler', 'danismanlar', 'aktiviteTipleri', 'aktiviteDurumlari', 'kisi_id'));
    }

    /**
     * Yeni aktiviteyi kaydet
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kisi_id' => 'required|exists:kisiler,id',
            'danisman_id' => 'nullable|exists:users,id',
            'tip' => 'required|string|in:'.implode(',', array_keys(Aktivite::getAktiviteTipleri())),
            'baslik' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'baslangic_tarihi' => 'required|date',
            'bitis_tarihi' => 'nullable|date|after_or_equal:baslangic_tarihi',
            'status' => 'required|string|in:'.implode(',', array_keys(Aktivite::getAktiviteDurumlari())),
            'sonuc' => 'nullable|string',
            'ilan_id' => 'nullable|exists:ilanlar,id',
        ]);

        $user = Auth::user();
        // Eğer danışman ve danışman_id boşsa kendi ID'sini ata
        if ($user->role && $user->role->name === 'danisman' && empty($validatedData['danisman_id'])) {
            $validatedData['danisman_id'] = $user->id;
        }

        $this->aktiviteService->createAktivite($validatedData);

        if ($request->get('return_to_kisi')) {
            return redirect()->route('admin.kisiler.show', $validatedData['kisi_id']) // Context7: crm.* → admin.*
                ->with('success', 'Aktivite başarıyla oluşturuldu.');
        }

        return redirect()->route('admin.aktiviteler.index') // Context7: crm.* → admin.*
            ->with('success', 'Aktivite başarıyla oluşturuldu.');
    }

    /**
     * Aktivite detaylarını göster
     */
    public function show(Aktivite $aktivite)
    {
        $this->authorize('view', $aktivite); // Policy ile yetkilendirme

        return view('Crm::aktiviteler.show', compact('aktivite'));
    }

    /**
     * Aktivite düzenleme formunu göster
     */
    public function edit(Aktivite $aktivite)
    {
        $this->authorize('update', $aktivite); // Policy ile yetkilendirme
        $kisiler = $this->kisiService->getAllKisilerForDropdown();
        $danismanlar = $this->danismanService->getAllDanismanlarForDropdown();
        $aktiviteTipleri = Aktivite::getAktiviteTipleri();
        $aktiviteDurumlari = Aktivite::getAktiviteDurumlari();

        return view('Crm::aktiviteler.edit', compact('aktivite', 'kisiler', 'danismanlar', 'aktiviteTipleri', 'aktiviteDurumlari'));
    }

    /**
     * Aktiviteyi güncelle
     */
    public function update(Request $request, Aktivite $aktivite)
    {
        $this->authorize('update', $aktivite); // Policy ile yetkilendirme
        $validatedData = $request->validate([
            'kisi_id' => 'required|exists:kisiler,id',
            'danisman_id' => 'nullable|exists:users,id',
            'tip' => 'required|string|in:'.implode(',', array_keys(Aktivite::getAktiviteTipleri())),
            'baslik' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'baslangic_tarihi' => 'required|date',
            'bitis_tarihi' => 'nullable|date|after_or_equal:baslangic_tarihi',
            'status' => 'required|string|in:'.implode(',', array_keys(Aktivite::getAktiviteDurumlari())),
            'sonuc' => 'nullable|string',
            'ilan_id' => 'nullable|exists:ilanlar,id',
        ]);

        $this->aktiviteService->updateAktivite($aktivite, $validatedData);

        return redirect()->route('admin.aktiviteler.index')->with('success', 'Aktivite başarıyla güncellendi.'); // Context7: crm.* → admin.*
    }

    /**
     * Aktiviteyi sil
     */
    public function destroy(Aktivite $aktivite)
    {
        $this->authorize('delete', $aktivite); // Policy ile yetkilendirme
        $this->aktiviteService->deleteAktivite($aktivite);

        return redirect()->route('admin.aktiviteler.index')->with('success', 'Aktivite başarıyla silindi.'); // Context7: crm.* → admin.*
    }

    /**
     * Bir kişiye ait aktiviteleri listeler.
     */
    public function kisiAktiviteleri(Kisi $kisi, Request $request)
    {
        $this->authorize('view', $kisi); // Kişiyi görme yetkisi
        $filters = $request->all();
        $filters['kisi_id'] = $kisi->id;
        $aktiviteler = $this->aktiviteService->getAllAktiviteler($filters);

        return view('Crm::aktiviteler.index_kisi', compact('aktiviteler', 'kisi', 'filters'));
    }
}
