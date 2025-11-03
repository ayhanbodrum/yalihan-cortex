<?php

namespace App\Services;

use App\Models\IlanKategori;

/**
 * Kategori Bazlı Özel Alanlar Servisi
 *
 * Context7 Standardı: C7-KATEGORI-OZELLIK-2025-10-11
 * Context7 Kural #66: Kategori Bazlı Dinamik Alanlar
 *
 * Her kategori için zorunlu, önerilen ve opsiyonel alanları yönetir
 */
class KategoriOzellikService
{
    /**
     * Kategoriye göre özel alanları getir
     */
    public function getOzelliklerByKategori($kategoriId)
    {
        if (!$kategoriId) {
            return $this->getDefaultFields();
        }

        $kategori = IlanKategori::find($kategoriId);

        if (!$kategori) {
            return $this->getDefaultFields();
        }

        $kategoriSlug = $kategori->slug ?? strtolower(str_replace(' ', '-', $kategori->name));

        return $this->getFieldsBySlug($kategoriSlug);
    }

    /**
     * Kategori adına göre özel alanları getir
     */
    public function getOzelliklerByName($kategoriName)
    {
        $slug = strtolower(str_replace(' ', '-', $kategoriName));
        return $this->getFieldsBySlug($slug);
    }

    /**
     * Slug'a göre alan tanımları
     */
    protected function getFieldsBySlug($slug)
    {
        $definitions = [
            'arsa' => $this->getArsaFields(),
            'yazlik' => $this->getYazlikFields(),
            'yazlık' => $this->getYazlikFields(),
            'villa' => $this->getVillaFields(),
            'daire' => $this->getDaireFields(),
            'isyeri' => $this->getIsyeriFields(),
            'işyeri' => $this->getIsyeriFields(),
            'turistik-tesis' => $this->getTuristikTesisFields(),
        ];

        return $definitions[$slug] ?? $this->getDefaultFields();
    }

    /**
     * Arsa özel alanları
     */
    protected function getArsaFields()
    {
        return [
            'required' => [
                'ada_no' => [
                    'label' => 'Ada No',
                    'type' => 'text',
                    'validation' => 'required|string|max:20',
                    'placeholder' => 'Örn: 126',
                    'help' => 'Tapuda yazan ada numarası',
                    'icon' => 'fa-hashtag'
                ],
                'parsel_no' => [
                    'label' => 'Parsel No',
                    'type' => 'text',
                    'validation' => 'required|string|max:20',
                    'placeholder' => 'Örn: 7',
                    'help' => 'Tapuda yazan parsel numarası',
                    'icon' => 'fa-hashtag'
                ],
                'imar_durumu' => [
                    'label' => 'İmar Durumu',
                    'type' => 'select',
                    'options' => [
                        'İmarlı' => 'İmarlı',
                        'İmar Dışında' => 'İmar Dışında',
                        'Tarla' => 'Tarla',
                        'Bahçe' => 'Bahçe',
                        'Zeytinlik' => 'Zeytinlik',
                        'Meyvelik' => 'Meyvelik'
                    ],
                    'validation' => 'required|string',
                    'help' => 'İmar durumunu seçin',
                    'icon' => 'fa-map-marked-alt'
                ],
                'alan_m2' => [
                    'label' => 'Alan (m²)',
                    'type' => 'number',
                    'validation' => 'required|numeric|min:0',
                    'placeholder' => 'Örn: 1500',
                    'help' => 'Arsa alanı metrekare cinsinden',
                    'icon' => 'fa-ruler-combined'
                ]
            ],
            'recommended' => [
                'taks' => [
                    'label' => 'TAKS (%)',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0|max:100',
                    'placeholder' => 'Örn: 30',
                    'help' => 'Taban alanı katsayısı (yüzde)',
                    'icon' => 'fa-percentage'
                ],
                'kaks' => [
                    'label' => 'KAKS',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0|max:10',
                    'placeholder' => 'Örn: 1.2',
                    'step' => '0.01',
                    'help' => 'Kat alanları katsayısı',
                    'icon' => 'fa-layer-group'
                ],
                'gabari' => [
                    'label' => 'Gabari (m)',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0',
                    'placeholder' => 'Örn: 12.5',
                    'help' => 'Maksimum bina yüksekliği',
                    'icon' => 'fa-arrows-alt-v'
                ],
                'yola_cephe' => [
                    'label' => 'Yola Cephe (m)',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0',
                    'placeholder' => 'Örn: 25',
                    'help' => 'Yola cephe uzunluğu',
                    'icon' => 'fa-road'
                ]
            ],
            'optional' => [
                'altyapi_elektrik' => [
                    'label' => 'Elektrik Alt Yapısı',
                    'type' => 'checkbox',
                    'icon' => 'fa-bolt'
                ],
                'altyapi_su' => [
                    'label' => 'Su Alt Yapısı',
                    'type' => 'checkbox',
                    'icon' => 'fa-water'
                ],
                'altyapi_dogalgaz' => [
                    'label' => 'Doğalgaz Alt Yapısı',
                    'type' => 'checkbox',
                    'icon' => 'fa-fire'
                ],
                'altyapi_kanalizasyon' => [
                    'label' => 'Kanalizasyon',
                    'type' => 'checkbox',
                    'icon' => 'fa-water'
                ]
            ],
            'ai_features' => [
                'tkgm_integration' => true,
                'price_per_sqm_calculation' => true,
                'investment_analysis' => true,
                'kaks_taks_validation' => true
            ]
        ];
    }

    /**
     * Yazlık özel alanları
     */
    protected function getYazlikFields()
    {
        return [
            'required' => [
                'havuz' => [
                    'label' => 'Havuz Var mı?',
                    'type' => 'checkbox',
                    'validation' => 'nullable|boolean',
                    'help' => 'Havuz olup olmadığını belirtin',
                    'icon' => 'fa-swimming-pool'
                ],
                'min_konaklama' => [
                    'label' => 'Minimum Konaklama (Gün)',
                    'type' => 'number',
                    'validation' => 'required|integer|min:1|max:365',
                    'placeholder' => 'Örn: 7',
                    'help' => 'En az kaç gün kiralanabilir?',
                    'icon' => 'fa-calendar-day'
                ],
                'max_misafir' => [
                    'label' => 'Maksimum Misafir Sayısı',
                    'type' => 'number',
                    'validation' => 'required|integer|min:1|max:50',
                    'placeholder' => 'Örn: 8',
                    'help' => 'Maksimum kaç kişi kalabilir?',
                    'icon' => 'fa-users'
                ]
            ],
            'recommended' => [
                'sezon_baslangic' => [
                    'label' => 'Sezon Başlangıç',
                    'type' => 'date',
                    'validation' => 'nullable|date',
                    'help' => 'Kiralama sezonu başlangıç tarihi',
                    'icon' => 'fa-calendar-alt'
                ],
                'sezon_bitis' => [
                    'label' => 'Sezon Bitiş',
                    'type' => 'date',
                    'validation' => 'nullable|date|after:sezon_baslangic',
                    'help' => 'Kiralama sezonu bitiş tarihi',
                    'icon' => 'fa-calendar-alt'
                ],
                'temizlik_ucreti' => [
                    'label' => 'Temizlik Ücreti (TL)',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0',
                    'placeholder' => 'Örn: 500',
                    'icon' => 'fa-broom'
                ],
                'depozito' => [
                    'label' => 'Depozito (TL)',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0',
                    'placeholder' => 'Örn: 2000',
                    'icon' => 'fa-shield-alt'
                ]
            ],
            'optional' => [
                'elektrik_dahil' => [
                    'label' => 'Elektrik Dahil',
                    'type' => 'checkbox',
                    'icon' => 'fa-bolt'
                ],
                'su_dahil' => [
                    'label' => 'Su Dahil',
                    'type' => 'checkbox',
                    'icon' => 'fa-tint'
                ],
                'wifi' => [
                    'label' => 'WiFi',
                    'type' => 'checkbox',
                    'icon' => 'fa-wifi'
                ],
                'klima' => [
                    'label' => 'Klima',
                    'type' => 'checkbox',
                    'icon' => 'fa-snowflake'
                ],
                'havuz_turu' => [
                    'label' => 'Havuz Türü',
                    'type' => 'select',
                    'options' => [
                        'Açık' => 'Açık Havuz',
                        'Kapalı' => 'Kapalı Havuz',
                        'Isıtmalı' => 'Isıtmalı Havuz'
                    ],
                    'icon' => 'fa-swimming-pool'
                ]
            ],
            'ai_features' => [
                'seasonal_pricing' => true,
                'booking_optimization' => true,
                'occupancy_prediction' => true
            ]
        ];
    }

    /**
     * Villa özel alanları
     */
    protected function getVillaFields()
    {
        return [
            'required' => [
                'bahce_m2' => [
                    'label' => 'Bahçe Alanı (m²)',
                    'type' => 'number',
                    'validation' => 'required|numeric|min:0',
                    'placeholder' => 'Örn: 500',
                    'help' => 'Bahçe alanı metrekare cinsinden',
                    'icon' => 'fa-tree'
                ],
                'otopark_kapasitesi' => [
                    'label' => 'Otopark Kapasitesi',
                    'type' => 'number',
                    'validation' => 'required|integer|min:0|max:20',
                    'placeholder' => 'Örn: 3',
                    'help' => 'Kaç araç park edebilir?',
                    'icon' => 'fa-parking'
                ]
            ],
            'recommended' => [
                'guvenlik' => [
                    'label' => 'Güvenlik',
                    'type' => 'checkbox',
                    'help' => '7/24 güvenlik var mı?',
                    'icon' => 'fa-shield-alt'
                ],
                'jenerator' => [
                    'label' => 'Jeneratör',
                    'type' => 'checkbox',
                    'help' => 'Elektrik kesintisine karşı jeneratör',
                    'icon' => 'fa-battery-full'
                ],
                'teras_m2' => [
                    'label' => 'Teras Alanı (m²)',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0',
                    'placeholder' => 'Örn: 80',
                    'icon' => 'fa-home'
                ],
                'havuz_m2' => [
                    'label' => 'Havuz Alanı (m²)',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0',
                    'placeholder' => 'Örn: 40',
                    'icon' => 'fa-swimming-pool'
                ]
            ],
            'optional' => [
                'denize_uzaklik' => [
                    'label' => 'Denize Uzaklık (m)',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0',
                    'placeholder' => 'Örn: 500',
                    'icon' => 'fa-water'
                ],
                'sauna' => [
                    'label' => 'Sauna',
                    'type' => 'checkbox',
                    'icon' => 'fa-hot-tub'
                ],
                'jakuzi' => [
                    'label' => 'Jakuzi',
                    'type' => 'checkbox',
                    'icon' => 'fa-bath'
                ]
            ],
            'ai_features' => [
                'luxury_analysis' => true,
                'amenity_scoring' => true,
                'investment_roi' => true
            ]
        ];
    }

    /**
     * Daire özel alanları
     */
    protected function getDaireFields()
    {
        return [
            'required' => [
                'oda_sayisi' => [
                    'label' => 'Oda Sayısı',
                    'type' => 'select',
                    'options' => [
                        '1+0' => '1+0 (Stüdyo)',
                        '1+1' => '1+1',
                        '2+1' => '2+1',
                        '3+1' => '3+1',
                        '4+1' => '4+1',
                        '5+1' => '5+1',
                        '6+1' => '6+1'
                    ],
                    'validation' => 'required|string',
                    'help' => 'Oda + salon sayısı',
                    'icon' => 'fa-door-open'
                ],
                'banyo_sayisi' => [
                    'label' => 'Banyo Sayısı',
                    'type' => 'number',
                    'validation' => 'required|integer|min:1|max:10',
                    'placeholder' => 'Örn: 2',
                    'help' => 'Kaç banyo var?',
                    'icon' => 'fa-bath'
                ],
                'net_m2' => [
                    'label' => 'Net m²',
                    'type' => 'number',
                    'validation' => 'required|numeric|min:0',
                    'placeholder' => 'Örn: 120',
                    'help' => 'Net kullanım alanı',
                    'icon' => 'fa-ruler-combined'
                ]
            ],
            'recommended' => [
                'brut_m2' => [
                    'label' => 'Brüt m²',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0',
                    'placeholder' => 'Örn: 140',
                    'help' => 'Brüt alan (duvarlar dahil)',
                    'icon' => 'fa-ruler-combined'
                ],
                'balkon' => [
                    'label' => 'Balkon',
                    'type' => 'checkbox',
                    'icon' => 'fa-home'
                ],
                'asansor' => [
                    'label' => 'Asansör',
                    'type' => 'checkbox',
                    'icon' => 'fa-building'
                ],
                'kat' => [
                    'label' => 'Bulunduğu Kat',
                    'type' => 'number',
                    'validation' => 'nullable|integer|min:-3|max:100',
                    'placeholder' => 'Örn: 5',
                    'help' => 'Daire hangi katta? (-3=Bodrum)',
                    'icon' => 'fa-layer-group'
                ],
                'toplam_kat' => [
                    'label' => 'Toplam Kat Sayısı',
                    'type' => 'number',
                    'validation' => 'nullable|integer|min:1|max:100',
                    'placeholder' => 'Örn: 12',
                    'help' => 'Binanın toplam kat sayısı',
                    'icon' => 'fa-building'
                ]
            ],
            'optional' => [
                // ✅ Context7: site_adi → name (SiteApartman modeline uyumlu)
                'site_name' => [
                    'label' => 'Site Adı',
                    'type' => 'text',
                    'validation' => 'nullable|string|max:255',
                    'placeholder' => 'Örn: Yalıkavak Residence',
                    'icon' => 'fa-city'
                ],
                'aidat' => [
                    'label' => 'Aidat (TL/Ay)',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0',
                    'placeholder' => 'Örn: 500',
                    'icon' => 'fa-money-bill'
                ]
            ],
            'ai_features' => [
                'neighborhood_analysis' => true,
                'building_amenities_scoring' => true
            ]
        ];
    }

    /**
     * İşyeri özel alanları
     */
    protected function getIsyeriFields()
    {
        return [
            'required' => [
                'isyeri_tipi' => [
                    'label' => 'İşyeri Tipi',
                    'type' => 'select',
                    'options' => [
                        'Dükkan' => 'Dükkan',
                        'Ofis' => 'Ofis',
                        'Depo' => 'Depo',
                        'Cafe/Restoran' => 'Cafe/Restoran',
                        'Otel' => 'Otel',
                        'Fabrika' => 'Fabrika'
                    ],
                    'validation' => 'required|string',
                    'icon' => 'fa-briefcase'
                ]
            ],
            'recommended' => [
                'ciro' => [
                    'label' => 'Aylık Ciro (TL)',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0',
                    'placeholder' => 'Örn: 150000',
                    'help' => 'Ortalama aylık ciro',
                    'icon' => 'fa-chart-line'
                ],
                'personel_sayisi' => [
                    'label' => 'Personel Sayısı',
                    'type' => 'number',
                    'validation' => 'nullable|integer|min:0',
                    'placeholder' => 'Örn: 5',
                    'icon' => 'fa-users'
                ],
                'kira_getirisi' => [
                    'label' => 'Kira Getirisi (TL/Ay)',
                    'type' => 'number',
                    'validation' => 'nullable|numeric|min:0',
                    'placeholder' => 'Örn: 25000',
                    'icon' => 'fa-money-bill-wave'
                ]
            ],
            'optional' => [
                'ruhsat' => [
                    'label' => 'Ruhsat Var mı?',
                    'type' => 'checkbox',
                    'icon' => 'fa-certificate'
                ],
                'devren' => [
                    'label' => 'Devren mi?',
                    'type' => 'checkbox',
                    'help' => 'İşletme ile birlikte devren',
                    'icon' => 'fa-handshake'
                ]
            ],
            'ai_features' => [
                'business_valuation' => true,
                'roi_calculation' => true
            ]
        ];
    }

    /**
     * Turistik Tesis özel alanları
     */
    protected function getTuristikTesisFields()
    {
        return [
            'required' => [
                'tesis_tipi' => [
                    'label' => 'Tesis Tipi',
                    'type' => 'select',
                    'options' => [
                        'Otel' => 'Otel',
                        'Pansiyon' => 'Pansiyon',
                        'Apart Otel' => 'Apart Otel',
                        'Butik Otel' => 'Butik Otel',
                        'Tatil Köyü' => 'Tatil Köyü'
                    ],
                    'validation' => 'required|string',
                    'icon' => 'fa-hotel'
                ],
                'oda_sayisi' => [
                    'label' => 'Oda Sayısı',
                    'type' => 'number',
                    'validation' => 'required|integer|min:1',
                    'placeholder' => 'Örn: 25',
                    'icon' => 'fa-bed'
                ],
                'yatak_kapasitesi' => [
                    'label' => 'Toplam Yatak Kapasitesi',
                    'type' => 'number',
                    'validation' => 'required|integer|min:1',
                    'placeholder' => 'Örn: 60',
                    'icon' => 'fa-bed'
                ]
            ],
            'recommended' => [
                'yildiz' => [
                    'label' => 'Yıldız Sayısı',
                    'type' => 'select',
                    'options' => [
                        '1' => '1 Yıldız',
                        '2' => '2 Yıldız',
                        '3' => '3 Yıldız',
                        '4' => '4 Yıldız',
                        '5' => '5 Yıldız',
                        'Butik' => 'Butik (Yıldızsız)'
                    ],
                    'icon' => 'fa-star'
                ],
                'restoran' => [
                    'label' => 'Restoran',
                    'type' => 'checkbox',
                    'icon' => 'fa-utensils'
                ],
                'spa' => [
                    'label' => 'SPA',
                    'type' => 'checkbox',
                    'icon' => 'fa-spa'
                ]
            ],
            'optional' => [],
            'ai_features' => [
                'tourism_season_analysis' => true,
                'occupancy_rate_prediction' => true
            ]
        ];
    }

    /**
     * Varsayılan alanlar (kategori belirsiz)
     */
    protected function getDefaultFields()
    {
        return [
            'required' => [
                'baslik' => [
                    'label' => 'Başlık',
                    'type' => 'text',
                    'validation' => 'required|string|max:255',
                    'placeholder' => 'İlan başlığını girin',
                    'icon' => 'fa-heading'
                ],
                'fiyat' => [
                    'label' => 'Fiyat',
                    'type' => 'number',
                    'validation' => 'required|numeric|min:0',
                    'placeholder' => 'Örn: 2500000',
                    'icon' => 'fa-lira-sign'
                ],
                'metrekare' => [
                    'label' => 'Metrekare',
                    'type' => 'number',
                    'validation' => 'required|numeric|min:0',
                    'placeholder' => 'Örn: 150',
                    'icon' => 'fa-ruler-combined'
                ]
            ],
            'recommended' => [],
            'optional' => [],
            'ai_features' => []
        ];
    }

    /**
     * Kategori adından slug oluştur
     */
    public function generateSlug($kategoriName)
    {
        $replacements = [
            'ş' => 's',
            'Ş' => 's',
            'ı' => 'i',
            'İ' => 'i',
            'ğ' => 'g',
            'Ğ' => 'g',
            'ü' => 'u',
            'Ü' => 'u',
            'ö' => 'o',
            'Ö' => 'o',
            'ç' => 'c',
            'Ç' => 'c',
            ' ' => '-'
        ];

        return strtolower(str_replace(
            array_keys($replacements),
            array_values($replacements),
            $kategoriName
        ));
    }

    /**
     * Alan tipine göre HTML input oluştur
     */
    public function renderField($fieldName, $fieldConfig, $value = null)
    {
        $html = '';
        $type = $fieldConfig['type'] ?? 'text';
        $label = $fieldConfig['label'] ?? ucfirst($fieldName);
        $icon = $fieldConfig['icon'] ?? 'fa-info-circle';
        $help = $fieldConfig['help'] ?? '';
        $placeholder = $fieldConfig['placeholder'] ?? '';

        // Input wrapper
        $html .= '<div class="neo-form-group">';
        $html .= '<label class="neo-label">';
        $html .= '<i class="fas ' . $icon . ' mr-2"></i>';
        $html .= $label;
        if (strpos($fieldConfig['validation'] ?? '', 'required') !== false) {
            $html .= ' <span class="text-red-500">*</span>';
        }
        $html .= '</label>';

        // Input field
        switch ($type) {
            case 'text':
            case 'number':
            case 'date':
                $step = $fieldConfig['step'] ?? ($type === 'number' ? '1' : '');
                $html .= '<input type="' . $type . '" ';
                $html .= 'name="' . $fieldName . '" ';
                $html .= 'class="neo-input" ';
                $html .= 'placeholder="' . htmlspecialchars($placeholder) . '" ';
                if ($value) $html .= 'value="' . htmlspecialchars($value) . '" ';
                if ($step) $html .= 'step="' . $step . '" ';
                $html .= '>';
                break;

            case 'select':
                $html .= '<select name="' . $fieldName . '" class="neo-select">';
                $html .= '<option value="">Seçin...</option>';
                foreach ($fieldConfig['options'] ?? [] as $optValue => $optLabel) {
                    $selected = ($value == $optValue) ? 'selected' : '';
                    $html .= '<option value="' . htmlspecialchars($optValue) . '" ' . $selected . '>';
                    $html .= htmlspecialchars($optLabel);
                    $html .= '</option>';
                }
                $html .= '</select>';
                break;

            case 'checkbox':
                $checked = $value ? 'checked' : '';
                $html .= '<div class="flex items-center">';
                $html .= '<input type="checkbox" name="' . $fieldName . '" ';
                $html .= 'class="neo-checkbox" ' . $checked . '>';
                $html .= '<span class="ml-2 text-sm text-gray-600">' . $help . '</span>';
                $html .= '</div>';
                break;

            case 'textarea':
                $html .= '<textarea name="' . $fieldName . '" ';
                $html .= 'class="neo-textarea" ';
                $html .= 'rows="' . ($fieldConfig['rows'] ?? 4) . '" ';
                $html .= 'placeholder="' . htmlspecialchars($placeholder) . '">';
                $html .= htmlspecialchars($value ?? '');
                $html .= '</textarea>';
                break;
        }

        // Help text
        if ($help && $type !== 'checkbox') {
            $html .= '<small class="text-gray-500 mt-1">' . htmlspecialchars($help) . '</small>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Tüm alanları render et
     */
    public function renderAllFields($kategoriId, $values = [])
    {
        $fields = $this->getOzelliklerByKategori($kategoriId);
        $html = '';

        // Zorunlu alanlar
        if (!empty($fields['required'])) {
            $html .= '<div class="required-fields mb-6">';
            $html .= '<h3 class="text-lg font-semibold text-red-600 mb-4">';
            $html .= '<i class="fas fa-asterisk mr-2"></i>Zorunlu Alanlar';
            $html .= '</h3>';
            $html .= '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            foreach ($fields['required'] as $fieldName => $fieldConfig) {
                $html .= $this->renderField($fieldName, $fieldConfig, $values[$fieldName] ?? null);
            }
            $html .= '</div></div>';
        }

        // Önerilen alanlar
        if (!empty($fields['recommended'])) {
            $html .= '<div class="recommended-fields mb-6">';
            $html .= '<h3 class="text-lg font-semibold text-blue-600 mb-4">';
            $html .= '<i class="fas fa-lightbulb mr-2"></i>Önerilen Alanlar';
            $html .= '</h3>';
            $html .= '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            foreach ($fields['recommended'] as $fieldName => $fieldConfig) {
                $html .= $this->renderField($fieldName, $fieldConfig, $values[$fieldName] ?? null);
            }
            $html .= '</div></div>';
        }

        // Opsiyonel alanlar
        if (!empty($fields['optional'])) {
            $html .= '<div class="optional-fields">';
            $html .= '<h3 class="text-lg font-semibold text-gray-600 mb-4">';
            $html .= '<i class="fas fa-plus-circle mr-2"></i>Opsiyonel Alanlar';
            $html .= '</h3>';
            $html .= '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
            foreach ($fields['optional'] as $fieldName => $fieldConfig) {
                $html .= $this->renderField($fieldName, $fieldConfig, $values[$fieldName] ?? null);
            }
            $html .= '</div></div>';
        }

        return $html;
    }

    /**
     * Validation rules al
     */
    public function getValidationRules($kategoriId)
    {
        $fields = $this->getOzelliklerByKategori($kategoriId);
        $rules = [];

        foreach (['required', 'recommended', 'optional'] as $section) {
            foreach ($fields[$section] ?? [] as $fieldName => $fieldConfig) {
                if (isset($fieldConfig['validation'])) {
                    $rules[$fieldName] = $fieldConfig['validation'];
                }
            }
        }

        return $rules;
    }
}
