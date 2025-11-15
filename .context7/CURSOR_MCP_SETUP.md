# Context7 MCP Setup Guide

## 🎯 Context7 MCP Nedir?

Context7 MCP (Model Context Protocol), up-to-date, version-specific dokümantasyon ve kod örneklerini kaynaklardan çekerek doğrudan prompt'unuza yerleştiren bir sistemdir.

### ⚠️ ÖNEMLİ: Upstash Context7 MCP'nin Sınırları

Upstash Context7 MCP **genel kütüphane dokümantasyonu** çeker (Laravel, React, MySQL vb.). Ancak:
- ❌ **Proje-spesifik kurallar** bilmez (Context7 standartları, Neo Design System vb.)
- ❌ **Bizim özel pattern'lerimiz** bilmez (status field, display_order vb.)

**Çözüm**: Yalıhan Bekçi Context7 proje kurallarını yönetir. İki sistem birlikte çalışır!

Detaylı bilgi için: `.context7/UPSTASH_CONTEXT7_TECHNOLOGIES.md`

## ✅ Otomatik Çalışma

**ÖNEMLİ**: Context7 MCP **otomatik olarak çalışır**. Ekstra ayar gerektirmez!

### Nasıl Çalışır?

1. **Upstash Context7 MCP** (External):
   - Cursor'da MCP server olarak yapılandırılmış
   - Otomatik olarak kütüphane dokümantasyonu çeker
   - Versiyon-spesifik dokümantasyon sağlar
   - **Ayar Gerekli**: ❌ HAYIR (Otomatik çalışır)

2. **Yalıhan Bekçi Context7** (Internal):
   - Proje içi MCP server (`yalihan-bekci/server/mcp-server.js`)
   - Proje kurallarını yönetir
   - Kod doğrulaması yapar
   - **Ayar Gerekli**: ❌ HAYIR (Otomatik çalışır)

## 🔧 Cursor'da Kullanım

### Prompt'a Ekleme:

```
"Context7 kullan, Laravel migration oluştur"
```

veya

```
"use context7" + "Laravel migration oluştur"
```

### Otomatik Aktivasyon:

Kullanıcı **"Context7 kullan"** veya **"use context7"** dediğinde:

1. ✅ Upstash Context7 MCP otomatik aktif olur
2. ✅ Yalıhan Bekçi Context7 otomatik aktif olur
3. ✅ Her iki sistem birlikte çalışır
4. ✅ Güncel dokümantasyon + Proje kuralları birleştirilir

## 📋 MCP Server Yapılandırması

### Cursor Settings (`.cursor/settings.json`):

Context7 MCP server'ları zaten yapılandırılmış durumda:

```json
{
  "mcpServers": {
    "context7": {
      "command": "npx",
      "args": ["-y", "@context7/mcp-server"]
    },
    "yalihan-bekci": {
      "command": "node",
      "args": ["yalihan-bekci/server/mcp-server.js"]
    }
  }
}
```

### Otomatik Sync:

- ✅ MCP server'lar otomatik başlatılır
- ✅ Context7 kuralları otomatik yüklenir
- ✅ Dokümantasyon otomatik çekilir
- ✅ Kod doğrulaması otomatik yapılır

## 🚀 Kullanım Örnekleri

### Örnek 1: Kütüphane Dokümantasyonu
```
Kullanıcı: "Context7 kullan, Laravel Eloquent relationships nasıl kullanılır?"
→ Upstash Context7 MCP: Laravel dokümantasyonu çeker
→ Yalıhan Bekçi Context7: Proje standartlarını kontrol eder
→ Sonuç: Context7 uyumlu kod örneği
```

### Örnek 2: Kod Doğrulama
```
Kullanıcı: "Context7 kurallarına göre bu kodu düzenle"
→ Yalıhan Bekçi Context7: Kodu analiz eder
→ Yasaklı pattern'leri tespit eder
→ Context7 uyumlu kod önerir
```

### Örnek 3: Yeni Kod Üretimi
```
Kullanıcı: "Context7'e göre yeni bir model oluştur"
→ Upstash Context7 MCP: Laravel model dokümantasyonu
→ Yalıhan Bekçi Context7: Proje standartları (status, display_order)
→ Sonuç: Context7 uyumlu model kodu
```

## ⚙️ Environment Variables (Opsiyonel)

Eğer özel ayarlar yapmak isterseniz `.env` dosyasına ekleyebilirsiniz:

```env
# Context7 MCP Configuration (Opsiyonel - Varsayılanlar çalışır)
CONTEXT7_MCP_ENABLED=true
CONTEXT7_MCP_URL=https://mcp.context7.com/mcp
CONTEXT7_API_URL=https://context7.com/api/v1
CONTEXT7_API_KEY=your-api-key-here
```

**NOT**: Bu ayarlar opsiyoneldir. Context7 MCP varsayılan ayarlarla otomatik çalışır.

## 🔍 Doğrulama

Context7 MCP'nin çalışıp çalışmadığını kontrol etmek için:

1. Cursor'da "Context7 kullan" yazın
2. Otomatik olarak her iki sistem aktif olmalı
3. Güncel dokümantasyon + Proje kuralları birleştirilmeli

## 📚 Referanslar

- `.cursorrules` (satır 145-262) - Context7 Dual System Integration
- `.context7/authority.json` - Master otorite dosyası
- `config/context7.php` - Context7 konfigürasyonu
- `yalihan-bekci/server/mcp-server.js` - MCP server implementasyonu

---

**Son Güncelleme**: 2025-11-12
**Durum**: ✅ Otomatik Çalışıyor - Ayar Gerektirmiyor

