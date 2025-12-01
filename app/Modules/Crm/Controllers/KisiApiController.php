<?php

namespace App\Modules\Crm\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kisi;
use App\Modules\Crm\Models\Etiket;
use App\Modules\Crm\Services\EtiketService;
use App\Modules\Crm\Services\KisiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KisiApiController extends Controller
{
    protected $kisiService;

    protected $etiketService;

    public function __construct(KisiService $kisiService, EtiketService $etiketService)
    {
        $this->kisiService = $kisiService;
        $this->etiketService = $etiketService;
        // Route seviyesinde auth middleware tanımlandı
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->all();

        // Rol bazlı filtreleme (opsiyonel, policy ile daha iyi yönetilebilir)
        if ($user && $user->hasRole && $user->hasRole('danisman')) {
            $filters['danisman_id'] = $user->id;
        }

        $kisiler = $this->kisiService->getAllKisiler($filters);

        // API response için format
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $kisiler,
                'message' => 'Kişiler başarıyla alındı.',
            ]);
        }

        return response()->json($kisiler);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ad' => 'required|string|max:255',
            'soyad' => 'required|string|max:255',
            'email' => 'nullable|email|unique:kisiler,email',
            'telefon' => 'nullable|string|max:20',
            'danisman_id' => 'nullable|exists:users,id',
            // Diğer zorunlu alanlar ve validasyonlar eklenebilir
        ]);

        // Danışman ID'sini ayarla (eğer kullanıcı danışmansa ve danisman_id gönderilmediyse)
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'danisman' && ! isset($validatedData['danisman_id'])) {
            $validatedData['danisman_id'] = $user->id;
        }

        $kisi = $this->kisiService->createKisi($validatedData);

        if ($request->has('etiket_ids')) {
            $this->etiketService->syncEtiketlerForKisi($kisi, $request->input('etiket_ids'));
        }

        return response()->json($kisi, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kisi $kisi)
    {
        // Yetkilendirme (Policy kullanılabilir)
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'danisman' && $kisi->danisman_id !== $user->id) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 403);
        }
        $kisi->load('etiketler'); // İlişkili etiketleri yükle

        return response()->json($kisi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kisi $kisi)
    {
        // Yetkilendirme
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'danisman' && $kisi->danisman_id !== $user->id) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 403);
        }

        $validatedData = $request->validate([
            'ad' => 'sometimes|required|string|max:255',
            'soyad' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|unique:kisiler,email,'.$kisi->id,
            'telefon' => 'nullable|string|max:20',
            'danisman_id' => 'nullable|exists:users,id',
        ]);

        $updatedKisi = $this->kisiService->updateKisi($kisi, $validatedData);

        if ($request->has('etiket_ids')) {
            $this->etiketService->syncEtiketlerForKisi($updatedKisi, $request->input('etiket_ids'));
        }

        return response()->json($updatedKisi);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kisi $kisi)
    {
        // Yetkilendirme
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'danisman' && $kisi->danisman_id !== $user->id) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 403);
        }

        $this->kisiService->deleteKisi($kisi);

        return response()->json(null, 204);
    }

    /**
     * Bir kişiye etiket atar.
     */
    public function attachEtiket(Request $request, Kisi $kisi)
    {
        $request->validate(['etiket_id' => 'required|exists:etiketler,id']);
        $etiket = Etiket::find($request->input('etiket_id'));

        // Yetkilendirme
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'danisman' && $kisi->danisman_id !== $user->id) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 403);
        }

        $this->etiketService->attachEtiketToKisi($kisi, $etiket);

        return response()->json(['message' => 'Etiket başarıyla eklendi.'], 200);
    }

    /**
     * Bir kişiden etiketi kaldırır.
     */
    public function detachEtiket(Kisi $kisi, Etiket $etiket) // Route model binding
    {
        // Yetkilendirme
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'danisman' && $kisi->danisman_id !== $user->id) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 403);
        }

        $this->etiketService->detachEtiketFromKisi($kisi, $etiket);

        return response()->json(['message' => 'Etiket başarıyla kaldırıldı.'], 200);
    }

    /**
     * Bir kişinin etiketlerini senkronize eder.
     */
    public function syncEtiketler(Request $request, Kisi $kisi)
    {
        $request->validate(['etiket_ids' => 'array', 'etiket_ids.*' => 'exists:etiketler,id']);

        // Yetkilendirme
        $user = Auth::user();
        if ($user && $user->role && $user->role->name === 'danisman' && $kisi->danisman_id !== $user->id) {
            return response()->json(['message' => 'Yetkisiz erişim.'], 403);
        }

        $this->etiketService->syncEtiketlerForKisi($kisi, $request->input('etiket_ids', []));

        return response()->json(['message' => 'Etiketler başarıyla senkronize edildi.'], 200);
    }
}
