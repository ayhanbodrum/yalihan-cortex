<?php

namespace App\Http\Controllers\Admin;

use App\Models\Talep;
use App\Modules\TalepAnaliz\Services\AIAnalizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Talep Rapor Controller
 * 
 * Context7 Standardı: C7-TALEP-RAPOR-2025-11-05
 * 
 * PDF ve Excel rapor oluşturma
 */
class TalepRaporController extends AdminController
{
    protected AIAnalizService $aiAnalizService;

    public function __construct(AIAnalizService $analizService)
    {
        $this->aiAnalizService = $analizService;
    }

    /**
     * Talep analiz raporu oluştur (PDF/Excel)
     * 
     * GET /admin/talepler/{id}/rapor?format=pdf
     * GET /admin/talepler/{id}/rapor?format=excel
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function raporOlustur(Request $request, int $id)
    {
        $request->validate([
            'format' => 'nullable|in:pdf,excel'
        ]);

        $talep = Talep::with(['kisi', 'il', 'ilce', 'mahalle'])->findOrFail($id);
        $format = $request->input('format', 'pdf');

        // Analiz sonuçlarını al
        $analizSonucu = $this->aiAnalizService->analizEt($talep);

        if ($format === 'excel') {
            return $this->excelRapor($talep, $analizSonucu);
        }

        return $this->pdfRapor($talep, $analizSonucu);
    }

    /**
     * PDF rapor oluştur
     * 
     * @param Talep $talep
     * @param array $analizSonucu
     * @return \Illuminate\Http\Response
     */
    protected function pdfRapor(Talep $talep, array $analizSonucu)
    {
        $data = [
            'talep' => $talep,
            'analiz_sonucu' => $analizSonucu,
            'tarih' => now()->format('d.m.Y H:i'),
        ];

        $pdf = Pdf::loadView('admin.talepler.reports.pdf', $data);
        
        $dosyaAdi = "Talep_Analiz_Raporu_{$talep->id}_" . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($dosyaAdi);
    }

    /**
     * Excel rapor oluştur
     * 
     * @param Talep $talep
     * @param array $analizSonucu
     * @return \Illuminate\Http\Response
     */
    protected function excelRapor(Talep $talep, array $analizSonucu)
    {
        $data = [
            ['Talep ID', $talep->id],
            ['Talep Sahibi', $talep->kisi?->tam_ad ?? 'Bilinmiyor'],
            ['İl', $talep->il?->il_adi ?? ''],
            ['İlçe', $talep->ilce?->ilce_adi ?? ''],
            ['Min Fiyat', $talep->min_fiyat ?? 0],
            ['Max Fiyat', $talep->max_fiyat ?? 0],
            ['Oda Sayısı', $talep->oda_sayisi ?? ''],
            ['Tarih', now()->format('d.m.Y H:i')],
            [''],
            ['Eşleşen Emlaklar'],
            ['ID', 'Başlık', 'Fiyat', 'Lokasyon', 'Metrekare', 'Eşleşme %'],
        ];

        // Analiz sonucu formatını kontrol et
        $eslesmeler = [];
        if (isset($analizSonucu['eslesmeler'])) {
            $eslesmeler = $analizSonucu['eslesmeler'];
        } elseif (is_array($analizSonucu) && isset($analizSonucu[0]['emlak'])) {
            $eslesmeler = $analizSonucu;
        }

        foreach ($eslesmeler as $eslesme) {
            $ilan = $eslesme['emlak'] ?? null;
            if ($ilan) {
                $data[] = [
                    $ilan->id ?? '',
                    $ilan->baslik ?? '',
                    ($ilan->fiyat ?? 0) . ' ' . ($ilan->para_birimi ?? 'TL'),
                    ($ilan->il?->il_adi ?? '') . ' / ' . ($ilan->ilce?->ilce_adi ?? ''),
                    $ilan->metrekare ?? '-',
                    number_format($eslesme['eslesme_yuzdesi'] ?? 0, 1) . '%',
                ];
            }
        }

        $dosyaAdi = "Talep_Analiz_Raporu_{$talep->id}_" . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
        }, $dosyaAdi);
    }

    /**
     * Toplu rapor oluştur (birden fazla talep için)
     * 
     * POST /admin/talepler/toplu-rapor
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function topluRapor(Request $request)
    {
        $request->validate([
            'talep_ids' => 'required|array|min:1',
            'talep_ids.*' => 'required|exists:talepler,id',
            'format' => 'nullable|in:pdf,excel'
        ]);

        $talepIds = $request->talep_ids;
        $format = $request->input('format', 'excel');

        $talepler = Talep::with(['kisi', 'il', 'ilce'])
            ->whereIn('id', $talepIds)
            ->get();

        if ($format === 'pdf') {
            return $this->topluPdfRapor($talepler);
        }

        return $this->topluExcelRapor($talepler);
    }

    /**
     * Toplu Excel rapor
     */
    protected function topluExcelRapor($talepler)
    {
        $data = [
            ['Toplu Talep Analiz Raporu'],
            ['Tarih', now()->format('d.m.Y H:i')],
            ['Toplam Talep', $talepler->count()],
            [''],
            ['Talep ID', 'Talep Sahibi', 'İl', 'İlçe', 'Min Fiyat', 'Max Fiyat', 'Oda Sayısı', 'Durum'],
        ];

        foreach ($talepler as $talep) {
            $data[] = [
                $talep->id,
                $talep->kisi?->tam_ad ?? 'Bilinmiyor',
                $talep->il?->il_adi ?? '',
                $talep->ilce?->ilce_adi ?? '',
                $talep->min_fiyat ?? 0,
                $talep->max_fiyat ?? 0,
                $talep->oda_sayisi ?? '',
                $talep->status ?? 'Aktif',
            ];
        }

        $dosyaAdi = "Toplu_Talep_Raporu_" . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
        }, $dosyaAdi);
    }

    /**
     * Toplu PDF rapor
     */
    protected function topluPdfRapor($talepler)
    {
        $data = [
            'talepler' => $talepler,
            'tarih' => now()->format('d.m.Y H:i'),
        ];

        $pdf = Pdf::loadView('admin.talepler.reports.toplu_pdf', $data);
        
        $dosyaAdi = "Toplu_Talep_Raporu_" . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($dosyaAdi);
    }
}

