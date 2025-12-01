# 🎨 Frontend Performans ve Tasarım Planı 2025

**Kaynaklar birleştirildi:**
- `FRONTEND_BUYUK_VERI_COZUMLERI.md`
- `TASARIM_GELISTIRME_PLANI_2025.md`

Bu dosya; frontend tarafında **büyük veri performansı** ve **tasarım / UX geliştirmeleri** için tek referans rehberdir.

---

## 1. Mevcut Durum (Özet)

- Laravel + Blade + Tailwind + Alpine.js kullanılıyor.
- Listeler için klasik pagination + bazı yerlerde `ListPaginate` JS servisi var.
- Grid, table, card bileşenleri mevcut; ancak çok büyük veri setlerinde (10.000+ kayıt) performans zayıflıyor.
- Tasarım tarafında Context7 / Tailwind standartları büyük ölçüde uygulanmış, bazı sayfalar eski yapıyı kullanıyor.

---

## 2. Büyük Veri Çözümleri (Liste Performansı)

Bu bölüm `FRONTEND_BUYUK_VERI_COZUMLERI.md` içeriğinin sadeleştirilmiş özeti.

### 2.1 Infinite Scroll (Sonsuz Kaydırma)

**Amaç:** Kullanıcı sayfa sonuna geldikçe yeni ilanların otomatik yüklenmesi.

- Backend:
  - Cursor-based pagination (`last_id`, `limit`) ile, `id > last_id` ve `limit` kadar kayıt.
  - Sadece gerekli kolonları seç (`select([...])`), ilişkilerde eager loading.
- Frontend:
  - Alpine.js ile `infiniteScroll()` komponenti.
  - Intersection Observer ile yükleme tetikleme.
  - Loading, error, "tüm sonuçlar gösterildi" durumları.

> Öneri: İlan liste sayfaları için birincil yaklaşım.

### 2.2 Virtual Scrolling (Sanal Kaydırma)

**Amaç:** 50.000–100.000+ kayıt için bile **yalnızca görünen satırları** DOM’da tutmak.

- Yüksek hacimli admin tablolarında kullanılmalı.
- Sabit satır yüksekliği (`itemHeight`) + container yüksekliği (`containerHeight`) üzerinden hesaplama.
- Alpine.js ile basit bir `virtualScroll()` helper komponenti yeterli.

> Öneri: Özellikle admin tarafındaki uzun listelerde (log, hareket, büyük dataset) kullan.

### 2.3 Lazy Loading Images

**Amaç:** Görselleri ancak görünür alana geldiklerinde yüklemek.

- Intersection Observer tabanlı `lazy-image` komponenti.
- Placeholder + progressive yükleme (thumb → full).

> Öneri: İlan kartlarındaki tüm resim alanlarında zorunlu hale getir.

### 2.4 Progressive Data Loading

**Amaç:** İlk etapta küçük sayıdaki kaydı hızlı gösterip, sonra "Daha Fazla Yükle" ile artırmak.

- Kullanıcı deneyimi açısından, Infinite Scroll ile birlikte veya alternatif olarak kullanılabilir.
- Skeleton ekranlar ile iyi bir ilk izlenim sağlar.

---

## 3. Tasarım ve UX Geliştirme Planı

Bu bölüm `TASARIM_GELISTIRME_PLANI_2025.md` içeriğinin sadeleştirilmiş halidir.

### 3.1 Hedefler

- Tüm admin ve frontend sayfalarında **tutarlı** tasarım:
  - Renk paleti, tipografi, spacing
  - Form bileşenleri (`FORM_STANDARDS.md` ile uyumlu)
  - Kart, tablo, modal pattern’leri
- Dark mode desteğinin her yerde çalışması.
- Mobil cihazlarda kullanılabilirlik.

### 3.2 Öncelikli Alanlar

1. **İlan Liste ve Detay Sayfaları**
   - Yeni grid/card tasarımı.
   - Büyük veri çözümleri (Infinite Scroll + Lazy Image).

2. **Admin AI Sayfaları**
   - AI ilan taslakları ve mesaj taslakları için modern, okunabilir layout.
   - Durum badge’leri, aksiyon butonları, yan panel özetleri.

3. **Formlar ve Filtreler**
   - Tüm formların `docs/FORM_STANDARDS.md` ile uyumlu hale getirilmesi.
   - Filtre bloklarının sade ve tekrarlanabilir component haline getirilmesi.

---

## 4. Roadmap (Önerilen Sıra)

### Phase A – Altyapı

- [ ] Ortak listeleme bileşeni (Blade component veya View Component):
  - Grid + karta dayalı görünüm.
  - Infinite Scroll desteği.
  - Lazy Image kullanımı.
- [ ] Admin için ortak tablo bileşeni:
  - Virtual scrolling isteğe bağlı.
  - Ortak filtre/aksiyon header’ı.

### Phase B – Sayfa Dönüşümleri

- [ ] İlan liste sayfalarını yeni liste bileşenine taşı.
- [ ] Admin AI ekranlarını ortak layout ve tabloya taşı.
- [ ] Kritik formları `FormStandards` helper kullanarak refaktör et.

### Phase C – İyileştirmeler

- [ ] Yavaş sayfalarda Lighthouse / Web Vitals ölçümleri.
- [ ] İmaj optimizasyonu (boyut, format, cache).
- [ ] UX feedback’lerine göre iterasyon.

---

## 5. Kurallar ile Uyum

- CSS ve form tarafında `docs/FORM_STANDARDS.md` ve `.yalihan-bekci/knowledge/css-system-standards-*.md` ile uyum zorunludur.
- Kod örneklerinde mümkünse **vanilla JS + Alpine.js + Tailwind** üçlüsü kullanılmalı, ağır kütüphaneler eklenmemelidir.
- Yeni pattern’ler `.context7/` ve `yalihan-bekci/knowledge/` altında dokümante edilmelidir.

---

## 6. İlgili Dokümanlar

- `docs/FORM_STANDARDS.md` – Form bileşenleri ve CSS standardı.
- `.context7/TAILWIND-TRANSITION-RULE.md` – Tailwind geçiş kuralları.
- `MASTER_PROMPT_YALIHAN_EMLAK_AI.md` – AI tarafında frontend’e dokunan kurallar.
- `.yalihan-bekci/knowledge/css-architecture-standards.md` (varsa) – CSS mimarisi.
