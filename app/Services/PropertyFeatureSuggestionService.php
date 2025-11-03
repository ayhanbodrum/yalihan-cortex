<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * 🏠 Özellik Önerileri Servisi
 *
 * Kategori bazında akıllı özellik önerileri
 * - Arsa özellikleri
 * - Yazlık özellikleri
 * - Villa/Daire özellikleri
 * - İşyeri özellikleri
 */
class PropertyFeatureSuggestionService
{
    /**
     * Kategori bazında özellik önerileri
     */
    public function getFeatureSuggestions(string $category, string $subCategory = null): array
    {
        $cacheKey = "feature_suggestions_{$category}_{$subCategory}";

        return Cache::remember($cacheKey, 3600, function () use ($category, $subCategory) {
            return $this->generateFeatureSuggestions($category, $subCategory);
        });
    }

    /**
     * Özellik önerileri oluştur
     */
    private function generateFeatureSuggestions(string $category, string $subCategory = null): array
    {
        $suggestions = [];

        switch (strtolower($category)) {
            case 'arsa':
                $suggestions = $this->getArsaFeatureSuggestions($subCategory);
                break;
            case 'yazlık':
            case 'yazlik':
                $suggestions = $this->getYazlikFeatureSuggestions($subCategory);
                break;
            case 'villa':
            case 'daire':
                $suggestions = $this->getVillaDaireFeatureSuggestions($subCategory);
                break;
            case 'işyeri':
            case 'isyeri':
                $suggestions = $this->getIsyeriFeatureSuggestions($subCategory);
                break;
            default:
                $suggestions = $this->getDefaultFeatureSuggestions();
        }

        return $suggestions;
    }

    /**
     * Arsa özellik önerileri
     */
    private function getArsaFeatureSuggestions(string $subCategory = null): array
    {
        return [
            'required_features' => [
                'ada_no' => [
                    'label' => 'Ada Numarası',
                    'type' => 'text',
                    'placeholder' => '123',
                    'required' => true,
                    'suggestion' => 'Tapu senedindeki ada numarasını girin'
                ],
                'parsel_no' => [
                    'label' => 'Parsel Numarası',
                    'type' => 'text',
                    'placeholder' => '45',
                    'required' => true,
                    'suggestion' => 'Tapu senedindeki parsel numarasını girin'
                ],
                'imar_statusu' => [
                    'label' => 'İmar Durumu',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'İmar Var' => 'İmar Var',
                        'İmar Yok' => 'İmar Yok',
                        'İmar Beklemede' => 'İmar Beklemede'
                    ],
                    'suggestion' => 'Arsanın imar durumunu seçin'
                ]
            ],
            'optional_features' => [
                'kaks' => [
                    'label' => 'KAKS (Kat Alanı Kat Sayısı)',
                    'type' => 'number',
                    'placeholder' => '0.5',
                    'suggestion' => 'İmar durumuna göre KAKS değeri'
                ],
                'taks' => [
                    'label' => 'TAKS (Tabii Alan Kat Sayısı)',
                    'type' => 'number',
                    'placeholder' => '0.3',
                    'suggestion' => 'İmar durumuna göre TAKS değeri'
                ],
                'gabari' => [
                    'label' => 'Gabari (Maksimum Yükseklik)',
                    'type' => 'number',
                    'placeholder' => '10',
                    'suggestion' => 'Metre cinsinden maksimum yükseklik'
                ]
            ],
            'smart_suggestions' => [
                'KAKS değeri genellikle 0.3-0.8 arasındadır',
                'TAKS değeri genellikle 0.2-0.6 arasındadır',
                'Gabari genellikle 8-15 metre arasındadır'
            ]
        ];
    }

    /**
     * Yazlık özellik önerileri
     */
    private function getYazlikFeatureSuggestions(string $subCategory = null): array
    {
        return [
            'required_features' => [
                'gunluk_fiyat' => [
                    'label' => 'Günlük Fiyat',
                    'type' => 'number',
                    'placeholder' => '500',
                    'required' => true,
                    'suggestion' => 'Günlük kiralama fiyatı (TL)'
                ],
                'min_konaklama' => [
                    'label' => 'Minimum Konaklama',
                    'type' => 'number',
                    'placeholder' => '3',
                    'required' => true,
                    'suggestion' => 'Minimum konaklama süresi (gün)'
                ],
                'sezon_baslangic' => [
                    'label' => 'Sezon Başlangıcı',
                    'type' => 'date',
                    'required' => true,
                    'suggestion' => 'Sezon başlangıç tarihi'
                ]
            ],
            'optional_features' => [
                'havuz' => [
                    'label' => 'Havuz',
                    'type' => 'checkbox',
                    'suggestion' => 'Havuz var mı?'
                ],
                'bahce' => [
                    'label' => 'Bahçe',
                    'type' => 'checkbox',
                    'suggestion' => 'Bahçe var mı?'
                ],
                'deniz_manzara' => [
                    'label' => 'Deniz Manzarası',
                    'type' => 'checkbox',
                    'suggestion' => 'Deniz manzarası var mı?'
                ]
            ],
            'smart_suggestions' => [
                'Yaz sezonunda fiyatlar %30-50 artar',
                'Havuzlu yazlıklar %20-30 daha pahalı',
                'Deniz manzaralı yazlıklar %40-60 daha pahalı'
            ]
        ];
    }

    /**
     * Villa/Daire özellik önerileri
     */
    private function getVillaDaireFeatureSuggestions(string $subCategory = null): array
    {
        return [
            'required_features' => [
                'oda_sayisi' => [
                    'label' => 'Oda Sayısı',
                    'type' => 'number',
                    'placeholder' => '3',
                    'required' => true,
                    'suggestion' => 'Toplam oda sayısı'
                ],
                'banyo_sayisi' => [
                    'label' => 'Banyo Sayısı',
                    'type' => 'number',
                    'placeholder' => '2',
                    'required' => true,
                    'suggestion' => 'Banyo sayısı'
                ],
                'net_m2' => [
                    'label' => 'Net M²',
                    'type' => 'number',
                    'placeholder' => '120',
                    'required' => true,
                    'suggestion' => 'Net kullanım alanı (m²)'
                ]
            ],
            'optional_features' => [
                'brut_m2' => [
                    'label' => 'Brüt M²',
                    'type' => 'number',
                    'placeholder' => '150',
                    'suggestion' => 'Brüt alan (m²)'
                ],
                'kat' => [
                    'label' => 'Kat',
                    'type' => 'number',
                    'placeholder' => '2',
                    'suggestion' => 'Bulunduğu kat'
                ],
                'toplam_kat' => [
                    'label' => 'Toplam Kat',
                    'type' => 'number',
                    'placeholder' => '4',
                    'suggestion' => 'Binanın toplam kat sayısı'
                ],
                'bina_yasi' => [
                    'label' => 'Bina Yaşı',
                    'type' => 'number',
                    'placeholder' => '5',
                    'suggestion' => 'Bina yaşı (yıl)'
                ],
                'isinma_tipi' => [
                    'label' => 'Isınma Tipi',
                    'type' => 'select',
                    'options' => [
                        'Doğalgaz' => 'Doğalgaz',
                        'Kombi' => 'Kombi',
                        'Klima' => 'Klima',
                        'Soba' => 'Soba',
                        'Merkezi' => 'Merkezi',
                        'Yerden Isıtma' => 'Yerden Isıtma'
                    ],
                    'suggestion' => 'Isınma sistemi'
                ],
                'site_ozellikleri' => [
                    'label' => 'Site Özellikleri',
                    'type' => 'checkbox_group',
                    'options' => [
                        'Güvenlik' => 'Güvenlik',
                        'Otopark' => 'Otopark',
                        'Havuz' => 'Havuz',
                        'Spor' => 'Spor',
                        'Sauna' => 'Sauna',
                        'Oyun Alanı' => 'Oyun Alanı',
                        'Asansör' => 'Asansör'
                    ],
                    'suggestion' => 'Site içi özellikler'
                ]
            ],
            'smart_suggestions' => [
                'Oda sayısı genellikle 1-5 arasındadır',
                'Banyo sayısı genellikle 1-3 arasındadır',
                'Net m² genellikle 50-300 arasındadır'
            ]
        ];
    }

    /**
     * İşyeri özellik önerileri
     */
    private function getIsyeriFeatureSuggestions(string $subCategory = null): array
    {
        return [
            'required_features' => [
                'isyeri_tipi' => [
                    'label' => 'İşyeri Tipi',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'Ofis' => 'Ofis',
                        'Mağaza' => 'Mağaza',
                        'Dükkan' => 'Dükkan',
                        'Depo' => 'Depo',
                        'Fabrika' => 'Fabrika',
                        'Atölye' => 'Atölye',
                        'Showroom' => 'Showroom'
                    ],
                    'suggestion' => 'İşyeri tipini seçin'
                ],
                'kira_bilgisi' => [
                    'label' => 'Kira Bilgisi',
                    'type' => 'textarea',
                    'placeholder' => 'Aylık kira: 5000 TL',
                    'required' => true,
                    'suggestion' => 'Kira bilgilerini detaylı yazın'
                ]
            ],
            'optional_features' => [
                'ciro_bilgisi' => [
                    'label' => 'Ciro Bilgisi',
                    'type' => 'number',
                    'placeholder' => '50000',
                    'suggestion' => 'Aylık tahmini ciro (TL)'
                ],
                'ruhsat_durumu' => [
                    'label' => 'Ruhsat Durumu',
                    'type' => 'select',
                    'options' => [
                        'Var' => 'Var',
                        'Yok' => 'Yok',
                        'Başvuruda' => 'Başvuruda'
                    ],
                    'suggestion' => 'Ruhsat durumu'
                ],
                'personel_kapasitesi' => [
                    'label' => 'Personel Kapasitesi',
                    'type' => 'number',
                    'placeholder' => '10',
                    'suggestion' => 'Maksimum personel sayısı'
                ],
                'isyeri_cephesi' => [
                    'label' => 'İşyeri Cephesi',
                    'type' => 'number',
                    'placeholder' => '5',
                    'suggestion' => 'Cephe uzunluğu (metre)'
                ]
            ],
            'smart_suggestions' => [
                'Ofis kiraları genellikle 2000-10000 TL arasındadır',
                'Mağaza kiraları genellikle 3000-15000 TL arasındadır',
                'Depo kiraları genellikle 1000-5000 TL arasındadır'
            ]
        ];
    }

    /**
     * Varsayılan özellik önerileri
     */
    private function getDefaultFeatureSuggestions(): array
    {
        return [
            'required_features' => [],
            'optional_features' => [],
            'smart_suggestions' => [
                'Kategori seçimi yapın',
                'Özellik önerileri görünecek'
            ]
        ];
    }

    /**
     * Akıllı öneriler oluştur
     */
    public function getSmartSuggestions(string $category, array $currentData = []): array
    {
        $suggestions = [];

        // Kategori bazında akıllı öneriler
        switch (strtolower($category)) {
            case 'arsa':
                $suggestions = $this->getArsaSmartSuggestions($currentData);
                break;
            case 'yazlık':
            case 'yazlik':
                $suggestions = $this->getYazlikSmartSuggestions($currentData);
                break;
            case 'villa':
            case 'daire':
                $suggestions = $this->getVillaDaireSmartSuggestions($currentData);
                break;
            case 'işyeri':
            case 'isyeri':
                $suggestions = $this->getIsyeriSmartSuggestions($currentData);
                break;
        }

        return $suggestions;
    }

    /**
     * Arsa akıllı önerileri
     */
    private function getArsaSmartSuggestions(array $currentData): array
    {
        $suggestions = [];

        if (isset($currentData['imar_statusu'])) {
            if ($currentData['imar_statusu'] === 'İmar Var') {
                $suggestions[] = 'İmar var ise KAKS ve TAKS değerlerini girin';
                $suggestions[] = 'Gabari değeri genellikle 8-15 metre arasındadır';
            } else {
                $suggestions[] = 'İmar yok ise tarım arazisi olarak değerlendirilebilir';
            }
        }

        return $suggestions;
    }

    /**
     * Yazlık akıllı önerileri
     */
    private function getYazlikSmartSuggestions(array $currentData): array
    {
        $suggestions = [];

        if (isset($currentData['gunluk_fiyat'])) {
            $price = (int)$currentData['gunluk_fiyat'];
            if ($price < 300) {
                $suggestions[] = 'Fiyat düşük görünüyor, piyasa araştırması yapın';
            } elseif ($price > 1000) {
                $suggestions[] = 'Fiyat yüksek görünüyor, rekabetçi olup olmadığını kontrol edin';
            }
        }

        return $suggestions;
    }

    /**
     * Villa/Daire akıllı önerileri
     */
    private function getVillaDaireSmartSuggestions(array $currentData): array
    {
        $suggestions = [];

        if (isset($currentData['oda_sayisi']) && isset($currentData['net_m2'])) {
            $odaSayisi = (int)$currentData['oda_sayisi'];
            $netM2 = (int)$currentData['net_m2'];

            if ($netM2 / $odaSayisi < 15) {
                $suggestions[] = 'Oda başına düşen alan düşük görünüyor';
            } elseif ($netM2 / $odaSayisi > 30) {
                $suggestions[] = 'Oda başına düşen alan yüksek görünüyor';
            }
        }

        return $suggestions;
    }

    /**
     * İşyeri akıllı önerileri
     */
    private function getIsyeriSmartSuggestions(array $currentData): array
    {
        $suggestions = [];

        if (isset($currentData['isyeri_tipi'])) {
            $suggestions[] = $currentData['isyeri_tipi'] . ' için uygun özellikleri seçin';
        }

        return $suggestions;
    }
}
