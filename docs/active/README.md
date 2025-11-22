# 📚 Aktif Dokümantasyon Dizini

**Son Güncelleme:** 20 Kasım 2025  
**Dosya Sayısı:** 5 dosya (sade ve temiz yapı)  
**Kapsam:** `docs/active/` klasöründeki yaşayan, uzun ömürlü rehberler.

Bu dizin; proje mimarisi, Context7 kuralları ve API referansları gibi ekibin günlük ihtiyaç duyduğu temel belgeleri içerir. Tarihli raporlar ve tamamlanmış analizler arşive taşınmıştır.

---

## 🔖 Çekirdek Belgeler

| Belge | Amaç | Durum |
|-------|------|-------|
| `PROJE_CALISMA_SISTEMI_VE_GELISIM_PLANI.md` | **Ana Dokümantasyon** - Proje çalışma sistemi, mimari, modüller, API'ler, AI, Context7, Cache, Error Handling, Security, Queue, Environment, Frontend Standartları, Teknik Detaylar | ✅ Güncel (v1.3.0) |
| `SYSTEM-STATUS-2025.md` | 2025 sistem durumu, metrikler ve izleme notları (dinamik rapor) | ✅ Güncel |
| `YARIM_KALMIS_PLANLAMALAR.md` | Yarım kalmış planlar, önceliklendirme, süre tahminleri | ✅ Güncel |
| `ANALIZ_VE_GELISIM_FIRSATLARI.md` | Mevcut durum analizi, yarım kalmış planlar, geliştirme fırsatları, önceliklendirme matrisi | ✅ Güncel (v2.0.0) |

> Diğer güncel rehberler:  
> • Standartlar → `docs/rules/`  
> • Yol haritaları → `docs/roadmaps/`  
> • Teknik detaylar → `docs/technical/`  
> • Tarihsel raporlar → `docs/archive/YYYY-MM/`

---

## 🧭 Güncelleme Kuralları

1. **Uzun soluklu rehberler** bu dizine eklenir; günlük/haftalık raporlar arşive taşınır.
2. Belge güncellendiğinde üstteki _Son Güncelleme_ tarihini yenile.
3. Yeni rehber eklersen tabloya satır eklemeyi unutma.
4. Önemli değişiklikleri Yalıhan Bekçi hafızasına (`.yalihan-bekci/knowledge/`) kaydet.
5. Arşive taşınan dokümanlar için `docs/archive/2025-11/root-reports/` klasör yapısını kullan.

---

## 🛠 Bakım İpuçları

- `scripts/generate-doc-index.sh` komutu ile doküman indekslerini yenileyebilirsin.
- Bekçi raporlarında bu dizine referans vererek standart setini canlı tut.
- Yeni modül veya servis eklendiğinde `PROJE_CALISMA_SISTEMI_VE_GELISIM_PLANI.md` dosyasını güncelle (Bölüm 3: Modül Sistemi).

---

**Hedef:** Ekibin tek kaynaktan güncel bilgiye erişmesini sağlamak ve MD dağınıklığını minimumda tutmak. Bakım sorumluluğu proje mimar ekibindedir.
