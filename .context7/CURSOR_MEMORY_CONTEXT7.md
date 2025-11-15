# Context7 - Cursor Memory Dokümantasyonu

## 🎯 Context7 Nedir?

Context7, Yalıhan Emlak projesinin kod standartları ve kurallarını yöneten bir sistemdir. İki ana bileşenden oluşur:

1. **Upstash Context7 MCP** - Kütüphane dokümantasyonu (Laravel, React, MySQL vb.)
2. **Yalıhan Bekçi Context7** - Proje kuralları ve standartları

## 🔄 "Context7 Kullan" Ne Demek?

Kullanıcı **"Context7 kullan"**, **"Context7 kurallarına göre düzenle"** veya **"Context7'e göre yap"** dediğinde:

### Otomatik Aktivasyon:

1. **Upstash Context7 MCP** otomatik aktif olur:
   - Kütüphane dokümantasyonu çeker
   - Güncel API örnekleri sağlar
   - Versiyon-spesifik dokümantasyon getirir

2. **Yalıhan Bekçi Context7** otomatik aktif olur:
   - Proje kurallarını kontrol eder
   - Kod doğrulaması yapar
   - Pattern kontrolü yapar
   - Sistem yapısını kontrol eder

### Örnek Kullanım Senaryoları:

#### Senaryo 1: "Context7 kullan, Laravel migration oluştur"
```
→ Upstash Context7 MCP: Laravel migration dokümantasyonu çeker
→ Yalıhan Bekçi Context7: Proje migration standartlarını kontrol eder
→ Sonuç: Context7 uyumlu migration kodu üretilir
```

#### Senaryo 2: "Context7 kurallarına göre düzenle"
```
→ Yalıhan Bekçi Context7: Mevcut kodu analiz eder
→ Yasaklı pattern'leri kontrol eder (durum, is_active, neo-*, btn-*)
→ Tailwind CSS kullanımını kontrol eder
→ transition-all duration-200 kontrolü yapar
→ Dark mode variant'larını kontrol eder
→ Sonuç: Context7 uyumlu kod önerilir
```

#### Senaryo 3: "Context7'e göre yap"
```
→ Her iki sistem de aktif olur
→ Kütüphane dokümantasyonu + Proje kuralları birleştirilir
→ En güncel ve proje standartlarına uygun kod üretilir
```

## 📋 Context7 Temel Kuralları

### Yasaklı Pattern'ler:
- ❌ `durum` → ✅ `status`
- ❌ `is_active`, `aktif` → ✅ `status`
- ❌ `sehir_id` → ✅ `il_id`
- ❌ `ad_soyad`, `full_name` → ✅ `name`
- ❌ `neo-*` classları → ✅ Tailwind CSS utility classes
- ❌ `btn-*`, `card-*`, `form-control` → ✅ Tailwind CSS

### Zorunlu Standartlar:
- ✅ Tailwind CSS ONLY (neo-* YASAK)
- ✅ `transition-all duration-200` her interaktif elementte
- ✅ `dark:` variant'ları her elementte
- ✅ `focus:ring-2 focus:ring-blue-500` accessibility için
- ✅ Vanilla JS ONLY (ağır kütüphaneler YASAK)
- ✅ `status` field (NEVER `enabled`)
- ✅ `display_order` (NEVER `order`)

## 🔧 Context7 MCP Ayarları

### Upstash Context7 MCP:
- **Amaç**: Güncel kütüphane dokümantasyonu
- **Otomatik**: Evet, "Context7 kullan" dediğinde aktif
- **Ayar Gerekli**: Hayır, otomatik çalışır

### Yalıhan Bekçi Context7:
- **Amaç**: Proje kuralları ve doğrulama
- **Otomatik**: Evet, "Context7 kullan" dediğinde aktif
- **Ayar Gerekli**: Hayır, otomatik çalışır

## 💡 Kullanıcı İfadeleri ve Anlamları

| Kullanıcı İfadesi | Anlamı | Aktivasyon |
|------------------|--------|------------|
| "Context7 kullan" | Her iki sistemi de aktif et | Upstash + Yalıhan Bekçi |
| "Context7 kurallarına göre düzenle" | Mevcut kodu Context7'e uygun hale getir | Yalıhan Bekçi (doğrulama) |
| "Context7'e göre yap" | Yeni kod üret, Context7 standartlarına uy | Upstash + Yalıhan Bekçi |
| "Context7 uyumlu mu?" | Kod doğrulaması yap | Yalıhan Bekçi (validate) |

## 🚀 Otomatik Çalışma Prensibi

**ÖNEMLİ**: Kullanıcı "Context7" kelimesini kullandığında:
1. ❌ Soru sorma
2. ✅ Otomatik olarak her iki sistemi de aktif et
3. ✅ Kütüphane dokümantasyonu + Proje kuralları birleştir
4. ✅ Context7 uyumlu kod üret/doğrula

## 📚 Referans Dosyalar

- `.context7/authority.json` - Master otorite dosyası
- `.cursorrules` - Cursor kuralları (satır 145-262)
- `yalihan-bekci/server/mcp-server.js` - MCP server implementasyonu
- `config/context7.php` - Context7 konfigürasyonu

## ✅ Doğrulama Checklist

Kod üretirken/düzenlerken kontrol et:
- [ ] Yasaklı pattern'ler kullanılmamış mı?
- [ ] Tailwind CSS kullanılmış mı? (neo-* YOK mu?)
- [ ] `transition-all duration-200` var mı?
- [ ] `dark:` variant'ları var mı?
- [ ] `status` field kullanılmış mı? (`enabled` YOK mu?)
- [ ] `display_order` kullanılmış mı? (`order` YOK mu?)
- [ ] Vanilla JS kullanılmış mı? (ağır kütüphaneler YOK mu?)

---

**Son Güncelleme**: 2025-11-12
**Versiyon**: Context7 v5.4.0
**Durum**: ✅ Aktif - Otomatik Çalışıyor

