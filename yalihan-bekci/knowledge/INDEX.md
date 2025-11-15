# 📚 Yalıhan Bekçi Knowledge Index

**Sürüm:** 2.1.0  
**Son Güncelleme:** 2025-11-10  
**Durum:** 🟢 Aktif (aylık bakım gerekli)

---

## 🔝 Ana Referanslar

| Öncelik  | Belge                                          | Açıklama                                                 | Son Güncelleme |
| -------- | ---------------------------------------------- | -------------------------------------------------------- | -------------- |
| ⭐⭐⭐⭐ | `../SYSTEM-UPDATE-2025-11-02.md`               | Bekçi sisteminin top-line özeti                          | 2025-11-02     |
| ⭐⭐⭐⭐ | `../HARITA-ARACLARI-V2-OZET-2025-11-05.md`     | Harita araçları v2 çalışmaları                           | 2025-11-05     |
| ⭐⭐⭐   | `order-display-order-seeder-fix-2025-11-10.md` | Seeder & Authority.json order→display_order düzeltmeleri | 2025-11-10     |
| ⭐⭐⭐   | `../CSS_CLEANUP_SUMMARY.md`                    | Tailwind sonrası global CSS durumu                       | 2025-11-05     |
| ⭐⭐⭐   | `../LIGHT-MODE-DESIGN-SYSTEM.md`               | Light/Dark mode tasarım kuralları                        | 2024-12-27     |
| ⭐⭐⭐   | `../POLYMORPHIC-SYSTEM-QUICK-REF.md`           | Polymorphic field sistemi özet                           | 2025-11-02     |
| ⭐⭐     | `css-system-standards-2025-11-02.md`           | Tailwind + Context7 CSS rehberi                          | 2025-11-02     |
| ⭐⭐     | `learning-report-code-quality-2025-11-07.md`   | Son kod kalitesi tespiti                                 | 2025-11-07     |

Bu tabloda bulunmayan belgeler eski/ara çıktılardır; gerekli olduğunda arşive taşınır.

---

## 📦 Klasörlere Göre Aktif İçerik

### 1. `analysis/`

- `harita-tutarsizlik-analiz-2025-11-05.md`
- `ilan-pages-analysis-2025-11-01.md`
- `neo-design-cleanup-2025-11-01.md`

> **Not:** İlk incelemeden sonra tamamlanan dosyalar `analysis/archive/` klasörüne taşınmalıdır.

### 2. `knowledge/`

- `order-display-order-seeder-fix-2025-11-10.md` ⭐⭐⭐ (YENİ - Seeder & Authority.json düzeltmeleri)
- `order-display-order-seeder-fix-2025-11-10.json` (YENİ - JSON formatında knowledge)
- `order-display-order-learning-report-2025-11-09.md` (Order → Display Order standardı)
- `order-display-order-standard-2025-11-09.json` (Order standardı JSON)
- `css-system-standards-2025-11-02.md`
- `learning-report-code-quality-2025-11-07.md`
- `TAILWIND-CSS-MIGRATION-GUIDE-2025-10-30.md`
- `TAILWIND-V3-DOWNGRADE-FIX.md`
- `VANILLA-JS-ENFORCEMENT-RULES.md`

> _Index (bu dosya) ve README dışındaki belgeler rehber / referans amaçlıdır. Eski günlük kayıtları `archive/` klasörüne taşınmalıdır._

### 3. `reports/`

- `PAGE_ANALYSIS_AND_RECOMMENDATIONS_2025-11-07.md`
- `route-error-root-cause-analysis-2025-11-07.md`
- `adres-sistemi-nasil-calismali.md`
- `google-maps-roadmap-hatasi-cozum.md`
- `javascript-final-cozum-raporu.md`
- `javascript-hata-cozum-ozet-raporu.md`
- `dropdown-fix-2025-11-01-111550.json`
- `dropdown-fix-2025-11-01-111619.json`
- `tailwind-dropdown-scan-2025-11-01-111019.json`
- `sorun-dokumantasyon-eslesmesi.md`

> 30 günden eski, tamamlanmış raporlar `reports/archive/` altına taşınır. Mevcut klasörler: `2025-11/`, `2025-10/`, `2024-12/`.

### 4. `learned/`

- `MAINTENANCE-SESSION-2025-11-04.md`
- `PHOTO-MODEL-IMPLEMENTATION-2025-11-04.md`

> Öğrenme kayıtları iki hafta sonra arşivlenir ya da özetlenerek `knowledge/` altına taşınır.

### 5. `recommendations/`

- `immediate-actions-ilan-yonetimi-2025-10-31.md`
- `sirada-ne-var-2025-10-31.md`

> Güncel olmayan öneriler kapatılıp arşive taşınmalıdır.

---

## 🛠️ Bakım Checklist'i

1. **Aylık (veya büyük değişiklik sonrası)**
    - README ve bu INDEX dosyası güncellenir.
    - Kilit doküman tablosundaki tarihler gözden geçirilir.
    - `analysis/` ve `reports/` klasörleri arşivlenir.

2. **Yeni doküman eklendiğinde**
    - README’de “Kilit Dokümanlar” tablosuna eklenip eklenmeyeceğini değerlendir.
    - INDEX altında doğru klasör başlığına ekle ve son güncelleme tarihini yaz.

3. **Eski içerik temizliği**
    - Günlük/haftalık özetler 30 gün sonra kaldırılır veya `archive/` altına taşınır.
    - Yinelenen bilgiler tek rehberde birleştirilir.

4. **Commit notu**
    - Doküman değişiklikleri `docs:` veya `yalihan-bekci docs:` önekiyle commitlenir.

---

## 🔍 Hızlı Erişim Komutları

```bash
# Aktif raporları görüntüle
ls -1 yalihan-bekci/reports

# Bilgi rehberlerini incele
ls -1 yalihan-bekci/knowledge

# Arşivlenmiş belgeleri kontrol et (varsa)
find yalihan-bekci -maxdepth 2 -type d -name "archive"
```

---

**Bakım Sahibi:** Context7 AI Takımı  
**Bir Sonraki İnceleme:** 2025-12-07 (veya büyük güncelleme sonrası)
