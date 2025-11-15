<?php

namespace App\Modules\Crm\Controllers;

use App\Http\Controllers\Controller; // BaseController yerine Laravel'in temel Controller'ı kullanılacak
use App\Modules\Auth\Models\User;
use App\Modules\Crm\Models\Kisi;
use App\Modules\Crm\Services\EtiketService;
use App\Modules\Crm\Services\KisiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $kisiService;

    protected $etiketService;

    public function __construct(KisiService $kisiService, EtiketService $etiketService)
    {
        $this->kisiService = $kisiService;
        $this->etiketService = $etiketService;
        $this->middleware('auth'); // Tüm metodlar için auth middleware
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->all();

        // Süper Admin tüm kişileri görebilir, Danışman sadece kendi kişilerini görebilir
        // Not: Bu rol kontrolü servise taşınabilir veya policy kullanılabilir.
        if ($user && $user->role && $user->role->name === 'danisman') {
            $filters['danisman_id'] = $user->id;
        }

        $kisiler = $this->kisiService->getAllKisiler($filters);

        return view('Crm::kisiler.index', compact('kisiler')); // View yolu Crm:: şeklinde olmalı
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $danismanlar = User::whereHas('role', function ($q) {
            $q->where('name', 'danisman');
        })->get(); // Bu kısım olduğu gibi kalabilir veya bir DanismanService'e taşınabilir.
        $etiketler = $this->etiketService->getAllEtiketler();

        return view('Crm::kisiler.create', compact('danismanlar', 'etiketler'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Form verilerini doğrula
        $validator = Validator::make($request->all(), [
            'ad' => 'required|string|max:255',
            'soyad' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefon' => 'required|string|max:20',
            'adres' => 'nullable|string',
            'dogum_tarihi' => 'nullable|date',
            'musteri_tipi' => 'required|in:alici,satici,kiraci,kiralayan,yatirimci',
            'not' => 'nullable|string',
            'status' => 'required|string',
            'danisman_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Danışman kontrolü
        $danisman_id = $request->danisman_id;

        // Kullanıcı süper admin değilse ve danışman ise, sadece kendi müşterisini ekleyebilir
        $user = Auth::user();
        if ($user) {
            // Using direct role properties instead of methods to avoid hasRole issues
            $userRole = $user->role ? $user->role->name : null;

            if ($userRole !== 'admin' && $userRole === 'danisman') {
                $danisman_id = $user->id;
            }
        }

        // Kişi oluştur
        $kisi = $this->kisiService->createKisi([
            'ad' => $request->ad,
            'soyad' => $request->soyad,
            'email' => $request->email,
            'telefon' => $request->telefon,
            'adres' => $request->adres,
            'dogum_tarihi' => $request->dogum_tarihi,
            'musteri_tipi' => $request->musteri_tipi,
            'not' => $request->not,
            'status' => $request->status,
            'danisman_id' => $danisman_id,
        ]);

        if ($request->has('etiketler')) {
            $this->etiketService->syncEtiketlerForKisi($kisi, $request->input('etiketler'));
        }

        return redirect()->route('admin.kisiler.index') // Context7: crm.* → admin.*
            ->with('success', 'Kişi başarıyla eklendi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kisi $kisi) // Route model binding Kisi $kisi olarak değiştirildi
    {
        // Yetki kontrolü
        $user = Auth::user();
        if ($user) {
            $userRole = $user->role ? $user->role->name : null;

            if ($userRole !== 'admin' && $userRole !== 'superadmin' && $kisi->danisman_id != $user->id) {
                abort(403, 'Bu kişiyi görüntüleme yetkiniz yok.');
            }
        }

        return view('Crm::kisiler.show', compact('kisi')); // View yolu Crm:: şeklinde olmalı
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kisi $kisi) // Route model binding Kisi $kisi olarak değiştirildi
    {
        // Yetki kontrolü
        $user = Auth::user();
        if ($user) {
            $userRole = $user->role ? $user->role->name : null;

            if ($userRole !== 'admin' && $userRole !== 'superadmin' && $kisi->danisman_id != $user->id) {
                abort(403, 'Bu kişiyi düzenleme yetkiniz yok.');
            }
        }

        // Form için gerekli veriler
        $danismanlar = User::whereHas('role', function ($q) {
            $q->where('name', 'danisman');
        })->get();

        $etiketler = $this->etiketService->getAllEtiketler();
        $kisiEtiketIds = $kisi->etiketler()->pluck('etiket_id')->toArray(); // Doğru ilişki ve alan adı

        return view('Crm::kisiler.edit', compact('kisi', 'danismanlar', 'etiketler', 'kisiEtiketIds')); // View yolu Crm:: şeklinde olmalı
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kisi $kisi) // Route model binding Kisi $kisi olarak değiştirildi
    {
        // Form verilerini doğrula
        $validator = Validator::make($request->all(), [
            'ad' => 'required|string|max:255',
            'soyad' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefon' => 'required|string|max:20',
            'adres' => 'nullable|string',
            'dogum_tarihi' => 'nullable|date',
            'musteri_tipi' => 'required|in:alici,satici,kiraci,kiralayan,yatirimci',
            'not' => 'nullable|string',
            'status' => 'required|string',
            'danisman_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Yetki kontrolü
        $user = Auth::user();
        if ($user) {
            $userRole = $user->role ? $user->role->name : null;

            if ($userRole !== 'admin' && $userRole !== 'superadmin' && $kisi->danisman_id != $user->id) {
                abort(403, 'Bu kişiyi güncelleme yetkiniz yok.');
            }
        }

        // Danışman kontrolü
        $danisman_id = $request->danisman_id;

        // Kullanıcı süper admin değilse ve danışman ise, sadece kendi müşterisini güncelleyebilir
        if ($user) {
            $userRole = $user->role ? $user->role->name : null;

            if ($userRole !== 'admin' && $userRole === 'danisman') {
                $danisman_id = $user->id;
            }
        }

        // Kişi güncelle
        $this->kisiService->updateKisi($kisi, [
            'ad' => $request->ad,
            'soyad' => $request->soyad,
            'email' => $request->email,
            'telefon' => $request->telefon,
            'adres' => $request->adres,
            'dogum_tarihi' => $request->dogum_tarihi,
            'musteri_tipi' => $request->musteri_tipi,
            'not' => $request->not,
            'status' => $request->status,
            'danisman_id' => $danisman_id,
        ]);

        if ($request->has('etiketler')) {
            $this->etiketService->syncEtiketlerForKisi($kisi, $request->input('etiketler'));
        } else {
            $this->etiketService->syncEtiketlerForKisi($kisi, []); // Seçim yoksa tüm etiketleri kaldır
        }

        return redirect()->route('admin.kisiler.index') // Context7: crm.* → admin.*
            ->with('success', 'Kişi başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kisi $kisi) // Route model binding Kisi $kisi olarak değiştirildi
    {
        // Yetki kontrolü
        $user = Auth::user();
        if ($user) {
            $userRole = $user->role ? $user->role->name : null;

            if ($userRole !== 'admin' && $userRole !== 'superadmin') {
                abort(403, 'Bu kişiyi silme yetkiniz yok.');
            }
        }

        $this->kisiService->deleteKisi($kisi);

        return redirect()->route('admin.kisiler.index') // Context7: crm.* → admin.*
            ->with('success', 'Kişi başarıyla silindi.');
    }
}
