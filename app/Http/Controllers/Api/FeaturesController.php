<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\Log;

class FeaturesController extends Controller
{
    /**
     * Kategori bazlı özellikler
     */
    public function getFeaturesByCategory($categoryId)
    {
        try {
            $features = $this->getCategoryFeatures($categoryId);

            return ResponseService::success([
                'features' => $features,
                'category_id' => $categoryId,
            ], 'Kategori özellikleri başarıyla getirildi');
        } catch (\Exception $e) {
            Log::error('Özellik yükleme hatası: '.$e->getMessage());

            return ResponseService::serverError('Özellikler yüklenirken hata oluştu.', $e);
        }
    }

    /**
     * Kategori özelliklerini al
     */
    private function getCategoryFeatures($categoryId)
    {
        $features = [];

        switch ($categoryId) {
            case 1: // Villa/Daire
            case 3: // Villa/Daire (alternatif ID)
                $features = [
                    'oda_sayisi' => ['label' => 'Oda Sayısı', 'type' => 'number', 'required' => true],
                    'banyo_sayisi' => ['label' => 'Banyo Sayısı', 'type' => 'number', 'required' => true],
                    'net_metrekare' => ['label' => 'Net m²', 'type' => 'number', 'required' => true],
                    'brut_metrekare' => ['label' => 'Brüt m²', 'type' => 'number', 'required' => false],
                    'kat' => ['label' => 'Kat', 'type' => 'number', 'required' => false],
                    'toplam_kat' => ['label' => 'Toplam Kat', 'type' => 'number', 'required' => false],
                    'balkon_sayisi' => ['label' => 'Balkon Sayısı', 'type' => 'number', 'required' => false],
                    'asansor' => ['label' => 'Asansör', 'type' => 'checkbox', 'required' => false],
                    'otopark' => ['label' => 'Otopark', 'type' => 'checkbox', 'required' => false],
                ];
                break;

            case 2: // Arsa
            case 4: // Arsa (alternatif ID)
                $features = [
                    'ada_no' => ['label' => 'Ada No', 'type' => 'text', 'required' => true],
                    'parsel_no' => ['label' => 'Parsel No', 'type' => 'text', 'required' => true],
                    'imar_durumu' => ['label' => 'İmar Durumu', 'type' => 'select', 'required' => true, 'options' => ['İmarlı', 'İmarsız']],
                    'kaks' => ['label' => 'KAKS', 'type' => 'number', 'required' => false],
                    'taks' => ['label' => 'TAKS', 'type' => 'number', 'required' => false],
                    'gabari' => ['label' => 'Gabari', 'type' => 'number', 'required' => false],
                    'elektrik' => ['label' => 'Elektrik', 'type' => 'checkbox', 'required' => false],
                    'su' => ['label' => 'Su', 'type' => 'checkbox', 'required' => false],
                    'dogalgaz' => ['label' => 'Doğalgaz', 'type' => 'checkbox', 'required' => false],
                ];
                break;

            case 3: // Yazlık
            case 5: // Yazlık (alternatif ID)
                $features = [
                    'gunluk_fiyat' => ['label' => 'Günlük Fiyat', 'type' => 'number', 'required' => true],
                    'haftalik_fiyat' => ['label' => 'Haftalık Fiyat', 'type' => 'number', 'required' => false],
                    'aylik_fiyat' => ['label' => 'Aylık Fiyat', 'type' => 'number', 'required' => false],
                    'sezon_baslangic' => ['label' => 'Sezon Başlangıç', 'type' => 'date', 'required' => false],
                    'sezon_bitis' => ['label' => 'Sezon Bitiş', 'type' => 'date', 'required' => false],
                    'havuz' => ['label' => 'Havuz', 'type' => 'checkbox', 'required' => false],
                    'havuz_tipi' => ['label' => 'Havuz Tipi', 'type' => 'select', 'required' => false, 'options' => ['Özel', 'Ortak', 'Yok']],
                    'misafir_sayisi' => ['label' => 'Misafir Sayısı', 'type' => 'number', 'required' => true],
                    'cocuk_sayisi' => ['label' => 'Çocuk Sayısı', 'type' => 'number', 'required' => false],
                ];
                break;

            case 4: // İşyeri
                $features = [
                    'isyeri_tipi' => ['label' => 'İşyeri Tipi', 'type' => 'select', 'required' => true, 'options' => ['Ofis', 'Dükkan', 'Fabrika', 'Depo']],
                    'kira_tutari' => ['label' => 'Kira Tutarı', 'type' => 'number', 'required' => false],
                    'ciro' => ['label' => 'Ciro', 'type' => 'number', 'required' => false],
                    'ruhsat_tipi' => ['label' => 'Ruhsat Tipi', 'type' => 'select', 'required' => false, 'options' => ['İşyeri', 'Ofis', 'Ticari']],
                    'kapasite' => ['label' => 'Kapasite', 'type' => 'number', 'required' => false],
                    'otopark' => ['label' => 'Otopark', 'type' => 'checkbox', 'required' => false],
                    'depo' => ['label' => 'Depo', 'type' => 'checkbox', 'required' => false],
                ];
                break;

            default:
                $features = [
                    'genel_ozellik' => ['label' => 'Genel Özellik', 'type' => 'text', 'required' => false],
                ];
                break;
        }

        return $features;
    }
}
