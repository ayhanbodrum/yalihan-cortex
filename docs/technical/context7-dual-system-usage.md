# Context7 Dual System Kullanım Rehberi

**Tarih:** 2025-11-11  
**Versiyon:** 1.0.0  
**Durum:** ✅ Aktif

---

## 🎯 İki Context7 Sistemi

Projenizde **iki farklı Context7 sistemi** var ve **her ikisi de** otomatik olarak devreye girebilir:

### 1. **Upstash Context7 MCP** (Dış Kaynak)
- **Amaç:** Genel kütüphane dokümantasyonu
- **Kullanım:** Laravel, React, MySQL vb. dokümantasyonu

### 2. **Yalıhan Bekçi Context7** (Proje İçi)
- **Amaç:** Proje kuralları ve standartlar
- **Kullanım:** Kod doğrulama, proje uyumluluğu

---

## 🔄 "Context7 Kullan" Komutu

Kullanıcı **"Context7 kullan"** dediğinde:

### ✅ Otomatik Devreye Giren Sistemler:

1. **Upstash Context7 MCP** → Kütüphane dokümantasyonu
2. **Yalıhan Bekçi Context7** → Proje kuralları

**Her ikisi de birlikte çalışır!**

---

## 📝 Kullanım Senaryoları

### Senaryo 1: "Context7 kullan" Komutu

**Kullanıcı:**
```
"Context7 kullan, Laravel migration nasıl oluşturulur?"
```

**Otomatik İşlem:**
```
1. Upstash Context7 MCP:
   → resolve-library-id("Laravel")
   → get-library-docs("/laravel/laravel", topic: "migrations")
   → Güncel Laravel migration dokümantasyonu

2. Yalıhan Bekçi Context7:
   → get_context7_rules()
   → check_pattern("migration")
   → validate(code)
   → Proje kurallarına uygun mu kontrol et
```

**Sonuç:**
- ✅ Güncel Laravel migration dokümantasyonu
- ✅ Proje kurallarına uygun kod örneği

### Senaryo 2: Kod Oluşturma

**Kullanıcı:**
```
"Context7 kullan, yeni bir model oluştur"
```

**Otomatik İşlem:**
```
1. Upstash Context7 MCP:
   → Laravel Eloquent dokümantasyonu

2. Yalıhan Bekçi Context7:
   → Proje model standartları
   → status field kontrolü
   → display_order kontrolü
   → Tailwind CSS kontrolü
```

**Sonuç:**
- ✅ Laravel standartlarına uygun model
- ✅ Proje kurallarına uygun model

### Senaryo 3: Kod Doğrulama

**Kullanıcı:**
```
"Context7 kullan, bu kod uyumlu mu?"
```

**Otomatik İşlem:**
```
1. Yalıhan Bekçi Context7:
   → validate(code)
   → check_pattern()
   → get_context7_rules()

2. Upstash Context7 MCP:
   → Gerekirse kütüphane dokümantasyonu
```

**Sonuç:**
- ✅ Kod uyumluluk raporu
- ✅ Hata tespiti
- ✅ Düzeltme önerileri

---

## 🔧 Teknik Detaylar

### Upstash Context7 MCP Araçları

```javascript
// resolve-library-id
{
  "libraryName": "Laravel"
}
→ "/laravel/laravel"

// get-library-docs
{
  "context7CompatibleLibraryID": "/laravel/laravel",
  "topic": "migrations",
  "tokens": 5000
}
```

### Yalıhan Bekçi Context7 Araçları

```javascript
// get_context7_rules
→ { forbidden: [...], required: [...] }

// check_pattern
{
  "query": "migration"
}
→ Pattern kontrolü

// validate
{
  "code": "...",
  "filePath": "..."
}
→ Uyumluluk kontrolü

// get_system_structure
→ { models: {...}, controllers: {...} }
```

---

## ✅ Sonuç

**"Context7 kullan" dediğinizde:**

1. ✅ **Upstash Context7 MCP** → Kütüphane dokümantasyonu
2. ✅ **Yalıhan Bekçi Context7** → Proje kuralları

**Her ikisi de otomatik devreye girer!**

---

**Durum:** ✅ Aktif ve Çalışıyor  
**Son Güncelleme:** 2025-11-11

