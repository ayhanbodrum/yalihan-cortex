<?php

namespace App\Modules\Crm\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Crm\Models\Aktivite;
use App\Modules\Crm\Services\AktiviteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AktiviteApiController extends Controller
{
    protected $aktiviteService;

    public function __construct(AktiviteService $aktiviteService)
    {
        $this->aktiviteService = $aktiviteService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $user = Auth::user();

        // Danışman kendi aktivitelerini görür, admin herkesi (Policy ile daha iyi yönetilebilir)
        if ($user && $user->role && $user->role->name === 'danisman') {
            $filters['danisman_id'] = $user->id;
        }

        $aktiviteler = $this->aktiviteService->getAllAktiviteler($filters);

        return response()->json($aktiviteler);
    }

    /**
     * Store a newly created resource in storage.
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
        if ($user && $user->role && $user->role->name === 'danisman' && empty($validatedData['danisman_id'])) {
            $validatedData['danisman_id'] = $user->id;
        }

        $aktivite = $this->aktiviteService->createAktivite($validatedData);

        return response()->json($aktivite, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Aktivite $aktivite)
    {
        // Yetkilendirme (Policy kullanılabilir)
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'danisman' && $aktivite->danisman_id !== $user->id) {
            // Admin/Superadmin kontrolü de eklenebilir
            if (! ($user->hasRole('admin') || $user->hasRole('superadmin'))) {
                return response()->json(['message' => 'Yetkisiz erişim.'], 403);
            }
        }

        return response()->json($aktivite->load(['kisi', 'danisman', 'ilan']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aktivite $aktivite)
    {
        // Yetkilendirme
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'danisman' && $aktivite->danisman_id !== $user->id) {
            if (! ($user->hasRole('admin') || $user->hasRole('superadmin'))) {
                return response()->json(['message' => 'Yetkisiz erişim.'], 403);
            }
        }

        $validatedData = $request->validate([
            'kisi_id' => 'sometimes|required|exists:kisiler,id',
            'danisman_id' => 'nullable|exists:users,id',
            'tip' => 'sometimes|required|string|in:'.implode(',', array_keys(Aktivite::getAktiviteTipleri())),
            'baslik' => 'sometimes|required|string|max:255',
            'aciklama' => 'nullable|string',
            'baslangic_tarihi' => 'sometimes|required|date',
            'bitis_tarihi' => 'nullable|date|after_or_equal:baslangic_tarihi',
            'status' => 'sometimes|required|string|in:'.implode(',', array_keys(Aktivite::getAktiviteDurumlari())),
            'sonuc' => 'nullable|string',
            'ilan_id' => 'nullable|exists:ilanlar,id',
        ]);

        $updatedAktivite = $this->aktiviteService->updateAktivite($aktivite, $validatedData);

        return response()->json($updatedAktivite);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aktivite $aktivite)
    {
        // Yetkilendirme
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'danisman' && $aktivite->danisman_id !== $user->id) {
            if (! ($user->hasRole('admin') || $user->hasRole('superadmin'))) {
                return response()->json(['message' => 'Yetkisiz erişim.'], 403);
            }
        }

        $this->aktiviteService->deleteAktivite($aktivite);

        return response()->json(null, 204);
    }

    /**
     * Bir kişiye ait aktiviteleri listeler (API).
     */
    public function kisiAktiviteleri(Kisi $kisi, Request $request)
    {
        // Yetkilendirme (Policy veya manuel kontrol)
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'danisman' && $kisi->danisman_id !== $user->id) {
            if (! ($user->hasRole('admin') || $user->hasRole('superadmin'))) {
                return response()->json(['message' => 'Bu kişiye ait aktivitelere erişim yetkiniz yok.'], 403);
            }
        }

        $filters = $request->all();
        $filters['kisi_id'] = $kisi->id;
        $aktiviteler = $this->aktiviteService->getAllAktiviteler($filters);

        return response()->json($aktiviteler);
    }
}
