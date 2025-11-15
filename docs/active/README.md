# 📚 Aktif Dokümantasyon Dizini

**Son Güncelleme:** 8 Kasım 2025  
**Kapsam:** `docs/active/` klasöründeki yaşayan, uzun ömürlü rehberler.

Bu dizin; proje mimarisi, Context7 kuralları ve API referansları gibi ekibin günlük ihtiyaç duyduğu temel belgeleri içerir. Tarihli raporlar ve tamamlanmış analizler arşive taşınmıştır.

---

## 🔖 Çekirdek Belgeler

| Belge                        | Amaç                                                                      |
| ---------------------------- | ------------------------------------------------------------------------- |
| `PROJECT-ANATOMY.md`         | Modül haritası, servis yapısı, Context7 × Yalıhan Bekçi ilişkisi          |
| `CONTEXT7-MASTER-GUIDE.md`   | Context7 standartlarının üst düzey özeti                                  |
| `CONTEXT7-RULES-DETAILED.md` | Yasaklı alan adları, Tailwind, form ve harita standartlarının tam listesi |
| `API-REFERENCE.md`           | Context7 API uçları ve örnek kullanımlar                                  |
| `DATABASE-SCHEMA.md`         | İlanlar ve bağlı tablolar için şema özeti                                 |
| `SYSTEM-STATUS-2025.md`      | 2025 sistem durumu, metrikler ve izleme notları                           |

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
- Yeni modül veya servis eklendiğinde `PROJECT-ANATOMY.md` dosyasını güncelle.

---

**Hedef:** Ekibin tek kaynaktan güncel bilgiye erişmesini sağlamak ve MD dağınıklığını minimumda tutmak. Bakım sorumluluğu proje mimar ekibindedir.
