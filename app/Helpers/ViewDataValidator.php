<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class ViewDataValidator
{
    /**
     * Controller'dan view'e aktarılan verilerin kontrolünü yapar
     *
     * @param array $controllerData Controller'dan gelen veriler
     * @param array $viewData View'de kullanılan veriler
     * @param string $viewName View adı
     * @return array Eksik veya yanlış veriler
     */
    public static function validate(array $controllerData, array $viewData, string $viewName): array
    {
        $issues = [];

        foreach ($controllerData as $key => $controllerValue) {
            // View'de bu veri var mı?
            if (!array_key_exists($key, $viewData)) {
                $issues[] = [
                    'type' => 'missing_data',
                    'key' => $key,
                    'message' => "Controller'dan gelen '{$key}' verisi view'de kullanılmıyor",
                    'controller_type' => gettype($controllerValue),
                    'controller_count' => is_countable($controllerValue) ? count($controllerValue) : 'N/A'
                ];
                continue;
            }

            $viewValue = $viewData[$key];

            // Boş koleksiyon ile değiştirme kontrolü
            if (is_countable($controllerValue) && is_countable($viewValue)) {
                $controllerCount = count($controllerValue);
                $viewCount = count($viewValue);

                if ($controllerCount > 0 && $viewCount === 0) {
                    $issues[] = [
                        'type' => 'data_override',
                        'key' => $key,
                        'message' => "Controller'dan {$controllerCount} adet veri gelirken view'de boş koleksiyon kullanılıyor",
                        'controller_count' => $controllerCount,
                        'view_count' => $viewCount,
                        'suggestion' => "'{$key}' => \${$key} ?? collect(),"
                    ];
                }
            }
        }

        // Log kaydet
        if (!empty($issues)) {
            Log::warning("View Data Validation Issues in {$viewName}", [
                'issues' => $issues,
                'view' => $viewName
            ]);
        }

        return $issues;
    }

    /**
     * Form wizard özelinde veri kontrolü
     */
    public static function validateFormWizard(array $data, string $formType = 'ilan'): array
    {
        $requiredData = [
            'ilan' => [
                'anaKategoriler' => 'Ana kategoriler boş olamaz',
                'altKategoriler' => 'Alt kategoriler boş olamaz',
                'yayinTipleri' => 'Yayın tipleri boş olamaz',
                'danismanlar' => 'Danışmanlar boş olamaz'
            ]
        ];

        $issues = [];
        $required = $requiredData[$formType] ?? [];

        foreach ($required as $key => $message) {
            if (!isset($data[$key]) || (is_countable($data[$key]) && count($data[$key]) === 0)) {
                $issues[] = [
                    'type' => 'form_wizard_data_missing',
                    'key' => $key,
                    'message' => $message,
                    'form_type' => $formType
                ];
            }
        }

        return $issues;
    }
}
