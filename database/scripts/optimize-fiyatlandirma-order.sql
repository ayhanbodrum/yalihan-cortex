-- 🎯 Fiyatlandırma Kategorisi Optimal Sıralama
-- Yeni eklenen Season Pricing Manager'a göre optimize edildi
-- 2025-11-03

-- ═══════════════════════════════════════════════════
-- FİYATLANDIRMA ALANLARI (Akıllı Öncelik Sıralaması)
-- ═══════════════════════════════════════════════════

UPDATE kategori_yayin_tipi_field_dependencies
SET `order` = CASE field_slug
    -- 🔥 KRİTİK ALANLAR (1-5) - Mutlaka doldurulmalı
    WHEN 'gunluk_fiyat' THEN 1          -- En önemli: BASE fiyat!
    WHEN 'minimum_konaklama' THEN 2     -- Kritik kural (min 3 gece?)
    WHEN 'depozito' THEN 3              -- Finansal güvenlik
    WHEN 'check_in' THEN 4              -- Lojistik: Giriş saati
    WHEN 'check_out' THEN 5             -- Lojistik: Çıkış saati
    
    -- 💰 İNDİRİMLİ FİYATLAR (6-8) - Opsiyonel ama önemli
    WHEN 'haftalik_fiyat' THEN 6        -- 7+ gece indirimi
    WHEN 'aylik_fiyat' THEN 7           -- 30+ gün indirimi
    
    -- 📅 SEZONLUK FİYATLAR (10-12) - DEPRECATED! Season Pricing Manager kullan!
    WHEN 'yaz_sezonu_fiyat' THEN 10     -- ⚠️ Season Pricing Manager'da olmalı!
    WHEN 'kis_sezonu_fiyat' THEN 11     -- ⚠️ Season Pricing Manager'da olmalı!
    WHEN 'ara_sezon_fiyat' THEN 12      -- ⚠️ Season Pricing Manager'da olmalı!
    
    -- 💵 UZUN DÖNEM KİRALAMA (13-15)
    WHEN 'kira_bedeli' THEN 13          -- Aylık/yıllık kiralama
    WHEN 'satis_fiyati' THEN 14         -- Satış (eğer satılık ise)
    
    ELSE `order`
END
WHERE kategori_slug = 'yazlik' AND field_category = 'fiyatlandirma';

-- ═══════════════════════════════════════════════════
-- NOTLAR VE UYARILAR
-- ═══════════════════════════════════════════════════

-- ⚠️ DEPRECATION WARNING:
-- yaz_sezonu_fiyat, kis_sezonu_fiyat, ara_sezon_fiyat
-- Bu alanlar artık "Season Pricing Manager" component'inde yönetilmeli!
-- Form'daki bu field'lar:
--   1. Hızlı giriş için tutulabilir (backup)
--   2. VEYA tamamen kaldırılıp sadece component kullanılabilir

-- ✅ YENİ ÖZELLIKLER:
-- 1. Season Pricing Manager (Section 4.9)
--    → Dinamik sezon tanımlama
--    → Yaz/Kış/Ara sezon
--    → Tarih aralığı bazlı
--    → Haftalık/aylık otomatik indirim
--
-- 2. Event/Booking Calendar (Section 4.8)
--    → Rezervasyon yönetimi
--    → Bloke tarihler
--    → Availability tracking

-- 🎯 ÖNERİ:
-- Sezon fiyatlarını KALDIRIN veya GİZLEYİN (enabled = false)
-- Sadece Season Pricing Manager kullanılsın!

-- UYGULAMA (Opsiyonel - Sezon fiyatlarını gizle):
-- UPDATE kategori_yayin_tipi_field_dependencies
-- SET enabled = false
-- WHERE kategori_slug = 'yazlik' 
-- AND field_slug IN ('yaz_sezonu_fiyat', 'kis_sezonu_fiyat', 'ara_sezon_fiyat');
