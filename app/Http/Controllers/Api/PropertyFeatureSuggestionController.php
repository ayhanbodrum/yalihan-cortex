<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PropertyFeatureSuggestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyFeatureSuggestionController extends Controller
{
    protected $featureSuggestionService;

    public function __construct(PropertyFeatureSuggestionService $featureSuggestionService)
    {
        $this->featureSuggestionService = $featureSuggestionService;
    }

    /**
     * Kategori bazında özellik önerileri
     */
    public function getFeatureSuggestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
            'sub_category' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $suggestions = $this->featureSuggestionService->getFeatureSuggestions(
                $request->input('category'),
                $request->input('sub_category')
            );

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özellik önerileri alınamadı',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Akıllı öneriler
     */
    public function getSmartSuggestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
            'current_data' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $suggestions = $this->featureSuggestionService->getSmartSuggestions(
                $request->input('category'),
                $request->input('current_data', [])
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'suggestions' => $suggestions,
                    'count' => count($suggestions)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Akıllı öneriler alınamadı',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Özellik doğrulama
     */
    public function validateFeatures(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
            'features' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category = $request->input('category');
            $features = $request->input('features');

            $validationResults = $this->validateCategoryFeatures($category, $features);

            return response()->json([
                'success' => true,
                'data' => $validationResults
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özellik doğrulama başarısız',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kategori özelliklerini doğrula
     */
    private function validateCategoryFeatures(string $category, array $features): array
    {
        $results = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'suggestions' => []
        ];

        switch (strtolower($category)) {
            case 'arsa':
                $results = $this->validateArsaFeatures($features);
                break;
            case 'yazlık':
            case 'yazlik':
                $results = $this->validateYazlikFeatures($features);
                break;
            case 'villa':
            case 'daire':
                $results = $this->validateVillaDaireFeatures($features);
                break;
            case 'işyeri':
            case 'isyeri':
                $results = $this->validateIsyeriFeatures($features);
                break;
            default:
                $results['warnings'][] = 'Bilinmeyen kategori';
        }

        return $results;
    }

    /**
     * Arsa özelliklerini doğrula
     */
    private function validateArsaFeatures(array $features): array
    {
        $results = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'suggestions' => []
        ];

        // Ada numarası kontrolü
        if (isset($features['ada_no'])) {
            if (!is_numeric($features['ada_no']) || $features['ada_no'] <= 0) {
                $results['errors'][] = 'Ada numarası pozitif bir sayı olmalıdır';
                $results['valid'] = false;
            }
        }

        // Parsel numarası kontrolü
        if (isset($features['parsel_no'])) {
            if (!is_numeric($features['parsel_no']) || $features['parsel_no'] <= 0) {
                $results['errors'][] = 'Parsel numarası pozitif bir sayı olmalıdır';
                $results['valid'] = false;
            }
        }

        // KAKS kontrolü
        if (isset($features['kaks'])) {
            $kaks = (float)$features['kaks'];
            if ($kaks < 0 || $kaks > 2) {
                $results['warnings'][] = 'KAKS değeri genellikle 0-2 arasındadır';
            }
        }

        // TAKS kontrolü
        if (isset($features['taks'])) {
            $taks = (float)$features['taks'];
            if ($taks < 0 || $taks > 1) {
                $results['warnings'][] = 'TAKS değeri genellikle 0-1 arasındadır';
            }
        }

        return $results;
    }

    /**
     * Yazlık özelliklerini doğrula
     */
    private function validateYazlikFeatures(array $features): array
    {
        $results = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'suggestions' => []
        ];

        // Günlük fiyat kontrolü
        if (isset($features['gunluk_fiyat'])) {
            $price = (float)$features['gunluk_fiyat'];
            if ($price < 100) {
                $results['warnings'][] = 'Günlük fiyat düşük görünüyor';
            } elseif ($price > 2000) {
                $results['warnings'][] = 'Günlük fiyat yüksek görünüyor';
            }
        }

        // Minimum konaklama kontrolü
        if (isset($features['min_konaklama'])) {
            $minKonaklama = (int)$features['min_konaklama'];
            if ($minKonaklama < 1) {
                $results['errors'][] = 'Minimum konaklama en az 1 gün olmalıdır';
                $results['valid'] = false;
            }
        }

        return $results;
    }

    /**
     * Villa/Daire özelliklerini doğrula
     */
    private function validateVillaDaireFeatures(array $features): array
    {
        $results = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'suggestions' => []
        ];

        // Oda sayısı kontrolü
        if (isset($features['oda_sayisi'])) {
            $odaSayisi = (int)$features['oda_sayisi'];
            if ($odaSayisi < 1 || $odaSayisi > 10) {
                $results['warnings'][] = 'Oda sayısı genellikle 1-10 arasındadır';
            }
        }

        // Net m² kontrolü
        if (isset($features['net_m2'])) {
            $netM2 = (int)$features['net_m2'];
            if ($netM2 < 30) {
                $results['warnings'][] = 'Net m² düşük görünüyor';
            } elseif ($netM2 > 500) {
                $results['warnings'][] = 'Net m² yüksek görünüyor';
            }
        }

        // Banyo sayısı kontrolü
        if (isset($features['banyo_sayisi'])) {
            $banyoSayisi = (int)$features['banyo_sayisi'];
            if ($banyoSayisi < 1) {
                $results['errors'][] = 'En az 1 banyo olmalıdır';
                $results['valid'] = false;
            }
        }

        return $results;
    }

    /**
     * İşyeri özelliklerini doğrula
     */
    private function validateIsyeriFeatures(array $features): array
    {
        $results = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'suggestions' => []
        ];

        // Ciro bilgisi kontrolü
        if (isset($features['ciro_bilgisi'])) {
            $ciro = (float)$features['ciro_bilgisi'];
            if ($ciro < 0) {
                $results['errors'][] = 'Ciro bilgisi negatif olamaz';
                $results['valid'] = false;
            }
        }

        // Personel kapasitesi kontrolü
        if (isset($features['personel_kapasitesi'])) {
            $kapasite = (int)$features['personel_kapasitesi'];
            if ($kapasite < 0) {
                $results['errors'][] = 'Personel kapasitesi negatif olamaz';
                $results['valid'] = false;
            }
        }

        return $results;
    }
}
