<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Arsa Sözlükleri - Context7 Standardı
    |--------------------------------------------------------------------------
    |
    | İmar durumu, KAKS/TAKS değerleri ve arsa terimleri için sözlük sistemi
    | Context7: C7-ARSA-DICT-2025-10-22
    |
    */

    'imar_statusu' => [
        'imarli' => [
            'label' => 'İmarlı',
            'description' => 'İmar planında belirtilen imar durumuna sahip arsa',
            'color' => 'green',
            'icon' => '✅',
        ],
        'imarsiz' => [
            'label' => 'İmarsız',
            'description' => 'İmar planı dışında kalan arsa',
            'color' => 'gray',
            'icon' => '⚪',
        ],
        'tarla' => [
            'label' => 'Tarla',
            'description' => 'Tarım arazisi statüsündeki arsa',
            'color' => 'yellow',
            'icon' => '🌾',
        ],
        'villa_imarli' => [
            'label' => 'Villa İmarlı',
            'description' => 'Villa inşaatı için özel imar durumuna sahip arsa',
            'color' => 'purple',
            'icon' => '🏡',
        ],
        'konut_imarli' => [
            'label' => 'Konut İmarlı',
            'description' => 'Konut yapımı için imar durumuna sahip arsa',
            'color' => 'blue',
            'icon' => '🏘️',
        ],
        'ticari_imarli' => [
            'label' => 'Ticari İmarlı',
            'description' => 'Ticari yapı inşaatı için imar durumuna sahip arsa',
            'color' => 'orange',
            'icon' => '🏢',
        ],
    ],

    'kaks_ranges' => [
        '0.00-0.50' => [
            'label' => '0.00 - 0.50',
            'description' => 'Çok düşük yoğunluk (Villa, bahçeli konut)',
            'density' => 'very_low',
        ],
        '0.51-1.00' => [
            'label' => '0.51 - 1.00',
            'description' => 'Düşük yoğunluk (Müstakil ev, az katlı)',
            'density' => 'low',
        ],
        '1.01-2.00' => [
            'label' => '1.01 - 2.00',
            'description' => 'Orta yoğunluk (4-6 katlı konut)',
            'density' => 'medium',
        ],
        '2.01-4.00' => [
            'label' => '2.01 - 4.00',
            'description' => 'Yüksek yoğunluk (8-12 katlı konut)',
            'density' => 'high',
        ],
        '4.01+' => [
            'label' => '4.01+',
            'description' => 'Çok yüksek yoğunluk (Gökdelen, plaza)',
            'density' => 'very_high',
        ],
    ],

    'taks_ranges' => [
        '0.00-0.20' => [
            'label' => '0.00 - 0.20',
            'description' => 'Minimum taban alanı (Geniş bahçe)',
            'coverage' => 'minimal',
        ],
        '0.21-0.35' => [
            'label' => '0.21 - 0.35',
            'description' => 'Düşük taban alanı (Villa, bahçeli)',
            'coverage' => 'low',
        ],
        '0.36-0.50' => [
            'label' => '0.36 - 0.50',
            'description' => 'Orta taban alanı (Standart konut)',
            'coverage' => 'medium',
        ],
        '0.51-0.70' => [
            'label' => '0.51 - 0.70',
            'description' => 'Yüksek taban alanı (Apartman)',
            'coverage' => 'high',
        ],
        '0.71+' => [
            'label' => '0.71+',
            'description' => 'Maksimum taban alanı (Ticari bina)',
            'coverage' => 'maximum',
        ],
    ],

    'gabari_ranges' => [
        '0-6.5' => [
            'label' => '0-6.5m',
            'description' => '1-2 kat (Müstakil ev)',
            'floors' => '1-2',
        ],
        '6.51-9.5' => [
            'label' => '6.51-9.5m',
            'description' => '2-3 kat (Müstakil ev, ikiz villa)',
            'floors' => '2-3',
        ],
        '9.51-12.5' => [
            'label' => '9.51-12.5m',
            'description' => '3-4 kat (Apartman)',
            'floors' => '3-4',
        ],
        '12.51-15.5' => [
            'label' => '12.51-15.5m',
            'description' => '4-5 kat (Apartman)',
            'floors' => '4-5',
        ],
        '15.51+' => [
            'label' => '15.51m+',
            'description' => '5+ kat (Yüksek bina)',
            'floors' => '5+',
        ],
    ],

    'altyapi' => [
        'elektrik' => [
            'label' => 'Elektrik',
            'icon' => '⚡',
            'field' => 'altyapi_elektrik',
        ],
        'su' => [
            'label' => 'Su',
            'icon' => '💧',
            'field' => 'altyapi_su',
        ],
        'dogalgaz' => [
            'label' => 'Doğalgaz',
            'icon' => '🔥',
            'field' => 'altyapi_dogalgaz',
        ],
        'kanalizasyon' => [
            'label' => 'Kanalizasyon',
            'icon' => '🚰',
            'field' => 'altyapi_kanalizasyon',
        ],
        'telefon' => [
            'label' => 'Telefon',
            'icon' => '📞',
            'field' => 'altyapi_telefon',
        ],
        'internet' => [
            'label' => 'İnternet/Fiber',
            'icon' => '🌐',
            'field' => 'altyapi_internet',
        ],
    ],

    'arsa_tipleri' => [
        'konut' => 'Konut Arsası',
        'villa' => 'Villa Arsası',
        'ticari' => 'Ticari Arsa',
        'sanayi' => 'Sanayi Arsası',
        'tarim' => 'Tarım Arazisi',
        'bos' => 'Boş Arsa',
        'bag' => 'Bağ',
        'bahce' => 'Bahçe',
        'zeytinlik' => 'Zeytinlik',
        'kaynak_suyu' => 'Kaynak Suylu Arsa',
    ],

    'yola_cephe_tipleri' => [
        'tek_cephe' => 'Tek Cephe',
        'iki_cephe' => 'İki Cephe (Köşe Başı)',
        'uc_cephe' => 'Üç Cephe',
        'dort_cephe' => 'Dört Cephe (Ada İçi)',
    ],

    'konum_avantajlari' => [
        'denize_yakin' => 'Denize Yakın',
        'deniz_manzarali' => 'Deniz Manzaralı',
        'sehir_manzarali' => 'Şehir Manzaralı',
        'dag_manzarali' => 'Dağ Manzaralı',
        'golf_sahasi_yakin' => 'Golf Sahasına Yakın',
        'marina_yakin' => 'Marina Yakını',
        'havaalani_yakin' => 'Havaalanına Yakın',
        'otoban_yakin' => 'Otobana Yakın',
    ],

    // TKGM Entegrasyonu için
    'parsel_nitelikleri' => [
        'konut' => 'Konut',
        'ticaret' => 'Ticaret',
        'sanayi' => 'Sanayi',
        'turizm' => 'Turizm',
        'tarim' => 'Tarım',
        'ormani' => 'Orman',
        'mera' => 'Mera',
    ],
];

