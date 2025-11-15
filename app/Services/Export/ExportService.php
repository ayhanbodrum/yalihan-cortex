<?php

namespace App\Services\Export;

use App\Models\Ilan;
use App\Models\Kisi;
use App\Models\Talep;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Context7: Unified Export Service
 * 
 * Handles Excel and PDF exports for Ilan, Kisi, and Talep models
 */
class ExportService
{
    /**
     * Export data to Excel
     * 
     * @param string $type 'ilan', 'kisi', 'talep'
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToExcel(string $type, Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        // Normalize type (handle plural forms)
        $type = $this->normalizeType($type);
        
        $data = $this->getExportData($type, $request);
        $filename = $this->generateFilename($type, 'xlsx');
        
        return Excel::download(
            new ExportClass($data, $this->getHeaders($type), $type),
            $filename
        );
    }

    /**
     * Export data to PDF
     * 
     * @param string $type 'ilan', 'kisi', 'talep'
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportToPdf(string $type, Request $request): \Illuminate\Http\Response
    {
        // Normalize type (handle plural forms)
        $type = $this->normalizeType($type);
        
        $data = $this->getExportData($type, $request);
        $filename = $this->generateFilename($type, 'pdf');
        
        $pdf = Pdf::loadView('admin.exports.pdf', [
            'type' => $type,
            'data' => $data,
            'headers' => $this->getHeaders($type),
            'title' => $this->getTitle($type),
        ]);

        return $pdf->download($filename);
    }

    /**
     * Normalize type (handle plural forms)
     * 
     * @param string $type
     * @return string
     */
    protected function normalizeType(string $type): string
    {
        $typeMap = [
            'ilanlar' => 'ilan',
            'kisiler' => 'kisi',
            'talepler' => 'talep',
        ];

        return $typeMap[$type] ?? $type;
    }

    /**
     * Get export data based on type
     * 
     * @param string $type
     * @param Request $request
     * @return Collection
     */
    protected function getExportData(string $type, Request $request): Collection
    {
        return match ($type) {
            'ilan' => $this->getIlanData($request),
            'kisi' => $this->getKisiData($request),
            'talep' => $this->getTalepData($request),
            default => collect(),
        };
    }

    /**
     * Get Ilan export data
     * 
     * @param Request $request
     * @return Collection
     */
    protected function getIlanData(Request $request): Collection
    {
        $query = Ilan::with([
            'ilanSahibi:id,ad,soyad',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'anaKategori:id,name',
            'altKategori:id,name',
        ]);

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('baslik', 'like', "%{$request->search}%")
                  ->orWhere('aciklama', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori_id')) {
            $query->where('ana_kategori_id', $request->kategori_id);
        }

        if ($request->filled('il_id')) {
            $query->where('il_id', $request->il_id);
        }

        return $query->get();
    }

    /**
     * Get Kisi export data
     * 
     * @param Request $request
     * @return Collection
     */
    protected function getKisiData(Request $request): Collection
    {
        $query = Kisi::with([
            'danisman:id,name,email',
            'il:id,il_adi',
            'ilce:id,ilce_adi',
        ]);

        // Apply filters
        if ($request->filled('q')) {
            $query->search($request->q);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'Aktif' ? true : false);
        }

        if ($request->filled('kisi_tipi')) {
            $query->where('kisi_tipi', $request->kisi_tipi);
        }

        return $query->get();
    }

    /**
     * Get Talep export data
     * 
     * @param Request $request
     * @return Collection
     */
    protected function getTalepData(Request $request): Collection
    {
        $query = Talep::with([
            'kisi:id,ad,soyad,telefon',
            'danisman:id,name,email',
            'il:id,il_adi',
        ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tip')) {
            $query->where('tip', $request->tip);
        }

        return $query->get();
    }

    /**
     * Get headers for export type
     * 
     * @param string $type
     * @return array
     */
    protected function getHeaders(string $type): array
    {
        return match ($type) {
            'ilan' => [
                'ID',
                'Başlık',
                'Fiyat',
                'Para Birimi',
                'Durum',
                'İlan Sahibi',
                'İl',
                'İlçe',
                'Kategori',
                'Alt Kategori',
                'Kayıt Tarihi',
            ],
            'kisi' => [
                'ID',
                'Ad Soyad',
                'Telefon',
                'E-posta',
                'Kişi Tipi',
                'Durum',
                'Danışman',
                'İl',
                'İlçe',
                'Kayıt Tarihi',
            ],
            'talep' => [
                'ID',
                'Başlık',
                'Tip',
                'Durum',
                'Kişi',
                'İletişim',
                'İl',
                'İlçe',
                'Kayıt Tarihi',
            ],
            default => [],
        };
    }

    /**
     * Get title for export type
     * 
     * @param string $type
     * @return string
     */
    protected function getTitle(string $type): string
    {
        return match ($type) {
            'ilan' => 'İlan Raporu',
            'kisi' => 'Kişi Raporu',
            'talep' => 'Talep Raporu',
            default => 'Rapor',
        };
    }

    /**
     * Generate filename
     * 
     * @param string $type
     * @param string $extension
     * @return string
     */
    protected function generateFilename(string $type, string $extension): string
    {
        $date = now()->format('Y-m-d');
        return "{$type}_raporu_{$date}.{$extension}";
    }
}

