# 🧹 DİZİN TEMİZLİK PLANI - 5 Kasım 2025

**Durum:** Root dizinde 63 MD dosyası var  
**Hedef:** Önemli dosyaları koru, eski raporları archive'e taşı, duplicate'leri sil

---

## ✅ KORUNACAK DOSYALAR (Root'ta kalacak - 17 dosya)

### **Aktif Rehberler:**
1. `README.md` - Ana proje dokümantasyonu
2. `CLI_GUIDE.md` - Komut satırı rehberi
3. `STANDARDIZATION_GUIDE.md` - Standartlaştırma rehberi
4. `MODERNIZATION_PLAN.md` - Modernizasyon planı
5. `APP-MODULES-ARCHITECTURE.md` - Modül mimarisi
6. `KOMUTLAR_REHBERI.md` - Komutlar rehberi

### **Pattern/Component Rehberleri:**
7. `NEO-TO-TAILWIND-PATTERN-GUIDE.md` - Migration pattern guide
8. `COMPONENT-LIBRARY-GUIDE.md` - Component library rehberi
9. `COMPONENT-USAGE-EXAMPLES.md` - Component kullanım örnekleri

### **Aktif Planlar:**
10. `SIRADA-YAPMAK-LISTE.md` - İş listesi
11. `SIRADAKI-3-ADIM.md` - Sonraki adımlar
12. `ILAN_YONETIMI_ANALIZ.md` - İlan yönetimi analizi

### **Güncel Raporlar (2025-11-05):**
13. `TURKIYEAPI-WIKIMAPIA-ENTEGRASYON-2025-11-05.md` - Entegrasyon raporu
14. `WIKIMAPIA-FULL-AUDIT-2025-11-05.md` - WikiMapia audit
15. `TCMB-KUR-API-RAPOR-2025-11-05.md` - Kur API raporu
16. `PHASE3-PROGRESS-2025-11-05.md` - Phase 3 ilerleme

---

## 📦 ARCHIVE'E TAŞINACAK DOSYALAR (46 dosya)

### **Eski Raporlar → `docs/archive/reports-2025-11-05/old-reports/`**
- GECE-COMPREHENSIVE-REPORT-2025-11-05.md
- GECE-FINAL-COMPREHENSIVE-REPORT-2025-11-05.md
- BUGUN-GECE-FINAL-2025-11-05.md
- BUGUN-TAMAMLANAN-2025-11-04-FINAL.md
- BUGUN-FINAL-OZET-2025-11-04-GECE.md
- BU-GECE-FINAL-OZET.md
- GECE-FINAL-OZET-2025-11-04.md
- GECE-FINAL-RAPOR-2025-11-04.md
- YARIN-PLAN-2025-11-05.md

### **Migration Raporları → `docs/archive/reports-2025-11-05/migration-reports/`**
- 50-SAYFA-ULTIMATE-FINAL-2025-11-05.md
- 15-SAYFA-LEGENDARY-FINAL-2025-11-05.md
- 12-SAYFA-HARDCORE-MODE-FINAL-2025-11-05.md
- 9-SAYFA-MIGRATION-FINAL-2025-11-05.md
- 6-SAYFA-MIGRATION-FINAL-2025-11-05.md
- TALEPLER-INDEX-MIGRATION-REPORT-2025-11-05.md
- AI-CATEGORY-MIGRATION-REPORT-2025-11-05.md
- COMPONENT-LIBRARY-COMPLETE-REPORT-2025-11-05.md
- COMPONENT-LIBRARY-COMPLETE.md

### **Eski Analiz Raporları → `docs/archive/reports-2025-11-05/analysis-reports/`**
- AI-ANALIZLERIN-DEGERLENDIRMESI.md
- ANYTHINGLLM-N8N-ENTEGRASYON-PLANI.md
- API-ENTEGRASYON-PLANI-2025-11-04.md
- AYARLAR-CREATE-ANALIZ.md
- AYARLAR-SISTEM-UPGRADE-2025-11-05.md
- DIZIN-BOYUTU-ANALIZI.md
- ESKi-FRONTEND-TEMIZLIK-PLANI.md
- FRONTEND-CSS-KARAR.md
- FRONTEND-DETAYLI-TARAMA-RAPORU.md
- FRONTEND-INCELEME-RAPORU.md
- GIT-BUYUK-DOSYA-ANALIZI.md
- GIT-FRESH-START-RAPORU.md
- GIT-TEMIZLIK-SECENEKLER.md
- HORIZON-COZUM.md
- HORIZON-VS-TELESCOPE-ACIKLAMA.md
- KRITIK_VERI_KURTARMA_RAPORU.md
- KULLANILMAYAN-DOSYALAR-RAPORU.md
- OZEL-MODULLER-DURUM-RAPORU-2025-11-04.md
- OZEL-MODULLER-EK-RAPOR-2025-11-04.md
- PROJE-ANATOMISI-DEGERLENDIRME.md
- PROJE-ANATOMISI-VE-ONERILER-2025-11-04.md
- TAILWIND-V4-BETA-DETAYLI-ANALIZ.md
- TAILWIND-V4-FRONTEND-STRATEJISI.md
- TAILWIND-V4-MIGRATION-SORUN.md
- TEMIZLIK-RAPORU-2025-11-04.md
- WIKIMAPIA-API-ISSUE-2025-11-05.md
- YALIHANEMLAK_ULTRA_BOS_SEBEB_RAPORU.md

---

## ❌ SİLİNECEK DUPLICATE DOSYALAR (3 dosya)

1. `COMPONENT-LIBRARY-README.md` → Duplicate of `COMPONENT-LIBRARY-GUIDE.md`
2. `COMPONENT-USAGE-GUIDE.md` → Duplicate of `COMPONENT-USAGE-EXAMPLES.md`
3. `PROJE-ANATOMISI-DEGERLENDIRME.md` → Duplicate of `PROJE-ANATOMISI-VE-ONERILER-2025-11-04.md`

---

## 📊 ÖZET

```yaml
Toplam MD Dosyası: 63
├── Korunacak: 17 (aktif rehberler + güncel raporlar)
├── Archive'e taşınacak: 46 (eski raporlar)
└── Silinecek: 3 (duplicate)

Sonuç: Root dizinde sadece 17 dosya kalacak! ✨
```

---

## 🚀 TEMİZLİK KOMUTLARI

```bash
# 1. Archive dizinleri oluştur
mkdir -p docs/archive/reports-2025-11-05/{old-reports,migration-reports,analysis-reports}

# 2. Eski raporları taşı
mv GECE-*.md BUGUN-*.md BU-GECE-*.md YARIN-*.md docs/archive/reports-2025-11-05/old-reports/

# 3. Migration raporlarını taşı
mv *-SAYFA-*.md *-MIGRATION-*.md COMPONENT-LIBRARY-COMPLETE*.md docs/archive/reports-2025-11-05/migration-reports/

# 4. Eski analiz raporlarını taşı
mv AI-ANALIZ*.md ANYTHINGLLM*.md API-ENTEGRASYON*.md AYARLAR-*.md DIZIN-*.md ESKi-*.md FRONTEND-*.md GIT-*.md HORIZON-*.md KRITIK_*.md KULLANILMAYAN-*.md OZEL-MODULLER-*.md PROJE-ANATOMISI-*.md TAILWIND-V4-*.md TEMIZLIK-*.md WIKIMAPIA-API-*.md YALIHANEMLAK_*.md docs/archive/reports-2025-11-05/analysis-reports/

# 5. Duplicate dosyaları sil
rm COMPONENT-LIBRARY-README.md COMPONENT-USAGE-GUIDE.md PROJE-ANATOMISI-DEGERLENDIRME.md
```

---

**Durum:** Plan hazır, onay bekleniyor! ✅

