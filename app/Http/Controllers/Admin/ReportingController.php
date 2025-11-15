<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Export\ExportService;

class ReportingController extends AdminController
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function index(Request $request)
    {
        return view('admin.reports.index');
    }

    /**
     * Kişi (Müşteri) Reports
     * Context7: Method renamed but kept musteriReports name for backward compatibility
     * View uses: admin/reports/kisiler.blade.php (renamed from musteriler.blade.php)
     */
    public function musteriReports(Request $request)
    {
        // Context7: View renamed to kisiler.blade.php
        return view('admin.reports.kisiler');
    }

    /**
     * Performance Reports
     */
    public function performanceReports(Request $request)
    {
        return view('admin.reports.performance');
    }

    /**
     * Export to Excel
     * Context7: Unified export service implementation
     */
    public function exportExcel(Request $request)
    {
        try {
            $type = $request->input('type', 'ilan'); // Default: ilan

            // Validate type
            if (!in_array($type, ['ilan', 'kisi', 'talep'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz export tipi'
                ], 400);
            }

            return $this->exportService->exportToExcel($type, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Excel export hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to PDF
     * Context7: Unified export service implementation
     */
    public function exportPdf(Request $request)
    {
        try {
            $type = $request->input('type', 'ilan'); // Default: ilan

            // Validate type
            if (!in_array($type, ['ilan', 'kisi', 'talep'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz export tipi'
                ], 400);
            }

            return $this->exportService->exportToPdf($type, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PDF export hatası: ' . $e->getMessage()
            ], 500);
        }
    }
}
