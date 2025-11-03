-- 🎯 Yazlık Field Reorganization Script
-- 88 alan → 6 mantıklı kategoriye
-- 2025-11-03

-- ═══════════════════════════════════════════════════
-- ADIM 1: KATEGORIZE ET (field_category güncelleme)
-- ═══════════════════════════════════════════════════

-- 1️⃣ FİYATLANDIRMA (11 alan)
UPDATE kategori_yayin_tipi_field_dependencies
SET field_category = 'fiyatlandirma'
WHERE kategori_slug = 'yazlik' 
AND field_slug IN (
    'gunluk_fiyat',
    'haftalik_fiyat',
    'aylik_fiyat',
    'yaz_sezonu_fiyat',
    'kis_sezonu_fiyat',
    'ara_sezon_fiyat',
    'kira_bedeli',
    'satis_fiyati',
    'depozito',
    'check_in',
    'check_out',
    'minimum_konaklama'
);

-- 2️⃣ FİZİKSEL ÖZELLİKLER (6 alan)
UPDATE kategori_yayin_tipi_field_dependencies
SET field_category = 'fiziksel_ozellikler'
WHERE kategori_slug = 'yazlik' 
AND field_slug IN (
    'oda_sayisi',
    'banyo_sayisi',
    'maksimum_misafir',
    'denize_uzaklik',
    'esyali',
    'brut_metrekare',
    'net_metrekare'
);

-- 3️⃣ DONANIM & TESİSAT (6 alan)
UPDATE kategori_yayin_tipi_field_dependencies
SET field_category = 'donanim_tesisat'
WHERE kategori_slug = 'yazlik' 
AND field_slug IN (
    'klima',
    'wifi',
    'camasir_makinesi',
    'bulasik_makinesi',
    'mutfak_donanimli',
    'tv',
    'uydu'
);

-- 4️⃣ DIŞ MEKAN & OLANAKLAR (6 alan)
UPDATE kategori_yayin_tipi_field_dependencies
SET field_category = 'dismekan_olanaklar'
WHERE kategori_slug = 'yazlik' 
AND field_slug IN (
    'havuz',
    'bahce_teras',
    'barbeku',
    'deniz_manzarasi',
    'otopark',
    'guvenlik'
);

-- 5️⃣ YATAK ODASI & KONFOR (3 alan)
UPDATE kategori_yayin_tipi_field_dependencies
SET field_category = 'yatak_odasi_konfor'
WHERE kategori_slug = 'yazlik' 
AND field_slug IN (
    'jakuzi',
    'havlu_carsaf_dahil',
    'ensuite_banyo',
    'balkon'
);

-- 6️⃣ EK HİZMETLER (3 alan)
UPDATE kategori_yayin_tipi_field_dependencies
SET field_category = 'ek_hizmetler'
WHERE kategori_slug = 'yazlik' 
AND field_slug IN (
    'temizlik_servisi',
    'pet_friendly',
    'havuz_bakimi',
    'transfer'
);

-- ═══════════════════════════════════════════════════
-- ADIM 2: SIRALAMA DÜZENLEMESİ (order kolonu)
-- ═══════════════════════════════════════════════════

-- 1️⃣ FİYATLANDIRMA (1-15)
UPDATE kategori_yayin_tipi_field_dependencies
SET `order` = CASE field_slug
    WHEN 'gunluk_fiyat' THEN 1
    WHEN 'haftalik_fiyat' THEN 2
    WHEN 'aylik_fiyat' THEN 3
    WHEN 'yaz_sezonu_fiyat' THEN 4
    WHEN 'kis_sezonu_fiyat' THEN 5
    WHEN 'ara_sezon_fiyat' THEN 6
    WHEN 'depozito' THEN 7
    WHEN 'minimum_konaklama' THEN 8
    WHEN 'check_in' THEN 9
    WHEN 'check_out' THEN 10
    WHEN 'kira_bedeli' THEN 11
    WHEN 'satis_fiyati' THEN 12
    ELSE `order`
END
WHERE kategori_slug = 'yazlik' AND field_category = 'fiyatlandirma';

-- 2️⃣ FİZİKSEL ÖZELLİKLER (20-30)
UPDATE kategori_yayin_tipi_field_dependencies
SET `order` = CASE field_slug
    WHEN 'maksimum_misafir' THEN 20
    WHEN 'oda_sayisi' THEN 21
    WHEN 'banyo_sayisi' THEN 22
    WHEN 'brut_metrekare' THEN 23
    WHEN 'net_metrekare' THEN 24
    WHEN 'denize_uzaklik' THEN 25
    WHEN 'esyali' THEN 26
    ELSE `order`
END
WHERE kategori_slug = 'yazlik' AND field_category = 'fiziksel_ozellikler';

-- 3️⃣ DONANIM & TESİSAT (40-50)
UPDATE kategori_yayin_tipi_field_dependencies
SET `order` = CASE field_slug
    WHEN 'klima' THEN 40
    WHEN 'wifi' THEN 41
    WHEN 'camasir_makinesi' THEN 42
    WHEN 'bulasik_makinesi' THEN 43
    WHEN 'mutfak_donanimli' THEN 44
    WHEN 'tv' THEN 45
    WHEN 'uydu' THEN 46
    ELSE `order`
END
WHERE kategori_slug = 'yazlik' AND field_category = 'donanim_tesisat';

-- 4️⃣ DIŞ MEKAN & OLANAKLAR (60-70)
UPDATE kategori_yayin_tipi_field_dependencies
SET `order` = CASE field_slug
    WHEN 'havuz' THEN 60
    WHEN 'deniz_manzarasi' THEN 61
    WHEN 'bahce_teras' THEN 62
    WHEN 'barbeku' THEN 63
    WHEN 'otopark' THEN 64
    WHEN 'guvenlik' THEN 65
    ELSE `order`
END
WHERE kategori_slug = 'yazlik' AND field_category = 'dismekan_olanaklar';

-- 5️⃣ YATAK ODASI & KONFOR (80-85)
UPDATE kategori_yayin_tipi_field_dependencies
SET `order` = CASE field_slug
    WHEN 'jakuzi' THEN 80
    WHEN 'havlu_carsaf_dahil' THEN 81
    WHEN 'ensuite_banyo' THEN 82
    WHEN 'balkon' THEN 83
    ELSE `order`
END
WHERE kategori_slug = 'yazlik' AND field_category = 'yatak_odasi_konfor';

-- 6️⃣ EK HİZMETLER (90-95)
UPDATE kategori_yayin_tipi_field_dependencies
SET `order` = CASE field_slug
    WHEN 'temizlik_servisi' THEN 90
    WHEN 'pet_friendly' THEN 91
    WHEN 'havuz_bakimi' THEN 92
    WHEN 'transfer' THEN 93
    ELSE `order`
END
WHERE kategori_slug = 'yazlik' AND field_category = 'ek_hizmetler';

-- ═══════════════════════════════════════════════════
-- DOĞRULAMA
-- ═══════════════════════════════════════════════════

-- Kategori dağılımı kontrol
SELECT 
    field_category,
    COUNT(*) as toplam_alan,
    COUNT(DISTINCT field_slug) as benzersiz_alan
FROM kategori_yayin_tipi_field_dependencies
WHERE kategori_slug = 'yazlik'
GROUP BY field_category
ORDER BY field_category;

-- En yüksek order değeri
SELECT MAX(`order`) as max_order
FROM kategori_yayin_tipi_field_dependencies
WHERE kategori_slug = 'yazlik';

