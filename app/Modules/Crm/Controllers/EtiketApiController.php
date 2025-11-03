<?php

namespace App\Modules\Crm\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Crm\Models\Etiket;
use App\Modules\Crm\Services\EtiketService;
use Illuminate\Http\Request;

class EtiketApiController extends Controller
{
    protected $etiketService;

    public function __construct(EtiketService $etiketService)
    {
        $this->etiketService = $etiketService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $etiketler = $this->etiketService->getAllEtiketler();

        return response()->json($etiketler);
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

        $etiket = $this->etiketService->createEtiket($validatedData);

        return response()->json($etiket, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Etiket $etiket)
    {
        return response()->json($etiket);
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

        $updatedEtiket = $this->etiketService->updateEtiket($etiket, $validatedData);

        return response()->json($updatedEtiket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Etiket $etiket)
    {
        // Etikete bağlı kişiler varsa silme işlemini engelle veya kullanıcıyı uyar
        if ($etiket->kisiler()->count() > 0) {
            return response()->json(['message' => 'Bu etiket kişilere bağlı olduğu için silinemez.'], 422); // Unprocessable Entity
        }
        $this->etiketService->deleteEtiket($etiket);

        return response()->json(null, 204);
    }
}
