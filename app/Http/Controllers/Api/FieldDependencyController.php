<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriYayinTipiFieldDependency;
use App\Models\IlanKategori;
use App\Services\Response\ResponseService;
use App\Traits\ValidatesApiRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FieldDependencyController extends Controller
{
    use ValidatesApiRequests;
    /**
     * Get field dependencies for a specific category and publication type
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $kategoriSlug = $request->input('kategori_slug');
            $yayinTipi = $request->input('yayin_tipi');
            $kategoriId = $request->input('kategori_id');

            // Kategori slug'ı ID'den al (eğer sadece ID verilmişse)
            if (!$kategoriSlug && $kategoriId) {
                $kategori = IlanKategori::find($kategoriId);
                if ($kategori) {
                    $kategoriSlug = $kategori->slug;
                }
            }

            if (!$kategoriSlug) {
                return ResponseService::error('Kategori slug veya ID gerekli', 400);
            }

            // Query builder
            $query = KategoriYayinTipiFieldDependency::where('kategori_slug', $kategoriSlug)
                ->where('status', true) // Context7: enabled → status
                ->orderBy('display_order', 'asc'); // Context7: order → display_order

            // Yayın tipi filtresi (opsiyonel)
            if ($yayinTipi) {
                // Hem ID hem slug/isime göre filtrele
                if (is_numeric($yayinTipi)) {
                    $yayinTipiId = (string) $yayinTipi;
                    // İlgili yayın tipinin metin karşılığını bul (varsa)
                    $yayinTipiText = \App\Models\IlanKategoriYayinTipi::where('id', (int)$yayinTipi)
                        ->value('yayin_tipi');

                    $query->where(function ($q) use ($yayinTipiId, $yayinTipiText) {
                        $q->where('yayin_tipi', $yayinTipiId);
                        if ($yayinTipiText) {
                            $q->orWhere('yayin_tipi', $yayinTipiText);
                        }
                    });
                } else {
                    // Slug/metin olarak geldi
                    $query->where('yayin_tipi', $yayinTipi);
                }
            }

            $fields = $query->get();

            // Group by category (field_category)
            $groupedFields = $fields->groupBy('field_category')->map(function ($categoryFields, $categoryName) {
                return [
                    'category' => $categoryName ?: 'genel',
                    'name' => $this->getCategoryDisplayName($categoryName),
                    'icon' => $this->getCategoryIcon($categoryName),
                    'fields' => $categoryFields->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'slug' => $field->field_slug,
                            'name' => $field->field_name,
                            'type' => $field->field_type,
                            'category' => $field->field_category,
                            'required' => $field->required,
                            'status' => $field->status, // Context7: enabled → status
                            'display_order' => $field->display_order, // Context7: order → display_order
                            'icon' => $field->field_icon,
                            'options' => $field->field_options ? (is_array($field->field_options) ? $field->field_options : json_decode($field->field_options, true)) : null,
                            'unit' => $field->field_unit,
                            'placeholder' => $field->field_placeholder,
                            'help_text' => $field->field_help_text,
                            'validation' => $field->field_validation,
                            'searchable' => $field->searchable,
                            'show_in_card' => $field->show_in_card,
                            'ai_suggestion' => $field->ai_suggestion ?? false,
                            'ai_prompt_key' => $field->ai_prompt_key,
                        ];
                    })->values()
                ];
            })->values();

            return ResponseService::success([
                'data' => $groupedFields,
                'meta' => [
                    'kategori_slug' => $kategoriSlug,
                    'yayin_tipi' => $yayinTipi,
                    'total_fields' => $fields->count(),
                    'required_fields' => $fields->where('required', true)->count(),
                ]
            ], 'Field dependencies başarıyla yüklendi');
        } catch (\Exception $e) {
            Log::error('Field Dependencies API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return ResponseService::serverError('Field dependencies yüklenirken hata oluştu.', $e);
        }
    }

    /**
     * Get category display name
     */
    private function getCategoryDisplayName($category)
    {
        $names = [
            'fiyat' => 'Fiyat Bilgileri',
            'fiyatlandirma' => '💰 Fiyatlandırma',
            'fiziksel_ozellikler' => '📐 Fiziksel Özellikler',
            'donanim_tesisat' => '🔌 Donanım & Tesisat',
            'dismekan_olanaklar' => '🏖️ Dış Mekan & Olanaklar',
            'yatak_odasi_konfor' => '🛏️ Yatak Odası & Konfor',
            'ek_hizmetler' => '➕ Ek Hizmetler',
            'arsa' => 'Arsa Özellikleri',
            'konut' => 'Konut Özellikleri',
            'yazlik' => 'Yazlık Özellikleri',
            'ozellik' => 'Genel Özellikler',
            'olanaklar' => 'Olanaklar',
            'sezonluk' => 'Sezonluk Kiralama',
            'isyeri' => 'İşyeri Özellikleri',
            'genel' => 'Genel Bilgiler',
            'general' => 'Genel Bilgiler',
        ];

        return $names[$category] ?? ucfirst($category);
    }

    /**
     * Get category icon (Emoji - always works!)
     */
    private function getCategoryIcon($category)
    {
        $icons = [
            'fiyat' => '💰',
            'fiyatlandirma' => '💰',
            'fiziksel_ozellikler' => '📐',
            'donanim_tesisat' => '🔌',
            'dismekan_olanaklar' => '🏖️',
            'yatak_odasi_konfor' => '🛏️',
            'ek_hizmetler' => '➕',
            'arsa' => '🗺️',
            'konut' => '🏠',
            'yazlik' => '🏖️',
            'ozellik' => '⭐',
            'olanaklar' => '🎯',
            'sezonluk' => '📅',
            'isyeri' => '🏢',
            'genel' => 'ℹ️',
            'general' => 'ℹ️',
        ];

        return $icons[$category] ?? '📦';
    }

    /**
     * Get field dependencies by category (alternative endpoint)
     */
    public function getByCategory($kategoriId)
    {
        try {
            $kategori = IlanKategori::findOrFail($kategoriId);

            $fields = KategoriYayinTipiFieldDependency::where('kategori_slug', $kategori->slug)
                ->where('status', true) // Context7: enabled → status
                ->orderBy('display_order', 'asc') // Context7: order → display_order
                ->get();

            // Group by publication type
            $byYayinTipi = $fields->groupBy('yayin_tipi')->map(function ($fields) {
                return $fields->groupBy('field_category')->map(function ($categoryFields, $categoryName) {
                    return [
                        'category' => $categoryName,
                        'name' => $this->getCategoryDisplayName($categoryName),
                        'fields' => $categoryFields->values()
                    ];
                })->values();
            });

            return ResponseService::success([
                'data' => [
                    'kategori' => [
                        'id' => $kategori->id,
                        'name' => $kategori->name,
                        'slug' => $kategori->slug,
                    ],
                    'fields_by_yayin_tipi' => $byYayinTipi
                ]
            ], 'Kategori field dependencies başarıyla getirildi');
        } catch (\Exception $e) {
            return ResponseService::serverError('Field dependencies yüklenirken hata oluştu.', $e);
        }
    }
}
