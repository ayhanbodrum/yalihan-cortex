<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\FeatureCategory;
use App\Models\IlanKategori;
use App\Models\OzellikKategori;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    /**
     * Kategoriye göre özellikleri getir
     */
    public function getFeaturesByCategory(Request $request, $categoryId)
    {
        try {
            $category = IlanKategori::find($categoryId);
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori bulunamadı'
                ], 404);
            }

            // Kategoriye bağlı özellik kategorilerini yükle
            $featureCategories = $this->getFeatureCategoriesForIlanCategory($category);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category,
                    'featureCategories' => $featureCategories
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özellikler yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * İlan kategorisine göre özellik kategorilerini getir
     */
    private function getFeatureCategoriesForIlanCategory(IlanKategori $category)
    {
        $categoryName = strtolower($category->name);

        // OzellikKategori modeli ile özellik kategorilerini getir
        $query = OzellikKategori::where('status', 1);

        // Ana kategorilere göre filtreleme
        if (str_contains($categoryName, 'arsa') || str_contains($categoryName, 'tarla') ||
            str_contains($categoryName, 'bağ') || str_contains($categoryName, 'bahçe') ||
            str_contains($categoryName, 'zeytinlik')) {

            // Arsa kategorileri için uygun özellikler
            $query->whereJsonContains('uyumlu_emlak_turleri', 'arsa')
                  ->orWhereJsonContains('uyumlu_emlak_turleri', 'tarla')
                  ->orWhere('name', 'LIKE', '%Arsa%')
                  ->orWhere('name', 'LIKE', '%Temel%')
                  ->orWhere('name', 'LIKE', '%Lokasyon%');
        }
        elseif (str_contains($categoryName, 'yazlık') || str_contains($categoryName, 'tatil') ||
                str_contains($categoryName, 'villa') || str_contains($categoryName, 'apart')) {

            // Yazlık/Tatil kategorileri için uygun özellikler
            $query->whereJsonContains('uyumlu_emlak_turleri', 'yazlik')
                  ->orWhereJsonContains('uyumlu_emlak_turleri', 'villa')
                  ->orWhere('name', 'LIKE', '%Yazlık%')
                  ->orWhere('name', 'LIKE', '%Tatil%')
                  ->orWhere('name', 'LIKE', '%Temel%')
                  ->orWhere('name', 'LIKE', '%Oda%')
                  ->orWhere('name', 'LIKE', '%Lüks%');
        }
        else {
            // Diğer kategoriler için genel özellikler
            $query->where('name', 'LIKE', '%Temel%')
                  ->orWhere('name', 'LIKE', '%Oda%')
                  ->orWhere('name', 'LIKE', '%Bina%');
        }

        $featureCategories = $query->orderBy('siralama', 'asc')
                                  ->get()
                                  ->map(function($kategori) {
                                      return [
                                          'id' => $kategori->id,
                                          'name' => $kategori->name,
                                          'icon' => $kategori->icon,
                                          'features' => $this->getFeaturesToCategory($kategori)
                                      ];
                                  });

        return $featureCategories;
    }

    /**
     * Özellik kategorisine ait özellikleri getir
     */
    private function getFeaturesToCategory($kategori)
    {
        // Özellik kategorisi için örnek özellikler oluştur
        $features = [];

        switch(strtolower($kategori->name)) {
            case 'temel özellikler':
                $features = [
                    ['id' => 1, 'name' => 'Metrekare', 'slug' => 'metrekare', 'type' => 'number', 'unit' => 'm²'],
                    ['id' => 2, 'name' => 'Yaş', 'slug' => 'yas', 'type' => 'number', 'unit' => 'yıl'],
                    ['id' => 3, 'name' => 'Kat', 'slug' => 'kat', 'type' => 'number', 'unit' => ''],
                ];
                break;

            case 'oda bilgileri':
                $features = [
                    ['id' => 4, 'name' => 'Oda Sayısı', 'slug' => 'oda_sayisi', 'type' => 'select', 'options' => ['1+0', '1+1', '2+1', '3+1', '4+1', '5+1']],
                    ['id' => 5, 'name' => 'Banyo Sayısı', 'slug' => 'banyo_sayisi', 'type' => 'number', 'unit' => ''],
                    ['id' => 6, 'name' => 'Salon', 'slug' => 'salon', 'type' => 'boolean'],
                ];
                break;

            case 'arsa özellikleri':
                $features = [
                    ['id' => 7, 'name' => 'Arsa Metrekare', 'slug' => 'arsa_metrekare', 'type' => 'number', 'unit' => 'm²'],
                    ['id' => 8, 'name' => 'İmar Durumu', 'slug' => 'imar_durumu', 'type' => 'select', 'options' => ['İmarlı', 'İmarsız', 'Tarla', 'Villa İmarlı']],
                    ['id' => 9, 'name' => 'Ada No', 'slug' => 'ada_no', 'type' => 'number', 'unit' => ''],
                    ['id' => 10, 'name' => 'Parsel No', 'slug' => 'parsel_no', 'type' => 'number', 'unit' => ''],
                ];
                break;

            case 'yazlık özellikleri':
                $features = [
                    ['id' => 11, 'name' => 'Denize Mesafe', 'slug' => 'denize_mesafe', 'type' => 'number', 'unit' => 'mt'],
                    ['id' => 12, 'name' => 'Havuz', 'slug' => 'havuz', 'type' => 'boolean'],
                    ['id' => 13, 'name' => 'Bahçe', 'slug' => 'bahce', 'type' => 'boolean'],
                    ['id' => 14, 'name' => 'Deniz Manzarası', 'slug' => 'deniz_manzarasi', 'type' => 'boolean'],
                ];
                break;

            default:
                $features = [
                    ['id' => 15, 'name' => 'Özellik', 'slug' => 'ozellik', 'type' => 'boolean'],
                ];
        }

        return $features;
    }

    /**
     * Tüm özellik kategorilerini getir
     */
    public function getAllFeatureCategories(Request $request)
    {
        try {
            $featureCategories = FeatureCategory::with(['features' => function($query) {
                $query->where('status', true)->orderBy('order');
            }])
            ->where('status', true)
            ->orderBy('order')
            ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'featureCategories' => $featureCategories
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özellik kategorileri yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Özellik önerisi getir
     */
    public function suggestFeatures(Request $request)
    {
        try {
            $query = $request->get('q');
            if (!$query || strlen($query) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arama terimi en az 2 karakter olmalı'
                ], 400);
            }

            $features = Feature::where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->with('featureCategory')
                ->where('status', true)
                ->orderBy('name')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'features' => $features
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özellik önerisi alınırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
