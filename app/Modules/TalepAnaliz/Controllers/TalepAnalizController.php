<?php

namespace App\Modules\TalepAnaliz\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Talep;
use App\Modules\TalepAnaliz\Services\AIAnalizService; // Güncellendi
use Illuminate\Http\Request;

class TalepAnalizController extends Controller
{
    protected AIAnalizService $aiAnalizService;

    public function __construct(AIAnalizService $analizService)
    {
        // $this->middleware('auth');
        // $this->middleware('role:admin,danisman');
        $this->aiAnalizService = $analizService;
    }

    public function index()
    {
        $talepler = Talep::with('kullanici', 'il', 'ilce')->latest()->paginate(10);

        return view('admin.talepler.analiz_index', compact('talepler')); // View yolu düzeltildi
    }

    public function analizEt(Request $request, $id)
    {
        $talep = Talep::findOrFail($id);
        $sonuc = $this->aiAnalizService->analizEt($talep);

        return view('admin.talepler.analiz_detay', compact('talep', 'sonuc')); // View yolu düzeltildi
    }

    public function topluAnalizEt(Request $request)
    {
        return redirect()->route('admin.talepler.analiz.index')->with('info', 'Toplu analiz özelliği yakında eklenecektir.');
    }

    public function testSayfasi()
    {
        return view('admin.talepler.analiz_test'); // View yolu düzeltildi
    }

    public function raporOlustur(Request $request, $id)
    {
        $talep = Talep::findOrFail($id);

        return redirect()->route('admin.talepler.analiz.show', $id)->with('info', 'Rapor oluşturma özelliği yakında eklenecektir.');
    }
}
