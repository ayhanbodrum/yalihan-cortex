<?php

namespace App\Modules\Crm\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Crm\Models\Etiket;
use App\Modules\Crm\Services\EtiketService;
use Illuminate\Http\Request;

class EtiketController extends Controller
{
    protected $etiketService;

    public function __construct(EtiketService $etiketService)
    {
        $this->etiketService = $etiketService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $etiketler = $this->etiketService->getAllEtiketler();

        return view('Crm::etiketler.index', compact('etiketler'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Crm::etiketler.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ad' => 'required|string|max:255|unique:etiketler,ad',
            'renk' => 'nullable|string|max:7', // Hex renk kodu için
        ]);

        $this->etiketService->createEtiket($validatedData);

        return redirect()->route('crm.etiketler.index')->with('success', 'Etiket başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Etiket $etiket)
    {
        return view('Crm::etiketler.show', compact('etiket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Etiket $etiket)
    {
        return view('Crm::etiketler.edit', compact('etiket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Etiket $etiket)
    {
        $validatedData = $request->validate([
            'ad' => 'required|string|max:255|unique:etiketler,ad,'.$etiket->id,
            'renk' => 'nullable|string|max:7',
        ]);

        $this->etiketService->updateEtiket($etiket, $validatedData);

        return redirect()->route('crm.etiketler.index')->with('success', 'Etiket başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Etiket $etiket)
    {
        // Etikete bağlı kişiler varsa silme işlemini engelle veya kullanıcıyı uyar
        if ($etiket->kisiler()->count() > 0) {
            return redirect()->route('crm.etiketler.index')->with('error', 'Bu etiket kişilere bağlı olduğu için silinemez.');
        }
        $this->etiketService->deleteEtiket($etiket);

        return redirect()->route('crm.etiketler.index')->with('success', 'Etiket başarıyla silindi.');
    }
}
