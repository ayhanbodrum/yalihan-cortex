<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kisi;
use App\Models\KisiTask;
use App\Services\CRM\KisiScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KisiCRMController extends Controller
{
    protected $scoringService;

    public function __construct(KisiScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Kişinin etkileşim geçmişini getir
     */
    public function getEtkilesimler(int $id): JsonResponse
    {
        $kisi = Kisi::findOrFail($id);

        $etkilesimler = $kisi->etkilesimler()
            ->with('kullanici:id,name,email')
            ->aktif()
            ->sonEtkilesimler(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $etkilesimler,
        ]);
    }

    /**
     * Yeni etkileşim ekle
     */
    public function addEtkilesim(Request $request, int $id): JsonResponse
    {
        $kisi = Kisi::findOrFail($id);

        $validated = $request->validate([
            'tip' => 'required|in:telefon,email,sms,toplanti,whatsapp,not',
            'notlar' => 'nullable|string',
            'etkilesim_tarihi' => 'required|date',
        ]);

        $etkilesim = $kisi->etkilesimler()->create([
            'kullanici_id' => auth()->id(),
            'tip' => $validated['tip'],
            'notlar' => $validated['notlar'],
            'etkilesim_tarihi' => $validated['etkilesim_tarihi'],
            'status' => 1,
        ]);

        // Son etkileşim tarihini güncelle
        $kisi->update(['son_etkilesim' => $validated['etkilesim_tarihi']]);

        // Skorunu yeniden hesapla
        $kisi->update(['skor' => $this->scoringService->calculateScore($kisi)]);

        return response()->json([
            'success' => true,
            'message' => 'Etkileşim kaydedildi',
            'data' => $etkilesim->load('kullanici'),
        ]);
    }

    /**
     * Kişinin task'larını getir
     */
    public function getTasks(int $id): JsonResponse
    {
        $kisi = Kisi::findOrFail($id);

        $tasks = $kisi->tasks()
            ->with('kullanici:id,name,email')
            ->orderBy('tarih', 'asc')
            ->orderBy('status', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'bekleyen' => $tasks->where('status', 0)->values(),
                'tamamlanan' => $tasks->where('status', 1)->values(),
            ],
        ]);
    }

    /**
     * Yeni task ekle
     */
    public function addTask(Request $request, int $id): JsonResponse
    {
        $kisi = Kisi::findOrFail($id);

        $validated = $request->validate([
            'baslik' => 'required|string',
            'aciklama' => 'nullable|string',
            'tarih' => 'required|date',
            'saat' => 'nullable',
            'oncelik' => 'required|in:dusuk,normal,yuksek,kritik',
        ]);

        $task = $kisi->tasks()->create([
            'kullanici_id' => auth()->id(),
            'baslik' => $validated['baslik'],
            'aciklama' => $validated['aciklama'],
            'tarih' => $validated['tarih'],
            'saat' => $validated['saat'],
            'oncelik' => $validated['oncelik'],
            'status' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task oluşturuldu',
            'data' => $task->load('kullanici'),
        ]);
    }

    /**
     * Task güncelle (tamamla/güncelle)
     */
    public function updateTask(Request $request, int $taskId): JsonResponse
    {
        $task = KisiTask::findOrFail($taskId);

        $validated = $request->validate([
            'status' => 'nullable|in:0,1',
            'baslik' => 'nullable|string',
            'aciklama' => 'nullable|string',
            'tarih' => 'nullable|date',
            'oncelik' => 'nullable|in:dusuk,normal,yuksek,kritik',
        ]);

        if (isset($validated['status']) && $validated['status'] == 1 && $task->status == 0) {
            $validated['tamamlanma_tarihi'] = now();
        }

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task güncellendi',
            'data' => $task->fresh()->load('kullanici'),
        ]);
    }

    /**
     * Kişinin skorunu hesapla
     */
    public function calculateScore(int $id): JsonResponse
    {
        $kisi = Kisi::findOrFail($id);

        $skor = $this->scoringService->calculateScore($kisi);
        $kisi->update(['skor' => $skor]);

        return response()->json([
            'success' => true,
            'data' => [
                'skor' => $skor,
                'kisi' => $kisi->fresh(),
            ],
        ]);
    }
}
