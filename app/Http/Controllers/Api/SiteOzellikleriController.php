<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SiteOzellikleriController extends Controller
{
    /**
     * Site özellikleri listesi
     */
    public function index(): JsonResponse
    {
        try {
            $ozellikler = [
                ['id' => 'guvenlik', 'name' => 'Güvenlik', 'icon' => '🛡️'],
                ['id' => 'otopark', 'name' => 'Otopark', 'icon' => '🚗'],
                ['id' => 'havuz', 'name' => 'Havuz', 'icon' => '🏊'],
                ['id' => 'spor', 'name' => 'Spor Alanı', 'icon' => '🏋️'],
                ['id' => 'cocuk_parki', 'name' => 'Çocuk Parkı', 'icon' => '🎠'],
                ['id' => 'asansor', 'name' => 'Asansör', 'icon' => '🛗'],
                ['id' => 'jenerator', 'name' => 'Jeneratör', 'icon' => '⚡'],
                ['id' => 'kamerali_guvenlik', 'name' => 'Kameralı Güvenlik', 'icon' => '📹'],
                ['id' => 'yesil_alan', 'name' => 'Yeşil Alan', 'icon' => '🌳'],
                ['id' => 'kamelya', 'name' => 'Kamelya', 'icon' => '🏡'],
                ['id' => 'gosterim_saati', 'name' => 'Gösteri Salonu', 'icon' => '🎭'],
                ['id' => 'kapi_gorevlisi', 'name' => 'Kapı Görevlisi', 'icon' => '🚪'],
            ];

            return response()->json([
                'success' => true,
                'data' => $ozellikler
            ]);

        } catch (\Exception $e) {
            \Log::error('Site özellikleri yükleme hatası: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Özellikler yüklenemedi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aktif site özellikleri
     */
    public function active(): JsonResponse
    {
        return $this->index(); // Tüm özellikler aktif
    }
}

