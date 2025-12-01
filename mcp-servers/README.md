# 🤖 Yalıhan AI MCP Servers

**Versiyon:** 3.0.0  
**Son Güncelleme:** 30 Kasım 2025  
**Durum:** ✅ Aktif ve Güncel

---

## 📋 Genel Bakış

Bu dizin, Yalıhan Emlak projesi için Model Context Protocol (MCP) sunucularını içerir. MCP sunucuları, AI asistanlarının proje ile etkileşimini sağlar ve Context7 standartlarına uyumu garanti eder.

### Mevcut MCP Sunucuları

1. **Yalıhan Bekçi MCP** (`yalihan-bekci-mcp.js`) - AI öğrenme ve öğretme sistemi
2. **Context7 Validator MCP** (`context7-validator-mcp.js`) - Real-time validation ve auto-fix
3. **Laravel MCP** (`laravel-mcp.cjs`) - Laravel Artisan komutları ve database erişimi

---

## 🚀 Kurulum ve Başlatma

### Gereksinimler

- Node.js >= 18.0.0
- npm veya yarn

### Kurulum

```bash
cd mcp-servers
npm install
```

### Sunucuları Başlatma

```bash
# Tüm sunucuları başlat
npm run start:all

# Sadece Yalıhan Bekçi
npm run start:bekci

# Sadece Context7 Validator
npm run start:validator

# Sadece Laravel MCP
npm run start:laravel

# Development mode (auto-reload)
npm run dev
```

---

## 1️⃣ Yalıhan Bekçi MCP

### Amaç
AI asistanlarının projeden öğrenmesi ve öğretmesi için tasarlanmış akıllı sistem.

### Özellikler

- ✅ **Öğrenme Sistemi:** Yapılan işlemlerden pattern'leri öğrenir
- ✅ **İyileştirme Önerileri:** Kod kalitesi ve performans önerileri
- ✅ **Pattern Analizi:** Context7 ihlallerini ve kod pattern'lerini analiz eder
- ✅ **İhlal Tahmini:** Gelecekteki Context7 ihlallerini öngörür
- ✅ **Geliştirme Fikirleri:** Proje durumuna göre yeni özellik fikirleri üretir
- ✅ **Proje Sağlığı:** Genel proje sağlığını analiz eder

### Kullanılabilir Araçlar

#### `learn_from_action`
Yapılan işlemlerden öğrenir ve bilgi tabanına ekler.

```json
{
  "action_type": "context7_fix",
  "context": "Migration dosyasında order → display_order düzeltmesi",
  "files_changed": ["database/migrations/2025_11_30_create_table.php"]
}
```

#### `suggest_improvement`
Mevcut kod/proje durumuna göre iyileştirme önerileri üretir.

```json
{
  "scope": "file",
  "target_file": "app/Http/Controllers/IlanController.php",
  "area": "performance"
}
```

#### `analyze_pattern`
Kod pattern'lerini analiz eder ve raporlar.

```json
{
  "pattern_type": "context7_violations",
  "time_range": "last_week"
}
```

#### `predict_violation`
Gelecekteki Context7 ihlallerini öngörür.

```json
{
  "code_snippet": "Schema::create('users', function...",
  "file_path": "database/migrations/2025_11_30_create_users_table.php"
}
```

#### `generate_development_ideas`
Proje durumuna göre geliştirme fikirleri üretir.

```json
{
  "category": "performance",
  "priority": "high"
}
```

#### `get_project_health`
Proje sağlığını analiz eder ve rapor verir.

```json
{
  "include_metrics": true
}
```

### Bilgi Tabanı

- **Konum:** `.yalihan-bekci/knowledge/`
- **Format:** JSON ve Markdown
- **İçerik:** Context7 pattern'leri, öğrenilen kurallar, best practices

---

## 2️⃣ Context7 Validator MCP

### Amaç
Real-time Context7 validation ve otomatik düzeltme.

### Özellikler

- ✅ **Dosya Validasyonu:** Tek dosya Context7 kontrolü
- ✅ **Proje Validasyonu:** Tüm proje genelinde validation
- ✅ **Compliance Kontrolü:** Context7 uyumluluk seviyesi
- ✅ **Otomatik Düzeltme:** Auto-fix özellikleri
- ✅ **Rapor Oluşturma:** Detaylı compliance raporları

### Kullanılabilir Araçlar

#### `validate_file`
Dosyayı Context7 standartlarına göre validate eder.

```json
{
  "file_path": "app/Models/Ilan.php",
  "auto_fix": true
}
```

#### `validate_project`
Tüm projeyi Context7 standartlarına göre validate eder.

```json
{
  "scope": "migrations",
  "auto_fix": false
}
```

#### `check_compliance`
Context7 compliance seviyesini kontrol eder.

```json
{
  "detailed": true
}
```

#### `apply_rules`
Context7 kurallarını belirtilen dosyalara uygular.

```json
{
  "files": ["app/Models/User.php", "app/Models/Ilan.php"],
  "rule_set": "naming"
}
```

#### `generate_report`
Context7 compliance raporu oluşturur.

```json
{
  "format": "markdown",
  "save_to_file": true
}
```

### Authority Dosyası

- **Konum:** `.context7/authority.json`
- **İçerik:** Tüm Context7 kuralları ve standartları
- **Versiyon:** Otomatik olarak yüklenir

---

## 3️⃣ Laravel MCP

### Amaç
Laravel Artisan komutları ve database erişimi.

### Özellikler

- ✅ **Artisan Komutları:** Tüm Laravel Artisan komutlarını çalıştırır
- ✅ **Model Bilgileri:** Laravel model detaylarını getirir
- ✅ **Migration Yönetimi:** Database migration'ları çalıştırır
- ✅ **Context7 Kontrolü:** Context7 kurallarını kontrol eder
- ✅ **MySQL Sorguları:** Eloquent üzerinden database sorguları
- ✅ **Tablo Bilgileri:** MySQL tablo yapılarını getirir

### Kullanılabilir Araçlar

#### `artisan_command`
Laravel Artisan komutlarını çalıştırır.

```json
{
  "command": "migrate",
  "args": ["--force"]
}
```

#### `get_model_info`
Laravel model bilgilerini getirir.

```json
{
  "model": "Ilan"
}
```

#### `run_migration`
Database migration çalıştırır.

```json
{
  "fresh": false
}
```

#### `context7_check`
Context7 kurallarını kontrol eder.

```json
{
  "file": "database/migrations/2025_11_30_create_users_table.php"
}
```

#### `mysql_query`
MySQL veritabanında sorgu çalıştırır.

```json
{
  "operation": "count",
  "model": "User"
}
```

#### `get_table_info`
MySQL tablo bilgilerini getirir.

```json
{
  "table": "users"
}
```

---

## 📁 Dizin Yapısı

```
mcp-servers/
├── yalihan-bekci-mcp.js          # Yalıhan Bekçi MCP Server
├── context7-validator-mcp.js     # Context7 Validator MCP Server
├── laravel-mcp.cjs               # Laravel MCP Server
├── package.json                  # NPM package configuration
├── package-lock.json             # NPM lock file
├── README.md                     # Bu dosya
├── node_modules/                 # NPM dependencies
└── yalihan-bekci/                # Yalıhan Bekçi alt dizini
```

---

## 🔧 Konfigürasyon

### Environment Variables

```bash
# Proje root dizini
export PROJECT_ROOT="/Users/macbookpro/Projects/yalihanai"
```

### MCP Client Konfigürasyonu

MCP sunucularını AI asistanlarında kullanmak için `.cursor/mcp.json` veya benzeri konfigürasyon dosyasına ekleyin:

```json
{
  "mcpServers": {
    "yalihan-bekci": {
      "command": "node",
      "args": ["/Users/macbookpro/Projects/yalihanai/mcp-servers/yalihan-bekci-mcp.js"],
      "env": {
        "PROJECT_ROOT": "/Users/macbookpro/Projects/yalihanai"
      }
    },
    "context7-validator": {
      "command": "node",
      "args": ["/Users/macbookpro/Projects/yalihanai/mcp-servers/context7-validator-mcp.js"],
      "env": {
        "PROJECT_ROOT": "/Users/macbookpro/Projects/yalihanai"
      }
    },
    "laravel-mcp": {
      "command": "node",
      "args": ["/Users/macbookpro/Projects/yalihanai/mcp-servers/laravel-mcp.cjs"],
      "env": {
        "PROJECT_ROOT": "/Users/macbookpro/Projects/yalihanai"
      }
    }
  }
}
```

---

## 📊 Versiyon Geçmişi

### v3.0.0 (30 Kasım 2025)
- ✅ Proje root path düzeltildi (`/Users/macbookpro/Projects/yalihanai`)
- ✅ Context7 authority.json entegrasyonu eklendi
- ✅ Bilgi tabanı path'leri güncellendi (`.yalihan-bekci/`)
- ✅ Tüm MCP sunucuları senkronize edildi
- ✅ Package.json scripts genişletildi
- ✅ Kapsamlı dokümantasyon eklendi

### v2.0.0 (Kasım 2025)
- ✅ İlk stable release
- ✅ Yalıhan Bekçi MCP eklendi
- ✅ Context7 Validator MCP eklendi
- ✅ Laravel MCP eklendi

---

## 🔗 İlgili Dokümantasyon

- **Context7 Authority:** `.context7/authority.json`
- **Yalıhan Bekçi:** `.yalihan-bekci/README.md`
- **Antigravity Rules:** `antigravity_rules.md`
- **Form Standards:** `docs/FORM_STANDARDS.md`

---

## 🐛 Sorun Giderme

### MCP Sunucusu Başlamıyor

```bash
# Node.js versiyonunu kontrol edin
node --version  # >= 18.0.0 olmalı

# Dependencies'i yeniden yükleyin
npm install

# Sunucuyu manuel başlatın
node yalihan-bekci-mcp.js
```

### Authority.json Bulunamıyor

```bash
# .context7 dizinini kontrol edin
ls -la .context7/

# Proje root'unu doğrulayın
echo $PROJECT_ROOT
```

### Bilgi Tabanı Erişim Hatası

```bash
# .yalihan-bekci dizinini kontrol edin
ls -la .yalihan-bekci/

# Dizin yoksa oluşturun
mkdir -p .yalihan-bekci/knowledge
mkdir -p .yalihan-bekci/reports
```

---

## 📝 Notlar

- MCP sunucuları stdio üzerinden çalışır
- Her sunucu bağımsız olarak çalıştırılabilir
- Tüm sunucular Context7 standartlarına uyumludur
- Bilgi tabanı sürekli güncellenir ve öğrenir

---

**Son Güncelleme:** 30 Kasım 2025  
**Versiyon:** 3.0.0  
**Durum:** ✅ Aktif ve Güncel
