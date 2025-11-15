# 📁 Yalıhan Bekçi Knowledge Base

**Amaç:** Context7 standartlarını koruyan AI bekçi sisteminin öğrenilmiş kurallarını, analizlerini ve güncel raporlarını merkezi bir klasörde tutmak.

**Son Güncelleme:** 7 Kasım 2025  
**Sorumlu:** Context7 AI Takımı

---

## 🧭 Dizin Yapısı

- `analysis/` → Güncel teknik incelemeler ve aktif optimizasyon notları
- `knowledge/` → Referans niteliğindeki kalıcı rehberler (Tailwind, Context7 vb.)
- `reports/` → Son durum raporları ve özetler (tamamlanan işler kısa süre tutulur)
    - `reports/archive/2025-11/` → Kasım 2025’e ait arşivlenmiş raporlar (otomatik tasnif)
    - `reports/archive/2025-10/` → Ekim 2025’e ait arşivlenmiş raporlar
    - `reports/archive/2024-12/` → Aralık 2024’e ait arşivlenmiş raporlar
- `recommendations/` → Açık aksiyonlar ve yol haritası önerileri
- `rules/` → Harita, Tailwind, Context7 gibi standart dokümanları
- `milestones/` → Önemli teslimatlar ve kilometre taşı dökümanları
- `learned/` → Bekçi sisteminin son öğrenme kayıtları (gerekirse arşivlenir)
- `tools/`, `collectors/` → MCP scriptleri ve otomasyon araçları

Eski veya tamamlanmış belgeler `archive/` klasörlerine taşınır. Uzun süreli saklama gerekmeyen günlük raporlar (örn. günlük özetler) düzenli olarak silinir.

---

## 🔑 Kilit Dokümanlar

| Dosya                                          | Açıklama                                  | Durum    |
| ---------------------------------------------- | ----------------------------------------- | -------- |
| `SYSTEM-UPDATE-2025-11-02.md`                  | Bekçi sisteminin son durum özeti          | Güncel   |
| `CSS_CLEANUP_SUMMARY.md`                       | Tailwind geçişi sonrası CSS temizliği     | Referans |
| `LIGHT-MODE-DESIGN-SYSTEM.md`                  | Light/Dark mode tasarım standardı         | Referans |
| `HARITA-ARACLARI-V2-OZET-2025-11-05.md`        | Harita sistemi v2 çalışmaları             | Güncel   |
| `POLYMORPHIC-SYSTEM-QUICK-REF.md`              | Polymorphic özellik sistemi hızlı rehberi | Referans |
| `knowledge/css-system-standards-2025-11-02.md` | Tailwind + Context7 CSS kuralları         | Referans |

Tüm dokümanların kapsayıcı listesi için `knowledge/INDEX.md` dosyasını kullanın.

---

## 🔄 Bakım Politikası

1. `README.md` ve `knowledge/INDEX.md` ayda en az bir kez ya da büyük değişikliklerden hemen sonra güncellenmelidir.
2. Yeni rapor eklenirken
    - README’de “Kilit Dokümanlar” tablosuna eklenip eklenmeyeceği değerlendirilir.
    - Index dosyasında ilgili kategori altına kayıt açılır.
3. Eski raporlar (30 günden eski ve tamamlanmış işler) `archive/` dizinine taşınır veya kaldırılır.
4. Tailwind/Context7 kuralları değiştiğinde `rules/` ve `knowledge/` altındaki rehberler eş zamanlı güncellenir.
5. MCP scriptleri (`tools/`, `collectors/`) değiştirildiğinde README’ye kısa not düşülür.

**Hatırlatma:** README ve INDEX dosyalarının güncel tutulması projenin bakım standartlarının bir parçasıdır. Değişiklik yapıldığında commit mesajlarına “yalihan-bekci docs update” benzeri açıklayıcı bir ifade ekleyin.

---

## 📌 Hızlı Komutlar

```bash
# Önemli rehberleri görüntüle
cat yalihan-bekci/CSS_CLEANUP_SUMMARY.md
cat yalihan-bekci/LIGHT-MODE-DESIGN-SYSTEM.md

# Güncel raporları listele
ls -1 yalihan-bekci/reports | head

# Arşivlenmiş raporları görüntüle
ls -1 yalihan-bekci/reports/archive/2025-11

# Bilgi index'ini incele
cat yalihan-bekci/knowledge/INDEX.md
```

---

**Soru / öneri:** MCP dokümantasyon sorumlusu ile iletişime geçin veya `recommendations/` klasöründe yeni bir kayıt oluşturun.
