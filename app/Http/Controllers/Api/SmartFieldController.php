<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SmartFieldGenerationService;
use App\Services\AIService;

class SmartFieldController extends Controller
{
    protected $smartFieldService;
    protected $aiService;

    public function __construct(SmartFieldGenerationService $smartFieldService, AIService $aiService)
    {
        $this->smartFieldService = $smartFieldService;
        $this->aiService = $aiService;
    }

    /**
     * Kategori bazlı akıllı field önerileri
     */
    public function getSmartFields(Request $request)
    {
        $request->validate([
            'kategori_slug' => 'required|string',
            'yayin_tipi' => 'nullable|string'
        ]);

        try {
            $smartFields = $this->smartFieldService->getSmartFieldsForCategory(
                $request->kategori_slug,
                $request->yayin_tipi
            );

            return response()->json([
                'success' => true,
                'data' => $smartFields,
                'message' => 'Akıllı field önerileri başarıyla alındı'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Field önerileri alınırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kategori bazlı özellik matrisi
     */
    public function getCategoryMatrix(Request $request)
    {
        $request->validate([
            'kategori_slug' => 'required|string'
        ]);

        try {
            $matrix = $this->smartFieldService->generateCategoryMatrix($request->kategori_slug);

            return response()->json([
                'success' => true,
                'data' => $matrix,
                'message' => 'Kategori matrisi başarıyla oluşturuldu'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Matris oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Akıllı form oluştur
     */
    public function generateSmartForm(Request $request)
    {
        $request->validate([
            'kategori_slug' => 'required|string',
            'yayin_tipi' => 'nullable|string'
        ]);

        try {
            $smartForm = $this->smartFieldService->generateSmartForm(
                $request->kategori_slug,
                $request->yayin_tipi
            );

            return response()->json([
                'success' => true,
                'data' => $smartForm,
                'message' => 'Akıllı form başarıyla oluşturuldu'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Form oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI ile özellik analizi
     */
    public function analyzeProperty(Request $request)
    {
        $request->validate([
            'property_data' => 'required|array',
            'context' => 'nullable|array'
        ]);

        try {
            $analysis = $this->aiService->analyzePropertyFeatures(
                $request->property_data,
                $request->context ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $analysis,
                'message' => 'Özellik analizi başarıyla tamamlandı'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Analiz sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI ile field önerileri
     */
    public function getAISuggestions(Request $request)
    {
        $request->validate([
            'kategori_slug' => 'required|string',
            'yayin_tipi' => 'nullable|string',
            'context' => 'nullable|array'
        ]);

        try {
            $suggestions = $this->aiService->suggestFieldsForCategory(
                $request->kategori_slug,
                $request->yayin_tipi,
                $request->context ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $suggestions,
                'message' => 'AI önerileri başarıyla alındı'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI önerileri alınırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
