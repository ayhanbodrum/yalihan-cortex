# Context7 MCP Entegrasyon Rehberi

**Tarih:** 2025-11-11  
**Versiyon:** 1.0.0  
**Durum:** ✅ Aktif  
**Context7 Compliance:** 100%

---

## 📋 Genel Bakış

Context7 MCP, AI kod asistanlarının güncel kütüphane dokümantasyonlarına erişmesini sağlar. Bu rehber, Context7 MCP'nin projeye nasıl entegre edildiğini ve nasıl kullanılacağını açıklar.

---

## 🔧 Kurulum

### 1. MCP Server Konfigürasyonu

**Dosya:** `~/.cursor/mcp.json`

```json
{
  "mcpServers": {
    "context7": {
      "command": "npx",
      "args": ["-y", "@upstash/context7-mcp", "--api-key", "YOUR_API_KEY"],
      "env": {
        "CTX7_WATCH_DIRS": "app,resources,database,config,docs"
      }
    }
  }
}
```

### 2. API Key Alma

1. [Context7 Dashboard](https://context7.com/dashboard) adresine gidin
2. Hesap oluşturun veya giriş yapın
3. API Key'inizi kopyalayın
4. `mcp.json` dosyasına ekleyin

---

## 🎯 Kullanım Senaryoları

### Senaryo 1: Laravel Dokümantasyonu

**Kullanıcı Sorusu:**
```
"Laravel migration nasıl oluşturulur?"
```

**Otomatik İşlem:**
1. `resolve-library-id("Laravel")` → `/laravel/laravel`
2. `get-library-docs("/laravel/laravel", topic: "migrations")`
3. Güncel Laravel migration dokümantasyonu sağlanır

### Senaryo 2: React Hooks

**Kullanıcı Sorusu:**
```
"React useState hook örnekleri göster"
```

**Otomatik İşlem:**
1. `resolve-library-id("React")` → `/facebook/react`
2. `get-library-docs("/facebook/react", topic: "hooks", tokens: 3000)`
3. Güncel React hooks dokümantasyonu sağlanır

### Senaryo 3: Direkt Library ID

**Kullanıcı Sorusu:**
```
"Supabase authentication implement et. 
use library /supabase/supabase for API and docs."
```

**Otomatik İşlem:**
1. `resolve-library-id` atlanır (direkt ID verilmiş)
2. `get-library-docs("/supabase/supabase")`
3. Supabase dokümantasyonu sağlanır

---

## 🔍 Context7 MCP Araçları

### 1. `resolve-library-id`

**Amaç:** Kütüphane adını Context7-compatible ID'ye çevirir

**Parametreler:**
- `libraryName` (required): Kütüphane adı (örn: "Laravel", "React")

**Örnek:**
```json
{
  "libraryName": "Laravel"
}
```

**Dönüş:**
```json
{
  "libraryId": "/laravel/laravel"
}
```

### 2. `get-library-docs`

**Amaç:** Kütüphane dokümantasyonunu getirir

**Parametreler:**
- `context7CompatibleLibraryID` (required): Context7 ID (örn: "/laravel/laravel")
- `topic` (optional): Konu odaklı dokümantasyon (örn: "routing", "migrations")
- `tokens` (optional, default: 5000): Max token sayısı

**Örnek:**
```json
{
  "context7CompatibleLibraryID": "/laravel/laravel",
  "topic": "migrations",
  "tokens": 3000
}
```

---

## 📝 Cursor Rules Entegrasyonu

**Dosya:** `.cursorrules`

Context7 MCP otomatik kullanım kuralları `.cursorrules` dosyasına eklenmiştir:

```markdown
## 🔗 CONTEXT7 MCP INTEGRATION

**CRITICAL RULE:** Always use Context7 MCP for library documentation.

### ✅ AUTOMATIC CONTEXT7 USAGE:

When user asks about:
- Library documentation → Automatically use Context7 MCP
- Code generation → Use Context7 for latest API docs
- Setup/Configuration → Use Context7 for official docs
```

---

## 🚀 Avantajlar

### 1. Güncel Dokümantasyon
- ✅ Her zaman en son API'ler
- ✅ Sürüm farkındalığı
- ✅ Deprecated API uyarıları

### 2. Otomatik Kullanım
- ✅ Kullanıcı açıkça istemeden çalışır
- ✅ AI asistan otomatik olarak Context7 kullanır
- ✅ Daha doğru kod önerileri

### 3. Topic-Focused
- ✅ Sadece ilgili dokümantasyon
- ✅ Gereksiz bilgi yok
- ✅ Token optimizasyonu

---

## 🔧 Troubleshooting

### Problem 1: Module Not Found

**Hata:**
```
ERR_MODULE_NOT_FOUND
```

**Çözüm:**
```json
{
  "command": "bunx",
  "args": ["-y", "@upstash/context7-mcp"]
}
```

### Problem 2: ESM Resolution Issues

**Hata:**
```
Error: Cannot find module 'uriTemplate.js'
```

**Çözüm:**
```json
{
  "command": "npx",
  "args": ["-y", "--node-options=--experimental-vm-modules", "@upstash/context7-mcp"]
}
```

### Problem 3: TLS/Certificate Issues

**Hata:**
```
TLS certificate verification failed
```

**Çözüm:**
```json
{
  "command": "npx",
  "args": ["-y", "--node-options=--experimental-fetch", "@upstash/context7-mcp"]
}
```

---

## 📊 Kullanım İstatistikleri

### Desteklenen Kütüphaneler

- **Backend:** Laravel, Symfony, Express.js, Django
- **Frontend:** React, Vue.js, Angular, Next.js
- **Database:** MySQL, PostgreSQL, MongoDB, Redis
- **Tools:** Docker, Git, npm, Composer

### Proje İçi Kullanım

- **Laravel Dokümantasyonu:** Migration, Eloquent, Routing
- **React Dokümantasyonu:** Hooks, Components, State Management
- **MySQL Dokümantasyonu:** Queries, Indexes, Performance

---

## 🔗 Kaynaklar

- [Context7 GitHub](https://github.com/upstash/context7)
- [Context7 Website](https://context7.com)
- [Context7 Dashboard](https://context7.com/dashboard)
- [MCP Protocol Documentation](https://modelcontextprotocol.io)

---

## ✅ Sonuç

Context7 MCP başarıyla entegre edildi ve aktif olarak kullanılıyor. AI kod asistanınız artık her zaman güncel, doğru ve sürüm uyumlu dokümantasyon kullanıyor.

**Durum:** ✅ Aktif ve Çalışıyor  
**Son Güncelleme:** 2025-11-11

