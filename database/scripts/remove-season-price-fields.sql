-- 🗑️ Sezon Fiyatları Field'larını Kaldır
-- Season Pricing Manager component kullanılacak!
-- 2025-11-03

-- ═══════════════════════════════════════════════════
-- SEZON FİYATLARI DEAKTİVE ET
-- ═══════════════════════════════════════════════════

-- ⚠️ Bu field'lar artık gereksiz!
-- Sebep: Season Pricing Manager component'i (Section 4.9) kullanılıyor
-- Yeni sistem:
--   - Dinamik sezon tanımlama (yaz/kış/ara_sezon/bayram/özel)
--   - Tarih aralığı bazlı
--   - Günlük/haftalık/aylık fiyat
--   - Min/max konaklama
--   - Active/inactive status

UPDATE kategori_yayin_tipi_field_dependencies
SET enabled = false
WHERE kategori_slug = 'yazlik'
AND field_slug IN (
    'yaz_sezonu_fiyat',
    'kis_sezonu_fiyat',
    'ara_sezon_fiyat'
);

-- ═══════════════════════════════════════════════════
-- DOĞRULAMA: AKTİF FİYATLANDIRMA ALANLARI
-- ═══════════════════════════════════════════════════

SELECT
    field_slug,
    field_name,
    enabled,
    `order`,
    CASE
        WHEN field_slug IN ('yaz_sezonu_fiyat', 'kis_sezonu_fiyat', 'ara_sezon_fiyat')
        THEN '⚠️ DEPRECATED (Season Manager kullan!)'
        ELSE '✅ Aktif'
    END as status
FROM kategori_yayin_tipi_field_dependencies
WHERE kategori_slug = 'yazlik'
AND field_category = 'fiyatlandirma'
ORDER BY `order`;

-- ═══════════════════════════════════════════════════
-- SONUÇ
-- ═══════════════════════════════════════════════════

-- Öncesi: 12 alan (3 deprecated sezon fiyatı dahil)
-- Sonrası: 9 alan (sadece aktif olanlar gösterilir)

-- ✅ KALAN AKTİF ALANLAR:
-- 1. Günlük Fiyat ⭐
-- 2. Minimum Konaklama ⭐
-- 3. Depozito ⭐
-- 4. Check-in Saati ⭐
-- 5. Check-out Saati ⭐
-- 6. Haftalık Fiyat 💚
-- 7. Aylık Fiyat 💚
-- 8. Kira Bedeli 📅
-- 9. Satış Fiyatı 🏷️

-- ❌ KALDIRILDI (Season Pricing Manager'da):
-- X. Yaz Sezonu Fiyatı
-- X. Kış Sezonu Fiyatı
-- X. Ara Sezon Fiyatı

-- 🎯 YENİ İŞ AKIŞI:
-- 1. Form'da günlük fiyat gir (base price)
-- 2. Section 4.9'da Season Pricing Manager'ı aç
-- 3. Yaz/kış/ara sezon tanımla
-- 4. Her sezon için tarih aralığı + fiyat
-- 5. Otomatik fiyat hesaplama!
