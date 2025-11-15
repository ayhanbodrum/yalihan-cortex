# Context7 MCP - Proje Bilgisi Nasıl Öğreniliyor?

**Tarih:** 2025-11-11  
**Versiyon:** 1.0.0  
**Durum:** ✅ Aktif

---

## 🔍 İki Farklı Context7 Sistemi

Projenizde **iki farklı Context7 sistemi** var:

### 1. **Upstash Context7 MCP** (Dış Kaynak)
- **Amaç:** Genel kütüphane dokümantasyonu (Laravel, React, MySQL vb.)
- **Proje Bilgisi:** ❌ Projeyi bilmiyor, sadece genel dokümantasyon sağlıyor
- **Kaynak:** Upstash Context7 API (cloud)

### 2. **Yalıhan Bekçi Context7** (Proje İçi)
- **Amaç:** Proje özel kurallar ve standartlar
- **Proje Bilgisi:** ✅ Projeyi tamamen biliyor
- **Kaynak:** Proje içi dosyalar ve sistemler

---

## 📚 Yalıhan Bekçi Context7 - Projeyi Nasıl Biliyor?

### 1. **`.context7/authority.json` Dosyası**

**Konum:** `.context7/authority.json`

**İçerik:**
- Proje kuralları
- Yasaklı pattern'ler
- Zorunlu standartlar
- Teknoloji stack bilgileri
- Migration standartları
- CSS framework kuralları

**Nasıl Kullanılıyor:**
```javascript
// yalihan-bekci/server/mcp-server.js
const context7Rules = require('../knowledge/context7-rule-loader');
// authority.json dosyasını otomatik okur
```

### 2. **`CTX7_WATCH_DIRS` Environment Variable**

**Konum:** `~/.cursor/mcp.json`

**Değer:**
```json
{
  "env": {
    "CTX7_WATCH_DIRS": "app,resources,database,config,docs"
  }
}
```

**Ne Yapıyor:**
- Bu dizinleri izler
- Değişiklikleri takip eder
- Proje yapısını analiz eder

**İzlenen Dizinler:**
- `app/` - Laravel uygulama kodları
- `resources/` - Blade view'lar, JavaScript
- `database/` - Migration'lar, seeders
- `config/` - Konfigürasyon dosyaları
- `docs/` - Dokümantasyon

### 3. **Master Dokümanlar**

**Konum:** Çeşitli yerler

**Dosyalar:**
- `docs/ai-training/02-CONTEXT7-RULES-SIMPLIFIED.md`
- `docs/context7/rules/context7-rules.md`
- `README.md`
- `.context7/authority.json`

**Nasıl Öğreniliyor:**
```javascript
// yalihan-bekci/knowledge/context7-rule-loader.js
this.masterDocs = [
    'docs/ai-training/02-CONTEXT7-RULES-SIMPLIFIED.md',
    'docs/context7/rules/context7-rules.md',
    'README.md',
    '.context7/authority.json',
];

// Her dosyayı parse eder ve kuralları çıkarır
this.parseDocument(fullPath);
```

### 4. **Yalıhan Bekçi MCP Server**

**Konum:** `yalihan-bekci/server/mcp-server.js`

**Özellikler:**
- Proje kök dizinini bilir
- Dosya sistemine erişir
- Kuralları yükler
- Sistem yapısını analiz eder

**Kod:**
```javascript
class YalihanBekciMCP {
    constructor() {
        this.projectRoot = path.join(__dirname, '../..');
        // Proje kök dizinini bilir
    }
}
```

### 5. **Otomatik Öğrenme Sistemi**

**Konum:** `yalihan-bekci/knowledge/`

**Sistemler:**
- `context7-rule-loader.js` - Kuralları öğrenir
- `system-memory.js` - Sistem hafızası
- `knowledge-base.js` - Bilgi tabanı
- `error-learner.js` - Hata öğrenme

**Nasıl Çalışıyor:**
1. Master dokümanları tarar
2. Kuralları parse eder
3. Pattern'leri öğrenir
4. Hafızaya kaydeder
5. MCP server'a sağlar

---

## 🔄 Öğrenme Süreci

### Adım 1: Dosya Tarama
```
yalihan-bekci/knowledge/context7-rule-loader.js
→ Master dokümanları bulur
→ .context7/authority.json okur
→ CTX7_WATCH_DIRS dizinlerini tarar
```

### Adım 2: Kural Çıkarma
```
→ MD dosyalarını parse eder
→ Yasaklı pattern'leri bulur
→ Zorunlu standartları çıkarır
→ Proje yapısını analiz eder
```

### Adım 3: Hafızaya Kaydetme
```
→ system-memory.js'e kaydeder
→ knowledge-base.js'e ekler
→ MCP server'a sağlar
```

### Adım 4: MCP Server'a Aktarma
```
→ Yalıhan Bekçi MCP server kuralları yükler
→ AI asistan'a sağlar
→ Otomatik kontrol yapar
```

---

## 📊 Proje Bilgisi Kaynakları

### 1. **Proje Yapısı**
```javascript
// get_system_structure tool
{
  "models": { "count": 101, "files": [...] },
  "controllers": { "count": 118, "files": [...] },
  "views": { "count": 406, "files": [...] },
  "migrations": { "count": 136, "files": [...] }
}
```

### 2. **Yasaklı Pattern'ler**
```json
{
  "forbidden": [
    "durum", "is_active", "aktif",
    "sehir", "sehir_id",
    "neo-*", "btn-*"
  ]
}
```

### 3. **Zorunlu Standartlar**
```json
{
  "required": [
    "status field (NOT enabled)",
    "display_order (NOT order)",
    "Tailwind CSS (NOT Bootstrap)"
  ]
}
```

### 4. **Teknoloji Stack**
```json
{
  "framework": "Laravel 10.x",
  "database": "MySQL 8.0+",
  "css": "Tailwind CSS",
  "js": "Alpine.js + Vanilla JS"
}
```

---

## 🎯 Upstash Context7 MCP vs Yalıhan Bekçi

| Özellik | Upstash Context7 MCP | Yalıhan Bekçi Context7 |
|---------|---------------------|------------------------|
| **Proje Bilgisi** | ❌ Bilmiyor | ✅ Tamamen biliyor |
| **Kaynak** | Cloud API | Proje dosyaları |
| **Amaç** | Genel dokümantasyon | Proje kuralları |
| **Öğrenme** | Yok | Otomatik öğrenme |
| **Kurallar** | Yok | Proje kuralları |

---

## 🔍 Proje Bilgisi Nasıl Güncelleniyor?

### Otomatik Güncelleme
```bash
# Yalıhan Bekçi otomatik öğrenme
php artisan testsprite:auto-learn

# Context7 rule loader
node yalihan-bekci/knowledge/context7-rule-loader.js
```

### Manuel Güncelleme
```bash
# Authority.json güncelle
# Master dokümanları güncelle
# CTX7_WATCH_DIRS dizinlerini değiştir
```

---

## ✅ Sonuç

**Yalıhan Bekçi Context7** projeyi şu şekilde biliyor:

1. ✅ `.context7/authority.json` dosyasını okur
2. ✅ `CTX7_WATCH_DIRS` dizinlerini izler
3. ✅ Master dokümanları parse eder
4. ✅ Otomatik öğrenme sistemi kullanır
5. ✅ Proje yapısını analiz eder

**Upstash Context7 MCP** ise:
- ❌ Projeyi bilmiyor
- ✅ Sadece genel kütüphane dokümantasyonu sağlıyor

Her iki sistem birlikte çalışarak:
- **Upstash Context7 MCP** → Genel dokümantasyon
- **Yalıhan Bekçi Context7** → Proje kuralları

---

**Durum:** ✅ Aktif ve Çalışıyor  
**Son Güncelleme:** 2025-11-11

